<?php

namespace App\Http\Requests\Income;

use App\Http\Requests\Request;

class Customer extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $method = $this->getMethod();

        if ($method == 'PATCH' || $method == 'PUT') {
            $id = $this->customer;
        }
        else $id = null;

        return [
            'code' => 'required|string|max:5|unique:customers,NULL,' . $id,
            'name' => 'required|string|max:191',

        ];
    }

    public function messages()
    {
        return [
            // Code..
        ];
    }
}
