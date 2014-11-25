<?php

return [
    'day' => [
        'day_name' => 'Day Name',
        'day_number' => 'Day Number',
        'is_vacation' => 'Vacation',
        'day_from' => 'Day begin',
        'break_from' => 'Break start',
        'break_to' => 'Break end',
        'day_to' => 'Day end',
        'label' => 'Day'
    ],
    'month' => [
        'label' => 'Month',
        'month_name' => 'Month',
        'return' => 'Return to the month list',
        'new' => 'New Month',
        'title' => 'Manage Month',
        'delete_confirm' => 'Do you really want to delete this month?',
        'year' => 'Year'
    ],
    'reservation' => [
        'label' => 'Reservation',
        'name' => 'Name',
        'reserv_date' => 'Day',
        'reserv_time' => 'Time',
        'return' => 'Return to the reservation list',
        'new' => 'New Reservation',
        'title' => 'Manage Reservation',
        'delete_confirm' => 'Do you really want to delete this reservation?'
    ],
    'settings' => [
        'minute_interval' => 'Time of receipt',
        'day_from' => 'Beginning of the day',
        'day_to' => 'End of the day',
        'break_from' => 'Beginning of the lounch break',
        'break_to' => 'End of the lounch break',
        'start_date' => 'Create calendar from',
        'end_date' => 'Create calendar from to',
        'tab' => [
            'general' => 'General',
            'generate' => 'Generate'
        ]
    ],
    'plugin' => [
        'name' => 'Resevation System Plugin',
        'description' => 'Provides some features for reservation',
        'settings' => [
            'label' => 'Reservation',
            'description' => 'Manage reservation preference'
        ],
        'nav' => [
            'label' => 'Reservation',
            'month' => 'Month',
            'reserv' => 'Reserv'
        ]
    ],
    'component' => [
        'name' => 'Reservation Form',
        'description' => 'Display a Reservation Form',
        'property' => [
            'first_day' => 'First day of week'
        ],
        'message' => 'Selected date and time is already reserved!',
        'success' => [
            'title' => 'Reservation',
            'time' => 'Time',
            'day' => 'Day',
            'name' => 'Name'
        ],
        'form' => [
            'name' => 'Name',
            'submit' => 'Submit',
            'time' => 'Time'
        ]
    ],
    'widget' => [
        'weekreserv' => [
            'label' => 'Show Statistic of reserve in week ago'
        ]
    ]
];