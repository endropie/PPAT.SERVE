<?php

namespace App\Models\Common;

use App\Filters\Filterable;
use App\Models\Model;

class ItemStockable extends Model
{
    use Filterable;

    protected $fillable = ['item_id', 'stockist', 'unit_amount', 'base_type', 'base_id'];

    protected $casts = [
        'unit_amount' => 'double',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function item()
    {
        return $this->belongsTo('App\Models\Common\Item');
    }

    public function base()
    {
        return $this->morphTo();
    }

    public function getBaseDataAttribute() {
        switch ($this->base_type) {
            case 'App\Models\Warehouse\IncomingGoodItem':
            return $this->base->incoming_good;

            case 'App\Models\Warehouse\OutgoingGoodItem':
            return $this->base->outgoing_good;

            case 'App\Models\Warehouse\TransferStockItem':
            return $this->base->transfer_stock;

            case 'App\Models\Warehouse\OpnameStockItem':
            return $this->base->opname_stock;

            default:
            return null;
        }
    }
}
