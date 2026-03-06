<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(): JsonResponse
    {
        $categorias = Categoria::all();
        return response()->json($categorias, 200);
    }

    public function show(int $id): JsonResponse
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        return response()->json($categoria, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string',
        ]);

        $id        = Categoria::create($request->only('nombre', 'descripcion'));
        $categoria = Categoria::find($id);

        return response()->json([
            'message' => 'Categoría creada exitosamente',
            'data'    => $categoria,
        ], 201);
    }
}