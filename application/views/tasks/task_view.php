<?php
$task_id = $task->task_id;
?>
<div class="container mt-4">
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>


    <a href="<?= base_url("tasks/index/{$task->project_id}") ?>" class="btn btn-secondary mb-3">← Back to Tasks</a>
    <div class="card">
        <div class="card-header">
            <h3>Task Name: <?= htmlspecialchars($task->task_title) ?></h3>
            <p>Status:
                <?= $task->status ? '<span class="badge bg-success">Done</span>' : '<span class="badge bg-warning text-dark">Pending</span>' ?>
                <?= ($task->status == 0 && $task->due_date && $task->due_date < date('Y-m-d')) ? "<span class='text-danger'>(Overdue!)</span>" : "" ?>
            </p>
        </div>

        <div class="card-body">
            <div class="task-actions">
                <!-- Due date -->
                <form method="post"
                    action="<?= base_url("tasks/update_due_date/{$task->project_id}/{$task->task_id}") ?>"
                    class="task-action-form">
                    <div>
                        <label class="form-label mb-0">Due date:</label>
                        <input type="date" name="due_date" class="form-control" value="<?= $task->due_date ?>"
                            style="width:200px;">
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                </form>

                <!-- Upload -->
                <form action="<?= base_url('tasks/upload_images') ?>" method="post" class="task-action-form"
                    enctype="multipart/form-data">
                    <input type="hidden" name="task_id" value="<?= $task_id ?>">
                    <input type="file" name="images[]" id="image-input" class="form-control" multiple
                        style="width:220px;">
                    <button type="submit" class="btn btn-success">Upload</button>
                </form>

                <div class="images-preview images-grid" id="images-preview"></div>

                <button class="btn btn-success btn-sm btn-edit-task" data-id="<?= $task->task_id ?>"
                    data-project="<?= $task->project_id ?>">
                    Edit Task details
                </button>
            </div>

            <div class="images-grid">
                <?php foreach ($task_images as $img): ?>
                    <div class="image-item">
                        <img src="<?= base_url($img->image_path) ?>"
                            onclick="openModal('<?= base_url($img->image_path) ?>')">

                        <form action="<?= base_url('tasks/delete_image') ?>" method="post">
                            <input type="hidden" name="image_id" value="<?= $img->id ?>">
                            <input type="hidden" name="task_id" value="<?= $task_id ?>">
                            <button type="submit" class="btn btn-sm btn-danger w-100 mt-1"
                                onclick="return confirm('Are you sure?')">
                                Delete
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal ל-preview של תמונות לפני העלאה -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:transparent;border:0;">
            <div class="modal-body text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <img id="previewModalImage" src="" class="img-fluid" style="max-height:80vh;">
            </div>
        </div>
    </div>
</div>


<!--- Modal for image viewing --->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:transparent;border:0;">
            <div class="modal-body text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <img id="modalImage" src="" class="img-responsive center-block" style="max-height:80vh;">
            </div>
        </div>
    </div>
</div>

<!-- Modal for editing task -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="edit-task-container">
            </div>
        </div>
    </div>
</div>

<script>
    const input = document.getElementById('image-input');
    const previewContainer = document.getElementById('images-preview');

    let filesArray = []; // נשמור כאן את הקבצים שנבחרו

    input.addEventListener('change', function () {
        // מוסיפים את הקבצים החדשים למערך הקיים
        Array.from(this.files).forEach(file => filesArray.push(file));
        renderPreview();
    });

    function renderPreview() {
        previewContainer.innerHTML = ''; // מנקה preview קודם

        filesArray.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cursor = 'pointer';

                // לחיצה על התמונה ב-preview -> פתיחת modal ב-Bootstrap 3
                img.addEventListener('click', () => {
                    $('#previewModalImage').attr('src', e.target.result); // מעדכן את התמונה במודל
                    $('#previewModal').modal('show'); // פותח את ה-modal
                });

                const div = document.createElement('div');
                div.classList.add('image-item');

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

        // מעדכן את input כך שיכלול רק את הקבצים שנותרו
        const dataTransfer = new DataTransfer();
        filesArray.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
    }

    // פתיחת Modal והטענת הטופס
    $(document).on('click', '.btn-edit-task', function (e) {
        e.preventDefault();
        let taskId = $(this).data('id');
        let projectId = $(this).data('project');

        $.get(`<?= base_url("tasks/edit_ajax_form/") ?>${projectId}/${taskId}`, function (html) {
            $('#edit-task-container').html(html);
            $('#editTaskModal').modal('show');
        });
    });

    // שליחת טופס העריכה ב-AJAX
    $(document).on('submit', '#edit-task-form', function (e) {
        e.preventDefault();
        const taskId = $(this).data('id');
        const formData = $(this).serialize();
        const actionUrl = $(this).attr('action');

        $.post(actionUrl, formData, function (response) {
            if (response.success) {
                $('#editTaskModal').modal('hide');
                location.reload();
            } else {
                alert(response.message);
            }
        }, 'json');
    });

    // כפתור Cancel
    $(document).on('click', '#cancel-edit-task', function () {
        $('#edit-task-container').html('');
        $('#editTaskModal').modal('hide');
    });

    setTimeout(function () {
        var alertEl = document.querySelector('.alert');
        if (alertEl) {
            alertEl.classList.remove('show');
            alertEl.classList.add('fade');
        }
    }, 4000);

    function openModal(src) {
        $('#modalImage').attr('src', src); // מעדכן את התמונה במודל
        $('#imageModal').modal('show');     // פותח את ה-modal
    }
</script>


<style>
    .images-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 15px;
    }

    .image-item {
        position: relative;
    }

    .image-item img {
        width: 100%;
        height: 150px;
        object-fit: contain;
        /* לא חותך */
        border-radius: 6px;
    }

    /* כפתור הסרה קטן בפינה */
    .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255, 0, 0, 0.8);
        color: white;
        border: none;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        font-weight: bold;
        cursor: pointer;
    }

    .card-header {
        display: flex;
        justify-content: left;
        align-items: baseline;
        gap: 15px;
    }

    .task-actions {
        display: flex;
        gap: 20px;
        align-items: flex-end;
        margin-bottom: 1rem;
        flex-wrap: nowrap;
    }

    .task-action-form {
        display: flex;
        gap: 10px;
        align-items: flex-end;
        background: #f8f9fa;
        padding: 10px 12px;
        border-radius: 8px;
    }

    .task-action-form .form-control {
        width: 200px;
    }

    .images-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 15px;
    }

    .image-item img {
        width: auto;
        height: 150px;
        object-fit: contain;
        cursor: pointer;
        border-radius: 6px;
    }
</style>