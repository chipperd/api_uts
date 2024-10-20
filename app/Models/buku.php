<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class buku extends Model
{
    use HasFactory; // Pastikan Anda mengimpor trait ini dari namespace yang benar

    protected $fillable = ['judul', 'penulis', 'harga', 'stok', 'kategori_id'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}