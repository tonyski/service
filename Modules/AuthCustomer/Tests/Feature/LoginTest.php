<?php

namespace Modules\AuthCustomer\Tests\Feature;

use Modules\AuthCustomer\Entities\Customer;
use Tests\TestCase;

class LoginTest extends TestCase
{
    private static $uriLogin = 'auth/customer/login';
    private static $uriFetch = 'auth/customer/fetchUser';
    private static $uriLogout = 'auth/customer/logout';

    private static $customer;

    public function getCustomer()
    {
        if (!self::$customer) {
            self::$customer = factory(Customer::class)->create();
        }
        return self::$customer;
    }

    public function testLogin()
    {
        $response = $this->postJson(self::$uriLogin, [
            'username' => $this->getCustomer()->email,
            'password' => 'password',
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['customer', 'accessToken', 'tokenType', 'expiresIn']]);

        $token = $response->original['data']['tokenType'] . ' ' . $response->original['data']['accessToken'];
        return $token;
    }

    public function testFetchCurrentUser()
    {
        $this->actingAs($this->getCustomer(), 'customer')
            ->getJson(self::$uriFetch)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['customer']]);
    }

    /**
     * @depends testLogin
     */
    public function testLogout($token)
    {
        $headers = [
            'Authorization' => $token
        ];

        $this->getJson(self::$uriLogout, $headers)
            ->assertSuccessful();

        $this->getCustomer()->delete();
    }
}
