/* ============================================
   ADMIN USERS PAGE - JavaScript
   ============================================ */

$(document).ready(function () {
    initAdminUsersPage();
});

function initAdminUsersPage() {
    // Permission selector change handler
    $('.project-permission').change(function () {
        const $select = $(this);
        const userId = $select.data('user-id');
        const permission = $select.val();

        // Add loading state
        $select.addClass('loading');

        $.post(window.baseUrl + 'admin/set_project_permission_ajax', {
            user_id: userId,
            permission: permission
        }, function (res) {
            $select.removeClass('loading');

            // Show appropriate message
            const message = res.success
                ? 'Permission updated successfully.'
                : (res.message || 'Failed to update permission');
            const alertClass = res.success ? 'alert-success' : 'alert-danger';
            const icon = res.success
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';

            const $flashMessage = $('#flash-message');
            $flashMessage.html(`
                <div class="alert ${alertClass}">
                    <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${icon}
                    </svg>
                    <span>${message}</span>
                </div>
            `).fadeIn(200);

            // Auto-hide after 4 seconds
            setTimeout(() => {
                $flashMessage.fadeOut(300, function () {
                    $(this).empty().show(); // Reset for next message
                });
            }, 4000);
        }, 'json').fail(function () {
            $select.removeClass('loading');
            $('#flash-message').html(`
                <div class="alert alert-danger">
                    <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>Network error. Please try again.</span>
                </div>
            `).fadeIn(200);

            // Auto-hide after 4 seconds
            setTimeout(() => {
                $('#flash-message').fadeOut(300, function () {
                    $(this).empty().show();
                });
            }, 4000);
        });
    });

    // Auto-hide initial flash messages
    setTimeout(function () {
        const $flash = $('#flash-message .alert');
        if ($flash.length) {
            $('#flash-message').fadeOut(300, function () {
                $(this).empty().show();
            });
        }
    }, 4000);
}

function confirmDelete() {
    return confirm('Are you sure you want to delete this user and all their projects/tasks? This action cannot be undone.');
}
