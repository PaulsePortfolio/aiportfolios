<?php
/**
 * Elementor Portfolio Widget for Paul Stephensen Research Portfolio
 */

if (!defined('ABSPATH')) {
    exit;
}

class Elementor_Portfolio_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'ps_portfolio_widget';
    }

    public function get_title() {
        return 'Research Portfolio';
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return ['basic'];
    }

    public function get_script_depends() {
        return ['ps-portfolio-elementor'];
    }

    public function get_style_depends() {
        return ['ps-portfolio-elementor'];
    }

    public function get_keywords() {
        return ['portfolio', 'research', 'academic', 'papers', 'publications'];
    }

    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => 'Portfolio Settings',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'widget_type',
            [
                'label' => 'Display Type',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'research_papers',
                'options' => [
                    'research_papers' => 'Research Papers Grid',
                    'full_portfolio' => 'Full Portfolio Embed',
                    'featured_research' => 'Featured Research',
                    'categories' => 'Research Categories',
                ],
            ]
        );

        $this->add_control(
            'papers_limit',
            [
                'label' => 'Number of Papers',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 50,
                'step' => 1,
                'default' => 6,
                'condition' => [
                    'widget_type' => ['research_papers', 'featured_research'],
                ],
            ]
        );

        $this->add_control(
            'category_filter',
            [
                'label' => 'Category Filter',
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'e.g., AI Ethics, Machine Learning',
                'condition' => [
                    'widget_type' => ['research_papers', 'featured_research'],
                ],
            ]
        );

        $this->add_control(
            'show_excerpts',
            [
                'label' => 'Show Excerpts',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Show',
                'label_off' => 'Hide',
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'widget_type' => ['research_papers', 'featured_research'],
                ],
            ]
        );

        $this->add_control(
            'show_authors',
            [
                'label' => 'Show Authors',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Show',
                'label_off' => 'Hide',
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'widget_type' => ['research_papers', 'featured_research'],
                ],
            ]
        );

        $this->add_control(
            'iframe_height',
            [
                'label' => 'Embed Height',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 400,
                        'max' => 1200,
                        'step' => 50,
                    ],
                    'vh' => [
                        'min' => 50,
                        'max' => 100,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 800,
                ],
                'condition' => [
                    'widget_type' => 'full_portfolio',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => 'Style',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'layout_style',
            [
                'label' => 'Layout Style',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => 'Grid Layout',
                    'masonry' => 'Masonry Layout',
                    'list' => 'List Layout',
                    'carousel' => 'Carousel Layout',
                ],
                'condition' => [
                    'widget_type' => ['research_papers', 'featured_research'],
                ],
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => 'Columns',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'condition' => [
                    'layout_style' => ['grid', 'masonry'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ps-research-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->add_control(
            'card_background',
            [
                'label' => 'Card Background',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ps-research-paper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => 'Title Typography',
                'selector' => '{{WRAPPER}} .ps-paper-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => 'Title Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ps-paper-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_shadow',
                'label' => 'Card Shadow',
                'selector' => '{{WRAPPER}} .ps-research-paper',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ps-research-paper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Animation Section
        $this->start_controls_section(
            'animation_section',
            [
                'label' => 'Animation',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'entrance_animation',
            [
                'label' => 'Entrance Animation',
                'type' => \Elementor\Controls_Manager::ANIMATION,
                'prefix_class' => 'animated ',
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => 'Hover Animation',
                'type' => \Elementor\Controls_Manager::HOVER_ANIMATION,
                'prefix_class' => 'elementor-animation-',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_type = $settings['widget_type'];
        
        echo '<div class="ps-portfolio-elementor-widget" data-widget-type="' . esc_attr($widget_type) . '">';
        
        switch ($widget_type) {
            case 'full_portfolio':
                $this->render_full_portfolio($settings);
                break;
            case 'research_papers':
                $this->render_research_papers($settings);
                break;
            case 'featured_research':
                $this->render_featured_research($settings);
                break;
            case 'categories':
                $this->render_categories($settings);
                break;
        }
        
        echo '</div>';
    }

    private function render_full_portfolio($settings) {
        $api_url = get_option('ps_portfolio_api_url', '');
        $height = $settings['iframe_height']['size'] . $settings['iframe_height']['unit'];
        
        if (empty($api_url)) {
            echo '<div class="ps-error">Please configure your Portfolio API URL in Settings > Portfolio Integration</div>';
            return;
        }
        
        echo '<div class="ps-portfolio-embed">';
        echo '<iframe src="' . esc_url($api_url) . '" width="100%" height="' . esc_attr($height) . '" frameborder="0"></iframe>';
        echo '</div>';
    }

    private function render_research_papers($settings) {
        $layout_class = 'ps-research-' . $settings['layout_style'];
        $columns_class = 'ps-columns-' . $settings['columns'];
        
        echo '<div class="ps-research-papers ' . esc_attr($layout_class) . ' ' . esc_attr($columns_class) . '" 
                   data-limit="' . esc_attr($settings['papers_limit']) . '"
                   data-category="' . esc_attr($settings['category_filter']) . '"
                   data-show-excerpts="' . esc_attr($settings['show_excerpts']) . '"
                   data-show-authors="' . esc_attr($settings['show_authors']) . '"
                   data-elementor="true">';
        
        echo '<div class="ps-loading">Loading research papers...</div>';
        
        echo '</div>';
    }

    private function render_featured_research($settings) {
        echo '<div class="ps-featured-research" 
                   data-limit="' . esc_attr($settings['papers_limit']) . '"
                   data-category="' . esc_attr($settings['category_filter']) . '"
                   data-elementor="true">';
        
        echo '<div class="ps-loading">Loading featured research...</div>';
        
        echo '</div>';
    }

    private function render_categories($settings) {
        echo '<div class="ps-research-categories" data-elementor="true">';
        echo '<div class="ps-loading">Loading research categories...</div>';
        echo '</div>';
    }

    protected function content_template() {
        ?>
        <# 
        var widgetType = settings.widget_type;
        var iframeHeight = settings.iframe_height.size + settings.iframe_height.unit;
        #>
        
        <div class="ps-portfolio-elementor-widget" data-widget-type="{{ widgetType }}">
            <# if ( widgetType === 'full_portfolio' ) { #>
                <div class="ps-portfolio-embed">
                    <div style="background: #f0f0f0; height: {{ iframeHeight }}; display: flex; align-items: center; justify-content: center; border: 1px dashed #ccc;">
                        <span>Portfolio Embed Preview ({{ iframeHeight }})</span>
                    </div>
                </div>
            <# } else if ( widgetType === 'research_papers' ) { #>
                <div class="ps-research-papers ps-research-{{ settings.layout_style }} ps-columns-{{ settings.columns }}">
                    <div style="background: #f9f9f9; padding: 20px; border: 1px dashed #ddd; text-align: center;">
                        Research Papers Grid Preview<br>
                        <small>Limit: {{ settings.papers_limit }} | Layout: {{ settings.layout_style }}</small>
                    </div>
                </div>
            <# } else if ( widgetType === 'featured_research' ) { #>
                <div class="ps-featured-research">
                    <div style="background: #f0f8ff; padding: 20px; border: 1px dashed #0073aa; text-align: center;">
                        Featured Research Preview<br>
                        <small>Limit: {{ settings.papers_limit }}</small>
                    </div>
                </div>
            <# } else { #>
                <div class="ps-research-categories">
                    <div style="background: #fff3cd; padding: 20px; border: 1px dashed #856404; text-align: center;">
                        Research Categories Preview
                    </div>
                </div>
            <# } #>
        </div>
        <?php
    }
}

// Register the widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elementor_Portfolio_Widget());