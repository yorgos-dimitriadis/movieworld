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
      ->withCount(['votes as likes' => fn($q) => $q->where('type', 'like')])
      ->withCount(['votes as hates' => fn($q) => $q->where('type', 'hate')])
      ->when($sort === 'likes', fn($q) => $q->orderBy('likes', 'desc'))
      ->when($sort === 'hates', fn($q) => $q->orderBy('hates', 'desc'))
      ->when($sort === 'date', fn($q) => $q->orderBy('created_at', 'desc'))
      ->paginate();

    return Inertia::render('Movies/Index', [
      'canLogin' => Route::has('login'),
      'canRegister' => Route::has('register'),
      'movies' => $movies,
    ]);
  }

  public function store(Request $request)
  {
    if (!auth()->check()) {
      return redirect()->route('login')->with('error', 'You must be logged in to create a movie.');
    }

    $request->validate([
      'title' => 'required|string|max:255|unique:movies,title',
      'description' => 'nullable|string',
    ]);

    $movie = new Movie();
    $movie->user_id = auth()->id();
    $movie->title = $request->title;
    $movie->description = $request->description;

    $movie->save();

    return redirect()->route('home')->with('success', 'Movie created successfully.');
  }

}
