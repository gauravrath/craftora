<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceSensorData extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'device_sensor_data';

    protected $fillable = ['device_id', 'sensor_type', 'reading_unit', 'value', 'metadata'];

    protected $casts = [
        'value' => 'decimal:6',
        'metadata' => 'array',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
