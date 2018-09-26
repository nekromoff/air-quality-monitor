<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $fillable = ['number', 'location'];

    public function values()
    {
        return $this->hasMany('\App\SensorsValue');
    }
}
