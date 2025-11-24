<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\TemporaryPassword;

class WorkerController extends Controller
{
    public function index()
    {
        $workers = User::with('project')
            ->where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($workers);
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'nullable|string|max:20',
        'role' => 'required|in:trabajador,supervisor',
        'project_id' => 'nullable|exists:projects,id',  
        'specialty' => 'nullable|string|max:255',
    ]);

    // generar contraseña automática
    $randomPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
    
    $validated['password'] = Hash::make($randomPassword);

    $worker = User::create($validated);

    // guardar contraseña temporal
    TemporaryPassword::create([
        'user_id' => $worker->id,
        'password' => $randomPassword
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Usuario creado correctamente',
        'worker' => $worker->load('project'),
        'password' => $randomPassword
    ]);
    
        

        // Generar contraseña temporal
        $password = User::generateTemporaryPassword();

        $worker = User::create([
            'name' => $validated['name'],
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $validated['role'],
            'project_id' => $validated['project_id'] ?? null,
            'phone' => $validated['phone'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente',
            'worker' => $worker->load('project'),
            'generated_email' => $email,
            'generated_password' => $password
        ]);
    }

    public function update(Request $request, User $worker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $worker->id,
            'password' => ['nullable', Password::min(6)],
            'role' => 'required|in:supervisor,trabajador',
            'project_id' => 'nullable|exists:projects,id',
            'phone' => 'nullable|string|max:20'
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $worker->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
            'worker' => $worker->load('project')
        ]);
    }

    public function destroy(User $worker)
    {
        if ($worker->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar un administrador'
            ], 403);
        }

        $worker->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente'
        ]);
    }

    // Obtener solo supervisores
    public function supervisors()
    {
        $supervisors = User::supervisors()->get();
        return response()->json($supervisors);
    }

    // Obtener solo trabajadores
    public function workers()
    {
        $workers = User::workers()->with('project')->get();
        return response()->json($workers);
    }
    



public function getTemporaryPassword(User $worker)
{
    $tempPassword = $worker->temporaryPassword;
    
    if (!$tempPassword) {
        return response()->json([
            'success' => false,
            'message' => 'No hay contraseña temporal disponible'
        ], 404);
    }

    // Marcar como vista
    $tempPassword->markAsViewed();

    return response()->json([
        'success' => true,
        'password' => $tempPassword->password,
        'created_at' => $tempPassword->created_at->format('d/m/Y H:i'),
        'viewed_at' => $tempPassword->viewed_at ? $tempPassword->viewed_at->format('d/m/Y H:i') : null
    ]);
}
}