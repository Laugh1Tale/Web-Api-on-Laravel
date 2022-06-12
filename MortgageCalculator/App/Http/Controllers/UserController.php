<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * @OA\Get(
     * path="/users/{userId}",
     * tags={"/users"},
     * security={{ "bearerAuth": {} }},
     * @OA\Parameter(
     *     name="userId",
     *     in="path",
     *     description="Buscar por estado",
     *     required=true,
     *      ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *     )
     * )
     */
    public function show(Request $request, $userId)
    {
        $user = User::find($userId);

        if($user) {
            return response()->json($user);
        }

        return response()->json(['message' => 'User not found!'], 404);
    }

    /**
     * @OA\Post(
     * path="/users/{userId}/change_role",
     * tags={"/users"},
     * security={{ "bearerAuth": {} }},
     * @OA\Parameter(
     *     name="userId",
     *     in="path",
     *     description="Buscar por estado",
     *     required=true,
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"name", "email","password"},
     *       @OA\Property(property="role", type="string", format="role", example="moderator"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *     )
     * )
     */
    public function changeRole(Request $request, $userId){
        $request->validate([
            'role' => 'required',
        ]);

        $user = User::find($userId);

        if($user) {
            $role = DB::table('roles')->where('user_id', $userId)->value('role');

            if ($role == null) {
                DB::table('roles')->insert([
                    'user_id' => $userId,
                    'role' => $request->role
                ]);
                $role = DB::table('roles')->where('user_id', $userId)->value('role');
                return response()->json($role);
            }

            return response()->json($role);
        }

        return response()->json(['message' => 'User not found!'], 404);
    }
}
