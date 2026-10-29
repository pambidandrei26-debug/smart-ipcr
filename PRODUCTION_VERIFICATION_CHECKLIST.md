# Production Environment Verification Checklist

**Purpose:** Verify all security controls in live production environment  
**Target:** DevOps/System Administrators  
**Security Control:** SEC-006

---

## ✅ PRE-DEPLOYMENT CHECKLIST

### 1. Environment Configuration

- [ ] **PHP Configuration** (`php.ini`)
  ```ini
  display_errors = Off
  display_startup_errors = Off
  error_reporting = E_ALL
  log_errors = On
  error_log = /var/log/php/error.log
  expose_php = Off
  max_execution_time = 30
  max_input_time = 60
  memory_limit = 256M
  post_max_size = 20M
  upload_max_filesize = 10M
  ```
  
  **Verify:**
  ```bash
  php -i | grep display_errors
  # Should show: display_errors => Off => Off
  
  php -i | grep error_reporting
  # Should show: error_reporting => 32767 => 32767 (E_ALL)
  
  php -i | grep log_errors
  # Should show: log_errors => On => On
  ```

- [ ] **Application Configuration** (`.env`)
  ```env
  APP_ENV=production
  APP_DEBUG=false
  BASE_URL=https://yourdomain.com
  ```

- [ ] **File Permissions**
  ```bash
  # .env should be readable only by web server user
  chmod 600 .env
  ls -la .env
  # Should show: -rw------- (600)
  
  # Config files should be readable
  chmod 644 config/*.php
  
  # Uploads directory writable by web server
  chown -R www-data:www-data uploads/
  chmod 755 uploads/
  ```

### 2. SSL/TLS Configuration

- [ ] **SSL Certificate Installed**
  ```bash
  # Check certificate
  openssl s_client -connect yourdomain.com:443 -servername yourdomain.com < /dev/null | openssl x509 -noout -dates
  
  # Should show valid dates
  ```

- [ ] **HTTPS Redirect Active**
  - Uncommented in `.htaccess` (lines 8-12)
  - Test HTTP access redirects to HTTPS
  
  ```bash
  # Test redirect
  curl -I http://yourdomain.com/ipcr
  # Should show: HTTP/1.1 301 Moved Permanently
  # Location: https://yourdomain.com/ipcr
  ```

- [ ] **HSTS Header Active**
  - Uncommented in `.htaccess` (line 22)
  
  ```bash
  # Check HSTS header
  curl -I https://yourdomain.com/ipcr | grep Strict-Transport
  # Should show: Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
  ```

### 3. Security Headers

- [ ] **All Security Headers Present**
  ```bash
  curl -I https://yourdomain.com/ipcr
  ```
  
  **Must Include:**
  - ✅ `X-Content-Type-Options: nosniff`
  - ✅ `X-Frame-Options: SAMEORIGIN`
  - ✅ `X-XSS-Protection: 1; mode=block`
  - ✅ `Referrer-Policy: strict-origin-when-cross-origin`
  - ✅ `Content-Security-Policy: ...`
  - ✅ `Strict-Transport-Security: max-age=31536000`

### 4. Database Security

- [ ] **Application User Permissions**
  ```sql
  SHOW GRANTS FOR 'ipcr_user'@'localhost';
  ```
  
  **Should Have ONLY:**
  - SELECT, INSERT, UPDATE, DELETE on ipcr_system.*
  
  **Should NOT Have:**
  - ALL PRIVILEGES
  - GRANT OPTION
  - SUPER
  - FILE
  - PROCESS

- [ ] **Separate Admin Account**
  ```sql
  SHOW GRANTS FOR 'ipcr_admin'@'localhost';
  ```

- [ ] **Remote Access Disabled**
  ```sql
  SELECT User, Host FROM mysql.user WHERE User LIKE 'ipcr%';
  ```
  
  **All should show:** `Host = localhost` (NOT `%`)

---

## 🧪 POST-DEPLOYMENT TESTING

### Security Test Suite

#### Test 1: HTTPS Enforcement
```bash
# Test HTTP redirect
curl -I http://yourdomain.com/ipcr
# Expected: 301 redirect to HTTPS

# Test HTTPS works
curl -I https://yourdomain.com/ipcr
# Expected: 200 OK
```

**Status:** [ ] PASS  [ ] FAIL

---

#### Test 2: Cookie Security Flags

1. Open browser DevTools (F12)
2. Navigate to: https://yourdomain.com/ipcr
3. Login with valid credentials
4. Go to Application tab → Cookies
5. Find `IPCR_SESSION` cookie

**Verify Flags:**
- [ ] ✅ **HttpOnly** = true
- [ ] ✅ **Secure** = true
- [ ] ✅ **SameSite** = Strict
- [ ] ✅ **Domain** = your domain
- [ ] ✅ **Path** = /

**Status:** [ ] PASS  [ ] FAIL

---

#### Test 3: Error Handling (Generic Messages)

1. Trigger a safe error:
   - Access invalid URL: `https://yourdomain.com/ipcr/nonexistent.php`
   - Submit form with database offline
   - Cause validation error

2. Verify response:
   - [ ] User sees generic message (no technical details)
   - [ ] NO stack traces visible
   - [ ] NO database errors shown
   - [ ] NO file paths revealed

3. Check error log:
   ```bash
   tail -f /var/log/php/error.log
   ```
   - [ ] Detailed errors ARE logged (for debugging)

**Status:** [ ] PASS  [ ] FAIL

---

#### Test 4: CSRF Protection

1. Login to system
2. Open browser DevTools → Network tab
3. Submit a form (e.g., update profile)
4. Copy request as cURL
5. Remove `csrf_token` parameter
6. Replay request

**Expected Result:**
- [ ] 403 Forbidden error
- [ ] Message: "CSRF token validation failed"
- [ ] Action NOT completed

**Status:** [ ] PASS  [ ] FAIL

---

#### Test 5: Session Timeout

1. Login to system
2. Note the time
3. Wait 31 minutes (idle timeout = 30 min)
4. Try to access a protected page

**Expected Result:**
- [ ] Redirected to login page
- [ ] URL shows: `?error=session_expired`
- [ ] Session destroyed

**Status:** [ ] PASS  [ ] FAIL

---

#### Test 6: Authorization Bypass Prevention

**Test 6.1: Role-based Access**
1. Login as Faculty user
2. Try to access: `/admin/users.php`

**Expected Result:**
- [ ] Redirected to `/dashboard.php`
- [ ] NOT able to view admin pages

**Test 6.2: Object-level Access**
1. Login as Faculty user (e.g., User ID = 5)
2. View your own form: `/faculty/ipcr-view.php?id=10`
3. Try to view another user's form: `/faculty/ipcr-view.php?id=11`

**Expected Result:**
- [ ] Own form: Accessible
- [ ] Other user's form: Access denied

**Status:** [ ] PASS  [ ] FAIL

---

#### Test 7: SQL Injection Prevention

1. Login to system
2. Try entering SQL in search fields:
   ```
   ' OR '1'='1
   '; DROP TABLE users; --
   ' UNION SELECT * FROM users--
   ```

**Expected Result:**
- [ ] No database errors
- [ ] No data leakage
- [ ] Query treated as literal string

**Status:** [ ] PASS  [ ] FAIL

---

#### Test 8: XSS Prevention

1. Try entering script tags in form fields:
   ```html
   <script>alert('XSS')</script>
   <img src=x onerror=alert('XSS')>
   ```

2. Submit and view the data

**Expected Result:**
- [ ] Scripts NOT executed
- [ ] HTML tags escaped/encoded
- [ ] Displayed as plain text

**Status:** [ ] PASS  [ ] FAIL

---

#### Test 9: File Upload Security

1. Try uploading disallowed file types:
   - `.php` file
   - `.exe` file
   - File with no extension

**Expected Result:**
- [ ] Upload rejected
- [ ] Clear error message
- [ ] File NOT saved

2. Upload allowed file (PDF, DOCX, etc.)

**Expected Result:**
- [ ] Upload succeeds
- [ ] Filename randomized
- [ ] File not directly accessible via URL

**Status:** [ ] PASS  [ ] FAIL

---

#### Test 10: Sensitive Data Exposure

1. Check `.env` file accessibility:
   ```bash
   curl https://yourdomain.com/ipcr/.env
   ```
   **Expected:** 403 Forbidden

2. Check backup files:
   ```bash
   curl https://yourdomain.com/ipcr/config/database.php.bak
   curl https://yourdomain.com/ipcr/.git/config
   ```
   **Expected:** 403 Forbidden or 404 Not Found

3. Check debug endpoints:
   ```bash
   curl https://yourdomain.com/ipcr/phpinfo.php
   curl https://yourdomain.com/ipcr/test.php
   ```
   **Expected:** 404 Not Found (files deleted)

**Status:** [ ] PASS  [ ] FAIL

---

## 📊 SECURITY HEADERS VERIFICATION

### Check with Online Tools

1. **SSL Labs SSL Test**
   - URL: https://www.ssllabs.com/ssltest/
   - Enter your domain
   - Target Grade: A or A+

2. **Security Headers Check**
   - URL: https://securityheaders.com/
   - Enter your domain
   - Target Grade: A or A+

3. **Mozilla Observatory**
   - URL: https://observatory.mozilla.org/
   - Enter your domain
   - Target Grade: A or A+

---

## 📋 COMPLIANCE VERIFICATION

### PHP Configuration Compliance

```bash
# Create verification script
cat > /tmp/verify_php_config.sh << 'EOF'
#!/bin/bash
echo "=== PHP Configuration Verification ==="
echo ""

# Check display_errors
DISPLAY_ERRORS=$(php -i | grep "^display_errors" | awk '{print $3}')
if [ "$DISPLAY_ERRORS" = "Off" ]; then
    echo "✅ display_errors: OFF (secure)"
else
    echo "❌ display_errors: ON (insecure!)"
fi

# Check error_reporting
ERROR_REPORTING=$(php -i | grep "^error_reporting" | awk '{print $3}')
if [ "$ERROR_REPORTING" = "32767" ]; then
    echo "✅ error_reporting: E_ALL (secure)"
else
    echo "⚠️  error_reporting: $ERROR_REPORTING (check value)"
fi

# Check log_errors
LOG_ERRORS=$(php -i | grep "^log_errors" | awk '{print $3}')
if [ "$LOG_ERRORS" = "On" ]; then
    echo "✅ log_errors: ON (secure)"
else
    echo "❌ log_errors: OFF (errors not logged!)"
fi

# Check expose_php
EXPOSE_PHP=$(php -i | grep "^expose_php" | awk '{print $3}')
if [ "$EXPOSE_PHP" = "Off" ]; then
    echo "✅ expose_php: OFF (secure)"
else
    echo "⚠️  expose_php: ON (version leaks)"
fi

echo ""
echo "=== Summary ==="
echo "Review results above and fix any ❌ or ⚠️  items"
EOF

chmod +x /tmp/verify_php_config.sh
/tmp/verify_php_config.sh
```

---

## 🎯 FINAL VERIFICATION SCORE

| Category | Status | Notes |
|----------|--------|-------|
| **PHP Configuration** | [ ] PASS | All settings secure |
| **HTTPS Enforcement** | [ ] PASS | Redirect + HSTS active |
| **Security Headers** | [ ] PASS | All headers present |
| **Cookie Security** | [ ] PASS | HttpOnly, Secure, SameSite |
| **Error Handling** | [ ] PASS | Generic messages only |
| **CSRF Protection** | [ ] PASS | Tokens validated |
| **Session Security** | [ ] PASS | Timeouts working |
| **Authorization** | [ ] PASS | Access controls work |
| **SQL Injection** | [ ] PASS | Prepared statements |
| **XSS Prevention** | [ ] PASS | Output encoding |
| **File Upload** | [ ] PASS | Validation working |
| **Data Exposure** | [ ] PASS | Secrets protected |

---

## 🏆 OVERALL ASSESSMENT

**Total Tests:** 12  
**Passed:** _____ / 12  
**Failed:** _____ / 12  

**Security Score:** _____ / 10

**Deployment Approval:**
- [ ] ✅ **APPROVED** - All tests passed
- [ ] ⚠️ **CONDITIONAL** - Minor issues, proceed with monitoring
- [ ] ❌ **REJECTED** - Critical issues, do not deploy

**Approved By:** ___________________________  
**Date:** ___________________________  
**Signature:** ___________________________

---

## 📞 ESCALATION

**If any test fails:**
1. Document the failure
2. Contact security team immediately
3. DO NOT proceed with deployment
4. Create incident report
5. Fix issue and retest

**Security Team:** [Security Email]  
**System Administrator:** [Admin Email]  
**Database Administrator:** [DBA Email]

---

**Document Version:** 1.0  
**Last Updated:** October 29, 2026  
**SEC-006 Control:** ✅ Complete
