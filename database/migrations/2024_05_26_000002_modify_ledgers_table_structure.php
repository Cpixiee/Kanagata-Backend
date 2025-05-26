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
        // Cek apakah foreign key exists
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'ledgers'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            AND CONSTRAINT_NAME = 'ledgers_budget_id_foreign'
        ");

        Schema::table('ledgers', function (Blueprint $table) {
            // Tambah kolom baru terlebih dahulu
            if (!Schema::hasColumn('ledgers', 'budget')) {
                $table->string('budget')->nullable()->after('category');
            }
            if (!Schema::hasColumn('ledgers', 'description')) {
                $table->string('description')->nullable()->after('credit');
            }
        });

        // Copy data dari budget_id ke budget jika kolom budget_id ada
        if (Schema::hasColumn('ledgers', 'budget_id')) {
            DB::statement('UPDATE ledgers l LEFT JOIN projects p ON l.budget_id = p.id SET l.budget = p.coa WHERE l.budget_id IS NOT NULL');
        }

        Schema::table('ledgers', function (Blueprint $table) {
            // Hapus foreign key jika ada
            if (!empty($foreignKeys)) {
                $table->dropForeign('ledgers_budget_id_foreign');
            }

            // Hapus kolom lama jika ada
            $columns = ['budget_id', 'sub_budget', 'recipient', 'month'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('ledgers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledgers', function (Blueprint $table) {
            // Hapus kolom baru
            if (Schema::hasColumn('ledgers', 'budget')) {
                $table->dropColumn('budget');
            }
            if (Schema::hasColumn('ledgers', 'description')) {
                $table->dropColumn('description');
            }

            // Tambah kolom lama
            if (!Schema::hasColumn('ledgers', 'budget_id')) {
                $table->foreignId('budget_id')->nullable()->constrained('projects')->onDelete('cascade');
            }
            if (!Schema::hasColumn('ledgers', 'sub_budget')) {
                $table->enum('sub_budget', [
                    'BY PAYROLL',
                    'BY PROJECT',
                    'BY PAJAK',
                    'SHAREHOLDER',
                    'BY INVENTARIS',
                    'BY SEWA',
                    'BY TUTOR',
                    'BY TAKIS'
                ])->nullable();
            }
            if (!Schema::hasColumn('ledgers', 'recipient')) {
                $table->enum('recipient', [
                    'rizal ramdhanu',
                    'andar rahman',
                    'fariz dandy',
                    'adam',
                    'wirakusuma'
                ])->nullable();
            }
            if (!Schema::hasColumn('ledgers', 'month')) {
                $table->string('month')->nullable();
            }
        });
    }
}; 