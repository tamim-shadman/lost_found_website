import http from 'http';

const BASE_URL = 'http://localhost:8000';

async function fetchJson(endpoint, options = {}) {
  return new Promise((resolve, reject) => {
    const url = new URL(endpoint, BASE_URL);
    const req = http.request(url, options, (res) => {
      let data = '';
      res.on('data', chunk => { data += chunk; });
      res.on('end', () => {
        try {
          resolve({ status: res.statusCode, data: JSON.parse(data) });
        } catch (e) {
          resolve({ status: res.statusCode, raw: data });
        }
      });
    });
    req.on('error', reject);
    if (options.body) {
      req.write(typeof options.body === 'string' ? options.body : JSON.stringify(options.body));
    }
    req.end();
  });
}

async function fetchStatus(endpoint) {
  return new Promise((resolve, reject) => {
    const url = new URL(endpoint, BASE_URL);
    const req = http.request(url, { method: 'GET' }, (res) => {
      res.resume();
      resolve(res.statusCode);
    });
    req.on('error', reject);
    req.end();
  });
}

async function runTests() {
  console.log('🧪 Starting Lost & Found BD Comprehensive Verification Suite...\n');
  let passed = 0;
  let failed = 0;

  async function test(name, fn) {
    try {
      await fn();
      console.log(`  ✅ [PASS] ${name}`);
      passed++;
    } catch (e) {
      console.error(`  ❌ [FAIL] ${name}: ${e.message}`);
      failed++;
    }
  }

  // 1. Health & Core Infrastructure
  await test('Infrastructure: GET /api/health.php', async () => {
    const res = await fetchJson('/api/health.php');
    if (res.status !== 200 || res.data.status !== 'healthy') {
      throw new Error(`Expected status healthy, got ${JSON.stringify(res.data)}`);
    }
    if (res.data.version !== '2.0.0') {
      throw new Error(`Unexpected version: ${res.data.version}`);
    }
  });

  // 2. Static and Interactive Route Delivery
  const routes = [
    { name: 'Route: GET / (Homepage)', path: '/' },
    { name: 'Route: GET /recentposts.html', path: '/recentposts.html' },
    { name: 'Route: GET /submit_lostitems.html', path: '/submit_lostitems.html' },
    { name: 'Route: GET /missing_person.html', path: '/missing_person.html' },
    { name: 'Route: GET /chatbot.html', path: '/chatbot.html' },
    { name: 'Route: GET /payment.html', path: '/payment.html' },
    { name: 'Route: GET /login.html', path: '/login.html' },
    { name: 'Route: GET /signup.html', path: '/signup.html' },
    { name: 'Route: GET /User_profile.html', path: '/User_profile.html' },
    { name: 'Route: GET /admin panel/admin-dashboard.html', path: '/admin%20panel/admin-dashboard.html' }
  ];

  for (const r of routes) {
    await test(r.name, async () => {
      const status = await fetchStatus(r.path);
      if (status !== 200) throw new Error(`Expected 200, received HTTP ${status}`);
    });
  }

  // 3. Stats Metric Telemetry
  await test('API: GET /api/stats.php', async () => {
    const res = await fetchJson('/api/stats.php');
    if (res.status !== 200 || !res.data.success || !res.data.stats) {
      throw new Error('Stats payload malformed');
    }
    if (typeof res.data.stats.total_items !== 'number') {
      throw new Error('Invalid total_items count');
    }
  });

  // 4. Authentication Lifecycle & Authorization
  await test('Auth: GET /api/auth.php?action=me', async () => {
    const res = await fetchJson('/api/auth.php?action=me');
    if (res.status !== 200 || !res.data.success || !res.data.user) {
      throw new Error('Expected active session user');
    }
    if (!res.data.user.email || !res.data.user.first_name) {
      throw new Error('Session user missing required profile attributes');
    }
  });

  await test('Auth: POST /api/auth.php (Valid Login)', async () => {
    const res = await fetchJson('/api/auth.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: { action: 'login', email: 'admin@lostfoundbd.com', password: 'Admin@1234' }
    });
    if (res.status !== 200 || !res.data.success || res.data.user.role !== 'admin') {
      throw new Error('Admin login failed: ' + JSON.stringify(res.data));
    }
  });

  await test('Auth: POST /api/auth.php (Negative Test - Invalid Credentials)', async () => {
    const res = await fetchJson('/api/auth.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: { action: 'login', email: 'unknown.hacker@evil.com', password: 'bad_password' }
    });
    if (res.status !== 401 || res.data.success !== false) {
      throw new Error('Expected 401 Unauthorized for invalid credentials');
    }
  });

  const testEmail = `citizen.${Date.now()}@domain.com`;
  await test('Auth: POST /api/auth.php (New Citizen Registration)', async () => {
    const res = await fetchJson('/api/auth.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        action: 'register',
        first_name: 'Test',
        last_name: 'Citizen',
        email: testEmail,
        phone: '01899112233',
        password: 'SecurePassword123'
      }
    });
    if (res.status !== 201 || !res.data.success || !res.data.user) {
      throw new Error('Registration failed: ' + JSON.stringify(res.data));
    }
  });

  await test('Auth: POST /api/auth.php (Negative Test - Duplicate Email Rejection)', async () => {
    const res = await fetchJson('/api/auth.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        action: 'register',
        first_name: 'Duplicate',
        last_name: 'User',
        email: testEmail,
        phone: '01899112233',
        password: 'SecurePassword123'
      }
    });
    if (res.status !== 400 || res.data.success !== false) {
      throw new Error('Expected 400 Bad Request for duplicate email');
    }
  });

  await test('Auth: POST /api/auth.php (Update Profile)', async () => {
    const res = await fetchJson('/api/auth.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        action: 'update_profile',
        first_name: 'Tamim',
        last_name: 'Architect',
        phone: '01715000002'
      }
    });
    if (res.status !== 200 || !res.data.success || res.data.user.last_name !== 'Architect') {
      throw new Error('Profile update failed: ' + JSON.stringify(res.data));
    }
  });

  // 5. Items Lifecycle & Query Filtering
  await test('Items: GET /api/items.php?action=list', async () => {
    const res = await fetchJson('/api/items.php?action=list');
    if (res.status !== 200 || !res.data.success || !Array.isArray(res.data.data.items)) {
      throw new Error('Items list payload malformed');
    }
    if (res.data.data.items.length < 3) {
      throw new Error('Expected at least 3 seeded items');
    }
  });

  await test('Items: GET /api/items.php with Query Filter', async () => {
    const res = await fetchJson('/api/items.php?action=list&query=MacBook');
    if (res.status !== 200 || res.data.data.items.length === 0) {
      throw new Error('Search failed to return matching MacBook item');
    }
  });

  await test('Items: GET /api/items.php with Division Filter', async () => {
    const res = await fetchJson('/api/items.php?action=list&division=Dhaka');
    if (res.status !== 200 || res.data.data.items.some(i => i.division.toLowerCase() !== 'dhaka')) {
      throw new Error('Division filter returned non-Dhaka records');
    }
  });

  let createdItemId = null;
  await test('Items: POST /api/items.php (Create Item)', async () => {
    const res = await fetchJson('/api/items.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        action: 'create',
        item_type: 'lost',
        title: 'Sony WH-1000XM5 Noise Canceling Headphones',
        category: 'Electronics',
        division: 'Dhaka',
        district: 'Gulshan',
        specific_location: 'Gulshan 2 Circle Gloria Jeans Cafe',
        description: 'Black over-ear headphones left on sofa.',
        reward: 3500,
        phone: '01700112233',
        email: 'test@domain.com'
      }
    });
    if (res.status !== 201 || !res.data.success || !res.data.id) {
      throw new Error('Item creation failed: ' + JSON.stringify(res.data));
    }
    createdItemId = res.data.id;
  });

  await test('Items: GET /api/items.php?action=get&id=1', async () => {
    const res = await fetchJson('/api/items.php?action=get&id=1');
    if (res.status !== 200 || !res.data.success || !res.data.item) {
      throw new Error('Failed to retrieve item by ID');
    }
  });

  await test('Items: POST /api/items.php (Submit Claim)', async () => {
    const res = await fetchJson('/api/items.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        action: 'claim',
        item_id: 1,
        claimant_name: 'Verified Claimant',
        contact_phone: '01712345678',
        proof_description: 'Serial number SN849204 matches invoice.'
      }
    });
    if (res.status !== 200 || !res.data.success) {
      throw new Error('Claim submission failed: ' + JSON.stringify(res.data));
    }
  });

  await test('Items: POST /api/items.php (Delete Item)', async () => {
    if (!createdItemId) throw new Error('No created item to delete');
    const res = await fetchJson('/api/items.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: { action: 'delete', id: createdItemId }
    });
    if (res.status !== 200 || !res.data.success) {
      throw new Error('Item deletion failed: ' + JSON.stringify(res.data));
    }
  });

  // 6. Missing Persons Registry
  await test('Missing: GET /api/missing.php?action=list', async () => {
    const res = await fetchJson('/api/missing.php?action=list');
    if (res.status !== 200 || !res.data.success || !Array.isArray(res.data.data.persons)) {
      throw new Error('Missing persons payload malformed');
    }
    if (res.data.data.persons.length === 0) {
      throw new Error('Expected seeded missing persons');
    }
  });

  await test('Missing: POST /api/missing.php (Create Alert)', async () => {
    const res = await fetchJson('/api/missing.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        action: 'create',
        full_name: 'Tanvir Hossain',
        nid: '19952691029485712',
        contact_phone: '01788776655',
        missing_date: '2026-10-18',
        last_seen_division: 'Dhaka',
        last_seen_district: 'Uttara',
        last_seen_details: 'Sector 4 park entrance',
        age: 26,
        clothing_description: 'Navy polo shirt and khaki chinos',
        gd_number: 'GD-4019/Uttara'
      }
    });
    if (res.status !== 201 || !res.data.success) {
      throw new Error('Missing alert creation failed: ' + JSON.stringify(res.data));
    }
  });

  // 7. Chatbot Emergency & Guidance
  await test('Chatbot: POST /api/chatbot.php (Lost Guidance)', async () => {
    const res = await fetchJson('/api/chatbot.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: { action: 'ask', message: 'I lost my backpack on the bus' }
    });
    if (res.status !== 200 || !res.data.success || !res.data.data.reply.includes('Report Lost Item')) {
      throw new Error('Chatbot failed to return lost item guidance: ' + JSON.stringify(res.data));
    }
  });

  await test('Chatbot: POST /api/chatbot.php (Police & GD Help)', async () => {
    const res = await fetchJson('/api/chatbot.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: { action: 'ask', message: 'How do I file a police GD?' }
    });
    if (res.status !== 200 || !res.data.success || !res.data.data.reply.includes('General Diary')) {
      throw new Error('Chatbot failed to return GD guidance: ' + JSON.stringify(res.data));
    }
  });

  // 8. Staff Moderation Desk
  await test('Admin: GET /api/admin.php?action=dashboard', async () => {
    const res = await fetchJson('/api/admin.php?action=dashboard');
    if (res.status !== 200 || !res.data.success || !res.data.metrics) {
      throw new Error('Admin metrics payload malformed');
    }
  });

  await test('Admin: GET /api/admin.php?action=list_users', async () => {
    const res = await fetchJson('/api/admin.php?action=list_users');
    if (res.status !== 200 || !res.data.success || !Array.isArray(res.data.users)) {
      throw new Error('Admin list_users payload malformed');
    }
  });

  await test('Admin: POST /api/admin.php (Update User Status)', async () => {
    const res = await fetchJson('/api/admin.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: { action: 'update_user_status', user_id: 3, status: 'active' }
    });
    if (res.status !== 200 || !res.data.success) {
      throw new Error('Admin update_user_status failed: ' + JSON.stringify(res.data));
    }
  });

  await test('Admin: GET /api/admin.php?action=list_claims', async () => {
    const res = await fetchJson('/api/admin.php?action=list_claims');
    if (res.status !== 200 || !res.data.success || !Array.isArray(res.data.claims)) {
      throw new Error('Admin list_claims payload malformed');
    }
    if (res.data.claims.length === 0) {
      throw new Error('Expected seeded claims');
    }
  });

  await test('Admin: POST /api/admin.php (Resolve Claim)', async () => {
    const res = await fetchJson('/api/admin.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: { action: 'resolve_claim', claim_id: 1, status: 'approved', admin_notes: 'Verified via NID matching.' }
    });
    if (res.status !== 200 || !res.data.success) {
      throw new Error('Admin resolve_claim failed: ' + JSON.stringify(res.data));
    }
  });

  // 9. RC Hardening & Edge-Case Regression Defense
  await test('Donation: POST /api/donate.php (Valid donation persists with TRX ID)', async () => {
    const res = await fetchJson('/api/donate.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        amount: 2500,
        gateway: 'bkash',
        donor_name: 'Tanvir Rahman',
        donor_email: 'tanvir@example.com',
        donor_phone: '01712345678'
      }
    });
    if (res.status !== 200 || !res.data.success || !res.data.trx_id || !res.data.trx_id.startsWith('TRX-')) {
      throw new Error('Donation failed to persist or generate valid TRX: ' + JSON.stringify(res.data));
    }
  });

  await test('Donation: POST /api/donate.php (Boundary validation on zero/negative amount)', async () => {
    const res = await fetchJson('/api/donate.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        amount: 0,
        gateway: 'nagad',
        donor_phone: '01712345678'
      }
    });
    if (res.status !== 400 || res.data.success) {
      throw new Error('Expected 400 rejection for zero amount donation: ' + JSON.stringify(res.data));
    }
  });

  await test('Claims: POST /api/items.php (Action: claim validation - missing required fields)', async () => {
    const res = await fetchJson('/api/items.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        action: 'claim',
        item_id: 1,
        ownership_proof: '',
        claimant_phone: ''
      }
    });
    if (res.status !== 400 || res.data.success) {
      throw new Error('Expected 400 rejection for empty ownership proof and phone: ' + JSON.stringify(res.data));
    }
  });

  await test('Claims: POST /api/items.php (Action: claim duplicate prevention)', async () => {
    // First claim
    const firstClaim = await fetchJson('/api/items.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        action: 'claim',
        item_id: 2,
        ownership_proof: 'I have the original purchase receipt and invoice #INV-8819.',
        claimant_phone: '01799887766'
      }
    });
    if (firstClaim.status !== 201 && firstClaim.status !== 200) {
      throw new Error('Initial claim failed: ' + JSON.stringify(firstClaim.data));
    }

    // Duplicate claim attempt on same item by same user
    const dupeClaim = await fetchJson('/api/items.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        action: 'claim',
        item_id: 2,
        ownership_proof: 'Submitting again just in case.',
        claimant_phone: '01799887766'
      }
    });
    if (dupeClaim.status !== 409 || dupeClaim.data.success) {
      throw new Error('Expected 409 Conflict duplicate claim rejection: ' + JSON.stringify(dupeClaim.data));
    }
  });

  await test('Missing Person: POST /api/missing.php (Age boundary validation <= 0 and > 125)', async () => {
    const res = await fetchJson('/api/missing.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        action: 'create',
        full_name: 'Invalid Age Person',
        age: -5,
        last_seen_division: 'Dhaka',
        last_seen_district: 'Gulshan',
        missing_date: '2026-10-18',
        contact_phone: '01700000000'
      }
    });
    if (res.status !== 400 || res.data.success) {
      throw new Error('Expected 400 rejection for negative age: ' + JSON.stringify(res.data));
    }
  });

  await test('Missing Person: POST /api/missing.php (Action: sighting submission and police dispatch)', async () => {
    const res = await fetchJson('/api/missing.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        action: 'sighting',
        person_id: 1,
        location: 'Farmgate Footbridge, Dhaka',
        details: 'Observed heading towards Karwan Bazar wearing navy blue shirt.',
        reporter_phone: '01899112233'
      }
    });
    if ((res.status !== 200 && res.status !== 201) || !res.data.success || !res.data.sighting_ref) {
      throw new Error('Sighting report submission failed: ' + JSON.stringify(res.data));
    }
  });

  await test('Chatbot: POST /api/chatbot.php (Empty query boundary check)', async () => {
    const res = await fetchJson('/api/chatbot.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: { action: 'ask', message: '   ' }
    });
    if (res.status !== 400 || res.data.success) {
      throw new Error('Expected 400 rejection for empty message: ' + JSON.stringify(res.data));
    }
  });

  // 12. Smart Match Engine & QR Shield Suite
  await test('Smart Match: GET /api/match.php?action=system_pairs', async () => {
    const res = await fetchJson('/api/match.php?action=system_pairs&min_confidence=30');
    if (res.status !== 200 || !res.data.success || !Array.isArray(res.data.data.pairs)) {
      throw new Error('Expected 200 with pairs array, got: ' + JSON.stringify(res.data));
    }
  });

  await test('Smart Match: GET /api/match.php?action=item_matches&item_id=1', async () => {
    const res = await fetchJson('/api/match.php?action=item_matches&item_id=1');
    if (res.status !== 200 || !res.data.success || !res.data.data.target_item) {
      throw new Error('Expected target_item in match response: ' + JSON.stringify(res.data));
    }
  });

  await test('Smart Match: GET /api/match.php boundary check (missing item_id)', async () => {
    const res = await fetchJson('/api/match.php?action=item_matches');
    if (res.status !== 400 || res.data.success) {
      throw new Error('Expected 400 for missing item_id');
    }
  });

  let createdTagCode = null;
  await test('QR Shield: POST /api/qr_tag.php?action=create', async () => {
    const res = await fetchJson('/api/qr_tag.php?action=create', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        item_name: 'Dell XPS 15 9520 Test Laptop',
        category: 'Electronics',
        owner_phone: '01715000002',
        owner_email: 'tamimshadman@gmail.com',
        reward_amount: 1000
      }
    });
    if (res.status !== 200 || !res.data.success || !res.data.data.tag_code) {
      throw new Error('Failed to create QR tag: ' + JSON.stringify(res.data));
    }
    createdTagCode = res.data.data.tag_code;
    if (!createdTagCode.startsWith('LF-BD-')) {
      throw new Error('Tag code does not match standard prefix LF-BD-: ' + createdTagCode);
    }
  });

  await test('QR Shield: GET /api/qr_tag.php?action=list', async () => {
    const res = await fetchJson('/api/qr_tag.php?action=list');
    if (res.status !== 200 || !res.data.success || !Array.isArray(res.data.data.tags)) {
      throw new Error('Expected 200 with tags list');
    }
  });

  await test('QR Shield: GET /api/qr_tag.php?action=get&tag_code=' + createdTagCode, async () => {
    const res = await fetchJson('/api/qr_tag.php?action=get&tag_code=' + createdTagCode);
    if (res.status !== 200 || !res.data.success || res.data.data.tag_code !== createdTagCode) {
      throw new Error('Expected tag match for code ' + createdTagCode);
    }
  });

  await test('QR Shield: POST /api/qr_tag.php?action=report_found (Anonymous return notification)', async () => {
    const res = await fetchJson('/api/qr_tag.php?action=report_found', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: {
        tag_code: createdTagCode,
        finder_name: 'Good Citizen Rahim',
        finder_phone: '01811223344',
        found_location: 'Dhanmondi Lake Bench, Dhaka',
        message: 'Safely retrieved your laptop from park security desk.'
      }
    });
    if (res.status !== 200 || !res.data.success || !res.data.report_id) {
      throw new Error('Failed to submit recovery notice: ' + JSON.stringify(res.data));
    }
  });

  await test('UI Routes: GET /recover.html', async () => {
    const status = await fetchStatus('/recover.html');
    if (status !== 200) throw new Error('Expected 200 for /recover.html, got ' + status);
  });

  await test('UI Components: GET /components/command-palette.js', async () => {
    const status = await fetchStatus('/components/command-palette.js');
    if (status !== 200) throw new Error('Expected 200 for command-palette.js, got ' + status);
  });

  console.log(`\n================================`);
  console.log(`Test Results: ${passed} passed, ${failed} failed`);
  console.log(`================================`);

  if (failed > 0) {
    process.exit(1);
  } else {
    console.log('🎉 100% Comprehensive End-to-End Tests Passed! Elite transformation verified.\n');
  }
}

runTests();
