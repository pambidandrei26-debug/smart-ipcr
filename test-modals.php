<?php
/**
 * Modal System Test Page
 * Test all modal types and interactions
 */
$pageTitle = 'Modal System Test';
$activePage = 'dashboard';
require_once __DIR__ . '/includes/header.php';
requireLogin();
?>

<style>
.test-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 20px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.test-card h3 {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.test-card p {
  color: #6b7280;
  font-size: 0.9rem;
  margin-bottom: 16px;
}
.btn-group-test {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
</style>

<div class="page-header">
  <h1><i class="bi bi-window-stack me-2"></i>Modal System Test</h1>
  <p class="text-muted mb-0">Test all modal types and interactions</p>
</div>

<div class="alert alert-info mb-4">
  <i class="bi bi-info-circle me-2"></i>
  <strong>Test Page:</strong> This page demonstrates all modal types used throughout the IPCR system. 
  Click buttons to test different modal configurations.
</div>

<!-- Confirm Modals -->
<div class="test-card">
  <h3><i class="bi bi-question-circle"></i>Confirm Modals</h3>
  <p>Standard confirmation dialogs with customizable buttons and icons</p>
  <div class="btn-group-test">
    <button class="btn btn-danger" onclick="testDeleteConfirm()">
      <i class="bi bi-trash me-1"></i>Delete Confirm
    </button>
    <button class="btn btn-warning" onclick="testWarningConfirm()">
      <i class="bi bi-exclamation-triangle me-1"></i>Warning Confirm
    </button>
    <button class="btn btn-success" onclick="testApproveConfirm()">
      <i class="bi bi-check-circle me-1"></i>Approve Confirm
    </button>
    <button class="btn btn-primary" onclick="testSubmitConfirm()">
      <i class="bi bi-send me-1"></i>Submit Confirm
    </button>
  </div>
</div>

<!-- Prompt Modals -->
<div class="test-card">
  <h3><i class="bi bi-pencil-square"></i>Prompt Modals</h3>
  <p>Input dialogs with validation support</p>
  <div class="btn-group-test">
    <button class="btn btn-primary" onclick="testBasicPrompt()">
      <i class="bi bi-input-cursor me-1"></i>Basic Prompt
    </button>
    <button class="btn btn-info" onclick="testValidatedPrompt()">
      <i class="bi bi-shield-check me-1"></i>Validated Prompt
    </button>
    <button class="btn btn-secondary" onclick="testOptionalPrompt()">
      <i class="bi bi-question me-1"></i>Optional Prompt
    </button>
    <button class="btn btn-success" onclick="testNestedPrompt()">
      <i class="bi bi-layers me-1"></i>Nested Prompts
    </button>
  </div>
</div>

<!-- Alert Modals -->
<div class="test-card">
  <h3><i class="bi bi-bell"></i>Alert Modals</h3>
  <p>Information and notification dialogs</p>
  <div class="btn-group-test">
    <button class="btn btn-success" onclick="testSuccessAlert()">
      <i class="bi bi-check-circle me-1"></i>Success Alert
    </button>
    <button class="btn btn-danger" onclick="testErrorAlert()">
      <i class="bi bi-x-circle me-1"></i>Error Alert
    </button>
    <button class="btn btn-warning" onclick="testWarningAlert()">
      <i class="bi bi-exclamation-triangle me-1"></i>Warning Alert
    </button>
    <button class="btn btn-info" onclick="testInfoAlert()">
      <i class="bi bi-info-circle me-1"></i>Info Alert
    </button>
  </div>
</div>

<!-- Password Modal -->
<div class="test-card">
  <h3><i class="bi bi-key"></i>Password Modal</h3>
  <p>Specialized password input with show/hide toggle and validation</p>
  <div class="btn-group-test">
    <button class="btn btn-warning" onclick="testPasswordModal()">
      <i class="bi bi-key me-1"></i>Change Password
    </button>
  </div>
</div>

<!-- Real-World Examples -->
<div class="test-card">
  <h3><i class="bi bi-gear"></i>Real-World Examples</h3>
  <p>Test modals as they appear in actual system workflows</p>
  <div class="btn-group-test">
    <button class="btn btn-outline-danger" onclick="testDeleteUser()">
      <i class="bi bi-person-x me-1"></i>Delete User Workflow
    </button>
    <button class="btn btn-outline-success" onclick="testSubmitIPCR()">
      <i class="bi bi-file-earmark-check me-1"></i>Submit IPCR Workflow
    </button>
    <button class="btn btn-outline-primary" onclick="testAddDepartment()">
      <i class="bi bi-building-add me-1"></i>Add Department Workflow
    </button>
  </div>
</div>

<!-- Result Display -->
<div class="test-card" id="result-card" style="display:none">
  <h3><i class="bi bi-clipboard-check"></i>Last Action Result</h3>
  <pre id="result-display" style="background:#f3f4f6;padding:12px;border-radius:6px;font-size:0.85rem;color:#1f2937"></pre>
</div>

<script>
function showResult(msg) {
  document.getElementById('result-card').style.display = 'block';
  document.getElementById('result-display').textContent = msg;
  setTimeout(() => {
    document.getElementById('result-card').style.display = 'none';
  }, 5000);
}

// ══════════════════════════════════════════════════════════
// CONFIRM MODAL TESTS
// ══════════════════════════════════════════════════════════
function testDeleteConfirm() {
  showConfirm(
    'Delete Item',
    'Are you sure you want to <strong>permanently delete</strong> this item?<br><br>This action <strong>cannot be undone</strong>.',
    () => {
      showResult('✓ User confirmed deletion');
      Toast.success('Item deleted successfully');
    },
    { confirmText: 'Delete', confirmClass: 'btn-danger', icon: 'bi-trash' }
  );
}

function testWarningConfirm() {
  showConfirm(
    'Warning',
    '<strong style="color:#dc2626">⚠️ WARNING:</strong> This action may have unintended consequences.<br><br>Are you sure you want to proceed?',
    () => {
      showResult('✓ User confirmed despite warning');
      Toast.warning('Action completed');
    },
    { confirmText: 'Proceed', confirmClass: 'btn-warning', icon: 'bi-exclamation-triangle' }
  );
}

function testApproveConfirm() {
  showConfirm(
    'Approve IPCR',
    'Approve this IPCR form?<br><br>The faculty member will be notified of the approval.',
    () => {
      showResult('✓ IPCR approved');
      Toast.success('IPCR approved successfully');
    },
    { confirmText: 'Approve', confirmClass: 'btn-success', icon: 'bi-check-circle' }
  );
}

function testSubmitConfirm() {
  showConfirm(
    'Submit Evaluation',
    'Submit your evaluation to the Administrator?<br><br>You will not be able to edit it after submission.',
    () => {
      showResult('✓ Evaluation submitted');
      Toast.success('Evaluation submitted');
    },
    { confirmText: 'Submit', confirmClass: 'btn-primary', icon: 'bi-send' }
  );
}

// ══════════════════════════════════════════════════════════
// PROMPT MODAL TESTS
// ══════════════════════════════════════════════════════════
function testBasicPrompt() {
  showPrompt(
    'Enter Your Name',
    'What is your name?',
    (value) => {
      showResult(`✓ User entered: "${value}"`);
      Toast.success(`Hello, ${value}!`);
    },
    { placeholder: 'e.g. John Doe' }
  );
}

function testValidatedPrompt() {
  showPrompt(
    'Enter Employee ID',
    'Enter employee ID (must be at least 4 characters):',
    (value) => {
      showResult(`✓ Valid employee ID: "${value}"`);
      Toast.success('Employee ID accepted');
    },
    { 
      placeholder: 'e.g. FAC001',
      validation: (val) => val.length >= 4 || 'Employee ID must be at least 4 characters'
    }
  );
}

function testOptionalPrompt() {
  showPrompt(
    'Enter Remarks',
    'Enter optional remarks (can be left blank):',
    (value) => {
      showResult(`✓ User entered: "${value || '(blank)'}"`);
      Toast.info(value ? 'Remarks saved' : 'No remarks entered');
    },
    { placeholder: 'Optional remarks...', required: false }
  );
}

function testNestedPrompt() {
  showPrompt(
    'Enter Department Name',
    'What is the department name?',
    (deptName) => {
      showPrompt(
        'Enter Department Code',
        'What is the department code?',
        (deptCode) => {
          showResult(`✓ Department created:\n  Name: "${deptName}"\n  Code: "${deptCode}"`);
          Toast.success(`Department ${deptName} (${deptCode}) created`);
        },
        { placeholder: 'e.g. CS, IT, BSCS' }
      );
    },
    { placeholder: 'e.g. Computer Science Department' }
  );
}

// ══════════════════════════════════════════════════════════
// ALERT MODAL TESTS
// ══════════════════════════════════════════════════════════
function testSuccessAlert() {
  showAlert('Success', 'Operation completed successfully!<br><br>All changes have been saved.', 'success');
  showResult('✓ Success alert displayed');
}

function testErrorAlert() {
  showAlert('Error', 'An error occurred while processing your request.<br><br>Please try again or contact support.', 'error');
  showResult('✓ Error alert displayed');
}

function testWarningAlert() {
  showAlert('Warning', 'Your session will expire in 5 minutes.<br><br>Please save your work.', 'warning');
  showResult('✓ Warning alert displayed');
}

function testInfoAlert() {
  showAlert('Information', 'The system will undergo maintenance tonight from 10 PM to 2 AM.<br><br>Please plan accordingly.', 'info');
  showResult('✓ Info alert displayed');
}

// ══════════════════════════════════════════════════════════
// PASSWORD MODAL TEST
// ══════════════════════════════════════════════════════════
function testPasswordModal() {
  showPasswordModal('Juan Dela Cruz', 123, (userId, password) => {
    showResult(`✓ Password changed for user ID ${userId}\n  New password: ${password}`);
    Toast.success('Password changed successfully');
  });
}

// ══════════════════════════════════════════════════════════
// REAL-WORLD WORKFLOW TESTS
// ══════════════════════════════════════════════════════════
function testDeleteUser() {
  showConfirm(
    'Delete User',
    '<strong style="color:#dc2626">⚠️ WARNING:</strong> Permanently delete user <strong>Juan Dela Cruz</strong>?<br><br>This action <strong>cannot be undone</strong>.<br><br><em>Note: Users with existing IPCR records cannot be deleted (deactivate instead).</em>',
    () => {
      showResult('✓ User deletion confirmed');
      Toast.success('User Juan Dela Cruz deleted');
    },
    { confirmText: 'Delete User', confirmClass: 'btn-danger', icon: 'bi-trash' }
  );
}

function testSubmitIPCR() {
  showConfirm(
    'Submit IPCR',
    'Submit your IPCR for evaluation?<br><br><strong>Note:</strong> You cannot edit it until the Dean returns it.',
    () => {
      showResult('✓ IPCR submission confirmed');
      Toast.success('IPCR submitted successfully!');
    },
    { confirmText: 'Submit', confirmClass: 'btn-success', icon: 'bi-send' }
  );
}

function testAddDepartment() {
  showPrompt(
    'Add Department',
    'Enter new department/program name:',
    (deptName) => {
      showPrompt(
        'Department Abbreviation',
        'Enter abbreviation (optional):',
        (deptAbbr) => {
          showResult(`✓ Department workflow completed:\n  Name: "${deptName}"\n  Abbreviation: "${deptAbbr || '(none)'}"`);
          Toast.success('Department added successfully');
        },
        { placeholder: 'e.g. CS, IT, BSIT...', required: false }
      );
    },
    { placeholder: 'e.g. Computer Science Department', required: true }
  );
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
