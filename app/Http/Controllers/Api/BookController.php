<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Auth::user()->books;
        return response()->json([
            'status' => 'success',
            'data' => $books
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $book = Auth::user()->books()->create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Buku berhasil ditambahkan',
            'data' => $book
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        // Cek kepemilikan buku (tidak boleh akses buku user lain)
        if ($book->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized: Anda tidak memiliki akses ke buku ini'], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $book
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        // Cek kepemilikan buku
        if ($book->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized: Anda tidak bisa mengedit buku user lain'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $book->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Buku berhasil diupdate',
            'data' => $book
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        // Cek kepemilikan buku
        if ($book->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized: Anda tidak bisa menghapus buku user lain'], 403);
        }

        $book->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Buku berhasil dihapus'
        ]);
    }
}
