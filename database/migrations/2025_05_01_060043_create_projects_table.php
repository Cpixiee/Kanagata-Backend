<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            
            // Data Customer
            $table->enum('customer_name', ['MAN', 'SMA', 'SMK', 'UNIVERSITAS'])
                  ->comment('Jenis institusi customer');
            
            // Data Tutor
            $table->enum('tutor_name', [
                'andar praskasa', 
                'danu steven', 
                'michale sudarsono', 
                'wit urrohman', 
                'ageng prasetyo'
            ])->comment('Nama tutor yang bertanggung jawab');
            
            // Periode
            $table->char('tahun_ajaran', 7)
                  ->comment('Format: 2023/2024');
            
            // Detail Kegiatan
            $table->enum('activity', [
                'Workshop', 
                'Pelatihan', 
                'Seminar', 
                'incubasi'
            ])->comment('Jenis kegiatan');
            
            $table->enum('prodi', [
                'TKJ', 
                'RPL', 
                'MM', 
                'BDP'
            ])->comment('Program studi');
            
            $table->enum('grade', [
                'X', 
                'XI', 
                'XII'
            ])->comment('Tingkat kelas');
            
            // Data Kuantitatif
            $table->tinyInteger('quantity')
                  ->comment('Jumlah peserta/siswa');
            
            $table->integer('rate_tutor')
                  ->comment('Rate honor tutor per jam');
            
            $table->integer('gt_rev')
                  ->comment('Gross Total Revenue');
            
            $table->tinyInteger('jam_pertemuan')
                  ->comment('Total jam pertemuan');
            
            // Data Keuangan
            $table->integer('sum_ip')
                  ->comment('Total invoice price');
            
            $table->integer('gt_cost')
                  ->comment('Gross Total Cost');
            
            $table->integer('gt_margin')
                  ->comment('Gross Total Margin');
            
            // AR (Accounts Receivable)
            $table->integer('ar')
                  ->comment('Account Receivable');
            
            $table->integer('ar_outstanding')
                  ->comment('AR yang belum dibayar');
            
            $table->integer('sum_ar')
                  ->comment('Total AR');
            
            $table->integer('sum_ar_paid')
                  ->comment('Total AR yang sudah dibayar');
            
            // Status
            $table->tinyInteger('todo')
                  ->comment('Todo list status');
            
            $table->integer('arus_kas')
                  ->nullable()
                  ->comment('Arus kas');
            
            $table->timestamps();
            
            // Index untuk pencarian
            $table->index('customer_name');
            $table->index('tahun_ajaran');
            $table->index('activity');
            $table->index('prodi');
            $table->index('grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};