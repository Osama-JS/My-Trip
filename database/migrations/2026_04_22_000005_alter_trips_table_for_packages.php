<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            // New rich-content fields
            $table->text('includes_ar')->nullable()->after('description_en');
            $table->text('includes_en')->nullable()->after('includes_ar');
            $table->text('excludes_ar')->nullable()->after('includes_en');
            $table->text('excludes_en')->nullable()->after('excludes_ar');
            $table->text('children_policy_ar')->nullable()->after('excludes_en');
            $table->text('children_policy_en')->nullable()->after('children_policy_ar');

            // Make old price fields nullable (backward compat — do NOT drop them)
            $table->decimal('price', 8, 2)->nullable()->change();
            $table->decimal('price_before_discount', 8, 2)->nullable()->change();
            $table->integer('personnel_capacity')->nullable()->change();
            $table->date('expiry_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn([
                'includes_ar', 'includes_en',
                'excludes_ar', 'excludes_en',
                'children_policy_ar', 'children_policy_en',
            ]);
        });
    }
};
