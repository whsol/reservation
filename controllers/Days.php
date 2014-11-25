<?php namespace WhSol\Reservation\Controllers;

use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use WhSol\Reservation\Models\Day;

class Days extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
}