<?php

namespace App\Http\Controllers;

use App\Models\tickets;
use App\Models\tickets_replis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ticketsController extends Controller
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

    public function make_ticket(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => ['required', 'max:50'],
            'token' => ['required'],
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors(), 'error' => true]);
        } else {
            $token = $this->check_token($request->token);
            if ($token) {
                if (! tickets::where(['create_by' => $token->id, 'type' => 'open'])->first() or $token->type == 'admin') {
                    $make = tickets::create([
                        'title' => $request->title,
                        'create_by' => $token->id,
                    ]);
                    if ($make) {
                        return response()->json(['ticket_id' => $make->id, 'created_at' => $make->created_at, 'title' => $make->title, 'message' => 'Done Create Ticket', 'error' => false]);
                    } else {
                        return response()->json(['message' => 'Failed Create Ticket', 'error' => true]);
                    }
                } else {
                    return response()->json(['message' => 'You already have a ticket', 'error' => true]);
                }
            } else {
                return response()->json(['message' => 'invalid Token', 'error' => true]);
            }
        }
    }

    public function close_ticket(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => ['required', 'integer'],
            'token' => ['required'],
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors(), 'error' => true]);
        } else {
            $token = $this->check_token($request->token);
            if ($token) {
                $check = tickets::where(['id' => $request->id, 'type' => 'open'])->first();
                if ($check) {
                    if ($check->create_by == $token->id or $token->type == 'admin') {
                        $check->update(['type' => 'close']);
                    }// 'create_by' => $token->id,
                    else {
                        return response()->json(['message' => 'You dont have accses to this ticket :P ', 'error' => true]);
                    }

                    return $check;
                } else {
                    return response()->json(['message' => 'Ticket Not Found', 'error' => true]);
                }
            } else {
                return response()->json(['message' => 'invalid Token', 'error' => true]);
            }
        }
    }

    public function reply_ticket(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => ['required', 'integer'],
            'message' => ['required', 'max:150'],
            'token' => ['required'],
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors(), 'error' => true]);
        } else {
            $token = $this->check_token($request->token);
            if ($token) {
                $check = tickets::where(['id' => $request->id, 'type' => 'open'])->first();
                if ($check) {
                    if ($check->create_by == $token->id or $token->type == 'admin') {
                        $make_reply = tickets_replis::create(['message' => $request->message, 'create_by' => $token->id, 'ticket_id' => $request->id]);
                        if ($make_reply) {
                            return response()->json(['message' => 'Done Send Message', 'error' => true]);
                        } else {
                            return response()->json(['message' => 'Ohh Erorr????', 'error' => true]);
                        }
                    } else {
                        return response()->json(['message' => 'You dont have access', 'error' => true]);
                    }
                } else {
                    return response()->json(['message' => 'Ticket Not Found', 'error' => true]);
                }
            } else {
                return response()->json(['message' => 'invalid Token', 'error' => true]);
            }
        }
    }

    public function view_ticket(Request $request)
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
                $check_is_ticket = tickets::where(['id' => $request->id])->first();

                if ($check_is_ticket) {
                    if ($check_is_ticket->create_by == $check->id or $check->type == 'admin') {
                        return tickets_replis::where(['ticket_id' => $request->id])->get();
                    } else {
                        return response()->json(['message' => 'You dont have access', 'error' => true]);
                    }
                } else {
                    return response()->json(['message' => 'Not Found this ticket', 'error' => true]);
                }
            } else {
                return response()->json(['message' => 'invalid Token', 'error' => true]);
            }
        }
    }
}
