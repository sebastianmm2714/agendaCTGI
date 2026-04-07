<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Funcionario;
use Illuminate\Support\Facades\DB;

echo "Starting cleanup of duplicated funcionarios...\n";

$dupes = Funcionario::select('nombre', DB::raw('count(*) as count'))
    ->groupBy('nombre')
    ->having('count', '>', 1)
    ->get();

foreach ($dupes as $dupe) {
    echo "Processing duplicate: {$dupe->nombre}\n";
    
    // Get all records for this name
    $records = Funcionario::where('nombre', $dupe->nombre)->get();
    
    // Find the 'best' record (one with email and document)
    $best = $records->first(fn($f) => !empty($f->email) && !empty($f->numero_documento));
    
    if (!$best) {
        $best = $records->sortByDesc('created_at')->first();
    }
    
    echo "Best record ID: {$best->id}\n";
    
    foreach ($records as $f) {
        if ($f->id == $best->id) continue;
        
        echo "Merging ID {$f->id} into ID {$best->id}...\n";
        
        // Relink Users (supervisor_id)
        User::where('supervisor_id', $f->id)->update(['supervisor_id' => $best->id]);
        
        // Relink Users (ordenador_id)
        User::where('ordenador_id', $f->id)->update(['ordenador_id' => $best->id]);
        
        // Transfer signature if best doesn't have it but this one does
        if (empty($best->firma) && !empty($f->firma)) {
            $best->update(['firma' => $f->firma]);
        }
        
        // Delete the redundant record
        $f->delete();
    }
}

echo "Cleanup finished. Now ensuring User signatures are synced with Funcionarios...\n";
foreach (User::whereNotNull('firma')->get() as $u) {
    Funcionario::where('email', $u->email)
        ->orWhere('numero_documento', $u->numero_documento)
        ->update(['firma' => $u->firma]);
}

echo "All done.\n";
