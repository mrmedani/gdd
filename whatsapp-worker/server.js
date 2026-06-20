const express = require('express');
const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode');
const path = require('path');
const fs = require('fs');
const { execSync } = require('child_process');

const app = express();
app.use(express.json());

app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type');
    if (req.method === 'OPTIONS') return res.sendStatus(200);
    next();
});

const PORT = process.env.WA_PORT || 9090;
const DATA_DIR = path.resolve(__dirname, '..', 'storage', 'app', 'whatsapp');

if (!fs.existsSync(DATA_DIR)) {
    fs.mkdirSync(DATA_DIR, { recursive: true });
}

let client = null;
let qrBase64 = null;
let clientStatus = 'disconnected';
let connectedPhone = null;
let startingLock = false;
let heartbeatTimer = null;

function getChromePath() {
    const home = process.env.HOME || '/home/gestion';
    const cacheDir = path.join(home, '.cache', 'puppeteer');
    if (fs.existsSync(cacheDir)) {
        try { return walkDirForChrome(cacheDir); } catch (e) {}
    }
    return null;
}

function walkDirForChrome(dir) {
    const entries = fs.readdirSync(dir);
    for (const entry of entries) {
        const full = path.join(dir, entry);
        if (!fs.statSync(full).isDirectory()) continue;
        const chromePath = path.join(full, 'chrome-linux64', 'chrome');
        if (fs.existsSync(chromePath)) return chromePath;
        const nested = walkDirForChrome(full);
        if (nested) return nested;
    }
    return null;
}

function killChrome() {
    try {
        execSync('pkill -9 -f "chrome" 2>/dev/null; sleep 2', { timeout: 5000 });
    } catch (e) {}
    try { fs.rmSync(DATA_DIR, { recursive: true, force: true }); } catch (e) {}
    try { fs.rmSync(path.resolve(__dirname, 'tokens'), { recursive: true, force: true }); } catch (e) {}
}

function startHeartbeat() {
    stopHeartbeat();
    if (!client) return;
    heartbeatTimer = setInterval(async () => {
        try {
            if (client && clientStatus === 'connected') {
                const state = await client.getState();
                if (state === 'CONNECTED') return;
                console.log('Heartbeat: state is', state);
                if (state === 'UNPAIRED' || state === 'UNPAIRED_IDLE') {
                    clientStatus = 'disconnected';
                    connectedPhone = null;
                    stopHeartbeat();
                }
            }
        } catch (e) {
            if (e.message.includes('detached') || e.message.includes('closed')) {
                console.log('Heartbeat: detected detached frame, reinitializing...');
                clientStatus = 'error';
                connectedPhone = null;
                stopHeartbeat();
                startRecovery();
            }
        }
    }, 30000);
}

function stopHeartbeat() {
    if (heartbeatTimer) {
        clearInterval(heartbeatTimer);
        heartbeatTimer = null;
    }
}

let recoveryTimer = null;

function startRecovery() {
    if (recoveryTimer || startingLock) return;
    recoveryTimer = setTimeout(() => {
        recoveryTimer = null;
        if (clientStatus !== 'connected' && !startingLock) {
            console.log('Recovery: attempting to restart client...');
            createClient();
        }
    }, 10000);
}

function createClient() {
    if (startingLock) return;
    startingLock = true;

    stopHeartbeat();
    if (recoveryTimer) { clearTimeout(recoveryTimer); recoveryTimer = null; }

    if (client) {
        client.destroy().catch(() => {});
        client = null;
    }
    qrBase64 = null;
    clientStatus = 'starting';

    const chromePath = getChromePath();
    console.log('Chrome path:', chromePath || '(bundled)');

    const puppeteerOpts = {
        headless: true,
        protocolTimeout: 300000,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu',
            '--no-first-run',
            '--disable-background-timer-throttling',
            '--disable-backgrounding-occluded-windows',
            '--disable-renderer-backgrounding',
            '--disable-extensions',
            '--disable-default-apps',
            '--disable-sync',
            '--disable-translate',
            '--disable-hang-monitor',
            '--mute-audio',
            '--disable-features=IsolateOrigins,site-per-process',
            '--disable-features=MemorySaverMode',
            '--memory-pressure-off',
        ],
    };
    if (chromePath) puppeteerOpts.executablePath = chromePath;

    client = new Client({
        authStrategy: new LocalAuth({ dataPath: DATA_DIR }),
        puppeteer: puppeteerOpts,
        webVersionCache: { type: 'local' },
    });

    client.on('qr', async (qr) => {
        try { qrBase64 = await qrcode.toDataURL(qr); } catch (e) { qrBase64 = null; }
        clientStatus = 'qr_ready';
        console.log('QR code generated');
    });

    client.on('ready', () => {
        qrBase64 = null;
        clientStatus = 'connected';
        connectedPhone = client.info?.wid?.user || client.info?.me?.user || null;
        console.log('WhatsApp connected:', connectedPhone);
        startHeartbeat();
    });

    client.on('disconnected', (reason) => {
        qrBase64 = null;
        clientStatus = 'disconnected';
        connectedPhone = null;
        stopHeartbeat();
        console.log('WhatsApp disconnected:', reason);
    });

    client.on('auth_failure', (msg) => {
        qrBase64 = null;
        clientStatus = 'auth_failure';
        connectedPhone = null;
        stopHeartbeat();
        console.log('Auth failure:', msg);
    });

    client.initialize().then(() => {
        console.log('Client initialized');
        startingLock = false;
    }).catch((err) => {
        console.error('Client init error:', err.message);
        clientStatus = 'error';
        client = null;
        startingLock = false;
    });
}

app.get('/status', (req, res) => res.json({ status: clientStatus, phone: connectedPhone }));
app.get('/qr', (req, res) => res.json({ qr: qrBase64 }));

app.post('/send', async (req, res) => {
    const { chatId, message } = req.body;
    if (!client || clientStatus !== 'connected') {
        return res.status(400).json({ ok: false, error: 'WhatsApp not connected' });
    }
    if (!chatId) return res.status(400).json({ ok: false, error: 'chatId required' });
    try {
        const n = chatId.includes('@c.us') ? chatId : `${chatId}@c.us`;
        await client.sendMessage(n, message);
        res.json({ ok: true });
    } catch (err) {
        res.status(500).json({ ok: false, error: err.message });
    }
});

app.post('/start', (req, res) => {
    if (client && clientStatus === 'connected') return res.json({ status: 'connected' });
    if (startingLock) return res.json({ status: 'starting' });
    createClient();
    res.json({ status: 'starting' });
});

app.post('/disconnect', async (req, res) => {
    try {
        stopHeartbeat();
        if (client) { await client.destroy(); client = null; }
        killChrome();
        res.json({ ok: true });
    } catch (e) {
        res.status(500).json({ ok: false, error: e.message });
    }
});

app.listen(PORT, () => {
    console.log(`WhatsApp worker listening on port ${PORT}`);
    console.log('Chrome found at:', getChromePath());
    console.log('Waiting for /start to initialize client');
});
