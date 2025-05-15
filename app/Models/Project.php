<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'tutor_name',
        'tahun_ajaran',
        'activity',
        'prodi',
        'grade',
        'quantity',
        'rate_tutor',
        'gt_rev',
        'jam_pertemuan',
        'sum_ip',
        'gt_cost',
        'gt_margin',
        'ar',
        'ar_outstanding',
        'sum_ar',
        'sum_ar_paid',
        'todo',
        'arus_kas'
    ];

    public $timestamps = false;

    // Jika Anda ingin menggunakan enums
    public static function getCustomerNames()
    {
        return ['MAN', 'SMA', 'SMK', 'UNIVERSITAS'];
    }

    public static function getTutorNames()
    {
        return ['andar praskasa', 'danu steven', 'michale sudarsono', 'wit urrohman', 'ageng prasetyo'];
    }

    public static function getActivity()
    {
        return ['Workshop', 'Pelatihan', 'Seminar', 'Incubasi'];
    }

    public static function getProdi()
    {
        return ['TKJ', 'MM', 'RPL', 'BDP'];

    }

    public static function getgrade()
    {
        return ['X', 'XI', 'XII'];
    
    }

}