<?php

namespace App\Http\Controllers\Api\Incomes;

use App\Filters\Income\DeliveryOrder as Filters;
use App\Http\Requests\Income\DeliveryOrder as Request;
use App\Http\Controllers\ApiController;
use App\Models\Income\DeliveryOrder; 
use App\Models\Income\DeliveryOrderItem;
use App\Models\Income\ShipDeliveryItem;
use App\Traits\GenerateNumber;

class DeliveryOrders extends ApiController
{
    use GenerateNumber;

    public function index(Filters $filters)
    {
        switch (request('mode')) {
            case 'all':            
                $delivery_orders = DeliveryOrder::filter($filters)->get();    
                break;

            case 'datagrid':    
                $delivery_orders = DeliveryOrder::with(['customer','operator','vehicle'])->filterable()->get();
                $delivery_orders->each->setAppends(['is_relationship']);
                break;

            default:
                $delivery_orders = DeliveryOrder::with(['customer','operator','vehicle'])->collect();
                $delivery_orders->getCollection()->transform(function($item) {
                    $item->setAppends(['is_relationship']);
                    return $item;
                });
                break;
        }

        return response()->json($delivery_orders);
    }

    public function show($id)
    {
        
        $delivery_order = DeliveryOrder::with([
            'customer', 
            'request_order',
            'delivery_order_items.item.item_units', 
            'delivery_order_items.unit',
        ])->findOrFail($id);

        $delivery_order->setAppends(['has_revision', 'has_relationship']);

        return response()->json($delivery_order);
    }

    public function revision(Request $request, $id)
    {
        
        $this->DATABASE::beginTransaction();

        $associate = [];
        $revise = DeliveryOrder::findOrFail($id);   
        if($revise) {
            $revise->delivery_order_items->each(function($detail, $i) {
                $detail->request_order_item_id = null;
                $detail->save();
            });
        }

        // Auto generate number of revision
        if($request->number) {
            $max = (int) DeliveryOrder::where('number', $request->number)->max('numrev');
            $request->merge(['numrev'=> ($max + 1)]);
        }

        $delivery_order = DeliveryOrder::create($request->all());

        $rows = $request->delivery_order_items;
        $mounting = []; $check = [];
        for ($i=0; $i < count($rows); $i++) {
            $row = $rows[$i];

            $oldDetail = DeliveryOrderItem::find($row["id"]);
            
            // create DeliveryOrder items on the Delivery order revision!
            $detail = $delivery_order->delivery_order_items()->create($row);

            $detail->item->transfer($detail, (-1*$oldDetail->unit_amount), null, 'FG');
            $detail->item->transfer($detail, $detail->unit_amount, null, 'FG');

            $detail->item->transfer($detail, (-1*$oldDetail->unit_amount), null, 'PDO');
            $detail->item->transfer($detail, $detail->unit_amount, null, 'PDO');

            if($detail->item->stock('PDO')->total < (0 + 0.1)) $this->error('Data is not allowed to be changed');

            $detail->request_order_item_id = $row['request_order_item_id'];
            $detail->save();

            if($detail->request_order_item->total_delivery_order_item > ($detail->request_order_item->unit_amount + 0.1)) {
                $this->error('Data is not allowed to be changed');
            }
            
        }

        $delivery_order->request_order_id = $request->request_order_id;
        $delivery_order->ship_delivery_id = $request->ship_delivery_id;
        $delivery_order->save();


        $revise->revise_id = $delivery_order->id;
        $revise->save();

        $this->DATABASE::commit();
        return response()->json($delivery_order);
    }
}
