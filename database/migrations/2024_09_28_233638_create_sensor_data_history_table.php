<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSensorDataHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensor_data_history', function (Blueprint $table) {
            $table->id();
            $table->decimal('temperature', 5, 2); // Nhiệt độ
            $table->decimal('humidity', 5, 2);    // Độ ẩm
            $table->integer('light');               // Ánh sáng
            $table->timestamp('received_at');       // Thời gian nhận dữ liệu
            $table->timestamps();                    // Timestamps cho created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sensor_data_history');
    }
}
