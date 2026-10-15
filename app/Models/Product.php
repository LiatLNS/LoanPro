<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Nombre explícito de la tabla (opcional, pero útil por claridad)
    protected $table = 'products';

    // Campos permitidos para asignación masiva (creación y actualización)
    protected $fillable = [
        'name',
        'sku',
        'description',
        'category',
        'price',
        'stock',
        'weight_kg',
    ];
}