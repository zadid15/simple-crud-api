<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    //
    protected $table = 'barangs';
    protected $fillable = [
        'nama_barang',
        'berat',
        'poto'
    ];

    protected function poto(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('storage/posts/' . $value),
        );
    }
}

