<?php

namespace App\Http\Requests\Reference;

use App\Http\Requests\Request;

class Unit extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Check if store or update
        $method = $this->getMethod();
        
        if ($method == 'PATCH' || $method == 'PUT') 
        {
            $id = $this->unit;
        } else 
        {
            $id = null;
        }

        return [
            'code' => 'required|string|max:191|unique:units,NULL,' . $id,
            'name' => 'required|string|max:191',
        ];
    }
}
