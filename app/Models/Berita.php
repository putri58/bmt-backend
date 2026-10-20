<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Berita extends Model
{
    protected $table = 'berita';

    protected $fillable = [
        'judul',
        'slug',
        'kategori',
        'ringkasan',
        'konten',
        'gambar',
        'status',
        'penulis_id',
    ];

    public function penulis()
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }

    protected static function booted(): void
    {
        static::creating(function (Berita $berita) {
            if (empty($berita->slug)) {
                $berita->slug = static::buatSlugUnik($berita->judul);
            }
        });

        static::updating(function (Berita $berita) {
            if ($berita->isDirty('judul') && !$berita->isDirty('slug')) {
                $berita->slug = static::buatSlugUnik(
                    $berita->judul,
                    $berita->id
                );
            }
        });
    }

    public static function buatSlugUnik(
        string $judul,
        ?int $kecualiId = null
    ): string {
        $slugDasar = Str::slug($judul);
        $slugDasar = $slugDasar ?: 'berita';

        $slug = $slugDasar;
        $nomor = 2;

        while (
            static::where('slug', $slug)
                ->when(
                    $kecualiId,
                    fn ($query) => $query->where('id', '!=', $kecualiId)
                )
                ->exists()
        ) {
            $slug = $slugDasar . '-' . $nomor;
            $nomor++;
        }

        return $slug;
    }
}