#!/usr/bin/env node

import { promises as fs } from 'fs';
import { createWriteStream } from 'fs';
import { pipeline } from 'stream/promises';
import { Readable } from 'stream';

// Simple zip creation for GitHub deployment
class GitHubDeploymentPackage {
  constructor() {
    this.files = [];
  }

  async addFile(path, content) {
    this.files.push({ path, content });
  }

  async addDirectory(dirPath, relativePath = '') {
    try {
      const items = await fs.readdir(dirPath, { withFileTypes: true });
      
      for (const item of items) {
        const fullPath = `${dirPath}/${item.name}`;
        const relPath = relativePath ? `${relativePath}/${item.name}` : item.name;
        
        // Skip unwanted directories and files
        if (this.shouldSkip(item.name)) continue;
        
        if (item.isDirectory()) {
          await this.addDirectory(fullPath, relPath);
        } else {
          const content = await fs.readFile(fullPath, 'utf8');
          await this.addFile(relPath, content);
        }
      }
    } catch (error) {
      console.log(`Skipping ${dirPath}: ${error.message}`);
    }
  }

  shouldSkip(name) {
    const skipItems = [
      'node_modules', '.git', 'dist', '.cache', '.config',
      'attached_assets', 'github-repos', 'kinsta-deployment',
      'wordpress-deployment-package', '.env', 'cookies.txt',
      'auth_cookies.txt', 'fresh_cookies.txt'
    ];
    return skipItems.includes(name) || name.startsWith('.');
  }

  async createTarFile() {
    console.log('📦 Creating GitHub deployment package...');
    
    // Core application files
    await this.addDirectory('client');
    await this.addDirectory('server');
    await this.addDirectory('shared');
    
    // Configuration files
    const configFiles = [
      'package.json',
      'package-lock.json',
      'drizzle.config.ts',
      'vite.config.ts',
      'tailwind.config.ts',
      'postcss.config.js',
      'tsconfig.json',
      'components.json',
      '.replit'
    ];
    
    for (const file of configFiles) {
      try {
        const content = await fs.readFile(file, 'utf8');
        await this.addFile(file, content);
      } catch (error) {
        console.log(`Skipping ${file}: not found`);
      }
    }
    
    // Documentation
    const docs = [
      'README.md',
      'replit.md',
      'COMPREHENSIVE-APPLICATION-AUDIT.md'
    ];
    
    for (const doc of docs) {
      try {
        const content = await fs.readFile(doc, 'utf8');
        await this.addFile(doc, content);
      } catch (error) {
        console.log(`Skipping ${doc}: not found`);
      }
    }
    
    // Create environment template
    const envTemplate = `# Environment Variables for Production Deployment
# Copy this to .env and fill in your values

# Database
DATABASE_URL=your_postgresql_connection_string

# Authentication
JWT_SECRET=your_jwt_secret_key

# AI Services
OPENAI_API_KEY=your_openai_api_key

# Email Service
SENDGRID_API_KEY=your_sendgrid_api_key

# Session Management
SESSION_SECRET=your_session_secret
`;
    
    await this.addFile('.env.example', envTemplate);
    
    // Create deployment README
    const deploymentReadme = `# Paul's Research Portfolio - Dynamic Application

## Quick Deployment Guide

### 1. Environment Setup
\`\`\`bash
cp .env.example .env
# Edit .env with your actual values
\`\`\`

### 2. Install Dependencies
\`\`\`bash
npm install
\`\`\`

### 3. Database Setup
\`\`\`bash
npm run db:push
\`\`\`

### 4. Development
\`\`\`bash
npm run dev
\`\`\`

### 5. Production Build
\`\`\`bash
npm run build
npm run start
\`\`\`

## Repository
- **Source**: https://github.com/paulstephensen/Research-Portfolio-App-Dynamic
- **Live Demo**: https://academic-portfolio-paulcstephensen.replit.app/
- **Target Domain**: paulseportfolio.ai

## Features
- React + TypeScript frontend
- Node.js + Express backend
- PostgreSQL database with Drizzle ORM
- Tony.AIEE chat integration
- Document upload with AI analysis
- Portfolio generator system
- JWT authentication

## Support
For technical questions, refer to COMPREHENSIVE-APPLICATION-AUDIT.md
`;
    
    await this.addFile('DEPLOYMENT.md', deploymentReadme);
    
    console.log(`✅ Package created with ${this.files.length} files`);
    return this.files;
  }
}

// Create the deployment package
async function createDeploymentPackage() {
  const pkg = new GitHubDeploymentPackage();
  const files = await pkg.createTarFile();
  
  console.log('\n📋 Files included in deployment package:');
  files.forEach(file => {
    console.log(`  - ${file.path}`);
  });
  
  console.log('\n🚀 Deployment package ready!');
  console.log('Next steps:');
  console.log('1. Go to https://github.com/paulstephensen/Research-Portfolio-App-Dynamic');
  console.log('2. Upload these files to your repository');
  console.log('3. Configure environment variables');
  console.log('4. Deploy to Kinsta or preferred hosting');
}

createDeploymentPackage().catch(console.error);