<?php

namespace App\Http\Requests\Common;

use App\Http\Requests\Request;

class Item extends Request
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
            $id = $this->item;
            $code = 'required|';
        }
        else {
            $id = null;
            $code = '';
        }

        $code = ($id ? 'required|':'').'max:191|unique:items,NULL,' . $id;

        return [
            'code' => $code,
            'customer_id' => 'required'
        ];
    }
}
