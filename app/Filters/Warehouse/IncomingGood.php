<?php
namespace App\Filters\Warehouse;

use App\Filters\Filter;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IncomingGood extends Filter
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct($request);
    }

    public function sort_customer_id($order) {

        $table = 'incoming_goods';
        $with = 'customers';
        $key = 'customer_id';
        $field = 'name';

        return $this->builder->select($table.'.*', \DB::raw('(SELECT '.$field.' FROM '.$with.' WHERE '. $with .'.id ='.$table.'.'.$key.' ) as fieldsort'))
        ->orderBy('fieldsort', $order);


    }
}
