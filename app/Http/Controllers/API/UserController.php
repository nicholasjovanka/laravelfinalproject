<?php
namespace App\Http\Controllers\API;

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
            return response()->json(['success' => $success, 'is_verified' => $user->email_verified_at], $this-> successStatus);
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
            'name' => 'required|unique:App\User,name',
            'email' => 'required|email|unique:App\User,email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response(['error'=>$validator->errors()],401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $user->userType = 'user';
        $user->userImage = '/userprofile/default_user_picture.png';
        $user->save();
        return response(['success'=>true], $this-> successStatus);
//        return response()->json(['success'=>true], $this-> successStatus);
    }
    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserDetails(Request $request)
    {
        $user = Auth::user();
        return response($user,$this->successStatus);
    }

    public function setProfilePicture(request $request)
    {
        if($request->hasFile('userimage')) {
            $validator = Validator::make($request->all(), [
                'userimage' => 'mimes:jpeg,jpg,png|required|max:1024',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }
            $extension = $request->file('userimage')->getClientOriginalExtension();
            $img = Image::make($request->file('userimage'))->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });
            $filename= uniqid('userProfile');
            $temporarypath= "/userprofile/".$filename.'.'.$extension;
            $img->save(public_path($temporarypath));
            $imagefile = new File(public_path($temporarypath));
            $path = Storage::disk('local')->put('userprofile',$imagefile);
            $img->destroy();
            File2::delete(public_path($temporarypath));
            $user = User::find(Auth::user()->id);
            if(!is_null($user->userImage)){
                $oldpath = $user->userImage;
                if($oldpath !== 'userprofile/default_user_picture.png') {
                    Storage::disk('local')->delete($oldpath);
                }
            }
            $user->userImage=$path;
            $user->save();

        return response()->json(['success'=>true],200);
        }
        else{
            return response()->json(['success'=>false],400);
        }

    }


    public function verifyemail(){
        $isverified = Auth::user()->email_verified_at;
        return response()->json(['email_verified_at' => $isverified ],200);
    }

    public function getUserImage(){
        //        $filename = User::where('email', 'nicholasjovanka@gmail.com')->get();
//        $collection = collect($filename)->toJson();
//        $data = json_decode($collection,true);
//        return $data[0]['id'];
        $filename = User::where('email', Auth::user()->email)->first();
//        return response()->download(public_path($filename->userImage),'User Image');
        return Storage::disk('local')->download($filename->userImage);
    }

    public function updateProfile(Request $request){
        $user = User::find(Auth::user()->id);
        if($user->name !== $request->name){
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:App\User,name',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }
            $user->name = $request->name;
        }
        $user->birthdate = $request->birthdate;
        if($user->email !== $request->email){
            $validator = Validator::make($request->all(), [
                'email' => 'required|unique:App\User|email',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }
            $user->email = $request->email;
        }
        if($request->hasFile('userimage')){
            $this->setProfilePicture($request);
        }
        if($request->has('newpassword')){
            if(Hash::check($request->oldpassword, Auth::user()->password)){
                $validator = Validator::make($request->all(), [
                    'newpassword' => 'required',
                    'c_password' => 'required|same:newpassword',
                ]);
                if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors()], 401);
                }
                $user->password= bcrypt($request->newpassword);
            }
            else{
                return response(['error'=>'Wrong Password'],400);
            }
        }
        $user->save();
        return response(['success'=>true],200);
    }

    public function isAdmin(){
        if(Auth::user()->userType == 'admin'){
            return  1;
        }else{
            return  0;
        }
    }

    public function isLoggedIn(){
        if(Auth::user() !== null){
            return 1;
        }else{
            return 0;
        }
    }


    public function username($id){
        $username = User::find($id);
        return response($username->name);
    }
}
