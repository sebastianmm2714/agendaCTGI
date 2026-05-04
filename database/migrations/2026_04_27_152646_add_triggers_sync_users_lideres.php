<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Trigger: lideres_de_proceso -> users
        // Solo actualiza si los valores son diferentes para evitar bucles infinitos
        DB::unprepared("
            CREATE TRIGGER sync_lider_to_user_update
            AFTER UPDATE ON lideres_de_proceso
            FOR EACH ROW
            BEGIN
                UPDATE users 
                SET name = NEW.nombre,
                    email = NEW.email,
                    numero_documento = NEW.numero_documento,
                    tipo_documento = NEW.tipo_documento,
                    numero_cuenta_tipo = NEW.numero_cuenta_tipo
                WHERE (numero_documento = OLD.numero_documento OR email = OLD.email)
                AND (
                    NOT (name <=> NEW.nombre) OR 
                    NOT (email <=> NEW.email) OR 
                    NOT (numero_documento <=> NEW.numero_documento) OR
                    NOT (numero_cuenta_tipo <=> NEW.numero_cuenta_tipo)
                );
            END
        ");

        // 2. Trigger: users -> lideres_de_proceso
        DB::unprepared("
            CREATE TRIGGER sync_user_to_lider_update
            AFTER UPDATE ON users
            FOR EACH ROW
            BEGIN
                UPDATE lideres_de_proceso 
                SET nombre = NEW.name,
                    email = NEW.email,
                    numero_documento = NEW.numero_documento,
                    tipo_documento = NEW.tipo_documento,
                    numero_cuenta_tipo = NEW.numero_cuenta_tipo
                WHERE (numero_documento = OLD.numero_documento OR email = OLD.email)
                AND (
                    NOT (nombre <=> NEW.name) OR 
                    NOT (email <=> NEW.email) OR 
                    NOT (numero_documento <=> NEW.numero_documento) OR
                    NOT (numero_cuenta_tipo <=> NEW.numero_cuenta_tipo)
                );
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS sync_lider_to_user_update');
        DB::unprepared('DROP TRIGGER IF EXISTS sync_user_to_lider_update');
    }
};
