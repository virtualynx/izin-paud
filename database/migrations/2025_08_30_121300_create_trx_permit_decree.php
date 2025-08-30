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
        Schema::create('trx_permit_decree', function (Blueprint $table) {
            $table->uuid('permit_decree_id')->primary();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_disabled')->default('0');
            
            $table->uuid('req_id')->nullable(); //to allow manual upload of already-existing decree
            $table->string('decree_num', 50)->unique();
            $table->enum('decree_type', ['NEW', 'EXTENSION', 'REVISION'])->default('NEW');

            $table->date('issued_date');
            $table->date('effective_date');
            $table->date('expired_date');

            $table->text('file_path');

            // $table
            //     ->foreign('req_id')
            //     ->references('req_id')
            //     ->on('trx_request')
            //     ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_permit_decree');
    }
};
