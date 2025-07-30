// Elementor Portfolio Widget JavaScript

jQuery(document).ready(function($) {
    
    // Initialize when Elementor widgets are ready
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/ps_portfolio_widget.default', initPortfolioWidget);
    });
    
    // Also initialize for non-Elementor contexts
    initPortfolioWidgets();
    
    function initPortfolioWidgets() {
        $('.ps-portfolio-elementor-widget').each(function() {
            initPortfolioWidget($(this));
        });
    }
    
    function initPortfolioWidget($scope) {
        const $widget = $scope.find('.ps-portfolio-elementor-widget');
        if (!$widget.length) return;
        
        const widgetType = $widget.data('widget-type');
        
        switch (widgetType) {
            case 'research_papers':
                initResearchPapers($widget);
                break;
            case 'featured_research':
                initFeaturedResearch($widget);
                break;
            case 'categories':
                initCategories($widget);
                break;
            case 'full_portfolio':
                initFullPortfolio($widget);
                break;
        }
    }
    
    function initResearchPapers($widget) {
        const $container = $widget.find('.ps-research-papers');
        if (!$container.length) return;
        
        const settings = {
            limit: $container.data('limit') || 6,
            category: $container.data('category') || '',
            showExcerpts: $container.data('show-excerpts') !== 'no',
            showAuthors: $container.data('show-authors') !== 'no'
        };
        
        loadResearchPapers($container, settings);
        
        // Initialize layout-specific features
        const layoutClass = $container.attr('class').match(/ps-research-(\w+)/);
        if (layoutClass && layoutClass[1] === 'carousel') {
            initCarousel($container);
        }
    }
    
    function initFeaturedResearch($widget) {
        const $container = $widget.find('.ps-featured-research');
        if (!$container.length) return;
        
        const settings = {
            limit: $container.data('limit') || 3,
            category: $container.data('category') || '',
            featured: true
        };
        
        loadResearchPapers($container, settings);
    }
    
    function initCategories($widget) {
        const $container = $widget.find('.ps-research-categories');
        if (!$container.length) return;
        
        loadCategories($container);
    }
    
    function initFullPortfolio($widget) {
        const $iframe = $widget.find('iframe');
        if (!$iframe.length) return;
        
        // Make iframe responsive
        makeIframeResponsive($iframe);
        
        // Setup cross-frame communication
        setupIframeCommunication($iframe);
    }
    
    function loadResearchPapers($container, settings) {
        const apiUrl = getApiUrl();
        if (!apiUrl) {
            showError($container, 'Portfolio API URL not configured');
            return;
        }
        
        // Build API endpoint
        let endpoint = apiUrl + '/api/research-papers';
        const params = new URLSearchParams();
        
        if (settings.category) {
            params.append('category', settings.category);
        }
        if (settings.limit) {
            params.append('limit', settings.limit);
        }
        
        if (params.toString()) {
            endpoint += '?' + params.toString();
        }
        
        $.ajax({
            url: endpoint,
            method: 'GET',
            timeout: 10000,
            success: function(papers) {
                renderResearchPapers($container, papers, settings);
            },
            error: function(xhr, status, error) {
                console.error('Portfolio API Error:', error);
                showError($container, 'Failed to load research papers');
            }
        });
    }
    
    function loadCategories($container) {
        const apiUrl = getApiUrl();
        if (!apiUrl) {
            showError($container, 'Portfolio API URL not configured');
            return;
        }
        
        $.ajax({
            url: apiUrl + '/api/research-papers',
            method: 'GET',
            timeout: 10000,
            success: function(papers) {
                const categories = extractCategories(papers);
                renderCategories($container, categories);
            },
            error: function(xhr, status, error) {
                console.error('Portfolio API Error:', error);
                showError($container, 'Failed to load categories');
            }
        });
    }
    
    function renderResearchPapers($container, papers, settings) {
        if (!papers || papers.length === 0) {
            $container.html('<div class="ps-no-papers">No research papers found.</div>');
            return;
        }
        
        // Apply limit
        if (settings.limit) {
            papers = papers.slice(0, settings.limit);
        }
        
        // Determine layout
        const layoutClass = $container.attr('class').match(/ps-research-(\w+)/);
        const layout = layoutClass ? layoutClass[1] : 'grid';
        
        let html = '';
        
        if (layout === 'carousel') {
            html += '<div class="ps-carousel-track">';
        }
        
        papers.forEach(function(paper, index) {
            html += renderPaperCard(paper, settings, index);
        });
        
        if (layout === 'carousel') {
            html += '</div>';
            html += renderCarouselControls(papers.length);
        }
        
        $container.html(html);
        
        // Initialize layout-specific features
        if (layout === 'carousel') {
            initCarousel($container);
        } else if (layout === 'masonry') {
            initMasonry($container);
        }
        
        // Add animations
        animateCards($container);
    }
    
    function renderPaperCard(paper, settings, index) {
        const showExcerpts = settings.showExcerpts !== false;
        const showAuthors = settings.showAuthors !== false;
        const isFeatured = settings.featured;
        
        let html = '<article class="ps-research-paper" data-index="' + index + '">';
        html += '<div class="ps-paper-content">';
        html += '<div class="ps-paper-header">';
        
        // Title
        html += '<h3 class="ps-paper-title">';
        html += '<a href="' + escapeHtml(paper.pdfUrl || '#') + '" target="_blank">';
        html += escapeHtml(paper.title);
        html += '</a>';
        html += '</h3>';
        
        // Meta information
        html += '<div class="ps-paper-meta">';
        if (paper.category) {
            html += '<span class="ps-paper-category">' + escapeHtml(paper.category) + '</span>';
        }
        if (paper.publishDate) {
            html += '<span class="ps-paper-date">' + escapeHtml(paper.publishDate) + '</span>';
        }
        if (paper.venue) {
            html += '<span class="ps-paper-venue">' + escapeHtml(paper.venue) + '</span>';
        }
        html += '</div>';
        
        html += '</div>'; // Close header
        
        // Excerpt
        if (showExcerpts && paper.description) {
            const excerpt = paper.description.length > 150 
                ? paper.description.substring(0, 150) + '...'
                : paper.description;
            html += '<div class="ps-paper-excerpt">' + escapeHtml(excerpt) + '</div>';
        }
        
        // Authors
        if (showAuthors && paper.authors && paper.authors.length > 0) {
            html += '<div class="ps-paper-authors">';
            html += '<strong>Authors:</strong> ' + escapeHtml(paper.authors.join(', '));
            html += '</div>';
        }
        
        html += '</div>'; // Close content
        html += '</article>';
        
        return html;
    }
    
    function renderCategories($container, categories) {
        if (!categories || categories.length === 0) {
            $container.html('<div class="ps-no-categories">No categories found.</div>');
            return;
        }
        
        let html = '';
        categories.forEach(function(category) {
            html += '<div class="ps-category-item" data-category="' + escapeHtml(category.name) + '">';
            html += '<div class="ps-category-name">' + escapeHtml(category.name) + '</div>';
            html += '<div class="ps-category-count">' + category.count + ' papers</div>';
            html += '</div>';
        });
        
        $container.html(html);
        
        // Add click handlers
        $container.find('.ps-category-item').on('click', function() {
            const category = $(this).data('category');
            filterByCategory(category);
        });
    }
    
    function renderCarouselControls(totalItems) {
        let html = '';
        html += '<button class="ps-carousel-nav ps-prev" aria-label="Previous">‹</button>';
        html += '<button class="ps-carousel-nav ps-next" aria-label="Next">›</button>';
        html += '<div class="ps-carousel-dots">';
        
        const dotsCount = Math.ceil(totalItems / getVisibleItemsCount());
        for (let i = 0; i < dotsCount; i++) {
            html += '<button class="ps-carousel-dot' + (i === 0 ? ' active' : '') + '" data-slide="' + i + '"></button>';
        }
        
        html += '</div>';
        return html;
    }
    
    function initCarousel($container) {
        const $track = $container.find('.ps-carousel-track');
        const $prev = $container.find('.ps-prev');
        const $next = $container.find('.ps-next');
        const $dots = $container.find('.ps-carousel-dot');
        
        let currentSlide = 0;
        const visibleItems = getVisibleItemsCount();
        const totalItems = $track.find('.ps-research-paper').length;
        const maxSlides = Math.ceil(totalItems / visibleItems);
        
        function updateCarousel() {
            const translateX = -(currentSlide * 100);
            $track.css('transform', 'translateX(' + translateX + '%)');
            
            $dots.removeClass('active');
            $dots.eq(currentSlide).addClass('active');
            
            $prev.prop('disabled', currentSlide === 0);
            $next.prop('disabled', currentSlide >= maxSlides - 1);
        }
        
        $prev.on('click', function() {
            if (currentSlide > 0) {
                currentSlide--;
                updateCarousel();
            }
        });
        
        $next.on('click', function() {
            if (currentSlide < maxSlides - 1) {
                currentSlide++;
                updateCarousel();
            }
        });
        
        $dots.on('click', function() {
            currentSlide = $(this).data('slide');
            updateCarousel();
        });
        
        // Auto-play (optional)
        if ($container.data('autoplay')) {
            setInterval(function() {
                if (currentSlide < maxSlides - 1) {
                    currentSlide++;
                } else {
                    currentSlide = 0;
                }
                updateCarousel();
            }, 5000);
        }
        
        updateCarousel();
    }
    
    function initMasonry($container) {
        // Simple masonry implementation
        const $items = $container.find('.ps-research-paper');
        const columnCount = getColumnCount($container);
        
        if (columnCount <= 1) return;
        
        const columns = Array(columnCount).fill(0);
        
        $items.each(function(index) {
            const $item = $(this);
            const shortestColumn = columns.indexOf(Math.min(...columns));
            
            $item.css({
                'order': shortestColumn,
                'margin-bottom': '20px'
            });
            
            // Estimate height (you might want to measure actual height)
            columns[shortestColumn] += 300;
        });
    }
    
    function animateCards($container) {
        const $cards = $container.find('.ps-research-paper');
        
        // Intersection Observer for animation on scroll
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        $(entry.target).addClass('ps-animate-in');
                    }
                });
            }, { threshold: 0.1 });
            
            $cards.each(function() {
                observer.observe(this);
            });
        } else {
            // Fallback for older browsers
            $cards.addClass('ps-animate-in');
        }
    }
    
    function makeIframeResponsive($iframe) {
        function adjustHeight() {
            const containerWidth = $iframe.parent().width();
            if (containerWidth < 768) {
                $iframe.css('height', '600px');
            } else {
                $iframe.css('height', $iframe.data('original-height') || '800px');
            }
        }
        
        adjustHeight();
        $(window).on('resize', adjustHeight);
    }
    
    function setupIframeCommunication($iframe) {
        window.addEventListener('message', function(event) {
            // Verify origin for security
            const apiUrl = getApiUrl();
            if (!apiUrl || event.origin !== apiUrl.replace(/\/$/, '')) {
                return;
            }
            
            const data = event.data;
            
            switch (data.type) {
                case 'height_change':
                    $iframe.css('height', data.height + 'px');
                    break;
                case 'navigation':
                    handleNavigation(data.route);
                    break;
            }
        });
    }
    
    function extractCategories(papers) {
        const categoryMap = {};
        
        papers.forEach(function(paper) {
            if (paper.category) {
                if (categoryMap[paper.category]) {
                    categoryMap[paper.category]++;
                } else {
                    categoryMap[paper.category] = 1;
                }
            }
        });
        
        return Object.keys(categoryMap).map(function(name) {
            return {
                name: name,
                count: categoryMap[name]
            };
        }).sort(function(a, b) {
            return b.count - a.count;
        });
    }
    
    function filterByCategory(category) {
        // Find all research paper widgets and filter them
        $('.ps-research-papers').each(function() {
            const $container = $(this);
            $container.data('category', category);
            
            const settings = {
                limit: $container.data('limit'),
                category: category,
                showExcerpts: $container.data('show-excerpts') !== 'no',
                showAuthors: $container.data('show-authors') !== 'no'
            };
            
            loadResearchPapers($container, settings);
        });
    }
    
    function getVisibleItemsCount() {
        const width = $(window).width();
        if (width < 480) return 1;
        if (width < 768) return 2;
        return 3;
    }
    
    function getColumnCount($container) {
        const classList = $container.attr('class');
        const match = classList.match(/ps-columns-(\d+)/);
        return match ? parseInt(match[1]) : 3;
    }
    
    function getApiUrl() {
        return window.ps_portfolio_config && window.ps_portfolio_config.api_url || '';
    }
    
    function showError($container, message) {
        $container.html('<div class="ps-error">' + escapeHtml(message) + '</div>');
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function handleNavigation(route) {
        // Update browser URL without page reload
        if (history.pushState) {
            const newUrl = window.location.pathname + '#portfolio' + route;
            history.pushState(null, null, newUrl);
        }
    }
    
    // Utility function to refresh all portfolio widgets
    window.refreshPortfolioWidgets = function() {
        $('.ps-portfolio-elementor-widget').each(function() {
            initPortfolioWidget($(this));
        });
    };
    
    // Handle Elementor preview mode
    if (window.elementorFrontend) {
        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
            if ($scope.find('.ps-portfolio-elementor-widget').length) {
                initPortfolioWidget($scope);
            }
        });
    }
    
});