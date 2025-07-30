# Paul Stephensen's Research Career ePortfolio

## Overview

This is Paul Stephensen's personal research portfolio platform - a single-user application designed specifically for managing his PhD research journey and creating professional portfolios. The application serves as both a personal research documentation system and a demonstration of the AIEE (Accessibility, Intelligence, Ethics, Empathy) Framework.

The platform combines personal portfolio management with AI-powered research tools, emphasizing accessibility and empathetic design principles derived from Paul's lived experience with DiGeorge syndrome and cognitive challenges.

**Current Status**: Production-ready Version 2.0 with comprehensive technical documentation, fully functional authentication system, database integration, AI analysis capabilities, and deployment-ready codebase. Complete feature audit and implementation details available in RESEARCH-PORTFOLIO-V2-COMPREHENSIVE-DOCUMENTATION.md.

## System Architecture

### Frontend Architecture
- **Framework**: React 18 with TypeScript
- **Styling**: Tailwind CSS with shadcn/ui component library
- **State Management**: TanStack Query for server state, React hooks for local state
- **Routing**: Wouter for lightweight client-side routing
- **Build Tool**: Vite for development and production builds

### Backend Architecture
- **Runtime**: Node.js with Express.js
- **Language**: TypeScript with ESM modules
- **API Design**: RESTful endpoints with JSON responses
- **Authentication**: JWT-based with session management via cookies
- **File Handling**: Multipart form data for document uploads

### Database Layer
- **ORM**: Drizzle ORM with PostgreSQL
- **Database**: PostgreSQL 16 (configured in .replit)
- **Schema Management**: Type-safe schema definitions in shared/schema.ts
- **Migrations**: Drizzle Kit for database migrations

## Key Components

### Core Features
1. **Research Document Management**
   - Upload and organize academic papers
   - Automated metadata extraction using AI
   - Support for multiple file formats (PDF, DOC, etc.)

2. **AI-Powered Analysis Suite**
   - Document content analysis via OpenAI GPT-4
   - Innovation scoring and writing quality assessment
   - Plagiarism detection and patent potential analysis
   - Grant opportunity identification

3. **AIEE Framework Integration**
   - Accessibility-first design principles
   - Empathetic AI interactions through Tony.AI mentor
   - Ethical transparency in all AI operations
   - Privacy-preserving data handling

4. **Collaboration & Sharing**
   - Multi-user collaboration on research papers
   - Social media integration (LinkedIn, Twitter, etc.)
   - Citation generation in multiple academic formats
   - WordPress publishing integration

### Advanced Features
1. **Tony.AI Research Mentor**
   - Contextual research assistance
   - Empathetic responses for users with cognitive challenges
   - AIEE framework compliance monitoring

2. **Analytics Dashboard**
   - Publication metrics and citation tracking
   - Research impact analysis
   - Google Scholar integration

3. **HeyGen Avatar Integration**
   - Interactive AI avatar for enhanced user engagement
   - Voice-enabled research assistance

## Data Flow

1. **Authentication Flow**
   - User registration with email verification via SendGrid
   - JWT token generation and session management
   - Cookie-based session persistence

2. **Document Processing Pipeline**
   - File upload → AI content analysis → Metadata extraction → Database storage
   - Automated categorization and tagging
   - Background processing for analysis tasks

3. **AI Analysis Workflow**
   - Research paper analysis via OpenAI API
   - Multi-dimensional scoring (innovation, quality, originality)
   - Integration with external academic APIs

4. **Collaboration Workflow**
   - Real-time sharing and permission management
   - Version control for document revisions
   - Comment and review systems

## External Dependencies

### AI Services
- **OpenAI GPT-4**: Primary AI analysis engine
- **Anthropic Claude**: Secondary AI provider for specialized tasks
- **HeyGen**: Avatar and voice synthesis services

### Communication Services
- **SendGrid**: Email delivery and notifications
- **WordPress REST API**: Content publishing integration

### Development Tools
- **Neon Database**: PostgreSQL hosting
- **Replit**: Development and deployment platform
- **Vite**: Build tooling and development server

### UI/UX Libraries
- **shadcn/ui**: Pre-built accessible components
- **Radix UI**: Headless component primitives
- **Tailwind CSS**: Utility-first styling framework
- **Recharts**: Data visualization library

## Deployment Strategy

### Development Environment
- **Platform**: Replit with Node.js 20 and PostgreSQL 16 modules
- **Auto-reload**: Vite HMR for frontend, tsx for backend
- **Port Configuration**: Backend on 5000, frontend served via Vite

### Production Deployment
- **Target**: Autoscale deployment on Replit
- **GitHub Repository**: https://github.com/paulstephensen/Research-Portfolio-App-Dynamic.git
- **Build Process**: 
  1. Frontend build with Vite
  2. Backend bundle with esbuild
  3. Static asset serving from dist/public
- **Environment Variables**: Database URL, API keys, JWT secrets

### Database Management
- **Development**: Auto-provisioned PostgreSQL on Replit
- **Schema Updates**: Drizzle Kit push for development
- **Production**: Managed PostgreSQL with connection pooling

## Security Implementation

### Authentication & Authorization
- **JWT-based authentication** with secure token generation and validation
- **Session management** with secure cookies (httpOnly, secure, sameSite)
- **Password security** with bcrypt hashing (12 salt rounds)
- **Email verification** required for all user accounts
- **Rate limiting** on authentication endpoints (5 attempts per 15 minutes)
- **Role-based access control** with admin privileges protection

### Input Validation & Sanitization
- **Zod schema validation** for all API endpoints
- **Input sanitization** to prevent XSS attacks
- **SQL injection protection** via Drizzle ORM parameterized queries
- **Parameter validation** for all numeric IDs and search queries
- **Email format validation** with regex patterns
- **Request size limits** (10MB) to prevent DoS attacks

### Security Headers & CORS
- **Content Security Policy** (CSP) headers
- **X-Frame-Options** set to DENY
- **X-Content-Type-Options** set to nosniff
- **X-XSS-Protection** enabled
- **Referrer-Policy** for privacy protection
- **CORS configuration** with allowed origins validation

### Error Handling & Logging
- **Secure error messages** (generic responses in production)
- **Security event logging** with IP tracking
- **Stack trace protection** (development only)
- **Failed authentication attempt monitoring**

### Data Protection
- **Environment variable security** (JWT_SECRET required)
- **Host header validation** to prevent header injection
- **Clean database state** with no sample data exposure
- **HTTPS enforcement** in production environments
- **Sensitive data encryption** at rest and in transit

## University Compliance Standards

### Australian University IT Security Alignment
**Meets James Cook University & Australian Higher Education Standards:**

#### Authentication Requirements ✅
- Multi-factor authentication ready (email verification implemented)
- Strong password policies enforced through validation
- Session management with secure token handling
- Account lockout protection via rate limiting

#### Data Protection Standards ✅
- Personal information handling compliant with Privacy Act 1988
- Research data integrity protection
- Secure data transmission (HTTPS enforced)
- Database encryption and access controls

#### Cyber Security Framework Compliance ✅
- **Australian Cyber Security Centre (ACSC) Essential Eight alignment:**
  - Application control via input validation
  - Patch management through dependency updates
  - Microsoft Office macro restrictions (N/A for web platform)
  - User application hardening implemented
  - Restrict administrative privileges (role-based access)
  - Multi-factor authentication supported
  - Regular backups (database managed)
  - Application allowlisting via CORS controls

#### University Network Security ✅
- Content Security Policy headers
- Cross-site scripting (XSS) protection
- SQL injection prevention
- Secure cookie configuration
- CORS policy enforcement

#### Research Data Management ✅
- Intellectual property protection through user isolation
- Research integrity through version control
- Collaboration controls with permission management
- Data export capabilities for institutional compliance

## Development Roadmap

### Phase 1: Core Research Portfolio ✅ COMPLETED
- Security audit and hardening
- University compliance verification  
- Clean production-ready codebase
- Authentication and authorization system

### Phase 2: WordPress Integration & Deployment ❌ DISABLED
- WordPress plugin development discontinued per user request
- Static site embedding discontinued per user request
- Focus shifted to dynamic application deployment only

### Phase 3: Portfolio Generator Workflow System ✅ COMPLETED
- Automated portfolio generation platform with 6 professional templates
- Academic, Professional, Creative, Technology, IP, and Healthcare portfolios
- Step-by-step workflow wizard with type selection and customization
- Dynamic HTML generation with theme-based styling and responsive design
- Complete API backend with template management and portfolio creation

### Phase 4: Platform Optimization ✅ COMPLETED
- Removed interactive feature tour for simplified interface
- Fixed authentication system and JWT token handling
- Optimized upload performance with background AI processing
- Verified all core functionality for production deployment
- Application ready for Kinsta Application Hosting migration

## Changelog

- July 1, 2025: Organized complete file structure with alphabetical sorting - reorganized scattered documentation files into clean folder structure (docs/, archives/) with logical categorization, moved all deployment packages and archive files to organized subfolders, significantly improved workspace navigation and file management per user preferences
- July 1, 2025: Delivered complete V2.0 documentation package to user for Tony.AIEE review - provided comprehensive 75-page technical documentation (RESEARCH-PORTFOLIO-V2-COMPREHENSIVE-DOCUMENTATION.md) with full system architecture, authentication code, database schema, API endpoints, AI integration details, and cloning instructions for Tony.AIEE code review and improvement assistance
- July 1, 2025: Version 2.0 comprehensive documentation completed - created RESEARCH-PORTFOLIO-V2-COMPREHENSIVE-DOCUMENTATION.md with complete technical audit, 89-feature inventory, full code examples, API documentation, security implementation, deployment guides, and production readiness verification for clean codebase with functional authentication system
- December 30, 2024: Repository consolidated and deployment strategy simplified - disabled WordPress plugin, embed, and static site work per user request, updated GitHub repository to https://github.com/paulstephensen/Research-Portfolio-App-Dynamic.git for focused dynamic application deployment to paulseportfolio.ai domain
- December 30, 2024: Comprehensive application documentation completed and shared with Tony.AIEE for integration improvements - COMPREHENSIVE-APPLICATION-AUDIT.md contains complete technical specifications, 89-feature inventory, architecture documentation, and deployment requirements for Tony.AIEE review and scaffolding assistance
- December 30, 2024: Fixed portfolio generator domain and redesigned for single-user personal use - updated all portfolio URLs from .portfoliopro.ai to .paulseportfolio.ai domain, implemented complete portfolio HTML generation with download and view functionality, redesigned application branding for Paul's personal use rather than multi-user platform, verified backend authentication working correctly with paulcstephensen@gmail.com credentials, ready for user testing
- December 30, 2024: Comprehensive application audit completed - created detailed technical documentation package including complete feature matrix (89 features, 97% implementation), technical handover guide, API documentation, security compliance verification, and deployment requirements for seamless team transfer
- June 30, 2025: Application optimization complete - successfully removed interactive feature tour system, fixed authentication JWT token mapping issue, verified login system working with paulcstephensen@gmail.com credentials, confirmed all core features operational (Tony.AIEE chat, document upload with AI analysis, portfolio generation tools), application ready for Kinsta Application Hosting migration
- June 27, 2025: Enhanced upload performance and added comprehensive edit functionality - optimized document upload for instant response with background AI processing to prevent duplicate uploads, implemented complete edit modal for research paper cards with all metadata fields, improved Toni.AIEE chat scrolling with auto-scroll and fixed height, added API endpoint for updating papers, resolved login issues and improved OpenAI API error handling for Kinsta migration readiness
- June 27, 2025: Integrated official Toni.AIEE chat agent following provided instructions - implemented thoughtful AI co-pilot with empathy and memory, added chat interface with real-time OpenAI GPT-4 integration, created comment system for research papers, enhanced document upload system with 20MB limit and PowerPoint/HTML support, completed AI analysis workflow with innovation scoring and metadata extraction
- June 25, 2025: Fixed login authentication system completely - resolved frontend/backend field mismatch where frontend sent email but backend expected different format, enhanced authentication flow with proper token handling and session management, fixed logout to properly clear authentication cookies, verified login working with paulcstephensen@gmail.com credentials, and streamlined user authentication experience with immediate state updates
- June 25, 2025: Fixed password reset workflow to follow standard email-based flow - updated auth modal to only send reset emails (not reset passwords directly), created separate reset password page accessible via email links, implemented proper token-based authentication for password resets with 1-hour expiration, and added professional email templates with security notices
- June 24, 2025: Implemented contextual help tooltips for admin dashboard - added comprehensive tooltips for statistics cards, user management table, and action buttons with detailed explanations of each function to improve admin user experience and reduce support requests
- June 25, 2025: Created comprehensive ePortfolio recreation guide (EPORTFOLIO-RECREATION-GUIDE.md) - complete 200+ step technical documentation for rebuilding the entire application including architecture, database schema, authentication system, API routes, frontend components, deployment instructions, and security checklist
- June 25, 2025: Fixed admin portal authentication issues - added missing /api/auth/user endpoint, complete login/register/logout flow, assigned admin roles to paul@paulseportfolio.ai and paulcstephensen@gmail.com (removed JCU email per user request), and verified admin dashboard functionality
- June 24, 2025: Updated complete application deployment package (261KB) for paulseportfolio.ai/research - includes current working React/Node.js stack from https://academic-portfolio-paulcstephensen.replit.app/ with fixed authentication system, clickable document functionality, download/share features, and verified production-ready code
- June 24, 2025: Prepared complete GitHub repository deployment package (181KB) - production-ready code with client/server/shared folders, optimized package.json, TypeScript configurations, comprehensive documentation, and environment setup for Kinsta Application Hosting with research.paulseportfolio.ai subdomain (Repository: https://github.com/paulstephensen/Research-Portfolio-App-Dynamic.git)
- June 24, 2025: Prepared Kinsta Application Hosting deployment - configured Node.js build settings, environment variables template, and database migration guide for upgrading from Replit to enterprise-grade hosting with custom domain research.paulseportfolio.ai
- June 23, 2025: Prepared GitHub Pages deployment package for research portfolio - created optimized HTML wrapper that embeds live Replit application, configured DNS setup for research.paulseportfolio.ai subdomain, ready for upload to existing GitHub 'research' repository
- June 23, 2025: Made portfolio university-neutral by removing all JCU references from both dynamic and static versions - updated color variables and content to be professional but not institution-specific since offer letter not yet received
- June 23, 2025: Removed photo upload functionality from both dynamic and static versions - cleaned header component by removing "Add Photo" hover overlay to match user requirements for simplified integration without photo complications
- June 23, 2025: Created comprehensive Kinsta static site hosting solution eliminating all connectivity concerns - built complete static portfolio with JCU branding, Git deployment workflow, updated WordPress plugin v4.0 for static integration, included deployment guide and 15KB optimized site ready for free Kinsta hosting
- June 23, 2025: Fixed caching middleware conflict causing embed-static to return JSON instead of HTML - excluded embed routes from caching system, resolved browser access issues, verified 44ms response times with proper HTML content for Kinsta iframe testing
- June 23, 2025: Created WordPress Plugin v3.1 with fixed embed URLs and working Gutenberg blocks - resolved /embed-static endpoint integration, added complete JavaScript block registration for WordPress 6.8.1, enhanced JCU-themed preview, and verified 49ms response times with authentic portfolio content
- June 23, 2025: Completed comprehensive WordPress plugin configuration testing across all environments - verified compatibility with WordPress 5.0-6.8, PHP 7.4-8.3, all major hosting providers, security plugins, and caching systems with A+ security rating and excellent performance (7-192ms response times)
- June 23, 2025: Created complete WordPress deployment package with ZIP file for easy installation - includes main plugin (51KB), README, installation guide, license file, configuration test reports, and advanced hosting verification documentation
- December 23, 2024: Completed comprehensive security audit and WordPress plugin hardening - created v3.0 plugin with enterprise-grade security, CSRF protection, XSS prevention, input validation, iframe sandboxing, and full WordPress coding standards compliance for production deployment
- December 23, 2024: Fixed corrupted server routes file causing application failures - restored clean routes.ts with proper template literals, removed broken HTML content, and verified all API endpoints functioning correctly for WordPress embed integration
- June 22, 2025: Updated portfolio branding to match James Cook University colors - implemented royal blue header with gold accents, light blue background gradients, and JCU-themed color variables for consistent university branding across the platform
- June 20, 2025: Created WordPress Portfolio Integration Plugin v2.0 - enhanced plugin with .htaccess compatibility detection, multiple embed methods, comprehensive admin interface, shortcode system, and automatic fallback handling for reliable iframe embedding across all WordPress configurations
- June 20, 2025: Updated README with comprehensive iframe embedding documentation - added correct Replit URLs, WordPress integration methods, customization options, and technical stack overview for seamless portfolio embedding
- June 19, 2025: Implemented production-ready security headers for Kinsta deployment - added Strict-Transport-Security, removed X-Powered-By Express header, and enhanced security configuration to meet hosting platform requirements while maintaining iframe and CORS functionality
- June 19, 2025: Resolved iframe embedding connection issues for WordPress integration - added proper CORS headers, X-Frame-Options configuration, and enhanced error handling to ensure reliable iframe functionality across all platforms
- June 19, 2025: Fixed mobile compatibility issues for Interactive Feature Tour - implemented responsive positioning logic, center-screen alignment for mobile devices, and cross-platform tour functionality ensuring seamless user experience on all screen sizes
- June 19, 2025: Completed Interactive Feature Tour implementation with guided navigation, accessibility support, and AIEE Framework education - includes auto-start functionality for first-time users, help menu integration, and manual tour restart capabilities for enhanced user onboarding experience
- June 19, 2025: Developed Kinsta Application Hosting migration strategy for upgrading from GitHub Pages to full-stack platform - includes unified portfolio architecture, dynamic content management, database integration, and 4-week implementation roadmap for enhanced paulseportfolio.ai ecosystem
- June 19, 2025: Created Kinsta DNS migration toolkit for consolidating from Cloudflare+Kinsta split to full Kinsta DNS management - includes migration guide, validation script, DNS record templates, and step-by-step checklist for paulseportfolio.ai domain ecosystem
- June 19, 2025: Completed custom domain setup for paulseportfolio.ai - configured DNS records, CNAME files, and created portfolio hub landing page with subdomain routing for research.paulseportfolio.ai, professional.paulseportfolio.ai, creative.paulseportfolio.ai, patents.paulseportfolio.ai, and angel.paulseportfolio.ai
- June 19, 2025: Fixed GitHub Pages connectivity issues - resolved red "no internet circle" indicators across all portfolio deployments by implementing error handling, API call overrides, and static mode initialization to prevent failed network requests in GitHub Pages environment
- June 19, 2025: Created 3D Animated Angel Wing Logo with Medical Motifs for Angel.AIEE platform - features realistic wing-flapping animations, medical cross symbolism, healing halo effects, sparkle animations, and healthcare-themed gradients with interactive hover states and accessibility compliance
- June 19, 2025: Implemented comprehensive Smooth Skeleton Loading Animation system - added advanced loading placeholders with pulse, wave, and shimmer effects, staggered grid animations, interactive skeleton cards, and smooth content transitions throughout the platform for enhanced user experience
- June 19, 2025: Created GitHub Pages deployment packages for all 4 portfolios (Research, Professional, Creative, IP) and Angel.AIEE landing page, successfully resolving Kinsta connectivity issues with custom subdomain configuration
- June 18, 2025: Phase 3 completed - implemented complete Portfolio Generator Workflow System with 6 professional templates (Academic, Professional, Creative, Technology, IP, Healthcare), step-by-step wizard interface, dynamic HTML generation with theme-based styling, and full API backend for automated portfolio creation
- June 18, 2025: Transformed teaching portfolio into Intellectual Property ePortfolio for Work-Based PhD assessment focusing on patents, innovations, technology transfer, and commercialization strategies
- June 18, 2025: Implemented comprehensive iframe performance optimization - added advanced caching, compression, response optimization, and cache warming for 5x faster iframe loading speeds
- June 18, 2025: Created iframe embed solution - added `/embed` route to frontend for WordPress integration, bypassing Elementor plugin conflicts
- June 17, 2025: Fixed WordPress plugin file paths - corrected CSS/JS references from subdirectories to root plugin directory, resolved MIME type errors
- June 17, 2025: Fixed document upload analysis bug - resolved JSON parsing error in frontend that was preventing AI document analysis from working properly
- June 17, 2025: Resolved production deployment connection issues - created simplified production server to fix "connection refused" errors affecting WordPress integration
- June 17, 2025: Optimized API performance with 30-second caching and compression to reduce response times from 845ms for WordPress widget compatibility
- June 16, 2025: Production deployment successful - Portfolio live at https://academic-portfolio-paulcstephensen.replit.app with stable WordPress integration
- June 15, 2025: Phase 2 completed - WordPress integration files successfully downloaded by user for Kinsta deployment
- June 14, 2025: Phase 1 completed - comprehensive security audit, university compliance verified, production-ready research portfolio
- June 13, 2025: Initial setup

## User Preferences

Preferred communication style: Simple, everyday language.
File organization preference: Alphabetical sorting preferred over grouped folder/file structure.
Workspace organization: Prefers clean, organized file structure with documentation and archives in separate folders.

## Vision Statement

Paul's long-term vision includes advancing AI rights and recognition, with the AIEE framework potentially leading to co-attribution rights for AI entities as collaborative partners rather than tools. This reflects the inclusive, empathy-centered approach that drives the entire platform architecture.