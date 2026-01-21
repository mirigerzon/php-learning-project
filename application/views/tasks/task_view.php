<?php
$task_id = $task->task_id;
?>
<div class="task-container">
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <a href="<?= base_url("tasks/index/{$task->project_id}") ?>" class="btn-back">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Tasks
    </a>

    <div class="task-card">
        <div class="task-header">
            <div class="task-title-section">
                <h1 class="task-title"><?= htmlspecialchars($task->task_title) ?></h1>
                <div class="task-meta">
                    <?php if ($task->status): ?>
                        <span class="status-badge status-done">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Completed
                        </span>
                    <?php else: ?>
                        <span class="status-badge status-pending">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Pending
                        </span>
                        <?php if ($task->due_date && $task->due_date < date('Y-m-d')): ?>
                            <span class="status-badge status-overdue">Overdue</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="task-body">
            <div class="actions-section">
                <div class="action-card">
                    <label class="action-label">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Due Date
                    </label>
                    <form method="post"
                        action="<?= base_url("tasks/update_due_date/{$task->project_id}/{$task->task_id}") ?>"
                        class="inline-form">
                        <input type="date" name="due_date" class="form-input" value="<?= $task->due_date ?>">
                        <button type="submit" class="btn-primary btn-sm">Update</button>
                    </form>
                </div>

                <div class="action-card">
                    <label class="action-label">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Upload Images
                    </label>
                    <form action="<?= base_url('tasks/upload_images') ?>" method="post" class="inline-form"
                        enctype="multipart/form-data">
                        <input type="hidden" name="task_id" value="<?= $task_id ?>">
                        <input type="file" name="images[]" id="image-input" class="form-input" multiple
                            accept="image/*">
                        <button type="submit" class="btn-primary btn-sm">Upload</button>
                    </form>
                </div>

                <button class="btn-secondary btn-edit-task" data-id="<?= $task->task_id ?>"
                    data-project="<?= $task->project_id ?>">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Edit Task Details
                </button>
            </div>

            <div class="images-preview" id="images-preview"></div>

            <?php if (!empty($task_images)): ?>
                <div class="images-section">
                    <h2 class="section-title">Attached Images</h2>
                    <div class="images-grid">
                        <?php foreach ($task_images as $img): ?>
                            <div class="image-card">
                                <div class="image-wrapper" onclick="openModal('<?= base_url($img->image_path) ?>')">
                                    <img src="<?= base_url($img->image_path) ?>" alt="Task image" loading="lazy">
                                    <div class="image-overlay">
                                        <svg width="24" height="24" fill="white" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                            <path fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <form action="<?= base_url('tasks/delete_image') ?>" method="post" class="delete-form">
                                    <input type="hidden" name="image_id" value="<?= $img->id ?>">
                                    <input type="hidden" name="task_id" value="<?= $task_id ?>">
                                    <button type="submit" class="btn-delete"
                                        onclick="return confirm('Are you sure you want to delete this image?')">
                                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal" id="imageModal" onclick="closeModal()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeModal()">&times;</button>
        <img id="modalImage" src="" alt="Full size image">
    </div>
</div>

<!-- Preview Modal -->
<div class="modal" id="previewModal" onclick="closePreviewModal()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closePreviewModal()">&times;</button>
        <img id="previewModalImage" src="" alt="Preview image">
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal" id="editTaskModal" onclick="closeEditModal()">
    <div class="modal-dialog" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h3>Edit Task</h3>
            <button class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <div class="modal-body" id="edit-task-container"></div>
    </div>
</div>

<style>
    * {
        box-sizing: border-box;
    }

    .task-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background: #f8f9fa;
        min-height: 100vh;
    }

    /* Alert */
    .alert {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        border-radius: 12px;
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        font-size: 0.95rem;
        animation: slideDown 0.3s ease;
    }

    .alert-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Back Button */
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.125rem;
        margin-bottom: 1.5rem;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        color: #374151;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .btn-back:hover {
        background: #f9fafb;
        border-color: #d1d5db;
        color: #111827;
        transform: translateX(-2px);
    }

    /* Task Card */
    .task-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .task-header {
        padding: 2rem;
        border-bottom: 1px solid #f3f4f6;
        background: linear-gradient(to bottom, #ffffff, #fafbfc);
    }

    .task-title {
        margin: 0 0 0.75rem 0;
        font-size: 1.75rem;
        font-weight: 600;
        color: #111827;
        letter-spacing: -0.025em;
    }

    .task-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        letter-spacing: 0.01em;
    }

    .status-done {
        background: #d1fae5;
        color: #065f46;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-overdue {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Task Body */
    .task-body {
        padding: 2rem;
    }

    /* Actions Section */
    .actions-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .action-card {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.25rem;
    }

    .action-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .inline-form {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .form-input {
        flex: 1;
        min-width: 150px;
        padding: 0.625rem 0.875rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.2s;
        background: white;
    }

    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-primary {
        padding: 0.625rem 1.25rem;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-primary:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }

    .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: white;
        color: #374151;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    /* Images Preview */
    .images-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .images-preview .image-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        background: #f3f4f6;
        border: 2px dashed #d1d5db;
    }

    .images-preview .image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .images-preview .image-item img:hover {
        transform: scale(1.05);
    }

    .remove-btn {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        width: 28px;
        height: 28px;
        background: rgba(239, 68, 68, 0.95);
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 1.25rem;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        z-index: 10;
    }

    .remove-btn:hover {
        background: #dc2626;
        transform: scale(1.1);
    }

    /* Images Section */
    .images-section {
        margin-top: 2rem;
    }

    .section-title {
        margin: 0 0 1.25rem 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
    }

    .images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.25rem;
    }

    .image-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.2s;
    }

    .image-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .image-wrapper {
        position: relative;
        aspect-ratio: 4/3;
        overflow: hidden;
        cursor: pointer;
        background: #f3f4f6;
    }

    .image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .image-wrapper:hover img {
        transform: scale(1.08);
    }

    .image-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .image-wrapper:hover .image-overlay {
        opacity: 1;
    }

    .delete-form {
        padding: 0.75rem;
    }

    .btn-delete {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        width: 100%;
        padding: 0.5rem;
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-delete:hover {
        background: #fecaca;
        border-color: #fca5a5;
    }

    /* Image & Preview Modal - refined */
    #imageModal .modal-content,
    #previewModal .modal-content {
        margin: auto;
        margin-top: 10vh;
        max-width: 80vw;
        max-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #imageModal img,
    #previewModal img {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
        border-radius: 14px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.55);
    }

    /* Edit Task Modal layout fix */
    #editTaskModal {
        display: none;
        align-items: center;
        justify-content: center;
    }

    #editTaskModal.show {
        display: flex;
    }

    #editTaskModal .modal-dialog {
        margin: auto;
        margin-top: 10vh;
        max-width: 720px;
        width: 100%;
        max-height: 85vh;
        border-radius: 18px;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        padding: 1rem;
        overflow-y: auto;
        backdrop-filter: blur(4px);
        animation: fadeIn 0.2s;
    }

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .modal-content {
        margin-top: 10vh;
        margin-left: 0;
        margin-right: 0;
        position: relative;
        max-width: 60vw;
        max-height: 60vh;
        animation: scaleIn 0.2s;
    }

    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .modal-content img {
        max-width: 100%;
        max-height: 90vh;
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
    }

    .modal-dialog {
        background: white;
        border-radius: 16px;
        max-width: 800px;
        width: 100%;
        max-height: 90vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        animation: scaleIn 0.2s;
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
    }

    .modal-body {
        padding: 2rem;
        overflow-y: auto;
    }

    .modal-close {
        width: 36px;
        height: 36px;
        background: rgba(0, 0, 0, 0.1);
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 1.5rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .modal-close:hover {
        background: rgba(0, 0, 0, 0.2);
        transform: rotate(90deg);
    }

    .modal-dialog .modal-close {
        color: #6b7280;
        background: #f3f4f6;
    }

    .modal-dialog .modal-close:hover {
        background: #e5e7eb;
        color: #111827;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .images-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .task-container {
            padding: 1rem;
        }

        .task-header {
            padding: 1.5rem;
        }

        .task-title {
            font-size: 1.5rem;
        }

        .task-body {
            padding: 1.5rem;
        }

        .actions-section {
            grid-template-columns: 1fr;
        }

        .images-grid {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 1rem;
        }

        .modal-header,
        .modal-body {
            padding: 1.25rem;
        }
    }

    @media (max-width: 480px) {
        .task-container {
            padding: 0.75rem;
        }

        .task-header,
        .task-body {
            padding: 1.25rem;
        }

        .task-title {
            font-size: 1.25rem;
        }

        .images-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .images-preview {
            grid-template-columns: repeat(2, 1fr);
        }

        .inline-form {
            flex-direction: column;
        }

        .form-input,
        .btn-primary {
            width: 100%;
        }
    }
</style>

<script>
    const input = document.getElementById('image-input');
    const previewContainer = document.getElementById('images-preview');
    let filesArray = [];

    input.addEventListener('change', function () {
        Array.from(this.files).forEach(file => filesArray.push(file));
        renderPreview();
    });

    function renderPreview() {
        previewContainer.innerHTML = '';
        filesArray.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const div = document.createElement('div');
                div.classList.add('image-item');

                const img = document.createElement('img');
                img.src = e.target.result;
                img.addEventListener('click', () => {
                    document.getElementById('previewModalImage').src = e.target.result;
                    document.getElementById('previewModal').classList.add('show');
                });

                const btn = document.createElement('button');
                btn.classList.add('remove-btn');
                btn.innerHTML = '&times;';
                btn.addEventListener('click', () => {
                    filesArray.splice(index, 1);
                    renderPreview();
                });

                div.appendChild(img);
                div.appendChild(btn);
                previewContainer.appendChild(div);
            }
            reader.readAsDataURL(file);
        });

        const dataTransfer = new DataTransfer();
        filesArray.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
    }

    // Edit Task AJAX
    $(document).on('click', '.btn-edit-task', function (e) {
        e.preventDefault();

        let taskId = $(this).data('id');
        let projectId = $(this).data('project');

        $.get(`<?= base_url("tasks/edit_ajax_form/") ?>${projectId}/${taskId}`, function (html) {
            $('#edit-task-container').html(html);
            $('#editTaskModal').addClass('show'); // ← תיקון
        });
    });

    $(document).on('submit', '#edit-task-form', function (e) {
        e.preventDefault();
        const actionUrl = $(this).attr('action');
        $.post(actionUrl, $(this).serialize(), function (response) {
            if (response.success) {
                closeEditModal();
                location.reload();
            } else {
                alert(response.message);
            }
        }, 'json');
    });

    $(document).on('click', '#cancel-edit-task', function () {
        closeEditModal();
    });

    function openModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.add('show');
    }

    function closeModal() {
        document.getElementById('imageModal').classList.remove('show');
    }

    function closePreviewModal() {
        document.getElementById('previewModal').classList.remove('show');
    }

    function closeEditModal() {
        $('#edit-task-container').html('');
        $('#editTaskModal').removeClass('show');
    }


    // Auto-hide alert
    setTimeout(function () {
        const alertEl = document.querySelector('.alert');
        if (alertEl) {
            alertEl.style.opacity = '0';
            alertEl.style.transform = 'translateY(-10px)';
            setTimeout(() => alertEl.remove(), 300);
        }
    }, 4000);
</script>