<?php namespace WhSol\Reservation\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateMonthTable extends Migration
{

    public function up()
    {
        if ( !Schema::hasTable('whsol_reservation_month') )
        {
            Schema::create('whsol_reservation_month', function($table)
            {
                $table->increments('id');
                $table->string('month_name');
                $table->string('month_number');
                $table->integer('year');
                $table->timestamps();
            });
        }

    }

    public function down()
    {
        Schema::drop('whsol_reservation_month');
    }

}
