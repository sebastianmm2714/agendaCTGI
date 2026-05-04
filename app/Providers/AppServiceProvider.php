<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // 1. Automatización de Storage Link (Para cPanel/Producción)
        try {
            $link = public_path('storage');
            $target = storage_path('app/public');
            if (!file_exists($link) && file_exists($target)) {
                @symlink($target, $link);
            }
        } catch (\Exception $e) {
            // Silencioso si falla por restricciones del servidor
        }

        // 2. Registrar Helper Global para Firmas (Base64)
        view()->share('getFirmaBase64', function($path) {
            if (!$path) return '';
            $full = storage_path('app/public/' . $path);
            if (!file_exists($full) && !str_contains($path, 'firmas/')) {
                $full = storage_path('app/public/firmas/' . $path);
            }
            if (file_exists($full)) {
                $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
                $mime = 'image/' . ($ext == 'jpg' ? 'jpeg' : $ext);
                return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($full));
            }
            return '';
        });

        // 3. Registrar Observers para sincronización bidireccional
        \App\Models\LiderDeProceso::observe(\App\Observers\LiderDeProcesoObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);
    }
}
