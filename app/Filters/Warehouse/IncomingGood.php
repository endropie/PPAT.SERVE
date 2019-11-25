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

    public function has_amount_outgoing($value) {
        $callback =  function ($q) {
          $or_details = explode(',', request('or_detail_ids', ''));
          $q->whereRaw('(quantity * unit_rate) > amount_outgoing')
            ->orWhereIn('item_id', $or_details);

        };
        return $this->builder
            ->with(['incoming_good_items'])
            ->whereHas('incoming_good_items', $callback);
    }

}
