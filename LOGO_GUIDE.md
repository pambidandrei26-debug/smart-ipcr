# 🎨 Logo & Branding Guide

**IPCR System - Logo Setup**

---

## 📍 Current Logo Files

### Located in: `/assets/images/`

```
✅ isu-logo.svg     - ISU main logo (SVG format, scalable)
✅ R.png            - ISU logo (PNG format, backup)
✅ ceat.jpg         - CEAT department logo
✅ gemini_generated.jpg - Login background image
```

---

## 🖼️ Where Logos Are Used

### 1. **Login Page**
```
Location: index.php, forgot-password.php
Logo: R.png (fallback: isu-logo.svg)
Size: Large (120x120px)
Style: Centered, prominent
```

### 2. **Header/Topbar** (All Pages)
```
Location: includes/header.php
Logo: R.png (fallback: isu-logo.svg)
Size: Small (40x40px)
Style: Top-left corner, circular
```

### 3. **Print/Export**
```
Location: exports/print-ipcr.php
Logo: isu-logo.svg
Size: Medium
Style: Header letterhead
```

---

## 🔄 Logo Fallback System

The system uses smart fallback:

```
1st Try: R.png           (main PNG logo)
2nd Try: isu-logo.svg    (SVG fallback)
3rd Try: Hide/Default    (if both fail)
```

**This ensures the logo always displays even if one file is missing.**

---

## 📐 Logo Specifications

### ISU Logo (R.png / isu-logo.svg)

```
Recommended Size: 500x500px minimum
Format: PNG with transparency OR SVG
Colors: ISU official colors (green/gold)
File Size: < 100KB (optimized)
Background: Transparent
```

### Department Logos (e.g., ceat.jpg)

```
Size: 300x300px minimum
Format: JPG or PNG
Usage: Department-specific pages
File Size: < 50KB
```

---

## 🎨 How to Replace/Update Logo

### Option 1: Replace Existing Files

```
1. Prepare your new ISU logo
2. Save as: R.png (500x500px, transparent)
3. Place in: /assets/images/R.png
4. Overwrite existing file
5. Refresh browser (Ctrl+F5)
6. Done! ✅
```

### Option 2: Update SVG Logo

```
1. Get SVG version of ISU logo
2. Save as: isu-logo.svg
3. Place in: /assets/images/isu-logo.svg
4. Replace existing file
5. Refresh browser
6. Done! ✅
```

### Option 3: Add Department Logo

```
1. Save department logo as: dept-name.jpg
2. Place in: /assets/images/
3. Update code where needed (if custom dept pages)
4. Done! ✅
```

---

## 🖼️ Background Image (Login Page)

### Current File: `gemini_generated.jpg`

**Specifications:**
```
Resolution: 1920x1080 or higher
Format: JPG (optimized)
File Size: < 1MB
Content: ISU campus or professional abstract
Style: Works with dark green overlay
```

**How to Replace:**
```
1. Save new image as: gemini_generated.jpg
2. Place in: /assets/images/
3. Overwrite existing
4. Refresh login page
5. Background updates automatically! ✅
```

**Fallback:**
If image missing → Beautiful gradient background (green+gold)

---

## 🎨 Branding Colors

### ISU Official Colors (used in system):

```css
Primary Green: #16a34a
Dark Green: #065f46
Light Green: #dcfce7
Gold/Yellow: #eab308
```

### Usage in System:
```
- Primary buttons: Green (#16a34a)
- Headers: Dark green gradient
- Highlights: Gold accents
- Backgrounds: Light green
- Text: Dark gray (#1f2937)
```

---

## 📱 Responsive Logo Display

### Desktop (> 768px):
```
Header Logo: 40x40px
Login Logo: 120x120px
Full visibility
```

### Mobile (< 768px):
```
Header Logo: 36x36px
Login Logo: 100x100px
Optimized spacing
```

---

## 🔧 Technical Details

### Logo in Header (header.php)

```php
<img src="<?= BASE_URL ?>/assets/images/R.png"
     class="topbar-brand-logo" 
     alt="ISU Logo"
     onerror="this.onerror=null; this.src='<?= BASE_URL ?>/assets/images/isu-logo.svg'">
```

**Features:**
- ✅ Loads R.png first
- ✅ Falls back to isu-logo.svg if R.png fails
- ✅ Prevents infinite error loop
- ✅ Always displays something

### Logo in Login (index.php)

```php
<img src="<?= BASE_URL ?>/assets/images/R.png"
     onerror="this.src='<?= BASE_URL ?>/assets/images/isu-logo.svg'"
     class="login-isu-logo" 
     alt="ISU Ilagan Logo">
```

**Features:**
- ✅ Larger display (120px)
- ✅ SVG fallback
- ✅ Centered positioning
- ✅ Responsive sizing

---

## ✅ Logo Checklist

### Verify Logo Display:

**Login Page:**
- [ ] Logo appears centered
- [ ] Size is appropriate (not pixelated)
- [ ] Transparent background works
- [ ] Responsive on mobile

**Header/Topbar:**
- [ ] Logo appears top-left
- [ ] Circular shape renders correctly
- [ ] Size is 40x40px
- [ ] Clickable (if linked)

**Print Pages:**
- [ ] Logo appears in letterhead
- [ ] High resolution (not blurry)
- [ ] Proper alignment
- [ ] Prints correctly

---

## 🎨 Customization Tips

### 1. Different Logo Per Department

```php
// In department pages, use:
<?php if($_SESSION['department'] === 'CEAT'): ?>
  <img src="<?= BASE_URL ?>/assets/images/ceat.jpg" alt="CEAT Logo">
<?php else: ?>
  <img src="<?= BASE_URL ?>/assets/images/R.png" alt="ISU Logo">
<?php endif; ?>
```

### 2. Add Subtext Under Logo

```php
<div class="logo-container">
  <img src="R.png" alt="ISU Logo">
  <div class="logo-subtext">College of Education</div>
</div>
```

### 3. Animated Logo (Optional)

```css
.topbar-brand-logo:hover {
  transform: scale(1.1);
  transition: transform 0.3s ease;
}
```

---

## 📊 Logo File Sizes

### Recommended Sizes:

| File | Current | Recommended |
|------|---------|-------------|
| R.png | ~50KB | < 100KB |
| isu-logo.svg | ~10KB | < 50KB |
| ceat.jpg | ~30KB | < 50KB |
| gemini_generated.jpg | ~500KB | < 1MB |

**Optimize large files:**
```
Use: TinyPNG.com or Squoosh.app
Result: 50-80% size reduction
Quality: No visible loss
```

---

## 🆘 Troubleshooting

### Problem 1: Logo Not Showing

**Check:**
```
1. File exists in /assets/images/
2. Filename is correct (case-sensitive!)
   - ✅ R.png (correct)
   - ❌ r.png (wrong on Linux servers)
3. File permissions (644 or 755)
4. Clear browser cache (Ctrl+F5)
```

### Problem 2: Logo Blurry/Pixelated

**Solution:**
```
1. Use higher resolution image (500x500px+)
2. Or use SVG format (scales perfectly)
3. Save PNG with transparency
4. Optimize with TinyPNG
```

### Problem 3: Logo Wrong Colors

**Solution:**
```
1. Check image is correct ISU logo
2. Verify no dark mode filters
3. Use official ISU branding assets
4. Check CSS filters/effects
```

---

## 📁 Quick Reference

### File Paths:
```
Logo Files: /assets/images/
Header Logo: includes/header.php (line ~358)
Login Logo: index.php (line ~153)
Print Logo: exports/print-ipcr.php
```

### Image URLs:
```
http://localhost/ipcr-system/assets/images/R.png
http://localhost/ipcr-system/assets/images/isu-logo.svg
http://localhost/ipcr-system/assets/images/ceat.jpg
```

---

## ✅ Summary

**Current Status:**
- ✅ ISU logo files exist (R.png, isu-logo.svg)
- ✅ Fallback system implemented
- ✅ Responsive design
- ✅ Used in login, header, prints
- ✅ Optimized file sizes

**No Action Needed** - Logo system working correctly!

**To Customize:**
- Replace R.png with your official ISU logo
- Update gemini_generated.jpg with campus photo
- Add department logos as needed

---

**Last Updated:** October 17, 2026  
**Status:** ✅ Working  
**Files:** Complete  
**Fallback:** Implemented
