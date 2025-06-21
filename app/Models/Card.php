<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'position',
        'priority',
        'due_date',
        'list_id',
        'user_id'
    ];

    protected $casts = [
        'due_date' => 'datetime'
    ];

    public function list()
    {
        return $this->belongsTo(KanbanList::class, 'list_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function board()
    {
        return $this->list->board();
    }
}
