<?php namespace WhSol\Reservation\Components;

use App;
use Config;
use DateTime;
use Detection\MobileDetect;
use Validator;
use Lang;
use October\Rain\Support\ValidationException;
use Cms\Classes\ComponentBase;
use WhSol\Reservation\Models\Day;
use WhSol\Reservation\Models\Reservation;

class ReservationForm extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => Lang::get('whsol.reservation::lang.component.name'),
            'description' => Lang::get('whsol.reservation::lang.component.description')
        ];
    }

    public function defineProperties()
    {
        return [
            'first_day' => [
                'title'       => Lang::get('whsol.reservation::lang.component.property.first_day'),
                'type'        => 'string',
                'default'     => '1',
            ]
        ];
    }

    public function isDesktop() {
        $md = new MobileDetect();
        if ( $md->isMobile() || $md->isTablet() ) {
            return False;
        }
        return True;
    }

    public function onRun()
    {
        $this->addCss('/plugins/whsol/reservation/assets/css/datepicker.css');
        $this->addJs('/plugins/whsol/reservation/assets/js/bootstrap-datepicker.js');
        $this->addJs(sprintf('/plugins/whsol/reservation/assets/js/locales/bootstrap-datepicker.%s.js', Config::get('app.locale')));
        $this->addJs('/plugins/whsol/reservation/assets/js/reservation.js');
        $time = time();

        $days = Day::getDaysTwoMonth();

        $disableDays = [];

        foreach($days['vacation'] as $day ){
            if (!isset($disableDays[$day['year']])) {
                $disableDays[$day['year']] = [];
            }
            $disableDays[$day['year']][$day['month_number']][] = (int) $day['day_number'];
        }

        $datePickierParams = [
            "startDate" => date('Y-m-d', $time),
            "endDate" => date('Y-m-d', strtotime("+2 week", $time)),
            "disableDays" => $disableDays,
            'first_day' => $this->property('first_day'),
            'locale' => Config::get('app.locale')
        ];

        $workingTimes = Reservation::getTimes($days['requestedDay'], date('H:i', $time));

        $this->page['datePickierParams'] = json_encode($datePickierParams);
        $this->page['isDesktop'] = $this->isDesktop();
        $this->page['workingTimes'] = $workingTimes;
        $this->page['submitName'] =  Lang::get('whsol.reservation::lang.component.form.submit');
        $this->page['reservNamePlaceHolder'] =  Lang::get('whsol.reservation::lang.component.form.name');
    }

    public function onChangeDate(){
        $date = \Input::get('date', False);
        if ( !$date ) {
            return False;
        }
        $date = new DateTime($date);

        $day = [
            'year' => $date->format('Y'),
            'month' => $date->format('m'),
            'day_number' => $date->format('d'),
        ];

        $timeFilter = null;
        $dateNow = new DateTime("now");
        if ($date->format('Y-m-d') == $dateNow->format('Y-m-d')) {
            $timeFilter = $dateNow->format('Y-m-d H:i');
        }
        $day = Day::getDay($date->format('Y-m-d'));
        $workingTimes = Reservation::getTimes($day, $timeFilter);

        $this->page['isDesktop'] = $this->isDesktop();
        $this->page["workingTimes"] = $workingTimes;
        $this->page["workingTimesLable"] = Lang::get('whsol.reservation::lang.component.form.time');
    }

    public function onDoReserv() {
        $returnValues = [
            'success' => True,
            'message' => ''
        ];
        /*
         *    Validate Input
         */
        $rules = [
            'reservName' => 'required',
            'reservDate' => 'required|date',
            'reservTime' => array('regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/')
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails())
            throw new ValidationException($validation);


        $params = \Input::all();

        $reservDateObj = new DateTime($params['reservDate']);

        $reservTimeObj = new DateTime($params['reservTime']);


        $day = [
            'year' => $reservDateObj->format('Y'),
            'month' => $reservDateObj->format('m'),
            'day_number' => $reservDateObj->format('d'),
        ];

        $timeFilter = null;
        $dateNow = new DateTime("now");
        if ($reservDateObj->format('Y-m-d') == $dateNow->format('Y-m-d')) {
            $timeFilter = $dateNow->format('Y-m-d H:i');
        }

        $day = Day::getDay($reservDateObj->format('Y-m-d'));
        $workingTimes = Reservation::getTimes($day, $timeFilter);

        $findTime = array_filter($workingTimes, function($item) use ($params) {
           if ( $item['time'] == $params['reservTime']) {
               return True;
           }
        });
        if ( count($findTime) > 0) {
            $findTime = array_pop($findTime);
            if ($findTime['reserved'] == True) {
                $returnValues['success'] = False;
                $returnValues['message'] = Lang::get('whsol.reservation::lang.component.message');
                return $returnValues;
            }
            else {
                $reserv = Reservation::firstOrNew([
                    'name' => $params['reservName'],
                    'month_number' => $reservDateObj->format('m'),
                    'day_number' => $reservDateObj->format('d'),
                    'year_number' =>  $reservDateObj->format('Y')
                ]);

                $reserv->reserv_date = $params['reservDate'];
                $reserv->reserv_time = $params['reservTime'];
                $reserv->reserv_hour = $reservTimeObj->format('H');
                $reserv->reserv_min = $reservTimeObj->format('i');

                $reserv->save();

                $this->page['reserv_date'] = $params['reservDate'];
                $this->page['reserv_time'] = $params['reservTime'];
                $this->page['reserv_name'] = $params['reservName'];
                $this->page['panel_title'] = Lang::get('whsol.reservation::lang.component.success.title');
                $this->page['reserv_name_lable'] = Lang::get('whsol.reservation::lang.component.success.name');
                $this->page['reserv_date_lable'] = Lang::get('whsol.reservation::lang.component.success.day');
                $this->page['reserv_time_lable'] = Lang::get('whsol.reservation::lang.component.success.time');
            }
        }
        else {
            $returnValues['success'] = False;
            $returnValues['message'] = Lang::get('whsol.reservation::lang.component.message');
        }

        return $returnValues;
    }

}