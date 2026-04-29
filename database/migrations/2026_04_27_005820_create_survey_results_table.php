<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->foreignId('surveyor_id')->constrained('users')->onDelete('cascade');
            $table->string('photo')->nullable(); // foto hasil survei lapangan
            $table->text('notes');              // catatan surveyor
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('synced')->default(false); // sudah di-sync ke server?
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_results');
    }
};
