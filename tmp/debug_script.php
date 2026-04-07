<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$a = \App\Models\AgendaDesplazamiento::find(2);
if ($a) {
    file_put_contents('tmp/agenda2_debug.txt', 
        "User: " . $a->user->name . "\n" .
        "Supervisor: " . ($a->user->supervisor->nombre ?? 'NULL') . " | Firma: " . ($a->user->supervisor->firma ?? 'NULL') . "\n" .
        "Ordenador: " . ($a->user->ordenador->nombre ?? 'NULL') . " | Firma: " . ($a->user->ordenador->firma ?? 'NULL') . "\n"
    );
}
