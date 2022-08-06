<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UserDetailsChanged;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function All()
    {
        $response = ['users' => User::all()];
        return response()->json($response);
    }

    public function RegisterAdmin(Request $request)
    {
        $fields = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'is_admin' => true,
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('auth-token', ['is_admin'])->plainTextToken;

        $response = [
            'user' => ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'email' => $user->email],
            'token' => $token,
        ];

        return response()->json($response, 201);
    }

    public function PendingChanges()
    {
        $pendingChanges = User::whereNotNull(['pending_user_data'])->get();
        $response = ['users' => $pendingChanges];
        return response()->json($response);
    }

    public function InitiateUserUpdate(Request $request, $id)
    {
        $fields = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email',
        ]);

        $user = User::find($id);

        if (!$user) return response()->json(['message' => 'user not found'], 404);
        if ($user->pending_user_data != null) return response()->json(['message' => 'user has pending changes'], 400);

        $pendingData = json_encode([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'pending_user_data' => null,
            'changed_by'=>null
        ]);

        $user->update(['pending_user_data' => $pendingData, 'changed_by' => auth()->user()->id]);

        //notify all other admins
        User::where([['is_admin','=', 1],['id','<>',auth()->user()->id]])->chunk(10,function ($users) use ($user){
            \Notification::route('mail',$users)->notify(new UserDetailsChanged($user));
        });

        return response()->json(['message' => 'user changes initiated successfully']);
    }

    public function CompleteUserUpdate(Request $request, $id)
    {
        $fields = $request->validate([
            'approve' => 'boolean'
        ]);

        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'user not found'], 404);
        if ($user->pending_user_data == null) return response()->json(['message' => 'user has no pending changes'], 400);

        if ($user->changed_by == auth()->user()->id) return response()->json(['message' => 'You cannot complete changes you initiated'], 403);

        if (!$fields['approve']) {
            $user->update(['pending_user_data' => null]);
            return response()->json(['message' => 'changes declined successfully']);
        }

        $user->update(json_decode($user->pending_user_data, true));

        return response()->json(['message' => 'changes approved successfully']);
    }
}
