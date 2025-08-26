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
        Schema::create('doctype_requirement', function (Blueprint $table) {
            $table->string('doctypereq_id', 25)->primary();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_disabled')->default('0');

            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->boolean('is_optional')->default('0');
        });

        // Insert data after table creation
        $now = now();
        DB::table('doctype_requirement')->insert([
            [
                'created_at' => $now,
                'updated_at' => $now,
                'doctypereq_id' => 'KTP_LEADER',
                'name' => 'KTP Pembina',
                'description' => 'KTP Pembina (Ketua Yayasan/ Ketua Lembaga /Direktur /Penanggung Jawab)',
                'is_optional' => 0
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
                'doctypereq_id' => 'CERT_ESTABLISH',
                'name' => 'Akta Yayasan',
                'description' => 'Akta Yayasan dan Pengesahannya serta Akta Perubahannya (bila ada perubahan)',
                'is_optional' => 0
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
                'doctypereq_id' => 'POW_OF_ATTR',
                'name' => 'Surat kuasa',
                'description' => 'Surat kuasa bermeterai Rp. 10.000 dan stempel apabila pengurusan dikuasakan kepada orang lain, dengan melampirkan foto kopi KTP yang diberi kuasa',
                'is_optional' => 0
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
                'doctypereq_id' => 'CONSENT_LOCALCOMM',
                'name' => 'Persetujuan RT/RW',
                'description' => 'Persetujuan / Dukungan Warga Setempat di ketahui RT/RW',
                'is_optional' => 0
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
                'doctypereq_id' => 'RECOMM_LOCALSCHOOL',
                'name' => 'Rekomendasi dari sekolah terdekat',
                'description' => 'Persetujuan / Dukungan dari Satuan Pendidikan yang terdekat yang sejenis (min 2 satuan pendidikan)',
                'is_optional' => 0
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
                'doctypereq_id' => 'SITE_PLAN',
                'name' => 'Denah Lokasi',
                'description' => 'Denah Lokasi dengan Luas lahan min 300m2 dengan ruang belajar 2 (dua) ruang belajar',
                'is_optional' => 1
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
                'doctypereq_id' => 'RIPS',
                'name' => 'Rencana Induk Pengembangan Sekolah (RIPS)',
                'description' => 'Rencana Induk Pengembangan Sekolah (RIPS)',
                'is_optional' => 1
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctype_requirement');
    }
};
