<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Models\UserAddresses;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    public function read(Request $request){
        $user_id = isset($request->id) ? $request->id : auth()->user()->id;
        $user = User::Data($user_id);
        return $this->send_response(200, $user);
    }

    public function create(Request $request){

    }

    public function update(Request $request){
        $user_id = isset($request->id) ? $request->id : auth()->user()->id;
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
        ]);

        if($validator->fails()){
            $errors = [
                'Correo electrónico' => !empty($validator_errors['email']) ? $validator_errors['email'] : null,
                'Nombre' => !empty($validator_errors['firstName']) ? $validator_errors['firstName'] : null,
                'Apellido' => !empty($validator_errors['lastName']) ? $validator_errors['lastName'] : null,
            ];
            return $this->send_response(200, $errors);
        }

        $user_data = [
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'country' => $request->country,
        ];
        User::where('id', $user_id)
            ->update($user_data);

        $user_profile_data = [
            'title' => $request->title,
            'aboutMe' => $request->aboutMe,
        ];
        UserProfile::where('user_id', $user_id)
            ->update($user_profile_data);

        $user_address_data = [
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'address' => $request->address,
            'address2' => $request->address2,
            'postalCode' => $request->postalCode,
            'isPrimary' => 1,
            'updated_at' => now()
        ];
        UserAddresses::where('user_id', $user_id)
            ->update($user_address_data);
        return $this->send_response(200, ['user' => User::Data($user_id)]);
    }
}
