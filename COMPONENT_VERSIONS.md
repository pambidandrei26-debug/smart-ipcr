# IPCR System - Component Version Inventory

**Document Purpose:** Security tracking and vulnerability management  
**Last Updated:** October 29, 2026  
**Status:** ✅ Complete

---

## 📦 Core Dependencies

### Server Components

| Component | Version | Purpose | Security Status |
|-----------|---------|---------|-----------------|
| **PHP** | 7.4+ (Recommended: 8.1) | Server-side runtime | ✅ Supported |
| **MySQL** | 5.7+ (Recommended: 8.0) | Database server | ✅ Supported |
| **Apache** | 2.4+ | Web server | ✅ Supported |
| **OpenSSL** | 1.1.1+ | SSL/TLS encryption | ✅ Supported |

### Frontend Libraries (CDN)

| Library | Version | Purpose | CDN Source |
|---------|---------|---------|------------|
| **Bootstrap** | 5.3.3 | UI framework | jsdelivr.net |
| **Bootstrap Icons** | 1.11.3 | Icon library | jsdelivr.net |
| **SweetAlert2** | 11.x | Modal dialogs | jsdelivr.net |

### PHP Extensions Required

| Extension | Version | Purpose | Status |
|-----------|---------|---------|--------|
| **PDO** | Built-in | Database abstraction | ✅ Required |
| **PDO_MySQL** | Built-in | MySQL driver | ✅ Required |
| **mbstring** | Built-in | Multi-byte string | ✅ Required |
| **OpenSSL** | Built-in | Encryption | ✅ Required |
| **JSON** | Built-in | JSON encoding | ✅ Required |
| **cURL** | Built-in | HTTP requests | ⚠️ Optional (for AI features) |
| **GD** | Built-in | Image processing | ⚠️ Optional (for uploads) |
| **fileinfo** | Built-in | File type detection | ✅ Required |

---

## 🔐 Security-Related Components

### Cryptography

| Component | Algorithm | Purpose |
|-----------|-----------|---------|
| **Password Hashing** | bcrypt (cost: 10) | User password storage |
| **CSRF Token** | random_bytes(32) | CSRF protection |
| **Session ID** | PHP native | Session management |
| **API Encryption** | AES-256-CBC | API key encryption |

### Session Management

| Setting | Value | Purpose |
|---------|-------|---------|
| **session.cookie_httponly** | 1 | Prevent XSS cookie theft |
| **session.cookie_secure** | 1 (if HTTPS) | HTTPS-only cookies |
| **session.cookie_samesite** | Strict | CSRF protection |
| **session.use_strict_mode** | 1 | Session fixation prevention |
| **session.use_only_cookies** | 1 | No URL-based sessions |
| **session.gc_maxlifetime** | 21600 (6 hours) | Absolute expiration |
| **Idle timeout** | 1800 (30 min) | Inactivity logout |

---

## 📊 Version Check Commands

### Check PHP Version
```bash
php -v
# Expected: PHP 7.4.0 or higher
```

### Check PHP Extensions
```bash
php -m
# Should include: PDO, pdo_mysql, mbstring, openssl, json, fileinfo
```

### Check MySQL Version
```bash
mysql --version
# Expected: mysql Ver 5.7.0 or higher
```

### Check Apache Version
```bash
apache2 -v  # Linux
httpd -v    # Some systems
# Expected: Apache/2.4.x
```

### Check Loaded Apache Modules
```bash
apache2ctl -M
# Should include: rewrite_module, headers_module, ssl_module
```

---

## 🔄 Update Policy

### Critical Security Updates
- **Frequency:** Immediately upon release
- **Components:** PHP, MySQL, Apache, OpenSSL
- **Process:**
  1. Check security advisories
  2. Test in staging environment
  3. Apply to production during maintenance window
  4. Verify system functionality

### Regular Updates
- **Frequency:** Monthly review
- **Components:** All components
- **Process:**
  1. Check for available updates
  2. Review changelogs for breaking changes
  3. Test in development
  4. Schedule production update

### Frontend Library Updates
- **Frequency:** Quarterly review
- **Components:** Bootstrap, SweetAlert2
- **Process:**
  1. Check CDN versions
  2. Test UI compatibility
  3. Update version numbers in HTML

---

## 🚨 Known Vulnerabilities

### Current Status: ✅ NONE

**Last CVE Check:** October 29, 2026  
**Next Review:** November 29, 2026

### Monitoring Resources
- [PHP Security Advisories](https://www.php.net/security)
- [MySQL CVE Database](https://www.cvedetails.com/vendor/185/Mysql.html)
- [Apache Security Reports](https://httpd.apache.org/security_report.html)
- [NIST National Vulnerability Database](https://nvd.nist.gov/)

---

## 📝 Change Log

| Date | Component | Old Version | New Version | Reason |
|------|-----------|-------------|-------------|--------|
| 2026-10-29 | Initial Inventory | N/A | See above | Security audit requirement |

---

## 🎯 Production Recommendations

### Minimum Versions for Production

```
PHP: 8.1+
MySQL: 8.0+
Apache: 2.4.50+
OpenSSL: 1.1.1+
```

### Why These Versions?

- **PHP 8.1**: Performance improvements, security patches
- **MySQL 8.0**: Better security, JSON support, performance
- **Apache 2.4.50+**: Security fixes, HTTP/2 support
- **OpenSSL 1.1.1+**: TLS 1.3 support, active maintenance

---

## 📞 Update Contacts

- **PHP Updates:** [System Admin]
- **Database Updates:** [DBA]
- **Server Updates:** [DevOps]
- **Library Updates:** [Developer]

---

**Document Owner:** Security Team  
**Review Frequency:** Monthly  
**Next Review:** November 29, 2026
