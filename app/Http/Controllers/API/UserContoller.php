<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserInfo;
use App\Http\Resources\IndexResource;
use App\Http\Resources\ShowSingleDataResource;
use App\Http\Resources\StoreUserInfoResource;
use App\Mail\UserAccount;
use App\Mail\UserRegistration;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserContoller extends Controller
{

    public function getToken()
    {
        return response()->json(['csrf_token' => csrf_token()]);
    }

    public function generateOTP($otpDigits)
    {
        return rand(pow(10, $otpDigits - 1), pow(10, $otpDigits) - 1);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user_type = '';
            if ($user->isSuperAdmin()) {
                $user_type = 'super admin';
            }
            if ($user->isAdmin()) {
                $user_type = 'admin';
            }
            if ($user->isUser()) {
                $user_type = 'user';
            }
            $token = $user->createToken('Admin Token')->plainTextToken;
            return response()->json(['token' => $token, 'user_type' => $user_type]);
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
            return new IndexResource($user);
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
            $otp = $this->generateOTP(4);
            $userData['otp'] = $otp;
            $userData['user'] = 0;
            $userData['password'] = Hash::make($userData['password']);

            // Validate the request using the StoreUserInfoResource
            $validator = Validator::make($userData, (new StoreUserInfoResource($request))->toArray($request));
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $users = User::create($userData);

            // send email to registered users
            $body = [
                'name' => $users->name,
                'otp' => $otp,
            ];
            Mail::to($users->email)->send(new UserRegistration($body));

            return Response([
                'success_msg' => 'User registered successfully',
                'data' => $users,
                200
            ]);
        } catch (Exception $error) {
            return Response([
                'message' => $error->getMessage()
            ], 401);
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

            $user = User::where('otp', $request->otp)->first();
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
            $userData = new ShowSingleDataResource($user);
            return Response(
                [
                    'data' => $userData,
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
                throw new Exception($validator->errors()->first());
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

    public function delete(string $id)
    {
        try {
            $user = User::findOrFail($id);
            if (Gate::allows('superadmin')) {
                $user->forceDelete();
                return response()->json([
                    'message' => 'Archived record permanently deleted'
                ]);
            } else {
                abort(403, "Unauthorized action.");
            }
        } catch (Exception $error) {
            return response()->json([
                'message' => 'User not found',
                'error' => $error->getMessage()
            ], 404);
        }
    }

    // Create an admin user
    public function admins(Request $request)
    {
        try {
            if (Gate::allows('superadmin')) {
                $validatedData = $request->validate([
                    'name' => 'required',
                    'username' => 'required',
                    'email' => 'required|unique:users,email',
                    'password' => 'required',
                    'street' => 'nullable',
                    'suite' => 'nullable',
                    'city' => 'nullable',
                    'zip_code' => 'nullable',
                    'latitude' => 'nullable',
                    'longitude' => 'nullable',
                ]);
                $validatedData['admin'] = 1;
                User::create($validatedData);
                // send email to admins
                $body = [
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'password' => $validatedData['password'],
                ];
                Mail::to($validatedData['email'])->send(new UserAccount($body));
                return response()->json([
                    'message' => 'Admin account created successfully',
                ], 200);
            } else {
                abort(403, "Unauthorized action.");
            }
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Whoops! Something went wrong, please try again ...',
                'error' => $error->getMessage()
            ], 403);
        }
    }

    // create an end user
    public function users(Request $request)
    {
        try {
            if (Gate::allows('superadmin') || Gate::allows('admin')) {
                $validatedData = $request->validate([
                    'name' => 'required',
                    'username' => 'required',
                    'email' => 'required|unique:users,email',
                    'password' => 'required',
                    'street' => 'nullable',
                    'suite' => 'nullable',
                    'city' => 'nullable',
                    'zip_code' => 'nullable',
                    'latitude' => 'nullable',
                    'longitude' => 'nullable',
                ]);
                $validatedData['user'] = 1;
                User::create($validatedData);

                // send email to users
                $body = [
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'password' => $validatedData['password'],
                ];
                Mail::to($validatedData['email'])->send(new UserAccount($body));
                return response()->json([
                    'message' => 'User account created successfully',
                ], 200);
            } else {
                abort(403, "Unauthorized action.");
            }
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Whoops! Something went wrong, please try again ...',
                'error' => $error->getMessage()
            ], 403);
        }
    }

    // resend otp
    public function resendOtp(Request $request)
    {
        $otp = $this->generateOTP(4);
        // Update the user's record with the new OTP value
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->otp = $otp;
        $user->save();
        // Send the new OTP via email
        $body = [
            'name' => $user->name,
            'otp' => $otp,
        ];
        Mail::to($request->email)->send(new UserRegistration($body));
        return response()->json([
            'message' => 'OTP has been sent again',
        ], 200);
    }
}
