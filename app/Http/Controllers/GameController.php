<?php

namespace App\Http\Controllers;

use App\Game;
use App\User;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File as File2;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use function MongoDB\BSON\toJSON;
use Validator;
use Image;
class GameController extends Controller
{
    public function createGame(Request $request){
    $validator = Validator::make($request->all(),[
        'gameName' => 'required|unique:App\Game,gameName',
    ]);
        if ($validator->fails()) {
            return response(['error'=>$validator->errors()],401);
        }
    $content = $request->all();
    $game = Game::create($content);
    if ($request->hasFile('gameImage')){
        $this->setGamePicture($request, $game->id);
    } else{
        $game->gameImage = '/gameimages/default_game_image.jpg';
        $game->save();
    }

    return response(['success'=>true, $game], 200);
    }

    public function getAllGame(){
        $getgame = Game::all();
        $name = $getgame->pluck('gameName');
        $id = $getgame->pluck('id');
        $gamearray = [];
        for ($x = 0; $x < sizeof($getgame); $x++){
            $object = [
                'id'=> $id[$x],
                'gameName'=> $name [$x]
            ];
            array_push($gamearray, $object);
        }
        return response($gamearray,200);
    }

    public function getLatestGame(){
        $game = Game::orderBy('created_at','desc')->take(5)->get();
        return response($game);
    }

    public function getCertainGame($id){
        $getgame = Game::find($id);
        return $getgame;
    }

    public function getGameImage($id){
        $filename = Game::find($id);
        return response()->download(public_path($filename->gameImage),'Game Image');
    }

    public function filterGameName(Request $request){
        if ($request->has('gameName') && !empty($request->gameName))
        {
            $result = Game::orderBy('gameName','desc')->where('gameName','like', $request->gameName.'%')->take(10)->pluck('gameName');
            return response($result);
        }
    }

    public function getGameId(Request $request){
       $id = Game::where('gameName', $request->gameName)->first();
        $object = [
            'id'=> $id->id,
            'gameName'=> $id->gameName,
        ];
        return response($object,200);
    }

    public function updateGame(Request $request, $id){
        $game = Game::find($id);
        if($game->gameName !== $request->gameName){
            $validator = Validator::make($request->all(), [
                'gameName' => 'required|unique:App\Game,gameName',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }
            $game->gameName = $request->gameName;
        }
        $game->gameDescription = $request->gameDescription;
        $game->gameTrailer = $request->gameTrailer;
        $game->gamePublisher = $request->gamePublisher;
        $game->gameReleaseDate = $request->gameReleaseDate;
        $game->platform = $request->platform;
        $game->onSteam = $request->onSteam;
        $game->AgeRating = $request->AgeRating;
        $game->steamId = $request->steamId;
        $game->save();
        if($request->hasFile('gameImage')){
            $this->setGamePicture($request, $id);
        }
        return response()->json(['success'=>"true"],200);
    }

    public function deleteGame($id){
        $game = Game::find($id);
        $filepath = $game->gameImage;
        if ($filepath !== '/gameimages/default_game_image.jpg'){
           File2::delete(public_path($filepath));
        }
        $game->delete();
        return response('Sucessful Delete',200);
    }

    public function setGamePicture(request $request , $id)
    {
        if($request->hasFile('gameImage')) {
            $validator = Validator::make($request->all(), [
                'gameImage' => 'mimes:jpeg,jpg,png|required|max:1024',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }
            $extension = $request->file('gameImage')->getClientOriginalExtension();
            $img = Image::make($request->file('gameImage'))->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
            });
            $filename= uniqid('gameImage');
            $path= "/gameimages/".$filename.'.'.$extension;
            $img->save(public_path($path),95);
            $game = Game::find($id);
            if(!is_null($game->gameImage)){
                $oldpath = $game->gameImage;
                if($oldpath !== '/gameimages/default_game_image.jpg') {
                   File2::delete(public_path($oldpath));
                }
            }
            $game->gameImage=$path;
            $game->save();

            return response()->json(['success'=>true],200);
        }
        else{
            return response()->json(['success'=>false],400);
        }

    }

    public function getSteamGame($id){
        $response = Http::get('http://store.steampowered.com/appreviews/'.$id.'?json=1?&day_range=all&language=all&review_type=all&purchase_type=alll&num_per_page=5');
        $result = round((($response['query_summary']['total_positive']/$response['query_summary']['total_reviews']) * 5),1);
        return response($result);
    }
}
