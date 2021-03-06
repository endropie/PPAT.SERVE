<?php
namespace App\Filters\Auth;

use App\Filters\Filter;
use Illuminate\Http\Request;

class User extends Filter
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct($request);
    }

    public function name($value) {
        return $this->builder->where('name',  'like', '%' . $value. '%');
    }

}
