# IPCR System - Production Deployment Guide

**Target Audience:** System Administrators, DevOps Engineers  
**Estimated Time:** 30-60 minutes  
**Prerequisites:** Server with PHP 7.4+, MySQL 5.7+, Apache/Nginx with mod_rewrite

---

## 🚀 QUICK START (5 STEPS)

### 1. Upload Files
```bash
# Upload all files to web server
# Typical location: /var/www/html/ipcr or C:\inetpub\wwwroot\ipcr
```

### 2. Configure Environment
```bash
# Copy environment template
cp .env.example .env

# Edit .env with production credentials
nano .env  # or use your preferred editor
```

Required `.env` values:
```env
# Database Configuration
DB_HOST=localhost
DB_NAME=ipcr_system
DB_USER=ipcr_user
DB_PASS=STRONG_PASSWORD_HERE

# Application Settings
APP_ENV=production
APP_DEBUG=false
BASE_URL=https://yourdomain.com/ipcr

# Session Configuration
SESSION_LIFETIME=43200

# Email Configuration (for notifications)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASS=your-app-password
SMTP_FROM=noreply@yourdomain.com
```

### 3. Import Database
```bash
# Import SQL file
mysql -u root -p < database/FRESH_START_WITH_DATA.sql

# Or use phpMyAdmin to import the SQL file
```

### 4. Enable HTTPS
Edit `.htaccess` and uncomment lines 8-12:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

And line 22:
```apache
Header set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
```

### 5. Set Permissions
```bash
# Set proper file permissions
chmod 755 /var/www/html/ipcr
chmod 644 .env
chmod 755 uploads/
chmod 644 config/*.php

# Make sure Apache can write to uploads
chown -R www-data:www-data uploads/
```

---

## 🔒 SECURITY CHECKLIST

### Before Going Live

- [ ] **SSL Certificate Installed**
  ```bash
  # Test HTTPS
  curl -I https://yourdomain.com/ipcr
  ```

- [ ] **HTTPS Redirect Active**
  - Access site via HTTP
  - Should automatically redirect to HTTPS

- [ ] **.env File Protected**
  ```bash
  chmod 600 .env
  # Verify it's not accessible via browser
  curl https://yourdomain.com/ipcr/.env  # Should return 403
  ```

- [ ] **PHP Production Settings**
  Edit `php.ini`:
  ```ini
  display_errors = Off
  display_startup_errors = Off
  error_reporting = E_ALL
  log_errors = On
  error_log = /var/log/php/error.log
  ```

- [ ] **Database User Permissions**
  ```sql
  -- Grant only necessary privileges
  GRANT SELECT, INSERT, UPDATE, DELETE ON ipcr_system.* TO 'ipcr_user'@'localhost';
  FLUSH PRIVILEGES;
  
  -- Verify permissions
  SHOW GRANTS FOR 'ipcr_user'@'localhost';
  ```

- [ ] **Remove Development Files**
  ```bash
  rm -f test-*.php
  rm -f phpinfo.php
  rm -rf .git/
  ```

- [ ] **Verify Security Headers**
  ```bash
  curl -I https://yourdomain.com/ipcr
  ```
  
  Should see:
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: SAMEORIGIN`
  - `X-XSS-Protection: 1; mode=block`
  - `Strict-Transport-Security: max-age=31536000`
  - `Content-Security-Policy: ...`

- [ ] **Test Session Security**
  - Login to system
  - Check cookies in DevTools (F12)
  - Verify:
    - `HttpOnly` flag is set
    - `Secure` flag is set
    - `SameSite=Strict` is set

- [ ] **Test CSRF Protection**
  - Try submitting a form without CSRF token
  - Should get "403 Forbidden" error

- [ ] **Test Error Handling**
  - Trigger a safe error (e.g., invalid URL)
  - Should see generic error page, NOT stack trace
  - Check error log for detailed error

- [ ] **Backup Strategy**
  ```bash
  # Setup daily database backup
  0 2 * * * mysqldump -u backup_user -p ipcr_system > /backups/ipcr_$(date +\%Y\%m\%d).sql
  
  # Setup file backup
  0 3 * * * tar -czf /backups/ipcr_files_$(date +\%Y\%m\%d).tar.gz /var/www/html/ipcr/uploads/
  ```

---

## 🧪 TESTING AFTER DEPLOYMENT

### 1. Authentication Testing
```bash
# Test login with valid credentials
# Test login with invalid credentials (should lockout after 5 attempts)
# Test logout
# Verify session expires after 30 minutes of inactivity
```

### 2. Authorization Testing
```bash
# Login as Faculty
# Try to access /admin/users.php (should be redirected)

# Login as Admin
# Access /admin/users.php (should work)
```

### 3. CSRF Testing
```bash
# Use browser DevTools
# Copy a POST request
# Remove csrf_token field
# Replay request (should fail with 403)
```

### 4. Session Timeout Testing
```bash
# Login to system
# Wait 31 minutes
# Try to access a protected page
# Should be redirected to login with "session_expired" message
```

### 5. HTTPS Testing
```bash
# Access site via HTTP
curl -I http://yourdomain.com/ipcr
# Should return 301 redirect to HTTPS

# Verify Secure cookie flag
# Login and check cookies - Secure flag should be present
```

---

## 📊 MONITORING

### Log Files to Monitor

1. **PHP Error Log**
   ```bash
   tail -f /var/log/php/error.log
   ```

2. **Apache/Nginx Error Log**
   ```bash
   tail -f /var/log/apache2/error.log
   # or
   tail -f /var/log/nginx/error.log
   ```

3. **Apache/Nginx Access Log**
   ```bash
   tail -f /var/log/apache2/access.log
   # or
   tail -f /var/log/nginx/access.log
   ```

4. **Application Audit Log**
   - Check `audit_log` table in database
   ```sql
   SELECT * FROM audit_log ORDER BY performed_at DESC LIMIT 50;
   ```

### Key Metrics to Track

- Failed login attempts (potential brute force)
- CSRF token failures (potential CSRF attack)
- Session hijacking attempts (IP mismatch)
- File upload errors
- Database connection failures

---

## 🆘 TROUBLESHOOTING

### Issue: White screen (500 error)

**Solution:**
```bash
# Check PHP error log
tail -f /var/log/php/error.log

# Check .env file exists and is readable
ls -la .env

# Verify database connection
mysql -u ipcr_user -p ipcr_system
```

### Issue: HTTPS redirect not working

**Solution:**
```bash
# Verify mod_rewrite is enabled
a2enmod rewrite
systemctl restart apache2

# Check .htaccess is being read
# Add this to Apache config:
<Directory /var/www/html/ipcr>
    AllowOverride All
</Directory>
```

### Issue: Session expires immediately

**Solution:**
```bash
# Check session directory permissions
ls -la /var/lib/php/sessions

# Verify PHP session settings
php -i | grep session
```

### Issue: CSRF token validation fails

**Solution:**
- Clear browser cookies
- Check if session is working
- Verify CSRF meta tag exists in page source
- Check browser console for JavaScript errors

---

## 🔄 UPDATES AND MAINTENANCE

### Monthly Tasks
- [ ] Review audit logs for suspicious activity
- [ ] Check for failed login attempts
- [ ] Verify backups are working
- [ ] Update PHP/MySQL if needed
- [ ] Review and rotate credentials

### Quarterly Tasks
- [ ] Run security scan (e.g., OWASP ZAP)
- [ ] Review and update dependencies
- [ ] Test disaster recovery procedure
- [ ] Review user accounts (remove inactive)

---

## 📞 SUPPORT CONTACTS

- **System Administrator:** [Your Email]
- **Database Administrator:** [DBA Email]
- **Security Team:** [Security Email]
- **Developer:** [Dev Email]

---

## 📚 ADDITIONAL RESOURCES

- Security Testing Report: `IPCR-Web-App-Security-Testing-Report.docx`
- Security Fixes: `SECURITY_FIXES_COMPLETED.md`
- Security Status: `SECURITY_STATUS_REPORT.md`
- Database Import Guide: `DATABASE_IMPORT_GUIDE.md`
- Deployment Guide: `DEPLOYMENT_GUIDE.md`

---

**Document Version:** 1.0  
**Last Updated:** October 29, 2026  
**Status:** Production Ready ✅
