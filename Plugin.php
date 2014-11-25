<?php namespace WhSol\Reservation;

use App;
use Lang;
use Backend;
use System\Classes\PluginBase;
use Illuminate\Foundation\AliasLoader;

class Plugin extends PluginBase
{

    /**
     * Boot method, called right before the request route.
     */
    public function boot()
    {
        // Register ServiceProviders
        App::register('\Maatwebsite\Excel\ExcelServiceProvider');

        // Register aliases
        $alias = AliasLoader::getInstance();
        $alias->alias('Excel', 'Maatwebsite\Excel\Facades\Excel');

    }


    public function pluginDetails()
    {
        return [
            'name' => Lang::get('whsol.reservation::lang.plugin.name'),
            'description' => Lang::get('whsol.reservation::lang.plugin.description'),
            'author' => 'WhSol',
            'icon' => 'icon-calendar'
        ];
    }

    public function registerComponents()
    {
        return [
            'WhSol\Reservation\Components\ReservationForm' => 'reservationForm',
        ];
    }

    public function registerSettings()
    {
        return [
            'reservation' => [
                'label'       => Lang::get('whsol.reservation::lang.plugin.settings.label'),
                'description' => Lang::get('whsol.reservation::lang.plugin.settings.description'),
                'class'       => 'WhSol\Reservation\Models\Settings',
                'icon'        => 'icon-calendar',
                'order'       => 500
            ]
        ];

    }

    public function registerNavigation()
    {
        return [
            'reservation' => [
                'label'       => Lang::get('whsol.reservation::lang.plugin.nav.label'),
                'url'         => Backend::url('whsol/reservation/month'),
                'icon'        => 'icon-calendar',
                'permissions' => ['user:*'],
                'order'       => 500,
                'sideMenu'    => [
                    'month'  => [
                        'label'       => Lang::get('whsol.reservation::lang.plugin.nav.month'),
                        'url'         => Backend::url('whsol/reservation/month'),
                        'icon'        => 'icon-calendar',
                        'permissions' => ['user:*'],
                    ],
                    'reserv'  => [
                        'label'       => Lang::get('whsol.reservation::lang.plugin.nav.reserv'),
                        'url'         => Backend::url('whsol/reservation/reservation'),
                        'icon'        => 'icon-users',
                        'permissions' => ['user:*'],
                    ],
                ]
            ]
        ];
    }

    public function registerReportWidgets()
    {
        return [
            'WhSol\Reservation\ReportWidgets\WeekReserv' => [
                'label'   => Lang::get('whsol.reservation::lang.widget.weekreserv.label'),
                'context' => 'dashboard'
            ]
        ];
    }

}