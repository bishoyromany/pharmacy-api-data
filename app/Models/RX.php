<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RX extends Model
{
    use HasFactory;

    protected $table = "RxDetails";

    protected $guarded = [];
}
