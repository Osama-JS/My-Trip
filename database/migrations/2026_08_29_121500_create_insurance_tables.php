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
        // 1. Insurance Quotes Table
        if (!Schema::hasTable('insurance_quotes')) {
            Schema::create('insurance_quotes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('quote_reference')->unique()->index();
                $table->string('external_quote_id')->nullable()->index(); // Sitata quote id
                $table->string('booking_type')->default('flight'); // flight, trip, hotel, standalone
                $table->string('destination_country', 10)->nullable();
                $table->date('departure_date')->nullable();
                $table->date('return_date')->nullable();
                $table->integer('duration_days')->default(1);
                $table->decimal('trip_cost', 12, 2)->default(0);
                $table->integer('passengers_count')->default(1);
                $table->json('passengers_ages')->nullable();
                $table->string('coverage_type')->default('comprehensive'); // basic, comprehensive, schengen, vip
                $table->decimal('net_cost', 10, 2)->default(0); // Cost from Sitata
                $table->decimal('selling_price', 10, 2)->default(0); // Price charged to customer
                $table->decimal('platform_profit', 10, 2)->default(0); // Profit margin
                $table->string('currency', 10)->default('SAR');
                $table->json('raw_quote_data')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        // 2. Insurance Policies Table
        if (!Schema::hasTable('insurance_policies')) {
            Schema::create('insurance_policies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('insurance_quote_id')->nullable()->constrained('insurance_quotes')->nullOnDelete();
                
                // Polymorphic or direct booking relationships
                $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete(); // Flight bookings
                $table->foreignId('trip_booking_id')->nullable()->constrained('trip_bookings')->nullOnDelete(); // Package bookings
                $table->foreignId('hotel_booking_id')->nullable()->constrained('hotel_bookings')->nullOnDelete(); // Hotel bookings
                
                $table->string('booking_type')->default('flight'); // flight, trip, hotel, standalone
                $table->string('policy_number')->unique()->index();
                $table->string('external_policy_id')->nullable()->index(); // Sitata policy id
                $table->string('certificate_number')->nullable()->index();
                $table->string('status')->default('active'); // active, pending, cancelled, expired, refunded
                
                $table->string('coverage_type')->default('comprehensive');
                $table->string('destination_country', 10)->nullable();
                $table->date('departure_date')->nullable();
                $table->date('return_date')->nullable();
                $table->integer('duration_days')->default(1);
                
                $table->json('insured_passengers')->nullable(); // Array of travelers details
                
                $table->decimal('net_cost', 10, 2)->default(0);
                $table->decimal('selling_price', 10, 2)->default(0);
                $table->decimal('platform_profit', 10, 2)->default(0);
                $table->string('currency', 10)->default('SAR');
                
                $table->string('pdf_url')->nullable();
                $table->string('pdf_path')->nullable();
                $table->string('emergency_phone')->nullable();
                
                $table->json('raw_policy_data')->nullable();
                $table->timestamp('issued_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamps();
            });
        }

        // 3. Insurance API Logs Table
        if (!Schema::hasTable('insurance_api_logs')) {
            Schema::create('insurance_api_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('policy_id')->nullable()->constrained('insurance_policies')->nullOnDelete();
                $table->string('action')->index(); // quote, issue, cancel, get_details
                $table->string('endpoint')->nullable();
                $table->string('method', 10)->default('POST');
                $table->json('request_payload')->nullable();
                $table->json('response_payload')->nullable();
                $table->integer('status_code')->nullable();
                $table->decimal('execution_time', 8, 4)->nullable(); // In seconds
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }

        // 4. Update bookings tables to link insurance
        if (Schema::hasTable('bookings') && !Schema::hasColumn('bookings', 'insurance_policy_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->foreignId('insurance_policy_id')->nullable()->after('platform_profit')->constrained('insurance_policies')->nullOnDelete();
                $table->decimal('insurance_amount', 10, 2)->default(0)->after('insurance_policy_id');
            });
        }

        if (Schema::hasTable('trip_bookings') && !Schema::hasColumn('trip_bookings', 'insurance_policy_id')) {
            Schema::table('trip_bookings', function (Blueprint $table) {
                $table->foreignId('insurance_policy_id')->nullable()->after('platform_profit')->constrained('insurance_policies')->nullOnDelete();
                $table->decimal('insurance_amount', 10, 2)->default(0)->after('insurance_policy_id');
            });
        }

        if (Schema::hasTable('hotel_bookings') && !Schema::hasColumn('hotel_bookings', 'insurance_policy_id')) {
            Schema::table('hotel_bookings', function (Blueprint $table) {
                $table->foreignId('insurance_policy_id')->nullable()->after('total_price')->constrained('insurance_policies')->nullOnDelete();
                $table->decimal('insurance_amount', 10, 2)->default(0)->after('insurance_policy_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'insurance_policy_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropForeign(['insurance_policy_id']);
                $table->dropColumn(['insurance_policy_id', 'insurance_amount']);
            });
        }

        if (Schema::hasTable('trip_bookings') && Schema::hasColumn('trip_bookings', 'insurance_policy_id')) {
            Schema::table('trip_bookings', function (Blueprint $table) {
                $table->dropForeign(['insurance_policy_id']);
                $table->dropColumn(['insurance_policy_id', 'insurance_amount']);
            });
        }

        if (Schema::hasTable('hotel_bookings') && Schema::hasColumn('hotel_bookings', 'insurance_policy_id')) {
            Schema::table('hotel_bookings', function (Blueprint $table) {
                $table->dropForeign(['insurance_policy_id']);
                $table->dropColumn(['insurance_policy_id', 'insurance_amount']);
            });
        }

        Schema::dropIfExists('insurance_api_logs');
        Schema::dropIfExists('insurance_policies');
        Schema::dropIfExists('insurance_quotes');
    }
};
