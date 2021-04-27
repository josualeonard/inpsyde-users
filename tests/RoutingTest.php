<?php declare(strict_types=1); // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar

namespace InpsydeTest;

use \PHPUnit\Framework\TestCase;
use \Mockery;
use \Brain\Monkey;
use \Brain\Monkey\Functions;
use \InpsydeUsers\Routing;

class RoutingTest extends TestCase
{
    // Properties
    private $mockDomain; // Only domain name
    private $mockUri; // URI
    
    // Dependencies
    private $uri; // URI class mock
    private $api; // UsersAPI class mock

    // This class object
    private $routing;

    public function setUp(): void
    {
        Monkey\setUp();
        $this->mockDomain = 'mock.domain';
        $this->mockUri = 'custom-uri';
        $mockUrl = 'http://'.$this->mockDomain.'/'.$this->mockUri;

        Functions\when('home_url')->justReturn($mockUrl.'?query=param');
        Functions\when('sanitize_option')->justReturn(true);
        
        $this->uri = Mockery::mock('InpsydeUsers\URI');
        $this->uri->shouldReceive('uri')->andReturn($this->mockUri);
        $this->uri->shouldReceive('usingPermalink')->andReturn(false);
        $this->api = Mockery::mock('InpsydeUsers\UsersAPI');
        $this->routing = new Routing($this->uri, $this->api);
    }

    public function testUriCaught()
    {
        // Partial mock
        $mock = Mockery::mock(\InpsydeUsers\Routing::class, [$this->uri, $this->api])->makePartial();
        // Assuming requested url is correct
        $mock->shouldReceive('reqURL')->andReturn($this->mockDomain.'/'.$this->mockUri);
        // Do catch matching URI
        $mock->catchURI();
        // Then template should be loaded
        $this->assertSame(10, has_action('template_include', [$mock, 'loadTemplate']));
    }

    public function testRuleAdded()
    {
        // Mock wp_rewrite
        global $wp_rewrite;
        $wp_rewrite = Mockery::mock('wp_rewrite');
        $wp_rewrite
            ->shouldReceive('flush_rules')
            ->once();
        $wp_rewrite
            ->shouldReceive('add_external_rule')
            ->once();
        $wp_rewrite
            ->shouldReceive('set_permalink_structure')
            ->once();
        $this->routing->addRule();
        $this->assertSame(10, has_action('admin_init', 'flush_rewrite_rules'));
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
