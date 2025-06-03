<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        // Langkah 1: Tambah kolom tutor_id
        Schema::table('logsheets', function (Blueprint $table) {
            $table->foreignId('tutor_id')->nullable()->after('tutor');
        });

        // Langkah 2: Update data yang ada
        try {
            DB::table('logsheets')
                ->join('tutors', 'logsheets.tutor', '=', 'tutors.name')
                ->update(['logsheets.tutor_id' => DB::raw('tutors.id')]);
        } catch (\Exception $e) {
            // Log error jika terjadi masalah saat update
            Log::error('Error updating tutor_id: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        Schema::table('logsheets', function (Blueprint $table) {
            $table->dropColumn('tutor_id');
        });
    }
}; 