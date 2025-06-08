<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
  public function vote(Request $request, Movie $movie)
  {
    if ($movie->user_id === auth()->id()) {
      return redirect()->back()->with('error', 'You cannot vote on your own movie.');
    }

    $request->validate(['type' => 'required|in:like,hate']);

    $vote = Vote::firstOrNew([
      'user_id' => auth()->id(),
      'movie_id' => $movie->id,
    ]);


    if ($vote->exists && $vote->type === $request->type) {
      $vote->delete(); // retract
    }
    else {
      $vote->type = $request->type;
      $vote->save(); // new or updated
    }

    return redirect()->back();
  }
}
