<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'commission_type')) {
                $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage')->after('iban_number');
            }
            if (!Schema::hasColumn('companies', 'commission_value')) {
                $table->decimal('commission_value', 10, 2)->default(0.00)->after('commission_type');
            }
        });

        // Copy existing commission_rate values to commission_value if present
        if (Schema::hasColumn('companies', 'commission_rate')) {
            DB::statement('UPDATE companies SET commission_value = commission_rate WHERE commission_rate > 0 AND (commission_value = 0 OR commission_value IS NULL)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('companies', 'commission_type')) {
                $columnsToDrop[] = 'commission_type';
            }
            if (Schema::hasColumn('companies', 'commission_value')) {
                $columnsToDrop[] = 'commission_value';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
