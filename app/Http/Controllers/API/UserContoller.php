<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserInfo;
use App\Http\Requests\UpdateUserInfo;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserContoller extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('Admin Token')->plainTextToken;
            return response()->json(['token' => $token]);
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get all users
        $users = User::all();
        $userData = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
            ];
        });
        return Response([$userData, 200]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserInfo $request)
    {
        try {
            // register a new user
            $userData = $request->validated();
            $userData['password'] = Hash::make($userData['password']);
            $validator = Validator::make($userData, $request->rules(), $request->messages());
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $users = User::create($userData);
            return Response(
                [
                    'success_msg' => 'User registered successfully',
                    'data' => $users,
                    200
                ],
            );
        } catch (Exception $error) {
            return Response(
                ['message' => 'Error: ' . $error],
                401
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return Response(
                [
                    'data' => $user,
                    200
                ],
            );
        } catch (Exception $error) {
            return Response(
                ['message' => 'Error: ' . $error],
                401
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserInfo $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $update = $user->update([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
                'street' => $request->street,
                'suite' => $request->suite,
                'city' => $request->city,
                'zip_code' => $request->zip_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            if ($update) {
                return response()->json(['message' => 'Information updated successfully'], 200);
            }

            return response()->json(['message' => 'Oops! Something went wrong, please try again'], 500);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $error) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (Exception $error) {
            return response()->json(['message' => 'Error: ' . $error->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
