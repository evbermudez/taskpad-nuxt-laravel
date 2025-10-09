<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'statement','task_date','priority','position','is_done','user_id'
    ];

    protected $casts = [
        'task_date' => 'date',
        'is_done'   => 'boolean',
    ];
}
