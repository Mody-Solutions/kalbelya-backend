<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Client;
use Laravel\Passport\RefreshToken;

class AuthController extends BaseController
{
    protected $login_name;

    public function __construct()
    {
        $this->login_name = config('kal.login_name');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($data, !!$request->rememberMe)) {
            $user = auth()->user();
            $user->img = config('kal.profile_img_url');
            $token = $user->createToken($this->login_name);
            return $this->send_response(200,
                [
                    'user' => $user,
                    'access_token' => $token->accessToken,
                    'token_type' => 'Bearer',
                    'login' => true,
                    'permissions' => [],
                ]);
        } else {
            return $this->send_response(401, ['email' => [__('auth.failed')]]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'name' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);

        if ($validator->fails()) {
            $validator_errors = $validator->errors()->getMessages();
            $errors = [
                'Email' => !empty($validator_errors['email']) ? $validator_errors['email'] : null,
                'Clave' => !empty($validator_errors['password']) ? $validator_errors['password'] : null,
                'Confirmar clave' => !empty($validator_errors['password_confirm']) ?
                    $validator_errors['password_confirm'] :
                    null,
                'Términos y condiciones' => !empty($validator_errors['agree']) ? $validator_errors['agree'] : null,
            ];
            return $this->send_response(400, $errors);
        }

        $input = $request->except('password_confirmation');
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        event(new Registered($user));
        auth()->login($user, !!$request->rememberMe);
        $token = $user->createToken($this->login_name)->accessToken;
        return $this->send_response(200, [
            'message' => "Hemos enviado un mensaje a {$user->email} con un enlace para validar tu correo electrónico",
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(){
        $token = auth()->user()->token();
        $token->delete();
        return $this->send_response(201);
    }

    public function token()
    {
        $user = auth()->user();

        return $this->send_response(200, [
            'user' => $user,
            'login' => true,
            'permissions' => [],
        ]);
    }
}
