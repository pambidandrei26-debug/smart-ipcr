# 📁 Database Files

## 🎯 Main Files (Use These!)

### 1. `FRESH_START_WITH_DATA.sql` ⭐ PRIMARY IMPORT FILE
**Complete database with pre-configured API keys**

**What's Included:**
- ✅ All tables and complete structure
- ✅ **COMPLETE sample data** from original
- ✅ **Gemini API Key pre-configured**
- ✅ **OpenRouter API Key pre-configured**
- ✅ AI Provider set to **OpenRouter** (100% FREE)
- ✅ AI Mode set to **Advisory** (recommended)
- ✅ Sample users, departments, periods, templates
- ✅ Sample IPCR forms with data
- ✅ Audit logs and complete history

**To Import:**
```bash
mysql -u username -p database_name < FRESH_START_WITH_DATA.sql
```

**After Import - You Get:**
- ✅ Gemini API Key: `[Configure your own key]`
- ✅ OpenRouter API Key: `[Configure your own key]`
- ✅ Active Provider: OpenRouter
- ✅ AI Mode: Advisory
- ✅ Everything ready to configure and test!

---

### 2. `ipcr_system.sql` (BACKUP)
**Original source file - same content as FRESH_START_WITH_DATA.sql**

**Purpose:** 
- Backup/reference copy
- Same data as FRESH_START_WITH_DATA.sql
- Keep for safety

**Note:** Both files have identical content. Use either one.

---

### 3. `update_api_keys.sql` (FOR EXISTING DATABASES)
**Updates API keys in existing database without full reset**

**Use This When:**
- You already imported the database before
- You only want to update API keys
- You don't want to lose existing data

**To Run:**
```bash
mysql -u username -p database_name < update_api_keys.sql
```

**What It Does:**
1. Updates Gemini API key
2. Updates OpenRouter API key
3. Sets OpenRouter as default provider
4. Sets AI mode to Advisory
5. Shows verification query

---

## 📚 Documentation Files

### `IMPORT_ME_README.md` ⭐ READ THIS FIRST
**Complete import guide with step-by-step instructions**

Contains:
- Quick import commands
- What's included
- Testing steps
- Troubleshooting
- Success checklist

### `README.md`
**This file - Database folder documentation**

---

## 🚀 Quick Start Guide

### For New Installation:
```bash
# 1. Import complete database (includes API keys!)
mysql -u username -p database_name < FRESH_START_WITH_DATA.sql

# 2. Verify in browser
https://your-domain.com/verify-api-keys-from-db.php

# 3. Test AI
https://your-domain.com/test-both-ai-providers.php

# Done! ✅
```

### For Existing Installation (Update Keys Only):
```bash
# Run update script
mysql -u username -p database_name < update_api_keys.sql

# Verify
https://your-domain.com/verify-api-keys-from-db.php
```

---

## 🔑 API Keys Configuration

After import, you need to configure your own API keys:

### Gemini API Key
```
Get from: https://makersuite.google.com/app/apikey
```
- Provider: Google Gemini
- Note: May require billing for full access

### OpenRouter API Key
```
Get from: https://openrouter.ai/keys
```
- Provider: OpenRouter
- **100% FREE** - 200 requests/day
- **Recommended as DEFAULT**

Configure in Admin > Settings after importing the database.

---

## 📊 What You Get After Import

### Configuration:
| Setting | Value | Description |
|---------|-------|-------------|
| `ai_provider` | `openrouter` | Active provider (FREE) |
| `ai_verification_mode` | `advisory` | Shows feedback, never blocks |
| `gemini_api_key` | [Full key] | Ready to use |
| `openrouter_api_key` | [Full key] | Ready to use |

### Sample Data:
- ✅ 4 Users (admin, dean, secretary, faculty)
- ✅ Multiple departments
- ✅ Active rating period
- ✅ Complete IPCR template
- ✅ MFOs, KRAs, Success Indicators
- ✅ Required proof types
- ✅ Sample forms with history

### Default Login:
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@isu.edu.ph | password123 |
| Dean | dean@isu.edu.ph | password123 |
| Secretary | secretary@isu.edu.ph | password123 |
| Faculty | faculty@isu.edu.ph | password123 |

---

## ✅ Verification After Import

### Check Configuration:
```sql
SELECT 
    config_key, 
    CASE 
        WHEN config_key LIKE '%api_key%' 
        THEN CONCAT(SUBSTRING(config_value, 1, 15), '...') 
        ELSE config_value 
    END as value
FROM system_config 
WHERE config_key IN (
    'ai_provider', 
    'ai_verification_mode', 
    'gemini_api_key', 
    'openrouter_api_key'
);
```

**Expected Result:**
```
ai_provider          | openrouter
ai_verification_mode | advisory
gemini_api_key       | [Configure in Admin > Settings]
openrouter_api_key   | [Configure in Admin > Settings]
```

---

## 🧪 Testing After Import

### 1. Verify Installation
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

### 2. Test Both Providers
```
https://your-domain.com/test-both-ai-providers.php
```

Expected:
```
✅ Gemini API: Working
✅ OpenRouter API: Working
✅ Side-by-side comparison
✅ Response times
✅ Recommendations
```

### 3. Login & Test Live
```
1. Login: https://your-domain.com/
2. Use: faculty@isu.edu.ph / password123
3. Go to: Faculty > IPCR Form
4. Upload: Any PDF file
5. Watch: AI verifies automatically! 🤖
```

---

## 🔄 File Summary

| File | Size | Purpose | When to Use |
|------|------|---------|-------------|
| **FRESH_START_WITH_DATA.sql** | ~500KB | **PRIMARY** | New setup or complete reset |
| ipcr_system.sql | ~500KB | Backup | Same as above (source) |
| update_api_keys.sql | ~5KB | Update only | Existing database |
| IMPORT_ME_README.md | - | Guide | Read first |
| README.md | - | Docs | Reference |

---

## ⚠️ Important Notes

### Database Name
Line 30 in SQL files uses:
```sql
USE u907280979_borrowing;
```
Change this to match your database name if different.

### Before Importing
If you have existing data:
```bash
# Backup first!
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
```

### After Importing
1. Change default passwords
2. Update institution info in Admin > Settings
3. Verify file permissions on uploads folder
4. Test AI verification

---

## 🐛 Troubleshooting

### Issue: API keys not found after import

**Check:**
```sql
SELECT * FROM system_config WHERE config_key LIKE '%api%';
```

**Solution:** If empty, run `update_api_keys.sql`

### Issue: Database name mismatch

**Solution:** Edit line 30 in SQL file:
```sql
USE your_database_name_here;
```

### Issue: Import fails

**Solution:**
```sql
SET FOREIGN_KEY_CHECKS = 0;
-- Then import
SET FOREIGN_KEY_CHECKS = 1;
```

### Issue: Can't login

**Solution:** Default password is `password123` for all users

---

## 🎯 Recommended Workflow

**For Production Deployment:**
```
1. Import FRESH_START_WITH_DATA.sql
2. Verify API keys work
3. Change all default passwords
4. Update institution information
5. Add real users
6. Configure settings
7. Test thoroughly
8. Go live!
```

**For Development/Testing:**
```
1. Import FRESH_START_WITH_DATA.sql
2. Test immediately
3. Reset anytime by re-importing
```

---

## 📞 Support Resources

**Test Scripts:**
- `../verify-api-keys-from-db.php` - Quick check
- `../test-both-ai-providers.php` - Compare APIs
- `../test-ai-config.php` - Full diagnostics

**Documentation:**
- `../READY_TO_TEST.md` - Quick start
- `../AI_QUICK_START.md` - Setup guide
- `../AI_TESTING_README.md` - Testing guide

**Settings:**
- `/admin/settings.php` - Change provider/mode
- Can update API keys anytime

---

## ✨ Summary

**Main File:** `FRESH_START_WITH_DATA.sql`
**Purpose:** Complete database with pre-configured API keys
**Size:** ~500KB with full data
**Import Time:** ~2 minutes
**Setup Time After Import:** 0 minutes (already configured!)

**Status:** ✅ Ready to Import
**API Keys:** ✅ Pre-configured
**Data:** ✅ Complete with samples
**Testing:** ✅ Can start immediately

---

**Quick Import:**
```bash
mysql -u username -p database_name < FRESH_START_WITH_DATA.sql
```

**Then test:** `https://your-domain.com/verify-api-keys-from-db.php`

**THAT'S IT!** 🚀
