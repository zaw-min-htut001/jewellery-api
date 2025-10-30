<?php

namespace App\Models;

use App\Models\Variant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'base_price', 'slug'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
    ];

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }
}
