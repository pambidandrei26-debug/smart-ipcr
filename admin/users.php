<?php
// SECURITY: Check authentication and authorization BEFORE any output
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requireRole('admin','secretary','dean');

// NOW safe to include header (after authorization check)
$pageTitle  = 'User Management';
$activePage = 'users';
require_once __DIR__ . '/../includes/header.php';

$db = getDB();
$msg = ''; $msgType = 'success';

// Handle POST actions - ADMIN AND DEAN ONLY
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($currentRole, ['admin', 'dean'])) {
    requireCSRF(); // Verify CSRF token
    
    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'edit') {
        $empId    = trim($_POST['employee_id'] ?? '');
        $fName    = trim($_POST['first_name']   ?? '');
        $mName    = trim($_POST['middle_name']  ?? '');
        $lName    = trim($_POST['last_name']    ?? '');
        $suffix   = trim($_POST['suffix']       ?? '');
        $email    = trim($_POST['email']        ?? '');
        $role     = $_POST['role']              ?? 'faculty';
        $dept     = trim($_POST['department']   ?? '');
        $pos      = trim($_POST['position']     ?? '');
        $empStat  = $_POST['employment_status'] ?? 'permanent';
        $deanId   = (int)($_POST['assigned_dean_id'] ?? 0) ?: null;
        $returnUrl = trim($_POST['return_url']  ?? ''); // For redirecting back after save
        
        // If current user is a dean and adding a faculty, auto-assign to self
        if ($currentRole === 'dean' && $role === 'faculty' && !$deanId) {
            $deanId = $currentUserId;
        }
        
        $userId   = (int)($_POST['user_id']     ?? 0);
        $pw       = $_POST['password']          ?? '';

        // DETAILED VALIDATION
        $errors = [];
        
        // Required fields
        if (!$empId) $errors[] = 'Employee ID is required';
        if (!$fName) $errors[] = 'First Name is required';
        if (!$lName) $errors[] = 'Last Name is required';
        
        // Email validation
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        // Employee ID format validation (alphanumeric, dashes, underscores allowed)
        if ($empId && !preg_match('/^[a-zA-Z0-9_-]+$/', $empId)) {
            $errors[] = 'Employee ID can only contain letters, numbers, dashes, and underscores';
        }
        
        // Password validation for new users
        if ($action === 'create') {
            if (!$pw) {
                $errors[] = 'Password is required for new users';
            } elseif (strlen($pw) < 8) {
                $errors[] = 'Password must be at least 8 characters';
            }
        }
        
        // Department required for faculty
        if ($role === 'faculty' && !$dept) {
            $errors[] = 'Department is required for faculty members';
        }
        
        if (!empty($errors)) {
            $msg = implode('. ', $errors) . '.';
            $msgType = 'danger';
        } else {
            if ($action === 'create') {
                // Check for duplicate Employee ID
                $exists = $db->prepare("SELECT id FROM users WHERE employee_id=?");
                $exists->execute([$empId]);
                if ($exists->fetch()) {
                    $msg = "Employee ID '{$empId}' already exists. Please use a different ID.";
                    $msgType = 'danger';
                } else {
                    try {
                        $hash = password_hash($pw, PASSWORD_BCRYPT);
                        $db->prepare("INSERT INTO users (employee_id,first_name,middle_name,last_name,suffix,email,password_hash,role,department,college,position,employment_status,assigned_dean_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)")
                           ->execute([$empId,$fName,$mName,$lName,$suffix,$email,$hash,$role,$dept,$dept,$pos,$empStat,$deanId]);
                        $newId = $db->lastInsertId();
                        auditLog('created','user',$newId,null,['employee_id'=>$empId,'role'=>$role],'User created');
                        $msg = "✓ User {$fName} {$lName} ({$empId}) created successfully!";
                        
                        // Redirect back if return_url is provided
                        if ($returnUrl) {
                            header("Location: {$returnUrl}?msg=" . urlencode($msg));
                            exit;
                        }
                    } catch (Exception $e) {
                        $msg = "Database error: " . $e->getMessage();
                        $msgType = 'danger';
                        error_log("User creation error: " . $e->getMessage());
                    }
                }
            } else {
                try {
                    $db->prepare("UPDATE users SET first_name=?,middle_name=?,last_name=?,suffix=?,email=?,role=?,department=?,college=?,position=?,employment_status=?,assigned_dean_id=?,updated_at=NOW() WHERE id=?")
                       ->execute([$fName,$mName,$lName,$suffix,$email,$role,$dept,$dept,$pos,$empStat,$deanId,$userId]);
                    auditLog('updated','user',$userId,null,['role'=>$role,'dept'=>$dept],'User updated');
                    $msg = "✓ User {$fName} {$lName} updated successfully!";
                } catch (Exception $e) {
                    $msg = "Database error: " . $e->getMessage();
                    $msgType = 'danger';
                    error_log("User update error: " . $e->getMessage());
                }
            }
        }
    }

    if ($action === 'toggle_active') {
        $userId    = (int)($_POST['user_id'] ?? 0);
        $newActive = (int)($_POST['new_active'] ?? 0);
        if ($userId === $currentUserId) { $msg = 'You cannot deactivate your own account.'; $msgType = 'danger'; }
        else {
            if (!$newActive) {
                $admins = $db->query("SELECT COUNT(*) FROM users WHERE role='admin' AND is_active=1")->fetchColumn();
                $user   = $db->prepare("SELECT role FROM users WHERE id=?"); $user->execute([$userId]); $user=$user->fetch();
                if ($user['role']==='admin' && $admins<=1) { $msg='Cannot deactivate the only active admin.'; $msgType='danger'; }
                else { $db->prepare("UPDATE users SET is_active=?,updated_at=NOW() WHERE id=?")->execute([$newActive,$userId]); $msg=$newActive?'User activated.':'User deactivated.'; auditLog('updated','user',$userId,null,['is_active'=>$newActive],$msg); }
            } else { $db->prepare("UPDATE users SET is_active=?,updated_at=NOW() WHERE id=?")->execute([$newActive,$userId]); $msg='User activated.'; auditLog('updated','user',$userId,null,['is_active'=>1],'User activated'); }
        }
    }

    if ($action === 'reset_password') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $newPw  = trim($_POST['new_password'] ?? '');
        if (strlen($newPw) < 8) { $msg = 'Password must be at least 8 characters.'; $msgType = 'danger'; }
        else {
            $hash = password_hash($newPw, PASSWORD_BCRYPT);
            $db->prepare("UPDATE users SET password_hash=?,failed_attempts=0,lockout_until=NULL WHERE id=?")->execute([$hash,$userId]);
            auditLog('password_reset','user',$userId,null,null,'Password reset by admin');
            $msg = 'Password reset successfully.';
        }
    }

    if ($action === 'delete') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId === $currentUserId) {
            $msg = 'You cannot delete your own account.'; $msgType = 'danger';
        } else {
            // Check if user has IPCR forms
            $hasIpcr = $db->prepare("SELECT COUNT(*) FROM ipcr_forms WHERE user_id=?");
            $hasIpcr->execute([$userId]);
            if ($hasIpcr->fetchColumn() > 0) {
                $msg = 'Cannot delete: user has existing IPCR records. Deactivate instead.'; $msgType = 'danger';
            } else {
                $delUser = $db->prepare("SELECT first_name, last_name, employee_id FROM users WHERE id=?");
                $delUser->execute([$userId]);
                $delUser = $delUser->fetch();
                $db->prepare("DELETE FROM users WHERE id=?")->execute([$userId]);
                auditLog('deleted','user',$userId,$delUser['employee_id'],null,'User deleted: '.$delUser['first_name'].' '.$delUser['last_name']);
                $msg = "User {$delUser['first_name']} {$delUser['last_name']} deleted successfully.";
            }
        }
    }
}

// List users
$filterRole   = $_GET['role']   ?? '';
$filterStatus = $_GET['status'] ?? '';
$filterSearch = trim($_GET['q'] ?? '');
$where = "1=1";
if ($filterRole)   $where .= " AND role=" . $db->quote($filterRole);
if ($filterStatus === 'active')   $where .= " AND is_active=1";
if ($filterStatus === 'inactive') $where .= " AND is_active=0";
if ($filterSearch) $where .= " AND (first_name LIKE " . $db->quote('%'.$filterSearch.'%') . " OR last_name LIKE " . $db->quote('%'.$filterSearch.'%') . " OR employee_id LIKE " . $db->quote('%'.$filterSearch.'%') . ")";

$users = $db->query("SELECT * FROM users WHERE {$where} ORDER BY role, last_name")->fetchAll();
$deans = $db->query("SELECT * FROM users WHERE role='dean' AND is_active=1 ORDER BY last_name")->fetchAll();
$depts = $db->query("SELECT * FROM departments ORDER BY name")->fetchAll();

// Pagination
$perPage = 15;
$page = max(1, (int)($_GET['page'] ?? 1));
$totalUsers = count($users);
$totalPages = max(1, ceil($totalUsers / $perPage));
$page = min($page, $totalPages); // Ensure page doesn't exceed total
$offset = ($page - 1) * $perPage;
$paginatedUsers = array_slice($users, $offset, $perPage);

// Helper function to build pagination URLs preserving filters
function buildPaginationUrl($pageNum, $currentFilters) {
    $params = [];
    if (!empty($currentFilters['role'])) $params['role'] = $currentFilters['role'];
    if (!empty($currentFilters['status'])) $params['status'] = $currentFilters['status'];
    if (!empty($currentFilters['q'])) $params['q'] = $currentFilters['q'];
    $params['page'] = $pageNum;
    return '?' . http_build_query($params);
}

$currentFilters = [
    'role' => $filterRole,
    'status' => $filterStatus,
    'q' => $filterSearch
];

// Edit user
$editUser = null;
if (isset($_GET['edit'])) {
    $eu = $db->prepare("SELECT * FROM users WHERE id=?");
    $eu->execute([(int)$_GET['edit']]);
    $editUser = $eu->fetch();
}
?>

<div class="page-header">
  <div class="page-header-left">
    <h1><i class="bi bi-people me-2"></i>User Management</h1>
    <p class="text-muted mb-0 mt-1" style="font-size: 0.9rem;">Manage all system users including faculty, deans, secretaries, and administrators</p>
  </div>
  <?php if (in_array($currentRole, ['admin', 'dean'])): ?>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="document.getElementById('modal-title').textContent='Add New User';document.getElementById('user-form').reset();document.getElementById('action-field').value='create';document.getElementById('user-id-field').value='';document.getElementById('emp-id-field').removeAttribute('readonly');document.getElementById('pw-group').style.display=''">
    <i class="bi bi-person-plus me-2"></i>Add User
  </button>
  <?php else: ?>
  <div class="alert alert-info mb-0" style="font-size:.85rem;padding:8px 12px">
    <i class="bi bi-info-circle me-1"></i>View-only mode. Use "My Faculty" to request edits.
  </div>
  <?php endif; ?>
</div>

<?php if($msg): ?><div class="alert alert-<?= $msgType ?> mb-4"><?= h($msg) ?></div><?php endif; ?>

<form method="GET" class="filter-bar mb-4">
  <div class="flex-grow-1" style="max-width:260px">
    <input type="text" name="q" class="form-control" placeholder="Search name or ID..." value="<?= h($filterSearch) ?>">
  </div>
  <select name="role" class="form-select" style="min-width:140px">
    <option value="">All Roles</option>
    <option value="faculty" <?= $filterRole==='faculty'?'selected':'' ?>>Faculty</option>
    <option value="dean"    <?= $filterRole==='dean'?'selected':'' ?>>Dean</option>
    <option value="secretary" <?= $filterRole==='secretary'?'selected':'' ?>>Secretary</option>
    <option value="admin"   <?= $filterRole==='admin'?'selected':'' ?>>Admin</option>
  </select>
  <select name="status" class="form-select" style="min-width:140px">
    <option value="">All Status</option>
    <option value="active"   <?= $filterStatus==='active'?'selected':'' ?>>Active</option>
    <option value="inactive" <?= $filterStatus==='inactive'?'selected':'' ?>>Inactive</option>
  </select>
  <button type="submit" class="btn btn-primary">Filter</button>
  <a href="?" class="btn btn-outline-secondary">Reset</a>
</form>

<div class="table-container">
  <table class="ipcr-table">
    <thead><tr><th>Employee ID</th><th>Name</th><th>Role</th><th>College/Dept</th><th>Position</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
    <tbody>
      <?php if(!$paginatedUsers): ?><tr><td colspan="8" class="text-center text-muted py-4">No users found.</td></tr><?php endif; ?>
      <?php foreach($paginatedUsers as $u):
        $dean = null;
        if($u['assigned_dean_id']){$ds=$db->prepare("SELECT * FROM users WHERE id=?");$ds->execute([$u['assigned_dean_id']]);$dean=$ds->fetch();}
        $roleColors = ['faculty'=>'primary','dean'=>'warning','admin'=>'danger','secretary'=>'info'];
      ?>
      <tr>
        <td><code><?= h($u['employee_id']) ?></code></td>
        <td>
          <strong><?= h(fullName($u)) ?></strong>
          <?php if($dean): ?><div class="text-muted-sm">Evaluator: <?= h(fullName($dean)) ?></div><?php endif; ?>
        </td>
        <td><span class="badge bg-<?= $roleColors[$u['role']] ?? 'secondary' ?>"><?= h(ucfirst($u['role'])) ?></span></td>
        <td><?= h($u['department'] ?? '—') ?></td>
        <td><?= h($u['position'] ?? '—') ?></td>
        <td><span class="badge <?= $u['is_active'] ? 'bg-success' : 'bg-secondary' ?>"><?= $u['is_active'] ? 'Active' : 'Inactive' ?></span></td>
        <td class="text-muted-sm"><?= $u['last_login'] ? h(date('M d, Y H:i', strtotime($u['last_login']))) : '—' ?></td>
        <td class="actions">
          <?php if (in_array($currentRole, ['admin', 'dean'])): ?>
          <button class="btn btn-sm btn-outline-primary me-1" title="Edit"
            onclick="editUser(<?= htmlspecialchars(json_encode($u), ENT_QUOTES) ?>)">
            <i class="bi bi-pencil"></i>
          </button>
          <form method="POST" style="display:inline" class="toggle-active-form">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="action" value="toggle_active">
            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
            <input type="hidden" name="new_active" value="<?= $u['is_active'] ? '0' : '1' ?>">
            <button type="button" class="btn btn-sm <?= $u['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?> me-1 btn-toggle-active"
              data-action="<?= $u['is_active'] ? 'deactivate' : 'activate' ?>"
              data-username="<?= h(fullName($u)) ?>"
              title="<?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>">
              <i class="bi <?= $u['is_active'] ? 'bi-person-dash' : 'bi-person-check' ?>"></i>
            </button>
          </form>
          <button class="btn btn-sm btn-outline-warning btn-reset-password" title="Reset Password"
            data-user-id="<?= $u['id'] ?>"
            data-username="<?= h(fullName($u)) ?>">
            <i class="bi bi-key"></i>
          </button>
          <?php if(in_array($currentRole, ['admin','dean'])): ?>
          <form method="POST" style="display:inline" class="delete-user-form">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
            <button type="button" class="btn btn-sm btn-danger ms-1 btn-delete-user"
              data-username="<?= h(fullName($u)) ?>"
              title="Delete User">
              <i class="bi bi-trash"></i>
            </button>
          </form>
          <?php endif; ?>
          <?php else: ?>
          <span class="badge bg-secondary">View Only</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination-wrapper" style="display: flex; flex-direction: column; align-items: center; gap: 12px; margin-top: 20px; padding: 20px 0;">
  <div class="text-muted small">
    Showing <?= $offset + 1 ?> to <?= min($offset + $perPage, $totalUsers) ?> of <?= $totalUsers ?> users
  </div>
  <nav>
    <ul class="pagination mb-0">
      <?php if ($page > 1): ?>
      <li class="page-item">
        <a class="page-link" href="<?= buildPaginationUrl($page - 1, $currentFilters) ?>">
          <i class="bi bi-chevron-left"></i>
        </a>
      </li>
      <?php endif; ?>
      
      <?php
      $startPage = max(1, $page - 2);
      $endPage = min($totalPages, $page + 2);
      
      if ($startPage > 1): ?>
        <li class="page-item"><a class="page-link" href="<?= buildPaginationUrl(1, $currentFilters) ?>">1</a></li>
        <?php if ($startPage > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
      <?php endif; ?>
      
      <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
      <li class="page-item <?= $i === $page ? 'active' : '' ?>">
        <a class="page-link" href="<?= buildPaginationUrl($i, $currentFilters) ?>"><?= $i ?></a>
      </li>
      <?php endfor; ?>
      
      <?php if ($endPage < $totalPages): ?>
        <?php if ($endPage < $totalPages - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
        <li class="page-item"><a class="page-link" href="<?= buildPaginationUrl($totalPages, $currentFilters) ?>"><?= $totalPages ?></a></li>
      <?php endif; ?>
      
      <?php if ($page < $totalPages): ?>
      <li class="page-item">
        <a class="page-link" href="<?= buildPaginationUrl($page + 1, $currentFilters) ?>">
          <i class="bi bi-chevron-right"></i>
        </a>
      </li>
      <?php endif; ?>
    </ul>
  </nav>
</div>
<?php endif; ?>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal-title">Add New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        <div class="modal-body">
          <input type="hidden" name="action" id="action-field" value="create">
          <input type="hidden" name="user_id" id="user-id-field">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Employee ID <span class="required-star">*</span></label>
              <input class="form-control" name="employee_id" id="emp-id-field" required placeholder="e.g. FAC004">
              <div class="invalid-feedback" id="emp-id-error">
                Employee ID already exists!
              </div>
              <div class="valid-feedback" id="emp-id-success">
                Employee ID is available!
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Role <span class="required-star">*</span></label>
              <select class="form-select" name="role" id="role-field" onchange="document.getElementById('dean-assign-row').style.display=this.value==='faculty'?'':'none'">
                <option value="faculty">Faculty</option>
                <option value="dean">Dean</option>
                <option value="secretary">Secretary</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Employment Status</label>
              <select class="form-select" name="employment_status" id="emp-stat-field">
                <option value="permanent">Permanent</option>
                <option value="temporary">Temporary</option>
                <option value="contractual">Contractual</option>
                <option value="part-time">Part-time</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">First Name <span class="required-star">*</span></label>
              <input class="form-control" name="first_name" id="fn-field" required>
              <div class="invalid-feedback">First name is required</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input class="form-control" name="middle_name" id="mn-field">
            </div>
            <div class="col-md-3">
              <label class="form-label">Last Name <span class="required-star">*</span></label>
              <input class="form-control" name="last_name" id="ln-field" required>
              <div class="invalid-feedback">Last name is required</div>
            </div>
            <div class="col-md-1">
              <label class="form-label">Suffix</label>
              <input class="form-control" name="suffix" id="sx-field" placeholder="Jr.">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input class="form-control" type="email" name="email" id="em-field">
            </div>
            <div class="col-md-6">
              <label class="form-label">Position</label>
              <select class="form-select" name="position" id="pos-field">
                <option value="">— Select Position —</option>
                <?php foreach ($FACULTY_POSITIONS as $pos): ?>
                <option value="<?= h($pos) ?>"><?= h($pos) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">College / Department</label>
              <select class="form-select" name="department" id="dept-field">
                <option value="">— Select Department —</option>
                <?php 
                // Get departments from departments table
                $depts = $db->query("SELECT name FROM departments ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
                foreach($depts as $d): 
                ?>
                <option value="<?= h($d) ?>"><?= h($d) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12" id="dean-assign-row">
              <label class="form-label">Assign Evaluator (Dean)</label>
              <select class="form-select" name="assigned_dean_id" id="dean-field">
                <option value="">— Select Dean —</option>
                <?php foreach($deans as $d): ?>
                <option value="<?= $d['id'] ?>"><?= h(fullName($d)) ?> — <?= h($d['department']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12" id="pw-group">
              <label class="form-label">Temporary Password <span class="required-star">*</span></label>
              <input class="form-control" type="text" name="password" id="pw-field" placeholder="Min 8 characters" minlength="8">
              <div class="invalid-feedback" id="pw-error">Password must be at least 8 characters</div>
              <div class="valid-feedback" id="pw-success">Password strength: Good</div>
              <div class="form-text">User can change this after first login.</div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<script>
// ═══════════════════════════════════════════════════════════
// REAL-TIME EMPLOYEE ID VALIDATION
// ═══════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function() {
  const empIdField = document.getElementById('emp-id-field');
  const empIdError = document.getElementById('emp-id-error');
  const empIdSuccess = document.getElementById('emp-id-success');
  const actionField = document.getElementById('action-field');
  const userIdField = document.getElementById('user-id-field');
  
  // Safety check
  if (!empIdField || !empIdError || !empIdSuccess || !actionField) {
    console.error('Employee ID validation: Required elements not found');
    return;
  }
  
  const form = empIdField.closest('form');
  let isChecking = false;
  
  // Debounced check function
  const checkEmployeeId = window.debounce(async function() {
    const employeeId = empIdField.value.trim();
    const action = actionField.value;
    
    // Don't check if empty
    if (!employeeId) {
      empIdField.classList.remove('is-invalid', 'is-valid');
      empIdSuccess.style.display = 'none';
      empIdError.style.display = 'none';
      window.validationState.empId = false;
      window.updateSubmitButton();
      return;
    }
    
    // For edit mode, mark as valid without checking
    if (action === 'edit') {
      empIdField.classList.remove('is-invalid');
      empIdField.classList.add('is-valid');
      empIdSuccess.style.display = 'block';
      empIdSuccess.textContent = `Employee ID '${employeeId}' (current)`;
      window.validationState.empId = true;
      window.updateSubmitButton();
      return;
    }
    
    isChecking = true;
    
    try {
      const userId = action === 'edit' ? userIdField.value : 0;
      const response = await fetch(`<?= BASE_URL ?>/api/check-employee-id.php?employee_id=${encodeURIComponent(employeeId)}&exclude_user_id=${userId}`);
      const data = await response.json();
      
      if (data.exists) {
        // Employee ID already exists
        empIdField.classList.remove('is-valid');
        empIdField.classList.add('is-invalid');
        empIdError.style.display = 'block';
        empIdSuccess.style.display = 'none';
        empIdError.textContent = `Employee ID '${employeeId}' already exists!`;
        window.validationState.empId = false;
      } else {
        // Employee ID is available
        empIdField.classList.remove('is-invalid');
        empIdField.classList.add('is-valid');
        empIdError.style.display = 'none';
        empIdSuccess.style.display = 'block';
        empIdSuccess.textContent = `Employee ID '${employeeId}' is available!`;
        window.validationState.empId = true;
      }
      
      // Update submit button
      window.updateSubmitButton();
      
    } catch (error) {
      console.error('Error checking employee ID:', error);
      empIdField.classList.remove('is-invalid', 'is-valid');
      window.validationState.empId = false;
      window.updateSubmitButton();
    } finally {
      isChecking = false;
    }
  }, 500);
  
  // Listen to input
  empIdField.addEventListener('input', checkEmployeeId);
  
  // Also validate on form submit as final check
  form.addEventListener('submit', async function(e) {
    const action = actionField.value;
    const employeeId = empIdField.value.trim();
    
    // Only validate for create action
    if (action !== 'create' || !employeeId) {
      return true;
    }
    
    // If already validated and available, allow submit
    if (window.validationState.empId) {
      return true;
    }
    
    // Prevent default and show error
    e.preventDefault();
    empIdField.classList.add('is-invalid');
    empIdError.textContent = 'Please use a valid Employee ID!';
    Toast.error('Please fix the Employee ID before submitting.', 'Validation Error');
    return false;
  });
});

// ═══════════════════════════════════════════════════════════
// COMPREHENSIVE FORM VALIDATION
// ═══════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('#userModal form');
  if (!form) {
    console.error('Form validation: Modal form not found');
    return;
  }
  
  const submitBtn = form.querySelector('button[type="submit"]');
  if (!submitBtn) {
    console.error('Form validation: Submit button not found');
    return;
  }
  
  // Get all required fields
  const fields = {
    empId: document.getElementById('emp-id-field'),
    firstName: document.getElementById('fn-field'),
    lastName: document.getElementById('ln-field'),
    password: document.getElementById('pw-field'),
    role: document.getElementById('role-field'),
    department: document.getElementById('dept-field')
  };
  
  // Check if all fields exist
  if (!fields.firstName || !fields.lastName || !fields.password) {
    console.error('Form validation: Required field elements not found');
    return;
  }
  
  // Validation state
  window.validationState = {
    empId: false, // Required - validated in real-time
    firstName: false,
    lastName: false,
    password: false,
    department: true // Optional unless role is faculty
  };
  
  // Check if form is valid
  window.updateSubmitButton = function updateSubmitButton() {
    const action = document.getElementById('action-field').value;
    const role = fields.role.value;
    
    // Check required fields based on action and role
    let allValid = window.validationState.empId && 
                   window.validationState.firstName && 
                   window.validationState.lastName;
    
    // Password only required for create action
    if (action === 'create') {
      allValid = allValid && window.validationState.password;
    }
    
    // Department required for faculty
    if (role === 'faculty') {
      allValid = allValid && window.validationState.department;
    }
    
    submitBtn.disabled = !allValid;
  }
  
  // Real-time validation for First Name
  fields.firstName.addEventListener('input', function() {
    const value = this.value.trim();
    if (value.length === 0) {
      this.classList.add('is-invalid');
      this.classList.remove('is-valid');
      window.validationState.firstName = false;
    } else if (value.length < 2) {
      this.classList.add('is-invalid');
      this.classList.remove('is-valid');
      this.nextElementSibling.textContent = 'First name must be at least 2 characters';
      window.validationState.firstName = false;
    } else {
      this.classList.remove('is-invalid');
      this.classList.add('is-valid');
      window.validationState.firstName = true;
    }
    window.updateSubmitButton();
  });
  
  // Real-time validation for Last Name
  fields.lastName.addEventListener('input', function() {
    const value = this.value.trim();
    if (value.length === 0) {
      this.classList.add('is-invalid');
      this.classList.remove('is-valid');
      window.validationState.lastName = false;
    } else if (value.length < 2) {
      this.classList.add('is-invalid');
      this.classList.remove('is-valid');
      this.nextElementSibling.textContent = 'Last name must be at least 2 characters';
      window.validationState.lastName = false;
    } else {
      this.classList.remove('is-invalid');
      this.classList.add('is-valid');
      window.validationState.lastName = true;
    }
    window.updateSubmitButton();
  });
  
  // Real-time validation for Password (with strength indicator)
  fields.password.addEventListener('input', function() {
    const value = this.value;
    const pwError = document.getElementById('pw-error');
    const pwSuccess = document.getElementById('pw-success');
    
    if (value.length === 0) {
      this.classList.add('is-invalid');
      this.classList.remove('is-valid');
      pwError.textContent = 'Password is required for new users';
      window.validationState.password = false;
    } else if (value.length < 8) {
      this.classList.add('is-invalid');
      this.classList.remove('is-valid');
      pwError.textContent = `Password must be at least 8 characters (${value.length}/8)`;
      window.validationState.password = false;
    } else {
      this.classList.remove('is-invalid');
      this.classList.add('is-valid');
      
      // Calculate password strength
      let strength = 'Weak';
      let score = 0;
      
      if (value.length >= 8) score++;
      if (/[a-z]/.test(value) && /[A-Z]/.test(value)) score++;
      if (/[0-9]/.test(value)) score++;
      if (/[^a-zA-Z0-9]/.test(value)) score++;
      
      if (score <= 1) strength = 'Weak 😕';
      else if (score === 2) strength = 'Good 👍';
      else if (score === 3) strength = 'Strong 💪';
      else strength = 'Very Strong 🔒';
      
      pwSuccess.textContent = `Password strength: ${strength}`;
      window.validationState.password = true;
    }
    window.updateSubmitButton();
  });
  
  // Validation for Department (required for faculty)
  fields.department.addEventListener('change', function() {
    const role = fields.role.value;
    if (role === 'faculty') {
      if (!this.value) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
        window.validationState.department = false;
      } else {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
        window.validationState.department = true;
      }
    } else {
      this.classList.remove('is-invalid', 'is-valid');
      window.validationState.department = true;
    }
    window.updateSubmitButton();
  });
  
  // Role change handler
  fields.role.addEventListener('change', function() {
    // Re-validate department when role changes
    const deptField = fields.department;
    if (this.value === 'faculty') {
      if (!deptField.value) {
        window.validationState.department = false;
      }
    } else {
      window.validationState.department = true;
    }
    window.updateSubmitButton();
  });
  
  // Clear validation on modal close
  document.getElementById('userModal').addEventListener('hidden.bs.modal', function() {
    // Clear all validation states
    Object.values(fields).forEach(field => {
      if (field) {
        field.classList.remove('is-valid', 'is-invalid');
      }
    });
    
    // Reset validation state
    window.validationState = {
      empId: false,
      firstName: false,
      lastName: false,
      password: false,
      department: true
    };
    
    submitBtn.disabled = false;
  });
  
  // Initialize on modal show
  document.getElementById('userModal').addEventListener('shown.bs.modal', function() {
    const action = document.getElementById('action-field').value;
    
    // For edit mode, mark existing values as valid
    if (action === 'edit') {
      if (fields.empId.value) window.validationState.empId = true;
      if (fields.firstName.value) window.validationState.firstName = true;
      if (fields.lastName.value) window.validationState.lastName = true;
      window.validationState.password = true; // Not required for edit
      
      // Update department validation
      const role = fields.role.value;
      if (role === 'faculty' && fields.department.value) {
        window.validationState.department = true;
      }
    }
    
    window.updateSubmitButton();
  });
});

function editUser(u) {
  document.getElementById('modal-title').textContent = 'Edit User';
  document.getElementById('action-field').value  = 'edit';
  document.getElementById('user-id-field').value = u.id;
  document.getElementById('emp-id-field').value  = u.employee_id;
  document.getElementById('emp-id-field').setAttribute('readonly','readonly');
  document.getElementById('role-field').value    = u.role;
  document.getElementById('fn-field').value      = u.first_name;
  document.getElementById('mn-field').value      = u.middle_name || '';
  document.getElementById('ln-field').value      = u.last_name;
  document.getElementById('sx-field').value      = u.suffix || '';
  document.getElementById('em-field').value      = u.email || '';
  document.getElementById('pos-field').value     = u.position || '';
  document.getElementById('dept-field').value    = u.department || '';
  document.getElementById('emp-stat-field').value= u.employment_status || 'permanent';
  if(u.assigned_dean_id) document.getElementById('dean-field').value = u.assigned_dean_id;
  document.getElementById('dean-assign-row').style.display = u.role==='faculty' ? '' : 'none';
  document.getElementById('pw-group').style.display = 'none';
  new bootstrap.Modal(document.getElementById('userModal')).show();
}

// ── Modal Handlers for User Actions ──────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  // Toggle Active/Inactive - Using event delegation
  document.body.addEventListener('click', function(e) {
    if (e.target.closest('.btn-toggle-active')) {
      const btn = e.target.closest('.btn-toggle-active');
      const action = btn.dataset.action;
      const username = btn.dataset.username;
      const form = btn.closest('.toggle-active-form');
      
      showConfirm(
        action === 'deactivate' ? 'Deactivate User' : 'Activate User',
        `${action === 'deactivate' ? 'Deactivate' : 'Activate'} user <strong>${username}</strong>?`,
        () => form.submit(),
        { 
          confirmText: action === 'deactivate' ? 'Deactivate' : 'Activate',
          confirmClass: action === 'deactivate' ? 'btn-danger' : 'btn-success',
          icon: action === 'deactivate' ? 'bi-person-dash' : 'bi-person-check'
        }
      );
    }
  });
  
  // Reset Password - Using event delegation
  document.body.addEventListener('click', function(e) {
    if (e.target.closest('.btn-reset-password')) {
      const btn = e.target.closest('.btn-reset-password');
      const userId = btn.dataset.userId;
      const username = btn.dataset.username;
      
      showPasswordModal(username, userId, (userId, password) => {
        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
          <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
          <input type="hidden" name="action" value="reset_password">
          <input type="hidden" name="user_id" value="${userId}">
          <input type="hidden" name="new_password" value="${password}">
        `;
        document.body.appendChild(form);
        form.submit();
      });
    }
  });
  
  // Delete User - Using event delegation for dynamic buttons
  document.body.addEventListener('click', function(e) {
    if (e.target.closest('.btn-delete-user')) {
      const btn = e.target.closest('.btn-delete-user');
      const username = btn.dataset.username;
      const form = btn.closest('.delete-user-form');
      
      showConfirm(
        'Delete User',
        `<strong style="color:#dc2626">⚠️ WARNING:</strong> Permanently delete user <strong>${username}</strong>?<br><br>This action <strong>cannot be undone</strong>.<br><br><em>Note: Users with existing IPCR records cannot be deleted (deactivate instead).</em>`,
        () => form.submit(),
        { 
          confirmText: 'Delete User',
          confirmClass: 'btn-danger',
          icon: 'bi-trash'
        }
      );
    }
  });
});
</script>
