<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    public function updated(User $user): void
    {
        // Buscar si el usuario es un líder de proceso
        $lider = \App\Models\LiderDeProceso::where('numero_documento', $user->getOriginal('numero_documento'))
            ->orWhere('email', $user->getOriginal('email'))
            ->first();

        if ($lider) {
            // Mapear rol de usuario a tipo de líder
            $tipoLider = match($user->role) {
                'supervisor_contrato' => 'SUPERVISOR',
                'ordenador_gasto' => 'ORDENADOR',
                'viaticos' => 'VIATICOS',
                default => $lider->tipo // Mantener el anterior si no es un rol de líder directo
            };

            // Actualizar sin disparar eventos de LiderDeProceso para evitar bucle infinito
            \App\Models\LiderDeProceso::withoutEvents(function () use ($lider, $user, $tipoLider) {
                $lider->update([
                    'nombre' => $user->name,
                    'email' => $user->email,
                    'numero_documento' => $user->numero_documento,
                    'tipo_documento' => $user->tipo_documento,
                    'numero_cuenta_tipo' => $user->numero_cuenta_tipo,
                    'tipo' => $tipoLider
                ]);
            });
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
