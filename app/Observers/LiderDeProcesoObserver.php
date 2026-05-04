<?php

namespace App\Observers;

use App\Models\LiderDeProceso;

class LiderDeProcesoObserver
{
    /**
     * Handle the LiderDeProceso "created" event.
     */
    public function created(LiderDeProceso $liderDeProceso): void
    {
        //
    }

    public function updated(LiderDeProceso $liderDeProceso): void
    {
        // Buscar el usuario vinculado por el documento o email original
        $user = \App\Models\User::where('numero_documento', $liderDeProceso->getOriginal('numero_documento'))
            ->orWhere('email', $liderDeProceso->getOriginal('email'))
            ->first();

        if ($user) {
            // Mapear tipo de líder a rol de sistema
            $role = match($liderDeProceso->tipo) {
                'SUPERVISOR' => 'supervisor_contrato',
                'ORDENADOR' => 'ordenador_gasto',
                'VIATICOS'   => 'viaticos',
                default      => $user->role // Mantener el anterior si no es un tipo estándar
            };

            // Actualizar sin disparar eventos de User para evitar bucle infinito
            \App\Models\User::withoutEvents(function () use ($user, $liderDeProceso, $role) {
                $user->update([
                    'name' => $liderDeProceso->nombre,
                    'email' => $liderDeProceso->email,
                    'numero_documento' => $liderDeProceso->numero_documento,
                    'tipo_documento' => $liderDeProceso->tipo_documento,
                    'numero_cuenta_tipo' => $liderDeProceso->numero_cuenta_tipo,
                    'role' => $role,
                ]);
            });
        }
    }

    /**
     * Handle the LiderDeProceso "deleted" event.
     */
    public function deleted(LiderDeProceso $liderDeProceso): void
    {
        //
    }

    /**
     * Handle the LiderDeProceso "restored" event.
     */
    public function restored(LiderDeProceso $liderDeProceso): void
    {
        //
    }

    /**
     * Handle the LiderDeProceso "force deleted" event.
     */
    public function forceDeleted(LiderDeProceso $liderDeProceso): void
    {
        //
    }
}
