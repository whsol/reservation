<?php namespace WhSol\Reservation\Models;

use App;
use Str;
use Model;
use DateTime;

class Day extends Model
{
    public $table = 'whsol_reservation_month_day';
    protected $fillable = ['day_name', 'day_from', 'break_from', 'break_to', 'day_to', 'is_vacation', 'day_number', 'minute_interval'];

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = ['published_at'];

    public $belongsTo = [
        'month' => ['WhSol\Reservation\Models\Month', 'table' => 'whsol_reservation_month', 'order' => 'name', 'primaryKey' => 'month_id']
    ];

    public function __construct(array $attributes = array())
    {
        $settings = Settings::instance();
        $this->setRawAttributes([
            'day_from' => $settings->day_from,
            'break_from' => $settings->break_from,
            'break_to' => $settings->break_to,
            'day_to' => $settings->day_to,
            'minute_interval' => $settings->minute_interval
        ], true);
        parent::__construct($attributes);
    }

    public function getDayNameOptions($keyValue = null)
    {
        for( $i = 0; $i <= 6; $i++ )
        {
            $day_name = strftime( '%A', strtotime( 'next Monday +' . $i . ' days' ) );
            $days[$day_name] = $day_name;
        }
        return $days;
    }

    public function getIsVacationAttribute($keyValue = null)
    {
        return $keyValue ? 'Yes': 'No';
    }

    public static function getDay($date)
    {
        $resDay = false;

        if (!$date) {
            $date = new DateTime('now');
        }
        else {
            $date = new DateTime($date);
        }

        $day = $date->format('d');

        $res = Month::where('month_number', '=', (int) $date->format('m'))
            ->where('year', '=', $date->format('Y'))
            ->with(array('days' => function($query) use ($day){
                    $query->where('day_number', '=', (int) $day);
                })
            )
            ->first();
        if ($res) {
            $resDay = [
                'id' => $res->days[0]->id,
                'day_name' => $res->days[0]->day_name,
                'day_number' => $res->days[0]->day_number,
                'is_vacation' => $res->days[0]->is_vacation,
                'day_from' => $res->days[0]->day_from,
                'break_to' => $res->days[0]->break_to,
                'break_from' => $res->days[0]->break_from,
                'day_to' => $res->days[0]->day_to,
                'minute_interval' => $res->days[0]->minute_interval,
                'month_id' => $res->days[0]->month_id,
                'created_at' => $res->days[0]->created_at,
                'updated_at' => $res->days[0]->updated_at,
            ];
            $resDay['month_name'] = $res->month_name;
            $resDay['month_number'] = $res->month_number;
            $resDay['year'] = $res->year;
        }

        return $resDay;
    }


    public static function getDaysTwoMonth($date = null) {
        if (!$date) {
            $date = new DateTime('now');
        }
        else {
            $date = new DateTime($date);
        }


        $month = (int) $date->format('m');
        $day = (int) $date->format('d');
        if ($month == 12) {
            $year = $date->format('Y');
            $nextMonth = 1;
            $nextYear = (int) $year + 1;
            $q = Month::where(function($query) use ($month, $year) {
                $query->where('month_number', '=', $month)
                      ->where('year', '=', $year);
            })->orWhere(function($query) use($nextMonth, $nextYear) {
                $query->where('month_number', '=', $nextMonth)
                      ->where('year', '=', $nextYear);
            });
        }
        else {
            $nextMonth = $month + 1;
            $q = Month::whereIn('month_number', [$month, $nextMonth])
                   ->where('year', '=', $date->format('Y'));
        }
        $res = $q->with('days')->get()->toArray();

        $resDays = [];

        foreach($res as $monthObj) {
            foreach($monthObj['days'] as $dayObj) {
                $dayObj['month_number'] = $monthObj['month_number'];
                $dayObj['year'] = $monthObj['year'];
                if ($dayObj['is_vacation'] == 'Yes') {
                    $resDays['vacation'][] = $dayObj;
                }
                if ($dayObj['day_number'] == $day && $monthObj['month_number'] == $month) {
                    $resDays['requestedDay'] = $dayObj;
                }
                $resDays[] = $dayObj;
            }
        }
        return $resDays;
    }

}