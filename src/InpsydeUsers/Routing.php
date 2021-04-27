<?php declare(strict_types=1); // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar

namespace InpsydeUsers;

/**
 * Routing class
 *
 * Handle frontend routing:
 * Adding rewrite rule
 * Catching page uri
 * Loading data and templates
 */
class Routing
{
    // Dependencies
    private $uri;
    private $api;

    /**
     * Constructor
    */
    public function __construct(Uri $uri, UsersAPI $api)
    {
        $this->uri = $uri;
        $this->api = $api;
    }

    /**
     * Adding rewrite rule for custom URI
     */
    public function addRule()
    {
        global $wp_rewrite;
        $wp_rewrite->flush_rules(true);
        // This only required to enable htaccess rewrite, otherwise htaccess won't be written
        if (!$this->uri->usingPermalink()) {
            $permalinkStructure = sanitize_option('permalink_structure', '/%postname%/');
            $wp_rewrite->set_permalink_structure($permalinkStructure);
        }
        $wp_rewrite->add_external_rule($this->uri->uri().'/?', 'index.php');
        // Flush on admin init, to make sure htaccess is changed
        add_action('admin_init', 'flush_rewrite_rules');
    }

    /**
     * Get complete request URL
     */
    public function reqURL(): string
    {
        $host = parse_url(home_url())['host'];
        $serverReqUri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL, [
            "options" => ["default" => "/"],
        ]);
        $reqUri = trim(strtok($serverReqUri, '?'), '/');
        if (!$this->uri->usingPermalink()) {
            $urlSp = explode("?", $serverReqUri);
            $reqUri = trim($urlSp[1], '/');
            $reqUriSp = explode("&", $reqUri);
            $reqUri = $reqUriSp[0];
        }
        return $host.'/'.$reqUri;
    }

    /**
     * Function to load template when url is match
     */
    public function catchURI()
    {
        $uri = $this->uri->uri();
        $host = parse_url(home_url())['host'];
        $reqUrl = $this->reqURL();
        $currentUrl = $host.'/'.$uri;
        if ($reqUrl===$currentUrl) {
            add_action('template_include', [$this, 'loadTemplate']);
        }
    }

    /**
     * Loading template for custom URI
     */
    public function loadTemplate()
    {
        status_header(200);
        $views = plugin_dir_path(__FILE__).'../../views/';

        $id = intval(filter_input(INPUT_GET, 'id'));
        $type = filter_input(INPUT_GET, 'type');
        $uri = $this->uri->uri();
        if (!$this->uri->usingPermalink()) {
            $uri = "?".$uri;
        }
        $reqUrl = $this->reqURL();
        $template = 'users.php';
        if ($id>0) {
            $response = $this->api->user($id);
            $args = [
                'uri' => $uri,
                'url' => $reqUrl,
                'pluginUrl' => plugin_dir_url(__FILE__).'../../',
                'data' => $response,
            ];
            $template = 'user.php';
            if ($type==='json') {
                header('Content-Type: application/json');
                $template = 'json.php';
            }
            load_template($views.$template, true, $args);
            exit;
        }
        $response = $this->api->users();
        $args = [
            'uri' => $uri,
            'url' => $reqUrl,
            'pluginUrl' => plugin_dir_url(__FILE__).'../../',
            'data' => $response,
        ];
        load_template($views.$template, true, $args);
    }
}
