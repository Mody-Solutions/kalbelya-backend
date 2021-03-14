<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Models\UserAddresses;
use App\Models\UserProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            $token = $user->createToken($this->login_name);
            return $this->send_response(200,
                [
                    'user' => User::Data($user->id),
                    'access_token' => $token->accessToken,
                    'token_type' => 'Bearer',
                ]);
        } else {
            return $this->send_response(200, ['email' => [__('auth.failed')]]);
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
            'firstName' => 'required',
            'lastName' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);

        if ($validator->fails()) {
            $validator_errors = $validator->errors()->getMessages();
            $errors = [
                'Email' => !empty($validator_errors['email']) ? $validator_errors['email'] : null,
                'Nombre' => !empty($validator_errors['firstName']) ? $validator_errors['firstName'] : null,
                'Apellido' => !empty($validator_errors['lastName']) ? $validator_errors['lastName'] : null,
                'Clave' => !empty($validator_errors['password']) ? $validator_errors['password'] : null,
                'Confirmar clave' => !empty($validator_errors['password_confirm']) ?
                    $validator_errors['password_confirm'] :
                    null,
                'Términos y condiciones' => !empty($validator_errors['agree']) ? $validator_errors['agree'] : null,
            ];
            return $this->send_response(200, $errors);
        }

        $input = $request->except('password_confirmation');
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        UserProfile::create(['user_id' => $user->id]);
        UserAddresses::create(['user_id' => $user->id, 'isPrimary' => true]);
        event(new Registered($user));
        $token = $user->createToken($this->login_name)->accessToken;
        $user->img = $this->_img();
        return $this->send_response(200, [
            'message' => "Hemos enviado un mensaje a {$user->email} con un enlace para validar tu correo electrónico",
            'user' => User::Data($user->id),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout()
    {
        $token = auth()->user()->token();
        $token->delete();
        return $this->send_response(201);
    }

    public function token()
    {
        $user = auth()->user();
        $user->img = $this->_img();
        return $this->send_response(201);
    }
}
