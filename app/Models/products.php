<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class products extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'bio',
        'img_in_page',
        'img_profile',
        'file_exe',
        'file_souce_code',
        'status',
        'count_prushed',
        'price_7day',
        'price_30day',
        'price_lifetime',
        'price_source_code',
        'create_by',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'file_exe',
        'file_souce_code',
    ];
}
