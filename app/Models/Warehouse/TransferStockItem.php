<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;

class TransferStockItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id', 'unit_id', 'unit_rate', 'quantity', 'from', 'to'
    ];

    protected $appends = ['unit_amount'];

    protected $casts = [
        'unit_rate' => 'double',
        'quantity' => 'double',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $relationships = [];

    public function transfer_stock()
    {
        return $this->belongsTo('App\Models\Warehouse\TransferStock');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Common\Item');
    }

    public function stockable()
    {
        return $this->morphMany('App\Models\Common\ItemStockable', 'base');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Reference\Unit');
    }

    public function getUnitAmountAttribute()
    {
        if($this->unit_rate < 0) $this->unit_rate = 0;

        return (double) ($this->quantity * $this->unit_rate);
    }
}
