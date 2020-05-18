<?php

namespace App\Http\Controllers;

use App\Game;
use Illuminate\Http\Request;
use function MongoDB\BSON\toJSON;

class GameController extends Controller
{
    public function create(Request $request){
    $validation = $request->validate([


    ]);
    if(!$validation){
        return response()->json([
           'success' => false
        ],401);
    }
    $content = $request->all;
    $game = Game::create($content);
    $game->save();
    }

    public function getAllGame(){
        $getgame = Game::all();
        if( $getgame->isEmpty()){
            return response()->json(['sucess'=>false,
                'test'=>[
                    'data1'=>'test'
                ]
                ]);
        }
        return response($getgame);
    }


    public function getCertainGame($id){
        $getgame = Game::find($id);

    }

    public function addGame(Request $request){
    $game = new Game;
    $game->gameName = $request->gameName;
    $game->gameDescription = $request->gameDescription;
    $game->platform = json_encode($request->input('platform'));
    $game->save();
    return response()->json(['success'=>true,$game],200);

    }

    public function updateGame(Request $request, $id){

        $game = Game::find($id);
        $game->gameName = $request->gameName;
        $game->save();
        return response()->json(['success'=>"true"],200);
    }

    public function deleteGame($id){
        $game = Game::find($id);
        $game->delete();
        return response()->json(['success'=>"true"
        ],200);
    }
}
