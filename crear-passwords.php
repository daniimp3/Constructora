<?php

use App\Models\User;
use App\Models\TemporaryPassword;
use Illuminate\Support\Facades\Hash;

// Buscar usuarios sin contraseña temporal
$users = User::doesntHave('temporaryPassword')
    ->where('role', '!=', 'admin')
    ->get();

echo "\n";
echo "═══════════════════════════════════════════════\n";
echo "   GENERADOR DE CONTRASEÑAS TEMPORALES\n";
echo "═══════════════════════════════════════════════\n";
echo "Usuarios sin contraseña temporal: {$users->count()}\n\n";

if ($users->count() === 0) {
    echo "✅ Todos los usuarios ya tienen contraseña temporal\n\n";
    return;
}

foreach ($users as $user) {
    // Generar contraseña aleatoria
    $tempPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
    
    // Guardar en temporary_passwords
    TemporaryPassword::create([
        'user_id' => $user->id,
        'password' => $tempPassword
    ]);
    
    // Actualizar contraseña del usuario
    $user->password = Hash::make($tempPassword);
    $user->save();
    
    echo "✅ {$user->name} ({$user->role}): {$tempPassword}\n";
}

echo "\n═══════════════════════════════════════════════\n";
echo "    ¡CONTRASEÑAS CREADAS EXITOSAMENTE!\n";
echo "═══════════════════════════════════════════════\n";
echo "  GUARDA ESTAS CONTRASEÑAS EN UN LUGAR SEGURO\n\n";