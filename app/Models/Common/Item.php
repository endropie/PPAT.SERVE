<?php

namespace App\Models\Common;

use App\Models\Model;
use App\Filters\Filterable;

class Item extends Model
{
    use Filterable;

    protected $allowTransferDisabled;

    protected $fillable = [
        'code', 'customer_id', 'brand_id', 'specification_id', 'part_name', 'part_alias',  'part_number',
        'load_type', 'load_capacity', 'packing_duration', 'sa_dm', 'weight', 'price',
        'category_item_id', 'type_item_id', 'size_id', 'unit_id', 'description', 'enable'
    ];

    protected $appends = ['customer_code', 'totals'];

    protected $casts = [
        'load_capacity' => 'double',
        'packing_duration' => 'double',
        'sa_dm' => 'double',
        'weight' => 'double',
        'price' => 'double'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $relationships = [
        'item_stockables', 'item_stocks',
        'forecast_items', 'incoming_good_items', 'outgoing_good_items',
    ];

    public function incoming_good_items() {
        return $this->hasMany('App\Models\Warehouse\IncomingGoodItem');
    }

    public function outgoing_good_items() {
        return $this->hasMany('App\Models\Warehouse\OutgoingGoodItem');
    }

    public function work_order_items() {
        return $this->hasMany('App\Models\Factory\WorkOrderItem');
    }

    public function forecast_items() {
        return $this->hasMany('App\Models\Income\ForecastItem');
    }

    public function item_prelines()
    {
        return $this->hasMany('App\Models\Common\ItemPreline');
    }

    public function item_units()
    {
        return $this->hasMany('App\Models\Common\ItemUnit');
    }

    public function item_stocks()
    {
        return $this->hasMany('App\Models\Common\ItemStock');
    }

    public function item_stockables()
    {
        return $this->hasMany('App\Models\Common\ItemStockable');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Reference\Unit');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Income\Customer');
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Reference\Brand');
    }

    public function specification()
    {
        return $this->belongsTo('App\Models\Reference\Specification');
    }

    public function category_item()
    {
        return $this->belongsTo(\App\Models\Reference\CategoryItem::class);
    }

    public function type_item()
    {
        return $this->belongsTo(\App\Models\Reference\TypeItem::class);
    }

    public function size()
    {
        return $this->belongsTo(\App\Models\Reference\Size::class);
    }


    public function getTotalsAttribute()
    {
        $stocks = [];
        foreach (ItemStock::getStockists() as $key => $value) {
            $stocks[$key] = $this->hasMany('App\Models\Common\ItemStock')->where('stockist', $key)->sum('total');
        }
        return $stocks;
    }

    public function getCustomerCodeAttribute()
    {
        return $this->customer ? $this->customer->code : null;
    }

    public function stock($stockist)
    {
        $stockist = ItemStock::getValidStockist($stockist);

        return $this->item_stocks->where('stockist', $stockist)->first();
    }

    public function transfer($collect, $number, $stockist = false, $exStockist = false) {

        if(!$this->enable && !$this->allowTransferDisabled) abort(501, "PART [$this->code] DISABLED");

        $collect = $collect->fresh();

        if($exStockist) {
            $exStockist = ItemStock::getValidStockist($exStockist);
            $exStock = $this->item_stocks()->firstOrCreate(['stockist' => $exStockist]);
            $exStock->total = $exStock->total - $number;
            $exStock->save();

            $this->item_stockables()->create([
                'base_id' => $collect->id,
                'base_type' => get_class($collect),
                'unit_amount' => (-1) * ($number),
                'stockist' => $exStockist,
            ]);

            if($stockist==false) return $exStock;
        }

        if($stockist !== false) {

            $stockist = ItemStock::getValidStockist($stockist);
            $stock = $this->item_stocks()->firstOrCreate(['stockist' => $stockist]);
            $stock->total = $stock->total + $number;
            $stock->save();

            $this->item_stockables()->create([
                'base_id' => $collect->id,
                'base_type' => get_class($collect),
                'unit_amount' => ($number),
                'stockist' => $stockist,
            ]);

            return $stock;
        }
    }

    public function distransfer($collect, $delete=true) {
        if ($collect->stockable->count() == 0) return;

        if(!$this->enable && !$this->allowTransferDisabled) abort(501, "PART [$this->code] DISABLED");

        foreach ($collect->stockable as $key => $log) {
            // abort(501, json_encode($log));
            $stock = $this->item_stocks()->firstOrCreate(['stockist' => $log->stockist]);
            $stock->total -= $log->unit_amount;
            $stock->save();

            if($delete) $log->delete();
        }
    }

    public function allowDisableTransfer () {
        $this->allowTransferDisabled = true;
        return $this;
    }
}
