<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthWebController extends Controller
{
    private function preguntasEstadio(): array
    {
        return [
            [
                'pregunta' => '¿En qué ciudad está el Nexora Arena?',
                'opciones' => ['Madrid', 'Barcelona', 'Valencia', 'Sevilla'],
                'respuesta' => 'Valencia',
            ],
            [
                'pregunta' => '¿Cuántos sectores tiene el Nexora Arena?',
                'opciones' => ['45', '60', '71', '80'],
                'respuesta' => '71',
            ],
            [
                'pregunta' => '¿Cuántos asientos tiene el Nexora Arena?',
                'opciones' => ['10.000', '14.896', '20.000', '8.500'],
                'respuesta' => '14.896',
            ],
            [
                'pregunta' => '¿En qué país está el Nexora Arena?',
                'opciones' => ['Francia', 'Portugal', 'Italia', 'España'],
                'respuesta' => 'España',
            ],
            [
                'pregunta' => '¿Cuál es el sector más exclusivo del arena?',
                'opciones' => ['Pista', 'Grada', 'Palco', 'Fondo'],
                'respuesta' => 'Palco',
            ],
            [
                'pregunta' => '¿Qué zona está más cerca del escenario?',
                'opciones' => ['Tribuna', 'Palco', 'Fondo', 'Pista'],
                'respuesta' => 'Pista',
            ],
            [
                'pregunta' => '¿En qué comunidad autónoma está el Nexora Arena?',
                'opciones' => ['Cataluña', 'Madrid', 'Comunidad Valenciana', 'Andalucía'],
                'respuesta' => 'Comunidad Valenciana',
            ],
            [
                'pregunta' => '¿Cuántos eventos próximos hay disponibles?',
                'opciones' => ['2', '3', '4', '5'],
                'respuesta' => '4',
            ],
            [
                'pregunta' => '¿Qué tipo de eventos se celebran en el Nexora Arena?',
                'opciones' => ['Solo deportes', 'Solo conciertos', 'Conciertos y deportes', 'Solo teatro'],
                'respuesta' => 'Conciertos y deportes',
            ],
            [
                'pregunta' => '¿Cuál es el aforo máximo del Nexora Arena?',
                'opciones' => ['10.000', '12.000', '14.896', '20.000'],
                'respuesta' => '14.896',
            ],
            [
                'pregunta' => '¿Qué sector tiene el precio más alto?',
                'opciones' => ['Pista', 'Palco', 'Grada', 'Fondo'],
                'respuesta' => 'Palco',
            ],
            [
                'pregunta' => '¿Qué año inauguró el Nexora Arena?',
                'opciones' => ['2020', '2022', '2023', '2024'],
                'respuesta' => '2024',
            ],
            [
                'pregunta' => '¿Cómo se llama la zona de pie del arena?',
                'opciones' => ['Grada', 'Tribuna', 'Pista', 'Palco'],
                'respuesta' => 'Pista',
            ],
            [
                'pregunta' => '¿Cuántos palcos tiene el Nexora Arena?',
                'opciones' => ['2', '4', '6', '8'],
                'respuesta' => '4',
            ],
            [
                'pregunta' => '¿Cuál es el nombre completo del arena?',
                'opciones' => ['Valencia Arena', 'Nexora Arena', 'Roig Arena', 'Sport Arena'],
                'respuesta' => 'Nexora Arena',
            ],
        ];
    }

    public function showLogin()
    {
        return view('web.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        return back()->withInput()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ]);
    }

    public function showRegister()
    {
        $preguntas = $this->preguntasEstadio();
        $indice = rand(0, count($preguntas) - 1);
        $pregunta = $preguntas[$indice]['pregunta'];
        $opciones = $preguntas[$indice]['opciones'];

        session(['pregunta_index' => $indice]);

        return view('web.auth.register', compact('pregunta', 'opciones'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'respuesta' => 'required|string',
        ]);

        // Verificar respuesta anti-bot
        $preguntas = $this->preguntasEstadio();
        $indice = session('pregunta_index', 0);
        $respuestaCorrecta = $preguntas[$indice]['respuesta'];
        $respuestaUsuario = strtolower(trim($request->respuesta));

        if ($respuestaUsuario !== $respuestaCorrecta) {
            return back()
                ->withInput()
                ->withErrors(['respuesta' => 'Respuesta incorrecta. ¿Eres un robot? 🤖']);
        }

        $user = User::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => false,
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}