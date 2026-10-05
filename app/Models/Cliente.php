<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nombres',
        'apellidos',
        'tipo_documento',
        'numero_documento',
        'email',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'estado',
        'user_id',
        'registrado_por',
    ];

    public function nombreCompleto(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    /** @return BelongsTo<User, $this> */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<User, $this> */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /** @return HasMany<Cuenta, $this> */
    public function cuentas(): HasMany
    {
        return $this->hasMany(Cuenta::class);
    }

    /** @return HasMany<Prestamo, $this> */
    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }
}
