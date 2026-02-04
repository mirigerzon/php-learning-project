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


    <?php
    $from = $_GET['from'] ?? 'projects';
    $back_url = '';
    $back_text = 'Back to Tasks';

    if ($from === 'shares') {
        $back_url = base_url('tasks/index/' . $task->project_id . '?from=' . $from);
    } elseif ($from === 'admin') {
        $back_url = base_url('tasks/index/' . $task->project_id . '?from=' . $from);
    } else {
        $back_url = base_url('tasks/index/' . $task->project_id . '?from=' . $from);
    }
    ?>
    <a href="<?= $back_url ?>" class="btn-back">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        <?= $back_text ?>
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

<script src="<?= base_url('assets/js/task-view.js') ?>"></script>