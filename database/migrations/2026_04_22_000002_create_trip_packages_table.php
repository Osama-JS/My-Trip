<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('hotel_name')->nullable();
            $table->tinyInteger('hotel_stars')->default(4)->comment('1-5 stars');
            $table->enum('tier', ['economy', 'gold', 'vip'])->default('economy');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('trip_id');
            $table->index('tier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_packages');
    }
};
