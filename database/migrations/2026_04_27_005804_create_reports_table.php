<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('photo'); // path to uploaded photo
            $table->text('description');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable(); // kecamatan
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable(); // alasan penolakan dari konsultan
            $table->foreignId('assigned_surveyor_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete(); // surveyor yang ditugaskan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
