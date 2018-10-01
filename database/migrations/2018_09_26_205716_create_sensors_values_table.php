<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSensorsValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* sensor JSON payload:
        {"esp8266id": "1844542", "software_version": "NRZ-2017-099", "sensordatavalues":[{"value_type":"SDS_P1","value":"4.37"},{"value_type":"SDS_P2","value":"2.23"},{"value_type":"temperature","value":"25.10"},{"value_type":"humidity","value":"51.00"},{"value_type":"BMP_pressure","value":"102372"},{"value_type":"BMP_temperature","value":"25.61"},{"value_type":"samples","value":"634035"},{"value_type":"min_micro","value":"226"},{"value_type":"max_micro","value":"33352"},{"value_type":"signal","value":"-73"}]}
         */
        Schema::create('sensors_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sensor_id')->unsigned()->index();
            $table->decimal('pm10', 10, 2);
            $table->decimal('pm2_5', 10, 2);
            $table->decimal('temperature', 10, 2);
            $table->decimal('humidity', 10, 2);
            $table->integer('pressure')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sensors_values');
    }
}
