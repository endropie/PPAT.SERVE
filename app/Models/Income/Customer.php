<?php

namespace App\Models\Income;

use App\Models\Model;

class Customer extends Model
{
    
    protected $fillable = [
        'code', 'name', 'phone', 'fax', 'email', 'address', 'subdistrict', 'district', 'province_id',
        'bank_account', 'npwp', 'pkp', 'with_tax', 'with_pph', 'tax', 'pph_material', 'pph_service', 
        'bill_mode', 'delivery_mode', 'order_mode', 'description'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $model_relations = ['items'];

    public function customer_contacts()
    {
        return $this->hasMany('App\Models\Income\CustomerContact');
    }  
}
