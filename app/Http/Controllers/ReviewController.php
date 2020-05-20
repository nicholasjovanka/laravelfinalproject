<?php

namespace App\Http\Controllers;

use App\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
  public function addReview(Request $request){
  $review = new Review;
  $review->user_id = $request->user_id;
  $review->game_id = $request->game_id;
  $review->userReview = $request->userReview;
  $review->userScore = $request->userScore;
  $review->save();
  return response()->json(['success'=>true,$review],200);

  }

}
