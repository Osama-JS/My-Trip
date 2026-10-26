<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_accounts', 'account_number')) {
                $table->string('account_number')->nullable()->after('beneficiary_name');
            }
            if (!Schema::hasColumn('bank_accounts', 'swift_code')) {
                $table->string('swift_code')->nullable()->after('iban');
            }
            if (!Schema::hasColumn('bank_accounts', 'currency')) {
                $table->string('currency')->default('SAR')->nullable()->after('swift_code');
            }
            if (!Schema::hasColumn('bank_accounts', 'branch')) {
                $table->string('branch')->nullable()->after('currency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn(['account_number', 'swift_code', 'currency', 'branch']);
        });
    }
};
