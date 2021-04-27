<?php declare(strict_types=1); // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar

namespace InpsydeUsers;

/**
 * Plugin class
 *
 * Main plugin instance
 */
class Plugin
{
    // Dependencies
    private $settings;
    private $routing;

    /**
     * Constructor
    */
    public function __construct(Settings $settings, Routing $routing)
    {
        // Set dependencies
        $this->settings = $settings;
        $this->routing = $routing;

        add_action('plugins_loaded', [$this, 'init']);
    }

    /**
     * Plugin init
     */
    public function init()
    {
        if (is_admin()) {
            // Load admin settings
            $this->settings->init();

            // Set rewrite rule
            add_action('init', [$this->routing, 'addRule']);
        }
        // Catch custom page URI
        add_filter('parse_request', [$this->routing, 'catchURI']);
    }
}
