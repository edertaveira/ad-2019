<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                "name" => "Eder",
                "email" => "edertaveira@gmail.com"
            ], [
                "name" => "Lan",
                "email" => "edertaveira@gmail.com"
            ], [
                "name" => "João Pedro",
                "email" => "edertaveira@gmail.com"
            ], [
                "name" => "Maria Helena",
                "email" => "edertaveira@gmail.com"
            ]
        ];

        foreach ($users as $value) {
            $user = new User();
            $user->name = $value["name"];
            $user->email = $value["email"];
            $user->save();
        }
    }
}
