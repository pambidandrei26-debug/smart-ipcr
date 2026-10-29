# 🚀 AI Setup - Quick Start (5 Minutes)

## TL;DR - Fastest Way to Setup

### 1️⃣ Add Your API Keys
Go to Admin Settings:
```
http://localhost/smart-ipcr/admin/settings.php
```
Login as admin, then:
- Scroll to **"AI Evidence Verification"** section
- Select your preferred provider (OpenRouter or Gemini)
- Enter your API key
- Set mode to "Advisory Only"
- Click **Save AI Settings**

### 2️⃣ Test Both Providers
Compare Gemini vs OpenRouter:
```
http://localhost/smart-ipcr/test-both-ai-providers.php
```

This will:
- Test both API keys from database
- Show side-by-side comparison
- Display response times and results
- Recommend which provider to use

### 3️⃣ Verify Configuration
```
http://localhost/smart-ipcr/test-ai-config.php
```

Look for:
```
✅ Database connected
✅ API key configured
✅ API is responding correctly!
```

### 4️⃣ Try Live Upload
- Login as faculty
- Go to IPCR Form
- Upload a PDF file
- Watch AI verify it in real-time

---

## Getting API Keys

### OpenRouter (100% FREE - Recommended)
1. Go to: https://openrouter.ai/keys
2. Sign in with Google (free)
3. Click "Create Key"
4. Copy the key (starts with `sk-or-v1-`)
5. Paste in Admin > Settings > OpenRouter API Key

**Benefits:**
- 🆓 100% FREE
- ⚡ 200 requests/day
- 🤖 Uses Gemini Flash 1.5
- 💳 No billing required

### Google Gemini
1. Go to: https://aistudio.google.com/app/apikey
2. Create API key
3. Copy the key (starts with `AIza`)
4. Paste in Admin > Settings > Gemini API Key

**Note:**
- ⚠️ Free tier has limited access
- 💳 May require billing setup
- 💰 Very cheap per request

---

## Testing Tools

### Test Both Providers Side-by-Side
```
http://localhost/smart-ipcr/test-both-ai-providers.php
```
Shows:
- Which providers are configured
- API response times
- AI verdict comparison
- Recommendation on which to use

### Test Individual Provider
**OpenRouter only:**
```
http://localhost/smart-ipcr/test-openrouter.php
```

**Full system check:**
```
http://localhost/smart-ipcr/test-ai-config.php
```

---

## Quick Troubleshooting

| Problem | Solution |
|---------|----------|
| API not working | Run `test-both-ai-providers.php` |
| No AI message on upload | Check if mode is "Disabled" |
| HTTP 401 error | API key invalid, check in settings |
| Slow response | Wait 10-15 seconds, normal |
| Both APIs failing | Check internet connection |

---

## What Each File Does

| File | Purpose |
|------|---------|
| `admin/settings.php` | Configure AI providers |
| `test-both-ai-providers.php` | Compare both providers |
| `test-openrouter.php` | Test OpenRouter only |
| `test-ai-config.php` | Full system diagnostics |
| `setup-api-keys.php` | Quick config form |

---## Recommended Settings

**For OpenRouter:**
```
Provider: OpenRouter
Mode: Advisory Only
Key: [Your key from openrouter.ai/keys]
```

**For Gemini:**
```
Provider: Google Gemini
Mode: Advisory Only
Key: [Your key from aistudio.google.com]
```

**Why Advisory Mode?**
- Shows AI feedback to users
- Never blocks valid uploads
- Deans still review everything
- Safe for production

---

## Success Checklist

- [ ] Got API key from OpenRouter or Gemini
- [ ] Added key in Admin > Settings
- [ ] Selected provider and mode
- [ ] Tested with `test-both-ai-providers.php`
- [ ] Verified with `test-ai-config.php`
- [ ] Uploaded test file as faculty
- [ ] Saw AI verification message
- [ ] ✅ **DONE! System is working!**

---

**Time to complete:** 5-10 minutes ⏱️
**Difficulty:** Easy ⭐
**Cost:** FREE (OpenRouter) or Very Cheap (Gemini) 💰
**Status:** Ready to test! 🚀
