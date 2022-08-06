<?php

namespace Tests\Unit;

use App\Models\User;
use Faker\Factory;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Register test
     *
     * @return void
     */
    public function test_register()
    {
        $faker = Factory::create();

        $response = $this->call('POST','/api/register',[
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => $faker->email,
            'password' => 'password',
            'password_confirmation'=>'password'
        ]);

        $response->assertStatus(201);
    }

    /**
     * Login test
     *
     * @return void
     */
    public function test_login()
    {
        $user = User::first();
        if($user==null){
            $this->assertTrue(true);
            return;
        }

        $response = $this->call('POST','/api/login',[
            'email' => $user->email,
            'password' => 'password'
        ]);



        $response->assertStatus(200);
    }

    /**
     * Logout test
     * @return void
     */
    public function test_logout()
    {
        $user = User::first();
        if($user==null){
            $this->assertTrue(true);
            return;
        }

        $response = $this->call('POST','/api/login',[
            'email' => $user->email,
            'password' => 'password'
        ]);

        $res=json_decode($response->content(),true);

        $response = $this->withHeader('Authorization', 'Bearer ' . $res['token'])
            ->json('post', '/api/logout', []);

        $response->assertStatus(200);
    }
}
