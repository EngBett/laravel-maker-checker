<?php

namespace Tests\Unit;

use Faker\Factory;
use Faker\Generator as Faker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test ability to register an admin without authentication
     *
     * @return void
     */
    public function test_user_register_admin()
    {

        $faker = Factory::create();

        $response = $this->call('POST','/api/users/register-admin',[
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => $faker->email,
            'password' => 'password'
        ]);
        //$response->assertCreated();
        $response->assertStatus(401);
    }

    public function get_users()
    {
        $response = $this->call('GET','/api/users');
        //$response->assertStatus(200);
        $response->assertStatus(401);
    }

    public function get_user_pending_changes()
    {
        $response = $this->call('GET','/api/users/pending-changes');
        //$response->assertStatus(200);
        $response->assertStatus(401);
    }

    public function user_initiate_change()
    {
        $faker = Factory::create();

        $response = $this->call('POST','/users/initiate-change/2',[
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => $faker->email,
        ]);
        //$response->assertStatus(200);
        $response->assertStatus(401);
    }

    public function user_complete_change()
    {
        $faker = Factory::create();

        $response = $this->call('POST','/users/complete-change/2',[
            'approve' => 1]);
        //$response->assertStatus(200);
        $response->assertStatus(401);
    }

}
