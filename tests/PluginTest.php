<?php declare(strict_types=1); // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar

namespace InpsydeTest;

use \PHPUnit\Framework\TestCase;
use \Mockery;
use \Brain\Monkey;
use \Brain\Monkey\Functions;
use \InpsydeUsers\Plugin;

class PluginTest extends TestCase
{
    // Dependencies
    private $settings; // Settings class mock
    private $routing; // Routing class mock
    
    // This class object
    private $plugin;

    public function setUp(): void
    {
        Monkey\setUp();

        // Skip cecking inside admin conditional
        Functions\when('is_admin')->justReturn(false);
        
        $this->settings = Mockery::mock('InpsydeUsers\Settings');
        $this->routing = Mockery::mock('InpsydeUsers\Routing');

        $this->plugin = new Plugin($this->settings, $this->routing);
    }

    public function testPluginIsConstructed()
    {
        $this->assertSame(10, has_action('plugins_loaded', [$this->plugin, 'init']));
    }

    public function testPluginInit()
    {
        $this->plugin->init();
        $this->assertSame(10, has_filter('parse_request', [$this->routing, 'catchURI']));
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
