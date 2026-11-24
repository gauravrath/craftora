<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceEvent extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['device_id', 'event_type', 'severity', 'details', 'payload'];

    protected $casts = [
        'payload' => 'array',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
