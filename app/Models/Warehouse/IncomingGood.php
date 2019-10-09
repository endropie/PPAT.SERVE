<?php
namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\WithUserBy;
use App\Filters\Filterable;

class IncomingGood extends Model
{
    use Filterable, SoftDeletes, WithUserBy;

    protected $fillable = [
        'number', 'registration', 'date', 'time', 'transaction', 'order_mode',
        'customer_id', 'reference_number', 'reference_date', 'rit',
        'vehicle_id', 'description',
        'revise_number', 'indexed_number'
    ];

    protected $appends = ['full_number'];

    protected $relationships = [];

    protected $hidden = [];

    public function incoming_good_items()
    {
        return $this->hasMany('App\Models\Warehouse\IncomingGoodItem')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Income\Customer');
    }

    public function getFullNumberAttribute()
    {
        $append = $this->revise_number ? ' REV.' . $this->revise_number : '';
        return $this->number . $append;
    }
}
