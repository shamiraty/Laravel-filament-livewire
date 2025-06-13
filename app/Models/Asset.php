<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'asset_type',
        'location',
        'registration_number',
        'purchase_date',
        'purchase_price',
        'custodian',
        'status',
    ];
}
