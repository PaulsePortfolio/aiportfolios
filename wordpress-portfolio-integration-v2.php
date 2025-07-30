<?php
/**
 * Plugin Name: Paul Stephensen Portfolio Integration v2.0
 * Plugin URI: https://academic-portfolio-paulcstephensen.replit.app
 * Description: Enhanced WordPress integration for Paul Stephensen's research portfolio with iframe embedding and Elementor widgets. Version 2.0 with improved .htaccess compatibility.
 * Version: 2.0.0
 * Author: Paul Stephensen
 * Author URI: https://paulseportfolio.ai
 * License: GPL v2 or later
 * Text Domain: paul-portfolio
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PAUL_PORTFOLIO_VERSION', '2.0.0');
define('PAUL_PORTFOLIO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PAUL_PORTFOLIO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PAUL_PORTFOLIO_API_URL', 'https://academic-portfolio-paulcstephensen.replit.app');

/**
 * Main Plugin Class
 */
class PaulPortfolioIntegration {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('elementor/widgets/widgets_registered', array($this, 'register_elementor_widgets'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_shortcode('paul_portfolio', array($this, 'portfolio_shortcode'));
        add_shortcode('paul_portfolio_embed', array($this, 'portfolio_embed_shortcode'));
        
        // Gutenberg block registration
        add_action('init', array($this, 'register_gutenberg_block'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
        
        // WordPress widget registration
        add_action('widgets_init', array($this, 'register_widget'));
        
        // AJAX handlers
        add_action('wp_ajax_paul_portfolio_test_connection', array($this, 'test_connection'));
        add_action('wp_ajax_paul_portfolio_clear_cache', array($this, 'clear_cache'));
        
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Register Gutenberg block
     */
    public function register_gutenberg_block() {
        if (!function_exists('register_block_type')) {
            return;
        }
        
        register_block_type('paul-portfolio/portfolio-block', array(
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
        $atts = array(
            'view' => $attributes['view'],
            'limit' => $attributes['limit'],
            'category' => $attributes['category'],
            'height' => $attributes['height']
        );
        return $this->portfolio_shortcode($atts);
    }
    
    /**
     * Enqueue block editor assets
     */
    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'paul-portfolio-block-editor',
            'data:application/javascript;base64,' . base64_encode($this->get_block_editor_js()),
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
            PAUL_PORTFOLIO_VERSION
        );
    }
    
    /**
     * Get block editor JavaScript
     */
    private function get_block_editor_js() {
        return '
        (function() {
            var el = wp.element.createElement;
            var registerBlockType = wp.blocks.registerBlockType;
            var InspectorControls = wp.blockEditor.InspectorControls;
            var PanelBody = wp.components.PanelBody;
            var SelectControl = wp.components.SelectControl;
            var RangeControl = wp.components.RangeControl;
            var TextControl = wp.components.TextControl;
            
            registerBlockType("paul-portfolio/portfolio-block", {
                title: "Paul\'s Portfolio",
                icon: "portfolio",
                category: "embed",
                attributes: {
                    view: { type: "string", default: "papers" },
                    limit: { type: "number", default: 6 },
                    category: { type: "string", default: "" },
                    height: { type: "number", default: 900 }
                },
                edit: function(props) {
                    var attributes = props.attributes;
                    var setAttributes = props.setAttributes;
                    
                    return [
                        el(InspectorControls, {},
                            el(PanelBody, { title: "Portfolio Settings" },
                                el(SelectControl, {
                                    label: "View Type",
                                    value: attributes.view,
                                    options: [
                                        { label: "Research Papers", value: "papers" },
                                        { label: "Full Portfolio", value: "full" }
                                    ],
                                    onChange: function(value) { setAttributes({ view: value }); }
                                }),
                                el(RangeControl, {
                                    label: "Number of Papers",
                                    value: attributes.limit,
                                    min: 1,
                                    max: 20,
                                    onChange: function(value) { setAttributes({ limit: value }); }
                                }),
                                el(TextControl, {
                                    label: "Category Filter",
                                    value: attributes.category,
                                    onChange: function(value) { setAttributes({ category: value }); }
                                }),
                                el(RangeControl, {
                                    label: "Height (px)",
                                    value: attributes.height,
                                    min: 400,
                                    max: 1200,
                                    onChange: function(value) { setAttributes({ height: value }); }
                                })
                            )
                        ),
                        el("div", { 
                            style: { 
                                border: "2px dashed #ccc", 
                                padding: "20px", 
                                textAlign: "center",
                                background: "linear-gradient(135deg, #2E4BC6 0%, #1E3A8A 100%)",
                                color: "white",
                                borderRadius: "8px"
                            } 
                        },
                            el("h3", { style: { color: "#FCD34D", margin: "0 0 10px 0" } }, "Paul Stephensen\'s Portfolio"),
                            el("p", { style: { margin: "0", opacity: "0.8" } }, 
                                "View: " + attributes.view + " | Papers: " + attributes.limit + 
                                (attributes.category ? " | Category: " + attributes.category : "")
                            )
                        )
                    ];
                },
                save: function() {
                    return null; // Server-side render
                }
            });
        })();
        ';
    }
    
    /**
     * Register WordPress widget
     */
    public function register_widget() {
        register_widget('Paul_Portfolio_Widget');
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Load text domain for translations
        load_plugin_textdomain('paul-portfolio', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Check for .htaccess iframe blocking and show admin notice
        add_action('admin_notices', array($this, 'check_htaccess_iframe_blocking'));
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // Inline CSS for better performance
        wp_add_inline_style('wp-block-library', $this->get_inline_css());
        
        // Inline JavaScript for iframe handling
        wp_add_inline_script('jquery', $this->get_inline_js());
        
    }
    
    /**
     * Get inline CSS for portfolio embeds
     */
    private function get_inline_css() {
        return '
        .paul-portfolio-embed-container {
            position: relative;
            background: #f8f9fa;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
            margin: 20px 0;
        }
        .paul-portfolio-embed-container:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }
        .paul-portfolio-embed-container iframe {
            width: 100%;
            border: none;
            border-radius: 12px;
            background: #fff;
            transition: opacity 0.3s ease;
        }
        .paul-portfolio-embed-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            color: #721c24;
            text-align: center;
        }
        @media (max-width: 768px) {
            .paul-portfolio-embed-container {
                margin: 15px 0;
                border-radius: 8px;
            }
        }
        .paul-portfolio-status.connected {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .paul-portfolio-status.disconnected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        ';
    }
    
    /**
     * Get inline JavaScript for iframe handling
     */
    private function get_inline_js() {
        return '
        jQuery(document).ready(function($) {
            // Handle iframe loading states
            $(".paul-portfolio-embed-container iframe").on("load", function() {
                $(this).parent().removeClass("loading");
            });
            
            // Add loading class initially
            $(".paul-portfolio-embed-container").addClass("loading");
            
            // Remove loading class after timeout
            setTimeout(function() {
                $(".paul-portfolio-embed-container").removeClass("loading");
            }, 5000);
        });
        ';
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'paul-portfolio') !== false) {
            wp_enqueue_style(
                'paul-portfolio-admin-style',
                PAUL_PORTFOLIO_PLUGIN_URL . 'assets/admin-style.css',
                array(),
                PAUL_PORTFOLIO_VERSION
            );
            
            wp_enqueue_script(
                'paul-portfolio-admin-script',
                PAUL_PORTFOLIO_PLUGIN_URL . 'assets/admin-script.js',
                array('jquery'),
                PAUL_PORTFOLIO_VERSION,
                true
            );
        }
    }
    
    /**
     * Register Elementor widgets
     */
    public function register_elementor_widgets() {
        if (class_exists('Elementor\Widget_Base')) {
            require_once PAUL_PORTFOLIO_PLUGIN_PATH . 'widgets/portfolio-widget.php';
            require_once PAUL_PORTFOLIO_PLUGIN_PATH . 'widgets/research-papers-widget.php';
            require_once PAUL_PORTFOLIO_PLUGIN_PATH . 'widgets/aiee-framework-widget.php';
            
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Paul_Portfolio_Widget());
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Paul_Research_Papers_Widget());
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Paul_AIEE_Framework_Widget());
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Paul Portfolio Settings', 'paul-portfolio'),
            __('Paul Portfolio', 'paul-portfolio'),
            'manage_options',
            'paul-portfolio',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Portfolio shortcode for simple embedding
     */
    public function portfolio_shortcode($atts) {
        // Enhanced input validation for WordPress repository standards
        $atts = shortcode_atts(array(
            'view' => 'full',
            'limit' => '12',
            'category' => '',
            'height' => '900',
            'width' => '100%'
        ), $atts, 'paul_portfolio');
        
        // Validate and sanitize all inputs
        $view = in_array($atts['view'], array('full', 'papers')) ? $atts['view'] : 'full';
        $limit = min(max(intval($atts['limit']), 1), 50); // Limit range 1-50
        $category = sanitize_text_field($atts['category']);
        $height = preg_match('/^\d+(px|%)?$/', $atts['height']) ? $atts['height'] : '900px';
        $width = preg_match('/^\d+(px|%)?$/', $atts['width']) ? $atts['width'] : '100%';
        
        // Build secure embed URL
        $embed_url = PAUL_PORTFOLIO_API_URL . '/embed';
        $params = array();
        
        if ($view === 'papers') {
            $params['view'] = 'papers';
        }
        if ($limit !== 12) {
            $params['limit'] = $limit;
        }
        if (!empty($category)) {
            $params['category'] = $category;
        }
        
        if (!empty($params)) {
            $embed_url .= '?' . http_build_query($params);
        }
        
        // Enhanced security: validate URL before output
        if (!wp_http_validate_url($embed_url)) {
            return '<div class="error">Invalid portfolio URL configuration.</div>';
        }
        
        return sprintf(
            '<div class="paul-portfolio-embed-container" style="width: %s; margin: 20px 0;">
                <iframe 
                    src="%s" 
                    width="100%%" 
                    height="%s" 
                    frameborder="0" 
                    style="border: none; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);"
                    loading="lazy"
                    title="Paul Stephensen Research Portfolio"
                    sandbox="allow-scripts allow-same-origin allow-popups allow-forms">
                </iframe>
            </div>',
            esc_attr($width),
            esc_url($embed_url),
            esc_attr($height)
        );
    }
    
    /**
     * Enhanced embed shortcode with fallback
     */
    public function portfolio_embed_shortcode($atts) {
        $atts = shortcode_atts(array(
            'type' => 'dynamic', // 'dynamic' or 'static'
            'view' => 'full',
            'limit' => '12',
            'category' => '',
            'height' => '900',
            'width' => '100%',
            'fallback' => 'true'
        ), $atts, 'paul_portfolio_embed');
        
        $embed_url = PAUL_PORTFOLIO_API_URL . ($atts['type'] === 'static' ? '/embed-static' : '/embed');
        $fallback_url = PAUL_PORTFOLIO_API_URL . '/embed-static';
        
        $params = array();
        if ($atts['view'] === 'papers') {
            $params['view'] = 'papers';
        }
        if (!empty($atts['limit'])) {
            $params['limit'] = intval($atts['limit']);
        }
        if (!empty($atts['category'])) {
            $params['category'] = sanitize_text_field($atts['category']);
        }
        
        if (!empty($params) && $atts['type'] !== 'static') {
            $embed_url .= '?' . http_build_query($params);
        }
        
        $output = '<div class="paul-portfolio-embed-container" style="width: ' . esc_attr($atts['width']) . '; margin: 20px 0;">';
        
        if ($atts['fallback'] === 'true' && $atts['type'] === 'dynamic') {
            $output .= sprintf(
                '<iframe 
                    src="%s" 
                    width="100%%" 
                    height="%s" 
                    frameborder="0" 
                    style="border: none; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);"
                    loading="lazy"
                    title="Paul Stephensen Research Portfolio"
                    onerror="this.src=\'%s\'">
                </iframe>',
                esc_url($embed_url),
                esc_attr($atts['height']),
                esc_url($fallback_url)
            );
        } else {
            $output .= sprintf(
                '<iframe 
                    src="%s" 
                    width="100%%" 
                    height="%s" 
                    frameborder="0" 
                    style="border: none; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);"
                    loading="lazy"
                    title="Paul Stephensen Research Portfolio">
                </iframe>',
                esc_url($embed_url),
                esc_attr($atts['height'])
            );
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Check for .htaccess iframe blocking
     */
    public function check_htaccess_iframe_blocking() {
        $htaccess_path = ABSPATH . '.htaccess';
        
        if (file_exists($htaccess_path)) {
            $htaccess_content = file_get_contents($htaccess_path);
            
            if (strpos($htaccess_content, 'X-Frame-Options') !== false || 
                strpos($htaccess_content, 'frame-ancestors') !== false) {
                
                echo '<div class="notice notice-warning is-dismissible">
                    <p><strong>Paul Portfolio Plugin:</strong> Your .htaccess file may contain iframe blocking rules. If embeds are not working, consider temporarily disabling X-Frame-Options or frame-ancestors rules.</p>
                </div>';
            }
        }
    }
    
    /**
     * Test API connection
     */
    public function test_connection() {
        check_ajax_referer('paul_portfolio_nonce', 'nonce');
        
        $response = wp_remote_get(PAUL_PORTFOLIO_API_URL . '/api/research-papers', array(
            'timeout' => 30,
            'headers' => array(
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url()
            )
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error(array(
                'message' => 'Connection failed: ' . $response->get_error_message()
            ));
        } else {
            $status_code = wp_remote_retrieve_response_code($response);
            if ($status_code === 200) {
                wp_send_json_success(array(
                    'message' => 'Connection successful!',
                    'status' => $status_code
                ));
            } else {
                wp_send_json_error(array(
                    'message' => 'API returned status code: ' . $status_code
                ));
            }
        }
    }
    
    /**
     * Clear plugin cache
     */
    public function clear_cache() {
        check_ajax_referer('paul_portfolio_nonce', 'nonce');
        
        // Clear any transients used by the plugin
        delete_transient('paul_portfolio_research_papers');
        delete_transient('paul_portfolio_api_status');
        
        wp_send_json_success(array(
            'message' => 'Cache cleared successfully!'
        ));
    }
    
    /**
     * Admin page content
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Paul Portfolio Settings', 'paul-portfolio'); ?></h1>
            
            <div class="card">
                <h2><?php _e('Integration Status', 'paul-portfolio'); ?></h2>
                <p><?php _e('Current API URL:', 'paul-portfolio'); ?> <code><?php echo esc_html(PAUL_PORTFOLIO_API_URL); ?></code></p>
                <button type="button" class="button button-primary" id="test-connection">
                    <?php _e('Test Connection', 'paul-portfolio'); ?>
                </button>
                <button type="button" class="button" id="clear-cache">
                    <?php _e('Clear Cache', 'paul-portfolio'); ?>
                </button>
                <div id="connection-result" style="margin-top: 15px;"></div>
            </div>
            
            <div class="card">
                <h2><?php _e('Shortcode Usage', 'paul-portfolio'); ?></h2>
                <h3><?php _e('Basic Portfolio Embed', 'paul-portfolio'); ?></h3>
                <code>[paul_portfolio]</code>
                <p><?php _e('Displays the full portfolio with AIEE Framework and research papers.', 'paul-portfolio'); ?></p>
                
                <h3><?php _e('Research Papers Only', 'paul-portfolio'); ?></h3>
                <code>[paul_portfolio view="papers" limit="6"]</code>
                <p><?php _e('Shows only research papers in a grid layout.', 'paul-portfolio'); ?></p>
                
                <h3><?php _e('Enhanced Embed with Fallback', 'paul-portfolio'); ?></h3>
                <code>[paul_portfolio_embed type="dynamic" fallback="true"]</code>
                <p><?php _e('Uses dynamic content with automatic fallback to static version if needed.', 'paul-portfolio'); ?></p>
                
                <h3><?php _e('Static Embed (Most Reliable)', 'paul-portfolio'); ?></h3>
                <code>[paul_portfolio_embed type="static"]</code>
                <p><?php _e('Uses static content for maximum compatibility.', 'paul-portfolio'); ?></p>
            </div>
            
            <div class="card">
                <h2><?php _e('Embed Parameters', 'paul-portfolio'); ?></h2>
                <ul>
                    <li><strong>view:</strong> "full" (default) or "papers"</li>
                    <li><strong>limit:</strong> Number of papers to show (default: 12)</li>
                    <li><strong>category:</strong> Filter by research category</li>
                    <li><strong>height:</strong> Iframe height in pixels (default: 900)</li>
                    <li><strong>width:</strong> Iframe width (default: 100%)</li>
                    <li><strong>type:</strong> "dynamic" (default) or "static"</li>
                    <li><strong>fallback:</strong> "true" (default) or "false"</li>
                </ul>
            </div>
            
            <div class="card">
                <h2><?php _e('Troubleshooting', 'paul-portfolio'); ?></h2>
                <h3><?php _e('If embeds are not loading:', 'paul-portfolio'); ?></h3>
                <ol>
                    <li><?php _e('Check if .htaccess file contains iframe blocking rules', 'paul-portfolio'); ?></li>
                    <li><?php _e('Test the API connection using the button above', 'paul-portfolio'); ?></li>
                    <li><?php _e('Try using the static embed type', 'paul-portfolio'); ?></li>
                    <li><?php _e('Clear plugin cache and refresh the page', 'paul-portfolio'); ?></li>
                </ol>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#test-connection').on('click', function() {
                var button = $(this);
                var result = $('#connection-result');
                
                button.prop('disabled', true).text('Testing...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'paul_portfolio_test_connection',
                        nonce: '<?php echo wp_create_nonce('paul_portfolio_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            result.html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                        } else {
                            result.html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                        }
                    },
                    error: function() {
                        result.html('<div class="notice notice-error"><p>AJAX request failed</p></div>');
                    },
                    complete: function() {
                        button.prop('disabled', false).text('Test Connection');
                    }
                });
            });
            
            $('#clear-cache').on('click', function() {
                var button = $(this);
                var result = $('#connection-result');
                
                button.prop('disabled', true).text('Clearing...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'paul_portfolio_clear_cache',
                        nonce: '<?php echo wp_create_nonce('paul_portfolio_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            result.html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                        }
                    },
                    complete: function() {
                        button.prop('disabled', false).text('Clear Cache');
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options
        add_option('paul_portfolio_version', PAUL_PORTFOLIO_VERSION);
        
        // Clear any existing cache
        delete_transient('paul_portfolio_research_papers');
        delete_transient('paul_portfolio_api_status');
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clear cache
        delete_transient('paul_portfolio_research_papers');
        delete_transient('paul_portfolio_api_status');
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

/**
 * WordPress Widget Class
 */
class Paul_Portfolio_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'paul_portfolio_widget',
            __('Paul\'s Portfolio', 'paul-portfolio'),
            array('description' => __('Display Paul Stephensen\'s research portfolio', 'paul-portfolio'))
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $portfolio_args = array(
            'view' => !empty($instance['view']) ? $instance['view'] : 'papers',
            'limit' => !empty($instance['limit']) ? intval($instance['limit']) : 6,
            'category' => !empty($instance['category']) ? $instance['category'] : '',
            'height' => !empty($instance['height']) ? intval($instance['height']) : 600
        );
        
        $portfolio = new PaulPortfolioIntegration();
        echo $portfolio->portfolio_shortcode($portfolio_args);
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('My Portfolio', 'paul-portfolio');
        $view = !empty($instance['view']) ? $instance['view'] : 'papers';
        $limit = !empty($instance['limit']) ? $instance['limit'] : 6;
        $category = !empty($instance['category']) ? $instance['category'] : '';
        $height = !empty($instance['height']) ? $instance['height'] : 600;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('view')); ?>"><?php _e('View Type:'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('view')); ?>" name="<?php echo esc_attr($this->get_field_name('view')); ?>">
                <option value="papers" <?php selected($view, 'papers'); ?>>Research Papers</option>
                <option value="full" <?php selected($view, 'full'); ?>>Full Portfolio</option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>"><?php _e('Number of Papers:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('limit')); ?>" name="<?php echo esc_attr($this->get_field_name('limit')); ?>" type="number" min="1" max="20" value="<?php echo esc_attr($limit); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php _e('Category Filter:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('category')); ?>" name="<?php echo esc_attr($this->get_field_name('category')); ?>" type="text" value="<?php echo esc_attr($category); ?>" placeholder="e.g., AI Ethics">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('height')); ?>"><?php _e('Height (px):'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('height')); ?>" name="<?php echo esc_attr($this->get_field_name('height')); ?>" type="number" min="400" max="1200" value="<?php echo esc_attr($height); ?>">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['view'] = (!empty($new_instance['view'])) ? strip_tags($new_instance['view']) : 'papers';
        $instance['limit'] = (!empty($new_instance['limit'])) ? intval($new_instance['limit']) : 6;
        $instance['category'] = (!empty($new_instance['category'])) ? strip_tags($new_instance['category']) : '';
        $instance['height'] = (!empty($new_instance['height'])) ? intval($new_instance['height']) : 600;
        return $instance;
    }
}

// Initialize the plugin
new PaulPortfolioIntegration();

/**
 * Block editor integration
 */
function paul_portfolio_register_blocks() {
    if (function_exists('register_block_type')) {
        register_block_type('paul-portfolio/embed', array(
            'editor_script' => 'paul-portfolio-block-editor',
            'render_callback' => 'paul_portfolio_render_block'
        ));
    }
}
add_action('init', 'paul_portfolio_register_blocks');

function paul_portfolio_render_block($attributes) {
    $atts = wp_parse_args($attributes, array(
        'view' => 'full',
        'limit' => 12,
        'height' => 900,
        'type' => 'dynamic'
    ));
    
    $plugin = new PaulPortfolioIntegration();
    return $plugin->portfolio_embed_shortcode($atts);
}