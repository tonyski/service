<?php

namespace Modules\Customer\Http\Controllers\Auth;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Http\Controllers\Controller;
use Modules\Customer\Http\Requests\Auth\RegisterRequest;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = $this->create($request->all());

//        event(new Registered($user));

        $this->guard()->login($user);

        return $this->registered();
    }

    protected function registered()
    {
        $token = $this->guard()->getToken()->get();
        $expiration = $this->guard()->getPayload()->get('exp');
        $customer = $this->guard()->user();

        $data = [
            'accessToken' => $token,
            'tokenType' => 'Bearer',
            'expiresIn' => $expiration - time(),
            'customer' => $customer,
        ];

        return $this->createSuccess($data);
    }

    protected function create(array $data)
    {
        return Customer::create([
            'uuid' => Str::uuid()->getHex(),
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    protected function guard()
    {
        return Auth::guard('customer');
    }
}
