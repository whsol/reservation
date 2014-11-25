<?php namespace WhSol\Reservation\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateReservTable extends Migration
{

    public function up()
    {
        if ( !Schema::hasTable('whsol_reservation_reserv') )
        {
            Schema::create('whsol_reservation_reserv', function($table)
            {
                $table->increments('id');
                $table->string('name');
                $table->date('reserv_date');
                $table->string('reserv_time');
                $table->integer('reserv_hour');
                $table->integer('reserv_min');
                $table->integer('month_number');
                $table->integer('day_number');
                $table->integer('year_number');

                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::drop('whsol_reservation_reserv');
    }

}
