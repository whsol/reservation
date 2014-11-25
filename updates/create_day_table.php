<?php namespace WhSol\Reservation\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateDayTable extends Migration
{

    public function up()
    {
        if ( !Schema::hasTable('whsol_reservation_month_day') )
        {
            Schema::create('whsol_reservation_month_day', function($table)
            {
                $table->increments('id');
                $table->string('day_name');
                $table->integer('day_number');
                $table->boolean('is_vacation')->default(false);
                $table->string('day_from')->nullable();
                $table->string('break_to')->nullable();
                $table->string('break_from')->nullable();
                $table->string('day_to')->nullable();
                $table->integer('minute_interval')->nullable();
                $table->integer('month_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::drop('whsol_reservation_month_day');
    }

}
