<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Category;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:3'

        ]);
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'invalid credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstorFail();
        $token = $user->createToken('expendy-token')->plainTextToken;


        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }


    // Register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:3|confirmed'
        ]);

        // 2. create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

         // 3. Assign default categories
                $defaultCategories = Category::whereNull('user_id')->get();

                foreach ($defaultCategories as $category) {
                    $user->categories()->create(['name' => $category->name]);
                }

                // 4. create token
             $token = $user->createToken('expendy-token')->plainTextToken;

             // 5. return response
        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    // Get authenticated user details
    public function me(Request $request)
{
    return response()->json($request->user());
}

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }
}
