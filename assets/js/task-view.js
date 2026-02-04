/**
 * Task View JavaScript Module
 * Handles task image previews, modals, and editing
 */

const input = document.getElementById('image-input');
const previewContainer = document.getElementById('images-preview');
let filesArray = [];

if (input) {
    input.addEventListener('change', function () {
        Array.from(this.files).forEach(file => filesArray.push(file));
        renderPreview();
    });
}

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

    $.get(`${window.baseUrl}tasks/edit_ajax_form/${projectId}/${taskId}`, function (html) {
        $('#edit-task-container').html(html);
        $('#editTaskModal').addClass('show');
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
