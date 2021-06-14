<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthLogoutRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Resources\AuthLoginResource;
use App\Models\User;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Stmt\TryCatch;

class AuthController extends Controller
{

    /**
     * Attempt login
     *
     * @param AuthLoginRequest $request
     * @return AuthLoginResource
     * @throws ValidationException
     */
    public function login(AuthLoginRequest $request): AuthLoginResource
    {
        $user = User::where('email', $request->email)->firstOrFail();

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        return new AuthLoginResource($user);
    }

    public function logout(AuthLogoutRequest $request)
    {
        try {
            Auth::user()->tokens()->delete();
        } catch (Exaption $e) {
            throw ValidationException::withMessages(['logouted']);
        }
        // Auth::user()->currentAccessToken()->delete();
        // Auth::logout();

        return response(null, 204);
    }

    public function register(AuthRegisterRequest $request)
    {
        if ($request->email == User::where('email')->exists()) {
            throw new Exception('already taken');
        }
        // $user = User::create(request(['name', 'email', 'password']));
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();


        return new AuthLoginResource($user);
    }
}
