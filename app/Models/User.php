<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * name table
     * @var string
     */
    protected $table = 'users';

    /**
     * define create_at update_at
     * @var boolean
     */
    public $timestamps = true;

    /**
     * Os atributos que são atribuíveis
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'login',
        'email',
        'phone',
        'active',
        'level_of_access'

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];
}
