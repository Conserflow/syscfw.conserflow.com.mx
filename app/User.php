<?php

namespace App;

use App\RHModels\Empleado;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'name_user',
        'email',
        'password',
        'tipo_ubicacion_id',
        'condicion',
        'session_id',
        'validar_nav'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /************* RELACIONES *************/
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, "empleado_id");
    }

    /************* SCOPES *************/
    public function scopeByEmpleado($query, $id)
    {
        return $query->whereHas("empleado", function ($q) use ($id)
        {
            return $q->where("id", $id);
        });
    }

    public function scopeByActivos($query)
    {
        return $query->where("condicion", 1);
    }
}
