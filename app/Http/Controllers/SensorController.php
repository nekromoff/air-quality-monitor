<?php

namespace App\Http\Controllers;

use App\Sensor;
use App\SensorsValue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
        $banned_sensors = config('sensors.banned');
        date_default_timezone_set('Europe/Bratislava');
        $content = json_decode(request()->getContent());
        // ignore invalid objects
        if (!is_object($content)) {
            return;
        }
        $sensor_number = $content->esp8266id;
        // skip banned sensors
        if (is_array($banned_sensors) and in_array($sensor_number, $banned_sensors)) {
            return 'sorry, you have been banned';
        }
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
            } elseif (stripos($item->value_type, 'temperature') !== false) {
                $temperature = $item->value;
            } elseif (stripos($item->value_type, 'humidity') !== false) {
                $humidity = $item->value;
            } elseif (stripos($item->value_type, 'pressure') !== false) {
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
        $previous_from = Carbon::today()->startOfMonth()->subMonths(23);
        $previous_to = Carbon::today()->startOfMonth()->subMonths(12)->addDay(1);
        $current_from = Carbon::today()->startOfMonth()->subMonths(11);
        $sensors = Sensor::orderBy('location')->get()->keyBy('id');
        if (!Cache::has('averages')) {
            $averages['sensors']['now'] = SensorsValue::where('pm2_5', '>', 0)->where('pm10', '>', 0)->orderBy('created_at', 'DESC')->take(Sensor::count())->get()->keyBy('sensor_id');
            $averages['sensors']['today'] = SensorsValue::selectRaw('sensor_id, AVG(pm2_5) as pm2_5, AVG(pm10) as pm10, AVG(temperature) as temperature, AVG(humidity) as humidity, AVG(pressure) as pressure')->where('pm2_5', '>', 0)->where('pm10', '>', 0)->where('created_at', '>=', Carbon::today())->groupBy('sensor_id')->get()->keyBy('sensor_id');
            $averages['sensors']['week'] = SensorsValue::selectRaw('sensor_id, AVG(pm2_5) as pm2_5, AVG(pm10) as pm10, AVG(temperature) as temperature, AVG(humidity) as humidity, AVG(pressure) as pressure')->where('pm2_5', '>', 0)->where('pm10', '>', 0)->where('created_at', '>=', Carbon::today()->subDays(6))->groupBy('sensor_id')->get()->keyBy('sensor_id');
            $averages['sensors']['month'] = SensorsValue::selectRaw('sensor_id, AVG(pm2_5) as pm2_5, AVG(pm10) as pm10, AVG(temperature) as temperature, AVG(humidity) as humidity, AVG(pressure) as pressure')->where('pm2_5', '>', 0)->where('pm10', '>', 0)->where('created_at', '>=', Carbon::today()->subMonth())->groupBy('sensor_id')->get()->keyBy('sensor_id');
            //select  FROM sensors_values GROUP BY month;
            $averages['chart']['previous'] = SensorsValue::selectRaw("AVG(pm2_5) as pm2_5,AVG(pm10) as pm10,DATE_FORMAT(created_at, '%m/%Y') as yearmonth,MONTH(created_at) as keyed,DATE_FORMAT(created_at, '%Y%m') as ordering")->where('created_at', '>', $previous_from)->where('created_at', '<=', $previous_to)->where('pm2_5', '>', 0)->where('pm10', '>', 0)->orderBy('ordering', 'asc')->groupBy('ordering')->get()->keyBy('keyed');
            $averages['chart']['current'] = SensorsValue::selectRaw("AVG(pm2_5) as pm2_5,AVG(pm10) as pm10,DATE_FORMAT(created_at, '%m/%Y') as yearmonth,MONTH(created_at) as keyed,DATE_FORMAT(created_at, '%Y%m') as ordering")->where('created_at', '>', $current_from)->where('pm2_5', '>', 0)->where('pm10', '>', 0)->orderBy('ordering', 'asc')->groupBy('ordering')->get()->keyBy('keyed');
            $averages['yearly_10'] = SensorsValue::selectRaw('AVG(pm10) as pm10')->where('pm10', '>', 0)->where('created_at', '>', $current_from)->first()->pm10;
            $averages['yearly_2_5'] = SensorsValue::selectRaw('AVG(pm2_5) as pm2_5')->where('pm2_5', '>', 0)->where('created_at', '>', $current_from)->first()->pm2_5;
            $averages['24h_10'] = SensorsValue::selectRaw('AVG(pm10) as pm10')->where('pm10', '>', 0)->where('created_at', '>', Carbon::now()->subHours(24))->first()->pm10;
            $averages['24h_2_5'] = SensorsValue::selectRaw('AVG(pm2_5) as pm2_5')->where('pm2_5', '>', 0)->where('created_at', '>', Carbon::now()->subHours(24))->first()->pm2_5;
            Cache::put('averages', $averages, Carbon::now()->addMinutes(60));
        } else {
            $averages = Cache::get('averages');
        }
        return view('index', ['sensors' => $sensors, 'averages' => $averages]);
    }
}
