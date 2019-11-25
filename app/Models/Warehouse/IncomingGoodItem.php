<?php

namespace App\Models\Warehouse;

use App\Filters\Filterable;
use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomingGoodItem extends Model
{
    use Filterable, SoftDeletes;

    protected $fillable = [
        'item_id', 'quantity', 'unit_id', 'unit_rate', 'note', 'is_valid'
    ];

    protected $appends = ['unit_amount'];

    protected $casts = [
        'quantity' => 'double',
        'unit_rate' => 'double',
        'amount_outgoing' => 'double',
        'is_valid' => 'boolean',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $relationships = [];

    public function outgoing_good_items()
    {
        return $this->hasMany('App\Models\Warehouse\OutgoingGoodItem');
    }

    public function incoming_good()
    {
        return $this->belongsTo('App\Models\Warehouse\IncomingGood');
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

    public function getUnitAmountAttribute() {

        if($this->unit_rate <= 0) return false;

        return (double) $this->quantity * $this->unit_rate;
    }

    public function calculate() {

        $total = (double) $this->outgoing_good_items->sum('unit_amount');
        $this->amount_outgoing = $total;
        $this->save();

        if ($this->unit_amount < $this->amount_outgoing) abort(501, 'TOTAL AMOUNT OUTGOING INVALID!');
    }
}
