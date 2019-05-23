<?php

namespace App\Http\Requests\Income;

use App\Http\Requests\Request;

class ShipDeliveryItem extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Check if store or update
        $method = $this->getMethod();
        
        if ($method == 'PATCH' || $method == 'PUT') {
            $id = $this->ship_delivery_item;
        } 
        else $id = null;

        return [
            // 'number' => ($id ? 'required|string|' : '') .'max:191|unique:ship_deliveries,NULL,' . $id,
            // 'date' => 'required',
            // 'time' => 'required',
            // 'customer_id' => 'required',

            // 'shipdelivery_items.*.item_id' => 'required',

            // 'shipdelivery_items' =>
            // function ($attribute, $value, $fail) {
            //     if (sizeof($value) == 0) {
            //         $fail('Delivery-Items must be select min. 1 item production.');
            //     }
            // },
        ];
    }

    public function messages()
    {
        $msg = 'The field is required!';

        return [
            'shipdelivery_items.*.item_id' => $msg,
            'quantity.required'         => $msg,
        ];
    }
}
