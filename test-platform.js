// WordPress Plugin Configuration Testing Platform
// Test various WordPress environments and plugin configurations

import { createRequire } from 'module';
const require = createRequire(import.meta.url);

// Test configurations for different WordPress setups
const testConfigurations = [
  {
    name: "Standard WordPress 6.4",
    version: "6.4",
    php: "8.1",
    theme: "twentytwentythree",
    plugins: ["elementor", "gutenberg"],
    https: true
  },
  {
    name: "WordPress 5.8 (Legacy)",
    version: "5.8", 
    php: "7.4",
    theme: "twentytwentyone",
    plugins: ["classic-editor"],
    https: false
  },
  {
    name: "WordPress Multisite",
    version: "6.3",
    php: "8.0",
    theme: "astra",
    plugins: ["elementor-pro", "gutenberg", "wp-super-cache"],
    https: true,
    multisite: true
  },
  {
    name: "High Security WordPress",
    version: "6.4",
    php: "8.2",
    theme: "generatepress",
    plugins: ["wordfence", "sucuri-security"],
    https: true,
    security: "high"
  }
];

// API endpoint tests
const apiTests = [
  {
    endpoint: "/api/research-papers",
    method: "GET",
    params: "?limit=6",
    expected: "array of papers"
  },
  {
    endpoint: "/embed",
    method: "GET", 
    params: "?view=papers&limit=3",
    expected: "HTML iframe content"
  },
  {
    endpoint: "/api/auth/user",
    method: "GET",
    params: "",
    expected: "user data or null"
  }
];

// Shortcode tests
const shortcodeTests = [
  '[paul_portfolio]',
  '[paul_portfolio view="papers"]',
  '[paul_portfolio limit="6"]',
  '[paul_portfolio height="800"]',
  '[paul_portfolio view="papers" limit="10" height="900"]',
  '[paul_portfolio_embed type="static"]'
];

async function makeRequest(method, path, data = null, headers = {}) {
  const baseUrl = 'http://localhost:5000';
  
  try {
    const response = await fetch(`${baseUrl}${path}`, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'User-Agent': 'WordPress-Plugin-Test/3.0',
        ...headers
      },
      body: data ? JSON.stringify(data) : null
    });
    
    const responseData = await response.text();
    
    return {
      status: response.status,
      ok: response.ok,
      headers: Object.fromEntries(response.headers.entries()),
      data: responseData
    };
  } catch (error) {
    return {
      status: 0,
      ok: false,
      error: error.message
    };
  }
}

async function runTests() {
  console.log('🔧 WordPress Plugin Configuration Testing Platform');
  console.log('================================================\n');

  // Test 1: API Endpoint Connectivity
  console.log('📡 Testing API Endpoints:');
  for (const test of apiTests) {
    const url = `${test.endpoint}${test.params}`;
    const result = await makeRequest(test.method, url);
    
    console.log(`  ${test.method} ${url}`);
    console.log(`    Status: ${result.status} ${result.ok ? '✅' : '❌'}`);
    
    if (result.ok && result.data) {
      const preview = result.data.substring(0, 100).replace(/\n/g, ' ');
      console.log(`    Preview: ${preview}...`);
    }
    
    if (result.error) {
      console.log(`    Error: ${result.error}`);
    }
    console.log();
  }

  // Test 2: WordPress Configuration Compatibility
  console.log('🔧 Testing WordPress Configurations:');
  for (const config of testConfigurations) {
    console.log(`\n  Configuration: ${config.name}`);
    console.log(`    WordPress: ${config.version} | PHP: ${config.php}`);
    console.log(`    Theme: ${config.theme} | HTTPS: ${config.https ? 'Yes' : 'No'}`);
    console.log(`    Plugins: ${config.plugins.join(', ')}`);
    
    // Test plugin compatibility
    const compatibility = {
      wordpress: config.version >= '5.0' ? '✅' : '❌',
      php: config.php >= '7.4' ? '✅' : '❌',
      https: config.https ? '✅' : '⚠️',
      security: config.security === 'high' ? '🔒' : '✅'
    };
    
    console.log(`    Compatibility: WP ${compatibility.wordpress} | PHP ${compatibility.php} | HTTPS ${compatibility.https} | Security ${compatibility.security}`);
  }

  // Test 3: Shortcode Parsing
  console.log('\n📝 Testing Shortcode Configurations:');
  for (const shortcode of shortcodeTests) {
    console.log(`  ${shortcode}`);
    
    // Parse shortcode attributes
    const matches = shortcode.match(/\[(\w+)([^\]]*)\]/);
    if (matches) {
      const [, tag, attrs] = matches;
      const attributes = {};
      
      // Parse attributes
      const attrMatches = attrs.matchAll(/(\w+)="([^"]*)"/g);
      for (const [, key, value] of attrMatches) {
        attributes[key] = value;
      }
      
      console.log(`    Tag: ${tag}`);
      console.log(`    Attributes: ${JSON.stringify(attributes)}`);
      
      // Validate attributes
      const validations = {
        view: attributes.view ? ['papers', 'full'].includes(attributes.view) : true,
        limit: attributes.limit ? (parseInt(attributes.limit) > 0 && parseInt(attributes.limit) <= 20) : true,
        height: attributes.height ? (parseInt(attributes.height) >= 400 && parseInt(attributes.height) <= 1200) : true
      };
      
      const isValid = Object.values(validations).every(v => v);
      console.log(`    Validation: ${isValid ? '✅' : '❌'}`);
    }
    console.log();
  }

  // Test 4: Security Headers
  console.log('🔒 Testing Security Headers:');
  const securityTest = await makeRequest('GET', '/embed?view=papers');
  
  if (securityTest.ok) {
    const headers = securityTest.headers;
    const securityChecks = {
      'X-Frame-Options': headers['x-frame-options'] || 'Missing',
      'Content-Security-Policy': headers['content-security-policy'] ? 'Present' : 'Missing',
      'X-Content-Type-Options': headers['x-content-type-options'] || 'Missing',
      'Referrer-Policy': headers['referrer-policy'] || 'Missing'
    };
    
    for (const [header, status] of Object.entries(securityChecks)) {
      const icon = status === 'Missing' ? '❌' : '✅';
      console.log(`  ${header}: ${status} ${icon}`);
    }
  }

  // Test 5: Performance Metrics
  console.log('\n⚡ Performance Testing:');
  const performanceTests = [
    { endpoint: '/api/research-papers', name: 'API Response' },
    { endpoint: '/embed', name: 'Embed Route' },
    { endpoint: '/api/auth/user', name: 'Auth Check' }
  ];

  for (const test of performanceTests) {
    const startTime = Date.now();
    const result = await makeRequest('GET', test.endpoint);
    const endTime = Date.now();
    const duration = endTime - startTime;
    
    console.log(`  ${test.name}: ${duration}ms ${result.ok ? '✅' : '❌'}`);
  }

  console.log('\n🎯 Configuration Testing Complete!');
  console.log('All tests executed. Review results above for compatibility status.');
}

// Run tests if called directly
runTests().catch(console.error);