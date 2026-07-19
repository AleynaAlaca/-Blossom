<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = ['title', 'completed', 'type', 'category', 'priority', 'due_date', 'amount'];

    protected function casts(): array
    {
        return ['completed' => 'boolean', 'due_date' => 'date:Y-m-d', 'amount' => 'decimal:2'];
    }
}
