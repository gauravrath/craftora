<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('manufacturer');
            $table->string('model_number')->nullable()->unique();
            $table->string('category')->nullable();
            $table->text('specifications')->nullable();
            $table->string('firmware_version')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['manufacturer', 'name']);
        });

        Schema::create('devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('model_id')->nullable();
            $table->string('name');
            $table->string('serial_number')->unique();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('model_id')->references('id')->on('device_models')->nullOnDelete();
            $table->index(['user_id', 'status']);
        });

        Schema::create('device_activations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('device_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('activation_code')->nullable()->unique();
            $table->timestamp('activated_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('device_id')->references('id')->on('devices')->cascadeOnDelete();
            $table->index(['device_id', 'status']);
        });

        Schema::create('device_sensor_data', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('device_id');
            $table->string('sensor_type');
            $table->string('reading_unit')->nullable();
            $table->decimal('value', 18, 6);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->foreign('device_id')->references('id')->on('devices')->cascadeOnDelete();
            $table->index(['device_id', 'sensor_type', 'created_at']);
        });

        Schema::create('device_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('device_id');
            $table->string('event_type');
            $table->string('severity')->default('info');
            $table->text('details')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('device_id')->references('id')->on('devices')->cascadeOnDelete();
            $table->index(['device_id', 'event_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_events');
        Schema::dropIfExists('device_sensor_data');
        Schema::dropIfExists('device_activations');
        Schema::dropIfExists('devices');
        Schema::dropIfExists('device_models');
    }
};
