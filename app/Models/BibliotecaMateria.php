<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BibliotecaMateria extends Model
{
    protected $table = 'bibliotecamateria';
    protected $primaryKey = 'id_materia';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'visible',
    ];

    protected $casts = [
        'visible' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function recursos()
    {
        return $this->hasMany(
            Recurso::class,
            'id_materia',
            'id_materia'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeVisibles(Builder $query): Builder
    {
        return $query->where('visible', true);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function ocultar(): void
    {
        $this->update(['visible' => false]);
    }

    public function mostrar(): void
    {
        $this->update(['visible' => true]);
    }
}