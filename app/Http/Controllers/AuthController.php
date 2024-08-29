<?php /** @noinspection ALL */

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Requirement;
use App\Models\Permissions\Role;
use App\Models\Permissions\User;
use App\Traits\MyTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule as ValidationRule;
use App\Traits\NotificationTrait;

class AuthController extends Controller
{

    use MyTrait, NotificationTrait;

    public function register(Request $request): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', ValidationRule::unique(table: 'users')],
            'password' => ['required', 'string', 'min:8'],
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $request['password'] = bcrypt($request['password']);
        $photoPath = $this->saveImage($request->photo);

        $user = User::create([
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

        $data['user'] = $user;
        $data['token_type'] = 'Bearer';
        $data['access_token'] = $tokenResult->accessToken;

        return response()->json(['data' => $data, 'message' => 'signed up successfully']);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'email' => ['required', 'email', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Account or password is not correct'], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('personal Access Token');

        $role = Role::find($user->role_id);


        if ($user->role_id == 6) {
            $validator = validator::make($request->all(), [
                'token' => ['required', 'string'],
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->all(), status: 400);
            }
            if ($user->deviceToken != $request->token) {
                $user->update(['deviceToken' => $request->token]);
            }
            $this->send($user, 'WELCOME', 'Logged In successfully ');
        }

        $data['user'] = $user;
        $data['token_type'] = 'Bearer';
        $data['access_token'] = $tokenResult->accessToken;
        $data['role'] = $role;

        return response()->json(['data' => $data, 'message' => 'logged In successfully']);
    }

    public function logout(Request $request): JsonResponse
    {
        if ($request->user()->role_id == 6) {
            $this->send($request->user(), 'GOODBYE', 'Logged Out successfully ');
        }
        $request->user()->token()->revoke();
        return response()->json(['message' => 'logged out ']);
    }

    public function profile(Request $request): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'phone' => ['string', 'max:10', 'min:10',  ValidationRule::unique(table: 'users')],
            'age' => ['integer'],
            'address' => ['string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $updateData = array_filter($request->only('phone', 'address', 'age'));
        $updateData['confirmation'] = '2';

        $user = Auth::user();
        $user->update($updateData);

        return response()->json(['user' => $user, 'message' => 'Your profile updated successfully']);
    }


    public function photo(Request $request): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $photoPath = $this->saveImage($request->photo);

        $user = Auth::user();
        $user->update([
            'photo' => $photoPath,
        ]);

        return response()->json(['photoPath' => $photoPath, 'message' => 'Your photo added successfully']);
    }


    public function passport(Request $request): JsonResponse
    {

        $validator = validator::make($request->all(), [
            'passport' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $passportPath = $this->saveImage($request->passport);

        $user = Auth::user();
        $user->update([
            'passport' => $passportPath,
        ]);
        return response()->json(['passportPath' => $passportPath, 'message' => 'Your passport added successfully']);
    }

}


