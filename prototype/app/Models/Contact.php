<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    
    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'photo'];

    public function cities()
    {
        return $this->belongsToMany(City::class, 'contact_city', 'contact_id', 'city_id');
    }
}