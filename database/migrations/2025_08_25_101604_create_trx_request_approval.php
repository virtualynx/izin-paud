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
        Schema::create('trx_request_approval', function (Blueprint $table) {
            $table->uuid('req_app_id')->primary();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_disabled')->default('0');

            $table->uuid('req_id');
            $table->integer('level'); //lower means higher
            $table->unsignedBigInteger('approver_user_id');
            $table->string('approver_position_id', 25); //for documentation purpose
            $table->string('approval_status', 25)->nullable(); //approved, revise, rejected
            
            $table
                ->foreign('req_id')
                ->references('req_id')
                ->on('trx_request')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_request_approval');
    }
};
