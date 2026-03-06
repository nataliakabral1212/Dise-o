<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(): JsonResponse
    {
        $clientes = Cliente::all();
        return response()->json($clientes, 200);
    }

    public function show(int $id): JsonResponse
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre'   => 'required|string|max:150',
            'correo'   => 'required|email|max:191',
            'telefono' => 'required|string|size:10',
            'password' => 'required|string|min:6',
        ]);

        if (Cliente::correoExiste($request->correo)) {
            return response()->json([
                'message' => 'El correo ya está registrado.',
                'errors'  => ['correo' => ['El correo ya está en uso.']],
            ], 422);
        }

        $id      = Cliente::create($request->only('nombre', 'correo', 'telefono', 'password'));
        $cliente = Cliente::find($id);

        return response()->json([
            'message' => 'Cliente creado exitosamente',
            'data'    => $cliente,
        ], 201);
    }
}