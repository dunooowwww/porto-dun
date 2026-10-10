<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mascot_messages', function (Blueprint $table) {
            $table->id();
            $table->string('section')->unique(); // hero, pit-wall, race, garage, box-box
            $table->string('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mascot_messages');
    }
};
