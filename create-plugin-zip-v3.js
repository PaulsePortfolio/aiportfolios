import fs from 'fs';

class SimpleZip {
  constructor() {
    this.files = [];
  }

  addFile(filename, content) {
    const data = Buffer.from(content, 'utf8');
    this.files.push({
      filename,
      data,
      crc32: this.crc32(data),
      size: data.length
    });
  }

  createLocalFileHeader(filename, data) {
    const filenameBuffer = Buffer.from(filename, 'utf8');
    const header = Buffer.alloc(30 + filenameBuffer.length);
    
    header.writeUInt32LE(0x04034b50, 0); // Local file header signature
    header.writeUInt16LE(20, 4);         // Version needed to extract
    header.writeUInt16LE(0, 6);          // General purpose bit flag
    header.writeUInt16LE(0, 8);          // Compression method (stored)
    header.writeUInt16LE(0, 10);         // File last modification time
    header.writeUInt16LE(0, 12);         // File last modification date
    header.writeUInt32LE(this.crc32(data), 14); // CRC-32
    header.writeUInt32LE(data.length, 18);      // Compressed size
    header.writeUInt32LE(data.length, 22);      // Uncompressed size
    header.writeUInt16LE(filenameBuffer.length, 26); // File name length
    header.writeUInt16LE(0, 28);         // Extra field length
    
    filenameBuffer.copy(header, 30);
    
    return header;
  }

  crc32(data) {
    const table = [];
    for (let i = 0; i < 256; i++) {
      let c = i;
      for (let j = 0; j < 8; j++) {
        if (c & 1) {
          c = 0xEDB88320 ^ (c >>> 1);
        } else {
          c = c >>> 1;
        }
      }
      table[i] = c;
    }

    let crc = 0xFFFFFFFF;
    for (let i = 0; i < data.length; i++) {
      crc = table[(crc ^ data[i]) & 0xFF] ^ (crc >>> 8);
    }
    return (crc ^ 0xFFFFFFFF) >>> 0;
  }

  generate() {
    const buffers = [];
    const centralDirectory = [];
    let offset = 0;

    // Write local file entries
    for (const file of this.files) {
      const localHeader = this.createLocalFileHeader(file.filename, file.data);
      buffers.push(localHeader);
      buffers.push(file.data);
      
      // Store info for central directory
      centralDirectory.push({
        filename: file.filename,
        crc32: file.crc32,
        size: file.size,
        offset: offset
      });
      
      offset += localHeader.length + file.data.length;
    }

    const centralDirStart = offset;

    // Write central directory entries
    for (const entry of centralDirectory) {
      const cdEntry = this.createCentralDirectoryEntry(entry);
      buffers.push(cdEntry);
      offset += cdEntry.length;
    }

    // Write end of central directory record
    const endRecord = this.createEndOfCentralDirectory(centralDirStart, offset - centralDirStart);
    buffers.push(endRecord);

    return Buffer.concat(buffers);
  }

  createCentralDirectoryEntry(file) {
    const filenameBuffer = Buffer.from(file.filename, 'utf8');
    const entry = Buffer.alloc(46 + filenameBuffer.length);
    
    entry.writeUInt32LE(0x02014b50, 0);  // Central directory file header signature
    entry.writeUInt16LE(20, 4);          // Version made by
    entry.writeUInt16LE(20, 6);          // Version needed to extract
    entry.writeUInt16LE(0, 8);           // General purpose bit flag
    entry.writeUInt16LE(0, 10);          // Compression method
    entry.writeUInt16LE(0, 12);          // Last mod file time
    entry.writeUInt16LE(0, 14);          // Last mod file date
    entry.writeUInt32LE(file.crc32, 16); // CRC-32
    entry.writeUInt32LE(file.size, 20);  // Compressed size
    entry.writeUInt32LE(file.size, 24);  // Uncompressed size
    entry.writeUInt16LE(filenameBuffer.length, 28); // File name length
    entry.writeUInt16LE(0, 30);          // Extra field length
    entry.writeUInt16LE(0, 32);          // File comment length
    entry.writeUInt16LE(0, 34);          // Disk number start
    entry.writeUInt16LE(0, 36);          // Internal file attributes
    entry.writeUInt32LE(0, 38);          // External file attributes
    entry.writeUInt32LE(file.offset, 42); // Relative offset of local header
    
    filenameBuffer.copy(entry, 46);
    
    return entry;
  }

  createEndOfCentralDirectory(centralDirStart, centralDirSize) {
    const record = Buffer.alloc(22);
    
    record.writeUInt32LE(0x06054b50, 0);     // End of central dir signature
    record.writeUInt16LE(0, 4);              // Number of this disk
    record.writeUInt16LE(0, 6);              // Number of disk with start of central directory
    record.writeUInt16LE(this.files.length, 8);  // Number of central directory records on this disk
    record.writeUInt16LE(this.files.length, 10); // Total number of central directory records
    record.writeUInt32LE(centralDirSize, 12);     // Size of central directory
    record.writeUInt32LE(centralDirStart, 16);    // Offset of start of central directory
    record.writeUInt16LE(0, 20);             // ZIP file comment length
    
    return record;
  }
}

async function createWordPressPluginZip() {
  const zip = new SimpleZip();
  
  try {
    // Add main plugin file (v3.1 fixed version)
    const pluginContent = fs.readFileSync('wordpress-deployment-package/paul-stephensen-portfolio-plugin-v3-fixed.php', 'utf8');
    zip.addFile('paul-stephensen-portfolio-plugin.php', pluginContent);
    
    // Add README
    const readmeContent = fs.readFileSync('wordpress-deployment-package/README-v3.1.md', 'utf8');
    zip.addFile('README.md', readmeContent);
    
    // Add license
    const licenseContent = fs.readFileSync('wordpress-deployment-package/LICENSE-v3.1.txt', 'utf8');
    zip.addFile('LICENSE.txt', licenseContent);
    
    // Generate ZIP
    const zipBuffer = zip.generate();
    const outputPath = 'wordpress-deployment-package/paul-stephensen-portfolio-plugin-v3.1-complete.zip';
    
    fs.writeFileSync(outputPath, zipBuffer);
    
    console.log(`✓ WordPress plugin ZIP created: ${outputPath}`);
    console.log(`✓ File size: ${(zipBuffer.length / 1024).toFixed(1)}KB`);
    console.log(`✓ Contains: ${zip.files.length} files`);
    console.log('✓ Ready for WordPress installation');
    
    return outputPath;
    
  } catch (error) {
    console.error('Error creating plugin ZIP:', error.message);
    throw error;
  }
}

// Execute if run directly
createWordPressPluginZip().catch(console.error);