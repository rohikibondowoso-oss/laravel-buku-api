<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Book;

class AuthTokenApi
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bookId = $request->route('id') ?? $request->route('book');

        if ($bookId) {
            $book = is_object($bookId) ? $bookId : Book::find($bookId);

            if (!$book) {
                return response()->json([
                    'message' => 'Data buku tidak ditemukan.'
                ], 404);
            }

            // Validasi
            if ($book->user_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'Akses ditolak. Anda tidak berhak mengakses, mengedit, atau menghapus buku milik user lain.'
                ], 403);
            }
        }

        return $next($request);
    }
}
