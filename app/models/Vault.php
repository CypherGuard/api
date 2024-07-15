<?php

namespace App\Models;

class Vault extends Model
{
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name',
        'owner_id',
        'shared_users'
    ];

    /**
     * The attributes that should be hidden for serialization.
     * @var array
     */
    protected $hidden = [];

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'shared_id' => 'array',
    ];
}
