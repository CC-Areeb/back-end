<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    // Posts per day
    public function postPerDay()
    {
        $postsPerDay = Post::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->get();
        return response()->json($postsPerDay, 200);
    }

    public function activeUsers()
    {
        $users = User::where('user', 1)->get();
        $data = $users->map(function ($user) {
            return [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'password' => $user->password,
                'super_admin' => $user->super_admin,
                'admin' => $user->admin,
                'user' => $user->user,
            ];
        });
        return response()->json($data, 200);
    }
}
