<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Cliente
{
    public static function all(): array
    {
        return DB::select(
            "SELECT id, nombre, correo, telefono, created_at
             FROM clientes
             ORDER BY id DESC"
        );
    }

    public static function find(int $id): ?object
    {
        $result = DB::select(
            "SELECT id, nombre, correo, telefono, created_at
             FROM clientes WHERE id = ? LIMIT 1",
            [$id]
        );
        return $result[0] ?? null;
    }

    public static function correoExiste(string $correo, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $r = DB::select(
                "SELECT id FROM clientes WHERE correo = ? AND id != ? LIMIT 1",
                [$correo, $excludeId]
            );
        } else {
            $r = DB::select(
                "SELECT id FROM clientes WHERE correo = ? LIMIT 1",
                [$correo]
            );
        }
        return count($r) > 0;
    }

    public static function create(array $data): int
    {
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);

        DB::insert(
            "INSERT INTO clientes (nombre, correo, telefono, password, created_at, updated_at)
             VALUES (?, ?, ?, ?, NOW(), NOW())",
            [
                $data['nombre'],
                $data['correo'],
                $data['telefono'],
                $hash,
            ]
        );

        return (int) DB::getPdo()->lastInsertId();
    }
}