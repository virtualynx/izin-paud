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
        Schema::create('trx_request_document', function (Blueprint $table) {
            $table->uuid('req_doc_id')->primary();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_disabled')->default('0');
            
            $table->uuid('req_id');
            $table->string('doctypereq_id', 25);
            $table->text('file_path')->nullable();
            $table->boolean('is_waived')->default('0');

            $table
                ->foreign('req_id')
                ->references('req_id')
                ->on('trx_request')
                ->onDelete('restrict');

            $table
                ->foreign('doctypereq_id')
                ->references('doctypereq_id')
                ->on('doctype_requirement')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_request_document');
    }
};
