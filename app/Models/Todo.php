<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Todo extends Model
{
    use HasFactory;

    protected $guarded    = ['id'];
    protected $attributes = [
        'is_completed' => false,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
