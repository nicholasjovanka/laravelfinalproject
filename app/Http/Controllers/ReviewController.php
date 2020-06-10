<?php

namespace App\Http\Controllers;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Validator;
use ZipArchive;
use function MongoDB\BSON\toJSON;
use Image;
use Illuminate\Support\Facades\File as File2;
use App\Review;

class ReviewController extends Controller
{
  public function addEditReview(Request $request){
    $oldReview = Review::where('user_id', Auth::user()->id)->where('game_id', $request->game_id)->first();
    if(is_null($oldReview)){
      $review = new Review;
      $review->user_id = Auth::user()->id;
      $review->game_id = $request->game_id;
      $review->userReview = $request->userReview;
      $review->userScore = $request->userScore;
      $review->save();
      return response()->json(['success'=>true,$review],200);
    }

    else{
      $oldReview->user_id = Auth::user()->id;
      $oldReview->game_id = $request->game_id;
      $oldReview->userReview = $request->userReview;
      $oldReview->userScore = $request->userScore;
      $oldReview->save();
      return response()->json(['success'=>true,$oldReview],200);
    }
  }

  public function deleteReviewAdmin($game_id){
      $review = Review::where('user_id', Auth::user()->id)->where('game_id', $game_id)->first();
      $review->delete();
      return response()->json(['success'=>"true"],200);
  }

  public function deleteReviewUser($game_id){
    $review = Review::where('user_id', Auth::user()->id)->where('game_id', $game_id)->first();
    $review->delete();
    return response()->json(['success'=>"true"],200);
  }

}
