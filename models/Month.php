<?php namespace WhSol\Reservation\Models;

use Str;
use Model;
use WhSol\Reservation\Models\Day;

class Month extends Model
{

    public $table = 'whsol_reservation_month';

    protected $guarded = [];

    public $hasMany = [
        'days' => ['WhSol\Reservation\Models\Day', 'table' => 'whsol_reservation_month', 'order' => 'day_name', 'foreignKey' => 'month_id']
    ];

    public function getMonthNameOptions($keyValue = null)
    {
        for ($m = 1; $m <= 12; $m++) {
            $mn = strftime('%B', mktime(0, 0, 0, $m, 1, date('Y')));
            $month[$mn] = $mn;
        }
        return $month;
    }

}