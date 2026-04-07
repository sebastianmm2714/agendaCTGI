<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$fs = \App\Models\Funcionario::where('nombre', 'like', '%MARIA HELENA%')->get();
$out = "";
foreach($fs as $f) {
    $out .= "ID: {$f->id} | Name: {$f->nombre} | Doc: " . ($f->numero_documento ?? 'NULL') . " | Email: " . ($f->email ?? 'NULL') . " | Firma: " . ($f->firma ?? 'NULL') . "\n";
}
file_put_contents('tmp/maria_debug.txt', $out);
echo "Done check maria\n";
