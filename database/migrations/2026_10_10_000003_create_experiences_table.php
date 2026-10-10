<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('organization');
            $table->string('role');
            // Nilai dari App\Enums\ExperienceType. Nullable agar baris placeholder seeder tidak mengarang jenis.
            $table->string('type')->nullable()->index();
            // Nullable agar baris placeholder tidak memakai tanggal karangan. Admin wajib mengisinya.
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable(); // kosong = masih berjalan ("sekarang")
            $table->text('achievement')->nullable();
            $table->string('logo')->nullable();
            $table->unsignedInteger('sort')->default(0)->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
