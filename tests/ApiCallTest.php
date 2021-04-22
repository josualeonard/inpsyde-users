<?php declare(strict_types=1); // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar

namespace InpsydeTest;

use \PHPUnit\Framework\TestCase;
use \Brain\Monkey;
use \Brain\Monkey\Functions;
use \InpsydeUsers\UsersAPI;

class ApiCallTest extends TestCase
{
    private $api;
    public function setUp(): void
    {
        Monkey\setUp();
        $this->api = new UsersAPI();

        // Mock wp functions
        Functions\when('get_transient')->justReturn(false);
        Functions\when('set_transient')->justReturn(false);
    }

    public function testGetUsers()
    {
        $response = $this->api->users();

        $this->assertEquals(200, $response['code']);
        $this->assertIsArray($response['users']);
        $this->assertGreaterThan(0, count($response['users']));
    }

    public function testGetFirstUser()
    {
        $response = $this->api->users();

        $this->assertEquals(200, $response['code']);
        $this->assertGreaterThan(0, count($response['users']));

        $user = isset($response['users'][0])?$response['users'][0]:false;
        $this->assertIsArray($user);
        $this->assertGreaterThan(0, $user['id']);

        if ($user) {
            $response = $this->api->user($user['id']);
            $this->assertEquals(200, $response['code']);
            $this->assertIsArray($response['user']);
        }
    }

    public function testUserNotFound()
    {
        $response = $this->api->user(999);
        $this->assertNotEquals(200, $response['code']);
        $this->assertArrayNotHasKey('user', $response);
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
