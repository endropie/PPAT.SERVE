<?php

namespace App\Http\Controllers\Api\Warehouses;

use App\Http\Requests\Warehouse\TransferStock as Request;
use App\Http\Controllers\ApiController;
use App\Filters\Warehouse\TransferStock as Filters;
use App\Models\Warehouse\TransferStock;
use App\Traits\GenerateNumber;

class TransferStocks extends ApiController
{
    use GenerateNumber;

    public function index(Filters $filters)
    {
        switch (request('mode')) {
            case 'all':
                $transfer_stocks = TransferStock::filter($filters)->get();
                break;

            case 'datagrid':
                $transfer_stocks = TransferStock::filter($filters)->latest()->get();
                $transfer_stocks->each->append(['is_relationship']);
                break;

            default:
                $transfer_stocks = TransferStock::filter($filters)->latest()->collect();
                $transfer_stocks->getCollection()->transform(function($item) {
                    $item->append(['is_relationship']);
                    return $item;
                });
                break;
        }

        return response()->json($transfer_stocks);
    }

    public function store(Request $request)
    {
        // DB::beginTransaction => Before the function process!
        $this->DATABASE::beginTransaction();

        if(!$request->number) $request->merge(['number'=> $this->getNextTransferStockNumber()]);

        $transfer_stock = TransferStock::create($request->all());

        $rows = $request->input('transfer_stock_items', []);
        for ($i=0; $i < count($rows); $i++) {
            $row = $rows[$i];

            // create item row on the incoming Goods updated!
            $detail = $transfer_stock->transfer_stock_items()->create($row);

            if (!$detail->item->enable) {
                $label = $detail->item->part_name ?? $detail->item->part_number ?? $detail->item->id;
                $this->error("PART [". $label . "] DISABLED");
            }
        }

        // DB::Commit => Before return function!
        $this->DATABASE::commit();
        return response()->json($transfer_stock);
    }

    public function show($id)
    {
        $transfer_stock = TransferStock::withTrashed()->with([
            'transfer_stock_items.item.item_units',
            'transfer_stock_items.unit'
        ])->findOrFail($id);

        $transfer_stock->append(['is_relationship','has_relationship']);

        return response()->json($transfer_stock);
    }

    public function update(Request $request, $id)
    {
        if(request('mode') === 'validation') return $this->validation($request, $id);
        if(request('mode') === 'revision') return $this->revision($request, $id);

        // DB::beginTransaction => Before the function process!
        $this->DATABASE::beginTransaction();

        $transfer_stock = TransferStock::findOrFail($id);

        if ($transfer_stock->status != "OPEN") $this->error("$transfer_stock->number is not OPEN state, is not allowed to be changed");
        if ($transfer_stock->is_relationship) $this->error("$transfer_stock->number has relationships, is not allowed to be changed");

        $transfer_stock->update($request->input());

        // Before Update Force delete opname stocks items
        $transfer_stock->transfer_stock_items()->forceDelete();

        // Update opname stocks items
        $rows = $request->transfer_stock_items;
        for ($i=0; $i < count($rows); $i++) {
            $row = $rows[$i];
            // Update or Create detail row
            $detail = $transfer_stock->transfer_stock_items()->create($row);
            $label = $detail->item->part_name ?? $detail->item->part_number ?? $detail->item->id;
            if (!$detail->item->enable) $this->error("PART [". $label . "] DISABLED");
        }

        $this->DATABASE::commit();
        return response()->json($transfer_stock);
    }

    public function destroy($id)
    {
        // DB::beginTransaction => Before the function process!
        $this->DATABASE::beginTransaction();

        $transfer_stock = TransferStock::findOrFail($id);

        $mode = strtoupper(request('mode') ?? 'DELETED');
        if($transfer_stock->is_relationship) $this->error("$transfer_stock->number has relationship, is not allowed to be $mode");
        if($mode == "DELETED" && $transfer_stock->status != 'OPEN') $this->error("The data $transfer_stock->status state, is not allowed to be $mode");

        if($mode == 'VOID') {
            $transfer_stock->status = "VOID";
            $transfer_stock->save();
        }

        if($details = $transfer_stock->transfer_stock_items) {
            foreach ($details as $detail) {
                $detail->item->distransfer($detail);
                $detail->delete();
            }
        }

        $transfer_stock->delete();

        // DB::Commit => Before return function!
        $this->DATABASE::commit();
        return response()->json(['success' => true]);
    }

    public function validation($request, $id)
    {
        // DB::beginTransaction => Before the function process!
        $this->DATABASE::beginTransaction();

        $transfer_stock = TransferStock::findOrFail($id);

        if ($transfer_stock->status != "OPEN") $this->error('The data not "OPEN" state, is not allowed to be changed');

        foreach ($transfer_stock->transfer_stock_items as $detail) {
            // Calculate stock on "validation" Opname Stock!
            $detail->item->transfer($detail, $detail->unit_amount, $detail->to, $detail->from);
        }

        $transfer_stock->status = 'VALIDATED';
        $transfer_stock->save();

        $this->DATABASE::commit();
        return response()->json($transfer_stock);
    }

    public function revision($request, $id)
    {
        $this->DATABASE::beginTransaction();

        $revise = TransferStock::findOrFail($id);
        $details = $revise->transfer_stock_items;
        foreach ($details as $detail) {
            $detail->item->distransfer($detail);
            $detail->delete();
        }

        if($request->number) {
            $max = (int) TransferStock::where('number', $request->number)->max('revise_number');
            $request->merge(['revise_number' => ($max + 1)]);
        }

        $transfer_stock = TransferStock::create($request->all());

        $rows = $request->transfer_stock_items;
        for ($i=0; $i < count($rows); $i++) {
            $row = $rows[$i];
            $detail = $transfer_stock->transfer_stock_items()->create($row);

            $detail->item->transfer($detail, $detail->unit_amount, $detail->to, $detail->from);
        }


        $transfer_stock->status = 'VALIDATED';
        $transfer_stock->save();

        $revise->status = 'REVISED';
        $revise->revise_id = $transfer_stock->id;
        $revise->save();
        $revise->delete();

        $this->DATABASE::commit();
        return response()->json($transfer_stock);
    }
}
