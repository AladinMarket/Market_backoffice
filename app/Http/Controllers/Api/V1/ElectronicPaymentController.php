<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ElectronicPaymentController extends Controller
{
    public function orane_money_payment(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'amount' => 'required|numeric|min:1',
                    'phone_number' => 'required',
                    'otp_code' => 'required',
                    'user_id' => 'required',
                ],
                [
                    'amount.min' => 'Le montant doit être supérieur à 1',
                    'amount.required' => 'Le montant est requis',
                    'phone_number.required' => 'Le numéro de téléphone est requis',
                    'otp_code.required' => 'Le code OTP est requis',
                    'user_id.required' => "L'ID de l'utilisateur est requis",
                ],
            );

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }

            $data = [
                'user_id' => $request->user_id,
                'amount' => $request->amount,
                'phone_number' => $request->phone_number,
            ];
            $idFromClient = 'Market' . date('dmy') . time();
            $response = Helpers::intouchOrangeMoneyDebit($request->phone_number, $request->otp_code, $request->amount, $idFromClient);
            $response = json_decode($response);
            if ($response) {
                if ($response->status == 'SUCCESSFUL') {
                    // $data['id_from_client'] = $response->idFromClient;
                    // $data['id_from_gu'] = $response->idFromGU;
                    // $data['num_transaction'] = $response->numTransaction;
                    // $data['date_time'] = $response->dateTime;
                    // $data['status'] = 'success';
                    // $data['message'] = 'Paiement réussi';
                    return response()->json($response, 200);
                } else {
                    return response()->json($response, 422);
                }
            }
            return response()->json(['errors' => ['message' => 'Erreur lors du paiement']], 403);
        } catch (\Throwable $th) {
            return response()->json(['errors' => ['message' => $th->getMessage()]], 403);
        }
    }
    public function moov_money_initialise(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'amount' => 'required|numeric|min:1',
                    'phone_number' => 'required',
                    'user_id' => 'required',
                ],
                [
                    'amount.min' => 'Le montant doit être supérieur à 1',
                    'amount.required' => 'Le montant est requis',
                    'phone_number.required' => 'Le numéro de téléphone est requis',
                    'user_id.required' => "L'ID de l'utilisateur est requis",
                ],
            );

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }

            $data = [
                'user_id' => $request->user_id,
                'amount' => $request->amount,
                'phone_number' => $request->phone_number,
            ];
            $idFromClient = 'Market' . date('dmy') . time();
            $response = Helpers::intouchMoovMoneyInitialise($request->phone_number,  $request->amount, $idFromClient);
            $response = json_decode($response);
            if ($response) {
                if ($response->status == 'SUCCEED') {
                    return response()->json($response, 200);
                } else {
                    return response()->json($response, 422);
                }
            }
            return response()->json(['errors' => ['message' => 'Erreur lors de l\'initialisation du paiement']], 403);
        } catch (\Throwable $th) {
            return response()->json(['errors' => ['message' => $th->getMessage()]], 403);
        }
    }
    public function moov_money_check_status($idFromClient)
    {
        try {
            $response = Helpers::intouchMoovMoneyCheckStatus($idFromClient);
            $response = json_decode($response);
            if ($response) {
                if ($response->status == 'SUCCEED') {
                    return response()->json($response, 200);
                } else {
                    return response()->json($response, 422);
                }
            }
            return response()->json(['errors' => ['message' => 'Erreur lors de la vérification du paiement']], 403);
        } catch (\Throwable $th) {
            return response()->json(['errors' => ['message' => $th->getMessage()]], 403);
        }
    }

}
