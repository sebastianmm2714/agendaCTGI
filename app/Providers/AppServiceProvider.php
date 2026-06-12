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

        // Forzar HTTPS en producción o si se define FORCE_HTTPS=true
        if (config('app.env') === 'production' || env('FORCE_HTTPS', false)) {
            \URL::forceScheme('https');
        }

        // 1. Automatización de Migraciones (Para cPanel/Producción)
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('legalizaciones') || 
                !\Illuminate\Support\Facades\Schema::hasColumn('legalizaciones', 'soportes_desplazamiento')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Exception $e) {
            // Silencioso si falla por restricciones o base de datos no configurada
        }

        // 2. Automatización de Carpetas de Subida y Permisos
        try {
            $paths = [
                storage_path('app/public/legalizacion/fotos'),
                storage_path('app/public/legalizacion/planillas'),
                storage_path('app/public/legalizacion/declaraciones'),
                storage_path('app/public/legalizacion/tiquetes'),
                storage_path('app/final_pdfs'),
            ];
            foreach ($paths as $p) {
                if (!file_exists($p)) {
                    @mkdir($p, 0775, true);
                }
            }
        } catch (\Exception $e) {
            // Silencioso
        }

        // 3. Automatización de Storage Link (Para cPanel/Producción)
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
