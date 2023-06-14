<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrucksSubunit extends Model
{
    use HasFactory;

    protected $table = 'truck_subunits';

    protected $fillable = [
        'main_truck',
        'subunit',
        'start_date',
        'end_date',
    ];

}
