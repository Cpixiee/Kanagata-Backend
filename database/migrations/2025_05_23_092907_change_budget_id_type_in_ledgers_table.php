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
        Schema::table('ledgers', function (Blueprint $table) {
            // Drop foreign key constraint terlebih dahulu
            $table->dropForeign(['budget_id']);
            
            // Ubah tipe data kolom budget_id menjadi string
            $table->string('budget_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledgers', function (Blueprint $table) {
            // Ubah kembali tipe data ke bigInteger dan tambahkan foreign key
            $table->bigInteger('budget_id')->unsigned()->change();
            $table->foreign('budget_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }
};
