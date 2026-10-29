# 🚀 Modal System — Quick Start Guide

## For Developers Adding New Features

---

## 📋 Overview

The IPCR system now has a complete modal dialog system. Use these instead of native browser dialogs (`confirm()`, `alert()`, `prompt()`).

---

## 🎯 Quick Reference

### Replace These:
```javascript
// ❌ OLD - Don't use these anymore
confirm('Delete this?')
alert('Success!')
prompt('Enter name:')
```

### With These:
```javascript
// ✅ NEW - Use these instead
showConfirm('Delete Item', 'Delete this?', () => deleteItem())
showAlert('Success', 'Operation completed!', 'success')
showPrompt('Enter Name', 'Enter name:', (name) => saveName(name))
```

---

## 🔥 Common Use Cases

### 1. Simple Delete Confirmation

```javascript
showConfirm(
  'Delete User',
  'Permanently delete this user?',
  () => {
    // User clicked "Confirm" - do the deletion
    form.submit();
  },
  { confirmText: 'Delete', confirmClass: 'btn-danger', icon: 'bi-trash' }
);
```

### 2. Submit Confirmation

```javascript
showConfirm(
  'Submit IPCR',
  'Submit your IPCR for evaluation?<br>You cannot edit it after submission.',
  () => {
    // User confirmed - submit
    submitForm();
  },
  { confirmText: 'Submit', confirmClass: 'btn-success', icon: 'bi-send' }
);
```

### 3. Ask for Text Input

```javascript
showPrompt(
  'Enter Name',
  'What is your name?',
  (name) => {
    // User entered name - save it
    console.log('User entered:', name);
    saveToDatabase(name);
  },
  { placeholder: 'e.g. Juan Dela Cruz' }
);
```

### 4. Ask with Validation

```javascript
showPrompt(
  'Enter Employee ID',
  'Employee ID (minimum 4 characters):',
  (empId) => {
    // User entered valid ID - save it
    saveEmployeeId(empId);
  },
  { 
    placeholder: 'FAC001',
    validation: (val) => {
      if (val.length < 4) return 'Must be at least 4 characters';
      return true; // Valid
    }
  }
);
```

### 5. Success Message

```javascript
showAlert('Success', 'User created successfully!', 'success');
```

### 6. Error Message

```javascript
showAlert('Error', 'Failed to save data. Please try again.', 'error');
```

### 7. Warning Message

```javascript
showAlert('Warning', 'Your session will expire in 5 minutes.', 'warning');
```

### 8. Change Password

```javascript
showPasswordModal('Juan Dela Cruz', 123, (userId, password) => {
  // Password validated (min 6 chars) - save it
  resetPassword(userId, password);
});
```

### 9. Nested Prompts (Multi-step Input)

```javascript
showPrompt('Department Name', 'Enter name:', (name) => {
  showPrompt('Department Code', 'Enter code:', (code) => {
    // Both collected - save
    createDepartment(name, code);
  });
});
```

---

## 🎨 Styling Options

### Button Classes
```javascript
{ confirmClass: 'btn-danger' }   // Red (delete, destructive)
{ confirmClass: 'btn-success' }  // Green (approve, confirm)
{ confirmClass: 'btn-warning' }  // Orange (caution)
{ confirmClass: 'btn-primary' }  // Blue (standard action)
```

### Icons
```javascript
{ icon: 'bi-trash' }              // 🗑️ Delete
{ icon: 'bi-check-circle' }       // ✅ Approve
{ icon: 'bi-exclamation-triangle' } // ⚠️ Warning
{ icon: 'bi-send' }               // 📤 Submit
{ icon: 'bi-key' }                // 🔑 Password
{ icon: 'bi-question-circle' }    // ❓ Question
```

---

## 📖 Full API Reference

### `showConfirm()`

```javascript
showConfirm(title, message, onConfirm, options)
```

**Parameters:**
- `title` (string) — Modal title
- `message` (string) — HTML message to display
- `onConfirm` (function) — Called when user clicks confirm
- `options` (object) — Optional settings
  - `confirmText` — Button text (default: 'Confirm')
  - `confirmClass` — Button CSS class (default: 'btn-danger')
  - `icon` — Icon class (default: 'bi-question-circle-fill')

**Returns:** Promise<boolean> — true if confirmed, false if cancelled

### `showAlert()`

```javascript
showAlert(title, message, type)
```

**Parameters:**
- `title` (string) — Modal title
- `message` (string) — HTML message to display
- `type` (string) — 'success', 'error', 'warning', or 'info'

**Returns:** void

### `showPrompt()`

```javascript
showPrompt(title, message, onSubmit, options)
```

**Parameters:**
- `title` (string) — Modal title
- `message` (string) — HTML message to display
- `onSubmit` (function) — Called with input value when submitted
- `options` (object) — Optional settings
  - `placeholder` — Input placeholder text
  - `defaultValue` — Default input value
  - `required` — Whether input is required (default: true)
  - `validation` — Function(value) that returns true or error message

**Returns:** Promise<string|null> — input value or null if cancelled

### `showPasswordModal()`

```javascript
showPasswordModal(userName, userId, onSubmit)
```

**Parameters:**
- `userName` (string) — User's name to display
- `userId` (number) — User ID (passed to callback)
- `onSubmit` (function) — Called with (userId, password) when submitted

**Returns:** void

---

## ⚡ Pro Tips

### 1. HTML in Messages
You can use HTML in confirm/prompt/alert messages:

```javascript
showConfirm(
  'Delete',
  '<strong style="color:red">⚠️ WARNING:</strong><br>This cannot be undone!',
  () => deleteItem()
);
```

### 2. Async/Await Support
All functions return Promises, so you can use async/await:

```javascript
const confirmed = await showConfirm('Delete', 'Sure?', () => {});
if (confirmed) {
  // User confirmed
}
```

### 3. No Callback Needed
If you just want to show an alert, no callback needed:

```javascript
showAlert('Info', 'Processing complete', 'info');
```

### 4. Custom Validation
Add complex validation logic:

```javascript
showPrompt('Email', 'Enter email:', (email) => {
  saveEmail(email);
}, {
  validation: (val) => {
    if (!val.includes('@')) return 'Must be a valid email';
    if (val.length < 5) return 'Email too short';
    return true;
  }
});
```

### 5. Keyboard Shortcuts
All modals support:
- **ESC** — Cancel/close
- **Enter** — Confirm/submit (when not typing)

---

## 🐛 Troubleshooting

### Modal Not Showing?
1. Check browser console for errors
2. Ensure `footer.php` is included in your page
3. Ensure `main.js` is loaded
4. Check that function names are correct

### Validation Not Working?
1. Ensure validation function returns `true` or error string
2. Check console for JavaScript errors
3. Verify options object is passed correctly

### Nested Modals Not Working?
1. Call second modal inside first modal's callback
2. Don't try to open multiple modals simultaneously
3. Wait for first modal to get input before opening second

---

## 📞 Need Help?

1. **Check documentation:** Read `MODAL_SYSTEM_IMPLEMENTATION.md`
2. **See examples:** Open `test-modals.php` in browser
3. **View source:** Look at `assets/js/main.js` for implementation
4. **Test your code:** Use browser console to test functions

---

## ✅ Checklist for New Feature

When adding a new confirmation/prompt:

- [ ] Replace `confirm()` with `showConfirm()`
- [ ] Replace `alert()` with `showAlert()`
- [ ] Replace `prompt()` with `showPrompt()`
- [ ] Choose appropriate button class (danger/success/warning/primary)
- [ ] Choose appropriate icon
- [ ] Add validation if collecting user input
- [ ] Test on desktop and mobile
- [ ] Test keyboard navigation (ESC, Enter)

---

## 🎯 Best Practices

✅ **DO:**
- Use descriptive titles and messages
- Choose appropriate colors and icons
- Validate user input
- Provide clear error messages
- Test on mobile devices

❌ **DON'T:**
- Use native browser dialogs
- Block without confirmation (for destructive actions)
- Use generic messages ("Are you sure?")
- Skip validation on important inputs
- Forget to test keyboard navigation

---

**Happy coding! 🚀**

For more details, see `MODAL_SYSTEM_IMPLEMENTATION.md`
