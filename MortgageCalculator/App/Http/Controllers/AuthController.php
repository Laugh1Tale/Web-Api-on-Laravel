<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/authentication/register",
     * summary="sign up",
     * description="register by email, password",
     * tags={"/authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"name", "email","password"},
     *       @OA\Property(property="name", type="string", format="name", example="John Johnson"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Successful user registration",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The user has been successfully registered")
     *        )
     * ),
     * @OA\Response(
     *    response=409,
     *    description="Wrong credentials for registration",
     * ),
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (DB::table('users')->where('email', $request->email)->get() != null)
            return response()->json(['message'=>'A user with this email already exists'], 409);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $user_id = DB::table('users')->where('email', $request->email)->value('id');

        DB::table('roles')->insert([
           'user_id' => $user_id,
            'role' => 'basic'
        ]);

        return response()->json("The user has been successfully registered", 200);
    }

    /**
     * @OA\Post(
     * path="/authentication/login",
     * summary="sign in",
     * description="login by email, password",
     * tags={"/authentication"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        if( Auth::attempt(['email'=>$request->email, 'password'=>$request->password]) ) {
            $user = Auth::user();
            $userRole = $user->role()->first();

            if ($userRole) {
                $this->scope = $userRole->role;
            }

            $token = $user->createToken($user->email.'-'.now(), [$this->scope]);

            return response()->json([
                'token' => $token->accessToken
            ]);
        }
    }
}
