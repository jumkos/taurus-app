<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDetails;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
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
            // 'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            // 'name' => 'required|string|max:255',
            // 'division_id' => 'required|integer',
            // 'region_id' => 'required|integer',
            // 'branch_location_id' => 'required|integer',
            'phone' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);

        $employee = DB::table('employees')
            ->select('hak_akses', 'email', 'name', 'division_id', 'province_id', 'city_id')
            ->where('nip', $request['nip'])
            ->first();

        $newUser = array(
            "nip" => $request['nip'],
            "email" => $employee->email,
            "hak_akses" => $employee->hak_akses,
            "password" => $request['password'],
            "remember_token" => $request['remember_token'],
        );
        $user = User::create($newUser);

        $newUserDetail = array(
            "name" => $employee->name,
            "user_id" => $user->id,
            "division_id" => $employee->division_id,
            "region_id" => $employee->province_id,
            "city_id" => $employee->city_id,
            "phone" => $request['phone']
        );

        UserDetails::create($newUserDetail);
        // $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $user->name = $employee->name;
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
        return redirect(env('FRONT_URL_RESET'). $token);
    }

    public function resetPassword(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );


        if ($status === Password::PASSWORD_RESET) {
            $response = ['message' => 'Password successfully updated'];
            return response($response, 200);
        } else {
            $response = ['message' => 'Error ocurred'];
            return response($response, 500);
        }
    }

    public function userInfo()
    {

        $user = auth()->user();

        $rating = DB::table('user_details')
                        ->join('users', 'users.id', '=', 'user_details.user_id')
                        ->select(DB::raw('IFNULL(user_details.rating,0) AS rating, IFNULL(user_details.rating_by,0) AS rating_by'))
                        ->where('user_id', $user->id)
                        ->first();

            if ($rating->rating_by != 0) {
                $avgRating = $rating->rating / $rating->rating_by;
            }else{
                $avgRating = 0;
            }



        $userDetails = DB::table('user_details')
                        ->join('divisions', 'user_details.division_id', '=', 'divisions.id')
                        ->join('regions', 'user_details.region_id', '=', 'regions.id')
                        ->join('cities', 'user_details.city_id', '=', 'cities.id')
                        ->join('users', 'users.id', '=', 'user_details.user_id')
                        ->select(DB::raw('user_id as id,user_details.name, divisions.name as division, regions.name as region, cities.name as city, users.nip as nip, IFNULL(user_details.point,0) AS point, IFNULL(user_details.rating_by,0) AS rating_by, user_details.phone as phone'))
                        ->where('user_id', $user->id)
                        ->first();

        $rankOrder = DB::table('user_details')
                        ->orderBy('point', 'desc')
                        ->orderBy('updated_at', 'desc')
                        ->get();

        $position = $rankOrder->search(function ($rankOrder) use ($user){
            return $rankOrder->user_id == $user->id;
        });

        $position = $position + 1;

        $userDetails->avg_rating = $avgRating;
        $userDetails->current_rank = $position;
        $user->user_details = $userDetails;
        $response = ['user' => $user];
        return response($response, 200);

    }

    public function getUserByNIP($nip)
    {
        $userName = DB::table('employees')
            ->select('name')
            ->where('nip', $nip)
            ->first();

        if ($userName == '') {
            return response(['errors' => 'Employee data not found'], 404);
        }

        return response(['employee' => $userName->name], 200);
    }
}
