# Database Security Configuration Guide

**Purpose:** Implement least privilege principle for database access  
**Target:** Database Administrators  
**Security Control:** SEC-004

---

## 🔒 Principle of Least Privilege

The application database user should have **ONLY** the permissions necessary to perform its functions. This limits the impact of potential SQL injection or credential compromise.

---

## ✅ RECOMMENDED PERMISSIONS

### For IPCR Application User

```sql
-- Create dedicated user for IPCR application
CREATE USER 'ipcr_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';

-- Grant ONLY necessary permissions
GRANT SELECT, INSERT, UPDATE, DELETE ON ipcr_system.* TO 'ipcr_user'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;
```

### What This Grants:
- ✅ **SELECT** - Read data from tables
- ✅ **INSERT** - Add new records
- ✅ **UPDATE** - Modify existing records
- ✅ **DELETE** - Remove records

### What This DENIES:
- ❌ **DROP** - Cannot delete tables
- ❌ **CREATE** - Cannot create tables
- ❌ **ALTER** - Cannot modify table structure
- ❌ **GRANT** - Cannot give permissions to others
- ❌ **SUPER** - No superuser privileges
- ❌ **FILE** - Cannot read/write OS files
- ❌ **PROCESS** - Cannot view other connections
- ❌ **SHUTDOWN** - Cannot shutdown database

---

## 🔍 VERIFY CURRENT PERMISSIONS

### Check What User Has

```sql
-- Show all grants for application user
SHOW GRANTS FOR 'ipcr_user'@'localhost';
```

### Expected Output (Secure):
```sql
GRANT USAGE ON *.* TO 'ipcr_user'@'localhost'
GRANT SELECT, INSERT, UPDATE, DELETE ON `ipcr_system`.* TO 'ipcr_user'@'localhost'
```

### Bad Output (Insecure):
```sql
GRANT ALL PRIVILEGES ON *.* TO 'ipcr_user'@'localhost'
-- ☠️ TOO MUCH POWER! This is dangerous!
```

---

## ⚠️ COMMON MISTAKES TO AVOID

### ❌ DON'T DO THIS:

```sql
-- WRONG: All privileges on all databases
GRANT ALL PRIVILEGES ON *.* TO 'ipcr_user'@'localhost';

-- WRONG: Superuser privilege
GRANT SUPER ON *.* TO 'ipcr_user'@'localhost';

-- WRONG: File access
GRANT FILE ON *.* TO 'ipcr_user'@'localhost';

-- WRONG: Allowing connections from anywhere
GRANT SELECT ON ipcr_system.* TO 'ipcr_user'@'%';
```

### ✅ DO THIS INSTEAD:

```sql
-- CORRECT: Minimal permissions on specific database
GRANT SELECT, INSERT, UPDATE, DELETE ON ipcr_system.* TO 'ipcr_user'@'localhost';

-- CORRECT: Localhost only
-- Only connections from same server allowed
```

---

## 🛡️ ADDITIONAL SECURITY MEASURES

### 1. Separate Admin Account

```sql
-- Create admin user for schema changes (migrations)
CREATE USER 'ipcr_admin'@'localhost' IDENTIFIED BY 'DIFFERENT_STRONG_PASSWORD';

-- Admin can modify structure
GRANT ALL PRIVILEGES ON ipcr_system.* TO 'ipcr_admin'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;
```

**Usage:**
- Use `ipcr_user` for running application (in `.env`)
- Use `ipcr_admin` ONLY for database migrations/changes
- NEVER put `ipcr_admin` credentials in `.env`

### 2. Read-Only User (For Reporting)

```sql
-- Create read-only user for reports/analytics
CREATE USER 'ipcr_reports'@'localhost' IDENTIFIED BY 'REPORT_PASSWORD';

-- Only SELECT permission
GRANT SELECT ON ipcr_system.* TO 'ipcr_reports'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;
```

### 3. Backup User

```sql
-- Create backup user
CREATE USER 'ipcr_backup'@'localhost' IDENTIFIED BY 'BACKUP_PASSWORD';

-- Only SELECT for mysqldump
GRANT SELECT, LOCK TABLES ON ipcr_system.* TO 'ipcr_backup'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;
```

---

## 🔐 PASSWORD POLICY

### Requirements:
- Minimum 16 characters
- Mix of uppercase, lowercase, numbers, symbols
- NOT based on dictionary words
- Different for each user

### Generate Strong Password:
```bash
# Linux/Mac
openssl rand -base64 24

# Or use online generator
# https://passwordsgenerator.net/
```

---

## 📊 AUDIT CURRENT SETUP

### Step 1: List All Users
```sql
SELECT User, Host FROM mysql.user WHERE User LIKE 'ipcr%';
```

### Step 2: Check Each User's Permissions
```sql
SHOW GRANTS FOR 'ipcr_user'@'localhost';
SHOW GRANTS FOR 'ipcr_admin'@'localhost';
SHOW GRANTS FOR 'ipcr_reports'@'localhost';
SHOW GRANTS FOR 'ipcr_backup'@'localhost';
```

### Step 3: Review for Excessive Privileges

Look for these RED FLAGS:
- ❌ `ALL PRIVILEGES`
- ❌ `GRANT OPTION`
- ❌ `SUPER`
- ❌ `FILE`
- ❌ `PROCESS`
- ❌ `RELOAD`
- ❌ `SHUTDOWN`
- ❌ Connections from `%` (anywhere)

---

## 🔧 FIX OVER-PRIVILEGED USER

If you find a user with too many privileges:

```sql
-- Remove all privileges
REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'ipcr_user'@'localhost';

-- Grant only what's needed
GRANT SELECT, INSERT, UPDATE, DELETE ON ipcr_system.* TO 'ipcr_user'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;

-- Verify
SHOW GRANTS FOR 'ipcr_user'@'localhost';
```

---

## 🧪 TEST PERMISSIONS

### Test 1: Application Functions (Should Work)
```sql
-- Connect as application user
mysql -u ipcr_user -p

-- These should work:
SELECT * FROM users LIMIT 1;
INSERT INTO audit_log (action, remarks) VALUES ('test', 'permission test');
UPDATE users SET last_login = NOW() WHERE id = 1;
DELETE FROM audit_log WHERE remarks = 'permission test';
```

### Test 2: Structural Changes (Should Fail)
```sql
-- Connect as application user
mysql -u ipcr_user -p

-- These should all fail with "Access denied":
DROP TABLE users;
-- ERROR 1142: DROP command denied

CREATE TABLE test (id INT);
-- ERROR 1142: CREATE command denied

ALTER TABLE users ADD COLUMN test VARCHAR(255);
-- ERROR 1142: ALTER command denied

GRANT SELECT ON ipcr_system.* TO 'other_user'@'localhost';
-- ERROR 1044: Access denied
```

**If any of Test 2 commands succeed, permissions are TOO PERMISSIVE!**

---

## 📋 PRODUCTION CHECKLIST

Before going live:

- [ ] Application user has ONLY `SELECT, INSERT, UPDATE, DELETE`
- [ ] Admin user exists with separate credentials
- [ ] Passwords are strong (16+ characters)
- [ ] All users restricted to `@'localhost'`
- [ ] No users have `GRANT OPTION`
- [ ] No users have `SUPER` privilege
- [ ] No users have `FILE` privilege
- [ ] Tested application functions work
- [ ] Tested structural changes are denied
- [ ] Documented all users and their purposes
- [ ] Backup user created with SELECT + LOCK TABLES

---

## 🔄 REGULAR MAINTENANCE

### Monthly Tasks:
```sql
-- Review all database users
SELECT User, Host FROM mysql.user;

-- Check for suspicious activity
SELECT * FROM mysql.general_log WHERE command_type NOT IN ('Query', 'Connect') LIMIT 100;

-- Review failed login attempts (if binary logging enabled)
```

### Quarterly Tasks:
- Rotate database passwords
- Review and document all user accounts
- Remove unused accounts
- Audit permission changes

---

## 📞 CONTACTS

- **Database Administrator:** [DBA Email]
- **Security Team:** [Security Email]
- **System Administrator:** [Admin Email]

---

## 📚 REFERENCES

- [MySQL Security Best Practices](https://dev.mysql.com/doc/refman/8.0/en/security-guidelines.html)
- [MySQL Privilege System](https://dev.mysql.com/doc/refman/8.0/en/privilege-system.html)
- [OWASP Database Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Database_Security_Cheat_Sheet.html)

---

**Document Version:** 1.0  
**Last Updated:** October 29, 2026  
**Status:** ✅ Complete  
**SEC-004 Control:** Implemented
