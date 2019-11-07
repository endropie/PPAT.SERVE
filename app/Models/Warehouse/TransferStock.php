<?php
namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\WithUserBy;
use App\Filters\Filterable;

class TransferStock extends Model
{
    use Filterable, SoftDeletes, WithUserBy;

    protected $fillable = [
        'number', 'date', 'reference', 'description'
    ];

    protected $appends = ['full_number'];

    protected $relationships = [];

    protected $hidden = [];

    public function transfer_stock_items()
    {
        return $this->hasMany('App\Models\Warehouse\TransferStockItem')->withTrashed();
    }

    public function getFullNumberAttribute()
    {
        $append = $this->revise_number ? ' REV.' . $this->revise_number : '';
        return $this->number . $append;
    }
}
