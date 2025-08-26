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
        Schema::create('mx_employee_position', function (Blueprint $table) {
            $table->uuid('mx_emp_pos_id')->primary();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_disabled')->default('0');

            $table->string('nip', 25);
            $table->string('position_id', 25);
            $table->boolean('is_pic')->default('0');

            $table
                ->foreign('nip')
                ->references('nip')
                ->on('user_profile')
                ->onDelete('restrict');

            $table
                ->foreign('position_id')
                ->references('position_id')
                ->on('position')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mx_employee_position');
    }
};
