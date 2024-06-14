<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserMail;

class UserController extends Controller
{
    public function insertUsers(array $profesores)
    {
        Log::info('Datos de profesores recibidos:', ['profesores' => $profesores]);

        // Por defecto doy el rol profesor a todos
        $roleProfesorado = Role::firstOrCreate(['name' => 'docente']);

        // Iniciar una lista de usuarios creados para la respuesta
        $createdUsers = [];

        foreach ($profesores as $profesor) {
            Log::info('Procesando profesor:', ['profesor' => $profesor]);

            // Primer nombre del profesor
            $fullName = $profesor[1];
            $nameParts = explode(' ', trim($fullName));
            $firstName = $nameParts[0];

            // Generar una contraseña en texto plano para enviar por correo
            $plainPassword = $firstName . Carbon::now()->year;
            // Uso el primer nombre y el año actual para generar una contraseña
            $hashedPassword = Hash::make($plainPassword);

            // Obtengo el nombre completo del profesor
            $name = $fullName;

            // Creo valores no válidos que puede contener el campo nombre para que no se incluyan
            $email_no_valido = ["del", "de la", "de los", "de las", "de"];
            // Creo un array con el nombre y apellidos, ambos contenidos en el campo nombre
            $partes = explode(' ', $name);
            // Filtro los nombres para que no incluyan los valores no válidos y que tengan una longitud mayor a 1 ya que hay un profesor que tiene una 'O' suelta
            $partesFiltradas = array_filter($partes, function ($parte) use ($email_no_valido) {
                $parteNormalizada = $this->normalizarTexto($parte);
                return !in_array($parteNormalizada, $email_no_valido) && strlen($parteNormalizada) > 1;
            });

            // Genero el correo electrónico del profesor
            $email_base = '';
            if (count($partesFiltradas) >= 2) {
                $email_base = $this->normalizarTexto(array_shift($partesFiltradas)) . '.' . $this->normalizarTexto(array_shift($partesFiltradas));
            } else {
                $email_base = $this->normalizarTexto($firstName) . "2024";
            }
            $email = $email_base . '@iespoligonosur1234.org';

            // Verificar si el correo ya existe y modificarlo si es necesario
            $original_email = $email_base;
            $email_counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = $original_email . $email_counter . '@iespoligonosur1234.org';
                $email_counter++;
            }

            // Genero el nombre de usuario del profesor
            $user_name = '';
            if (count($partesFiltradas) >= 2) {
                $user_name = $this->normalizarTexto($partesFiltradas[0]) . $this->normalizarTexto($partesFiltradas[1]);
            } else {
                $user_name = $this->normalizarTexto($firstName);
            }

            // Verificar si el username ya existe y modificarlo si es necesario
            $original_username = $user_name;
            $counter = 1;
            while (User::where('user_name', $user_name)->exists()) {
                $user_name = $original_username . $counter;
                $counter++;
            }

            Log::info('Creando nuevo usuario:', ['name' => $name, 'email' => $email, 'username' => $user_name]);

            try {
                // Crear un nuevo objeto User con valores predeterminados para ciertos atributos
                $newUser = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'user_name' => $user_name,
                    'professor_cod' => $profesor[0]
                ]);

                // Asignar el rol al usuario
                $newUser->roles()->attach($roleProfesorado->id);
                $newUser->save();

                // Enviar correo al usuario
                Mail::to($newUser->email)->send(new UserMail([
                    'name' => $name,
                    'email' => $email,
                    'user_name' => $user_name,
                    'password' => $plainPassword 
                ]));

                Log::info('Correo enviado a:', ['email' => $newUser->email]);

                // Agregar al array de usuarios creados
                $createdUsers[] = $newUser;
            } catch (\Exception $e) {
                Log::error('Error al crear usuario o enviar correo: ' . $e->getMessage());
                continue; // Saltar este usuario si hay un error
            }
        }

        return response()->json(['success' => true, 'created_users' => $createdUsers, 'token' => $createdUsers[0]->createToken("API TOKEN")->plainTextToken], 201);
    }


    /**
     * Normaliza el texto a minúsculas y reemplaza caracteres especiales.
     *
     * @param string $texto
     * @return string
     */
    protected function normalizarTexto($texto)
    {
        $texto = strtolower($texto);
        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
            $texto
        );
        return $texto;
    }
    public function index()
    {
        $users = User::with('roles')->get();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::with('roles')->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'user_name' => 'string|max:255|unique:users,user_name,' . $user->id,
            'professor_cod' => 'string|max:3',
            'current_password' => 'required_with:new_password|string',
            'new_password' => [
                'nullable',
                'string',
                'min:7', // mínimo 7 caracteres
                'regex:/[a-z]/', // al menos una letra minúscula
                'regex:/[A-Z]/', // al menos una letra mayúscula
                'regex:/[0-9]/', // al menos un número
                'confirmed' // debe coincidir con el campo new_password_confirmation
            ],
        ]);
    
        // Verificar la contraseña actual si se proporciona una nueva contraseña
        if ($request->filled('new_password') && !Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'La contraseña actual no es correcta.'], 403);
        }
    
        // Actualizar la contraseña y revocar todos los tokens del usuario si se proporciona una nueva contraseña
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
            $user->tokens()->delete(); // Revocar todos los tokens del usuario
            $user->save(); // Asegúrate de guardar los cambios
        }
    
        // Actualizar otros campos
        $user->update($request->except(['roles', 'current_password', 'new_password', 'new_password_confirmation']));
    
        if ($request->has('roles')) {
            $roles = Role::whereIn('name', $request->roles)->pluck('id');
            $user->roles()->sync($roles);
        }
    
        return response()->json($user->load('roles'));
    }
    
        public function destroy($id)
        {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->roles()->detach();
            $user->delete();

            return response()->json(['message' => 'User deleted']);
        }
        public function getAuthenticatedUser(Request $request)
        {
            try {
                $user = $request->user();

                if (!$user) {
                    return response()->json(['error' => 'User not found'], 404);
                }

                return response()->json($user, 200);
            } catch (\Exception $e) {
                Log::error('Error fetching user data: ' . $e->getMessage());
                return response()->json(['error' => 'Internal Server Error'], 500);
            }
    }

    //funcion para acceder a los roles y controlar permisos
        public function getUserRoles(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $roles = $user->roles()->pluck('name');
        return response()->json(['roles' => $roles]);
    }

    // actualiza la contraseña del usuario loguaedo
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        // Validar las contraseñas actuales y nuevas
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed', // La confirmación puede ser opcional
        ]);

        // Verificar la contraseña actual
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'La contraseña actual no es correcta.'], 403);
        }

        // Actualizar la contraseña
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Contraseña actualizada correctamente.'], 200);
    }

}


