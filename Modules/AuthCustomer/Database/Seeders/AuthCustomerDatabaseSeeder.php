<?php

namespace Modules\AuthCustomer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class AuthCustomerDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        factory(\Modules\AuthCustomer\Entities\Customer::class, 10)->create();
        // $this->call("OthersTableSeeder");
    }
}
