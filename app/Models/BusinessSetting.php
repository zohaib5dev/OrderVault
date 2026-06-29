<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'website',
        'address',
        'tax_type',
        'tax_rate',
        'registration_number',
        'currency',
        'logo'
    ];
 
    protected $casts = [
        'tax_rate' => 'float',
    ];
}