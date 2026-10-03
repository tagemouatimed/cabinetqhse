<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Le premier compte (admin) peut s'inscrire librement ; ensuite seul un admin connecté crée des utilisateurs.
        $this->middleware(function ($request, $next) {
            if (User::exists()) {
                if (! auth()->check()) {
                    return redirect()->route('login');
                }
                abort_unless(auth()->user()->role === 'admin', 403, 'Réservé aux administrateurs.');
            }

            return $next($request);
        });
    }

    /**
     * Crée l'utilisateur sans connecter le nouveau compte lorsqu'un admin en crée un autre.
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $premier = ! User::exists();
        $user = $this->create($request->all());

        if ($premier) {
            $this->guard()->login($user);

            return redirect($this->redirectPath());
        }

        return redirect()->route('register')->with('status', "Utilisateur {$user->name} créé.");
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'telephone' => ['nullable', 'string', 'max:30'],
            'fonction' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            // Le tout premier compte est toujours administrateur, quel que soit le rôle envoyé.
            'role' => User::exists() ? $data['role'] : 'admin',
            'telephone' => $data['telephone'] ?? null,
            'fonction' => $data['fonction'] ?? null,
            'password' => Hash::make($data['password']),
        ]);
    }
}
