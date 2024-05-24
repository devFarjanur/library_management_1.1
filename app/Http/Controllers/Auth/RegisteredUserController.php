<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $payments = Payment::all();
        return view('auth.register', compact('payments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:255'], // Add validation for id
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['nullable', 'numeric', 'digits:11'],
            'age' => ['required', 'integer', 'min:1'], // Add validation for age
            'gender' => ['required', 'string', 'max:255'], // Add validation for gender
            'address' => ['required', 'string', 'max:255'], // Add validation for address
            'payment_method' => ['required', 'exists:payments,id'],
            'trx_id' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'student_id' => $request->student_id, // Save the id
            'email' => $request->email,
            'phone' => $request->phone,
            'age' => $request->age, // Save the age
            'gender' => $request->gender, // Save the gender
            'address' => $request->address, // Save the address
            'payment_id' => $request->payment_method,
            'TrxID' => $request->trx_id,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // Redirect to the login page instead of home
        return redirect()->route('login')->with('success', 'Registration successful. Please log in.');
    }
}