  </main><!-- /page-content -->
</div><!-- /main-wrapper -->
</div><!-- /app-shell -->
<div id="toast-container"></div>

<!-- ═══════════════════════════════════════════════════════════
     GLOBAL MODAL SYSTEM - Consistent Design
     ═══════════════════════════════════════════════════════════ -->

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
      <div class="modal-header" style="background:linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);border:none;padding:24px 28px 20px">
        <h5 class="modal-title d-flex align-items-center gap-3 text-white w-100">
          <div style="width:48px;height:48px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(10px)">
            <i class="bi bi-question-circle-fill" style="font-size:24px"></i>
          </div>
          <span id="confirmModalTitle" style="font-weight:700;font-size:1.15rem;letter-spacing:-0.02em">Confirm Action</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="opacity:0.8"></button>
      </div>
      <div class="modal-body" style="padding:32px 28px">
        <p id="confirmModalMessage" class="mb-0" style="color:#374151;line-height:1.7;font-size:0.95rem"></p>
      </div>
      <div class="modal-footer" style="border:none;background:#f9fafb;padding:20px 28px">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="padding:10px 24px;font-weight:600;border-radius:8px">
          <i class="bi bi-x-lg me-2"></i>Cancel
        </button>
        <button type="button" class="btn btn-danger" id="confirmModalBtn" style="padding:10px 28px;font-weight:700;border-radius:8px;box-shadow:0 4px 12px rgba(220,38,38,0.25)">
          <i class="bi bi-check-lg me-2"></i>Confirm
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Prompt Modal (for text input) -->
<div class="modal fade" id="promptModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
      <div class="modal-header" style="background:linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);border:none;padding:24px 28px 20px">
        <h5 class="modal-title d-flex align-items-center gap-3 text-white w-100">
          <div style="width:48px;height:48px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(10px)">
            <i class="bi bi-pencil-square" style="font-size:22px"></i>
          </div>
          <span id="promptModalTitle" style="font-weight:700;font-size:1.15rem;letter-spacing:-0.02em">Enter Information</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="opacity:0.8"></button>
      </div>
      <div class="modal-body" style="padding:32px 28px">
        <p id="promptModalMessage" class="mb-3" style="color:#374151;line-height:1.7;font-size:0.95rem"></p>
        <input type="text" class="form-control" id="promptModalInput" placeholder="Enter value..." style="padding:12px 16px;border-radius:8px;border:2px solid #e5e7eb;font-size:0.95rem">
        <div id="promptModalError" class="text-danger mt-3" style="font-size:0.875rem;display:none;padding:10px;background:#fee;border-radius:6px;border-left:3px solid #dc2626"></div>
      </div>
      <div class="modal-footer" style="border:none;background:#f9fafb;padding:20px 28px">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="padding:10px 24px;font-weight:600;border-radius:8px">
          <i class="bi bi-x-lg me-2"></i>Cancel
        </button>
        <button type="button" class="btn btn-primary" id="promptModalBtn" style="padding:10px 28px;font-weight:700;border-radius:8px;box-shadow:0 4px 12px rgba(26,92,42,0.25)">
          <i class="bi bi-check-lg me-2"></i>Submit
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Alert Modal (for notifications) -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
      <div class="modal-header" style="background:linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);border:none;padding:24px 28px 20px">
        <h5 class="modal-title d-flex align-items-center gap-3 text-white w-100">
          <div style="width:48px;height:48px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(10px)">
            <i id="alertModalIcon" class="bi bi-info-circle-fill" style="font-size:24px"></i>
          </div>
          <span id="alertModalTitle" style="font-weight:700;font-size:1.15rem;letter-spacing:-0.02em">Notice</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="opacity:0.8"></button>
      </div>
      <div class="modal-body" style="padding:32px 28px">
        <p id="alertModalMessage" class="mb-0" style="color:#374151;line-height:1.7;font-size:0.95rem"></p>
      </div>
      <div class="modal-footer" style="border:none;background:#f9fafb;padding:20px 28px">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" style="padding:10px 32px;font-weight:700;border-radius:8px;box-shadow:0 4px 12px rgba(37,99,235,0.25)">
          <i class="bi bi-check-lg me-2"></i>OK
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Password Change Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
      <div class="modal-header" style="background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%);border:none;padding:24px 28px 20px">
        <h5 class="modal-title d-flex align-items-center gap-3 text-white w-100">
          <div style="width:48px;height:48px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(10px)">
            <i class="bi bi-key-fill" style="font-size:22px"></i>
          </div>
          <span style="font-weight:700;font-size:1.15rem;letter-spacing:-0.02em">Change Password</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="opacity:0.8"></button>
      </div>
      <div class="modal-body" style="padding:32px 28px">
        <div class="mb-4" style="background:#f9fafb;padding:12px 16px;border-radius:8px;border-left:3px solid #f59e0b">
          <div style="font-size:0.85rem;color:#6b7280;margin-bottom:4px">
            <i class="bi bi-person me-1"></i>User Account
          </div>
          <div style="font-weight:700;color:#1f2937" id="passwordModalUser"></div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold" style="color:#374151;margin-bottom:8px">New Password</label>
          <div class="input-group">
            <input type="password" class="form-control" id="passwordModalInput" placeholder="Enter new password" minlength="6" style="padding:12px 16px;border-radius:8px 0 0 8px;border:2px solid #e5e7eb;font-size:0.95rem">
            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('passwordModalInput', this)" style="border:2px solid #e5e7eb;border-left:none;border-radius:0 8px 8px 0;padding:0 16px">
              <i class="bi bi-eye"></i>
            </button>
          </div>
          <div class="form-text" style="margin-top:8px;color:#6b7280;font-size:0.85rem">
            <i class="bi bi-info-circle me-1"></i>Minimum 6 characters required
          </div>
        </div>
        <div id="passwordModalError" class="alert alert-danger" style="font-size:0.875rem;display:none;border-radius:8px;border-left:3px solid #dc2626"></div>
      </div>
      <div class="modal-footer" style="border:none;background:#f9fafb;padding:20px 28px">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="padding:10px 24px;font-weight:600;border-radius:8px">
          <i class="bi bi-x-lg me-2"></i>Cancel
        </button>
        <button type="button" class="btn btn-warning" id="passwordModalBtn" style="padding:10px 28px;font-weight:700;border-radius:8px;box-shadow:0 4px 12px rgba(245,158,11,0.25);color:#fff">
          <i class="bi bi-key me-2"></i>Change Password
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ── GLOBAL FILE PREVIEW MODAL ── -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:90vw">
    <div class="modal-content" style="height:90vh;display:flex;flex-direction:column">
      <div class="modal-header" style="flex-shrink:0">
        <h6 class="modal-title" id="preview-filename">
          <i class="bi bi-file-earmark me-2"></i>File Preview
        </h6>
        <div class="d-flex gap-2 align-items-center ms-auto">
          <a id="preview-download-btn" href="#" target="_blank"
             class="btn btn-sm btn-outline-light"
             style="font-size:.75rem">
            <i class="bi bi-download me-1"></i>Download
          </a>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body preview-modal-body" id="preview-modal-body">
        <div class="preview-unsupported">
          <i class="bi bi-file-earmark"></i>
          <span>Loading...</span>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= BASE_URL ?>/assets/js/main.js?v=<?= time() ?>"></script>
<script>
// ── Global File Preview ──────────────────────────────────────
function previewFile(fileId, fileName, fileType) {
  const url   = '<?= BASE_URL ?>/api/download-evidence.php?id=' + fileId;
  const modal = document.getElementById('filePreviewModal');
  const body  = document.getElementById('preview-modal-body');
  const title = document.getElementById('preview-filename');
  const dlBtn = document.getElementById('preview-download-btn');

  title.innerHTML = '<i class="bi bi-file-earmark me-2"></i>' + fileName;
  dlBtn.href = url;

  const ext      = fileName.split('.').pop().toLowerCase();
  const imgExts  = ['jpg','jpeg','png','gif','webp','bmp','svg'];
  const pdfExts  = ['pdf'];

  if (imgExts.includes(ext)) {
    body.innerHTML = `
      <div style="overflow:auto;height:100%;display:flex;align-items:center;justify-content:center;background:#1a1a1a;padding:12px">
        <img src="${url}" style="max-width:100%;max-height:100%;object-fit:contain;border-radius:4px;box-shadow:0 4px 24px rgba(0,0,0,.5)" alt="${fileName}">
      </div>`;

  } else if (pdfExts.includes(ext)) {
    body.innerHTML = `<iframe src="${url}" style="width:100%;height:100%;border:none;" title="${fileName}"></iframe>`;

  } else {
    // Word, Excel, and other files — show preview unavailable + download
    const icons = {
      doc:'bi-file-earmark-word-fill', docx:'bi-file-earmark-word-fill',
      xls:'bi-file-earmark-excel-fill', xlsx:'bi-file-earmark-excel-fill',
      ppt:'bi-file-earmark-ppt-fill', pptx:'bi-file-earmark-ppt-fill',
    };
    const colors = {
      doc:'#2563eb', docx:'#2563eb',
      xls:'#16a34a', xlsx:'#16a34a',
      ppt:'#dc2626', pptx:'#dc2626',
    };
    const icon  = icons[ext]  || 'bi-file-earmark-fill';
    const color = colors[ext] || '#6b7280';

    body.innerHTML = `
      <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:16px;padding:40px;text-align:center">
        <i class="bi ${icon}" style="font-size:5rem;color:${color}"></i>
        <div style="font-size:1.1rem;font-weight:700;color:#1e2d22">${fileName}</div>
        <div style="font-size:.88rem;color:#6a8f72;max-width:400px;line-height:1.6">
          <strong>.${ext.toUpperCase()}</strong> files cannot be previewed directly in the browser.<br><br>
          <span style="color:#dc2626;font-weight:600">⚠ Ask faculty to re-upload as PDF for instant preview.</span>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin-top:8px">
          <a href="${url}" target="_blank"
             style="background:#1a5c2a;color:#fff;padding:10px 24px;border-radius:8px;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:7px;font-size:.88rem">
            <i class="bi bi-box-arrow-up-right"></i> Open in New Tab
          </a>
          <a href="${url}" download="${fileName}"
             style="background:#fff;color:#1a5c2a;padding:10px 24px;border-radius:8px;font-weight:700;text-decoration:none;border:2px solid #1a5c2a;display:flex;align-items:center;gap:7px;font-size:.88rem">
            <i class="bi bi-download"></i> Download File
          </a>
        </div>
      </div>`;
  }

  new bootstrap.Modal(modal).show();

  // Clear on close to stop any loading
  modal.addEventListener('hidden.bs.modal', () => {
    body.innerHTML = '';
  }, { once: true });
}
</script>

</body>
</html>
