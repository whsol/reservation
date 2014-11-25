<?php

App::before(function($request)
{
        Route::group(['prefix' => '/whsol/reservation/'],function() {

            Route::any('plist',  //'WhSol\Reservation\Controllers\Reservation@plist');
                function()
            {
                if (!BackendAuth::check()) {
                    return Redirect::to('/');//\Redirect::make("Page not found", 404);
//                    return \Response::view('404', array(), 404);
                }
                else {
                    $date = new DateTime('now');
                    $headers = array(
                        'Content-Type: application/vnd.ms-excel; charset=utf-8',
                        'Content-Disposition' => sprintf('attachment; filename="Print-%s.xls"', $date->format('m-d-Y')),
                        'Content-Transfer-Encoding' => ' binary'
                    );
                    $fileContent = File::get(base_path() . \Config::get('cms.uploadsDir') . '/protected/' . 'file.xls');
                    return \Response::make($fileContent, 200, $headers);
                    // return \Response::download('uploads/protected/file.xls', , $headers);
                }
            });
        });
});