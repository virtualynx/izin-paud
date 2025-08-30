<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('position', function (Blueprint $table) {
            $table->string('position_id', 25)->primary();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_disabled')->default('0');

            $table->string('name', 500);
            $table->string('description')->nullable();
            $table->string('parent_position_id', 25)->nullable();
        });

        Schema::table('position', function (Blueprint $table) {
            $table
                ->foreign('parent_position_id')
                ->references('position_id')
                ->on('position')
                ->onDelete('restrict');
        });

        // Insert data after table creation
        $now = now();
        DB::table('position')->insert([
            [
                'created_at' => $now,
                'updated_at' => $now,
                'position_id' => 'CAMAT_CIOMAS',
                'name' => 'Camat Ciomas',
                'description' => 'Camat Ciomas',
                'parent_position_id' => null,
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
                'position_id' => 'PENDKES_KASIE',
                'name' => 'Kasie Pendkes',
                'description' => 'Kepala Seksi Pendidikan dan Kesehatan',
                'parent_position_id' => 'CAMAT_CIOMAS',
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
                'position_id' => 'PENDKES_STAFF',
                'name' => 'Staff Pendkes',
                'description' => 'Staff Seksi Pendidikan dan Kesehatan',
                'parent_position_id' => 'PENDKES_KASIE',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position');
    }
};
