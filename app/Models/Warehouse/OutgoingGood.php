<?php
namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Filters\Filterable;
use App\Models\WithUserBy;

class OutgoingGood extends Model
{
    use Filterable, SoftDeletes, WithUserBy;

    protected $fillable = [
        'number', 'customer_id', 'date', 'vehicle_id',  'description',
        'customer_name', 'customer_phone', 'customer_address',
        'revise_number'
    ];

    protected $appends = ['full_number'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $relationships = [];

    public function outgoing_good_items()
    {
        return $this->hasMany('App\Models\Warehouse\OutgoingGoodItem')->withTrashed();
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
