# Multi-Portfolio GitHub Deployment Strategy

## Portfolio Structure
Based on your needs, here's how to organize 4 separate portfolios:

### 1. Research Portfolio (Current)
- Repository: `research-portfolio`
- URL: `https://paulstephensen.github.io/research-portfolio`
- Domain: `research.paulseportfolio.ai`
- Content: PhD research, AIEE framework, academic papers

### 2. Professional Portfolio
- Repository: `professional-portfolio` 
- URL: `https://paulstephensen.github.io/professional-portfolio`
- Domain: `professional.paulseportfolio.ai`
- Content: Work experience, skills, industry projects

### 3. Teaching Portfolio
- Repository: `teaching-portfolio`
- URL: `https://paulstephensen.github.io/teaching-portfolio`
- Domain: `teaching.paulseportfolio.ai`
- Content: Educational materials, courses, student feedback

### 4. Creative/Personal Portfolio
- Repository: `creative-portfolio`
- URL: `https://paulstephensen.github.io/creative-portfolio`
- Domain: `creative.paulseportfolio.ai`
- Content: Personal projects, creative work, interests

## WordPress Integration Options

### Option 1: Separate Pages
Each portfolio gets its own WordPress page:
- `/research-portfolio` → iframe to research.paulseportfolio.ai
- `/professional-portfolio` → iframe to professional.paulseportfolio.ai
- `/teaching-portfolio` → iframe to teaching.paulseportfolio.ai
- `/creative-portfolio` → iframe to creative.paulseportfolio.ai

### Option 2: Master Portfolio Page
Single page with tab navigation:
```html
<div class="portfolio-tabs">
  <button onclick="loadPortfolio('research')">Research</button>
  <button onclick="loadPortfolio('professional')">Professional</button>
  <button onclick="loadPortfolio('teaching')">Teaching</button>
  <button onclick="loadPortfolio('creative')">Creative</button>
</div>
<iframe id="portfolio-frame" src="research.paulseportfolio.ai"></iframe>
```

### Option 3: Unified Hub
Main portfolio page that links to each specialized portfolio.

## Implementation Steps

1. **Create 4 GitHub Repositories**
   - Each with its own specialized content
   - Each with GitHub Pages enabled
   - Each with custom domain configured

2. **DNS Configuration**
   Add CNAME records:
   - `research.paulseportfolio.ai` → `paulstephensen.github.io`
   - `professional.paulseportfolio.ai` → `paulstephensen.github.io`
   - `teaching.paulseportfolio.ai` → `paulstephensen.github.io`
   - `creative.paulseportfolio.ai` → `paulstephensen.github.io`

3. **WordPress Menu Structure**
   Update navigation to include all portfolio types

## Benefits
- Clean separation of content types
- Professional subdomains for each portfolio
- Independent updates for each portfolio
- SEO optimization for different audiences
- Easy maintenance and expansion

Would you like me to create templates for each portfolio type or focus on a specific approach?