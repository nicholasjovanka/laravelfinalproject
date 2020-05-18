<?php
namespace App\Http\Controllers\API;

use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use function MongoDB\BSON\toJSON;
use Illuminate\Support\Facades\File;

class UserController extends Controller

{public $successStatus = 200;/**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $bool = $user->tokens;
            if(!$bool->isEmpty()){
                foreach($bool as $token)
                $token->revoke();
                $token->delete();
            }
            $success['token'] =  $user->createToken('MyApp')-> accessToken;
            return response()->json(['success' => $success], $this-> successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:App\User,email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $user->userType = 'user';
        $user->save();

        return response()->json(['success'=>true], $this-> successStatus);
    }
    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        $tokens = $user->tokens;
        foreach($tokens as $t){
            $t->revoke();
            $t->delete();
        }

        return response()->json(['success' => 'success'], $this-> successStatus);
    }

    public function setProfilePicture(request $request)
    {
        if($request->hasFile('userimage')) {
            $extension = $request->file('userimage')->getClientOriginalExtension();
            $filename= uniqid('userProfile');
            $path = $request->file('userimage')->move(public_path("/userprofile/"),$filename.'.'.$extension);
            $actualpath= "/userprofile/".$filename.'.'.$extension;
            $user = User::find(Auth::user()->id);
            if(!is_null($user->userImage)){
                $oldpath = $user->userImage;
                File::delete(public_path($oldpath));
            }
            $user->userImage=$actualpath;
            $user->save();
        return response()->json(['success'=>true],200);
        }
        else{
            return response()->json(['success'=>false],400);
        }

    }
    public function getimage()
    {
//        $filename = User::where('email', 'nicholasjovanka@gmail.com')->get();
//        $collection = collect($filename)->toJson();
//        $data = json_decode($collection,true);
//        return $data[0]['id'];

    $filename = User::where('email', Auth::user()->email)->first();
    return response()->download(public_path($filename->userImage),'User Image');
    }

    public function test(){
    }

}
