<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class Document extends Model
{
    abstract function updateMovementStatus();
}