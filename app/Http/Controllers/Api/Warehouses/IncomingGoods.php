<?php

namespace App\Http\Controllers\Api\Warehouses;

use App\Http\Requests\Warehouse\IncomingGood as Request;
use App\Http\Controllers\ApiController;
use App\Filters\Warehouse\IncomingGood as Filters;
use App\Models\Income\Customer;
use App\Models\Warehouse\IncomingGood;
use App\Traits\GenerateNumber;

class IncomingGoods extends ApiController
{
    use GenerateNumber;

    public function index(Filters $filters)
    {
        switch (request('mode')) {
            case 'all':
                $incoming_goods = IncomingGood::filter($filters)->get();
                break;

            case 'datagrid':
                $incoming_goods = IncomingGood::with(['customer'])->filter($filters)->latest()->get();
                $incoming_goods->each->append(['is_relationship']);
                break;

            default:
                $incoming_goods = IncomingGood::with(['customer'])->filter($filters)->latest()->collect();
                $incoming_goods->getCollection()->transform(function($item) {
                    $item->append(['is_relationship']);
                    return $item;
                });
                break;
        }

        return response()->json($incoming_goods);
    }

    public function store(Request $request)
    {
        // DB::beginTransaction => Before the function process!
        $this->DATABASE::beginTransaction();

        if (!$request->number) $request->merge([
            'number'=> $this->getNextIncomingGoodNumber($request->input('date'))
        ]);

        $prefix = Customer::find($request->input('customer_id'));
        if (!$request->indexed_number && $prefix) {
            $request->merge([
                'indexed_number' => $this->getNextIncomingGoodIndexedNumber($request->input('date'), $prefix->code)
            ]);
        }

        $incoming_good = IncomingGood::create($request->all());

        $rows = $request->incoming_good_items;
        for ($i=0; $i < count($rows); $i++) {
            $row = $rows[$i];

            // create item row on the incoming Goods updated!
            $detail = $incoming_good->incoming_good_items()->create($row);
            if (!$detail->item->enable) $this->error("PART [". $detail->item->code . "] DISABLED");

        }

        // DB::Commit => Before return function!
        $this->DATABASE::commit();
        return response()->json($incoming_good);
    }

    public function show($id)
    {
        $incoming_good = IncomingGood::withTrashed()->with([
            'customer',
            'incoming_good_items.item.item_units',
            'incoming_good_items.unit'
        ])->findOrFail($id);

        $incoming_good->append(['is_relationship','has_relationship']);

        return response()->json($incoming_good);
    }

    public function update(Request $request, $id)
    {
        if(request('mode') === 'rejection') return $this->rejection($request, $id);
        if(request('mode') === 'restoration') return $this->restoration($request, $id);
        if(request('mode') === 'validation') return $this->validation($request, $id);
        if(request('mode') === 'revision') return $this->revision($request, $id);

        // DB::beginTransaction => Before the function process!
        $this->DATABASE::beginTransaction();

        $incoming_good = IncomingGood::findOrFail($id);

        if ($incoming_good->status != "OPEN") $this->error('The data not "OPEN" state, is not allowed to be changed');
        if ($incoming_good->is_relationship) $this->error('The data has relationships, is not allowed to be changed');

        $incoming_good->update($request->input());

        // Before Update Force delete incoming goods items
        $incoming_good->incoming_good_items()->forceDelete();

        // Update incoming goods items
        $rows = $request->incoming_good_items;
        for ($i=0; $i < count($rows); $i++) {
            $row = $rows[$i];
            // Update or Create detail row
            $detail = $incoming_good->incoming_good_items()->create($row);
            if (!$detail->item->enable) $this->error("PART [". $detail->item->code . "] DISABLED");
        }

        $this->DATABASE::commit();
        return response()->json($incoming_good);
    }

    public function destroy($id)
    {
        // DB::beginTransaction => Before the function process!
        $this->DATABASE::beginTransaction();

        $incoming_good = IncomingGood::findOrFail($id);

        $mode = strtoupper(request('mode') ?? 'DELETED');
        if($incoming_good->is_relationship) $this->error("The data has RELATIONSHIP, is not allowed to be $mode");
        if($mode == "DELETED" && $incoming_good->status != 'OPEN') $this->error("The data $incoming_good->status state, is not allowed to be $mode");

        if($mode == 'VOID') {
            $incoming_good->status = "VOID";
            $incoming_good->save();
        }

        if($details = $incoming_good->incoming_good_items) {
            foreach ($details as $detail) {
                $detail->item->distransfer($detail);
                $detail->delete();
            }
        }

        $incoming_good->delete();

        // DB::Commit => Before return function!
        $this->DATABASE::commit();
        return response()->json(['success' => true]);
    }

    public function rejection($request, $id)
    {
        // DB::beginTransaction => Before the function process!
        $this->DATABASE::beginTransaction();

        $incoming_good = IncomingGood::findOrFail($id);

        if ($incoming_good->status != "OPEN") $this->error('The data not "OPEN" state, is not allowed to be changed');

        $incoming_good->status = 'REJECTED';
        $incoming_good->save();

        $this->DATABASE::commit();
        return response()->json($incoming_good);
    }

    public function restoration($request, $id)
    {
        $this->DATABASE::beginTransaction();

        $revise = IncomingGood::findOrFail($id);
        $details = $revise->incoming_good_items;
        foreach ($details as $detail) {
            $detail->item->distransfer($detail);
            $detail->delete();
        }

        if($request->number) {
            $max = (int) IncomingGood::where('number', $request->number)->max('revise_number');
            $request->merge(['revise_number' => ($max + 1)]);
        }

        if(!$request->transaction == 'RETURN') $request->merge(['order_mode'=> 'NONE']);

        $incoming_good = IncomingGood::create($request->all());

        $rows = $request->incoming_good_items;
        for ($i=0; $i < count($rows); $i++) {
            $row = $rows[$i];
            $detail = $incoming_good->incoming_good_items()->create($row);
        }

        $revise->revise_id = $incoming_good->id;
        $revise->save();
        $revise->delete();

        // $this->error('LOLOS');

        $this->DATABASE::commit();
        return response()->json($incoming_good);
    }

    public function validation($request, $id)
    {
        // DB::beginTransaction => Before the function process!
        $this->DATABASE::beginTransaction();

        $incoming_good = IncomingGood::findOrFail($id);

        $rows = $request->incoming_good_items ?? [];

        foreach ($rows as $row) {
            $detail = $incoming_good->incoming_good_items()->find($row["id"]);
            $detail->update($row);
        }

        if ($incoming_good->status != "OPEN") $this->error('The data not "OPEN" state, is not allowed to be changed');

        foreach ($incoming_good->incoming_good_items as $detail) {
            $detail->item->transfer($detail, $detail->unit_amount, 'FM');
        }

        $incoming_good->status = 'VALIDATED';
        $incoming_good->save();

        $this->DATABASE::commit();
        return response()->json($incoming_good);
    }

    public function revision($request, $id)
    {
        $this->DATABASE::beginTransaction();

        $revise = IncomingGood::findOrFail($id);
        $details = $revise->incoming_good_items;
        foreach ($details as $detail) {
            $detail->item->distransfer($detail);
            $detail->delete();
        }

        if($request->number) {
            $max = (int) IncomingGood::where('number', $request->number)->max('revise_number');
            $request->merge(['revise_number' => ($max + 1)]);
        }

        if(!$request->transaction == 'RETURN') $request->merge(['order_mode'=> 'NONE']);

        $incoming_good = IncomingGood::create($request->all());

        $rows = $request->incoming_good_items;
        for ($i=0; $i < count($rows); $i++) {
            $row = $rows[$i];
            $detail = $incoming_good->incoming_good_items()->create($row);
            $detail->item->transfer($detail, $detail->unit_amount, 'FM');
        }

        $incoming_good->status = 'VALIDATED';
        $incoming_good->save();

        $revise->status = 'REVISED';
        $revise->revise_id = $incoming_good->id;
        $revise->save();
        $revise->delete();

        $this->DATABASE::commit();
        return response()->json($incoming_good);
    }


}
