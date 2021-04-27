<?php declare(strict_types=1); // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar

namespace InpsydeTest;

use \PHPUnit\Framework\TestCase;
use \Mockery;
use \Brain\Monkey;
use \Brain\Monkey\Functions;
use \InpsydeUsers\Settings;

class SettingsTest extends TestCase
{
    // Dependencies
    private $uri; // URI class mock

    // This class object
    private $settings; // Settings class

    public function setUp(): void
    {
        Monkey\setUp();

        Functions\when('check_admin_referer')->justReturn(true);
        Functions\when('add_settings_error')->justReturn(true);

        $this->uri = Mockery::mock('InpsydeUsers\URI');
        $config = [
            'name' => 'Mock Name',
            'page' => 'mockname',
        ];
        $this->settings = new Settings($config, $this->uri);
    }

    public function testMenuAdded()
    {
        $this->settings->init();
        $this->assertSame(10, has_action('admin_menu', [$this->settings, 'addMenuPage']));
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
