<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SensorsValue extends Model
{
    public function sensor()
    {
        return $this->belongsTo('\App\Sensor');
    }
}
