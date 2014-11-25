<?php namespace WhSol\Reservation\Models;

use Model;

class Settings extends Model
{

    public $attributes = ['value' => '{"minute_interval":"20","day_from":"08:00","day_to":"17:00","break_from":"12:00","break_to":"13:00"}'];


    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'whsol_reservation_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    protected $cache = [];

    public function getDropdownOptions($fieldName = null, $keyValue = null){
        $date  = new \DateTime('now');

        $year = (int) $date->format('Y');

        $res = ['' => '---'];
        for($i = $year - 10; $i < $year + 10; $i++) {
            $index = $i.'-01';
            $res[$index] = $index;
        }

        return $res;
    }

    public function afterSave() {
        if ($this->get('start_date', '') == '' || $this->get('end_date', '') == '') {
            return TRUE;
        }
        $start_date = new \DateTime($this->get('start_date'));
        $end_date = new \DateTime($this->get('end_date'));
        $start_year = (int) $start_date->format('Y');
        $end_year = (int) $end_date->format('Y');
        if ($start_year > $end_year) {
            return TRUE;
        }
        for ($year = $start_year; $year < $end_year; $year++) {
            for ($m = 1; $m <= 12; $m++) {
                $month = strftime('%B', mktime(0, 0, 0, $m, 1, $year));
                $first_day = mktime(0, 0, 0, $m, 1, $year);
                $day_of_week = date('N', $first_day);
                $days_in_month = cal_days_in_month(0, $m, $year);
                $month_obj = Month::create([
                    'month_name' => $month,
                    'month_number' => $m,
                    'year' => $year
                ]);
                $days = [];

                for ($d = 1; $d <= $days_in_month; $d++) {
                    $day = [
                        'month_id' => $month_obj->id,
                        'day_name' => strftime('%A', strtotime('next Sunday +' . ($day_of_week % 7) . ' days')),
                        'day_number' => $d,
                        'day_from' => $this->get('day_from'),
                        'break_from' => $this->get('break_from'),
                        'break_to' => $this->get('break_to'),
                        'day_to' => $this->get('day_to'),
                        'is_vacation' => in_array(($day_of_week % 7), [6, 0]) ? True : False,
                        'minute_interval' => $this->get('minute_interval')
                    ];

                    $day_of_week++;
                    $days[] = $day;
                    //Day::create($day);
                }
                Day::insert($days);
            }
        }

        $this->set('start_date', '');
        $this->set('end_date', '');
        return TRUE;
    }

}