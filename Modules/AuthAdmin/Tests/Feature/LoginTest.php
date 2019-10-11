<?php

namespace Modules\AuthAdmin\Tests\Feature;

use Modules\Admin\Entities\Admin;
use Tests\TestCase;

class LoginTest extends TestCase
{
    private static $uriLogin = 'auth/admin/login';
    private static $uriFetch = 'auth/admin/fetchUser';
    private static $uriLogout = 'auth/admin/logout';

    private static $admin;

    public function getAdmin()
    {
        if (!self::$admin) {
            self::$admin = factory(Admin::class)->create();
        }
        return self::$admin;
    }

    public function testLogin()
    {
        $response = $this->postJson(self::$uriLogin, [
            'username' => $this->getAdmin()->email,
            'password' => 'password',
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['admin', 'accessToken', 'tokenType', 'expiresIn']]);

        $token = $response->original['data']['tokenType'] . ' ' . $response->original['data']['accessToken'];
        return $token;
    }

    public function testFetchCurrentUser()
    {
        $this->actingAs($this->getAdmin(), 'admin')
            ->getJson(self::$uriFetch)
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['admin']]);
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

        $this->getAdmin()->delete();
    }
}
