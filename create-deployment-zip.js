import fs from 'fs';
import path from 'path';

// Simple ZIP file creation utility
class SimpleZip {
  constructor() {
    this.files = [];
  }

  addFile(filename, data) {
    if (typeof data === 'string') {
      data = Buffer.from(data, 'utf8');
    }
    this.files.push({
      filename: filename,
      data: data,
      crc32: this.crc32(data),
      size: data.length
    });
  }

  crc32(data) {
    let crc = 0xFFFFFFFF;
    for (let i = 0; i < data.length; i++) {
      crc = crc ^ data[i];
      for (let j = 0; j < 8; j++) {
        crc = (crc >>> 1) ^ (0xEDB88320 & (-(crc & 1)));
      }
    }
    return (crc ^ 0xFFFFFFFF) >>> 0;
  }

  createLocalFileHeader(filename, data) {
    const filenameBuffer = Buffer.from(filename, 'utf8');
    const header = Buffer.alloc(30 + filenameBuffer.length);
    
    // Local file header signature
    header.writeUInt32LE(0x04034b50, 0);
    // Version needed to extract
    header.writeUInt16LE(20, 4);
    // General purpose bit flag
    header.writeUInt16LE(0, 6);
    // Compression method (0 = no compression)
    header.writeUInt16LE(0, 8);
    // Last mod file time & date
    header.writeUInt16LE(0, 10);
    header.writeUInt16LE(0, 12);
    // CRC-32
    header.writeUInt32LE(this.crc32(data), 14);
    // Compressed size
    header.writeUInt32LE(data.length, 18);
    // Uncompressed size
    header.writeUInt32LE(data.length, 22);
    // File name length
    header.writeUInt16LE(filenameBuffer.length, 26);
    // Extra field length
    header.writeUInt16LE(0, 28);
    
    // Copy filename
    filenameBuffer.copy(header, 30);
    
    return header;
  }

  createCentralDirectoryEntry(file, offset) {
    const filenameBuffer = Buffer.from(file.filename, 'utf8');
    const entry = Buffer.alloc(46 + filenameBuffer.length);
    
    // Central directory file header signature
    entry.writeUInt32LE(0x02014b50, 0);
    // Version made by
    entry.writeUInt16LE(20, 4);
    // Version needed to extract
    entry.writeUInt16LE(20, 6);
    // General purpose bit flag
    entry.writeUInt16LE(0, 8);
    // Compression method
    entry.writeUInt16LE(0, 10);
    // Last mod file time & date
    entry.writeUInt16LE(0, 12);
    entry.writeUInt16LE(0, 14);
    // CRC-32
    entry.writeUInt32LE(file.crc32, 16);
    // Compressed size
    entry.writeUInt32LE(file.size, 20);
    // Uncompressed size
    entry.writeUInt32LE(file.size, 24);
    // File name length
    entry.writeUInt16LE(filenameBuffer.length, 28);
    // Extra field length
    entry.writeUInt16LE(0, 30);
    // File comment length
    entry.writeUInt16LE(0, 32);
    // Disk number start
    entry.writeUInt16LE(0, 34);
    // Internal file attributes
    entry.writeUInt16LE(0, 36);
    // External file attributes
    entry.writeUInt32LE(0, 38);
    // Relative offset of local header
    entry.writeUInt32LE(offset, 42);
    
    // Copy filename
    filenameBuffer.copy(entry, 46);
    
    return entry;
  }

  createEndOfCentralDirectory(centralDirStart, centralDirSize) {
    const eocd = Buffer.alloc(22);
    
    // End of central dir signature
    eocd.writeUInt32LE(0x06054b50, 0);
    // Number of this disk
    eocd.writeUInt16LE(0, 4);
    // Number of the disk with start of central directory
    eocd.writeUInt16LE(0, 6);
    // Total number of entries in central directory on this disk
    eocd.writeUInt16LE(this.files.length, 8);
    // Total number of entries in central directory
    eocd.writeUInt16LE(this.files.length, 10);
    // Size of central directory
    eocd.writeUInt32LE(centralDirSize, 12);
    // Offset of start of central directory
    eocd.writeUInt32LE(centralDirStart, 16);
    // ZIP file comment length
    eocd.writeUInt16LE(0, 20);
    
    return eocd;
  }

  generate() {
    const buffers = [];
    const centralDirEntries = [];
    let offset = 0;

    // Write local file headers and data
    for (const file of this.files) {
      const header = this.createLocalFileHeader(file.filename, file.data);
      buffers.push(header);
      buffers.push(file.data);
      
      centralDirEntries.push(this.createCentralDirectoryEntry(file, offset));
      offset += header.length + file.data.length;
    }

    // Write central directory
    const centralDirStart = offset;
    let centralDirSize = 0;
    for (const entry of centralDirEntries) {
      buffers.push(entry);
      centralDirSize += entry.length;
    }

    // Write end of central directory
    const eocd = this.createEndOfCentralDirectory(centralDirStart, centralDirSize);
    buffers.push(eocd);

    return Buffer.concat(buffers);
  }
}

async function addDirectoryToZip(zip, dirPath, basePath = '') {
  try {
    const items = fs.readdirSync(dirPath, { withFileTypes: true });
    
    for (const item of items) {
      const fullPath = path.join(dirPath, item.name);
      const relativePath = basePath ? `${basePath}/${item.name}` : item.name;
      
      // Skip unwanted items
      if (shouldSkip(item.name)) continue;
      
      if (item.isDirectory()) {
        await addDirectoryToZip(zip, fullPath, relativePath);
      } else {
        try {
          const content = fs.readFileSync(fullPath, 'utf8');
          zip.addFile(relativePath, content);
        } catch (error) {
          console.log(`Skipping ${fullPath}: ${error.message}`);
        }
      }
    }
  } catch (error) {
    console.log(`Skipping directory ${dirPath}: ${error.message}`);
  }
}

function shouldSkip(name) {
  const skipItems = [
    'node_modules', '.git', 'dist', '.cache', '.config',
    'attached_assets', 'github-repos', 'kinsta-deployment',
    'wordpress-deployment-package', 'portfolio-templates',
    'kinsta-static-site', 'kinsta-upload-package', 'npm',
    '.env', 'cookies.txt', 'auth_cookies.txt', 'fresh_cookies.txt',
    'create-deployment-zip.js', 'create-github-deployment.js'
  ];
  
  return skipItems.includes(name) || 
         name.startsWith('.') || 
         name.endsWith('.zip') || 
         name.endsWith('.tar.gz') ||
         name.includes('wordpress') ||
         name.includes('kinsta') ||
         name.includes('github-') ||
         name.includes('deployment');
}

async function createGitHubDeploymentZip() {
  console.log('Creating GitHub deployment ZIP...');
  
  const zip = new SimpleZip();
  
  // Add core application directories
  await addDirectoryToZip(zip, 'client');
  await addDirectoryToZip(zip, 'server');
  await addDirectoryToZip(zip, 'shared');
  
  // Add configuration files
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
      const content = fs.readFileSync(file, 'utf8');
      zip.addFile(file, content);
    } catch (error) {
      console.log(`Skipping ${file}: not found`);
    }
  }
  
  // Add documentation
  const docs = ['README.md', 'replit.md', 'COMPREHENSIVE-APPLICATION-AUDIT.md'];
  for (const doc of docs) {
    try {
      const content = fs.readFileSync(doc, 'utf8');
      zip.addFile(doc, content);
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
  
  zip.addFile('.env.example', envTemplate);
  
  // Create deployment instructions
  const deploymentGuide = `# Paul's Research Portfolio - Dynamic Application

## Quick Deployment to GitHub

### 1. Upload Files
Upload all files from this ZIP to your GitHub repository:
https://github.com/paulstephensen/Research-Portfolio-App-Dynamic

### 2. Environment Setup
\`\`\`bash
cp .env.example .env
# Edit .env with your actual values
\`\`\`

### 3. Install Dependencies
\`\`\`bash
npm install
\`\`\`

### 4. Database Setup
\`\`\`bash
npm run db:push
\`\`\`

### 5. Development
\`\`\`bash
npm run dev
\`\`\`

### 6. Production Build
\`\`\`bash
npm run build
npm run start
\`\`\`

## Application Features
- React + TypeScript frontend
- Node.js + Express backend  
- PostgreSQL database with Drizzle ORM
- Tony.AIEE chat integration
- Document upload with AI analysis
- Portfolio generator system
- JWT authentication

## Target Deployment
- **Domain**: paulseportfolio.ai
- **Current Demo**: https://academic-portfolio-paulcstephensen.replit.app/
- **Repository**: https://github.com/paulstephensen/Research-Portfolio-App-Dynamic

## Support
Refer to COMPREHENSIVE-APPLICATION-AUDIT.md for complete technical documentation.
`;
  
  zip.addFile('DEPLOYMENT-GUIDE.md', deploymentGuide);
  
  // Generate ZIP file
  const zipData = zip.generate();
  const filename = 'paul-research-portfolio-github-deployment.zip';
  
  fs.writeFileSync(filename, zipData);
  
  console.log(`\n✅ ZIP file created: ${filename}`);
  console.log(`📦 Size: ${Math.round(zipData.length / 1024)}KB`);
  console.log(`📁 Files: ${zip.files.length}`);
  console.log('\n🚀 Ready for GitHub upload!');
  console.log('Next steps:');
  console.log('1. Download the ZIP file from Replit');
  console.log('2. Go to https://github.com/paulstephensen/Research-Portfolio-App-Dynamic');
  console.log('3. Upload files from the ZIP to your repository');
  console.log('4. Configure environment variables');
  console.log('5. Deploy to your hosting platform');
}

createGitHubDeploymentZip().catch(console.error);