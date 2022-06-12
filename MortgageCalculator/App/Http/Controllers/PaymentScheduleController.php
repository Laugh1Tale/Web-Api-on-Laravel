<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentScheduleController extends Controller
{
    /**
     * @OA\Post(
     * path="/payment-schedule/{mortgage_program_id}",
     * tags={"/payment-schedule"},
     * security={{ "bearerAuth": {} }},
     * @OA\Parameter(
     *     name="mortgage_program_id",
     *     in="path",
     *     description="Buscar por estado",
     *     required=true,
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"age", "is_a_bank_client","property_value", "initial_payment", "loan_term", "property_type"},
     *       @OA\Property(property="property_value", type="decimal", format="property_value", example="245000"),
     *       @OA\Property(property="initial_payment", type="decimal", format="initial_payment", example="125000"),
     *       @OA\Property(property="loan_term", type="integer", format="loan_term", example="24"),
     *       @OA\Property(property="payment_type", type="string", format="property_type", example="дифферинцированный"),
     *     )
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *     )
     * )
     */
    public function makePaymentSchedule(Request $request, $id){
        $request->validate([
            'property_value' => 'required',
            'initial_payment' => 'required',
            'loan_term' => 'required',
            'payment_type' => 'required',
        ]);

        $percentage = DB::table('mortgage_programs')->where('id',$id)->value('interest rate');
        $sum = $request->property_value - $request->initial_payment;

        if ($request->payment_type == "дифферинцированный") {
            $arr = [['property value' => $request->property_value,
                'amount without initial payment' => $request->property_value - $request->initial_payment,
                'initial payment' => $request->initial_payment, 'percentage' => $percentage ]];
            $principalDebt = $sum / $request->loan_term;
            $remainingDebt = $sum;

            for ($i = 1; $i <= $request->loan_term; $i++) {
                $percentages = $percentage * $remainingDebt / 12 / 100;
                $remainingDebt = $remainingDebt - $principalDebt;
                $arr[] = ['payment number' => $i, 'payment amount' => $principalDebt + $percentages,
                    'principal debt' => $principalDebt, 'percentages' => $percentages, 'remaining debt' => $remainingDebt];
            }
        }

        if ($request->payment_type == "аннуитетный") {
            $arr = [['property value' => $request->property_value,
                'amount without initial payment' => $request->property_value - $request->initial_payment,
                'initial payment' => $request->initial_payment, 'percentage' => $percentage ]];
            $p = $percentage / 12 / 100;
            $principalDebt = $sum * ($p + $p / ((1 + $p)**$request->loan_term - 1));//$request->loan_term;
            $percentages = $p * $sum; //+ $p / ((1 + $p)**$request->loan_term - 1);
            $remainingDebt = $sum; //- $principalDebt + $principalDebt * $p / ((1 + $p)**$request->loan_term - 1);

            for ($i = 1; $i <= $request->loan_term; $i++) {
                $arr[] = ['payment number' => $i, 'payment amount' =>  $principalDebt,
                    'principal debt' => $principalDebt - $percentages, 'percentages' =>  $percentages, 'remaining debt' => $remainingDebt];
                $remainingDebt = $remainingDebt - $principalDebt + $percentages;
                $percentages = $remainingDebt * $p;
            }
        }

        return response()->json($arr, 200);
    }
}
