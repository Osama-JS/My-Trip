<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->string('name_ar');
            $table->string('name_en');
            $table->decimal('extra_cost', 10, 2)->default(0);
            $table->string('currency', 10)->default('TRY');
            $table->boolean('is_replacement')->default(false)->comment('true = replaces a tour, false = extra add-on');
            $table->timestamps();

            $table->index('trip_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_addons');
    }
};
