<?php namespace WhSol\Reservation\ReportWidgets;

use DateTime;
use DateInterval;
use Backend\Classes\ReportWidgetBase;
use WhSol\Reservation\Models\Reservation;


class WeekReserv extends ReportWidgetBase
{
    public $day_range = 7;

    public function render()
    {
        $dayTo = new DateTime('now');
        $day_interval = new DateInterval(sprintf('P%sD', $this->day_range));
        $dayTo->add($day_interval);
        $day_interval->invert = 1;
        $dayFrom = new DateTime('now');
        $dayFrom->add($day_interval);
        $res = Reservation::getCountBetween($dayFrom, $dayTo);
        $day_interval = new DateInterval(sprintf('P%sD', 1));
        $days = [];
        if ($res) {
            foreach($res as $day) {
                $days[$day['reserv_date']] = $day;
            }
        }
        for($i = 0; $i < $this->day_range * 2; $i++) {
            if (!array_key_exists($dayFrom->format('Y-m-d'), $days)) {
                $days[$dayFrom->format('Y-m-d')] = [
                    'reserv_date' => $dayFrom->format('Y-m-d'),
                    'line_count' => 0
                ];
            }
            $dayFrom->add($day_interval);
        }
        return $this->makePartial('widget', ['days' => $days]);
    }
}