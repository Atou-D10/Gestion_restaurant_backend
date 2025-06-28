<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = ['dateReservation', 'heureDebut', 'nbrPersonne', 'heureFin', 'numeroTable','client_id'];

      // Relation avec le modèle Table
      public function table()
      {
          return $this->belongsTo(Table::class, 'numeroTable', 'numeroTable');
      }

         // Relation avec le modèle Client
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function disponibilites()
    {
        return $this->hasMany(TableDisponibilite::class);
    }

}
