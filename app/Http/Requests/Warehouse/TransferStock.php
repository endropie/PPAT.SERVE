<?php

namespace App\Http\Requests\Warehouse;

use App\Http\Requests\Request;

class TransferStock extends Request
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
            $id = $this->transfer_stock;

            if($this->exists('nodata')) return [];
        }
        else $id = null;

        return [
            'number' => ($id ? 'required|' : '') .'unique:transfer_stocks,number,'. $id .',id,revise_number,'. $this->get('revise_number'),
            'date' => 'required',
            'transfer_stock_items.*.item_id' => 'required',
            'transfer_stock_items.*.unit_id' => 'required',
            'transfer_stock_items.*.unit_rate' => 'required',
            'transfer_stock_items.*.quantity' => 'required',
            'transfer_stock_items.*.from' => 'required',
            'transfer_stock_items.*.to' => 'required',
            'transfer_stock_items' =>
            function ($attribute, $value, $fail) {
                if (sizeof($value) == 0)  $fail('List Part must be select min. 1 Part item.');
            },
        ];
    }
}
