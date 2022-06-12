<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\MortgageProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class MortgageProgramController extends Controller
{
    /**
     * @OA\Post(
     * path="/mortgage-programs",
     * tags={"/mortgage-programs"},
     * security={{ "bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"bank_name", "program_name","realty_type", "interest_rate", "minimum_payment", "maximum_amoumt"},
     *       @OA\Property(property="bank_name", type="string", format="bank name", example="Sberbank"),
     *       @OA\Property(property="program_name", type="string", format="program name", example="невыгодная ипотека"),
     *       @OA\Property(property="realty_type", type="string", format="realty type", example="жилое"),
     *       @OA\Property(property="interest_rate", type="decimal", format="interest rate", example="10"),
     *       @OA\Property(property="minimum_payment", type="decimal", format="minimum payment", example="100000"),
     *       @OA\Property(property="maximum_amount", type="decimal", format="maximum amount", example="500000"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *     )
     * )
     */
    public function createMortgageProgram(Request $request)
    {
        $request->validate([
            'bank_name' => 'required_without_all',
            'program_name' => 'required_without_all',
            'realty_type' => 'required_without_all',
            'interest_rate' => 'required_without_all',
            'minimum_payment' => 'required_without_all',
            'maximum_amount' => 'required_without_all',
        ]);

        DB::table('mortgage_programs')->insert([
            'bank name' => $request->bank_name,
            'program name' => $request->program_name,
            'realty type' => $request->realty_type,
            'interest rate' => $request->interest_rate,
            'minimum payment' => $request->minimum_payment,
            'maximum amount' => $request->maximum_amount,
        ]);

        return response()->json($request->program_name);
    }

    /**
     * @OA\Get(
     * path="/mortgage-programs",
     * tags={"/mortgage-programs"},
     * security={{ "bearerAuth": {} }},
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *     )
     * )
     */
    public function getAllMortgagePrograms(Request $request)
    {
        $mortgagePrograms = DB::table('mortgage_programs')->get();
        return response()->json($mortgagePrograms, 200);
    }

    /**
     * @OA\Get(
     * path="/mortgage-programs/{id}",
     * tags={"/mortgage-programs"},
     * security={{ "bearerAuth": {} }},
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Buscar por estado",
     *     required=true,
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *     )
     * )
     */
    public function getMortgageProgramById(Request $request, $id)
    {
        $mortgagePrograms = DB::table('mortgage_programs')->find($id);
        return response()->json($mortgagePrograms, 200);
    }

    /**
     * @OA\Delete(
     * path="/mortgage-programs/{id}",
     * tags={"/mortgage-programs"},
     * security={{ "bearerAuth": {} }},
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Buscar por estado",
     *     required=true,
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *     )
     * )
     */
    public function deleteMortgageProgramById(Request $request, $id)
    {
        DB::table('mortgage_programs')->where('id', $id)->delete();
        return response()->json('delete this shit', 200);
    }

    /**
     * @OA\Post(
     * path="/mortgage-programs/{id}",
     * tags={"/mortgage-programs"},
     * security={{ "bearerAuth": {} }},
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Buscar por estado",
     *     required=true,
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"bank_name", "program_name","realty_type", "interest_rate", "minimum_payment", "maximum_amoumt"},
     *       @OA\Property(property="bank_name", type="string", format="bank name", example="Sberbank"),
     *       @OA\Property(property="program_name", type="string", format="program name", example="невыгодная ипотека"),
     *       @OA\Property(property="realty_type", type="string", format="realty type", example="жилое"),
     *       @OA\Property(property="interest_rate", type="decimal", format="interest rate", example="10"),
     *       @OA\Property(property="minimum_payment", type="decimal", format="minimum payment", example="100000"),
     *       @OA\Property(property="maximum_amount", type="decimal", format="maximum amount", example="500000"),
     *     )
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *     )
     * )
     */
    public function updateMortgageProgramById(Request $request, $id)
    {
        $request->validate([
            'bank_name' => 'required',
            'program_name' => 'required',
            'realty_type' => 'required',
            'interest_rate' => 'required',
            'minimum_payment' => 'required',
            'maximum_amount' => 'required',
        ]);

        $updatedMortgageProgram = DB::table('mortgage_programs')->where('id', $id)->update([
            'bank name' => $request->bank_name,
            'program name' => $request->program_name,
            'realty type' => $request->realty_type,
            'interest rate' => $request->interest_rate,
            'minimum payment' => $request->minimum_payment,
            'maximum amount' => $request->maximum_amount,
        ]);

        return response()->json($updatedMortgageProgram, 200);
    }
}
