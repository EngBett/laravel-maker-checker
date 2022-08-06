<?php

namespace Tests\Unit;

use App\Models\User;
use Faker\Factory;
use Faker\Generator as Faker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     *
     *
     * @return void
     */
    public function test_user_register_admin()
    {

        $user = User::where([['email', 'super.admin@app.dev']])->first();

        if ($user == null) {
            $this->assertTrue(true);
            return;
        }

        $faker = Factory::create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->get_token())->json('POST', '/api/users/register-admin', [
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => $faker->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201);
    }

    /**
     *
     *
     * @return void
     */
    public function test_get_users()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->get_token())->json('GET', '/api/users');
        $response->assertStatus(200);
        $response->assertJsonStructure(["users" => []]);
    }

    /**
     *
     *
     * @return void
     */
    public function test_get_user_pending_changes()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->get_token())->json('GET', '/api/users/pending-changes');
        $response->assertStatus(200);
        $response->assertJsonStructure(["users" => []]);
    }

    /**
     *
     *
     * @return void
     */
    public function test_user_initiate_change_test()
    {
        $faker = Factory::create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->get_token())->json('POST', '/api/users/initiate-change/2', [
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => $faker->email,
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(["message" => []]);
    }

    /**
     * @return void
     */
    public function test_user_complete_change()
    {
        $user = User::where([['is_admin', '=', 1]])->orderBy('id','desc')->first();
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->get_token($user))->json('POST', '/api/users/complete-change/2', [
            'approve' => 1]);

        $response->assertStatus(200);
        $response->assertJsonStructure(["message" => []]);
    }

    /**
     * @param User|null $user
     * @return mixed
     */
    private function get_token(User $user = null)
    {
        $user = $user == null ? User::first() : $user;
        $response = $this->call('POST', '/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);


        $res = json_decode($response->content(), true);
        return $res["token"];
    }

}
