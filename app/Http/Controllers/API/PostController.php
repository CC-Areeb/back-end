<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::orderBy('created_at', 'asc')->get();
        $postData = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'excerpt' => $post->excerpt,
                'body' => $post->body,
                'slug' => $post->slug,
                'author' => $post->author,
                'category' => $post->category,
                'tags' => $post->tags,
                'image' => $post->image,
            ];
        });
        return response()->json([
            'data' => $postData
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // get the logged in user
        $user = Auth::user();
        try {
            if ($user) {
                $validatedData = $request->validate([
                    'title' => 'required|string|max:255',
                    'excerpt' => 'required|string',
                    'body' => 'required|string',
                    'slug' => 'required|string|unique:posts',
                    'author' => 'required|string',
                    'category' => 'required|string',
                    'tags' => 'array|nullable',
                    'image' => 'string|nullable',
                ]);
                $post = Post::create($validatedData);
                return response()->json($post, 200);
            }
        } catch (Exception $error) {
            return response()->json([
                'error' => $error->getMessage(),
                'message' => 'Whoops! There was an error! Please try again later'
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        try {
            if ($user) {
                $post = Post::findOrFail($id);
                $singlePost = $post->map(function ($OnePost) {
                    return [
                        'title' => $OnePost->title,
                        'excerpt' => $OnePost->excerpt,
                        'body' => $OnePost->body,
                        'slug' => $OnePost->slug,
                        'author' => $OnePost->author,
                        'category' => $OnePost->category,
                        'tags' => $OnePost->tags,
                        'image' => $OnePost->image,
                    ];
                });
                return response()->json([
                    'data' => $singlePost
                ], 200);
            }
        } catch (Exception $error) {
            return response()->json([
                'error' => $error->getMessage(),
                'message' => 'Whoops! There was an error! Please try again later'
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        try {
            if ($user) {
                $post = Post::findOrFail($id);

                $validatedData = $request->validate([
                    'title' => 'required|string|max:255',
                    'excerpt' => 'required|string',
                    'body' => 'required|string',
                    'slug' => 'required|string|unique:posts,slug,' . $id,
                    'author' => 'required|string',
                    'category' => 'required|string',
                    'tags' => 'array',
                    'image' => 'string|nullable',
                ]);
                $post->update($validatedData);
            }
        } catch (Exception $error) {
            return response()->json([
                'error' => $error->getMessage(),
                'message' => 'Whoops! There was an error! Please try again later'
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            Post::findOrFail($id)->delete();
            return response()->json([
                'message' => 'Post has been deleted successfully!'
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'error' => $error->getMessage(),
                'message' => 'Whoops! There was an error! Please try again later'
            ], 400);
        }
    }
}
