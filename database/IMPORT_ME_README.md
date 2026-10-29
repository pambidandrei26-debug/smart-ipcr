# 🚀 READY TO IMPORT!

## ⭐ USE THIS FILE: `FRESH_START_WITH_DATA.sql`

This is your complete database with:
- ✅ All tables and structure
- ✅ Complete sample data from original
- ✅ **PRE-CONFIGURED API KEYS!**
- ✅ Ready for immediate testing

---

## 🎯 Quick Import (One Command)

### Via Command Line:
```bash
mysql -u username -p u907280979_borrowing < FRESH_START_WITH_DATA.sql
```

### Via phpMyAdmin:
1. Open phpMyAdmin
2. Select database: `u907280979_borrowing`
3. Click "Import"
4. Choose file: `FRESH_START_WITH_DATA.sql`
5. Click "Go"
6. Wait for completion (may take 1-2 minutes)

---

## ✅ What's Included

### Pre-Configured API Keys:
```
Gemini API:     [Your Gemini API key - get from https://makersuite.google.com/app/apikey]
OpenRouter API: [Your OpenRouter API key - get from https://openrouter.ai/keys]
Active Provider: OpenRouter (100% FREE)
AI Mode: Advisory (safe for production)
```

### Complete Data:
- ✅ All system tables
- ✅ Sample users (admin, dean, secretary, faculty)
- ✅ Sample departments
- ✅ Sample rating periods
- ✅ Complete IPCR template
- ✅ MFOs, KRAs, Success Indicators
- ✅ Sample IPCR forms with data
- ✅ Audit logs and history
- ✅ Everything from original ipcr_system.sql

### Default Users:
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@isu.edu.ph | password123 |
| Dean | dean@isu.edu.ph | password123 |
| Secretary | secretary@isu.edu.ph | password123 |
| Faculty | faculty@isu.edu.ph | password123 |

---

## 🧪 After Import - Test Immediately

### 1. Verify API Keys:
```
https://your-domain.com/verify-api-keys-from-db.php
```

Expected:
```
✅ Database connected
✅ Gemini API Key: CONFIGURED
✅ OpenRouter API Key: CONFIGURED
✅ Active Provider: OPENROUTER
✅ Mode: advisory
```

### 2. Test Both AI Providers:
```
https://your-domain.com/test-both-ai-providers.php
```

Expected:
```
✅ Gemini API: Working
✅ OpenRouter API: Working
✅ Side-by-side comparison
```

### 3. Login:
```
https://your-domain.com/
```
Use any of the default users above.

### 4. Test AI Verification:
1. Login as `faculty@isu.edu.ph`
2. Go to Faculty > IPCR Form
3. Upload evidence file
4. Watch AI verify it automatically! 🤖

---

## 📊 What This Resets

| Item | Action |
|------|--------|
| All Tables | Dropped & Recreated |
| All Data | Replaced with fresh copy |
| API Keys | Auto-configured |
| Users | Reset to defaults |
| Passwords | Reset to "password123" |
| IPCR Forms | Sample data included |
| Settings | Reset to defaults |

---

## ⚠️ Important Notes

### 1. Database Name
Make sure line 30 in the SQL file matches your database:
```sql
USE u907280979_borrowing;  -- Your database name
```

### 2. Backup First!
If you have existing data you want to keep:
```bash
mysqldump -u username -p u907280979_borrowing > backup.sql
```

### 3. File Permissions
After import, ensure uploads folder exists:
```bash
mkdir uploads
chmod 755 uploads
```

### 4. .htaccess
Make sure `.htaccess` is in place for proper routing.

---

## 🔄 Files Available

| File | Purpose | Use When |
|------|---------|----------|
| **FRESH_START_WITH_DATA.sql** | **RECOMMENDED** | Fresh start with full data + API keys |
| ipcr_system.sql | Original backup | Same as above (source file) |
| FRESH_START.sql | Minimal data only | Want clean slate with less data |
| update_api_keys.sql | Update keys only | Already have database |

---

## 💡 Why This File?

### FRESH_START_WITH_DATA.sql gives you:
1. ✅ **Complete Reset** - Drops everything first
2. ✅ **Full Data** - All sample data from original
3. ✅ **API Keys** - Pre-configured and ready
4. ✅ **One File** - Import once, done!
5. ✅ **Tested** - Known working configuration

### vs. Other Files:
- `ipcr_system.sql` - Same content, different name
- `FRESH_START.sql` - Minimal data, faster but less examples
- `update_api_keys.sql` - Only updates keys, doesn't reset

---

## 🐛 Troubleshooting

### Issue: Import takes too long
**Normal!** File is large with complete data. Wait 1-2 minutes.

### Issue: Foreign key errors
**Solution:**
```sql
SET FOREIGN_KEY_CHECKS = 0;
-- Import file
SET FOREIGN_KEY_CHECKS = 1;
```

### Issue: Database name mismatch
**Solution:** Edit line 30 of SQL file to match your database name.

### Issue: Can't login after import
**Solution:** Password is `password123` for ALL default users.

### Issue: API keys not working
**Solution:**
1. Run: `https://your-domain.com/verify-api-keys-from-db.php`
2. Check if keys exist in database
3. Test with: `https://your-domain.com/test-both-ai-providers.php`

---

## ✅ Success Checklist

After importing, verify:

- [ ] Database imported without errors
- [ ] Can login with default users
- [ ] API keys visible in verification script
- [ ] Both AI providers work in test
- [ ] Can create new IPCR form
- [ ] Can upload evidence files
- [ ] AI verification runs automatically
- [ ] System is ready for production testing

---

## 🎉 You're Ready!

**Time to import:** ~2 minutes
**Time to test:** ~5 minutes
**Total time:** ~7 minutes

**Result:** Complete IPCR system with AI verification ready to go! 🚀

---

## Quick Command Checklist

```bash
# 1. Backup (if needed)
mysqldump -u username -p u907280979_borrowing > backup_$(date +%Y%m%d).sql

# 2. Import fresh database
mysql -u username -p u907280979_borrowing < FRESH_START_WITH_DATA.sql

# 3. Verify (in browser)
# https://your-domain.com/verify-api-keys-from-db.php

# 4. Test (in browser)
# https://your-domain.com/test-both-ai-providers.php

# 5. Login and enjoy!
# https://your-domain.com/
```

---

**Status:** ✅ Ready to Import
**Data:** ✅ Complete with Samples
**API Keys:** ✅ Pre-configured
**Testing:** ✅ Can start immediately

**LET'S GO!** 🎊
