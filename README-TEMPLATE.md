# Academic Research Portfolio Template

A comprehensive portfolio platform for academic researchers with AI integration, ethics framework, and professional presentation tools.

## Features

### Core Functionality
- **Research Document Management** - Upload, organize, and showcase academic papers
- **AI-Powered Analysis** - Grant opportunity identification, patent analysis, collaboration insights
- **Citation Generation** - Multiple academic formats (APA, MLA, Chicago, Harvard)
- **Social Media Sharing** - LinkedIn, Twitter, Facebook, WhatsApp integration
- **Analytics Dashboard** - Publication metrics, citation tracking, impact analysis
- **WordPress Integration** - Publish directly to WordPress sites

### AI Ethics Framework (AIEE)
- **Experience-Driven Design** - User-centric interface and accessibility
- **Empathetic AI Interactions** - Contextual, supportive AI responses
- **Ethical Implementation** - Transparent AI operations with bias detection
- **Privacy Protection** - Secure data handling and user consent

### Customization Options
- Personal branding and color schemes
- Configurable research areas and expertise
- Custom story and mission statements
- Flexible social media links
- Optional AI mentor integration

## Quick Setup

### 1. Configuration
Edit `template-config.json` to customize:
```json
{
  "portfolioConfig": {
    "personal": {
      "name": "Your Name",
      "title": "Your Academic Title",
      "email": "your.email@domain.com",
      "institution": "Your Institution"
    },
    "branding": {
      "siteName": "Your Portfolio Name",
      "primaryColor": "blue|purple|green|red"
    }
  }
}
```

### 2. Environment Setup
```bash
# Install dependencies
npm install

# Set up database
DATABASE_URL=your_database_url

# Configure AI services (optional)
OPENAI_API_KEY=your_openai_key

# Email services (optional)
SENDGRID_API_KEY=your_sendgrid_key

# Start development server
npm run dev
```

### 3. Content Addition
1. Upload research papers through the interface
2. Configure research areas in settings
3. Set up WordPress connections (optional)
4. Customize About section with your story

## File Structure

```
├── client/                 # Frontend React application
│   ├── src/
│   │   ├── components/    # Reusable UI components
│   │   ├── pages/         # Main application pages
│   │   └── lib/           # Utilities and helpers
├── server/                # Backend Express server
│   ├── auth.ts            # Authentication system
│   ├── db.ts              # Database connection
│   ├── openai.ts          # AI integration
│   ├── routes.ts          # API endpoints
│   └── storage.ts         # Data operations
├── shared/                # Shared types and schemas
└── template-config.json   # Configuration file
```

## Customization Guide

### Personal Branding
- Update colors in `template-config.json`
- Replace logo and profile images
- Modify header text and descriptions

### Research Areas
Add your specializations to the config:
```json
"researchAreas": [
  "Your Research Area 1",
  "Your Research Area 2",
  "Your Research Area 3"
]
```

### AI Features
Enable/disable AI features:
```json
"features": {
  "aiFramework": { "enabled": true },
  "aiMentor": { "enabled": false },
  "analytics": { "enabled": true }
}
```

### Social Integration
Configure your academic profiles:
```json
"socialLinks": {
  "linkedin": "https://linkedin.com/in/yourprofile",
  "googleScholar": "https://scholar.google.com/citations?user=yourid",
  "researchGate": "https://researchgate.net/profile/yourprofile"
}
```

## Deployment

### Replit Deployment
1. Fork this project on Replit
2. Configure environment variables
3. Run the deployment workflow
4. Your portfolio will be available at `yourproject.replit.app`

### Custom Domain
1. Set up your domain DNS
2. Configure SSL certificates
3. Update environment variables with your domain

## AI Integration

### OpenAI Services
The template includes AI-powered features:
- Research analysis and insights
- Grant opportunity identification
- Patent potential assessment
- Academic writing assistance

To enable, add your OpenAI API key to environment variables.

### Ethics Framework
The AIEE (AI Ethics & Experience) framework ensures:
- Transparent AI operations
- User-centered design
- Privacy protection
- Bias detection and mitigation

## WordPress Integration

Connect your portfolio to WordPress sites:
1. Navigate to WordPress settings
2. Add site credentials
3. Configure publishing options
4. Publish research directly to your blog

## Support and Documentation

### Template Customization
- Modify colors and branding in config file
- Update personal information and story
- Configure research areas and expertise
- Set up social media links

### Technical Support
- Check the documentation for common issues
- Review environment variable configuration
- Ensure database connectivity
- Verify API key setup

## License

MIT License - Free for academic and research use.

## Credits

Developed by Paul Stephensen as part of the AIEE Framework research project.
Template designed for the academic research community.