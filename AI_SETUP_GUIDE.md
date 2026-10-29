# 🤖 AI Verification Setup & Testing Guide

## Quick Summary
This guide will help you:
1. ✅ Set up OpenRouter API (100% FREE - RECOMMENDED)
2. ✅ Test if the API is working
3. ✅ Configure AI verification in your IPCR system
4. ✅ Test live evidence upload validation

---

## Step 1: Test OpenRouter API First

### 1.1 Direct API Test
Open your browser and go to:
```
http://localhost/smart-ipcr/test-openrouter.php
```

**What this does:**
- Tests your OpenRouter API key directly
- Shows if the API is responding correctly
- Simulates an evidence verification request
- Displays response time and results

**Expected Result:**
```
✅ API Call Successful!
✅ Successfully parsed AI verification result!
Verdict: VALID
Confidence: 85%
✅ UPLOAD WOULD BE ALLOWED (confidence >= 50%)
```

**If you see errors:**
- ❌ HTTP 401/403 = Invalid API key
- ❌ Connection Error = No internet or firewall blocking
- ❌ HTTP 429 = Rate limit exceeded (wait a few minutes)

---

## Step 2: Configure in Admin Settings

### 2.1 Login to Admin Panel
1. Go to: `http://localhost/smart-ipcr/dashboard.php`
2. Login with admin account
3. Click **Admin > Settings** in the sidebar

### 2.2 Configure AI Settings
Scroll down to **"AI Evidence Verification"** section:

1. **Select AI Provider:**
   - Choose: `OpenRouter` (100% FREE, 200 requests/day)
   - Alternative: `Gemini` (requires billing setup)

2. **Enter API Key:**
   ```
   your-openrouter-api-key-here
   ```
   (Get your free API key from https://openrouter.ai/keys)

3. **Select Verification Mode:**
   - ✅ **Advisory Only (RECOMMENDED)** - Shows AI feedback but never blocks uploads
   - ⚠️ **Strict Mode** - Blocks uploads below 50% confidence (may reject valid files)
   - **Disabled** - No AI checks at all

4. **Click "Save AI Settings"**

**Expected Result:**
```
✅ AI settings saved. Provider: OPENROUTER, Mode: advisory.
🟢 OPENROUTER Key Configured
```

---

## Step 3: Verify Configuration

### 3.1 Check System Configuration
Open your browser and go to:
```
http://localhost/smart-ipcr/test-ai-config.php
```

**What this checks:**
- ✅ Database connection
- ✅ AI settings are saved correctly
- ✅ API key is configured
- ✅ API is responding
- ✅ Shows current setup status

**Expected Output:**
```
✅ Database connected successfully
✅ system_config table exists

Current Configuration:
────────────────────────────────────────────
AI Provider: OPENROUTER
OpenRouter API Key: SET (sk-or-v1-...********)
AI Verification Mode: advisory

=== Testing API Connection ===
Response Time: 1234ms
HTTP Status: 200
✅ API is responding correctly!
✅ AI verification is ready to use
```

---

## Step 4: Test Live Evidence Upload

### 4.1 Test as Faculty User

1. **Login as Faculty:**
   - Go to dashboard
   - Navigate to: **Faculty > IPCR Form**

2. **Select a Success Indicator:**
   - Click on any SI that requires evidence upload
   - Example: "Submit Teaching Load Form"

3. **Upload a Test File:**
   - Click "Upload Evidence"
   - Select a PDF file (best for testing)
   - Upload it

4. **Watch for AI Verification:**
   
   **During Upload (Pre-Check):**
   ```
   🤖 AI is analyzing your file...
   ⏳ Please wait (this takes 5-10 seconds)
   ```
   
   **After Upload (Advisory Mode):**
   ```
   ✅ File uploaded successfully!
   
   🤖 AI Verification Result:
   Verdict: VALID
   Confidence: 85%
   Document Type: Teaching Load Form
   
   Reason: The filename "Teaching_Load_2024.pdf" clearly matches 
   the expected evidence type "Teaching Load Form". The document 
   appears to be the correct type of evidence for this indicator.
   ```
   
   **If AI finds issues (Advisory Mode):**
   ```
   ⚠️ File uploaded (AI has concerns)
   
   🤖 AI Verification Result:
   Verdict: UNCERTAIN
   Confidence: 45%
   Document Type: Unknown Document
   
   Reason: The filename "random_file.pdf" does not clearly match 
   the expected evidence type. Please verify this is the correct 
   document.
   
   Suggestions: Please upload a document that clearly relates to 
   the Teaching Load Form requirement.
   
   ℹ️ Note: In advisory mode, files are allowed even with low 
   confidence. Dean/Admin should review this evidence manually.
   ```

### 4.2 Test Different Scenarios

**Test 1 - Perfect Match:**
- File: `Teaching_Load_Form_2024.pdf`
- Expected: ✅ VALID (80-90% confidence)

**Test 2 - Partial Match:**
- File: `Schedule_Document.pdf` (for "Class Schedule" requirement)
- Expected: ✅ VALID (60-75% confidence)

**Test 3 - Unclear File:**
- File: `document.pdf` (generic name)
- Expected: ⚠️ UNCERTAIN (40-60% confidence)

**Test 4 - Wrong File:**
- File: `Syllabus.pdf` (for "Teaching Load" requirement)
- Expected: ❌ INVALID (below 50% confidence)

---

## Step 5: Understanding AI Modes

### Advisory Mode (RECOMMENDED)
```
✅ Shows AI confidence scores
✅ Displays warnings and suggestions
✅ NEVER blocks uploads
✅ Faculty sees feedback
✅ Dean/Admin review remains important
```

**Use when:**
- You want AI assistance but not enforcement
- You trust faculty to upload correct files
- You want deans to make final decisions
- You're testing the AI system

### Strict Mode
```
⚠️ Shows AI confidence scores
⚠️ BLOCKS uploads below 50% confidence
⚠️ May reject valid files with unclear names
⚠️ Faculty must rename/reupload
```

**Use when:**
- You need strong validation
- You have clear evidence naming conventions
- You want to reduce dean review workload
- You've tested thoroughly in advisory mode first

### Disabled Mode
```
No AI checks at all
All files allowed
Manual review only
```

---

## Troubleshooting

### Issue: API Key Not Working
**Symptoms:**
```
❌ Authentication Error (HTTP 401)
❌ API key may be invalid or expired
```

**Solution:**
1. Go to https://openrouter.ai/keys
2. Check if your key is still active
3. Generate a new key if needed
4. Update in Admin > Settings

---

### Issue: Slow Response
**Symptoms:**
```
⏳ AI verification taking more than 15 seconds
```

**Possible causes:**
- Slow internet connection
- Large PDF files (>5MB)
- OpenRouter server load

**Solution:**
- Use smaller files when possible
- Wait patiently (API is processing)
- Check your internet speed

---

### Issue: AI Not Running
**Symptoms:**
- No AI verification message appears
- Files upload without any AI check

**Check:**
1. Go to `test-ai-config.php`
2. Verify AI mode is NOT "disabled"
3. Verify API key is configured
4. Check browser console for errors (F12)

---

### Issue: Wrong API Provider
**Symptoms:**
```
⚠️ OpenRouter key stored but Gemini selected
```

**Solution:**
1. Go to Admin > Settings
2. Check the selected provider
3. Make sure it matches the API key you entered
4. Save settings again

---

## API Usage Limits

### OpenRouter (FREE Tier)
- **Limit:** 200 requests per day
- **Resets:** Daily
- **Cost:** $0 (completely free)
- **Model:** google/gemini-flash-1.5

**Usage estimate:**
- 50 faculty × 4 uploads each = 200 requests
- Enough for typical daily usage
- If you exceed: Wait 24 hours or upgrade to paid

### Google Gemini
- **Free tier:** Limited access to generateContent
- **Requires:** Billing enabled
- **Cost:** Very cheap (cents per request)
- **Model:** gemini-1.5-flash

---

## Best Practices

### 1. Start with Advisory Mode
- Test for 1-2 weeks
- Monitor false positives/negatives
- Train faculty on proper file naming
- Switch to strict mode if confident

### 2. Train Faculty on File Naming
**Good filenames:**
- ✅ `Teaching_Load_Form_2024.pdf`
- ✅ `Syllabus_Subject_Semester.pdf`
- ✅ `Class_Schedule_Approved.pdf`

**Bad filenames:**
- ❌ `document.pdf`
- ❌ `file123.pdf`
- ❌ `untitled.pdf`

### 3. Monitor AI Performance
- Check if AI verdicts match dean reviews
- Adjust mode based on accuracy
- Report issues if AI is consistently wrong

### 4. Educate Deans
- AI is a HELPER, not a replacement
- Always review evidence manually
- Use AI confidence as a guide
- Override AI when necessary

---

## Support & Next Steps

### If everything works:
✅ AI is configured and running
✅ Start using it in production
✅ Monitor performance
✅ Train users on best practices

### If you need help:
1. Check the test scripts first
2. Review error messages carefully
3. Check API status pages
4. Contact OpenRouter support if needed

### Additional Resources:
- OpenRouter Dashboard: https://openrouter.ai/dashboard
- OpenRouter API Docs: https://openrouter.ai/docs
- Gemini API Docs: https://ai.google.dev/docs

---

## Summary Checklist

- [ ] Tested API with `test-openrouter.php`
- [ ] Configured API key in Admin > Settings
- [ ] Selected OpenRouter as provider
- [ ] Set mode to "Advisory Only"
- [ ] Verified config with `test-ai-config.php`
- [ ] Tested live upload as faculty user
- [ ] Saw AI verification results
- [ ] Trained faculty on file naming
- [ ] Monitoring AI performance

**Status:** Ready for production! 🚀

