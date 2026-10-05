<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bitacora extends Model
{
    use HasFactory;

    /** Bitácora es inmutable: solo tiene created_at */
    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'tipo_evento',
        'descripcion',
        'datos_adicionales',
        'ip_address',
        'user_agent',
        'user_id',
        'created_at',
    ];

    /** Registrar un evento en la bitácora de forma estática */
    public static function registrar(
        string $tipoEvento,
        string $descripcion,
        ?int $userId = null,
        ?array $datosAdicionales = null
    ): self {
        return self::create([
            'tipo_evento' => $tipoEvento,
            'descripcion' => $descripcion,
            'user_id' => $userId,
            'datos_adicionales' => $datosAdicionales,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /** @return BelongsTo<User, $this> */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'datos_adicionales' => 'array',
            'created_at' => 'datetime',
        ];
    }
}
