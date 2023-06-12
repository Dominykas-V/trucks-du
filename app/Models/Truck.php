<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    use HasFactory;

    protected $table = 'trucks';

    protected $primaryKey = 'unit_number';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'unit_number',
        'year',
        'notes',
    ];
}
