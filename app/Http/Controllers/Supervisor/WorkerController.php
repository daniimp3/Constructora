<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\User;

class WorkerController extends Controller
{
    // Lista de trabajadores del supervisor
    public function index()
    {
        return User::where('role', 'trabajador')->get(['id','name','email']);
    }
}
