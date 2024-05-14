<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Requirement;
use App\Models\Permissions\Role;
use App\Models\Permissions\User;
use App\Traits\PhotoTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule as ValidationRule;

class AuthController extends Controller
{

    use PhotoTrait;

    public function register(Request $request)
    {
        $validator = validator::make($request->all(), [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', ValidationRule::unique(table: 'users')],
            'password' => ['required', 'string', 'min:8'],
            'photo' => ['required','image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
            'role_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $request['password'] = bcrypt($request['password']);
        $photoPath = $this->saveImage($request->photo);

        $user = User::query()->create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'password' => $request->password,
            'photo' => $photoPath,
            'role_id' => $request->role_id,
        ]);

        if (in_array($request->role_id, [2, 3, 4, 5])) {
            Requirement::create([
                'user_id' => $user->id,
                'note' => 'Approve or reject the account',
            ]);
        }

        $tokenResult = $user->createToken('personal Access Token');

        $u = User::find($user->id);

        if (!$u) {
            return response()->json(['message' => "User Not Found"], 404);
        }

        $data['user'] = $u;
        $data['token_type'] = 'Bearer';
        $data['access_token'] = $tokenResult->accessToken;

        return response()->json(['data' => $data, 'message' => 'signed up successfully'],200);
    }

    public function login(Request $request)
    {
        $validator = validator::make($request->all(), [
            'email' => ['required', 'email', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Account or password is not correct'], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('personal Access Token'); //->accessToken;

        $user = User::where('id', '=', auth()->id())->first();
        $role = Role::where('id', '=', $user->role_id)->first();

        $data['user'] = $user;
        $data['token_type'] = 'Bearer';
        $data['access_token'] = $tokenResult->accessToken;
        $data['role'] = $role;

        return response()->json(['data' => $data,'message' => 'logedd In successfully'],200);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'logged out '],200);
    }

    public function profile(Request $request)
    {

        $validator = validator::make($request->all(), [
            'phone' => ['string', 'max:10', 'min:10', 'regex:/^09[0-9]{8}/', ValidationRule::unique(table: 'users')], //syrian number
            'age' => ['integer'],
            'address' => ['string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        $user = User::find(Auth::id())->update([
            'phone' => $request->phone,
            'address' => $request->address,
            'age' => $request->age,
            'confirmation'=>'2'
        ]);

        return response()->json(['user'=>$user ,'message' => 'Your profile updated successfully'],200);
    }

    public function photo(Request $request)
    {
        $validator = validator::make($request->all(), [
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $photoPath = $this->saveImage($request->photo);

        User::find(Auth::id())->update([
            'photo' => $photoPath,
        ]);

        return response()->json(['photoPath' => $photoPath, 'message' => 'Your photo added successfully'],200);
    }

    public function passport(Request $request)
    {

        $validator = validator::make($request->all(), [
            'passport' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $passportPath = $this->saveImage($request->passport);

        User::find(Auth::id())->update([
            'passport' => $passportPath,
        ]);

        return response()->json(['passportPath' => $passportPath, 'message' => 'Your passport added successfully'],200);
    }

}


