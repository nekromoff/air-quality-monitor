<?php

namespace App\Http\Controllers;

use App\Sensor;
use App\SensorsValue;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    /*
    //sensor JSON payload:
    {"esp8266id": "1844542", "software_version": "NRZ-2017-099", "sensordatavalues":[{"value_type":"SDS_P1","value":"4.37"},{"value_type":"SDS_P2","value":"2.23"},{"value_type":"temperature","value":"25.10"},{"value_type":"humidity","value":"51.00"},{"value_type":"BMP_pressure","value":"102372"},{"value_type":"BMP_temperature","value":"25.61"},{"value_type":"samples","value":"634035"},{"value_type":"min_micro","value":"226"},{"value_type":"max_micro","value":"33352"},{"value_type":"signal","value":"-73"}]}

    // values desc:
    SDS_P1: PM10
    SDS_P2: PM2.5
    temperature: temperature DHT22
    humidity: Humidity DHT22
    BME280_temperature: Temperature value BME280
    BME280_humidity: Humidity value BME280
    BME280_pressure: Air pressure BME280
    samples: Number of passes through the main loop
    min_micro: minimum time for a loop pass in microseconds
    max_micro: maximum time for a loop pass in microseconds
    signal: RSSI value (WLAN signal strength) in dBa
     */
    public function receive(Request $request)
    {
        date_default_timezone_set('Europe/Bratislava');
        $content = json_decode(request()->getContent());
        // ignore invalid objects
        if (!is_object($content)) {
            return;
        }
        $sensor_number = $content->esp8266id;
        Sensor::updateOrCreate(['number' => $sensor_number]);
        $pm10 = 0;
        $pm2_5 = 0;
        $temperature = 0;
        $humidity = 0;
        $pressure = 0;
        foreach ($content->sensordatavalues as $item) {
            if ($item->value_type == 'SDS_P1') {
                $pm10 = $item->value;
            } elseif ($item->value_type == 'SDS_P2') {
                $pm2_5 = $item->value;
            } elseif ($item->value_type == 'temperature') {
                $temperature = $item->value;
            } elseif ($item->value_type == 'humidity') {
                $humidity = $item->value;
            } elseif ($item->value_type == 'BMP_pressure') {
                $pressure = $item->value;
            }
        }
        $sensor = Sensor::where('number', $sensor_number)->first();
        $sensor_value = new SensorsValue;
        $sensor_value->sensor_id = $sensor->id;
        $sensor_value->pm10 = $pm10;
        $sensor_value->pm2_5 = $pm2_5;
        $sensor_value->temperature = $temperature;
        $sensor_value->humidity = $humidity;
        $sensor_value->pressure = $pressure;
        $sensor_value->save();
        return 'coolio';
    }

    public function show(Request $request)
    {
        $sensors = Sensor::get()->keyBy('id');
        $averages['daily'] = SensorsValue::selectRaw('sensor_id, AVG(pm10) as pm10, AVG(pm2_5) as pm2_5, AVG(temperature) as temperature, AVG(humidity) as humidity, DATE(created_at) as day')->where('created_at', '>=', date('Y-m-d', time() - 86400 * 14))->orderBy('day', 'desc')->groupBy('day', 'sensor_id')->get();
        $averages['chart'] = SensorsValue::selectRaw('AVG(pm10) as pm10, AVG(pm2_5) as pm2_5, DATE(created_at) as day')->where('pm10', '>', 0)->where('pm2_5', '>', 0)->orderBy('day', 'asc')->groupBy('day')->get()->keyBy('day');
        $averages['yearly_10'] = SensorsValue::selectRaw('AVG(pm10) as pm10')->where('pm10', '>', 0)->where('created_at', '>', date('Y-m-d H:i:s', strtotime('first day of this year')))->first()->pm10;
        $averages['yearly_2_5'] = SensorsValue::selectRaw('AVG(pm2_5) as pm2_5')->where('pm2_5', '>', 0)->where('created_at', '>', date('Y-m-d H:i:s', strtotime('first day of this year')))->first()->pm2_5;
        $averages['24h_10'] = SensorsValue::selectRaw('AVG(pm10) as pm10')->where('pm10', '>', 0)->where('created_at', '>', date('Y-m-d H:i:s', time() - 86400))->first()->pm10;
        $averages['24h_2_5'] = SensorsValue::selectRaw('AVG(pm2_5) as pm2_5')->where('pm2_5', '>', 0)->where('created_at', '>', date('Y-m-d H:i:s', time() - 86400))->first()->pm2_5;
        return view('index', ['sensors' => $sensors, 'averages' => $averages]);
    }
}
