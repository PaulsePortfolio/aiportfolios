# Paul Stephensen's Research Portfolio

A comprehensive academic research portfolio platform featuring AI-powered document analysis, portfolio generation tools, and WordPress integration capabilities.

## 🎯 Overview

This platform serves as both a personal research documentation system and a demonstration of the AIEE (Accessibility, Intelligence, Ethics, Empathy) Framework - a novel approach to ethical AI integration in academic research platforms.

## 🚀 Features

### Core Functionality
- **Research Document Management**: Upload, organize, and analyze academic papers
- **AI-Powered Analysis**: Document content analysis via OpenAI GPT-4
- **AIEE Framework Integration**: Accessibility-first design with empathetic AI interactions
- **Analytics Dashboard**: Publication metrics and research impact analysis
- **Portfolio Generator**: 6 professional templates for different career paths

### Advanced Features
- **Tony.AI Research Mentor**: Contextual research assistance
- **WordPress Integration**: Professional iframe embedding
- **Secure Authentication**: JWT-based with enterprise-grade security
- **Real-time Collaboration**: Multi-user research paper collaboration

## 🛠 Tech Stack

### Frontend
- **React 18** with TypeScript
- **Tailwind CSS** with shadcn/ui components
- **TanStack Query** for server state management
- **Wouter** for lightweight routing
- **Vite** for development and production builds

### Backend
- **Node.js 20** with Express.js
- **TypeScript** with ESM modules
- **Drizzle ORM** with PostgreSQL
- **JWT Authentication** with session management

### Database
- **PostgreSQL 16** with connection pooling
- **Drizzle Kit** for migrations
- **Type-safe** schema definitions

## 📦 Installation

### Prerequisites
- Node.js 20.x or higher
- PostgreSQL 16+
- Git

### Local Development Setup

1. **Clone the repository**
```bash
git clone https://github.com/PaulsePortfolios-ai/Pauls-Research-portfolio.git
cd Pauls-Research-portfolio
```

2. **Install dependencies**
```bash
npm install
```

3. **Environment Configuration**
```bash
cp .env.example .env
# Edit .env with your actual values
```

4. **Database Setup**
```bash
npm run db:push
```

5. **Start Development Server**
```bash
npm run dev
```

Visit `http://localhost:5000` to access the application.

## 🌐 Production Deployment

### Kinsta Application Hosting

1. **Application Configuration**
   - **Build Command**: `npm run build`
   - **Start Command**: `npm start`
   - **Node Version**: 20.x
   - **Port**: 8080

2. **Environment Variables**
   Set the following in Kinsta dashboard:
   ```
   DATABASE_URL=postgresql://...
   SENDGRID_API_KEY=SG....
   JWT_SECRET=your_secure_secret
   SESSION_SECRET=your_secure_secret
   NODE_ENV=production
   PORT=8080
   ```

3. **Database Requirements**
   - PostgreSQL 16 with connection pooling
   - Auto-backups enabled
   - SSL connection required

### Domain Configuration
- Primary: `paulseportfolio.ai` (redirect to Kinsta)
- Research: `research.paulseportfolio.ai`
- WordPress embed: Available at `/embed` and `/embed-static`

## 🔐 Security Features

- **Authentication**: JWT-based with secure session management
- **Password Security**: bcrypt hashing with 12 salt rounds
- **Rate Limiting**: Protection against brute force attacks
- **Input Validation**: Comprehensive Zod schema validation
- **SQL Injection Prevention**: Parameterized queries via Drizzle ORM
- **XSS Protection**: Input sanitization and CSP headers

## 📊 API Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/user` - Get current user

### Research Papers
- `GET /api/research-papers` - List all papers
- `POST /api/research-papers` - Create new paper
- `GET /api/research-papers/:id` - Get specific paper
- `PUT /api/research-papers/:id` - Update paper
- `DELETE /api/research-papers/:id` - Delete paper

### Embed Routes
- `GET /embed` - Dynamic React embed
- `GET /embed-static` - Static HTML embed

## 🤝 Contributing

This is a personal research portfolio. For questions or collaboration inquiries, please contact:

**Paul Stephensen**
- Email: paul@paulseportfolio.ai
- Portfolio: https://paulseportfolio.ai

## 📄 License

MIT License - see LICENSE file for details.

## 🎓 Academic Context

This portfolio represents independent PhD research focusing on:
- AI Ethics and Transparency
- AIEE Framework Development
- Accessible Technology Design
- Empathetic AI Interactions

The platform demonstrates practical applications of ethical AI principles in academic research tools.

---

**Built with ❤️ for the academic research community**