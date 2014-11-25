<?php namespace WhSol\Reservation\Models;

use DB;
use Str;
use Model;
use DateTime;
use DateInterval;
use WhSol\Reservation\Models\Day;
use WhSol\Reservation\Models\Settings;


class Reservation extends Model
{

    public $table = 'whsol_reservation_reserv';

    protected $fillable = ['name', 'reserv_date', 'reserv_time', 'reserv_hour', 'reserv_min', 'month_number', 'day_number', 'year_number'];

    protected $guarded = [];

    public static function getReservedTime($date = null){
        if (!$date) {
            $date = new DateTime('now');
        }
        else {
            $date = new DateTime($date);
        }

        return Reservation::where('reserv_date', '=', $date->format('Y-m-d'))->get()->toArray();
    }

    public static function getTimes($day, $timeFilter = null) {

        $working_time = [];

        if ($day['is_vacation'] == 'Yes') {
            return $working_time;
        }

        $reservTime = self::getReservedTime( sprintf('%s-%s-%s', $day['year'], $day['month_number'], $day['day_number']) );

        // $settings = Settings::instance();


        $hour_from = new DateTime($day['day_from']);
        $hour_to = new DateTime($day['day_to']);
        $time_interval = new DateInterval(sprintf('PT%sM', $day['minute_interval']));

        $break_from = new DateTime($day['break_from']);
        $break_to = new DateTime($day['break_to']);

        $reserved = [];
        if (!empty($reservTime)) {
            foreach($reservTime as $rt) {
                $reserved[] = $rt['reserv_time'];
            }
        }

        if ($timeFilter) {
            $time_filter = new DateTime($timeFilter);
        }
        else {
            $time_filter = new DateTime('00:00');
        }

        while ($hour_to > $hour_from) {
            if ($hour_from >= $break_from && $hour_from < $break_to) {
                $hour_from = $break_to;
            }
            $time = [
                'time' => $hour_from->format('H:i'),
                'reserved' => False,
            ];
            if ( in_array($time['time'], $reserved) ) {
                $time['reserved'] = True;
            }
            if ($hour_from > $time_filter) {
                $working_time[] = $time;
            }
            $hour_from->add($time_interval);
        }

        return $working_time;
    }

    public static function getCountBetween(DateTime $dateFrom, DateTime $dateTo) {

        $res = Reservation::select(DB::raw('count(*) as line_count, reserv_date'))
            ->whereBetween( 'reserv_date', [$dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d')] )
            ->groupBy('reserv_date')
            ->orderBy('year_number')
            ->orderBy('month_number')
            ->orderBy('day_number')
            ->orderBy('reserv_hour')
            ->orderBy('reserv_min')
            ->get()
            ->toArray();

        return $res;
    }

}