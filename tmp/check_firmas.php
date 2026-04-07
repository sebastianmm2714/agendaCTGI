<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Funcionario;
use App\Models\AgendaDesplazamiento;

$a = AgendaDesplazamiento::find(2);
if (!$a) {
    echo "Agenda #2 not found.\n";
    exit;
}

echo "Agenda #2:\n";
echo "User: " . $a->user->name . " (ID: {$a->user->id})\n";
echo "Supervisor ID: " . ($a->user->supervisor_id ?? 'NULL') . "\n";

$supervisor = $a->user->supervisor;
if ($supervisor) {
    echo "Supervisor: " . $supervisor->nombre . " (Firma: " . ($supervisor->firma ?? 'NULL') . ")\n";
} else {
    echo "No supervisor relationship loaded.\n";
}

$ordenador = $a->user->ordenador;
if ($ordenador) {
    echo "Ordenador: " . $ordenador->nombre . " (Firma: " . ($ordenador->firma ?? 'NULL') . ")\n";
} else {
    echo "No ordenador relationship loaded.\n";
}

echo "Agenda state: " . $a->estado->nombre . "\n";
echo "--- ALL FUNCIONARIOS WITH FIRMAS ---\n";
foreach (Funcionario::whereNotNull('firma')->get() as $f) {
    echo "Funcionario: {$f->nombre} | Firma: {$f->firma}\n";
}
