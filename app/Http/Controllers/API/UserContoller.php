<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserInfo;
use App\Http\Requests\UpdateUserInfo;
use App\Mail\UserRegistration;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserContoller extends Controller
{

    public function generateOTP($otpDigits)
    {
        return rand(pow(10, $otpDigits - 1), pow(10, $otpDigits) - 1);
    }

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
            $userData['user'] = 0;
            $userData['password'] = Hash::make($userData['password']);
            $validator = Validator::make($userData, $request->rules(), $request->messages());
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $users = User::create($userData);

            // send email to registered users
            $body = [
                'name' => $users->name,
                'otp' => $this->generateOTP(4),
            ];
            Mail::to($users->email)->send(new UserRegistration($body));

            return Response(
                [
                    'success_msg' => 'User registered successfully',
                    'data' => $users,
                    200
                ],
            );
        } catch (Exception $error) {
            return Response(
                ['message' => $error->getMessage()],
                401
            );
        }
    }

    public function verifyOTP(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $user = User::where('email', $request->email)->where('otp', $request->otp)->first();
            if (!$user) {
                throw new Exception("Invalid email or OTPs");
            }

            if ($user->user == 0) {
                $user->update([
                    'user' => 1,
                    'otp' => $this->generateOTP(4),
                ]);
                return response()->json(['message' => 'Registration completed successfully'], 200);
            } else {
                return response()->json(['error' => 'Your registration process is already complete'], 400);
            }
        } catch (Exception $error) {
            return Response(
                ['message' => $error->getMessage()],
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
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            // Validate the request data manually
            $validator = Validator::make($request->all(), [
                'name' => 'nullable',
                'username' => 'nullable|unique:users,username,' . $id,
                'email' => 'nullable|email|unique:users,email,' . $id,
                'password' => 'nullable',
                'street' => 'nullable',
                'suite' => 'nullable',
                'city' => 'nullable',
                'zip_code' => 'nullable',
                'latitude' => 'nullable',
                'longitude' => 'nullable',
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            // Prepare the data to update the user model
            $dataToUpdate = [
                'name' => $request->filled('name') ? $request->name : $user->name,
                'username' => $request->filled('username') ? $request->username : $user->username,
                'email' => $request->filled('email') ? $request->email : $user->email,
                'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
                'street' => $request->input('street', $user->street),
                'suite' => $request->input('suite', $user->suite),
                'city' => $request->input('city', $user->city),
                'zip_code' => $request->input('zip_code', $user->zip_code),
                'latitude' => $request->input('latitude', $user->latitude),
                'longitude' => $request->input('longitude', $user->longitude),
            ];

            // Update the user model with the data (either new values or old values)
            $update = $user->update($dataToUpdate);

            if ($update) {
                return response()->json(
                    [
                        'message' => 'Information updated successfully',
                    ],
                    200
                );
            }

            return response()->json(['message' => 'Oops! Something went wrong, please try again'], 500);
        } catch (ModelNotFoundException $error) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (Exception $error) {
            return response()->json(['message' => 'Error: ' . $error->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function archive(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(
                [
                    'message' => "Archived successfully"
                ],
                200
            );
        } catch (Exception $error) {
            return Response(
                ['message' => 'User info has already been archived!'],
                401
            );
        }
    }
}
