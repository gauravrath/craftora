<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['user_id', 'model_id', 'name', 'serial_number', 'status', 'notes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->belongsTo(DeviceModel::class, 'model_id');
    }

    public function activations()
    {
        return $this->hasMany(DeviceActivation::class);
    }

    public function sensorData()
    {
        return $this->hasMany(DeviceSensorData::class);
    }

    public function events()
    {
        return $this->hasMany(DeviceEvent::class);
    }
}
