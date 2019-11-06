<?php

use Illuminate\Database\Seeder;
use App\Models\Model;

class Settings extends Seeder
{
	public function run()
    {
        Model::unguard();
            $this->create();
        Model::reguard();
    }

    private function create()
    {
        setting()->set([

            'general.app_name'                  => 'PLAY',
            'general.app_subname'               => 'ADMIN PLAY',
            'general.app_description'           => 'Administration',
            'general.app_logo'                  => '',
            'general.app_brand'                 => '',
            'general.email_protocol'            => 'mail',
            'general.email_sendmail_path'       => '/usr/sbin/sendmail -bs',

            'general.timezone'                  => 'Asia/Jakarta',
            'general.date_format'               => 'DD/MM/YYYYY',
            'general.percent_position'          => 'after',

            'general.prefix_separator'  => '/',

            'financial.begin_start'             => now()->startOfYear()->format('d-m'),

            'incoming_good.number_prefix'     => 'IMP',
            'incoming_good.number_interval'   => '{Y}',
            'incoming_good.number_digit'      => '5',

            'opname_stock.number_prefix'     => 'STO',
            'opname_stock.number_interval'   => '{Y}',
            'opname_stock.number_digit'      => '5',

            'outgoing_good.number_prefix'     => 'OMP',
            'outgoing_good.number_interval'   => '{Y}',
            'outgoing_good.number_digit'      => '5',

            'forecast.number_prefix'     => 'FCO',
            'forecast.number_interval'   => '{Y}',
            'forecast.number_digit'      => '5',
        ]);

        setting()->save();
    }
}
