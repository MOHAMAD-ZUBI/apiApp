<?php

namespace App\Http\Controllers;

use App\Models\products;
use App\Models\purchases;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class purchasesController extends Controller
{
    public function check_token($token)
    {
        $user = user::where(['token' => $token])->first();
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    public function view_purchases(Request $request)
    {
        $validate = Validator::make($request->all(), [

            'token' => ['required'],
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors(), 'error' => true]);
        } else {
            $check = $this->check_token($request->token);
            if ($check) {
                return purchases::where(['owner_id' => $check->id])->get();
            } else {
                return response()->json(['message' => 'invalid Token', 'error' => true]);
            }
        }
    }

    public function download(Request $request)
    {
        $validate = Validator::make($request->all(), [

            'token' => ['required'],
            'id' => ['required'],
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors(), 'error' => true]);
        } else {
            $check = $this->check_token($request->token);
            if ($check) {
                $check_prush = purchases::where(['id' => $request->id, 'owner_id' => $check->id])->first();

                if ($check_prush) {
                    $r = 'file_exe';
                    if ($check_prush->type == 'source_code') {
                        $r = 'file_souce_code';
                    }
                    $check_prodcts = products::where(['id' => $check_prush->product_id])->first();
                    if ($check_prodcts) {
                        return response()->download('../files/products/'.$check_prodcts->$r, $request->id.'.'.explode('.', $check_prodcts->$r)[1]);
                    } else {
                        return response()->json(['message' => 'Ohh Error :(', 'error' => true]);
                    }
                } else {
                    return response()->json(['message' => 'You dont have acsess :O', 'error' => true]);
                }
            } else {
                return response()->json(['message' => 'invalid Token', 'error' => true]);
            }
        }
    }

    public function change_ip(Request $request)
    {
        $validate = Validator::make($request->all(), [

            'token' => ['required'],
            'id' => ['required'],
            'ip' => ['required'],
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors(), 'error' => true]);
        } else {
            $check = $this->check_token($request->token);
            if ($check) {
                $check_id_pursh = purchases::where(['id' => $request->id, 'owner_id' => $check->id])->first();
                if ($check_id_pursh) {
                    if ($check_id_pursh->count_rest_ip <= 0) {
                        return response()->json(['message' => "You can't do it you have 0 rest", 'error' => true]);
                    } else {
                        $change = $check_id_pursh->update(['ip' => $request->ip, 'count_rest_ip' => $check_id_pursh->count_rest_ip - 1]);
                        if ($change) {
                            return response()->json(['message' => 'Done Change ip', 'error' => false]);
                        } else {
                            return response()->json(['message' => 'Ohh error :(', 'error' => true]);
                        }
                    }
                } else {
                    return response()->json(['message' => 'You dont have acsess :O', 'error' => true]);
                }
            } else {
                return response()->json(['message' => 'invalid Token', 'error' => true]);
            }
        }
    }
}
