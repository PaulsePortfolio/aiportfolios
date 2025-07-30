import fs from 'fs';
import path from 'path';

class SimpleZip {
  constructor() {
    this.files = [];
    this.centralDirectory = [];
  }

  addFile(filename, data) {
    const buffer = Buffer.isBuffer(data) ? data : Buffer.from(data, 'utf8');
    
    // Create local file header
    const localHeader = this.createLocalFileHeader(filename, buffer);
    
    this.files.push({
      name: filename,
      data: buffer,
      localHeader: localHeader,
      offset: this.getCurrentOffset()
    });
  }

  createLocalFileHeader(filename, data) {
    const filenameBuffer = Buffer.from(filename, 'utf8');
    const crc = this.crc32(data);
    
    const header = Buffer.alloc(30 + filenameBuffer.length);
    let offset = 0;
    
    // Local file header signature
    header.writeUInt32LE(0x04034b50, offset); offset += 4;
    // Version needed
    header.writeUInt16LE(20, offset); offset += 2;
    // General purpose bit flag
    header.writeUInt16LE(0, offset); offset += 2;
    // Compression method (0 = no compression)
    header.writeUInt16LE(0, offset); offset += 2;
    // Last mod time & date (dummy values)
    header.writeUInt16LE(0, offset); offset += 2;
    header.writeUInt16LE(0, offset); offset += 2;
    // CRC-32
    header.writeUInt32LE(crc, offset); offset += 4;
    // Compressed size
    header.writeUInt32LE(data.length, offset); offset += 4;
    // Uncompressed size
    header.writeUInt32LE(data.length, offset); offset += 4;
    // Filename length
    header.writeUInt16LE(filenameBuffer.length, offset); offset += 2;
    // Extra field length
    header.writeUInt16LE(0, offset); offset += 2;
    // Filename
    filenameBuffer.copy(header, offset);
    
    return header;
  }

  crc32(data) {
    const crcTable = [];
    for (let i = 0; i < 256; i++) {
      let crc = i;
      for (let j = 0; j < 8; j++) {
        crc = (crc & 1) ? (0xEDB88320 ^ (crc >>> 1)) : (crc >>> 1);
      }
      crcTable[i] = crc;
    }
    
    let crc = 0xFFFFFFFF;
    for (let i = 0; i < data.length; i++) {
      crc = crcTable[(crc ^ data[i]) & 0xFF] ^ (crc >>> 8);
    }
    return (crc ^ 0xFFFFFFFF) >>> 0;
  }

  getCurrentOffset() {
    let offset = 0;
    for (const file of this.files) {
      offset += file.localHeader.length + file.data.length;
    }
    return offset;
  }

  createCentralDirectoryEntry(file) {
    const filenameBuffer = Buffer.from(file.name, 'utf8');
    const crc = this.crc32(file.data);
    
    const entry = Buffer.alloc(46 + filenameBuffer.length);
    let offset = 0;
    
    // Central directory signature
    entry.writeUInt32LE(0x02014b50, offset); offset += 4;
    // Version made by
    entry.writeUInt16LE(20, offset); offset += 2;
    // Version needed
    entry.writeUInt16LE(20, offset); offset += 2;
    // General purpose bit flag
    entry.writeUInt16LE(0, offset); offset += 2;
    // Compression method
    entry.writeUInt16LE(0, offset); offset += 2;
    // Last mod time & date
    entry.writeUInt16LE(0, offset); offset += 2;
    entry.writeUInt16LE(0, offset); offset += 2;
    // CRC-32
    entry.writeUInt32LE(crc, offset); offset += 4;
    // Compressed size
    entry.writeUInt32LE(file.data.length, offset); offset += 4;
    // Uncompressed size
    entry.writeUInt32LE(file.data.length, offset); offset += 4;
    // Filename length
    entry.writeUInt16LE(filenameBuffer.length, offset); offset += 2;
    // Extra field length
    entry.writeUInt16LE(0, offset); offset += 2;
    // File comment length
    entry.writeUInt16LE(0, offset); offset += 2;
    // Disk number start
    entry.writeUInt16LE(0, offset); offset += 2;
    // Internal file attributes
    entry.writeUInt16LE(0, offset); offset += 2;
    // External file attributes
    entry.writeUInt32LE(0, offset); offset += 4;
    // Relative offset of local header
    entry.writeUInt32LE(file.offset, offset); offset += 4;
    // Filename
    filenameBuffer.copy(entry, offset);
    
    return entry;
  }

  createEndOfCentralDirectory(centralDirStart, centralDirSize) {
    const eocd = Buffer.alloc(22);
    let offset = 0;
    
    // End of central directory signature
    eocd.writeUInt32LE(0x06054b50, offset); offset += 4;
    // Number of this disk
    eocd.writeUInt16LE(0, offset); offset += 2;
    // Disk where central directory starts
    eocd.writeUInt16LE(0, offset); offset += 2;
    // Number of central directory records on this disk
    eocd.writeUInt16LE(this.files.length, offset); offset += 2;
    // Total number of central directory records
    eocd.writeUInt16LE(this.files.length, offset); offset += 2;
    // Size of central directory
    eocd.writeUInt32LE(centralDirSize, offset); offset += 4;
    // Offset of start of central directory
    eocd.writeUInt32LE(centralDirStart, offset); offset += 4;
    // Comment length
    eocd.writeUInt16LE(0, offset);
    
    return eocd;
  }

  generate() {
    const chunks = [];
    
    // Add all local file headers and data
    for (const file of this.files) {
      chunks.push(file.localHeader);
      chunks.push(file.data);
    }
    
    // Calculate central directory start
    const centralDirStart = chunks.reduce((sum, chunk) => sum + chunk.length, 0);
    
    // Add central directory entries
    const centralDirEntries = [];
    for (const file of this.files) {
      const entry = this.createCentralDirectoryEntry(file);
      centralDirEntries.push(entry);
      chunks.push(entry);
    }
    
    // Calculate central directory size
    const centralDirSize = centralDirEntries.reduce((sum, entry) => sum + entry.length, 0);
    
    // Add end of central directory
    const eocd = this.createEndOfCentralDirectory(centralDirStart, centralDirSize);
    chunks.push(eocd);
    
    return Buffer.concat(chunks);
  }
}

async function addDirectoryToZip(zip, dirPath, basePath = '') {
  const entries = fs.readdirSync(dirPath, { withFileTypes: true });
  
  for (const entry of entries) {
    if (shouldSkip(entry.name)) continue;
    
    const fullPath = path.join(dirPath, entry.name);
    const zipPath = basePath ? path.join(basePath, entry.name).replace(/\\/g, '/') : entry.name;
    
    if (entry.isDirectory()) {
      await addDirectoryToZip(zip, fullPath, zipPath);
    } else {
      const content = fs.readFileSync(fullPath);
      zip.addFile(zipPath, content);
    }
  }
}

function shouldSkip(name) {
  const skipList = [
    'node_modules', 'dist', '.git', '.env', '.replit', 
    'replit.nix', '*.zip', 'auth_cookies.txt', 'cookies.txt',
    '.DS_Store', 'Thumbs.db'
  ];
  return skipList.some(pattern => {
    if (pattern.includes('*')) {
      const regex = new RegExp(pattern.replace('*', '.*'));
      return regex.test(name);
    }
    return name === pattern;
  });
}

async function createGitHubDeploymentZip() {
  console.log('Creating GitHub deployment ZIP package...');
  
  const zip = new SimpleZip();
  const packageDir = 'github-deployment-package';
  
  try {
    // Add all files from the package directory
    await addDirectoryToZip(zip, packageDir);
    
    // Generate ZIP file
    const zipData = zip.generate();
    
    // Write ZIP file
    const outputPath = 'Research-Portfolio-App-Dynmanic-GitHub-Deploy.zip';
    fs.writeFileSync(outputPath, zipData);
    
    console.log(`✅ ZIP package created successfully: ${outputPath}`);
    console.log(`📦 Package size: ${Math.round(zipData.length / 1024)} KB`);
    
    // List contents
    console.log('\n📁 Package contents:');
    for (const file of zip.files) {
      console.log(`   ${file.name}`);
    }
    
    return outputPath;
  } catch (error) {
    console.error('❌ Error creating ZIP package:', error);
    throw error;
  }
}

createGitHubDeploymentZip().catch(console.error);