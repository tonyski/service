<?php

namespace Modules\AuthCustomer\Tests\Feature;

use Modules\AuthCustomer\Entities\Customer;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    private static $uri = 'auth/customer/register';

    private static $customer;

    //重用基境代码，不是重用基境
    protected function setUp(): void
    {
        parent::setUp();

        if (!self::$customer){
            self::$customer = [
                'name' => $this->faker()->unique()->userName,
                'email' => $this->faker()->unique()->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ];
        }
    }

    public function testRegister()
    {
        $this->postJson(self::$uri, self::$customer)
            ->assertSuccessful()
            ->assertJsonFragment(['status' => 'success'])
            ->assertJsonStructure(['data' => ['customer']]);
    }

    /**
     * @depends testRegister
     */
    public function testCanNotRegisterWithExistingNameOrEmail()
    {
        $this->postJson(self::$uri, self::$customer)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);

        Customer::where('email', self::$customer['email'])->delete();
    }
}
