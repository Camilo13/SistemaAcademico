<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Recurso extends Model
{
    protected $table = 'recurso';
    protected $primaryKey = 'id_recurso';
    public $timestamps = true;

    protected $fillable = [
        'id_materia',
        'titulo',
        'descripcion',
        'tipo',
        'origen',
        'url',
        'mime_type',
        'autor',
        'visible',
    ];

    protected $casts = [
        'visible' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | TIPOS DE RECURSO
    |--------------------------------------------------------------------------
    */

    public const TIPOS = [
        'archivo',
        'video',
        'audio',
        'enlace',
        'imagen',
    ];

    /*
    |--------------------------------------------------------------------------
    | ORIGEN DEL RECURSO
    |--------------------------------------------------------------------------
    */

    public const ORIGENES = [
        'archivo',
        'url',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function materia()
    {
        return $this->belongsTo(
            BibliotecaMateria::class,
            'id_materia',
            'id_materia'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS PARA UI
    |--------------------------------------------------------------------------
    */

    public function getUrlFinalAttribute(): string
    {
        return $this->origen === 'url'
            ? $this->url
            : Storage::url($this->url);
    }

    public function icono(): string
    {
        return match ($this->tipo) {
            'video'  => 'fa-video',
            'audio'  => 'fa-headphones',
            'enlace' => 'fa-link',
            'imagen' => 'fa-image',
            default  => 'fa-file-lines',
        };
    }

    public function esVisualizable(): bool
    {
        if ($this->origen !== 'archivo' || !$this->mime_type) {
            return true;
        }

        return str_contains($this->mime_type, 'pdf')
            || str_contains($this->mime_type, 'image');
    }

    public function accionLectura(): array
    {
        if ($this->tipo === 'video') {
            return ['Ver video', 'fa-play'];
        }

        if ($this->tipo === 'audio') {
            return ['Escuchar audio', 'fa-headphones'];
        }

        if ($this->origen === 'archivo') {
            return $this->esVisualizable()
                ? ['Ver recurso', 'fa-eye']
                : ['Descargar recurso', 'fa-download'];
        }

        return ['Abrir enlace', 'fa-arrow-up-right-from-square'];
    }

    public function accionAdmin(): array
    {
        return ['Ver', 'fa-eye'];
    }
}