# WordPress Portfolio Integration Guide

## Current Menu Structure (Perfect Industry Standard)
Based on your screenshot, you have the ideal nested menu structure:

### Main Navigation:
- Professional ePortfolio
- Creative ePortfolio  
- Paul's Avatars
- Reflections
- Contact Us
- Pauls ePortfolio's (dropdown with):
  - Intellectual Property ePortfolio
  - Research Portfolio

## GitHub Repository Setup
Create these 4 repositories on GitHub:

1. **research-portfolio** 
   - Files: portfolio-deploy-research/
   - URL: research.paulseportfolio.ai
   - Menu: Research Portfolio

2. **professional-portfolio**
   - Files: portfolio-deploy-professional/
   - URL: professional.paulseportfolio.ai  
   - Menu: Professional ePortfolio

3. **creative-portfolio**
   - Files: portfolio-deploy-creative/
   - URL: creative.paulseportfolio.ai
   - Menu: Creative ePortfolio

4. **intellectual-property-portfolio**
   - Files: portfolio-deploy-teaching/ (repurpose as IP portfolio)
   - URL: ip.paulseportfolio.ai
   - Menu: Intellectual Property ePortfolio

## WordPress Page Setup
For each portfolio page in WordPress:

1. Create new page (e.g., "Research Portfolio")
2. Add Custom HTML widget
3. Insert iframe code:

```html
<iframe 
  src="https://research.paulseportfolio.ai" 
  width="100%" 
  height="800" 
  frameborder="0" 
  allow="fullscreen"
  loading="lazy"
  style="border: none; border-radius: 8px;">
</iframe>
```

## DNS Configuration
Add these CNAME records in your domain settings:
- research.paulseportfolio.ai → paulstephensen.github.io
- professional.paulseportfolio.ai → paulstephensen.github.io
- creative.paulseportfolio.ai → paulstephensen.github.io
- ip.paulseportfolio.ai → paulstephensen.github.io

## Benefits of This Structure
- SEO optimized with separate URLs
- Professional subdomain structure
- Clean navigation hierarchy
- Independent portfolio updates
- Fast loading (no nested iframes)
- Industry standard approach

This setup eliminates all Kinsta-Replit connectivity issues while maintaining your professional navigation structure.