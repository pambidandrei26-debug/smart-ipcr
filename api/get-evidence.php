<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requireLogin();

$currentUserId = $_SESSION['user_id'];
$currentRole   = $_SESSION['role'];

header('Content-Type: application/json');

$siId   = (int)($_GET['si_id']   ?? 0);
$formId = (int)($_GET['form_id'] ?? 0);

if (!$siId || !$formId) {
    echo json_encode(['success'=>false,'message'=>'Missing params']); exit;
}

$db = getDB();

// Check if form is editable
$formRow = $db->prepare("SELECT status FROM ipcr_forms WHERE id=?");
$formRow->execute([$formId]);
$form = $formRow->fetch();
$isEditable = $form && in_array($form['status'], ['draft','returned']);

// Get evidence files
$rows = $db->prepare("SELECT * FROM evidence_files WHERE form_id=? AND si_id=? ORDER BY uploaded_at ASC");
$rows->execute([$formId, $siId]);
$files = $rows->fetchAll();

// Helper function for file icons
function getEvIcon($ext) {
    $ext = strtolower($ext);
    $map = [
        'pdf'  => ['bi-file-earmark-pdf-fill',  'ev-file-pdf'],
        'doc'  => ['bi-file-earmark-word-fill',  'ev-file-doc'],
        'docx' => ['bi-file-earmark-word-fill',  'ev-file-doc'],
        'xls'  => ['bi-file-earmark-excel-fill', 'ev-file-xls'],
        'xlsx' => ['bi-file-earmark-excel-fill', 'ev-file-xls'],
        'jpg'  => ['bi-file-earmark-image-fill', 'ev-file-img'],
        'jpeg' => ['bi-file-earmark-image-fill', 'ev-file-img'],
        'png'  => ['bi-file-earmark-image-fill', 'ev-file-img'],
        'gif'  => ['bi-file-earmark-image-fill', 'ev-file-img'],
        'webp' => ['bi-file-earmark-image-fill', 'ev-file-img'],
    ];
    return $map[$ext] ?? ['bi-file-earmark-fill', 'ev-file-other'];
}

$html = '';
if (empty($files)) {
    $html = '<div class="empty-ev"><i class="bi bi-folder2-open"></i>No files attached yet</div>';
} else {
    foreach ($files as $ev) {
        $ext  = strtolower(pathinfo($ev['original_name'], PATHINFO_EXTENSION));
        list($ico, $cls) = getEvIcon($ext);
        $size = $ev['file_size'] ? formatFileSize($ev['file_size']) : '';
        $date = date('M d, Y', strtotime($ev['uploaded_at']));
        $name = htmlspecialchars($ev['original_name'], ENT_QUOTES, 'UTF-8');
        $nameJs = addslashes($name);
        $fileType = htmlspecialchars($ev['file_type'] ?? '', ENT_QUOTES, 'UTF-8');
        $id   = $ev['id'];

        $html .= '<div class="ev-file-row" id="ev-item-' . $id . '">';
        $html .= '  <div class="ev-file-icon-sm ' . $cls . '"><i class="bi ' . $ico . '"></i></div>';
        $html .= '  <div class="ev-file-info-sm">';
        $html .= '    <div class="ev-fname" title="' . $name . '">' . $name . '</div>';
        $html .= '    <div class="ev-fmeta">' . ($size ? $size . ' · ' : '') . $date . '</div>';
        $html .= '  </div>';
        $html .= '  <div class="ev-actions">';
        $html .= '    <button onclick="previewFile(' . $id . ', \'' . $nameJs . '\', \'' . addslashes($fileType) . '\')" class="btn-dl">';
        $html .= '      <i class="bi bi-eye"></i> View';
        $html .= '    </button>';
        
        if ($isEditable) {
            $html .= '    <button class="ai-verify-btn btn-dl" data-file-id="' . $id . '" data-si-id="' . $siId . '" data-filename="' . $nameJs . '" style="border-color:#7c3aed;color:#7c3aed">';
            $html .= '      <i class="bi bi-stars"></i> AI';
            $html .= '    </button>';
            $html .= '    <button class="btn-rm" onclick="removeEvidence(' . $id . ', \'' . $nameJs . '\')" title="Remove">';
            $html .= '      <i class="bi bi-x-lg"></i>';
            $html .= '    </button>';
        }
        
        $html .= '  </div>';
        $html .= '</div>';
    }
}

echo json_encode(['success'=>true, 'html'=>$html, 'count'=>count($files)]);

