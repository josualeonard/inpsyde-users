<?php declare(strict_types=1); // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar

namespace InpsydeUsers;

class Plugin
{
    private $name;
    private $slug;
    private $uri;
    private $defaultUri = "my-lovely-users-table";

    public static function init(array $pluginData)
    {
        $class = __CLASS__;
        new $class($pluginData);
    }

    /**
     * Constructor
    *
    * @since  0.0.1
    */
    public function __construct(array $pluginData)
    {
        $this->name = $pluginData['Name'];
        $this->slug = $pluginData['TextDomain'];
        $this->uri = get_site_option('inpsyde_uri');
        $this->permalinkStructure = get_option('permalink_structure');

        add_action('plugins_loaded', [$this, 'pluginActions']);
    }

    /**
     * Do plugin actions here: admin update options, catch matching url
     */
    public function pluginActions()
    {
        if (is_admin()) {
            $submit = filter_input(INPUT_POST, 'submit');
            $optionPage = filter_input(INPUT_POST, 'option_page');
            $page = filter_input(INPUT_GET, 'page');
            if (!function_exists('check_admin_referer')) {
                require_once ABSPATH . WPINC . '/pluggable.php';
            }
            if (!function_exists('add_settings_error')) {
                require_once ABSPATH . 'wp-admin/includes/template.php';
            }
            if ($submit && $optionPage===$this->slug &&
                check_admin_referer('inpsyde_user')
            ) {
                $this->uri = filter_input(INPUT_POST, 'inpsyde_uri');
                update_site_option('inpsyde_uri', $this->uri, '');
                add_settings_error('inpsyde', 0, 'Settings saved!', 'updated');
            }

            add_action('init', [$this, 'addRule']);
            add_action('admin_menu', [$this, 'addMenuPage']);

            if ($page===$this->slug && $this->permalinkStructure==="") {
                add_settings_error(
                    'inpsyde',
                    1,
                    'Please <a href="options-permalink.php" target="_self">enable permalink</a> '.
                    'to make custom URI works.',
                    'error'
                );
            }
        }
        add_filter('parse_request', [$this, 'catchURI']);
    }

    /**
     * Adding rewrite rule for custom URI
     */
    public function addRule()
    {
        $uri = $this->uri?:$this->defaultUri;
        global $wp_rewrite;
        $wp_rewrite->flush_rules(true);
        // This only required to enable htaccess rewrite, otherwise htaccess won't be written
        if ($this->permalinkStructure==="") {
            $permalinkStructure = sanitize_option('permalink_structure', '/%postname%/');
            $wp_rewrite->set_permalink_structure($permalinkStructure);
        }
        $wp_rewrite->add_external_rule($uri.'/?', 'index.php');
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
        if ($this->permalinkStructure==="") {
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
        $uri = $this->uri?:$this->defaultUri;
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

        $utilities = new UsersAPI();
        $id = intval(filter_input(INPUT_GET, 'id'));
        $type = filter_input(INPUT_GET, 'type');
        $uri = $this->uri?:$this->defaultUri;
        if ($this->permalinkStructure==="") {
            $uri = "?".$uri;
        }
        $reqUrl = $this->reqURL();
        $template = 'users.php';
        if ($id>0) {
            $response = $utilities->user($id);
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
        $response = $utilities->users();
        $args = [
            'uri' => $uri,
            'url' => $reqUrl,
            'pluginUrl' => plugin_dir_url(__FILE__).'../../',
            'data' => $response,
        ];
        load_template($views.$template, true, $args);
    }

    /**
     * Add Menu item on WP Backend
     *
     * @uses   add_menu_page
     * @access public
     * @since  0.0.1
     * @return void
     */
    public function addMenuPage()
    {
        add_menu_page(
            esc_html__('Inpsyde Users', 'InpsydeUsers'),
            esc_html__('Inpsyde Users', 'InpsydeUsers'),
            'read',
            'inpsyde-users',
            [$this, 'inpsydeUsersAdminMenu'],
            plugin_dir_url(__FILE__).'../../images/logo.png',
            2
        );
    }

    /**
     * Get InpsydeUsers Admin Menu
     *
     * @uses
     * @access public
     * @since  0.0.1
     * @return void
     */
    public function inpsydeUsersAdminMenu()
    {
        $uri = $this->uri?:$this->defaultUri;
        if ($this->permalinkStructure==="") {
            $uri = "?".$uri;
        }
        ?>
        <div class="wrap">
            <h1><?=esc_html($this->name); ?>
            <a href="<?=esc_html(home_url($uri))?>" 
                class="page-title-action show" target="_blank">Go To Page
                <img src="<?=esc_html(plugin_dir_url(__FILE__)."../../images/external.svg")?>" />
            </a>
            </h1>

            <?=esc_html(settings_errors());?>

            <form action="" method="POST">
                <input type="hidden" name="option_page" value="<?=esc_html($this->slug)?>">
                <input type="hidden" name="action" value="update">
                <?php wp_nonce_field('inpsyde_user'); ?>
                <table class="form-table" role="presentation">
                    <tbody>
                    <tr>
                        <th scope="row"><label for="inpsyde-uri">Custom URI</label></th>
                        <td>
                        <input name="inpsyde_uri" type="text" id="inpsyde-uri" 
                            placeholder="<?=esc_html($this->defaultUri)?>" 
                            value="<?=esc_html($this->uri)?>" class="regular-text">
                        <p><small><em>Default is <code><?=esc_html($this->defaultUri)?></code></em></small></p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            
                <?=esc_html(submit_button('Save Changes'))?>
            </form>

        </div> <!-- .wrap -->
        <?php
    }
}
