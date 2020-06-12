<?php

namespace App\Http\Controllers;

use App\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function deleteReviewAdmin($user_id, $game_id){
        $review = Review::where('user_id', $user_id)->where('game_id', $game_id)->first();
        $review->delete();
        return response()->json(['success'=>"true"],200);
    }

    public function getSpecificUserReview($game_id) {
        $review = Review::where('user_id', Auth::user()->id)->where('game_id', $game_id)->first();
        return response($review);
    }



    public function getfiveReview($id){
        $review = Review::where('game_id', $id)->orderBy('created_at','desc')->paginate(5);
        return response($review);
    }

    public function deleteReviewUser($game_id){
        $review = Review::where('user_id', Auth::user()->id)->where('game_id', $game_id)->first();
        $review->delete();
        return response()->json(['success'=>"true"],200);
    }

    public function CalculateScore($id){
        $totalreview = Review::where('game_id', $id)->get();
        if($totalreview->count() > 0) {
            $totalscore = 0;
            foreach ($totalreview as $review) {
                $totalscore += $review['userScore'];
            }
            $score = $totalscore / $totalreview->count();
            return response(round($score, 1));
        } else{
            return 0;
        }
    }

    public function getAllReview($id){
        $totalreview = Review::where('game_id', $id)->get();
        return response(($totalreview));
    }
}
