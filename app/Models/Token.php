<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Token Model
 * @package App\Models
 */
class Token extends Model
{
    /**
     * @var string
     */
    protected $table = 'tokens';

    /**
     * @var bool
     */
    public $timestamps = true;

    //protected $fillable = [];

    /**
     * @var array
     */
    protected $hidden = [];
}