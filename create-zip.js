const fs = require('fs');
const archiver = require('archiver');

async function createDocumentationZip() {
  const output = fs.createWriteStream('Paul_Stephensen_Portfolio_Complete_Documentation.zip');
  const archive = archiver('zip', { zlib: { level: 9 } });

  // Files to include in the zip
  const files = [
    'Paul_Stephensen_Portfolio_Documentation.html',
    'Paul_Stephensen_Portfolio_Documentation.txt',
    'APPLICATION-DOCUMENTATION.md',
    'USABILITY-STANDARDS-COMPLIANCE.md',
    'README-DOCUMENTATION-PACKAGE.md',
    'replit.md'
  ];

  return new Promise((resolve, reject) => {
    output.on('close', () => {
      const sizeKB = (archive.pointer() / 1024).toFixed(1);
      console.log(`Created: Paul_Stephensen_Portfolio_Complete_Documentation.zip`);
      console.log(`Size: ${sizeKB} KB (${archive.pointer()} bytes)`);
      console.log('\nZip file contents:');
      
      // List the files we added
      files.forEach(file => {
        if (fs.existsSync(file)) {
          const stats = fs.statSync(file);
          const fileSizeKB = (stats.size / 1024).toFixed(1);
          console.log(`  ✓ ${file.padEnd(45)} ${fileSizeKB.padStart(8)} KB`);
        }
      });
      resolve();
    });

    archive.on('error', reject);
    archive.pipe(output);

    // Add each file to the archive
    files.forEach(file => {
      if (fs.existsSync(file)) {
        archive.file(file, { name: file });
        console.log(`Added: ${file}`);
      } else {
        console.log(`Warning: ${file} not found`);
      }
    });

    archive.finalize();
  });
}

createDocumentationZip().catch(console.error);