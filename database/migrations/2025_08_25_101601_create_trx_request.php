<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trx_request', function (Blueprint $table) {
            $table->uuid('req_id')->primary();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_disabled')->default('0');
            
            $table->string('reg_num', 50);
            $table->string('status', 25); //draft, submitted

            $table->string('name', 500);
            $table->string('foundation_type', 255);
            $table->string('phone', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('pic_name', 255)->nullable();
            $table->string('pic_position', 255)->nullable();
            $table->integer('founded_year')->nullable();
            $table->integer('student_count')->nullable();
            $table->integer('teacher_count')->nullable();
            $table->text('vision_mission')->nullable();

            $table->decimal('lat', 11, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_request');
    }
};
