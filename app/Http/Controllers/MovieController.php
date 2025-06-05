<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class MovieController extends Controller
{

  public function index(Request $request)
  {
    $sort = $request->get('sort', 'date');

    $movies = Movie::with('user')
      ->withCount(['votes as likes_count' => fn($q) => $q->where('type', 'like')])
      ->withCount(['votes as hates_count' => fn($q) => $q->where('type', 'hate')])
      ->when($sort === 'likes', fn($q) => $q->orderBy('likes_count', 'desc'))
      ->when($sort === 'hates', fn($q) => $q->orderBy('hates_count', 'desc'))
      ->when($sort === 'date', fn($q) => $q->orderBy('created_at', 'desc'))
      ->paginate(10);

    return Inertia::render('Movies/Index', [
      'canLogin' => Route::has('login'),
      'canRegister' => Route::has('register'),
      'movies' => $movies,
    ]);
  }

}
