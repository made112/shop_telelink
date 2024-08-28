<?php /** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api;

use App\Addon;
use App\Http\Controllers\OTPVerificationController;
use App\Models\BusinessSetting;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    protected function create(array $data)
    {
        if (Addon::where('unique_identifier', 'otp_system')->first() != null && Addon::where('unique_identifier', 'otp_system')->first()->activated){
            $email = null;
            $user = User::create([
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
//                'phone' => $data['phone'],
                'email' => isset($data['email']) ? $data['email'] : null,
                'verification_code' => rand(100000, 999999)
            ]);

            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->save();

//            $otpController = new OTPVerificationController;
//            $otpController->send_code($user);
            return $user;
        }
        return null;
    }

    public function signup(Request $request)
    {
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            if(User::where('email', $request->email)->first() != null){
                return response()->json([
                    'message' => translate('The email has already been taken. Please try again!'),
                    'status' => 'error'
                ], 409);
            }
        }
        $validators = Validator::make($request->all(), [
            'name' => 'required|string',
//            'phone' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6'
        ]);

        if($validators->fails()) {
            return response()->json([
                'message' => translate($validators->errors()->first()),
                'status' => 'error'
            ], 401);
        }
//        if(User::where('phone', $request->phone)->first() != null){
//            return response()->json([
//                'message' => translate('The phone has already been taken. Please try again!'),
//                'status' => 'error'
//            ], 409);
//        }

        $user = $this->create($request->all());
        if ($user != null) {
            $tokenResult = $user->createToken('Personal Access Token');
            return $this->loginSuccess($tokenResult, $user);
        }

    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        if(filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)){
            $credentials = request(['email', 'password']);
        }
        else{
            $credentials = ['phone'=> $request->get('email'), 'password'=> $request->get('password')];
        }
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => translate('Unauthorized')], 401);
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        return $this->loginSuccess($tokenResult, $user);
    }

    public function user(Request $request)
    {
        return response()->json([
            'user'=> $request->user(),
            'statusCode' => 200,
            'status' => 'success'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => translate('Successfully logged out')
        ]);
    }

    public function facebook_login(Request $request) {
        $validators = Validator::make($request->all(), [
            'token' => 'required|string'
        ]);
        if($validators->fails()) {
            return response()->json([
                'message' => translate($validators->errors()->first()),
                'status' => 'error'
            ], 401);
        }
        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v10.0/me?fields=name,first_name,last_name,email&access_token='.$request->token);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $res = curl_exec($ch);
        curl_close($ch);
        $user_logged = json_decode($res, true);
        if (User::where('email', $user_logged['email'])->first() != null) {
            $user = User::where('email', $user_logged['email'])->first();
        } else {
            $user = new User([
                'name' => $user_logged['name'],
                'email' => $user_logged['email'],
                'provider_id' => $user_logged['id'],
                'email_verified_at' => Carbon::now()
            ]);
            $user->save();
            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->save();
        }
        $tokenResult = $user->createToken('Personal Access Token');
        return $this->loginSuccess($tokenResult, $user);

    }
    public function google_login(Request $request) {
        $validators = Validator::make($request->all(), [
            'token' => 'required|string'
        ]);

        if($validators->fails()) {
            return response()->json([
                'message' => translate($validators->errors()->first()),
                'status' => 'error'
            ], 401);
        }
        $user_logged = Socialite::driver('google')->userFromToken($request->token);

        if (User::where('email', $user_logged->email)->first() != null) {
            $user = User::where('email', $user_logged->email)->first();
        } else {
            $user = new User([
                'name' => $user_logged->name,
                'email' => $user_logged->email,
                'provider_id' => $user_logged->id,
                'email_verified_at' => Carbon::now()
            ]);
            $user->save();
            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->save();
        }
        $tokenResult = $user->createToken('Personal Access Token');
        return $this->loginSuccess($tokenResult, $user);

    }

    public function login_seller(Request $request, $id) {
        $user = User::findOrFail($id);
            auth()->login($user, true);
            return redirect()->route('dashboard');
    }

    public function socialLogin(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'email' => 'required|string|email'
        ]);

        if($validators->fails()) {
            return response()->json([
                'message' => translate($validators->errors()->first()),
                'status' => 'error'
            ], 401);
        }

        if (User::where('email', $request->email)->first() != null) {
            $user = User::where('email', $request->email)->first();
        } else {
            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'provider_id' => $request->provider,
                'email_verified_at' => Carbon::now()
            ]);
            $user->save();
            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->save();
        }
        $tokenResult = $user->createToken('Personal Access Token');
        return $this->loginSuccess($tokenResult, $user);
    }

    public function apple_login(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'display_name' => 'required|string',
            'user_name' => 'required|string'
        ]);

        if($validators->fails()) {
            return response()->json([
                'message' => translate($validators->errors()->first()),
                'status' => 'error'
            ], 401);
        }

        if (User::where('email', $request->email)->first() != null) {
            $user = User::where('email', $request->email)->first();
        } else {
            $user = new User([
                'name' => $request->user_name,
                'email' => $request->email,
                'email_verified_at' => Carbon::now()
            ]);
            $user->save();
            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->save();
        }
        $tokenResult = $user->createToken('Personal Access Token');
        return $this->loginSuccess($tokenResult, $user);
    }

    protected function loginSuccess($tokenResult, $user)
    {
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(100);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'user' => [
                'id' => $user->id,
                'type' => $user->user_type,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'avatar_original' => $user->avatar_original,
                'address' => $user->address,
                'country'  => $user->country,
                'city' => $user->city,
                'postal_code' => $user->postal_code,
                'phone' => $user->phone
            ],
            'status' => 'success',
            'statusCode' => 200
        ]);
    }
}
