<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPage extends Model
{
    use HasFactory;

    // Menentukan kolom apa saja yang boleh diisi saat proses save ke database
    protected $fillable = [
        'user_id',
        'product_name',
        'description',
        'features',
        'target_audience',
        'price',
        'unique_selling_points',
        'ai_generated_content',
    ];

    // Kita definisikan juga bahwa data JSON harus otomatis diubah menjadi Array saat ditarik, dan sebaliknya
    protected $casts = [
        'features' => 'array',
    ];

    // Membuat relasi balik ke Model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}