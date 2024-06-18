<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lobbies extends Model
{
    use HasFactory;
    protected $fillable = ['playerOne','playerTwo','gameType', 'turn', 'speed'];
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator');
    }
    public function playerOne()
    {
        return $this->belongsTo(User::class, 'playerOne');
    }
    public function playerTwo()
    {
        return $this->belongsTo(User::class, 'playerTwo');
    }
    public function fields()
    {
        return $this->hasMany(fields::class);
    }
}
