<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableDisponibilite extends Model
{
    use HasFactory;

    protected $fillable = ['table_id', 'reservation_id', 'date', 'heureDebut', 'heureFin'];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
