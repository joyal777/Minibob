<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return view('admin'); // Admin dashboard view
    }
    public function indexshow()
{
    $posts = Post::all();
    return view('blogs', compact('posts'));
}

public function fetchAll()
{
    $posts = Post::all();
    return response()->json($posts);
}

// Store a new post
public function store(Request $request)
{
    // Validate incoming request
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'author' => 'required|string|max:255',
        'date' => 'required|date',
        'content' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Handle file upload (if any)
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('posts', 'public');
    }

    // Create a new post
    $post = Post::create([
        'name' => $validated['name'],
        'author' => $validated['author'],
        'date' => $validated['date'],
        'content' => $validated['content'],
        'image' => $imagePath, // Save the file path if image exists
    ]);

    return response()->json(['success' => 'Post created successfully', 'post' => $post]);
}

// Show a specific post
public function show($id)
{
    $post = Post::find($id);
    return response()->json($post);
}

// Update an existing post
public function update(Request $request, $id)
{
    // Validate incoming request
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'author' => 'required|string|max:255',
        'date' => 'required|date',
        'content' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Find the post
    $post = Post::find($id);

    // Handle file upload (if any)
    $imagePath = $post->image; // Keep the old image unless it's updated
    if ($request->hasFile('image')) {
        // Delete old image if exists
        if ($imagePath) {
            unlink(storage_path('app/public/' . $imagePath));
        }
        $imagePath = $request->file('image')->store('posts', 'public');
    }

    // Update the post
    $post->update([
        'name' => $validated['name'],
        'author' => $validated['author'],
        'date' => $validated['date'],
        'content' => $validated['content'],
        'image' => $imagePath,
    ]);

    return response()->json(['success' => 'Post updated successfully', 'post' => $post]);
}

// Delete a post
public function destroy($id)
{
    $post = Post::find($id);

    // Delete the associated image if it exists
    if ($post->image) {
        unlink(storage_path('app/public/' . $post->image));
    }

    // Delete the post
    $post->delete();

    return response()->json(['success' => 'Post deleted successfully']);
}

}
