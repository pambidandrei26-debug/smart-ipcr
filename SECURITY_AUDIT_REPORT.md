# 🔒 Security Audit Report - ISU IPCR System

**Date:** October 29, 2026  
**Audited By:** Security Assessment Tool  
**System Version:** 2.0 (Post-Authorization Fix)  
**Overall Security Rating:** 7.5/10 (Good - Minor improvements needed)

---

## Executive Summary

The ISU IPCR system has **good security fundamentals** in place. The recent authorization bypass fix significantly improved security. However, there are still **several vulnerabilities** that need attention before production deployment.

**Key Findings:**
- ✅ **Authorization controls fixed** (Critical fix applied)
- ✅ **Password hashing implemented** (BCrypt)
- ✅ **File upload validation** (Type & size checks)
- ✅ **Prepared statements** (SQL injection protected)
- ⚠️ **CSRF protection missing** (High priority)
- ⚠️ **Session security incomplete** (Medium priority)
- ⚠️ **Input validation gaps** (Medium priority)
- ⚠️ **Security headers missing** (Low priority)

---

## ✅ Security Strengths (What's Good)

### 1. **Authorization & Authentication** ✅
**Status:** FIXED (Previously Critical, now Good)

**Strengths:**
- ✅ Role-based access control (RBAC) implemented
- ✅ `requireRole()` called BEFORE any output
- ✅ Session-based authentication
- ✅ Login rate limiting (5 failed attempts = 15 min lockout)
- ✅ Session expiration (6 hours)
- ✅ Password requirements (minimum 6 characters)

**Code Example:**
```php
// admin/settings.php - SECURE
requireRole('admin');  // ← Checked BEFORE header
require_once 'includes/header.php';
```

**Test Results:**
- ❌ Faculty cannot access admin pages ✓
- ❌ Dean cannot access admin-only pages ✓
- ✅ Proper role enforcement ✓

---

### 2. **Password Security** ✅
**Status:** Good

**Strengths:**
- ✅ BCrypt hashing (cost factor: 10)
- ✅ `password_verify()` for checking
- ✅ Passwords never stored in plain text
- ✅ Failed login tracking

**Code Example:**
```php
// config/auth.php
function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

if (!password_verify($password, $user['password_hash'])) {
    // Handle failed login
}
```

**Recommendation:** ✓ Already secure

---

### 3. **SQL Injection Protection** ✅
**Status:** Good

**Strengths:**
- ✅ Prepared statements used everywhere
- ✅ Parameterized queries with `?` placeholders
- ✅ No direct string concatenation in queries
- ✅ Input binding with `execute([$params])`

**Code Example:**
```php
// SECURE: Using prepared statements
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);

// NOT FOUND IN CODEBASE (Good!):
// $db->query("SELECT * FROM users WHERE id = " . $_POST['id']); ❌
```

**Test Results:**
- No SQL injection vulnerabilities found in audit ✓

---

### 4. **File Upload Security** ✅
**Status:** Good (with AI verification)

**Strengths:**
- ✅ File type validation (whitelist)
- ✅ File size limit (10MB)
- ✅ Extension checking
- ✅ Unique filename generation
- ✅ Organized storage by user/form
- ✅ AI content verification (optional)

**Code Example:**
```php
// api/upload-evidence.php
$allowedTypes = ['pdf','doc','docx','xls','xlsx','jpg','jpeg','png','gif','webp'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedTypes)) {
    // Reject upload
}

if ($file['size'] > 10485760) { // 10MB
    // Reject upload
}
```

**Recommendation:** ✓ Already secure (consider adding MIME type check)

---

### 5. **XSS Protection (Output Escaping)** ✅
**Status:** Good

**Strengths:**
- ✅ Using `htmlspecialchars()` helper function `h()`
- ✅ Escaping user input before output
- ✅ No direct `echo $_GET` or `echo $_POST`

**Code Example:**
```php
// Helper function
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Usage in views
<div><?= h($user['name']) ?></div>
```

**Recommendation:** ✓ Continue using `h()` everywhere

---

## ⚠️ Security Vulnerabilities (What Needs Fixing)

### 1. **CSRF Protection Missing** ⚠️ HIGH PRIORITY
**Status:** VULNERABLE

**Problem:**
- ❌ No CSRF tokens implemented
- ❌ Forms can be submitted from external sites
- ❌ Attackers can forge requests if user is logged in

**Attack Scenario:**
```html
<!-- Attacker's malicious website -->
<form action="https://ipcr.isu.edu.ph/admin/users.php" method="POST">
  <input type="hidden" name="action" value="delete_user">
  <input type="hidden" name="user_id" value="5">
</form>
<script>document.forms[0].submit();</script>
```
If admin is logged in and visits this page, user #5 gets deleted!

**Solution Required:**
```php
// 1. Generate CSRF token (in auth.php)
function generateCSRFToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}

// 2. Add to forms (in header.php)
<meta name="csrf-token" content="<?= generateCSRFToken() ?>">

// 3. Verify in POST handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        die('CSRF token validation failed');
    }
}
```

**Priority:** HIGH - Should be fixed before production

---

### 2. **Session Security Incomplete** ⚠️ MEDIUM PRIORITY
**Status:** Partially Secure

**Current Implementation:**
```php
// config/auth.php
ini_set('session.cookie_httponly', 1);  // ✅ Good
ini_set('session.use_strict_mode', 1);  // ✅ Good
session_name('IPCR_SESSION');           // ✅ Good
session_start();
```

**Missing Security Settings:**
```php
// ❌ Not implemented:
ini_set('session.cookie_secure', 1);      // HTTPS only
ini_set('session.cookie_samesite', 'Strict'); // CSRF protection
session_regenerate_id(true);              // Session fixation protection
```

**Solution Required:**
```php
function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        // Existing settings
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        
        // ADD THESE:
        ini_set('session.cookie_secure', 1);        // HTTPS only
        ini_set('session.cookie_samesite', 'Strict'); // No cross-site
        ini_set('session.gc_maxlifetime', 21600);   // 6 hours
        
        session_name('IPCR_SESSION');
        session_start();
        
        // Session fixation protection
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }
    }
}
```

**Priority:** MEDIUM - Important for production with HTTPS

---

### 3. **Input Validation Gaps** ⚠️ MEDIUM PRIORITY
**Status:** Incomplete

**Current State:**
- ✅ File upload validation (good)
- ⚠️ Form input validation (inconsistent)
- ⚠️ Email validation (basic)
- ⚠️ No input sanitization layer

**Problem Areas:**

**Example 1: Email Validation**
```php
// admin/users.php
$email = trim($_POST['email'] ?? '');
// ⚠️ No validation if email is valid format
```

**Example 2: Numeric Input**
```php
// Various files
$userId = (int)($_GET['id'] ?? 0);
// ✅ Good: Type casting
// But no check if ID exists or user has access
```

**Solution Required:**
```php
// Add validation helper functions

function validateEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validateEmployeeId(string $id): bool {
    // Format: YYYY-XXXXX
    return preg_match('/^\d{4}-\d{5}$/', $id) === 1;
}

function validateInput(array $rules, array $data): array {
    $errors = [];
    foreach ($rules as $field => $rule) {
        if ($rule['required'] && empty($data[$field])) {
            $errors[$field] = "{$field} is required";
        }
        if (isset($rule['email']) && !validateEmail($data[$field])) {
            $errors[$field] = "Invalid email format";
        }
        // ... more rules
    }
    return $errors;
}

// Usage:
$errors = validateInput([
    'email' => ['required' => true, 'email' => true],
    'employee_id' => ['required' => true, 'pattern' => '/^\d{4}-\d{5}$/']
], $_POST);

if (!empty($errors)) {
    // Show errors
}
```

**Priority:** MEDIUM - Should be added systematically

---

### 4. **Security Headers Missing** ⚠️ LOW PRIORITY
**Status:** Not Implemented

**Missing Headers:**
```http
❌ X-Frame-Options: DENY
❌ X-Content-Type-Options: nosniff
❌ X-XSS-Protection: 1; mode=block
❌ Referrer-Policy: strict-origin-when-cross-origin
❌ Permissions-Policy: (various)
❌ Content-Security-Policy: (various)
```

**Solution Required:**
```php
// Add to includes/header.php (after session_start)
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// For production with HTTPS:
// header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
```

**Or via .htaccess:**
```apache
# Add to .htaccess
<IfModule mod_headers.c>
    Header always set X-Frame-Options "DENY"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

**Priority:** LOW - Nice to have, but not critical

---

### 5. **File Download Security** ⚠️ LOW PRIORITY
**Status:** Needs Path Traversal Protection

**Current Code:**
```php
// api/download-evidence.php
$file = $db->prepare("SELECT * FROM evidence_files WHERE id=?");
$file->execute([$fileId]);
$file = $file->fetch();

$filePath = __DIR__ . '/../' . $file['file_path'];
// ⚠️ No validation if file_path contains '..' or '/etc/passwd'
```

**Attack Scenario:**
```
If attacker can somehow inject '../../../etc/passwd' into file_path,
they could download system files!
```

**Solution Required:**
```php
// api/download-evidence.php
$filePath = __DIR__ . '/../' . $file['file_path'];

// ADD PATH TRAVERSAL PROTECTION:
$realPath = realpath($filePath);
$baseDir = realpath(__DIR__ . '/../uploads');

if ($realPath === false || strpos($realPath, $baseDir) !== 0) {
    http_response_code(403);
    exit('Access denied');
}

// Also check file ownership
if ($currentRole === 'faculty' && $file['user_id'] != $currentUserId) {
    http_response_code(403);
    exit('Access denied');
}
```

**Priority:** LOW - Already protected by database IDs, but add defense in depth

---

## 🔐 Additional Security Recommendations

### 1. **Rate Limiting**
**Current:** Only on login (5 attempts)  
**Recommendation:** Add rate limiting to:
- File uploads (max 10/minute per user)
- API endpoints (max 60/minute per IP)
- Form submissions (max 5/minute per user)

**Implementation:**
```php
function checkRateLimit(string $key, int $maxAttempts, int $window): bool {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM rate_limits 
        WHERE rate_key = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
    ");
    $stmt->execute([$key, $window]);
    $count = $stmt->fetchColumn();
    
    if ($count >= $maxAttempts) {
        return false; // Rate limit exceeded
    }
    
    // Log this attempt
    $db->prepare("INSERT INTO rate_limits (rate_key, created_at) VALUES (?, NOW())")
       ->execute([$key]);
    
    return true;
}

// Usage:
if (!checkRateLimit("upload_{$userId}", 10, 60)) {
    exit('Too many uploads. Please try again later.');
}
```

---

### 2. **Audit Logging Enhancement**
**Current:** Basic audit log exists  
**Recommendation:** Log security events:
- Failed login attempts (IP, timestamp)
- Permission denials (user, attempted action)
- File downloads (who downloaded what)
- Admin actions (user creation, deletion)

---

### 3. **Database Security**
**Recommendations:**
```sql
-- Use separate DB user with limited privileges
GRANT SELECT, INSERT, UPDATE, DELETE ON ipcr_system.* TO 'ipcr_app'@'localhost';
-- DO NOT grant DROP, CREATE, ALTER

-- Revoke FILE privilege
REVOKE FILE ON *.* FROM 'ipcr_app'@'localhost';
```

---

### 4. **Environment Variables**
**Current:** Database credentials in PHP file  
**Recommendation:** Move to `.env` file

```env
# .env
DB_HOST=localhost
DB_NAME=ipcr_system
DB_USER=ipcr_app
DB_PASS=secure_password_here
```

```php
// config/database.php
$host = getenv('DB_HOST');
$name = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
```

---

### 5. **HTTPS Enforcement**
**For Production:**
```php
// Add to includes/header.php
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}
```

---

## 📊 Security Scorecard

| Category | Status | Score | Notes |
|----------|--------|-------|-------|
| **Authentication** | ✅ Good | 9/10 | Strong, with lockout |
| **Authorization** | ✅ Fixed | 9/10 | Recently fixed |
| **Password Security** | ✅ Good | 9/10 | BCrypt with salt |
| **SQL Injection** | ✅ Protected | 10/10 | Prepared statements |
| **XSS Protection** | ✅ Good | 8/10 | Output escaping used |
| **File Upload** | ✅ Good | 8/10 | Type & size validation |
| **CSRF Protection** | ❌ Missing | 0/10 | **NEEDS FIX** |
| **Session Security** | ⚠️ Partial | 6/10 | Missing cookie flags |
| **Input Validation** | ⚠️ Incomplete | 6/10 | Inconsistent |
| **Security Headers** | ❌ Missing | 0/10 | Should add |
| **Rate Limiting** | ⚠️ Login Only | 4/10 | Need more coverage |
| **Audit Logging** | ⚠️ Basic | 6/10 | Could be better |
| **Overall** | ⚠️ Good | **7.5/10** | Production-ready with fixes |

---

## 🎯 Action Plan (Priority Order)

### **Before Production Deployment:**

#### 1. **HIGH PRIORITY (Fix Immediately)** 🔴
- [ ] Implement CSRF protection (tokens)
- [ ] Complete session security settings
- [ ] Add systematic input validation

#### 2. **MEDIUM PRIORITY (Fix Soon)** 🟡
- [ ] Add security headers
- [ ] Enhance rate limiting
- [ ] Improve audit logging
- [ ] Add path traversal protection

#### 3. **LOW PRIORITY (Nice to Have)** 🟢
- [ ] Move credentials to .env
- [ ] Add database user restrictions
- [ ] Implement Content Security Policy
- [ ] Add automated security testing

---

## 🧪 Security Testing Checklist

### Manual Testing:
- [x] Authorization bypass test (PASSED - Fixed!)
- [ ] CSRF attack test (Not tested - protection not implemented)
- [x] SQL injection test (PASSED)
- [x] XSS test (PASSED)
- [x] File upload malicious file test (PASSED)
- [ ] Session fixation test (Not tested)
- [ ] Session hijacking test (Not tested)

### Automated Testing:
- [ ] Run OWASP ZAP scan
- [ ] Run SQLMap for SQL injection
- [ ] Run Nikto web scanner
- [ ] Run Burp Suite security scan

---

## 📝 Conclusion

**Is the system hackable?**

**Short Answer:** Yes and No.

**Detailed Answer:**
- ✅ **Basic security is GOOD** - SQL injection, XSS, and authorization are properly handled
- ⚠️ **CSRF vulnerability exists** - Attackers can forge requests if user is logged in
- ⚠️ **Session security incomplete** - Need HTTPS-only and SameSite cookies
- ⚠️ **Input validation gaps** - Some forms lack proper validation

**Risk Level:**
- **Without CSRF fix:** MEDIUM RISK (6/10)
- **With CSRF fix:** LOW RISK (8/10)

**Recommendation:**
1. **Implement CSRF protection** before production deployment
2. **Enable HTTPS** and proper session security
3. **Add systematic input validation**
4. Then system is production-ready with GOOD security posture

---

**Next Steps:**
1. Review this report with development team
2. Prioritize fixes based on risk level
3. Implement HIGH priority fixes first
4. Re-test after each fix
5. Deploy to production only after HIGH priority items are resolved

---

**Report Status:** ✅ Complete  
**Next Review:** After implementing HIGH priority fixes  
**Contact:** security@isu.edu.ph
