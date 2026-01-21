<div class="projects-container">
    <div class="projects-header">
        <h2 class="projects-title">All Projects</h2>
        <p class="projects-subtitle">Overview of all your projects and shared workspaces</p>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success soft-alert">
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($projects)): ?>
        <div class="alert alert-info soft-alert">
            No projects found.
        </div>
    <?php else: ?>
        <div class="projects-card">
            <div class="table-wrapper">
                <table class="projects-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Owner</th>
                            <th>Created</th>
                            <th>Shared</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $p): ?>
                            <tr>
                                <td data-label="ID"><?= (int) $p->project_id ?></td>

                                <td data-label="Title">
                                    <strong class="project-title">
                                        <?= htmlspecialchars($p->project_title) ?>
                                    </strong>
                                </td>

                                <td data-label="Owner">
                                    <?= htmlspecialchars(
                                        $p->owner_name == $this->session->userdata('username')
                                        ? 'You'
                                        : ($p->owner_name ?? '-')
                                    ) ?>
                                </td>

                                <td data-label="Created">
                                    <?= date('d/m/Y', strtotime($p->created_at)) ?>
                                </td>

                                <td data-label="Shared with">
                                    <span class="shared-badge">
                                        <?= !empty($p->shared_users) ? count($p->shared_users) : 0 ?>
                                    </span>
                                </td>

                                <td data-label="Actions">
                                    <div class="action-group">
                                        <a href="<?= base_url('tasks/index/' . $p->project_id) ?>" class="btn-action btn-view">
                                            View
                                        </a>

                                        <?php if ($this->session->userdata('project_permission') === 'edit'): ?>
                                            <a href="<?= base_url('projects/edit/' . $p->project_id) ?>"
                                                class="btn-action btn-edit">
                                                Edit
                                            </a>

                                            <button class="btn-action btn-share share-project"
                                                data-id="<?= (int) $p->project_id ?>">
                                                Share
                                            </button>

                                            <form method="post" action="<?= base_url('projects/delete/' . $p->project_id) ?>"
                                                onsubmit="return confirmDelete();">
                                                <button type="submit" class="btn-action btn-delete">
                                                    Delete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .projects-container {
        max-width: 1200px;
        margin: auto;
        padding: 1.5rem;
        font-family: system-ui, -apple-system, BlinkMacSystemFont;
        background: #f9fafb;
        min-height: 100vh;
    }

    .projects-header {
        margin-bottom: 1.5rem;
    }

    .projects-title {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        color: #111827;
    }

    .projects-subtitle {
        margin-top: 0.25rem;
        color: #6b7280;
        font-size: 0.95rem;
    }

    .soft-alert {
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }

    .projects-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .projects-table {
        width: 100%;
        border-collapse: collapse;
    }

    .projects-table thead {
        background: #f3f4f6;
    }

    .projects-table th {
        padding: 1rem;
        text-align: left;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #374151;
    }

    .projects-table td {
        padding: 1rem;
        font-size: 1.05rem;
        color: #1f2937;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .projects-table tbody tr:hover {
        background: #f9fafb;
    }

    .project-title {
        color: #111827;
        font-weight: 600;
        font-size: 1.05rem;
    }

    .shared-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
        background: #e0e7ff;
        color: #3730a3;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Actions */
    .action-group {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 0.35rem 0.7rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        border: 1px solid;
        background: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-view {
        border-color: #d1d5db;
        color: #374151;
    }

    .btn-edit {
        border-color: #93c5fd;
        color: #1e40af;
    }

    .btn-share {
        border-color: #fde68a;
        color: #92400e;
    }

    .btn-delete {
        border-color: #fecaca;
        color: #991b1b;
    }

    .btn-action:hover {
        background: #f3f4f6;
    }

    /* 📱 Mobile Card Layout */
    @media (max-width: 640px) {

        .projects-container {
            padding: 0.75rem;
        }

        .projects-table {
            font-size: 0.9rem;
            min-width: 720px;
            /* מאפשר גלילה */
        }

        .projects-table th,
        .projects-table td {
            padding: 0.6rem 0.75rem;
            white-space: nowrap;
        }

        .projects-table th {
            font-size: 0.75rem;
        }

        .action-group {
            flex-wrap: nowrap;
            gap: 0.35rem;
        }

        .btn-action {
            font-size: 0.8rem;
            padding: 0.35rem 0.55rem;
        }

        .projects-title {
            font-size: 1.5rem;
        }

        .projects-subtitle {
            font-size: 0.95rem;
        }
    }
</style>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this project? This action cannot be undone.');
    }
</script>