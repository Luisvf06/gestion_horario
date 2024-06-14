<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Crea un rol si no existe.
     *
     * @param string $roleName
     * @return Role
     */
    public function createRoleIfNotExists($roleName)
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        if ($role->wasRecentlyCreated) {
            Log::info("Rol '{$roleName}' creado.");
        }
        return $role;
    }

    /**
     * Lista todos los roles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    /**
     * Almacena un rol recién creado en la base de datos.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name|max:255',
        ]);

        $role = Role::create($request->all());

        return response()->json($role, 201);
    }

    /**
     * Muestra un rol específico.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        return response()->json($role);
    }

    /**
     * Actualiza un rol específico en la base de datos.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $request->validate([
            'name' => 'required|unique:roles,name|max:255',
        ]);

        $role->update($request->all());

        return response()->json($role);
    }

    /**
     * Elimina un rol específico de la base de datos.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }
}
