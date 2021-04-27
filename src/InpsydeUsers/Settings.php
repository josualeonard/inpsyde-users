<?php declare(strict_types=1); // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar

namespace InpsydeUsers;

/**
 * Settings class
 *
 * Showing admin menu and update settings for this plugin
 */

class Settings
{
    // Properties
    private $name;
    private $page;

    // Dependencies
    private $uri;

    /**
     * Constructor
    */
    public function __construct(array $config, URI $uri)
    {
        // Set properties
        $this->name = isset($config['name'])?$config['name']:'';
        $this->page = isset($config['page'])?$config['page']:'';
        $this->uri = $uri;
    }

    /**
     * Do plugin actions here
     * Add and update plugin setting
     */
    public function init()
    {
        $submit = filter_input(INPUT_POST, 'submit');
        $optionPage = filter_input(INPUT_POST, 'option_page');
        $page = filter_input(INPUT_GET, 'page');
        if (!function_exists('check_admin_referer')) {
            require_once ABSPATH . WPINC . '/pluggable.php';
        }
        if (!function_exists('add_settings_error')) {
            require_once ABSPATH . 'wp-admin/includes/template.php';
        }
        if ($submit && $optionPage===$this->page &&
            check_admin_referer('inpsyde_user')
        ) {
            $this->uri->updateUri(filter_input(INPUT_POST, 'inpsyde_uri'));
            add_settings_error('inpsyde', 0, 'Settings saved!', 'updated');
        }
        add_action('admin_menu', [$this, 'addMenuPage']);

        if ($page===$this->page && !$this->uri->usingPermalink()) {
            add_settings_error(
                'inpsyde',
                1,
                'Please <a href="options-permalink.php" target="_self">enable permalink</a> '.
                'to make custom URI works.',
                'error'
            );
        }
    }
    
    /**
     * Add Menu item on WP Backend
     *
     * @uses   add_menu_page
     * @access public
     * @return void
     */
    public function addMenuPage()
    {
        add_menu_page(
            esc_html__('Inpsyde Users'),
            esc_html__('Inpsyde Users'),
            'read',
            $this->page,
            [$this, 'adminMenu'],
            plugin_dir_url(__FILE__).'../../images/logo.png',
            2
        );
    }

    /**
     * Get InpsydeUsers Admin Menu
     *
     * @uses
     * @access public
     * @return void
     */
    public function adminMenu()
    {
        $uri = $this->uri->uri();
        if (!$this->uri->usingPermalink()) {
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
                <input type="hidden" name="option_page" value="<?=esc_html($this->page)?>">
                <input type="hidden" name="action" value="update">
                <?php wp_nonce_field('inpsyde_user'); ?>
                <table class="form-table" role="presentation">
                    <tbody>
                    <tr>
                        <th scope="row"><label for="inpsyde-uri">Custom URI</label></th>
                        <td>
                        <input name="inpsyde_uri" type="text" id="inpsyde-uri" 
                            placeholder="<?=esc_html($this->uri->default())?>" 
                            value="<?=esc_html($this->uri->uri(true))?>" class="regular-text">
                        <p><small><em>Default is <code><?=esc_html($this->uri->default())?></code></em></small></p>
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
