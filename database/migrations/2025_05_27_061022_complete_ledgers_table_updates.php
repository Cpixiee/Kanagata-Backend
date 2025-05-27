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
        // 1. Add missing columns to ledgers table if they don't exist
        Schema::table('ledgers', function (Blueprint $table) {
            if (!Schema::hasColumn('ledgers', 'sub_budget')) {
                $table->string('sub_budget')->nullable()->after('budget');
            }
            if (!Schema::hasColumn('ledgers', 'recipient')) {
                $table->string('recipient')->nullable()->after('sub_budget');
            }
            if (!Schema::hasColumn('ledgers', 'month')) {
                $table->string('month')->nullable()->after('date');
            }
        });

        // 2. Fix status case - update lowercase to uppercase
        DB::table('ledgers')->where('status', 'listing')->update(['status' => 'LISTING']);
        DB::table('ledgers')->where('status', 'paid')->update(['status' => 'PAID']);
        DB::table('ledgers')->where('status', 'Listing')->update(['status' => 'LISTING']);
        DB::table('ledgers')->where('status', 'Paid')->update(['status' => 'PAID']);

        // 3. Update REVENUE PROJECT and COST PROJECT ledgers
        DB::table('ledgers')
            ->whereIn('category', ['REVENUE PROJECT', 'COST PROJECT'])
            ->update([
                'sub_budget' => '-',
                'recipient' => '-'
            ]);

        // 4. Convert COST PROJECT entries from debit to credit
        DB::table('ledgers')
            ->where('category', 'COST PROJECT')
            ->where('debit', '>', 0)
            ->update([
                'credit' => DB::raw('debit'),
                'debit' => 0
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the columns we added
        Schema::table('ledgers', function (Blueprint $table) {
            $table->dropColumn(['sub_budget', 'recipient', 'month']);
        });

        // Reverse status changes
        DB::table('ledgers')->where('status', 'LISTING')->update(['status' => 'listing']);
        DB::table('ledgers')->where('status', 'PAID')->update(['status' => 'paid']);
    }
};
