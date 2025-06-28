<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    protected $fillable = ['numeroTable', 'nbrPlace'];

      // Relation avec les réservations
      public function reservations()
      {
          return $this->hasMany(Reservation::class, 'numeroTable', 'numeroTable');
      }

      public function disponibilites()
        {
             return $this->hasMany(TableDisponibilite::class);
        }

}
