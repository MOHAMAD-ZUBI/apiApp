<?php

namespace App\Http\Controllers;

use App\Models\keys;
use App\Models\products;
use App\Models\purchases;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class userController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => ['required', 'min:3', 'max:15', 'unique:users'],
            'email' => ['required', 'unique:users', 'email'],
            'password' => ['required', Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()],
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors(), 'error' => true]);
        } else {
            $data = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'token' => Str::random(16),
                'ip' => $request->ip(),

            ]);

            return $data;
        }
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => ['required', 'min:3', 'max:15'],
            'password' => ['required', Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()],
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors(), 'error' => true]);
        } else {
            if (auth()->attempt([
                'username' => $request->username,
                'password' => $request->password,
            ])) {
                //->update(["token"=>Str::random(16)]
                $user = user::where(['username' => $request->username])->first();
                $user->update(['token' => Str::random(16), 'ip' => $request->ip()]);

                return $user;
            } else {
                return response()->json(['message' => 'invalid Login', 'error' => true]);
            }
        }
    }

    public function check_token($token)
    {
        $user = user::where(['token' => $token])->first();
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    public function update_username(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => ['required', 'unique:users', 'min:3', 'max:15'],
            'token' => ['required'],

        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors(), 'error' => true]);
        } else {
            $check = $this->check_token($request->token);
            if ($check) {
                if ($check->update(['username' => $request->username, 'ip' => $request->ip()])) {
                    return response()->json(['message' => 'Done Change Username', 'error' => false]);
                } else {
                    return response()->json(['message' => 'Ohh Error!!', 'error' => true]);
                }
            } else {
                return response()->json(['message' => 'invalid Token', 'error' => true]);
            }
        }
    }

    public function update_email(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => ['required', 'unique:users', 'email'],
            'token' => ['required'],

        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors(), 'error' => true]);
        } else {
            $check = $this->check_token($request->token);
            if ($check) {
                if ($check->update(['email' => $request->email, 'ip' => $request->ip()])) {
                    return response()->json(['message' => 'Done Change Email', 'error' => false]);
                } else {
                    return response()->json(['message' => 'Ohh Error!!', 'error' => true]);
                }
            } else {
                return response()->json(['message' => 'invalid Token', 'error' => true]);
            }
        }
    }

    public function update_password(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'old_password' => ['required', Password::min(8)->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()],
            'new_password' => ['required', Password::min(8)->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()],
            'token' => ['required'],

        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors(), 'error' => true]);
        } else {
            $check = $this->check_token($request->token);
            if ($check) {
                if (auth()->attempt([
                    'username' => $check->username,
                    'password' => $request->old_password,
                ])) {
                    $check_good = $check->update(['password' => Hash::make($request->new_password), 'ip' => $request->ip()]);

                    if ($check_good) {
                        return response()->json(['message' => 'Done Change Password', 'error' => false]);
                    } else {
                        return response()->json(['message' => 'Ohh Error!!', 'error' => true]);
                    }
                } else {
                    return response()->json(['message' => 'invalid Password', 'error' => true]);
                }
            } else {
                return response()->json(['message' => 'invalid Token', 'error' => true]);
            }
        }
    }

    public function calc_time($type)
    {
        $startDate = time();
        $date = '';
        if ($type == '7day' or $type == '30day') {
            $time = explode('day', $type);
            $date = date('Y-m-d H:i:s', strtotime("+$time[0] day", $startDate));
        } else {
            $date = date('Y-m-d H:i:s', strtotime('+500000 day', $startDate));
        }

        return $date;
    }

    public function active_key(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'key' => ['required'],
            'token' => ['required'],

        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors(), 'error' => true]);
        } else {
            $check = $this->check_token($request->token);
            if ($check) {
                $check_key = keys::where(['key_hash' => $request->key, 'iscliamed' => 0])->first();
                if ($check_key) {
                    $check_key->update([
                        'iscliamed' => 1,
                        'cliamby' => $check->id,

                    ]);

                    $check_pur = purchases::create([
                        'owner_id' => $check->id,
                        'product_id' => $check_key->product_id,
                        'type' => $check_key->type,
                        'end_time' => $this->calc_time($check_key->type),
                    ]);
                    if ($check_pur) {
                        return response()->json(['message' => 'Done Cliam Key', 'error' => false]);
                    } else {
                        return response()->json(['message' => 'Ohh Error!!', 'error' => true]);
                    }
                } else {
                    return response()->json(['message' => 'invalid Key', 'error' => true]);
                }
            } else {
                return response()->json(['message' => 'invalid Token', 'error' => true]);
            }
        }
    }

public function products(Request $request)
{
    return products::all();
}
}
