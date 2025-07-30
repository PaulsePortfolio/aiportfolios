<?php
/**
 * Plugin Name: Paul Stephensen Portfolio - Static Integration v4.0
 * Plugin URI: https://paulseportfolio.ai
 * Description: Displays Paul Stephensen's Work-Based PhD research portfolio via secure iframe embedding from Kinsta static hosting. Optimized for performance and reliability.
 * Version: 4.0.0
 * Author: Paul Stephensen
 * Author URI: https://paulseportfolio.ai
 * License: MIT
 * Text Domain: paul-portfolio
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * Network: false
 *
 * @package PaulPortfolio
 * @version 4.0.0
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('PAUL_PORTFOLIO_VERSION', '4.0.0');
define('PAUL_PORTFOLIO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PAUL_PORTFOLIO_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Static site URL - Update this to your Kinsta static site URL
define('PAUL_PORTFOLIO_STATIC_URL', 'https://your-kinsta-site.kinsta.app');

/**
 * Main Plugin Class
 */
class PaulPortfolioPlugin {
    
    private static $instance = null;
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Shortcode registration
        add_shortcode('paul_portfolio', array($this, 'portfolio_shortcode'));
        
        // Gutenberg block registration
        add_action('init', array($this, 'register_gutenberg_block'));
        
        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // AJAX handlers
        add_action('wp_ajax_paul_portfolio_test_connection', array($this, 'ajax_test_connection'));
        add_action('wp_ajax_nopriv_paul_portfolio_test_connection', array($this, 'ajax_test_connection'));
        
        // Plugin activation/deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        load_plugin_textdomain('paul-portfolio', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'paul-portfolio-frontend',
            PAUL_PORTFOLIO_PLUGIN_URL . 'assets/frontend.css',
            array(),
            PAUL_PORTFOLIO_VERSION
        );
        
        wp_enqueue_script(
            'paul-portfolio-frontend',
            PAUL_PORTFOLIO_PLUGIN_URL . 'assets/frontend.js',
            array('jquery'),
            PAUL_PORTFOLIO_VERSION,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('paul-portfolio-frontend', 'paulPortfolio', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('paul_portfolio_nonce'),
            'staticUrl' => PAUL_PORTFOLIO_STATIC_URL
        ));
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'paul-portfolio') === false) {
            return;
        }
        
        wp_enqueue_style(
            'paul-portfolio-admin',
            PAUL_PORTFOLIO_PLUGIN_URL . 'assets/admin.css',
            array(),
            PAUL_PORTFOLIO_VERSION
        );
        
        wp_enqueue_script(
            'paul-portfolio-admin',
            PAUL_PORTFOLIO_PLUGIN_URL . 'assets/admin.js',
            array('jquery'),
            PAUL_PORTFOLIO_VERSION,
            true
        );
    }
    
    /**
     * Portfolio shortcode handler
     */
    public function portfolio_shortcode($atts) {
        $atts = shortcode_atts(array(
            'view' => 'papers',
            'limit' => 6,
            'category' => '',
            'height' => 900,
            'width' => '100%'
        ), $atts, 'paul_portfolio');
        
        // Sanitize attributes
        $validated_atts = $this->sanitize_shortcode_attributes($atts);
        
        if ($validated_atts === false) {
            return $this->render_error_message(__('Invalid shortcode parameters.', 'paul-portfolio'));
        }
        
        // Generate iframe HTML
        return $this->generate_secure_iframe($validated_atts);
    }
    
    /**
     * Sanitize and validate shortcode attributes
     */
    private function sanitize_shortcode_attributes($atts) {
        $valid_views = array('papers', 'full', 'categories');
        $sanitized = array();
        
        // Validate view parameter
        $sanitized['view'] = in_array($atts['view'], $valid_views) ? $atts['view'] : 'papers';
        
        // Validate limit parameter
        $sanitized['limit'] = max(1, min(50, intval($atts['limit'])));
        
        // Validate category parameter
        $sanitized['category'] = sanitize_text_field($atts['category']);
        
        // Validate height parameter
        $sanitized['height'] = max(400, min(2000, intval($atts['height'])));
        
        // Validate width parameter
        $sanitized['width'] = sanitize_text_field($atts['width']);
        if (!preg_match('/^(\d+(%|px)?|100%)$/', $sanitized['width'])) {
            $sanitized['width'] = '100%';
        }
        
        return $sanitized;
    }
    
    /**
     * Generate secure iframe HTML
     */
    private function generate_secure_iframe($atts) {
        $static_url = PAUL_PORTFOLIO_STATIC_URL;
        
        // Build query parameters
        $params = array();
        if ($atts['view'] !== 'papers') {
            $params['view'] = $atts['view'];
        }
        if ($atts['limit'] !== 6) {
            $params['limit'] = $atts['limit'];
        }
        if (!empty($atts['category'])) {
            $params['category'] = $atts['category'];
        }
        
        $iframe_url = !empty($params) ? $static_url . '?' . http_build_query($params) : $static_url;
        
        if (!$this->is_valid_static_url($iframe_url)) {
            return $this->render_error_message(__('Invalid static site URL configuration.', 'paul-portfolio'));
        }
        
        // Generate unique iframe ID
        $iframe_id = 'paul-portfolio-' . uniqid();
        
        $iframe_html = sprintf(
            '<div class="paul-portfolio-container" style="width: %s; max-width: 100%%; margin: 0 auto;">
                <iframe id="%s" 
                        src="%s" 
                        width="100%%" 
                        height="%d" 
                        frameborder="0" 
                        scrolling="auto"
                        sandbox="allow-scripts allow-same-origin allow-popups allow-forms"
                        loading="lazy"
                        title="%s"
                        referrerpolicy="strict-origin-when-cross-origin"
                        style="border: none; background: #f8f9fa; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <p>%s <a href="%s" target="_blank" rel="noopener noreferrer">%s</a></p>
                </iframe>
                <script>
                    (function() {
                        var iframe = document.getElementById("%s");
                        var container = iframe.parentElement;
                        
                        // Enhanced iframe responsiveness
                        window.addEventListener("message", function(event) {
                            if (event.source === iframe.contentWindow) {
                                if (event.data.type === "resize-iframe") {
                                    iframe.style.height = event.data.height + "px";
                                }
                                if (event.data.type === "portfolio-loaded") {
                                    container.classList.add("loaded");
                                    console.log("Portfolio loaded successfully");
                                }
                            }
                        });
                        
                        // Loading state management
                        iframe.addEventListener("load", function() {
                            container.classList.add("iframe-loaded");
                        });
                        
                        // Error handling
                        iframe.addEventListener("error", function() {
                            console.error("Failed to load portfolio iframe");
                            container.innerHTML = "<div class=\"error-message\">%s</div>";
                        });
                    })();
                </script>
            </div>',
            esc_attr($atts['width']),
            esc_attr($iframe_id),
            esc_url($iframe_url),
            esc_attr($atts['height']),
            esc_attr(__('Paul Stephensen Research Portfolio - AIEE Framework Integration', 'paul-portfolio')),
            esc_html(__('Your browser does not support iframes. Please visit', 'paul-portfolio')),
            esc_url($static_url),
            esc_html(__('Paul Stephensen Portfolio', 'paul-portfolio')),
            esc_attr($iframe_id),
            esc_html(__('Unable to load portfolio. Please try refreshing the page.', 'paul-portfolio'))
        );
        
        return $iframe_html;
    }
    
    /**
     * Validate static site URL
     */
    private function is_valid_static_url($url) {
        // Check if URL is valid and uses HTTPS
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        $parsed = parse_url($url);
        return isset($parsed['scheme']) && $parsed['scheme'] === 'https';
    }
    
    /**
     * Render error message
     */
    private function render_error_message($message) {
        return sprintf(
            '<div class="paul-portfolio-error" style="padding: 20px; background: #fee; border: 1px solid #fcc; border-radius: 4px; color: #c66;">
                <strong>%s:</strong> %s
            </div>',
            esc_html(__('Portfolio Error', 'paul-portfolio')),
            esc_html($message)
        );
    }
    
    /**
     * Register Gutenberg block
     */
    public function register_gutenberg_block() {
        if (!function_exists('register_block_type')) {
            return;
        }
        
        wp_register_script(
            'paul-portfolio-block-editor',
            PAUL_PORTFOLIO_PLUGIN_URL . 'assets/block-editor.js',
            array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor'),
            PAUL_PORTFOLIO_VERSION
        );
        
        register_block_type('paul-portfolio/portfolio-block', array(
            'editor_script' => 'paul-portfolio-block-editor',
            'render_callback' => array($this, 'render_gutenberg_block'),
            'attributes' => array(
                'view' => array(
                    'type' => 'string',
                    'default' => 'papers'
                ),
                'limit' => array(
                    'type' => 'number',
                    'default' => 6
                ),
                'category' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'height' => array(
                    'type' => 'number',
                    'default' => 900
                )
            )
        ));
    }
    
    /**
     * Render Gutenberg block
     */
    public function render_gutenberg_block($attributes) {
        return $this->portfolio_shortcode($attributes);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Paul Portfolio Settings', 'paul-portfolio'),
            __('Paul Portfolio', 'paul-portfolio'),
            'manage_options',
            'paul-portfolio-settings',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Admin settings page
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(__('Paul Stephensen Portfolio Settings', 'paul-portfolio')); ?></h1>
            
            <div class="paul-portfolio-admin-content">
                <div class="card">
                    <h2><?php echo esc_html(__('Static Site Configuration', 'paul-portfolio')); ?></h2>
                    <p><?php echo esc_html(__('This plugin displays content from a static site hosted on Kinsta.', 'paul-portfolio')); ?></p>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php echo esc_html(__('Static Site URL', 'paul-portfolio')); ?></th>
                            <td>
                                <code><?php echo esc_html(PAUL_PORTFOLIO_STATIC_URL); ?></code>
                                <p class="description"><?php echo esc_html(__('To change this URL, edit the plugin file and update PAUL_PORTFOLIO_STATIC_URL.', 'paul-portfolio')); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="card">
                    <h2><?php echo esc_html(__('Usage Examples', 'paul-portfolio')); ?></h2>
                    
                    <h3><?php echo esc_html(__('Shortcode Usage', 'paul-portfolio')); ?></h3>
                    <p><strong><?php echo esc_html(__('Basic:', 'paul-portfolio')); ?></strong></p>
                    <code>[paul_portfolio]</code>
                    
                    <p><strong><?php echo esc_html(__('With Options:', 'paul-portfolio')); ?></strong></p>
                    <code>[paul_portfolio view="papers" limit="6" height="800"]</code>
                    
                    <h3><?php echo esc_html(__('Available Parameters', 'paul-portfolio')); ?></h3>
                    <ul>
                        <li><strong>view:</strong> papers, full, categories (default: papers)</li>
                        <li><strong>limit:</strong> 1-50 (default: 6)</li>
                        <li><strong>category:</strong> filter by category (optional)</li>
                        <li><strong>height:</strong> 400-2000px (default: 900)</li>
                    </ul>
                </div>
                
                <div class="card">
                    <h2><?php echo esc_html(__('Connection Test', 'paul-portfolio')); ?></h2>
                    <p><?php echo esc_html(__('Test the connection to the static site:', 'paul-portfolio')); ?></p>
                    
                    <button type="button" id="test-connection" class="button button-primary">
                        <?php echo esc_html(__('Test Connection', 'paul-portfolio')); ?>
                    </button>
                    
                    <div id="test-result" style="margin-top: 15px;"></div>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#test-connection').on('click', function() {
                var button = $(this);
                var result = $('#test-result');
                
                button.prop('disabled', true).text('Testing...');
                result.html('<p>Testing connection to static site...</p>');
                
                // Simple iframe test
                var testFrame = $('<iframe>', {
                    src: '<?php echo esc_js(PAUL_PORTFOLIO_STATIC_URL); ?>',
                    style: 'width: 300px; height: 200px; border: 1px solid #ddd; border-radius: 4px;'
                });
                
                testFrame.on('load', function() {
                    result.html('<div class="notice notice-success"><p><strong>Success!</strong> Static site is accessible and loading correctly.</p></div>');
                    result.append(testFrame);
                    button.prop('disabled', false).text('Test Connection');
                });
                
                testFrame.on('error', function() {
                    result.html('<div class="notice notice-error"><p><strong>Error:</strong> Unable to load static site. Please check the URL configuration.</p></div>');
                    button.prop('disabled', false).text('Test Connection');
                });
                
                // Timeout fallback
                setTimeout(function() {
                    if (button.prop('disabled')) {
                        result.html('<div class="notice notice-warning"><p><strong>Timeout:</strong> Site took longer than expected to load, but may still be working.</p></div>');
                        result.append(testFrame);
                        button.prop('disabled', false).text('Test Connection');
                    }
                }, 10000);
            });
        });
        </script>
        <?php
    }
    
    /**
     * AJAX connection test handler
     */
    public function ajax_test_connection() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'paul_portfolio_nonce')) {
            wp_send_json_error(array(
                'message' => __('Security check failed.', 'paul-portfolio')
            ));
            return;
        }
        
        // Test static site accessibility
        $response = wp_remote_get(PAUL_PORTFOLIO_STATIC_URL, array(
            'timeout' => 10,
            'headers' => array(
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url()
            )
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error(array(
                'message' => sprintf(__('Connection failed: %s', 'paul-portfolio'), $response->get_error_message())
            ));
            return;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code === 200) {
            wp_send_json_success(array(
                'message' => __('Static site is accessible and responding correctly.', 'paul-portfolio'),
                'status_code' => $status_code
            ));
        } else {
            wp_send_json_error(array(
                'message' => sprintf(__('Unexpected response code: %d', 'paul-portfolio'), $status_code)
            ));
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create plugin options
        add_option('paul_portfolio_version', PAUL_PORTFOLIO_VERSION);
        add_option('paul_portfolio_activated', current_time('mysql'));
        
        // Clear any existing cache
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up (but preserve settings for reactivation)
        delete_transient('paul_portfolio_connection_test');
        
        // Clear cache
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
    }
}

// Initialize plugin
add_action('plugins_loaded', function() {
    PaulPortfolioPlugin::getInstance();
});

// Block registration for WordPress 5.8+
add_action('init', function() {
    if (function_exists('register_block_type')) {
        wp_register_script(
            'paul-portfolio-block-script',
            plugin_dir_url(__FILE__) . 'block.js',
            array('wp-blocks', 'wp-element', 'wp-components'),
            '4.0.0'
        );
        
        register_block_type('paul-portfolio/research-portfolio', array(
            'editor_script' => 'paul-portfolio-block-script',
            'render_callback' => function($attributes) {
                $plugin = PaulPortfolioPlugin::getInstance();
                return $plugin->portfolio_shortcode($attributes);
            },
            'attributes' => array(
                'view' => array('type' => 'string', 'default' => 'papers'),
                'limit' => array('type' => 'number', 'default' => 6),
                'category' => array('type' => 'string', 'default' => ''),
                'height' => array('type' => 'number', 'default' => 900)
            )
        ));
    }
});
?>