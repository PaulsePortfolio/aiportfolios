# WordPress Integration - Production Ready

## Your Production Portfolio URL
**https://academic-portfolio-paulcstephensen.replit.app/**

## Quick Setup Instructions

### Step 1: Upload Plugin Files
Upload all files from the `kinsta-upload-package` folder to your WordPress plugins directory:
- `/wp-content/plugins/paul-stephensen-portfolio/`

### Step 2: Activate Plugin
1. Go to WordPress Admin → Plugins
2. Find "Paul Stephensen Research Portfolio Integration"
3. Click "Activate"

### Step 3: Configure Settings
1. Go to **Settings → Portfolio Integration**
2. Enter API URL: `https://academic-portfolio-paulcstephensen.replit.app`
3. Click "Save Changes"

### Step 4: Add Elementor Widget
1. Edit any page with Elementor
2. Look for "Research Portfolio" widget in Basic elements
3. Drag onto page and configure:
   - Widget Type: Research Papers, Featured Research, Categories, or Full Portfolio
   - Display options: Grid, List, or Carousel
   - Number of items to show
4. Publish page

### Step 5: Test Integration
Your research papers should now load automatically from your live portfolio platform.

## Available Features

### Elementor Widgets
- **Research Papers Widget**: Display your latest research papers
- **Featured Research Widget**: Highlight selected papers
- **Categories Widget**: Show research categories
- **Full Portfolio Widget**: Complete portfolio iframe embed

### Shortcodes
- `[ps_portfolio]` - Full portfolio embed
- `[ps_research_papers limit="5"]` - Recent papers
- `[ps_research_papers category="AI Ethics"]` - Category filtered

### Styling Options
- Responsive grid layouts
- Carousel display modes
- Custom colors and typography
- Animation effects
- Mobile-optimized design

## Troubleshooting

### If Research Papers Don't Load
1. Check API URL in Settings → Portfolio Integration
2. Ensure URL is: `https://academic-portfolio-paulcstephensen.replit.app`
3. Clear browser cache and WordPress cache
4. Check browser console for errors (F12)

### If Iframe Doesn't Display
1. Verify your hosting allows iframe embedding
2. Check Content Security Policy settings
3. Try using shortcode instead of direct embed

## Technical Notes

- The plugin connects to your live research portfolio API
- All data is fetched in real-time from your deployed application
- CORS and security headers are properly configured
- Mobile responsive and accessibility compliant

## Support

Your research portfolio is now production-ready and integrated with WordPress through professional-grade widgets and secure API connections.