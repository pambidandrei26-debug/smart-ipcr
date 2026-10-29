# 🔧 OpenAI Troubleshooting Guide
## Quick Solutions to Common Problems

**Last Updated:** October 17, 2026

---

## 🚨 Problem 1: "API key invalid" or "Authentication failed"

### Symptoms:
- ❌ Error message: "API key invalid"
- ❌ "Authentication failed" 
- ❌ "Invalid API key provided"
- ❌ AI check fails immediately

### Possible Causes:
1. API key copied incorrectly (missing characters)
2. Extra spaces before/after the key
3. API key was deleted from OpenAI platform
4. Wrong API key format

### Solutions:

#### Solution 1A: Re-copy the API Key
```
1. Go to IPCR System → Admin → Settings
2. Delete the current API key from field
3. Go to your Notepad where you saved the key
4. Select ALL the key text (Ctrl+A)
5. Copy (Ctrl+C)
6. Back to IPCR Settings
7. Click in API key field
8. Paste (Ctrl+V)
9. DO NOT add any spaces
10. Click Save
```

#### Solution 1B: Verify Key Format
Your key should look like:
```
✅ CORRECT:
sk-proj-Abc12Def34Ghi56Jkl78Mno90Pqr12Stu34Vwx56Yz78

❌ WRONG (extra spaces):
 sk-proj-Abc12Def34Ghi56Jkl78Mno90Pqr12Stu34Vwx56Yz78 

❌ WRONG (incomplete):
sk-proj-Abc12Def34Ghi56Jkl78Mno90
```

#### Solution 1C: Create New API Key
```
1. Go to https://platform.openai.com/api-keys
2. Find your old key "IPCR Evidence Verification"
3. Click Delete (trash icon)
4. Click "+ Create new secret key"
5. Name: "IPCR Evidence Verification v2"
6. Click "Create"
7. COPY the new key immediately
8. Paste into IPCR Settings
9. Click Save
```

#### Solution 1D: Check Key on OpenAI Platform
```
1. Go to https://platform.openai.com/api-keys
2. Verify your key is listed and NOT deleted
3. If deleted, create new key (Solution 1C)
4. If listed, re-copy from your saved location
```

---

## 💰 Problem 2: "Insufficient credits" or "Billing error"

### Symptoms:
- ❌ "You exceeded your current quota"
- ❌ "Insufficient credits"
- ❌ "Billing issue detected"
- ❌ AI works once then stops

### Possible Causes:
1. No credits in OpenAI account
2. Reached monthly budget limit
3. Payment method declined
4. No payment method added

### Solutions:

#### Solution 2A: Add Credits
```
1. Go to https://platform.openai.com
2. Login to your account
3. Click Settings → Billing
4. Click "Add to credit balance"
5. Select $10 USD
6. Click "Continue"
7. Confirm payment
8. Wait 2-3 minutes for credits to appear
9. Check balance shows $10.00
10. Test AI in IPCR again
```

#### Solution 2B: Check Current Balance
```
1. Go to https://platform.openai.com
2. Click Settings → Billing
3. Look for "Credit balance: $X.XX"
4. If $0.00 → Add credits (Solution 2A)
5. If balance exists but still error → Check usage limits
```

#### Solution 2C: Increase Budget Limit
```
1. OpenAI Platform → Settings → Billing
2. Find "Usage limits"
3. Click "Edit"
4. Current limit might be too low
5. Increase to $20 or $50
6. Click "Save"
7. Test again
```

#### Solution 2D: Verify Payment Method
```
1. OpenAI Platform → Settings → Billing
2. Check "Payment methods" section
3. If card is expired or declined:
   - Click "Edit"
   - Update card details
   - Save changes
4. If no payment method:
   - Click "Add payment method"
   - Add valid card
   - Save
```

---

## 🐌 Problem 3: "Rate limit exceeded" 

### Symptoms:
- ❌ "Rate limit exceeded. Please try again"
- ❌ Works sometimes, fails sometimes
- ❌ "Too many requests"
- ❌ AI check times out

### Possible Causes:
1. Too many uploads in short time
2. Free tier rate limits (if no payment method)
3. Temporary OpenAI server load

### Solutions:

#### Solution 3A: Wait and Retry
```
1. Wait 60 seconds (1 minute)
2. Try uploading evidence again
3. Should work after waiting
```

#### Solution 3B: Verify Payment Method Added
```
Free tier = Very low limits (3 requests/minute)
Paid tier = Much higher limits (3500 requests/minute)

1. Go to OpenAI Platform → Billing
2. Ensure payment method is added
3. Add at least $5 credits
4. Your limits automatically increase to Tier 1
5. Test again in IPCR
```

#### Solution 3C: Spread Out Uploads
```
Instead of uploading 10 files at once:
1. Upload 2-3 files
2. Wait 30 seconds
3. Upload 2-3 more files
4. Wait 30 seconds
5. Continue pattern
```

#### Solution 3D: Check OpenAI Status
```
1. Go to https://status.openai.com
2. Check if "API" shows green (operational)
3. If yellow/red → Wait for OpenAI to fix
4. Usually resolves in 5-10 minutes
```

---

## 🤷 Problem 4: AI always says "Uncertain"

### Symptoms:
- ⚠️ Every file gets "Uncertain" verdict
- ⚠️ Confidence always 40-60%
- ⚠️ AI can't determine document type
- ⚠️ Suggestions say "Upload better file"

### Possible Causes:
1. Bad filenames (generic like "document.pdf")
2. Wrong file type (Word/Excel instead of PDF)
3. Scanned images (not searchable PDFs)
4. File doesn't match SI requirement

### Solutions:

#### Solution 4A: Use Better Filenames
```
❌ BAD FILENAMES:
- document.pdf
- file1.pdf
- 123.pdf
- unnamed.pdf
- scan001.pdf

✅ GOOD FILENAMES:
- Syllabus_BSIT_Programming1_2024.pdf
- TeachingLoad_FirstSem_2024-2025.pdf
- ClassSchedule_MWF_Spring2024.pdf
- LessonPlan_Mathematics_Week1.pdf
- Attendance_Sheet_October2024.pdf
```

**How to rename:**
```
1. Before uploading, right-click file
2. Choose "Rename"
3. Type descriptive name matching the SI
4. Include: document type + subject + date
5. Save and upload
```

#### Solution 4B: Convert to PDF
```
If using Word/Excel files:

1. Open file in Microsoft Word/Excel
2. Click "File" → "Save As"
3. Choose "PDF" from format dropdown
4. Save
5. Upload the PDF version to IPCR
6. AI can read PDF content better
```

#### Solution 4C: Match File to SI Requirement
```
Check what SI asks for:
1. Look at SI description
2. Check "Expected Evidence" field
3. Upload EXACTLY that type
4. Example:
   - SI says: "Submit course syllabus"
   - Upload: Actual syllabus (not teaching load)
```

#### Solution 4D: Use Searchable PDFs
```
If scanning documents:
1. Use scanner's OCR feature
2. Save as "Searchable PDF"
3. NOT as "Image PDF"
4. This lets AI read the text
5. Improves accuracy significantly
```

---

## 🔇 Problem 5: "AI check unavailable" or "AI verification failed"

### Symptoms:
- ⏳ "AI check unavailable. File uploaded without AI verification"
- ⏳ AI loading spinner never stops
- ⏳ Upload succeeds but no AI feedback
- ⏳ AI section shows "pending" forever

### Possible Causes:
1. API key not configured
2. Internet connection issue
3. OpenAI API is down
4. PHP timeout (rare)

### Solutions:

#### Solution 5A: Verify API Key is Saved
```
1. Login as Admin
2. Go to Admin → Settings
3. Scroll to AI Evidence Verification
4. Check:
   - ✅ OpenAI is selected (blue border)
   - ✅ API key field shows dots (•••••)
   - ✅ Mode is set (Advisory or Strict)
5. If any missing:
   - Re-enter API key
   - Select OpenAI
   - Choose mode
   - Click Save
```

#### Solution 5B: Test Internet Connection
```
1. Open new browser tab
2. Go to https://www.google.com
3. If loads → Internet OK
4. If doesn't load → Check connection:
   - Restart router
   - Check WiFi
   - Test on another device
```

#### Solution 5C: Check OpenAI Service Status
```
1. Go to https://status.openai.com
2. Look at "API" row
3. Green = Operational (good)
4. Yellow = Performance issues (wait)
5. Red = Outage (wait for fix)
6. If red/yellow:
   - Wait 10-15 minutes
   - Try again
   - Usually resolves quickly
```

#### Solution 5D: Clear Browser Cache
```
1. Press Ctrl+Shift+Delete
2. Select "Cached images and files"
3. Click "Clear data"
4. Close browser
5. Reopen IPCR system
6. Try upload again
```

#### Solution 5E: Restart XAMPP
```
1. Open XAMPP Control Panel
2. Stop Apache
3. Stop MySQL
4. Wait 5 seconds
5. Start MySQL
6. Start Apache
7. Refresh IPCR system
8. Try again
```

---

## 📱 Problem 6: Settings page doesn't show OpenAI option

### Symptoms:
- ❓ AI section missing entirely
- ❓ Only shows old providers
- ❓ OpenAI not listed
- ❓ Page looks outdated

### Possible Causes:
1. Browser cache (showing old version)
2. Wrong version of settings.php file
3. File update didn't apply

### Solutions:

#### Solution 6A: Hard Refresh Browser
```
1. On Settings page
2. Press Ctrl+F5 (Windows)
3. Or Cmd+Shift+R (Mac)
4. This forces fresh load
5. Check if OpenAI appears
```

#### Solution 6B: Clear Browser Cache
```
1. Press Ctrl+Shift+Delete
2. Select:
   - ✅ Browsing history
   - ✅ Cached images and files
   - ✅ Cookies
3. Time range: "All time"
4. Click "Clear data"
5. Close browser completely
6. Reopen and login
```

#### Solution 6C: Try Different Browser
```
If using Chrome:
1. Open Firefox or Edge
2. Go to http://localhost/ipcr-system
3. Login as Admin
4. Check Settings
5. If OpenAI shows → Cache issue
6. If still missing → File issue
```

#### Solution 6D: Verify File Exists
```
1. Open File Explorer
2. Go to: C:\xampp\htdocs\ipcr-system\admin
3. Find: settings.php
4. Check file date (should be recent)
5. If old → Re-copy updated version
```

---

## ⏰ Problem 7: AI takes too long (> 10 seconds)

### Symptoms:
- 🐌 Loading spinner for 10+ seconds
- 🐌 Eventually succeeds but very slow
- 🐌 "AI is checking..." for long time
- 🐌 Sometimes times out

### Possible Causes:
1. Large file size (> 5MB)
2. Slow internet connection
3. OpenAI API experiencing load
4. PDF has many pages

### Solutions:

#### Solution 7A: Compress Large PDFs
```
If file is > 5MB:

1. Go to: https://www.ilovepdf.com/compress_pdf
2. Upload your large PDF
3. Click "Compress PDF"
4. Download compressed version
5. Upload compressed file to IPCR
6. Should be faster now
```

#### Solution 7B: Check Internet Speed
```
1. Go to: https://fast.com
2. Wait for speed test
3. Should show: > 5 Mbps
4. If slower:
   - Close other programs
   - Stop downloads
   - Move closer to router
   - Test again
```

#### Solution 7C: Use Fewer Pages
```
If PDF has 50+ pages:

1. Extract only relevant pages
2. Example: Submit only syllabus section
3. Don't include full 100-page document
4. Smaller = faster AI check
```

#### Solution 7D: Wait and Retry
```
If OpenAI is busy:
1. Wait 5 minutes
2. Try uploading again
3. Peak hours (10am-2pm) may be slower
4. Off-peak (evening) is faster
```

---

## 🔄 Problem 8: Changes don't take effect

### Symptoms:
- 🔄 Save button clicked but nothing changes
- 🔄 Settings revert to old values
- 🔄 API key disappears after save
- 🔄 Mode selection doesn't stick

### Possible Causes:
1. Browser not submitting form
2. Database not updating
3. Session issue
4. Permission problem

### Solutions:

#### Solution 8A: Verify Save Confirmation
```
After clicking Save:
1. Look for green message at top
2. Should say: "✅ AI settings saved..."
3. If no message → Form didn't submit
4. Try clicking Save again
5. Wait for confirmation
```

#### Solution 8B: Check MySQL Running
```
1. Open XAMPP Control Panel
2. Check MySQL shows "Running" (green)
3. If not running:
   - Click "Start" for MySQL
   - Wait until green
   - Try saving again
```

#### Solution 8C: Logout and Login
```
1. Click your username (top right)
2. Click "Logout"
3. Login again as Admin
4. Go to Settings
5. Enter API key again
6. Save
7. Refresh page (F5)
8. Check if settings persisted
```

#### Solution 8D: Check Database Directly
```
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Select database: ipcr_system
3. Find table: system_config
4. Look for row: openai_api_key
5. If missing → database issue
6. If exists → check config_value column
```

---

## 🎓 Best Practices to Avoid Problems

### For Administrators:

```
✅ DO:
- Set $10-20 budget limit on OpenAI
- Enable email alerts (75% and 100%)
- Check usage weekly
- Keep API key in secure location
- Monitor error logs
- Start with Advisory mode
- Switch to Strict after 2 weeks testing

❌ DON'T:
- Share API key publicly
- Set no budget limit
- Ignore email alerts
- Delete payment method
- Skip testing
- Start with Strict mode immediately
```

### For Faculty:

```
✅ DO:
- Use descriptive filenames
- Match file type to SI requirement
- Upload PDF when possible
- Check AI feedback
- Learn from suggestions
- Rename files before upload

❌ DON'T:
- Use generic names (document.pdf)
- Upload wrong document types
- Ignore AI warnings
- Upload huge files (> 10MB)
- Skip reading SI requirements
- Upload non-searchable scans
```

---

## 📊 How to Monitor OpenAI Usage

### Daily Monitoring:

```
1. Go to https://platform.openai.com/usage
2. Check today's usage
3. Look at:
   - Number of requests
   - Cost so far
   - Credit balance remaining
4. Should see pattern:
   - 10-50 requests per day (normal)
   - $0.10-0.50 cost per day (normal)
   - If higher → investigate
```

### Weekly Review:

```
Every Friday:
1. Check total weekly requests
2. Check total weekly cost
3. Estimate monthly projection
4. Expected ranges:
   - 100-500 requests/week = Normal
   - $1-5 per week = Normal
   - Monthly: $5-20 = Normal
5. If exceeding budget:
   - Review usage patterns
   - Check for abuse
   - Adjust budget limit
```

---

## 🆘 Still Having Problems?

### Diagnostic Steps:

```
1. ✅ Verify XAMPP is running (Apache + MySQL green)
2. ✅ Confirm internet connection working
3. ✅ Check OpenAI status: https://status.openai.com
4. ✅ Verify API key is correct (re-enter if needed)
5. ✅ Ensure credits > $0 in OpenAI account
6. ✅ Test with simple PDF file first
7. ✅ Use descriptive filename
8. ✅ Check browser console for errors (F12)
9. ✅ Try different browser
10. ✅ Restart XAMPP
```

### Get More Help:

```
📧 Contact Information:
- System Administrator
- IT Support
- OpenAI Support: https://help.openai.com

📝 When Reporting Issues:
Include:
- Exact error message
- Screenshot of problem
- What you were trying to do
- File type/name being uploaded
- When problem started
- Steps already tried
```

---

## 📋 Quick Reference

### Common Error Messages:

| Error | Cause | Solution |
|-------|-------|----------|
| "API key invalid" | Wrong key | Re-copy from Notepad |
| "Insufficient credits" | $0 balance | Add $10 credits |
| "Rate limit exceeded" | Too fast | Wait 60 seconds |
| "AI unavailable" | No key set | Enter API key in Settings |
| "Authentication failed" | Deleted key | Create new key |
| "Service unavailable" | OpenAI down | Check status.openai.com |
| "Timeout error" | Large file | Compress PDF |

### Essential URLs:

```
OpenAI Platform: https://platform.openai.com
API Keys: https://platform.openai.com/api-keys
Billing: https://platform.openai.com/settings/organization/billing
Usage: https://platform.openai.com/usage
Status: https://status.openai.com
Help: https://help.openai.com

IPCR Settings: http://localhost/ipcr-system/admin/settings.php
phpMyAdmin: http://localhost/phpmyadmin
```

---

**Last Updated:** October 17, 2026  
**Version:** 1.0  
**For:** IPCR System with OpenAI Integration

**Most problems are solved by:**
1. Re-entering API key correctly
2. Adding credits to OpenAI account
3. Using better filenames
4. Converting to PDF format

✅ **95% of issues fixed with these 4 steps!**
