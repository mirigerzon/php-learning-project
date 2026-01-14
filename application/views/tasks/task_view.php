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
            <h3><?= htmlspecialchars($task->task_title) ?></h3>
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
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>

                <!-- Upload -->
                <form action="<?= base_url('tasks/upload_images') ?>" method="post" class="task-action-form"
                    enctype="multipart/form-data">
                    <input type="hidden" name="task_id" value="<?= $task_id ?>">
                    <input type="file" name="images[]" class="form-control" multiple style="width:220px;">
                    <button type="submit" class="btn btn-success">Upload</button>
                </form>

                <p>Status:
                    <?= $task->status ? '<span class="badge bg-success">Done</span>' : '<span class="badge bg-warning text-dark">Pending</span>' ?>
                    <?= ($task->status == 0 && $task->due_date && $task->due_date < date('Y-m-d')) ? "<span class='text-danger'>(Overdue!)</span>" : "" ?>
                </p>
            </div>

            <div class="images-grid">
                <?php foreach ($task_images as $img): ?>
                    <div class="image-item">
                        <img src="<?= base_url($img->image_path) ?>"
                            onclick="openModal('<?= base_url($img->image_path) ?>')">

                        <form action="<?= base_url('tasks/delete_image') ?>" method="post">
                            <input type="hidden" name="image_id" value="<?= $img->id ?>">
                            <input type="hidden" name="task_id" value="<?= $task_id ?>">
                            <button type="submit" class="btn btn-sm btn-danger w-100 mt-1">
                                Delete
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
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

<script>
    function openModal(src) {
        var modalImg = document.getElementById('modalImage');
        modalImg.src = src;
        $('#imageModal').modal('show');
    }

    setTimeout(function () {
        var alertEl = document.querySelector('.alert');
        if (alertEl) {
            alertEl.classList.remove('show');
            alertEl.classList.add('fade');
        }
    }, 4000);
</script>

<style>
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
        width: 100%;
        height: 150px;
        object-fit: cover;
        cursor: pointer;
        border-radius: 6px;
    }
</style>