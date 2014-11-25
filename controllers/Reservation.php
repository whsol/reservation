<?php namespace WhSol\Reservation\Controllers;


use App;
use Lang;
use Excel;
use BackendMenu;
use Backend\Classes\Controller;
use WhSol\Reservation\Models\Reservation as MReservation;

class Reservation extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('WhSol.Reservation', 'reservation', 'reservation');
    }


    public function index_onPrint(){

        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            $reservations = MReservation::select('name', 'reserv_date', 'reserv_time')
                ->whereIn('id', $checkedIds)
                ->orderBy('year_number')
                ->orderBy('month_number')
                ->orderBy('day_number')
                ->orderBy('reserv_hour')
                ->orderBy('reserv_min')
                ->get();
            $export_res = [[
                Lang::get('whsol.reservation::lang.reservation.name'),
                Lang::get('whsol.reservation::lang.reservation.reserv_date'),
                Lang::get('whsol.reservation::lang.reservation.reserv_time')
            ]];
            foreach ($reservations as $reserv) {
                $export_res[] = array_values($reserv->toArray());
            }
            Excel::create('file', function($excel) use ($export_res) {
                $excel->sheet('Sheetname', function($sheet) use ($export_res) {

                    $sheet->rows($export_res);

                });

            })->store('xls', base_path() . \Config::get('cms.uploadsDir') . '/protected/');
        }


        return ['url' => \URL::to('/whsol/reservation/plist')];


    }

}
