# 🤖 OpenAI Setup Guide for IPCR System
## Complete Step-by-Step Instructions

**Last Updated:** October 17, 2026  
**Estimated Time:** 10-15 minutes  
**Cost:** ₱30-120 per month (approximately $0.50-2.00 USD)

---

## 📋 What You'll Need

- ✅ Gmail account or Email address
- ✅ Valid credit/debit card (for OpenAI billing)
- ✅ Internet connection
- ✅ Your IPCR system already installed and running

**IMPORTANT:** OpenAI requires a payment method even though initial credits may be provided.

---

## 🎯 PART 1: Create OpenAI Account

### Step 1.1: Go to OpenAI Website

1. Open your web browser (Chrome, Firefox, Edge)
2. Go to: **https://platform.openai.com**
3. You'll see the OpenAI Platform homepage

### Step 1.2: Sign Up or Log In

**If you DON'T have an account yet:**

1. Click the **"Sign Up"** button (top right corner)
2. Choose one of these options:
   - **Continue with Google** (recommended - fastest)
   - **Continue with Microsoft**
   - Or use **Email address**

3. If using Google:
   - Click "Continue with Google"
   - Select your Gmail account
   - Allow access when prompted

4. If using Email:
   - Enter your email address
   - Check your email for verification code
   - Enter the 6-digit code
   - Create a password

**If you ALREADY have an account:**

1. Click **"Log in"**
2. Enter your credentials
3. Continue to next section

### Step 1.3: Complete Profile (New Accounts Only)

1. Enter your **Name**
2. Enter your **Organization name** (can be "Personal" or your institution name)
3. Click **"Continue"**

---

## 💳 PART 2: Set Up Billing

**IMPORTANT:** This is required even for testing. OpenAI doesn't have a completely free tier anymore.

### Step 2.1: Navigate to Billing

1. After logging in, look at the left sidebar
2. Click on **"Settings"** (gear icon at bottom left)
3. Click on **"Billing"** in the settings menu

### Step 2.2: Add Payment Method

1. You'll see "Payment methods" section
2. Click **"Add payment method"** button
3. Choose payment type:
   - **Credit/Debit Card** (most common)
   - Or **PayPal** (if available in your region)

### Step 2.3: Enter Card Details

Fill in your card information:

```
Card Number: [Enter your 16-digit card number]
Expiration Date: [MM/YY]
CVC: [3-digit security code on back]
Cardholder Name: [Name on card]
Billing Address: [Your address]
```

4. Click **"Add payment method"**
5. You should see: ✅ "Payment method added successfully"

### Step 2.4: Set Up Budget Limit (RECOMMENDED)

**This prevents unexpected charges!**

1. Still in Billing section
2. Look for **"Usage limits"** or **"Soft limit"**
3. Click **"Edit"**
4. Set a monthly limit:
   ```
   Recommended for testing: $5 USD (₱300)
   For production use: $10-20 USD (₱600-1,200)
   ```
5. Check the box: **"Send email alerts at 75% and 100%"**
6. Click **"Save"**

### Step 2.5: Add Initial Credits (Optional but Recommended)

1. Still in Billing section
2. Look for **"Add to credit balance"** button
3. Click it
4. Choose amount:
   ```
   Minimum: $5 USD
   Recommended: $10 USD (good for 1-2 months)
   ```
5. Click **"Continue"**
6. Confirm payment
7. You should see your credit balance updated

**Expected result:** You now have $5-10 credits in your account

---

## 🔑 PART 3: Create API Key

### Step 3.1: Navigate to API Keys

1. Look at the left sidebar
2. Click on **"API keys"** 
   - Or go directly to: **https://platform.openai.com/api-keys**

### Step 3.2: Create New API Key

1. Click the **"+ Create new secret key"** button (green button)
2. A dialog will appear

### Step 3.3: Name Your Key

1. In the "Name" field, enter a descriptive name:
   ```
   Example: IPCR Evidence Verification
   ```
2. This helps you identify the key later

### Step 3.4: Set Permissions (Optional)

1. If you see "Permissions" dropdown:
   - Select **"All"** (simplest)
   - Or **"Write"** (if available)
2. If you don't see this, skip - default is fine

### Step 3.5: Copy Your API Key

**⚠️ CRITICAL STEP - READ CAREFULLY:**

1. Click **"Create secret key"**
2. A new window shows your API key
3. **The key looks like this:**
   ```
   sk-proj-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   ```
4. Click the **"Copy"** button (or copy icon)
5. **IMMEDIATELY paste it somewhere safe:**
   - Notepad
   - Text file
   - Secure password manager

**⚠️ WARNING:** 
- You can ONLY see this key ONCE
- If you close without copying, you must create a new key
- Never share this key publicly

### Step 3.6: Verify Key Format

Your key should:
- ✅ Start with `sk-proj-` or `sk-`
- ✅ Be about 50-60 characters long
- ✅ Contain random letters and numbers

**Example format:**
```
sk-proj-Ab1Cd2Ef3Gh4Ij5Kl6Mn7Op8Qr9St0Uv1Wx2Yz3
```

---

## 🖥️ PART 4: Add API Key to Your IPCR System

### Step 4.1: Open Your IPCR System

1. Make sure XAMPP is running:
   - Open **XAMPP Control Panel**
   - Ensure **Apache** and **MySQL** are running (green)

2. Open your browser
3. Go to: **http://localhost/ipcr-system**

### Step 4.2: Login as Admin

1. Username: `ADMIN001`
2. Password: `Admin@2024`
3. Click **"Login"**

### Step 4.3: Navigate to Settings

1. In the top navigation bar, click **"Admin"**
2. From the dropdown, click **"Settings"**
3. You'll see the System Settings page

### Step 4.4: Scroll to AI Section

1. Scroll down to the **"AI Evidence Verification"** section
2. You'll see a purple/violet colored card
3. This section has different AI provider options

### Step 4.5: Select OpenAI Provider

1. You'll see several provider options (boxes):
   - **OpenAI** (green "WORKING" badge)
   - OpenRouter (red "FREE MODELS GONE")
   - Groq (red "DISCONTINUED")
   - Gemini (yellow "REQUIRES BILLING")
   - Hugging Face (red "ACCESS ISSUES")

2. Click on the **OpenAI** box
   - It has a green badge saying "💰 PAID - CHEAPEST"
   - And a "WORKING" badge

3. The box should get highlighted with a blue border

### Step 4.6: Paste Your API Key

1. After selecting OpenAI, a text field appears below labeled:
   ```
   🤖 OpenAI API Key
   ```

2. Click in the input field (it shows placeholder: `sk-proj-...`)

3. **Paste your API key** that you copied earlier
   - Right-click → Paste
   - Or press `Ctrl + V`

4. Verify the key is complete:
   - Should start with `sk-proj-` or `sk-`
   - Should be around 50-60 characters
   - No extra spaces at start or end

### Step 4.7: Set AI Verification Mode

**Below the API key field, you'll see "AI Verification Mode":**

Choose one of these options:

**🟢 ADVISORY MODE (Recommended for Testing):**
```
✓ AI checks files but doesn't block uploads
✓ Shows AI feedback to users
✓ Users can still upload even if AI says "uncertain"
✓ Good for testing and training period
```
**Select this if:** You're just starting or want flexibility

**🔴 STRICT MODE (For Production):**
```
✓ AI blocks uploads if confidence < 50%
✓ Forces users to upload correct evidence
✓ Stronger validation
✓ Better data quality
```
**Select this if:** You want strict enforcement

**⚫ DISABLED:**
```
✗ AI verification turned off
✗ All uploads allowed without checking
```
**Select this if:** You don't want AI (not recommended)

**Recommendation:** Start with **ADVISORY**, switch to **STRICT** later

### Step 4.8: Save Settings

1. After entering API key and selecting mode
2. Scroll down to find the **"Save"** button (blue button)
3. Click **"Save"**
4. Wait for confirmation message:
   ```
   ✅ AI settings saved. Provider: OPENAI, Mode: advisory.
   ```

**If you see this message: SUCCESS! ✅**

---

## ✅ PART 5: Test Your OpenAI Integration

### Step 5.1: Simple Test - Check API Key Format

1. Make sure the key was saved:
   - Refresh the page (F5)
   - The OpenAI option should still be selected
   - The key field should show dots (••••••) or masked characters

### Step 5.2: Advanced Test - Upload Evidence

**Test with a real file upload:**

1. **Login as a Faculty user:**
   - Logout from Admin
   - Username: `ICT-001`
   - Password: `Faculty@2024`

2. **Go to IPCR Form:**
   - Click "IPCR Form" in navigation
   - If you don't have a form, create one first
   - Find any Success Indicator (SI)

3. **Upload a Test File:**
   - Click "Upload Evidence" button for any SI
   - Choose a file (PDF recommended):
     - Example: Any syllabus PDF
     - Or any document related to the SI
   - Click "Open" or "Upload"

4. **Watch for AI Verification:**
   - A loading message appears: "AI is checking..."
   - Wait 2-5 seconds
   - AI result appears:
     ```
     ✅ Valid Evidence (85% confidence)
     Document Type: Syllabus
     Reason: File matches expected evidence type
     ```

**If you see AI feedback: ✅ WORKING PERFECTLY!**

### Step 5.3: Check AI Response Quality

Good AI response should show:
- ✅ **Verdict:** valid/uncertain/invalid
- ✅ **Confidence:** 0-100%
- ✅ **Document Type:** What kind of document
- ✅ **Reason:** Why it decided that
- ✅ **Suggestions:** What to improve (if needed)

### Step 5.4: Test Different File Types

Try uploading:
1. **PDF file** - Should work best (AI can read content)
2. **Image file (JPG/PNG)** - Should work (AI can see text)
3. **Word file** - May show "uncertain" (AI can't read directly)

---

## 📊 PART 6: Monitor Usage and Costs

### Step 6.1: Check Usage on OpenAI Platform

1. Go back to: **https://platform.openai.com**
2. Login to your account
3. Click **"Usage"** in left sidebar
4. You'll see:
   - **Total requests today**
   - **Cost per day**
   - **Credit balance remaining**

### Step 6.2: Understand Costs

**OpenAI GPT-3.5-turbo Pricing (October 2026):**

```
Input tokens: $0.50 per 1M tokens
Output tokens: $1.50 per 1M tokens

In simple terms:
- Each evidence check = ~500-1000 tokens
- Cost per check = ~$0.001-0.002 (₱0.06-0.11)
- 100 evidence uploads = ~$0.10-0.20 (₱6-12)
- 1000 evidence uploads = ~$1-2 (₱60-120)
```

**Expected Monthly Cost:**

| Faculty Count | Uploads/Month | Cost |
|---------------|---------------|------|
| 10 faculty | ~200 uploads | ₱12-24 |
| 50 faculty | ~1000 uploads | ₱60-120 |
| 100 faculty | ~2000 uploads | ₱120-240 |

**Very affordable! Less than a cup of coffee! ☕**

### Step 6.3: Set Up Email Alerts

1. In OpenAI Platform → **Billing** → **Usage limits**
2. Enable email notifications:
   - ✅ Alert at 75% of budget
   - ✅ Alert at 100% of budget
3. Enter your email
4. Click **"Save"**

Now you'll receive warnings before hitting limits!

---

## 🔧 PART 7: Troubleshooting

### Problem 1: "API key invalid" error

**Possible causes:**
- ❌ Key copied incorrectly (missing characters)
- ❌ Extra spaces before/after key
- ❌ Key was deleted from OpenAI platform

**Solutions:**
1. Copy the key again carefully
2. Trim spaces: `Ctrl+A` → `Ctrl+C` → Paste again
3. Create a new key if needed
4. Make sure it starts with `sk-proj-` or `sk-`

### Problem 2: "Insufficient credits" error

**Possible causes:**
- ❌ No credits in account
- ❌ Reached usage limit

**Solutions:**
1. Add credits: OpenAI Platform → Billing → Add to balance
2. Add minimum $5
3. Wait 1-2 minutes for credits to reflect

### Problem 3: "Rate limit exceeded" error

**Possible causes:**
- ❌ Too many requests in short time
- ❌ Free tier limits (if no payment method)

**Solutions:**
1. Wait 1 minute and try again
2. Ensure payment method is added (increases limits)
3. Upgrade to Tier 1 ($5+ spent = higher limits)

### Problem 4: AI returns "uncertain" for everything

**Possible causes:**
- ❌ Uploaded files don't match SI requirements
- ❌ Generic filenames (e.g., "document.pdf")
- ❌ Wrong file type uploaded

**Solutions:**
1. Use descriptive filenames matching the SI
   - ❌ Bad: "file1.pdf"
   - ✅ Good: "Syllabus_BSIT_2024.pdf"
2. Upload correct document type
3. Make sure PDF is readable (not scanned image only)

### Problem 5: "AI check unavailable" message

**Possible causes:**
- ❌ API key not saved correctly
- ❌ Network connection issue
- ❌ OpenAI service down (rare)

**Solutions:**
1. Check Settings → AI section → OpenAI is selected
2. Re-enter API key and save
3. Test internet connection
4. Wait 5 minutes and try again

### Problem 6: Slow AI response (> 10 seconds)

**Possible causes:**
- ❌ Large PDF files (> 5MB)
- ❌ Network slow
- ❌ OpenAI API busy

**Solutions:**
1. Compress large PDFs before upload
2. Check internet speed
3. Normal response time: 1-3 seconds
4. Max wait time: 10 seconds

---

## 🎓 PART 8: Best Practices

### For Administrators:

1. **Monitor usage weekly:**
   - Check OpenAI dashboard every Friday
   - Review total costs
   - Adjust budget limits if needed

2. **Set appropriate verification mode:**
   - **Advisory** during first month (training)
   - Switch to **Strict** after faculty are trained

3. **Communicate with faculty:**
   - Explain AI verification purpose
   - Show examples of good file naming
   - Provide feedback on common issues

### For Faculty:

1. **Use descriptive filenames:**
   ```
   ✅ Good examples:
   - Syllabus_BSIT_Programming1_2024.pdf
   - TeachingLoad_SemA_2024-2025.pdf
   - ClassSchedule_FirstSemester.pdf
   
   ❌ Bad examples:
   - document.pdf
   - file1.pdf
   - 123.pdf
   ```

2. **Upload correct document types:**
   - Check SI requirements first
   - Match evidence type exactly
   - Use PDF format when possible

3. **Review AI feedback:**
   - Read why AI accepted/rejected
   - Learn from suggestions
   - Improve next upload

---

## 📋 Quick Reference Card

### Essential URLs:
- OpenAI Platform: https://platform.openai.com
- API Keys: https://platform.openai.com/api-keys
- Billing: https://platform.openai.com/settings/organization/billing
- Usage: https://platform.openai.com/usage

### IPCR System:
- Login: http://localhost/ipcr-system
- Settings: Admin → Settings → AI Evidence Verification
- Test: Faculty login → Upload evidence

### Support:
- OpenAI Help: https://help.openai.com
- OpenAI Status: https://status.openai.com

---

## ✅ Setup Checklist

Print this and check off as you complete each step:

- [ ] Created OpenAI account
- [ ] Added payment method (card/PayPal)
- [ ] Set budget limit ($5-10)
- [ ] Added initial credits ($5-10)
- [ ] Created API key
- [ ] Copied API key safely
- [ ] Logged into IPCR as Admin
- [ ] Went to Settings
- [ ] Selected OpenAI provider
- [ ] Pasted API key
- [ ] Selected verification mode (Advisory)
- [ ] Saved settings
- [ ] Tested with faculty account
- [ ] Uploaded test file
- [ ] Saw AI feedback
- [ ] Set up email alerts on OpenAI
- [ ] Bookmarked OpenAI dashboard

**All checked? YOU'RE DONE! 🎉**

---

## 💡 Pro Tips

1. **Start small:** Begin with Advisory mode, observe for 1-2 weeks
2. **Train users:** Show faculty how AI works, what it expects
3. **Monitor costs:** Check weekly, should be very low (₱20-50/week)
4. **Good naming:** Encourage descriptive filenames
5. **Budget safety:** Set $10 monthly limit to prevent surprises
6. **Document types:** PDFs work best, images second, Word files less reliable

---

## 🚀 You're Ready!

Your IPCR system now has **intelligent AI evidence verification** powered by OpenAI GPT-3.5-turbo!

**What happens now:**
1. ✅ Every file upload gets AI verification
2. ✅ Faculty see instant feedback
3. ✅ Quality of evidence improves
4. ✅ Dean's review time reduced
5. ✅ System becomes smarter over time

**Costs remain LOW:** ₱30-120/month for most institutions

**Questions?** Refer back to the Troubleshooting section (Part 7).

---

**Setup Complete!** 🎊

**Created:** October 17, 2026  
**Version:** 1.0  
**For:** IPCR Management System with OpenAI Integration
