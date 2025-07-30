# Complete Replit Export Package for GitHub & Kinsta Migration
## Academic Portfolio v2 - Paul Stephensen Research Platform

**Export Date**: July 1, 2025  
**Source**: https://academic-portfoliov-2-paulcstephensen.replit.app/  
**Target GitHub Repo**: https://github.com/paulstephensen/Research-Portfolio-App-Dynamic.git  
**Target Platform**: Kinsta Application Hosting  

---

## 🎯 **Migration Summary**

This document provides the complete source code export and migration guide for Paul Stephensen's Academic Portfolio v2 from Replit to GitHub and Kinsta Application Hosting.

### **Platform Details**
- **Current URL**: https://academic-portfoliov-2-paulcstephensen.replit.app/
- **Technology Stack**: React 18 + Node.js 20 + PostgreSQL 16 + TypeScript
- **Authentication**: JWT-based with cookie sessions
- **Database**: PostgreSQL with Drizzle ORM
- **AI Integration**: OpenAI GPT-4 for document analysis

---

## 📁 **Complete File Structure Export**

### **Core Application Files**

#### **Frontend (client/)**
```
client/
├── index.html                 # Main HTML entry point
├── src/
│   ├── App.tsx               # Main React application
│   ├── main.tsx              # React entry point
│   ├── pages/                # All application pages
│   ├── components/           # React components
│   ├── hooks/                # Custom React hooks
│   ├── lib/                  # Utility libraries
│   └── styles/               # CSS and styling
```

#### **Backend (server/)**
```
server/
├── index.ts                  # Server entry point
├── routes.ts                 # All API endpoints
├── auth-working.ts           # Authentication system (MAIN)
├── db.ts                     # Database configuration
├── storage.ts                # Data access layer
├── openai-fixed.ts           # AI analysis services
├── email.ts                  # Email services
├── heygen.ts                 # Avatar integration
└── vite.ts                   # Vite development server
```

#### **Shared (shared/)**
```
shared/
└── schema.ts                 # Database schema & TypeScript types
```

#### **Configuration Files**
```
├── package.json              # Dependencies and scripts
├── drizzle.config.ts         # Database ORM configuration
├── vite.config.ts            # Vite build configuration
├── tailwind.config.ts        # Tailwind CSS configuration
├── tsconfig.json             # TypeScript configuration
├── components.json           # shadcn/ui component configuration
├── postcss.config.js         # PostCSS configuration
├── .env.example              # Environment variables template
├── .replit                   # Replit hosting configuration
└── .gitignore                # Git ignore rules
```

---

## 🔧 **Environment Variables Required**

### **Essential Variables**
```env
# Database (Required)
DATABASE_URL=postgresql://username:password@host:port/database

# Security (Required)
JWT_SECRET=your_256_bit_secret_here
SESSION_SECRET=your_256_bit_session_secret_here

# AI Services (Required)
OPENAI_API_KEY=your_openai_api_key_here

# Email Services (Required)
SENDGRID_API_KEY=your_sendgrid_api_key_here

# Application
NODE_ENV=production
PORT=8080
```

### **Database Connection Details**
```env
PGHOST=your_postgres_host
PGPORT=5432
PGDATABASE=research_portfolio
PGUSER=your_username
PGPASSWORD=your_password
```

---

## 🗄️ **Database Schema Export**

### **PostgreSQL Tables Structure**

#### **Users Table**
```sql
CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  username VARCHAR(255) UNIQUE NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  first_name VARCHAR(255),
  last_name VARCHAR(255),
  institution VARCHAR(255),
  department VARCHAR(255),
  role VARCHAR(50) DEFAULT 'researcher',
  email_verified BOOLEAN DEFAULT false,
  email_verification_token VARCHAR(255),
  password_reset_token VARCHAR(255),
  password_reset_expires TIMESTAMP,
  last_login TIMESTAMP,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW(),
  profile_picture VARCHAR(500),
  bio TEXT,
  research_interests TEXT[],
  social_links JSONB,
  preferences JSONB,
  is_active VARCHAR(10) DEFAULT 'true'
);
```

#### **Research Papers Table**
```sql
CREATE TABLE research_papers (
  id SERIAL PRIMARY KEY,
  user_id INTEGER REFERENCES users(id),
  title VARCHAR(500) NOT NULL,
  description TEXT NOT NULL,
  authors VARCHAR(255)[],
  category VARCHAR(100) NOT NULL,
  publish_date VARCHAR(50) NOT NULL,
  venue VARCHAR(255),
  methodology VARCHAR(255),
  ethical_framework VARCHAR(255),
  innovation_score INTEGER,
  quality_score INTEGER,
  citation_count INTEGER DEFAULT 0,
  scholar_url VARCHAR(500),
  scholar_citations INTEGER DEFAULT 0,
  scholar_h_index INTEGER DEFAULT 0,
  scholar_i10_index INTEGER DEFAULT 0,
  scholar_last_updated TIMESTAMP,
  status VARCHAR(50) DEFAULT 'draft',
  tags VARCHAR(100)[],
  collaborators VARCHAR(255)[],
  funding_source VARCHAR(255),
  keywords VARCHAR(100)[],
  abstract TEXT,
  conclusions TEXT,
  limitations TEXT,
  future_work TEXT,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);
```

#### **Additional Tables**
```sql
-- Comments Table
CREATE TABLE comments (
  id SERIAL PRIMARY KEY,
  paper_id INTEGER REFERENCES research_papers(id),
  user_id INTEGER REFERENCES users(id),
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT NOW()
);

-- WordPress Connections Table
CREATE TABLE wordpress_connections (
  id SERIAL PRIMARY KEY,
  user_id INTEGER REFERENCES users(id),
  site_url VARCHAR(255) NOT NULL,
  username VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMP DEFAULT NOW()
);

-- Email Tokens Table
CREATE TABLE email_tokens (
  id SERIAL PRIMARY KEY,
  user_id INTEGER REFERENCES users(id),
  token VARCHAR(255) NOT NULL,
  type VARCHAR(50) NOT NULL,
  expires_at TIMESTAMP NOT NULL,
  used BOOLEAN DEFAULT false,
  created_at TIMESTAMP DEFAULT NOW()
);
```

---

## 🔐 **Authentication System**

### **Login Function Implementation**
```typescript
// server/routes.ts - Line 1074-1122
app.post("/api/auth/login", async (req, res) => {
  try {
    const { email, password } = req.body;
    
    if (!email || !password) {
      return res.status(400).json({ error: "Email and password are required" });
    }
    
    const user = await storage.getUserByEmail(email.toLowerCase());
    if (!user) {
      return res.status(400).json({ error: "Invalid credentials" });
    }
    
    const isValidPassword = await verifyPassword(password, user.password);
    if (!isValidPassword) {
      return res.status(400).json({ error: "Invalid credentials" });
    }
    
    const token = generateJWT(user.id.toString(), user.email, user.role || 'researcher');
    
    res.cookie('authToken', token, {
      httpOnly: true,
      secure: process.env.NODE_ENV === 'production',
      sameSite: 'lax',
      maxAge: 7 * 24 * 60 * 60 * 1000 // 7 days
    });
    
    res.json({
      user: {
        id: user.id,
        username: user.username,
        email: user.email,
        firstName: user.firstName,
        lastName: user.lastName,
        role: user.role,
        emailVerified: user.emailVerified
      }
    });
  } catch (error) {
    console.error("Login error:", error);
    res.status(500).json({ error: "Login failed" });
  }
});
```

### **Authentication Helper Functions**
```typescript
// server/auth-working.ts
import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';

const JWT_SECRET = process.env.JWT_SECRET || 'fallback-secret-key';
const JWT_EXPIRES_IN = '7d';

export async function hashPassword(password: string): Promise<string> {
  return bcrypt.hash(password, 12);
}

export async function verifyPassword(password: string, hash: string): Promise<boolean> {
  return bcrypt.compare(password, hash);
}

export function generateJWT(userId: string, email: string, role: string): string {
  return jwt.sign(
    { userId, email, role },
    JWT_SECRET,
    { expiresIn: JWT_EXPIRES_IN }
  );
}

export function verifyJWT(token: string): any {
  return jwt.verify(token, JWT_SECRET);
}
```

---

## 🤖 **AI Integration Setup**

### **OpenAI Configuration**
```typescript
// server/openai-fixed.ts
import OpenAI from 'openai';

const openai = new OpenAI({
  apiKey: process.env.OPENAI_API_KEY,
});

export async function analyzeDocument(content: string, filename: string) {
  const completion = await openai.chat.completions.create({
    model: "gpt-4",
    messages: [
      {
        role: "system",
        content: "You are an AI research assistant specialized in academic document analysis..."
      },
      {
        role: "user",
        content: `Analyze this research document: ${content}`
      }
    ],
    temperature: 0.7,
  });

  return completion.choices[0].message.content;
}
```

---

## 🚀 **Kinsta Deployment Configuration**

### **Build Scripts**
```json
{
  "scripts": {
    "dev": "NODE_ENV=development tsx server/index.ts",
    "build": "vite build && esbuild server/index.ts --platform=node --packages=external --bundle --format=esm --outdir=dist",
    "start": "NODE_ENV=production node dist/index.js",
    "check": "tsc",
    "db:push": "drizzle-kit push"
  }
}
```

### **Kinsta Application Settings**
```
Build Command: npm run build
Start Command: npm run start
Node Version: 20.x
Install Command: npm install
Environment: Node.js
Port: 8080 (or environment PORT)
```

---

## 📋 **Migration Checklist**

### **Pre-Migration Steps**
- [ ] Export all source code files
- [ ] Create .env file with production environment variables
- [ ] Set up PostgreSQL database on Kinsta or external provider
- [ ] Test local build with `npm run build`
- [ ] Verify all API endpoints work locally

### **GitHub Repository Setup**
- [ ] Clone: https://github.com/paulstephensen/Research-Portfolio-App-Dynamic.git
- [ ] Push all source code files
- [ ] Add environment variables to GitHub Secrets (if using GitHub Actions)
- [ ] Update README.md with deployment instructions

### **Kinsta Deployment Steps**
- [ ] Create new application on Kinsta
- [ ] Connect GitHub repository
- [ ] Configure build settings (Node.js 20, npm run build, npm run start)
- [ ] Add environment variables in Kinsta dashboard
- [ ] Set up PostgreSQL database connection
- [ ] Deploy and test application

### **Post-Migration Verification**
- [ ] Test login functionality with: paulcstephensen@gmail.com / password123
- [ ] Verify database connection and data persistence
- [ ] Test AI document analysis features
- [ ] Confirm email services work (SendGrid)
- [ ] Check all API endpoints respond correctly
- [ ] Verify HTTPS and security headers

---

## 🔍 **Test Credentials**

### **Application Login**
```
Email: paulcstephensen@gmail.com
Password: password123
```

### **API Testing**
```bash
# Login Test
curl -X POST https://your-domain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"paulcstephensen@gmail.com","password":"password123"}'

# Health Check
curl https://your-domain.com/api/research-papers
```

---

## 📞 **Support Information**

### **Current Issues (Known)**
1. Frontend receiving 401 errors on `/api/auth/user` - cookie authentication needs debugging
2. JWT token handling in frontend may need improvements
3. Session persistence across browser refreshes needs verification

### **Technical Contact**
- **Platform**: Research Portfolio v2.0
- **Documentation**: RESEARCH-PORTFOLIO-V2-COMPREHENSIVE-DOCUMENTATION.md (1,117 lines)
- **GitHub**: https://github.com/paulstephensen/Research-Portfolio-App-Dynamic.git
- **Technology Stack**: React 18, Node.js 20, PostgreSQL 16, TypeScript

---

## ✅ **Migration Completion Verification**

After migration, verify these features work:
1. ✅ User authentication (login/logout)
2. ✅ Document upload and AI analysis
3. ✅ Research paper management
4. ✅ Tony.AIEE chat assistant
5. ✅ Database persistence
6. ✅ Email notifications
7. ✅ API endpoints functionality

**End of Export Package**