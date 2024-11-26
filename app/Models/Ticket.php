<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'thumbnail',
        'address',
        'path_video',
        'price',
        'is_popular',
        'about',
        'open_time_at',
        'closed_time_at',
        'category_id',
        'seller_id',
        'slug',
    ];
}
