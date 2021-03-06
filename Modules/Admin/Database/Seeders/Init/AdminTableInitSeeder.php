<?php

namespace Modules\Admin\Database\Seeders\Init;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Admin\Entities\Admin;

class AdminTableInitSeeder extends Seeder
{
    public function getData()
    {
        return [
            [
                'uuid' => Str::uuid()->getHex(),
                'name' => env('ROOT'),
                'email' => env('ROOT_EMAIL'),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            ]
        ];
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect($this->getData())->each(function ($item) {
            Admin::firstOrCreate(
                ['email' => $item['email']],
                $item
            );
        });
    }
}
