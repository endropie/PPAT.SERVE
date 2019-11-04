<?php

namespace App\Http\Controllers\Api\Warehouses;

use App\Http\Requests\Warehouse\OutgoingGood as Request;
use App\Http\Controllers\ApiController;
use App\Filters\Warehouse\OutgoingGood as Filters;
use App\Models\Warehouse\OutgoingGood;
use App\Traits\GenerateNumber;

class OutgoingGoods extends ApiController
{
    use GenerateNumber;

    public function index(Filters $filters)
    {
        switch (request('mode')) {
            case 'all':
                $outgoing_goods = OutgoingGood::filter($filters)->get();
                break;

            default:
                $outgoing_goods = OutgoingGood::with(['customer'])->filter($filters)->latest()->collect();
                $outgoing_goods->getCollection()->transform(function($item) {
                    $item->append('is_relationship');
                    return $item;
                });
                break;
        }

        return response()->json($outgoing_goods);
    }

    public function store(Request $request)
    {
        // DB::beginTransaction => Before the function process!
        $this->DATABASE::beginTransaction();

        if (!$request->number) $request->merge([
            'number'=> $this->getNextOutgoingGoodNumber($request->input('date'))
        ]);

        $outgoing_good = OutgoingGood::create($request->all());

        $rows = $request->outgoing_good_items;
        for ($i=0; $i < count($rows); $i++) {
            $row = $rows[$i];

            // create item row on the incoming Goods updated!
            $detail = $outgoing_good->outgoing_good_items()->create($row);
            if (!$detail->item->enable) $this->error("PART [". $detail->item->code . "] DISABLED");

        }

        // DB::Commit => Before return function!
        $this->DATABASE::commit();
        return response()->json($outgoing_good);
    }

    public function show($id)
    {
        $outgoing_good = OutgoingGood::withTrashed()->with([
            'customer',
            'outgoing_good_items.item.item_units',
            'outgoing_good_items.unit'
        ])->findOrFail($id);

        $outgoing_good->append(['is_relationship','has_relationship']);

        return response()->json($outgoing_good);
    }

    public function update(Request $request, $id)
    {
        if(request('mode') === 'validation') return $this->validation($request, $id);
        if(request('mode') === 'revision') return $this->revision($request, $id);

        $this->DATABASE::beginTransaction();

        $outgoing_good = OutgoingGood::findOrFail($id);

        if ($outgoing_good->status != "OPEN") $this->error("[$outgoing_good->number] not 'OPEN' state, is not allowed to be changed");
        if ($outgoing_good->is_relationship) $this->error("The data has relationships, is not allowed to be changed");

        $outgoing_good->update($request->input());

        // Before Update Force delete incoming goods items
        $outgoing_good->outgoing_good_items()->forceDelete();

        // Update incoming goods items
        $rows = $request->outgoing_good_items;
        for ($i=0; $i < count($rows); $i++) {
            $row = $rows[$i];
            // Update or Create detail row
            $detail = $outgoing_good->outgoing_good_items()->create($row);
            if (!$detail->item->enable) $this->error("PART [". $detail->item->code . "] DISABLED");
        }

        $this->DATABASE::commit();
        return response()->json($outgoing_good);
    }

    public function destroy($id)
    {
        // DB::beginTransaction => Before the function process!
        $this->DATABASE::beginTransaction();

        $outgoing_good = OutgoingGood::findOrFail($id);

        $mode = strtoupper(request('mode') ?? 'DELETED');
        if($outgoing_good->is_relationship) $this->error("The data has RELATIONSHIP, is not allowed to be $mode");
        if($mode == "DELETED" && $outgoing_good->status != 'OPEN') $this->error("The data $outgoing_good->status state, is not allowed to be $mode");

        if($mode == 'VOID') {
            $outgoing_good->status = "VOID";
            $outgoing_good->save();
        }

        if($details = $outgoing_good->outgoing_good_items) {
            foreach ($details as $detail) {
                $detail->item->distransfer($detail);
                $detail->delete();
            }
        }

        $outgoing_good->delete();

        // DB::Commit => Before return function!
        $this->DATABASE::commit();
        return response()->json(['success' => true]);
    }

    public function validation($request, $id)
    {
        // DB::beginTransaction => Before the function process!
        $this->DATABASE::beginTransaction();

        $outgoing_good = OutgoingGood::findOrFail($id);

        if ($outgoing_good->status != "OPEN") $this->error("[$outgoing_good->number] has not 'OPEN' state, is not allowed to be validated");

        foreach ($outgoing_good->outgoing_good_items as $detail) {
            // Calculate stock on "validation" Incoming Goods!
            $detail->item->transfer($detail, $detail->unit_amount, null, 'FM');
        }

        $outgoing_good->status = 'VALIDATED';
        $outgoing_good->save();

        $this->DATABASE::commit();
        return response()->json($outgoing_good);
    }

    public function revision($request, $id)
    {
        $this->DATABASE::beginTransaction();

        $revise = OutgoingGood::findOrFail($id);
        $details = $revise->outgoing_good_items;
        foreach ($details as $detail) {
            $detail->item->distransfer($detail);
            $detail->delete();
        }

        if($request->number) {
            $max = (int) OutgoingGood::where('number', $request->number)->max('revise_number');
            $request->merge(['revise_number' => ($max + 1)]);
        }

        $outgoing_good = OutgoingGood::create($request->all());

        $rows = $request->outgoing_good_items;
        for ($i=0; $i < count($rows); $i++) {
            $row = $rows[$i];
            $detail = $outgoing_good->outgoing_good_items()->create($row);
            $detail->item->transfer($detail, $detail->unit_amount, null, 'FM');
        }

        $outgoing_good->status = 'VALIDATED';
        $outgoing_good->save();

        $revise->status = 'REVISED';
        $revise->revise_id = $outgoing_good->id;
        $revise->save();
        $revise->delete();

        $this->DATABASE::commit();
        return response()->json($outgoing_good);
    }
}
