<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kategori pengalaman untuk chart Race Strategy: professional / organizational.
 * Null = tidak tampil di chart (Pendidikan, Lomba, Sertifikasi).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('category')->nullable()->after('type')->index(); // App\Enums\ExperienceCategory
        });

        // Isi data lama berdasarkan jenis.
        DB::table('experiences')->whereIn('type', ['magang', 'kerja'])->update(['category' => 'professional']);
        DB::table('experiences')->whereIn('type', ['organisasi', 'kepanitiaan'])->update(['category' => 'organizational']);
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn('category');
        });
    }
};
