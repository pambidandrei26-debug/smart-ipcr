# 🚨 CRITICAL SECURITY FIXES REQUIRED

**Date:** October 29, 2026  
**Based On:** Security Scan Report (11 Findings)  
**Status:** URGENT - Fix before production deployment

---

## 📋 Summary of Findings

| ID | Control | Finding | Severity | Status |
|----|---------|---------|----------|--------|
| A01 | Confidentiality | Hard-coded DB credentials in config/database.php | **CRITICAL** | ⬜ Not Fixed |
| A02 | Integrity | Walang CSRF protection | **HIGH** | ⬜ Not Fixed |
| A08 | Fail Securely | May technical details sa error messages | **MEDIUM** | ⚠️ Partial |
| C03 | XSS | Hindi verified ang output encoding | **HIGH** | ⚠️ Partial |
| C04 | CSRF | Walang anti-CSRF token | **HIGH** | ⬜ Not Fixed |
| C08 | Data Exposure | Credentials sa source code & unencrypted API keys | **CRITICAL** | ⬜ Not Fixed |
| D01 | HTTPS | HTTP lang, walang HTTPS redirect | **HIGH** | ⬜ Not Fixed |
| D02 | Secure Cookie | Hindi naka-set ang session.cookie_secure | **MEDIUM** | ⬜ Not Fixed |
| D04 | SameSite Cookie | Hindi explicit na naka-set ang SameSite | **MEDIUM** | ⬜ Not Fixed |
| F02 | Error Safety | Same as A08 | **MEDIUM** | ⚠️ Partial |
| F03 | Secret Handling | Same as A01 | **CRITICAL** | ⬜ Not Fixed |

---

## 🔴 CRITICAL FIXES (Fix Immediately!)

### **A01 & F03: Hard-coded Database Credentials** ❌

**Current Code (VULNERABLE):**
```php
// config/database.php - NAKIKITA SA SOURCE CODE!
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // ← EXPOSED!
define('DB_NAME', 'u907280979_borrowing');
```

**Problem:**
- ❌ Credentials nakita sa Git commits
- ❌ Readable by anyone with file access
- ❌ Cannot change without code modification
- ❌ Stored in version control history

**SOLUTION 1: Use Environment Variables (.env file)**

**Step 1:** Create `.env` file (NOT in Git):
```env
# .env - ADD TO .gitignore!
DB_HOST=localhost
DB_USER=root
DB_PASS=your_secure_password_here
DB_NAME=u907280979_borrowing
DB_CHARSET=utf8mb4
```

**Step 2:** Add to `.gitignore`:
```gitignore
# Sensitive files
config/database.php
.env
*.env
config/email.php
```

**Step 3:** Update `config/database.php`:
```php
<?php
/**
 * Database Configuration - SECURE VERSION
 * Uses environment variables - NOT hardcoded
 */

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            putenv(trim($line));
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Get credentials from environment
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'ipcr_system');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            http_response_code(503);
            die('Database connection failed. Please contact administrator.');
        }
    }
    return $pdo;
}
```

**Step 4:** Create `config/database.example.php`:
```php
<?php
/**
 * Database Configuration - EXAMPLE FILE
 * Copy this to database.php and update with your credentials
 * OR use .env file (recommended)
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database');
define('DB_CHARSET', 'utf8mb4');
```

---

### **C08: API Keys Exposed in Database** ❌

**Current State:**
- API keys stored in `system_config` table
- ⚠️ Unencrypted
- ⚠️ Visible to anyone with DB access
- ⚠️ Shown in settings page (type="password" but value visible in HTML source)

**SOLUTION: Encrypt API Keys**

**Step 1:** Add encryption functions:
```php
// config/encryption.php
<?php
define('ENCRYPTION_KEY', getenv('ENCRYPTION_KEY') ?: 'change-this-to-random-32-char-key'); // Use .env!
define('ENCRYPTION_METHOD', 'AES-256-CBC');

function encryptData(string $data): string {
    if (empty($data)) return '';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(ENCRYPTION_METHOD));
    $encrypted = openssl_encrypt($data, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

function decryptData(string $data): string {
    if (empty($data)) return '';
    list($encrypted, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
}
```

**Step 2:** Update admin/settings.php:
```php
// When saving API keys
require_once __DIR__ . '/../config/encryption.php';

$geminiKey = trim($_POST['gemini_api_key'] ?? '');
// Encrypt before storing
$encryptedKey = encryptData($geminiKey);
$stmt->execute(['gemini_api_key', $encryptedKey]);
```

**Step 3:** Update API files when retrieving:
```php
// api/upload-evidence.php
require_once __DIR__ . '/../config/encryption.php';

$encryptedKey = $db->query("SELECT config_value FROM system_config WHERE config_key='gemini_api_key'")->fetchColumn();
$apiKey = decryptData($encryptedKey);
```

---

## 🔴 HIGH PRIORITY FIXES

### **A02 & C04: CSRF Protection Missing** ❌

**Current State:**
- ❌ No CSRF tokens in forms
- ❌ Vulnerable to cross-site request forgery

**Attack Example:**
```html
<!-- Attacker's site -->
<form action="https://ipcr.isu.edu.ph/admin/users.php" method="POST">
  <input name="action" value="delete_user">
  <input name="user_id" value="5">
</form>
<script>document.forms[0].submit();</script>
```

**SOLUTION: Implement CSRF Tokens**

**Step 1:** Add CSRF functions to `config/auth.php`:
```php
// config/auth.php
function generateCSRFToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken(?string $token): bool {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function requireCSRF(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verifyCSRFToken($_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) {
            http_response_code(403);
            die(json_encode(['success' => false, 'message' => 'CSRF token validation failed']));
        }
    }
}
```

**Step 2:** Add meta tag in `includes/header.php`:
```php
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="<?= generateCSRFToken() ?>">
  <!-- ... -->
</head>
```

**Step 3:** Add to all forms:
```html
<form method="POST">
  <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
  <!-- ... other fields ... -->
</form>
```

**Step 4:** Add to AJAX requests in `assets/js/main.js`:
```javascript
// Get CSRF token from meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

// Add to all fetch requests
fetch(url, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
    },
    body: JSON.stringify(data)
});
```

**Step 5:** Verify in all POST handlers:
```php
// At the start of files that handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCSRF(); // Will die() if invalid
}
```

---

### **C03: XSS - Output Encoding Not Verified** ⚠️

**Current State:**
- ⚠️ Using `h()` function (good!)
- ⚠️ But some places may not use it

**Problem Example Found:**
```php
// dashboard.php line 639
echo $actionMap[$myForm['status']] ?? '';
// ⚠️ If $actionMap values aren't escaped, XSS possible
```

**SOLUTION: Audit All Output**

**Step 1:** Search for unescaped output:
```bash
# Find all <?= without h()
grep -rn "<?= \$" --include="*.php" . | grep -v "<?= h("
```

**Step 2:** Fix each occurrence:
```php
// BEFORE (VULNERABLE if user-controlled)
<?= $variable ?>

// AFTER (SAFE)
<?= h($variable) ?>
```

**Step 3:** Use Content Security Policy header:
```php
// includes/header.php
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net; img-src 'self' data:;");
```

---

### **D01: No HTTPS Enforcement** ❌

**Current State:**
- HTTP only (localhost)
- No redirect to HTTPS

**SOLUTION: Force HTTPS**

**Add to `.htaccess`:**
```apache
# Force HTTPS (for production)
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteCond %{HTTP_HOST} !^localhost [NC]
    RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]
</IfModule>
```

**Or in PHP (includes/header.php):**
```php
// Force HTTPS in production
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    if ($_SERVER['HTTP_HOST'] !== 'localhost' && 
        strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === false) {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit;
    }
}
```

---

## 🟡 MEDIUM PRIORITY FIXES

### **A08 & F02: Technical Details in Error Messages** ⚠️

**Problem Found:**
```php
// api/test-upload.php - EXPOSES ERRORS!
error_reporting(E_ALL);
ini_set('display_errors', 1); // ← DANGEROUS IN PRODUCTION!
```

**SOLUTION: Hide Technical Details**

**Step 1:** Delete or secure `api/test-upload.php`:
```bash
# Remove test file
rm api/test-upload.php
```

**Step 2:** Create global error handler:
```php
// config/error_handler.php
<?php
// Production error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');
error_reporting(E_ALL);

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno]: $errstr in $errfile on line $errline");
    
    // Show generic message to user
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    http_response_code(500);
    if (headers_sent()) {
        echo '<div class="alert alert-danger">An error occurred. Please contact support.</div>';
    } else {
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please contact support.']);
    }
    exit;
});

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log("Fatal error: " . print_r($error, true));
        http_response_code(500);
        echo 'A critical error occurred. Please contact support.';
    }
});
```

**Step 3:** Include in all entry points:
```php
require_once __DIR__ . '/config/error_handler.php';
```

---

### **D02: session.cookie_secure Not Set** ❌

**SOLUTION: Secure Session Cookies**

**Update `config/auth.php`:**
```php
function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        // Existing
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        
        // ADD THESE:
        ini_set('session.cookie_secure', 1);           // HTTPS only
        ini_set('session.cookie_samesite', 'Strict'); // D04 fix
        ini_set('session.gc_maxlifetime', 21600);     // 6 hours
        ini_set('session.use_only_cookies', 1);       // No URL session IDs
        
        session_name('IPCR_SESSION');
        session_start();
        
        // Session fixation protection
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
            $_SESSION['created'] = time();
        }
        
        // Regenerate every 30 minutes
        if (isset($_SESSION['created']) && time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}
```

---

### **D04: SameSite Cookie Not Explicit** ❌

**Fixed in D02 solution above:**
```php
ini_set('session.cookie_samesite', 'Strict');
```

---

## 📋 Implementation Checklist

### Phase 1: CRITICAL (Do First!) 🔴
- [ ] Move DB credentials to `.env` file (A01)
- [ ] Encrypt API keys in database (C08)
- [ ] Add `.env` to `.gitignore`
- [ ] Create `config/database.example.php`
- [ ] Remove sensitive data from Git history:
  ```bash
  git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch config/database.php" \
  --prune-empty --tag-name-filter cat -- --all
  ```

### Phase 2: HIGH PRIORITY 🔴
- [ ] Implement CSRF tokens (A02, C04)
- [ ] Add CSRF to all forms
- [ ] Add CSRF to all AJAX requests
- [ ] Test CSRF protection
- [ ] Audit all output for XSS (C03)
- [ ] Add HTTPS enforcement (D01)

### Phase 3: MEDIUM PRIORITY 🟡
- [ ] Delete/secure `api/test-upload.php` (A08)
- [ ] Create global error handler (F02)
- [ ] Update session security settings (D02, D04)
- [ ] Test session security

### Phase 4: TESTING ✅
- [ ] Test all forms with CSRF tokens
- [ ] Test API endpoints with CSRF
- [ ] Test HTTPS redirect
- [ ] Test session security (logout, regeneration)
- [ ] Test error handling (hide technical details)
- [ ] Security scan with OWASP ZAP
- [ ] Penetration testing

---

## 📊 Priority Matrix

```
CRITICAL (Fix Now)     HIGH (Fix Soon)      MEDIUM (This Week)
┌─────────────────┐   ┌────────────────┐   ┌──────────────┐
│ A01: DB Creds   │   │ A02: CSRF      │   │ A08: Errors  │
│ C08: API Keys   │   │ C04: CSRF      │   │ D02: Cookies │
│ F03: Secrets    │   │ C03: XSS       │   │ D04: SameSite│
└─────────────────┘   │ D01: HTTPS     │   │ F02: Errors  │
                      └────────────────┘   └──────────────┘
```

---

## 🎯 After Fixes - Expected Security Score

| Category | Before | After | Improvement |
|----------|--------|-------|-------------|
| Confidentiality | ❌ CRITICAL | ✅ GOOD | +100% |
| CSRF Protection | ❌ MISSING | ✅ IMPLEMENTED | +100% |
| Session Security | ⚠️ PARTIAL | ✅ SECURE | +80% |
| Error Handling | ⚠️ EXPOSED | ✅ HIDDEN | +90% |
| HTTPS | ❌ HTTP ONLY | ✅ FORCED | +100% |
| **OVERALL** | **4.5/10** | **9.0/10** | **+100%** |

---

## ⚠️ IMPORTANT NOTES

1. **Backup First!**
   ```bash
   # Backup database
   mysqldump -u root -p ipcr_system > backup_$(date +%Y%m%d).sql
   
   # Backup files
   tar -czf backup_files_$(date +%Y%m%d).tar.gz .
   ```

2. **Test in Staging First!**
   - Don't apply fixes directly to production
   - Test each fix thoroughly
   - Verify nothing breaks

3. **Update Documentation:**
   - Update README.md with new setup instructions
   - Document .env file requirements
   - Update deployment guide

4. **Monitor After Deployment:**
   - Check error logs
   - Monitor failed CSRF attempts
   - Watch for unusual activity

---

## 📞 Need Help?

If you encounter issues during implementation:
1. Check logs in `logs/error.log`
2. Review this document
3. Contact: security@isu.edu.ph

**Status:** ⚠️ URGENT FIXES REQUIRED  
**Deadline:** Before production deployment  
**Priority:** CRITICAL
