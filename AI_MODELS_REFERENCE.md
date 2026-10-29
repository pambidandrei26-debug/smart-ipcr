# AI Models Reference - OpenRouter & Gemini

**Last Updated:** October 2026  
**Purpose:** Quick reference for available AI models when debugging or changing providers

---

## 🎯 RECOMMENDED MODELS (October 2026)

### ✅ BEST FOR THIS SYSTEM: OpenRouter FREE Models

| Model Name | Status | Cost | Speed | Use Case |
|------------|--------|------|-------|----------|
| `google/gemini-2.0-flash-exp:free` | ✅ **ACTIVE** | FREE | Fast | **Current/Best** |
| `google/gemini-3.1-pro-preview:free` | ✅ Working | FREE | Medium | Advanced tasks |
| `openrouter/free` | ✅ Working | FREE | Varies | Auto-selects free model |

---

## 📋 OpenRouter - All Available FREE Models

### Google Gemini Models (via OpenRouter)

```
✅ WORKING FREE MODELS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. google/gemini-2.0-flash-exp:free ⭐ RECOMMENDED
   - Released: December 11, 2024
   - Context: 1M tokens
   - Speed: FAST
   - Use: Perfect for evidence verification
   - Status: FREE (200 requests/day)

2. google/gemini-3.1-pro-preview:free
   - Released: 2025
   - Context: 2M tokens
   - Speed: Medium
   - Use: Advanced reasoning tasks
   - Status: FREE

3. google/gemini-exp-1121:free
   - Released: November 21, 2024
   - Context: 1M tokens
   - Speed: Fast
   - Use: Experimental features
   - Status: FREE

4. google/gemini-exp-1114:free
   - Released: November 15, 2024
   - Context: 1M tokens
   - Speed: Fast
   - Use: Older experimental version
   - Status: FREE
```

### Other Free Models on OpenRouter

```
✅ ALTERNATIVE FREE OPTIONS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

- openrouter/free (Auto-selects best free model)
- meta-llama/llama-3.2-11b-vision-instruct:free
- qwen/qwen-2.5-7b-instruct:free
- microsoft/phi-3-mini-128k-instruct:free
- mistralai/mistral-7b-instruct:free
```

---

## 📋 Google Gemini API - Direct Access Models

### ⚠️ WARNING: Gemini Direct API Requires Billing

While these models exist, **Gemini API free tier has very limited access** to the `generateContent` endpoint. You'll likely encounter "API key not valid" or quota errors without billing enabled.

```
✅ GEMINI DIRECT API MODELS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. gemini-1.5-flash ⭐ MOST COMMON
   - Endpoint: generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash
   - Speed: Fast
   - Context: 1M tokens
   - Cost: Free tier limited, requires billing for production

2. gemini-1.5-flash-8b
   - Endpoint: generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-8b
   - Speed: Very Fast
   - Context: 1M tokens
   - Cost: Cheaper than 1.5-flash

3. gemini-1.5-pro
   - Endpoint: generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro
   - Speed: Medium
   - Context: 2M tokens
   - Cost: More expensive, better quality

4. gemini-2.0-flash-exp (Experimental)
   - Endpoint: generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp
   - Speed: Fast
   - Context: 1M tokens
   - Cost: Free during experimental phase
```

---

## ❌ COMMON ERRORS & SOLUTIONS

### Error 1: "No endpoints found for google/gemini-flash-1.5-8b"

```
❌ WRONG MODEL NAMES (DO NOT USE):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

- google/gemini-flash-1.5-8b ❌ (Wrong order!)
- google/gemini-1.5-flash-8b ❌ (Missing :free suffix)
- gemini-flash-1.5 ❌ (Incomplete name)
- gemini-2.0-flash ❌ (Missing -exp and :free)

✅ CORRECT MODEL NAMES:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

- google/gemini-2.0-flash-exp:free ✅
- google/gemini-3.1-pro-preview:free ✅
- openrouter/free ✅
```

**Solution:** Use exact model names with `:free` suffix for OpenRouter free models

---

### Error 2: "API key not valid" (Gemini Direct API)

```
Problem: Google Gemini API free tier is very limited
         - Free tier may not have access to generateContent
         - Billing must be enabled for full access
         - Even with valid API key, you'll hit quota fast

Solution: Switch to OpenRouter (100% FREE, no billing)
         1. Go to https://openrouter.ai/keys
         2. Sign in with Google
         3. Create API key (starts with sk-or-v1-)
         4. Use in Admin Settings
```

---

### Error 3: HTTP 429 - Rate Limit Exceeded

```
Problem: Too many requests in short time
         - Gemini: Very limited on free tier
         - OpenRouter: 200 requests/day (generous)

Solution: 
         1. Wait a few minutes before retrying
         2. Use OpenRouter instead (higher limits)
         3. In strict mode, temporarily switch to advisory
```

---

## 🔧 HOW TO CHANGE MODEL IN CODE

### Option 1: Change in Database (RECOMMENDED)

```sql
-- For OpenRouter users (change model if needed)
UPDATE system_config 
SET config_value = 'google/gemini-2.0-flash-exp:free'
WHERE config_key = 'openrouter_model';

-- Switch provider to OpenRouter
UPDATE system_config 
SET config_value = 'openrouter'
WHERE config_key = 'ai_provider';
```

### Option 2: Change in Code Files

**Files that contain AI model names:**

1. `api/upload-evidence.php` (Line ~250)
2. `api/ai-verify-evidence.php` (Line ~180)
3. `api/pre-upload-ai-check.php` (Line ~140)
4. `test-ai-simple.php` (Line ~110)
5. `test-both-ai-providers.php` (Line ~182)
6. `test-ai-config.php` (Line ~150)

**Search for:**
```php
'model' => 'google/gemini-2.0-flash-exp:free'
```

**Replace with new model:**
```php
'model' => 'google/gemini-3.1-pro-preview:free'  // Or any other free model
```

---

## 📊 PROVIDER COMPARISON

| Feature | Google Gemini Direct | OpenRouter |
|---------|---------------------|------------|
| **Free Tier** | Limited access | 200 requests/day |
| **Billing Required** | ⚠️ YES for production | ❌ NO |
| **Setup Time** | 5 minutes | 2 minutes |
| **API Key Format** | `AIza...` | `sk-or-v1-...` |
| **Model Selection** | Limited | 80+ models |
| **Reliability** | Quota issues | Very stable |
| **Speed** | Fast | Fast |
| **Best For** | Testing only | Production use |

---

## 🎓 TESTING CHECKLIST

Before deploying any AI model change:

```
✅ TESTING STEPS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. ☐ Update model name in all 6 files
2. ☐ Upload files to server via FileZilla
3. ☐ Login as Admin
4. ☐ Go to Admin → Settings
5. ☐ Verify API key is configured
6. ☐ Click "Run Simple Test" button
7. ☐ Check for HTTP 200 response
8. ☐ Verify confidence score shows (50-100%)
9. ☐ Click "Compare Providers" button
10. ☐ Confirm no "endpoints not found" error
11. ☐ Test actual evidence upload
12. ☐ Check AI verdict appears correctly
```

---

## 🔍 DEBUGGING TIPS

### Check Current Configuration

```sql
-- See what's currently configured
SELECT config_key, config_value 
FROM system_config 
WHERE config_key IN (
    'ai_provider',
    'gemini_api_key',
    'openrouter_api_key',
    'ai_verification_mode'
);
```

### Check PHP Error Logs

```bash
# On hosting, check error logs
tail -f ~/logs/error_log

# Look for:
"AI CHECK STARTING"
"OpenRouter Response: SUCCESS"
"AI Verdict: valid | Confidence: 85%"
```

### Manual API Test (cURL)

```bash
# Test OpenRouter directly
curl -X POST "https://openrouter.ai/api/v1/chat/completions" \
  -H "Authorization: Bearer YOUR_OPENROUTER_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "model": "google/gemini-2.0-flash-exp:free",
    "messages": [{"role": "user", "content": "Hello"}]
  }'
```

---

## 📚 USEFUL LINKS

### OpenRouter
- Dashboard: https://openrouter.ai/
- API Keys: https://openrouter.ai/keys
- Models List: https://openrouter.ai/models
- Free Models: https://openrouter.ai/collections/free-models
- Docs: https://openrouter.ai/docs

### Google Gemini
- AI Studio: https://aistudio.google.com/
- API Keys: https://aistudio.google.com/app/apikey
- Docs: https://ai.google.dev/docs
- Pricing: https://ai.google.dev/pricing

---

## 💡 RECOMMENDATIONS FOR YOUR SYSTEM

### Current Setup (October 2026)
```
✅ BEST CONFIGURATION:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Provider: OpenRouter
Model: google/gemini-2.0-flash-exp:free
API Key: sk-or-v1-... (from openrouter.ai)
Mode: advisory (recommended)

Why?
- 100% FREE (no billing required)
- 200 requests/day (enough for testing)
- Reliable and fast
- Same AI quality as Gemini direct
- No quota issues
```

### Alternative if OpenRouter has issues
```
⚠️ BACKUP CONFIGURATION:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Provider: Google Gemini
Model: gemini-1.5-flash
API Key: AIza... (from aistudio.google.com)
Mode: advisory

Note: Requires billing enabled in Google Cloud Console
```

---

## 🚀 QUICK FIX GUIDE

If you get "No endpoints found" error:

```bash
1. Check model name has :free suffix
   ✅ google/gemini-2.0-flash-exp:free
   ❌ google/gemini-2.0-flash-exp

2. Update all 6 files with correct model name

3. Upload to server

4. Test with "Run Simple Test" button

5. If still failing, try alternative model:
   google/gemini-3.1-pro-preview:free
```

---

**Summary:** Stick with OpenRouter + `google/gemini-2.0-flash-exp:free` for best results! 🎯
