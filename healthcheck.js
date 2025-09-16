#!/usr/bin/env node

/**
 * Health check script for Docker container
 * Verifies that the Next.js application is running and responding
 */

const http = require('http');

const options = {
  host: 'localhost',
  port: process.env.PORT || 3000,
  path: '/api/health',
  timeout: 2000,
  method: 'GET'
};

const request = http.request(options, (res) => {
  console.log(`Health check status: ${res.statusCode}`);
  if (res.statusCode === 200) {
    process.exit(0);
  } else {
    process.exit(1);
  }
});

request.on('timeout', () => {
  console.log('Health check timeout');
  request.destroy();
  process.exit(1);
});

request.on('error', (err) => {
  console.log('Health check error:', err.message);
  process.exit(1);
});

request.end();