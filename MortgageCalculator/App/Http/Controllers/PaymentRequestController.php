<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentRequestController extends Controller
{
    /**
     * @OA\Post(
     * path="/payment-requests",
     * tags={"/payment-requests"},
     * security={{ "bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"age", "is_a_bank_client","property_value", "initial_payment", "loan_term", "property_type"},
     *       @OA\Property(property="age", type="integer", format="age", example="25"),
     *       @OA\Property(property="is_a_bank_client", type="boolean", format="is_a_bank_client", example="true"),
     *       @OA\Property(property="property_value", type="decimal", format="property_value", example="250000"),
     *       @OA\Property(property="initial_payment", type="decimal", format="initial_payment", example="125000"),
     *       @OA\Property(property="loan_term", type="integer", format="loan_term", example="36"),
     *       @OA\Property(property="property_type", type="string", format="property_type", example="жилое"),
     *     )
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *     )
     * )
     */
    public function makePaymentRequest(Request $request){
        $request->validate([
            'age' => 'required',
            'is_a_bank_client' => 'required',
            'property_value' => 'required',
            'initial_payment' => 'required',
            'loan_term' => 'required',
            'property_type' => 'required',
        ]);

        $mortgageProgramsByConditions = DB::table('mortgage_programs')
            ->where('maximum amount', '>=', $request->property_value)
            ->where('minimum payment', '<=', $request->initial_payment)
            ->where('realty type',  $request->property_type)
            ->get();

        return response()->json(['mortgage_programs' => $mortgageProgramsByConditions], 200);
    }
}
