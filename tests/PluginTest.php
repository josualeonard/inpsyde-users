<?php declare(strict_types=1); // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar

namespace InpsydeTest;

use \PHPUnit\Framework\TestCase;
use \Mockery;
use \Brain\Monkey;
use \Brain\Monkey\Functions;
use \InpsydeUsers\Plugin;

class PluginTest extends TestCase
{
    private $plugin; // Plugin class object
    private $pluginData; // Plugin data for init
    private $mockDomain; // Only domain name
    private $mockUri; // URI
    private $mockUrl; // Full URL

    public function setUp(): void
    {
        Monkey\setUp();
        $this->mockDomain = 'mock.domain';
        $this->mockUri = 'custom-uri';
        $this->mockUrl = 'http://'.$this->mockDomain.'/'.$this->mockUri;

        Functions\when('get_site_option')->justReturn($this->mockUri);
        Functions\when('get_option')->justReturn('/%postname%/');
        Functions\when('home_url')->justReturn($this->mockUrl.'?query=param');
        Functions\when('get_option')->justReturn(true);
        $this->pluginData = [
            'Name' => 'MockName',
            'TextDomain' => 'mockname',
        ];
        $this->plugin = new Plugin($this->pluginData);
    }

    public function testPluginConstructed()
    {
        // reqURL is returning string
        $this->assertIsString($this->plugin->reqURL());
    }

    public function testURICatched()
    {
        // Partial mock
        $mock = Mockery::mock(\InpsydeUsers\Plugin::class, [$this->pluginData])->makePartial();
        // Assuming requested url is correct
        $mock->shouldReceive('reqURL')->andReturn($this->mockDomain.'/'.$this->mockUri);
        // Do catch matching URI
        $mock->catchURI();
        // Then template should be loaded
        $this->assertSame(10, has_action('template_include', [$mock, 'loadTemplate']));
    }

    public function testHooksAdded()
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
        $this->plugin->addRule();
        $this->assertSame(10, has_action('admin_init', 'flush_rewrite_rules'));
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
