<?php declare(strict_types=1); // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar

namespace InpsydeTest;

use \PHPUnit\Framework\TestCase;
use \Mockery;
use \Brain\Monkey;
use \Brain\Monkey\Functions;
use \InpsydeUsers\URI;

class URITest extends TestCase
{
    // Properties
    private $mockUri; // URI

    // Depenency
    private $uri; // URI class

    public function setUp(): void
    {
        Monkey\setUp();
        $this->mockUri = 'custom-uri';

        Functions\expect('get_site_option')
            ->once()
            ->with('inpsyde_uri', '')
            ->andReturn($this->mockUri);
        Functions\expect('get_option')
            ->once()
            ->with('permalink_structure')
            ->andReturn('/%postname%/');
        Functions\expect('update_site_option')
            ->once();

        $this->uri = new URI();
    }

    public function testUriUpdated()
    {
        $newUri = $this->mockUri."-custom";
        $this->uri->updateUri($newUri);
        $this->assertEquals($this->uri->uri(), $newUri);
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
