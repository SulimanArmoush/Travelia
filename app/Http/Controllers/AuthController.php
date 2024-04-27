<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permissions\Role;
use App\Models\Permissions\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule as ValidationRule;
use App\Traits\PhotoTrait;

class AuthController extends Controller
{

    use PhotoTrait;

    public function register(Request $request)
    {
        $validator = validator::make($request->all(), [
            'firstName'=> ['required', 'string', 'max:255'],
            'lastName'=> ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', ValidationRule::unique(table: 'users')],
            'password'=> ['required', 'string', 'min:8'],
            'role_id'=>'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $request['password'] = bcrypt($request['password']);

        $user = User::query()->create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email ,
            'password' => $request->password ,
            'wallet' =>0.0,
            'role_id' => $request->role_id,
        ]);

        $tokenResult = $user->createToken('personal Access Token');

        $data['user'] = $user;
        $data['token_type'] = 'Bearer';
        $data['access_token'] = $tokenResult->accessToken;

        return response()->json(['data'=>$data, 'status' => 200, 'message' => 'signed up successfully']);
    }

    public function login(Request $request)
    {
        $validator = validator::make($request->all(), [
            'email' => ['required', 'email','string', 'max:255'], 
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 422);
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Account or password is not correct'], 401);
        }

        $user = $request->user();
        //add token to user
        $tokenResult = $user->createToken('personal Access Token'); //->accessToken;

        $user = User::where('id', '=', auth()->id())->first();
        $role = Role::where('id', '=', $user->role_id)->first();

        $data['user'] = $user;
        $data['token_type'] = 'Bearer';
        $data['access_token'] = $tokenResult->accessToken;
        $data['role'] = $role;

        return response()->json(['data'=> $data, 'status' => 200, 'message' => 'logedd In successfully']);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'logged out ', 'status' => 200]);
    }

    public function profile(Request $request){

        $validator = validator::make($request->all(), [
        'phone' => ['string', 'max:10', 'min:10', 'regex:/^09[0-9]{8}/', ValidationRule::unique(table: 'users')], //syrian number
        'age' => ['integer'],
        'address'=> ['string','max:255'], 
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        User::find(Auth::id())->update([
            'phone'=> $request->phone,
            'address'=> $request->address,
            'age'=> $request->age,
        ]) ;
        return response()->json(['message' => 'Your profile updated successfully']);
    }

    public function photo(Request $request){

        $validator = validator::make($request->all(), [
        'photo' => ['image','mimes:jpeg,png,jpg,gif','max:2048'], 
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $photoPath = $this->saveImage($request->photo);

        User::find(Auth::id())->update([
            'photo' => $photoPath,
        ]) ;

        return response()->json(['photoPath'=>$photoPath,'message' => 'Your photo added successfully']);
    }

    public function passport(Request $request){

        $validator = validator::make($request->all(), [
        'passport' => ['image','mimes:jpeg,png,jpg,gif','max:2048'], 
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $passportPath = $this->saveImage($request->passport);

        User::find(Auth::id())->update([
            'passport' => $passportPath,
        ]) ;

        return response()->json(['passportPath'=>$passportPath,'message' => 'Your passport added successfully']);
    }
    
}


