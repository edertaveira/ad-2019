<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use MongoDB\BSON\ObjectID;

class ClassTestHelper
{
    public static $user_id;
}

class UserTest extends TestCase
{

    public function testCreateUserWithoutParams()
    {
        $user = [];
        $response = $this->json("POST", "/api/user/store", $user);
        $response->assertStatus(401);
        $response->assertJsonStructure(['error']);
    }

    public function testCreateUser()
    {
        $user = [
            "name" => "Fulano",
            "email" => "fulano@test.com"
        ];
        $response = $this->json("POST", "/api/user/store", $user);
        $res = $response->json();
        ClassTestHelper::$user_id = $res["user"]["_id"];
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'user']);
    }

    public function testUpdateUserWithoutParams()
    {
        $user = [];
        $response = $this->json("PUT", "/api/user/" . ClassTestHelper::$user_id, $user);
        $response->assertStatus(401);
        $response->assertJsonStructure(['error']);
    }

    public function testUpdateUser()
    {
        $user = [
            "name" => "Fulano da Silva",
            "email" => "fulanosilva@test.com"
        ];
        $response = $this->json("PUT", "/api/user/" . ClassTestHelper::$user_id, $user);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'user']);
    }

    public function testUpdateNonExistentUser()
    {
        $user = [
            "name" => "Fulano da Silva",
            "email" => "fulanosilva@test.com"
        ];
        $response = $this->json("PUT", "/api/user/12345###", $user);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message']);
    }

    public function testGetAllUsers()
    {
        $response = $this->json('GET', '/api/user');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'users']);
    }

    public function testGetOneUser()
    {
        $response = $this->json('GET', '/api/user/' . ClassTestHelper::$user_id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'user']);
    }

    public function testGetOneNonexistentUser()
    {
        $response = $this->json('GET', '/api/user/12345###');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message']);
    }

    public function testDeleteUser()
    {
        $response = $this->json('DELETE', '/api/user/' . ClassTestHelper::$user_id);
        $response->assertStatus(200);
    }
}
