import http from 'http';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const PORT = process.env.PORT || 8000;

// MIME Types
const MIME_TYPES = {
  '.html': 'text/html; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.js': 'text/javascript; charset=utf-8',
  '.mjs': 'text/javascript; charset=utf-8',
  '.json': 'application/json; charset=utf-8',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.gif': 'image/gif',
  '.svg': 'image/svg+xml',
  '.webp': 'image/webp',
  '.ico': 'image/x-icon',
};

// In-Memory Seed State for Dev Server
const state = {
  users: [
    { id: 1, first_name: 'System', last_name: 'Administrator', email: 'admin@lostfoundbd.com', phone: '01711000001', role: 'admin', status: 'active' },
    { id: 2, first_name: 'Tamim', last_name: 'Shadman', email: 'tamimshadman@gmail.com', phone: '01715000002', role: 'user', status: 'active' },
    { id: 3, first_name: 'Ratul', last_name: 'Bhattacharjee', email: 'ratul.bhattarcharjee@northsouth.edu', phone: '01915286868', role: 'user', status: 'active' },
  ],
  items: [
    {
      id: 1,
      user_id: 2,
      item_type: 'lost',
      title: 'MacBook Pro 14" Space Gray',
      category: 'Electronics',
      description: 'Lost in a black Tomtoc sleeve containing university project and research files. Has a GitHub sticker on the back lid.',
      division: 'Dhaka',
      district: 'Dhaka',
      specific_location: 'Hazrat Shahjalal International Airport, Terminal 2',
      surrounding_details: 'Near boarding gate 6 waiting lounge seats.',
      date_lost_or_found: '2026-10-15',
      picture: 'images/logan-weaver-lgnwvr-jM_xJyX-sPI-unsplash.jpg',
      contact_phone: '01715000002',
      contact_email: 'tamimshadman@gmail.com',
      reward_amount: 5000.00,
      status: 'verified',
      created_at: '2026-10-15 14:30:00'
    },
    {
      id: 2,
      user_id: 3,
      item_type: 'found',
      title: 'Black Leather Bellroy Wallet',
      category: 'Wallet/Cards',
      description: 'Found black leather wallet containing a National ID card, student ID from NSU, and BRAC Bank debit card.',
      division: 'Dhaka',
      district: 'Dhaka',
      specific_location: 'Bashundhara R/A Gate 1 Bus Stand',
      surrounding_details: 'Found on the bench near the university shuttle stop.',
      date_lost_or_found: '2026-10-16',
      picture: 'images/OIP.jpeg',
      contact_phone: '01915286868',
      contact_email: 'ratul.bhattarcharjee@northsouth.edu',
      reward_amount: 0.00,
      status: 'verified',
      created_at: '2026-10-16 09:15:00'
    },
    {
      id: 3,
      user_id: 2,
      item_type: 'found',
      title: 'iPhone 15 Pro Titanium Blue',
      category: 'Electronics',
      description: 'Found phone with cracked screen protector in a clear case with a picture of a cat inside.',
      division: 'Chattogram',
      district: 'Chattogram',
      specific_location: 'GEC Circle, Well Food Bakery',
      surrounding_details: 'Left on table 4 beside the beverage counter.',
      date_lost_or_found: '2026-10-17',
      picture: 'images/pexels-fernando-arcos-190819.jpg',
      contact_phone: '01812345678',
      contact_email: 'gec.recovery@gmail.com',
      reward_amount: 0.00,
      status: 'verified',
      created_at: '2026-10-17 11:20:00'
    },
    {
      id: 4,
      user_id: 3,
      item_type: 'lost',
      title: 'Official Passport & Work Permit Documents',
      category: 'Documents',
      description: 'Blue plastic folder containing original Bangladeshi passport and UK visa endorsement papers.',
      division: 'Sylhet',
      district: 'Sylhet',
      specific_location: 'Sylhet Railway Station Platform 1',
      surrounding_details: 'Dropped while boarding the Upaban Express.',
      date_lost_or_found: '2026-10-14',
      picture: 'images/default_item.jpg',
      contact_phone: '01719876543',
      contact_email: 'sylhet.lost@gmail.com',
      reward_amount: 10000.00,
      status: 'verified',
      created_at: '2026-10-14 18:40:00'
    },
    {
      id: 5,
      user_id: 1,
      item_type: 'found',
      title: 'Golden Retriever Puppy (Red Collar)',
      category: 'Pets',
      description: 'Friendly golden retriever puppy found wandering near the lake. Wearing a red leather collar without a tag.',
      division: 'Dhaka',
      district: 'Dhaka',
      specific_location: 'Dhanmondi Lake, Road 8/A',
      surrounding_details: 'Found playing near the amphitheater stairs.',
      date_lost_or_found: '2026-10-17',
      picture: 'images/default_item.jpg',
      contact_phone: '01711000001',
      contact_email: 'admin@lostfoundbd.com',
      reward_amount: 0.00,
      status: 'verified',
      created_at: '2026-10-17 16:50:00'
    }
  ],
  missing: [
    {
      id: 1,
      user_id: 2,
      full_name: 'Mohammad Abdul Rahim',
      nid: '19582691234567890',
      contact_phone: '01711223344',
      emergency_phone: '01899887766',
      missing_date: '2026-10-10',
      last_seen_division: 'Dhaka',
      last_seen_district: 'Mirpur',
      last_seen_details: 'Last seen leaving home for Friday prayers at Mirpur-10 Central Mosque.',
      age: 68,
      gender: 'male',
      height: '5 ft 6 in',
      hair_color: 'Gray/White',
      eye_color: 'Brown',
      clothing_description: 'White Panjabi, white Pajama, and brown leather sandals.',
      distinguishing_features: 'Mild memory loss / dementia symptoms. Small scar on left forehead.',
      photo: 'images/default_person.jpg',
      gd_number: 'GD-8492/Mirpur',
      status: 'missing',
      created_at: '2026-10-10 12:00:00'
    },
    {
      id: 2,
      user_id: 3,
      full_name: 'Sadia Akter Mim',
      nid: '19982619472658193',
      contact_phone: '01999881122',
      emergency_phone: '01700112233',
      missing_date: '2026-10-12',
      last_seen_division: 'Chattogram',
      last_seen_district: 'Agrabad',
      last_seen_details: 'Last seen waiting near Badamtoli intersection around 5:30 PM.',
      age: 24,
      gender: 'female',
      height: '5 ft 2 in',
      hair_color: 'Black',
      eye_color: 'Dark Brown',
      clothing_description: 'Navy blue Kurti, white scarf, carrying a brown canvas backpack.',
      distinguishing_features: 'Wears round metallic spectacles.',
      photo: 'images/default_person.jpg',
      gd_number: 'GD-1042/Agrabad',
      status: 'missing',
      created_at: '2026-10-12 17:30:00'
    }
  ],
  claims: [
    {
      id: 1,
      item_id: 2,
      user_id: 2,
      item_title: 'Black Leather Bellroy Wallet',
      claimant_name: 'Tamim Shadman',
      contact_phone: '01715000002',
      proof_description: 'National ID ending in 4567, NSU Student ID #20123456, BRAC Bank debit card under my name.',
      status: 'pending',
      admin_notes: 'Claim under review with photo ID verification.',
      created_at: '2026-10-16 11:20:00'
    },
    {
      id: 2,
      item_id: 3,
      user_id: 3,
      item_title: 'iPhone 15 Pro Titanium Blue',
      claimant_name: 'Ratul Bhattacharjee',
      contact_phone: '01915286868',
      proof_description: 'IMEI number ending in 9812. Photo of cat inside clear phone case matches my pet Simba.',
      status: 'approved',
      admin_notes: 'Serial number verified via original box receipt.',
      created_at: '2026-10-17 14:05:00'
    }
  ],
  currentUser: {
    id: 2,
    first_name: 'Tamim',
    last_name: 'Shadman',
    email: 'tamimshadman@gmail.com',
    phone: '01715000002',
    role: 'user',
    status: 'active'
  },
  messages: [],
  donations: [
    {
      id: 1,
      donor_name: 'Shadman Foundation',
      donor_email: 'charity@shadman.org',
      amount: 25000.00,
      currency: 'BDT',
      payment_method: 'Stripe Tokenized',
      stripe_payment_id: 'tx_seed_donation_1',
      status: 'succeeded',
      created_at: '2026-10-15 10:00:00'
    }
  ],
  qrTags: [
    {
      id: 1,
      user_id: 2,
      tag_code: 'LF-BD-A89K2',
      asset_name: 'Apple MacBook Pro 14 (Space Black)',
      category: 'Electronics',
      notes: 'Contains confidential research work. Emergency reward ৳5,000.',
      status: 'active',
      created_at: '2026-10-15 11:20:00'
    },
    {
      id: 2,
      user_id: 2,
      tag_code: 'LF-BD-7K9W2',
      asset_name: 'House & Toyota Car Keys',
      category: 'Keys',
      notes: 'Attached to black leather key organizer.',
      status: 'active',
      created_at: '2026-10-16 15:45:00'
    }
  ],
  recoveryLogs: []
};

// Route Helpers
const server = http.createServer((req, res) => {
  const reqUrl = new URL(req.url, `http://${req.headers.host}`);
  let pathname = decodeURIComponent(reqUrl.pathname);

  // Normalize route defaults
  if (pathname === '/' || pathname === '/index.php') {
    pathname = '/index.html';
  } else if (pathname.endsWith('.php')) {
    const htmlEquivalent = pathname.replace('.php', '.html');
    if (fs.existsSync(path.join(__dirname, htmlEquivalent))) {
      pathname = htmlEquivalent;
    }
  }

  // Handle Mock API endpoints
  if (pathname.startsWith('/api/') || pathname === '/action.php') {
    handleApiRequest(req, res, reqUrl);
    return;
  }

  // Handle Static File Serving
  let filePath = path.join(__dirname, pathname);

  fs.stat(filePath, (err, stats) => {
    if (err || !stats.isFile()) {
      // Check if it's a directory containing index.html
      const dirIndex = path.join(filePath, 'index.html');
      if (fs.existsSync(dirIndex)) {
        filePath = dirIndex;
      } else {
        res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
        res.end(`404 Not Found: ${pathname}`);
        return;
      }
    }

    const ext = path.extname(filePath).toLowerCase();
    const contentType = MIME_TYPES[ext] || 'application/octet-stream';

    res.writeHead(200, {
      'Content-Type': contentType,
      'Cache-Control': 'no-cache',
      'X-Content-Type-Options': 'nosniff'
    });

    const stream = fs.createReadStream(filePath);
    stream.pipe(res);
  });
});

function handleApiRequest(req, res, reqUrl) {
  res.setHeader('Content-Type', 'application/json; charset=utf-8');
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (req.method === 'OPTIONS') {
    res.writeHead(204);
    res.end();
    return;
  }

  let body = '';
  req.on('data', chunk => { body += chunk; });
  req.on('end', () => {
    let parsedBody = {};
    try {
      if (req.headers['content-type']?.includes('application/json')) {
        parsedBody = JSON.parse(body || '{}');
      } else {
        const params = new URLSearchParams(body);
        for (const [k, v] of params.entries()) {
          parsedBody[k] = v;
        }
      }
    } catch (e) {}

    const queryParams = Object.fromEntries(reqUrl.searchParams.entries());
    const input = { ...queryParams, ...parsedBody };
    const pathname = reqUrl.pathname;

    // 0. Health Check Endpoint
    if (pathname === '/api/health.php') {
      res.writeHead(200);
      res.end(JSON.stringify({
        status: 'healthy',
        version: '2.0.0',
        environment: 'development',
        timestamp: new Date().toISOString(),
        database: { status: 'connected', driver: 'sqlite/in-memory' },
        stats: {
          items_count: state.items.length,
          users_count: state.users.length,
          missing_count: state.missing.length,
          claims_count: state.claims.length
        }
      }));
      return;
    }

    // 1. Stats Endpoint
    if (pathname === '/api/stats.php') {
      res.writeHead(200);
      res.end(JSON.stringify({
        success: true,
        stats: {
          total_items: state.items.length,
          lost_items: state.items.filter(i => i.item_type === 'lost').length,
          found_items: state.items.filter(i => i.item_type === 'found').length,
          resolved_items: 1240,
          missing_persons: state.missing.length,
          registered_users: state.users.length,
          recovery_rate_pct: 89.4
        }
      }));
      return;
    }

    // 2. Auth API
    if (pathname === '/api/auth.php') {
      const action = input.action || 'me';

      if (action === 'login') {
        const email = (input.email || input.Email || '').toLowerCase().trim();
        const user = state.users.find(u => u.email.toLowerCase() === email);
        if (user) {
          state.currentUser = user;
          res.writeHead(200);
          res.end(JSON.stringify({ success: true, user, message: 'Logged in successfully.' }));
        } else {
          res.writeHead(401);
          res.end(JSON.stringify({ success: false, error: 'Invalid email or password.' }));
        }
        return;
      }

      if (action === 'register') {
        const email = (input.email || input.Email || '').toLowerCase().trim();
        if (state.users.some(u => u.email.toLowerCase() === email)) {
          res.writeHead(400);
          res.end(JSON.stringify({ success: false, error: 'Email already registered.' }));
          return;
        }
        const newUser = {
          id: state.users.length + 1,
          first_name: input.first_name || input.Fname || 'User',
          last_name: input.last_name || input.lname || '',
          email: email,
          phone: input.phone || input.Mobile || '01700000000',
          role: 'user',
          status: 'active'
        };
        state.users.push(newUser);
        state.currentUser = newUser;
        res.writeHead(201);
        res.end(JSON.stringify({ success: true, user: newUser, message: 'Registration successful.' }));
        return;
      }

      if (action === 'logout') {
        state.currentUser = null;
        res.writeHead(200);
        res.end(JSON.stringify({ success: true, message: 'Logged out successfully.' }));
        return;
      }

      if (action === 'me') {
        res.writeHead(200);
        res.end(JSON.stringify({
          success: true,
          user: state.currentUser || state.users[1]
        }));
        return;
      }

      if (action === 'update_profile') {
        if (state.currentUser) {
          if (input.first_name) state.currentUser.first_name = input.first_name;
          if (input.last_name) state.currentUser.last_name = input.last_name;
          if (input.phone) state.currentUser.phone = input.phone;
          const u = state.users.find(x => x.id === state.currentUser.id);
          if (u) {
            u.first_name = state.currentUser.first_name;
            u.last_name = state.currentUser.last_name;
            u.phone = state.currentUser.phone;
          }
          res.writeHead(200);
          res.end(JSON.stringify({ success: true, user: state.currentUser, message: 'Profile updated successfully.' }));
        } else {
          res.writeHead(401);
          res.end(JSON.stringify({ success: false, error: 'Not authenticated.' }));
        }
        return;
      }
    }

    // 3. Items API
    if (pathname === '/api/items.php') {
      const action = input.action || (req.method === 'GET' ? 'list' : 'create');

      if (action === 'list') {
        let filtered = [...state.items];
        if (input.type && input.type !== 'all') {
          filtered = filtered.filter(i => i.item_type === input.type);
        }
        if (input.category && input.category !== 'All') {
          filtered = filtered.filter(i => i.category.toLowerCase() === input.category.toLowerCase());
        }
        if (input.division && input.division !== 'All') {
          filtered = filtered.filter(i => i.division.toLowerCase() === input.division.toLowerCase());
        }
        if (input.user_id) {
          const uid = parseInt(input.user_id, 10);
          filtered = filtered.filter(i => i.user_id === uid);
        }
        if (input.query) {
          const q = input.query.toLowerCase();
          filtered = filtered.filter(i =>
            i.title.toLowerCase().includes(q) ||
            i.description.toLowerCase().includes(q) ||
            i.district.toLowerCase().includes(q)
          );
        }

        res.writeHead(200);
        res.end(JSON.stringify({
          success: true,
          data: {
            items: filtered,
            total: filtered.length
          }
        }));
        return;
      }

      if (action === 'get') {
        const id = parseInt(input.id, 10);
        const item = state.items.find(i => i.id === id);
        if (item) {
          res.writeHead(200);
          res.end(JSON.stringify({ success: true, item }));
        } else {
          res.writeHead(404);
          res.end(JSON.stringify({ success: false, error: 'Item not found' }));
        }
        return;
      }

      if (action === 'create') {
        const newItem = {
          id: state.items.length + 1,
          user_id: state.currentUser ? state.currentUser.id : 2,
          item_type: input.item_type || 'lost',
          title: input.item_name || input.title || 'Untitled Report',
          category: input.category || 'Other',
          description: input.address2 || input.description || '',
          division: input.state || input.division || 'Dhaka',
          district: input.city || input.district || 'Dhaka',
          specific_location: input.address || input.specific_location || '',
          surrounding_details: input.address2 || '',
          date_lost_or_found: input.date || new Date().toISOString().split('T')[0],
          picture: input.image || input.picture || 'images/default_item.jpg',
          contact_phone: input.phone || '01700000000',
          contact_email: input.email || 'user@example.com',
          reward_amount: parseFloat(input.reward || 0),
          status: 'verified',
          created_at: new Date().toISOString()
        };
        state.items.unshift(newItem);
        res.writeHead(201);
        res.end(JSON.stringify({ success: true, id: newItem.id, message: 'Item created.' }));
        return;
      }

      if (action === 'claim') {
        const itemId = parseInt(input.item_id, 10);
        const userId = state.currentUser ? state.currentUser.id : 2;
        const proof = (input.proof_description || input.claim_details || input.ownership_proof || '').trim();
        const phone = (input.contact_phone || input.claimant_phone || '').trim();

        if (!proof || !phone) {
          res.writeHead(400);
          res.end(JSON.stringify({ success: false, error: 'Proof description and contact phone are required.' }));
          return;
        }

        // Deduplication: prevent duplicate pending claims for the same user & item
        const existingClaim = state.claims.find(c => c.item_id === itemId && c.user_id === userId && c.status === 'pending');
        if (existingClaim) {
          res.writeHead(409);
          res.end(JSON.stringify({ success: false, error: 'You already have an active pending claim for this item.' }));
          return;
        }

        const newClaim = {
          id: state.claims.length + 1,
          item_id: itemId,
          user_id: userId,
          item_title: input.item_title || 'Claimed Belonging',
          claimant_name: input.claimant_name || (state.currentUser ? state.currentUser.first_name + ' ' + state.currentUser.last_name : 'Anonymous Claimant'),
          contact_phone: phone,
          proof_description: proof,
          status: 'pending',
          admin_notes: 'Under review by moderation desk',
          created_at: new Date().toISOString()
        };
        state.claims.unshift(newClaim);
        res.writeHead(200);
        res.end(JSON.stringify({ success: true, message: 'Claim submitted successfully.' }));
        return;
      }

      if (action === 'delete') {
        const id = parseInt(input.id, 10);
        state.items = state.items.filter(i => i.id !== id);
        res.writeHead(200);
        res.end(JSON.stringify({ success: true, message: 'Item deleted.' }));
        return;
      }
    }

    // 4. Missing Persons API
    if (pathname === '/api/missing.php') {
      const action = input.action || (req.method === 'GET' ? 'list' : 'create');

      if (action === 'list') {
        let filtered = [...state.missing];
        if (input.division && input.division !== 'All') {
          filtered = filtered.filter(p => p.last_seen_division.toLowerCase() === input.division.toLowerCase());
        }
        if (input.query) {
          const q = input.query.toLowerCase();
          filtered = filtered.filter(p =>
            p.full_name.toLowerCase().includes(q) ||
            p.clothing_description.toLowerCase().includes(q) ||
            p.last_seen_district.toLowerCase().includes(q)
          );
        }
        res.writeHead(200);
        res.end(JSON.stringify({
          success: true,
          data: {
            persons: filtered,
            total: filtered.length
          }
        }));
        return;
      }

      if (action === 'sighting') {
        const id = parseInt(input.id || input.person_id, 10);
        const person = state.missing.find(p => p.id === id);
        if (!person) {
          res.writeHead(404);
          res.end(JSON.stringify({ success: false, error: 'Person record not found.' }));
          return;
        }
        const location = (input.location || '').trim();
        const details = (input.details || '').trim();

        if (!location || !details) {
          res.writeHead(400);
          res.end(JSON.stringify({ success: false, error: 'Sighting location and details are required.' }));
          return;
        }

        person.status = 'sighted';
        person.last_seen_details = `[SIGHTING REPORTED ${new Date().toISOString().split('T')[0]}] ${location} - ${details}`;
        const sightingRef = 'SIG-BD-' + Math.random().toString(36).substring(2, 8).toUpperCase();
        res.writeHead(200);
        res.end(JSON.stringify({
          success: true,
          sighting_ref: sightingRef,
          message: 'Sighting report recorded and forwarded to Bangladesh Police and family contact.'
        }));
        return;
      }

      if (action === 'create') {
        const age = parseInt(input.age || 0, 10);
        if (isNaN(age) || age < 0 || age > 125) {
          res.writeHead(400);
          res.end(JSON.stringify({ success: false, error: 'Please provide a realistic age between 0 and 125.' }));
          return;
        }

        const newP = {
          id: state.missing.length + 1,
          full_name: input.full_name || input.name || 'Anonymous',
          nid: input.nid || '',
          contact_phone: input.contact_phone || input.phone || '017XXXXXXXX',
          emergency_phone: input.emergency_phone || '',
          missing_date: input.missing_date || new Date().toISOString().split('T')[0],
          last_seen_division: input.last_seen_division || 'Dhaka',
          last_seen_district: input.last_seen_district || 'Mirpur',
          last_seen_details: input.last_seen_details || '',
          age: age,
          clothing_description: input.clothing_description || 'Casual wear',
          distinguishing_features: input.distinguishing_features || '',
          gd_number: input.gd_number || '',
          photo: 'images/default_person.jpg',
          status: 'missing',
          created_at: new Date().toISOString()
        };
        state.missing.unshift(newP);
        res.writeHead(201);
        res.end(JSON.stringify({ success: true, id: newP.id, message: 'Missing report created.' }));
        return;
      }
    }

    // 5. Chatbot API
    if (pathname === '/api/chatbot.php') {
      const rawMsg = (input.message || input.txt || input.query || '').trim();
      if (!rawMsg) {
        res.writeHead(400);
        res.end(JSON.stringify({ success: false, error: 'Query message cannot be empty.' }));
        return;
      }
      const q = rawMsg.toLowerCase();
      let reply = "Hello! I am your Lost & Found Bangladesh Assistant. You can report items, search missing persons, or submit claim verifications.";

      if (q.includes('lost') || q.includes('harie')) {
        reply = "To report a lost item, click the 'Report Lost Item' button on our header or home screen. Please provide specific landmarks and optional reward offers.";
      } else if (q.includes('found') || q.includes('paise')) {
        reply = "Thank you for your honesty! Please submit a 'Report Found Item' notice. When an owner claims it, they must verify ownership before getting your contact info.";
      } else if (q.includes('police') || q.includes('gd') || q.includes('thana')) {
        reply = "For formal police assistance in Bangladesh, file a General Diary (GD) at your local Thana. For life-threatening emergencies, call 999.";
      } else if (q.includes('free') || q.includes('cost') || q.includes('fee')) {
        reply = "Lost & Found Bangladesh is 100% free for all citizens! There are zero fees for posting or recovering items.";
      }

      res.writeHead(200);
      res.end(JSON.stringify({
        success: true,
        data: { reply, category: 'General' }
      }));
      return;
    }

    // 6. Admin API
    if (pathname === '/api/admin.php') {
      const action = input.action || 'dashboard';

      if (action === 'dashboard') {
        const totalDonations = (state.donations || []).reduce((acc, d) => acc + d.amount, 0);
        res.writeHead(200);
        res.end(JSON.stringify({
          success: true,
          metrics: {
            total_users: state.users.length,
            pending_items: 0,
            verified_items: state.items.length,
            resolved_items: 142,
            active_missing: state.missing.length,
            total_donations: totalDonations > 0 ? totalDonations : 45000.00
          }
        }));
        return;
      }

      if (action === 'list_users') {
        res.writeHead(200);
        res.end(JSON.stringify({ success: true, users: state.users }));
        return;
      }

      if (action === 'list_claims') {
        res.writeHead(200);
        res.end(JSON.stringify({ success: true, claims: state.claims }));
        return;
      }

      if (action === 'resolve_claim') {
        const id = parseInt(input.claim_id, 10);
        const claim = state.claims.find(c => c.id === id);
        if (claim) {
          claim.status = input.status || 'approved';
          claim.admin_notes = input.admin_notes || '';
          res.writeHead(200);
          res.end(JSON.stringify({ success: true, message: `Claim ${claim.status}.` }));
        } else {
          res.writeHead(404);
          res.end(JSON.stringify({ success: false, error: 'Claim not found.' }));
        }
        return;
      }

      if (action === 'update_user_status') {
        const id = parseInt(input.user_id, 10);
        const user = state.users.find(u => u.id === id);
        if (user) {
          user.status = input.status || 'active';
          res.writeHead(200);
          res.end(JSON.stringify({ success: true, message: `User status set to ${user.status}.` }));
        } else {
          res.writeHead(404);
          res.end(JSON.stringify({ success: false, error: 'User not found.' }));
        }
        return;
      }

      if (action === 'delete_item') {
        const id = parseInt(input.id, 10);
        state.items = state.items.filter(i => i.id !== id);
        res.writeHead(200);
        res.end(JSON.stringify({ success: true, message: 'Item deleted.' }));
        return;
      }
    }

    // 7. Donation API
    if (pathname === '/api/donate.php') {
      const amount = parseFloat(input.amount || 0);
      const name = (input.name || input.donor_name || '').trim();
      const email = (input.email || input.donor_email || '').trim();

      if (amount <= 0 || !name || !email) {
        res.writeHead(400);
        res.end(JSON.stringify({ success: false, error: 'Please provide valid donor name, email, and amount greater than 0.' }));
        return;
      }

      const txId = 'TRX-' + Math.random().toString(36).substring(2, 10).toUpperCase();
      state.donations = state.donations || [];
      state.donations.push({
        id: state.donations.length + 1,
        donor_name: name,
        donor_email: email,
        amount: amount,
        currency: 'BDT',
        payment_method: 'Stripe Tokenized',
        stripe_payment_id: txId,
        status: 'succeeded',
        created_at: new Date().toISOString()
      });

      res.writeHead(200);
      res.end(JSON.stringify({
        success: true,
        transaction_id: txId,
        trx_id: txId,
        amount: amount,
        currency: 'BDT',
        donor_name: name,
        message: 'Thank you! Your donation directly supports our nationwide search and recovery operations.'
      }));
      return;
    }

    // 6. Action.php (Live Search HTML fragment)
    if (pathname === '/action.php') {
      res.setHeader('Content-Type', 'text/html; charset=utf-8');
      const q = (input.query || '').toLowerCase();
      const matches = state.items.filter(i =>
        i.title.toLowerCase().includes(q) ||
        i.description.toLowerCase().includes(q) ||
        i.district.toLowerCase().includes(q)
      ).slice(0, 5);

      if (matches.length > 0) {
        let html = '';
        matches.forEach(m => {
          const badgeClass = m.item_type === 'lost' ? 'badge-lost' : 'badge-found';
          html += `
            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 search-result-item" onclick="selectSearchResult(${m.id}, '${m.title}'); return false;">
              <div>
                <div class="fw-semibold text-light">${m.title}</div>
                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>${m.district}, ${m.division}</small>
              </div>
              <span class="badge ${badgeClass}">${m.item_type}</span>
            </a>
          `;
        });
        res.writeHead(200);
        res.end(html);
      } else {
        res.writeHead(200);
        res.end('<p class="text-muted p-3 mb-0">No matching items found</p>');
      }
      return;
    }

    // 8. Smart Match API
    if (pathname === '/api/match.php') {
      const action = input.action || 'system_pairs';

      function calcMatchScore(a, b) {
        let score = 0;
        const catMatch = Boolean(a.category && b.category && a.category.toLowerCase() === b.category.toLowerCase());
        if (catMatch) score += 25;

        const divMatch = Boolean(a.division && b.division && a.division.toLowerCase() === b.division.toLowerCase());
        const distMatch = Boolean(divMatch && a.district && b.district && a.district.toLowerCase() === b.district.toLowerCase());
        if (distMatch) {
          score += 25;
        } else if (divMatch) {
          score += 15;
        }

        const wordsA = (a.title + ' ' + (a.description || '')).toLowerCase().match(/\b[a-z0-9]{3,}\b/g) || [];
        const wordsB = (b.title + ' ' + (b.description || '')).toLowerCase().match(/\b[a-z0-9]{3,}\b/g) || [];
        const setB = new Set(wordsB);
        const intersect = wordsA.filter(w => setB.has(w));
        let sim = 0;
        if (wordsA.length && wordsB.length) {
          sim = (intersect.length * 2) / (wordsA.length + wordsB.length);
          score += Math.round(sim * 35);
        }
        score += 10; // date proximity baseline
        const finalScore = Math.min(100, Math.max(0, score));

        let daysDiff = 2;
        if (a.date_lost_or_found && b.date_lost_or_found) {
          const dtA = new Date(a.date_lost_or_found);
          const dtB = new Date(b.date_lost_or_found);
          daysDiff = Math.round(Math.abs((dtA - dtB) / (1000 * 60 * 60 * 24)));
        }

        return {
          score: finalScore,
          breakdown: {
            token_score: Math.round(sim * 35),
            category_score: catMatch ? 25 : 0,
            location_score: distMatch ? 25 : (divMatch ? 15 : 0),
            date_score: 10,
            category_match: catMatch,
            district_match: distMatch,
            division_match: divMatch,
            text_dice: sim,
            days_apart: daysDiff
          }
        };
      }

      if (action === 'item_matches') {
        if (!input.item_id) {
          res.writeHead(400);
          res.end(JSON.stringify({ success: false, error: 'item_id is required.' }));
          return;
        }
        const id = parseInt(input.item_id, 10);
        const target = state.items.find(i => i.id === id);
        if (!target) {
          res.writeHead(404);
          res.end(JSON.stringify({ success: false, error: 'Item not found.' }));
          return;
        }
        const oppType = target.item_type === 'lost' ? 'found' : 'lost';
        const candidates = state.items.filter(i => i.item_type === oppType);
        const matches = candidates.map(c => {
          const comp = calcMatchScore(target, c);
          return {
            item: c,
            confidence: comp.score,
            breakdown: comp.breakdown
          };
        }).filter(m => m.confidence >= (parseInt(input.min_confidence, 10) || 30)).sort((a, b) => b.confidence - a.confidence);

        res.writeHead(200);
        res.end(JSON.stringify({
          success: true,
          data: {
            target_item: target,
            count: matches.length,
            matches
          }
        }));
        return;
      }

      // system_pairs
      const minConf = parseInt(input.min_confidence, 10) || 30;
      const losts = state.items.filter(i => i.item_type === 'lost');
      const founds = state.items.filter(i => i.item_type === 'found');
      const pairs = [];
      for (const l of losts) {
        for (const f of founds) {
          const comp = calcMatchScore(l, f);
          if (comp.score >= minConf) {
            pairs.push({
              lost_item: l,
              found_item: f,
              confidence: comp.score,
              breakdown: comp.breakdown
            });
          }
        }
      }
      pairs.sort((a, b) => b.confidence - a.confidence);
      res.writeHead(200);
      res.end(JSON.stringify({
        success: true,
        data: {
          total_pairs: pairs.length,
          pairs: pairs.slice(0, 20)
        }
      }));
      return;
    }

    // 9. QR Asset Recovery Shield API
    if (pathname === '/api/qr_tag.php') {
      const action = input.action || 'list';

      if (action === 'list') {
        res.writeHead(200);
        res.end(JSON.stringify({
          success: true,
          data: {
            tags: state.qrTags
          }
        }));
        return;
      }

      if (action === 'get' || action === 'inspect') {
        const code = (input.tag_code || '').trim().toUpperCase();
        const tag = state.qrTags.find(t => t.tag_code.toUpperCase() === code);
        if (tag) {
          res.writeHead(200);
          res.end(JSON.stringify({
            success: true,
            data: tag
          }));
        } else {
          res.writeHead(404);
          res.end(JSON.stringify({ success: false, error: 'Asset tag not found.' }));
        }
        return;
      }

      if (action === 'create') {
        const assetName = (input.item_name || input.asset_name || '').trim();
        if (!assetName) {
          res.writeHead(400);
          res.end(JSON.stringify({ success: false, error: 'Asset name is required.' }));
          return;
        }
        const tagCode = 'LF-BD-' + Math.random().toString(36).substring(2, 8).toUpperCase();
        const newTag = {
          id: state.qrTags.length + 1,
          user_id: state.currentUser ? state.currentUser.id : 2,
          tag_code: tagCode,
          item_name: assetName,
          asset_name: assetName,
          category: input.category || 'General',
          owner_phone: input.owner_phone || '',
          owner_email: input.owner_email || '',
          reward_amount: Number(input.reward_amount) || 0,
          notes: input.notes || '',
          status: 'active',
          recovery_reports: [],
          created_at: new Date().toISOString()
        };
        state.qrTags.unshift(newTag);
        res.writeHead(200);
        res.end(JSON.stringify({
          success: true,
          data: {
            ...newTag,
            recovery_url: `recover.html?tag=${tagCode}`
          },
          message: 'Asset recovery badge generated successfully.'
        }));
        return;
      }

      if (action === 'report_found') {
        const code = (input.tag_code || '').trim().toUpperCase();
        const phone = (input.finder_phone || '').trim();
        const loc = (input.found_location || '').trim();
        if (!code || !phone || !loc) {
          res.writeHead(400);
          res.end(JSON.stringify({ success: false, error: 'Tag code, phone, and found location are required.' }));
          return;
        }
        const targetTag = state.qrTags.find(t => t.tag_code.toUpperCase() === code);
        const report = {
          id: 'REC-BD-' + Math.random().toString(36).substring(2, 8).toUpperCase(),
          tag_code: code,
          finder_name: input.finder_name || 'Honest Citizen',
          finder_phone: phone,
          found_location: loc,
          message: input.message || '',
          created_at: new Date().toISOString()
        };
        if (targetTag) {
          targetTag.recovery_reports = targetTag.recovery_reports || [];
          targetTag.recovery_reports.unshift(report);
        }
        state.recoveryLogs = state.recoveryLogs || [];
        state.recoveryLogs.push(report);

        res.writeHead(200);
        res.end(JSON.stringify({
          success: true,
          report_id: report.id,
          message: 'Thank you! The verified owner has been notified via emergency encrypted dispatch.',
          tag_code: code
        }));
        return;
      }
    }

    res.writeHead(200);
    res.end(JSON.stringify({ success: true }));
  });
}

server.listen(PORT, () => {
  console.log(`🚀 Lost & Found BD Flagship Dev Server running at: http://localhost:${PORT}`);
});
