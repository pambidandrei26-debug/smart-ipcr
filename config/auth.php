<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/constants.php';

function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        // Security settings
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        // Enable secure flag only if HTTPS
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            ini_set('session.cookie_secure', 1);
        }
        
        // Session lifetime (12 hours absolute, 30 minutes idle)
        ini_set('session.gc_maxlifetime', getenv('SESSION_LIFETIME') ?: 21600);
        
        session_name('IPCR_SESSION');
        session_start();
        
        // Session fixation protection
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
            $_SESSION['created'] = time();
            $_SESSION['last_activity'] = time(); // SEC-013: Track last activity
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
        }
        
        // SEC-013: Idle timeout check (30 minutes)
        $idleTimeout = 1800; // 30 minutes in seconds
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $idleTimeout) {
            // Session expired due to inactivity
            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL . '/index.php?error=session_expired');
            exit;
        }
        $_SESSION['last_activity'] = time(); // Update last activity timestamp
        
        // Regenerate session ID every 30 minutes for security
        if (isset($_SESSION['created']) && time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
        
        // Validate IP address (basic session hijacking prevention)
        if (isset($_SESSION['user_ip']) && 
            $_SESSION['user_ip'] !== ($_SERVER['REMOTE_ADDR'] ?? '')) {
            // IP changed - possible hijacking
            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL . '/index.php?error=session_invalid');
            exit;
        }
    }
}

function isLoggedIn(): bool {
    startSecureSession();
    if (empty($_SESSION['user_id'])) return false;
    if (empty($_SESSION['expires_at'])) return false;
    if (time() > $_SESSION['expires_at']) {
        logoutUser();
        return false;
    }
    return true;
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

function requireRole(string ...$roles): void {
    requireLogin();
    if (!in_array($_SESSION['role'], $roles)) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    }
}

// ═══════════════════════════════════════════════════════════
// CSRF PROTECTION
// ═══════════════════════════════════════════════════════════

/**
 * Generate CSRF token for forms
 * 
 * @return string CSRF token
 */
function generateCSRFToken(): string {
    startSecureSession();
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string|null $token Token to verify
 * @return bool True if valid
 */
function verifyCSRFToken(?string $token): bool {
    startSecureSession();
    
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    // Use hash_equals to prevent timing attacks
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Require valid CSRF token for POST requests
 * Dies with 403 if invalid
 */
function requireCSRF(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        
        if (!verifyCSRFToken($token)) {
            error_log('CSRF token validation failed from IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            http_response_code(403);
            
            // Check if AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                die(json_encode([
                    'success' => false,
                    'message' => 'CSRF token validation failed. Please refresh the page.'
                ]));
            } else {
                die('<h1>403 Forbidden</h1><p>CSRF token validation failed. Please refresh the page and try again.</p>');
            }
        }
    }
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $st = $db->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
    $st->execute([$_SESSION['user_id']]);
    return $st->fetch() ?: null;
}

function loginUser(string $employeeId, string $password): array {
    $db = getDB();
    $st = $db->prepare("SELECT * FROM users WHERE employee_id = ?");
    $st->execute([trim($employeeId)]);
    $user = $st->fetch();

    if (!$user) {
        return ['success' => false, 'message' => 'Invalid Employee ID or password.'];
    }

    // Check lockout
    if ($user['lockout_until'] && strtotime($user['lockout_until']) > time()) {
        $mins = ceil((strtotime($user['lockout_until']) - time()) / 60);
        return ['success' => false, 'message' => "Account locked. Try again in {$mins} minute(s)."];
    }

    if (!$user['is_active']) {
        return ['success' => false, 'message' => 'Your account is inactive. Contact the Administrator.'];
    }

    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        $attempts = ($user['failed_attempts'] ?? 0) + 1;
        $maxAttempts = 5;
        if ($attempts >= $maxAttempts) {
            $lockout = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            $db->prepare("UPDATE users SET failed_attempts = 0, lockout_until = ? WHERE id = ?")
               ->execute([$lockout, $user['id']]);
            return ['success' => false, 'message' => 'Too many failed attempts. Account locked for 15 minutes.'];
        }
        $db->prepare("UPDATE users SET failed_attempts = ? WHERE id = ?")
           ->execute([$attempts, $user['id']]);
        $remaining = $maxAttempts - $attempts;
        return ['success' => false, 'message' => "Invalid credentials. {$remaining} attempt(s) remaining."];
    }

    // Success — reset attempts, update last login
    $db->prepare("UPDATE users SET failed_attempts = 0, lockout_until = NULL, last_login = NOW() WHERE id = ?")
       ->execute([$user['id']]);

    startSecureSession();
    session_regenerate_id(true);
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['role']       = $user['role'];
    $_SESSION['name']       = trim($user['first_name'] . ' ' . $user['last_name']);
    $_SESSION['employee_id']= $user['employee_id'];
    $_SESSION['expires_at'] = time() + (12 * 3600);

    auditLog('login', 'user', $user['id'], null, 'logged_in', 'User logged in', $user['id']);

    return ['success' => true, 'role' => $user['role']];
}

function logoutUser(): void {
    startSecureSession();
    if (!empty($_SESSION['user_id'])) {
        auditLog('logout', 'user', $_SESSION['user_id'], 'logged_in', 'logged_out', 'User logged out');
    }
    $_SESSION = [];
    session_destroy();
}

function auditLog(string $action, string $entityType, $entityId, $oldVal, $newVal, string $remarks = '', ?int $userId = null): void {
    try {
        $db = getDB();
        $uid = $userId ?? ($_SESSION['user_id'] ?? null);
        $db->prepare("INSERT INTO audit_log (action, entity_type, entity_id, old_value, new_value, performed_by, remarks, performed_at)
                      VALUES (?,?,?,?,?,?,?,NOW())")
           ->execute([
               $action, $entityType, $entityId,
               is_array($oldVal) ? json_encode($oldVal) : $oldVal,
               is_array($newVal) ? json_encode($newVal) : $newVal,
               $uid, $remarks
           ]);
    } catch (Exception $e) {
        // Silently fail audit — don't break main flow
    }
}

function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

function fullName(array $user): string {
    $parts = array_filter([
        $user['first_name'] ?? '',
        $user['middle_name'] ? ($user['middle_name'][0] . '.') : '',
        $user['last_name']  ?? '',
        $user['suffix']     ?? '',
    ]);
    return implode(' ', $parts);
}

function getInitials(string $name): string {
    $words = array_filter(explode(' ', $name));
    $initials = '';
    foreach (array_slice($words, 0, 2) as $w) $initials .= strtoupper($w[0]);
    return $initials ?: '?';
}

function h(mixed $val): string {
    return htmlspecialchars((string)($val ?? ''), ENT_QUOTES, 'UTF-8');
}

function statusBadge(string $status): string {
    global $STATUS_LABELS, $STATUS_COLORS;
    $label = $STATUS_LABELS[$status] ?? $status;
    $color = $STATUS_COLORS[$status] ?? 'secondary';
    return "<span class=\"badge bg-{$color}\">" . h($label) . "</span>";
}

function evBadge(string $status): string {
    global $EV_META;
    $meta  = $EV_META[$status] ?? ['label'=>$status,'dot'=>'⚪','cls'=>'secondary'];
    return "<span class=\"badge bg-{$meta['cls']}\">{$meta['dot']} " . h($meta['label']) . "</span>";
}

function formatFileSize(int $bytes): string {
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes/1024, 1) . ' KB';
    return round($bytes/1048576, 1) . ' MB';
}

function jsonResponse(mixed $data, int $code = 200): never {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
