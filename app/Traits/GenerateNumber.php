<?php
namespace App\Traits;

trait GenerateNumber
{
    public function getNextForecastNumber($date = null)
    {
        $modul = 'forecast';
        $digit = (int) setting()->get("$modul.number_digit", 5);
        $prefix = $this->prefixParser($modul, 'FC');
        $prefix = $this->dateParser($prefix, $date);

        $next = \App\Models\Income\Forecast::withTrashed()->where('number','LIKE', $prefix.'%')->max('number');
        $next = $next ? (int) str_replace($prefix,'', $next) : 0;
        $next++;

        $number = $prefix . str_pad($next, $digit, '0', STR_PAD_LEFT);

        return $number;
    }

    public function getNextIncomingGoodNumber($date = null)
    {
        $modul = 'incoming_good';
        $digit = (int) setting()->get("$modul.number_digit", 5);
        $prefix = $this->prefixParser($modul, 'IMP');
        $prefix = $this->dateParser($prefix, $date);

        $next = \App\Models\Warehouse\IncomingGood::withTrashed()->where('number','LIKE', $prefix.'%')->max('number');
        $next = $next ? (int) str_replace($prefix,'', $next) : 0;
        $next++;

        $number = $prefix . str_pad($next, $digit, '0', STR_PAD_LEFT);

        return $number;
    }

    public function getNextIncomingGoodIndexedNumber($date = null, $prefix)
    {
        $modul = 'incoming_good';
        $digit = (int) setting()->get("$modul.indexed_number_digit", 3);
        $interval = setting()->get("$modul.indexed_number_interval", '{Y-m}');
        $separator = setting()->get("general.prefix_separator", '/');

        if (strlen($interval)) $prefix = $prefix . $separator . $interval;
        $prefix.= $separator;

        $prefix = $this->dateParser($prefix, $date);

        $next = \App\Models\Warehouse\IncomingGood::withTrashed()->where('indexed_number','LIKE', $prefix.'%')->max('indexed_number');
        $next = $next ? (int) str_replace($prefix,'', $next) : 0;
        $next++;

        $number = $prefix . str_pad($next, $digit, '0', STR_PAD_LEFT);

        return $number;
    }

    public function getNextOutgoingGoodNumber($date = null)
    {
        $modul = 'outgoing_good';
        $digit = (int) setting()->get("$modul.number_digit", 5);
        $prefix = $this->prefixParser($modul, 'OMP');
        $prefix = $this->dateParser($prefix, $date);

        $next = \App\Models\Warehouse\OutgoingGood::withTrashed()->where('number','LIKE', $prefix.'%')->max('number');
        $next = $next ? (int) str_replace($prefix,'', $next) : 0;
        $next++;

        $number = $prefix . str_pad($next, $digit, '0', STR_PAD_LEFT);

        return $number;
    }

    public function getNextOpnameStockNumber($date = null)
    {
        $modul = 'opname_stock';
        $digit = (int) setting()->get("$modul.number_digit", 5);
        $prefix = $this->prefixParser($modul, 'STO');
        $prefix = $this->dateParser($prefix, $date);

        $next = \App\Models\Warehouse\IncomingGood::withTrashed()->where('number','LIKE', $prefix.'%')->max('number');
        $next = $next ? (int) str_replace($prefix,'', $next) : 0;
        $next++;

        $number = $prefix . str_pad($next, $digit, '0', STR_PAD_LEFT);

        return $number;
    }



    public function getNextTransferStockNumber($date = null)
    {
        $modul = 'transfer_stock';
        $digit = (int) setting()->get("$modul.number_digit", 5);
        $prefix = $this->prefixParser($modul, 'STT');
        $prefix = $this->dateParser($prefix, $date);

        $next = \App\Models\Warehouse\TransferStock::withTrashed()->where('number','LIKE', $prefix.'%')->max('number');
        $next = $next ? (int) str_replace($prefix,'', $next) : 0;
        $next++;

        $number = $prefix . str_pad($next, $digit, '0', STR_PAD_LEFT);

        return $number;
    }

    protected function prefixParser($modul, $prefix = '', $interval = '{Y}')
    {
        if (setting()->get("$modul.number_prefix")) $prefix .= setting()->get("$modul.number_prefix", $prefix);
        if (strlen($prefix) > 0) $prefix .= setting()->get("general.prefix_separator",'/');

        if (setting()->get("$modul.number_interval")) $interval = setting()->get("$modul.number_interval", $interval);
        if (strlen($interval) > 0) $interval .= setting()->get("general.prefix_separator",'/');

        return $prefix . $interval;
    }

    protected function dateParser($str, $date)
    {
        $matches = array();
        $regex = "/{(.*)}/";

        $date  = $date ? $date : date('Y-m-d');
        preg_match_all($regex, $str, $matches);

        if(count($matches) > 0 && count($matches[0]) > 0 )
        {
            $str = str_replace($matches[0][0], date($matches[1][0], strtotime($date)) , $str);
        }

        return $str ?? '';
    }

    public function toAlpha($num, $code = '')
    {
        $alphabets = array('', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');

        $division = floor($num / 26);
        $remainder = $num % 26;

        if($remainder == 0)
        {
            $division = $division - 1;
            $code .= 'z';
        }
        else
            $code .= $alphabets[$remainder];

        if($division > 26)
            return number_to_alpha($division, $code);
        else
            $code .= $alphabets[$division];

        return strrev($code);
    }
}
