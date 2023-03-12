<?php

namespace App\Http\Controllers;

use App\Models\keys;
use App\Models\products;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class adminController extends Controller
{
    public function check_token($token)
    {
        $user = User::where(['token' => $token])->first();
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    public function temp(Request $request)
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
                    return response()->json(['message' => 'Ohh Error!!', 'error' => false]);
                }
            } else {
                return response()->json(['message' => 'invalid Token', 'error' => true]);
            }
        }
    }

    public function upload_imgs($name)
    {
        $file_exct = $name->extension();
        //echo "<script>alert('$file_exct')</script>";
        $file_name = time().'.'.$file_exct;
        $path = '../files/images';
        $name->move($path, $file_name);

        return $file_name;
    }

    public function upload_file($name, $file_exct)
    {
        $file_name = time().'.'.$file_exct;
        $path = '../files/products';
        $name->move($path, $file_name);

        return $file_name;
    }

    public function add_key(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'type' => ['required'],
            'product_id' => ['required'],
            'token' => ['required'],

        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors(), 'error' => true]);
        } else {
            $check = $this->check_token($request->token);

            if ($check and $check->type == 'admin') {
                if ($request->type == '7day' or $request->type == '30day' or $request->type == 'lifetime' or $request->type == 'source_code') {
                    $c = products::where(['id' => $request->product_id])->first();
                    if ($c) {
                        $go = keys::create([
                            'type' => $request->type,
                            'create_by' => $check->id,
                            'key_hash' => Str::uuid()->toString(),
                            'product_id' => $request->product_id,

                        ]);
                        if ($go) {
                            return $go;
                        } else {
                            return response()->json(['message' => 'Ohh Error!!!!', 'error' => true]);
                        }
                    } else {
                        return response()->json(['message' => 'product not found', 'error' => true]);
                    }
                } else {
                    return response()->json(['message' => 'parm type check plz', 'error' => true]);
                }
            } else {
                return response()->json(['message' => 'invalid Token', 'error' => true]);
            }
        }
    }

        public function add_product(Request $request)
        {
            $validate = Validator::make($request->all(), [
                'title' => ['required'],
                'bio' => ['required'],
                'img_in_page' => 'required|image|mimes:png,jpg,jpeg',
                'img_profile' => 'required|image|mimes:png,jpg,jpeg',
                'file_exe' => 'required',
                'file_souce_code' => 'required',
                'price_7day' => ['required'],
                'price_30day' => ['required'],
                'price_lifetime' => ['required'],
                'price_source_code' => ['required'],
                'token' => ['required'],

            ]);
            if ($validate->fails()) {
                return response()->json(['errors' => $validate->errors(), 'error' => true]);
            } else {
                $check = $this->check_token($request->token);

                if ($check and $check->type == 'admin') {
                    $img_in_page = $this->upload_imgs($request->img_in_page);
                    $img_profile = $this->upload_imgs($request->img_profile);
                    $file_exe = $this->upload_file($request->file_exe, 'exe');
                    $file_souce_code = $this->upload_file($request->file_souce_code, 'py');
                    $create = products::create(['title' => $request->title, 'bio' => $request->bio, 'img_in_page' => $img_in_page, 'img_profile' => $img_profile, 'file_exe' => $file_exe, 'file_souce_code' => $file_souce_code, 'price_7day' => $request->price_7day, 'price_30day' => $request->price_30day, 'price_source_code' => $request->price_source_code, 'price_lifetime' => $request->price_lifetime, 'create_by' => $check->id]);

                    if ($create) {
                        return $create;
                    } else {
                        return response()->json(['message' => 'Ohh Error!!!!', 'error' => true]);
                    }
                } else {
                    return response()->json(['message' => 'invalid Token', 'error' => true]);
                }
            }
        }
}
