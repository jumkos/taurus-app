<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDetails;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;

class PassportAuthController extends Controller
{
    /**
     * Registration Req
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'required|string|unique:users|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'name' => 'required|string|max:255',
            'division_id' => 'required|integer',
            'region_id' => 'required|integer',
            'branch_location_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);

        $newUser = array(
            "nip" => $request['nip'],
            "email" => $request['email'],
            "password" => $request['password'],
            "remember_token" => $request['remember_token'],
        );
        $user = User::create($newUser);

        $newUserDetail = array(
            "name" => $request['name'],
            "user_id" => $user->id,
            "division_id" => $request['division_id'],
            "region_id" => $request['region_id'],
            "branch_location_id" => $request['branch_location_id'],
        );

        UserDetails::create($newUserDetail);
        // $token = $user->createToken('Laravel Password Grant Client')->accessToken;

        event(new Registered($user));
        $response = ['message' => 'Email verification has ben sent'];
        return response($response, 200);
    }

    /**
     * Login Req
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $loginType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';
        $user = User::where($loginType, $request->login)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                Auth::login($user, $request->get('remember'));
                $response = ['token' => $token];
                return response($response, 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" => 'User does not exist'];
            return response($response, 422);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === 'passwords.sent') {
            $response = ['message' => 'Reset password email has ben sent'];
            return response($response, 200);
        } else if ($status === 'passwords.throttled') {
            $response = ['message' => 'Please try again later'];
            return response($response, 429);
        } else {
            $response = ['message' => 'Email not found'];
            return response($response, 404);
        }

    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = auth()->user();

        $password = $request->post('password');

        DB::table('users')
            ->where('email', '=', $user->email)
            ->update(['password' => Hash::make($password)]);
        $response = ['message' => 'Password successfully updated'];
        return response($response, 200);
    }

    public function redirectForgotPassword(string $token): RedirectResponse
    {
        return redirect(env('FRONT_URL') . '/api/get-user/' . $token);
    }

    public function userInfo()
    {

        $user = auth()->user();
        $userDetails = DB::table('user_details')
                        ->join('divisions', 'user_details.division_id', '=', 'divisions.id')
                        ->join('regions', 'user_details.region_id', '=', 'regions.id')
                        ->join('branches', 'user_details.branch_location_id', '=', 'branches.id')
                        ->select('user_details.name', 'divisions.name as division', 'regions.name as region', 'branches.name as branch')
                        ->where('user_id', $user->id)
                        ->first();
        $user->user_details = $userDetails;
        $response = ['user' => $user];
        return response($response, 200);

    }
}
