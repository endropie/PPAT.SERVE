<?php
namespace App\Models\Income;

use App\Models\Model;
use App\Filters\Filterable;

class Customer extends Model
{
    use Filterable;

    protected $fillable = [
        'code', 'name', 'phone', 'fax', 'email', 'address', 'subdistrict', 'district', 'province_id', 'zipcode',
        'bank_account', 'npwp', 'pkp', 'with_tax', 'with_pph', 'tax', 'pph_material', 'pph_service',
        'invoice_mode', 'delivery_mode', 'order_mode', 'description', 'enable'
    ];

    protected $appends = [ 'address_raw' ];

    protected $casts = [
        'tax' => 'double',
        'pph_material' => 'double',
        'pph_service' => 'double'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $relationships = ['items'];

    public function customer_contacts()
    {
        return $this->hasMany('App\Models\Income\CustomerContact');
    }

    public function customer_items() {
        return $this->hasMany('App\Models\Common\Item');
    }

    public function province()
    {
        return $this->belongsTo('App\Models\Reference\Province');
    }

    public function getAddressRawAttribute() {
        $raw  = ($this->address ?? '');
        $raw .= ($this->subdistrict ? "\n". $this->subdistrict .' ' : '');
        $raw .= ($this->district ?  $this->district .', ' : '');
        $raw .= ($this->province_id ? "\n". $this->province()->value('name') .' ' : '');
        $raw .= ($this->zipcode ? ' '. $this->zipcode : '');

        return $raw;
    }
}
