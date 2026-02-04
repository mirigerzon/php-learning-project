/* ============================================
   GLOBAL APP.JS - Shared Functions
   ============================================ */

// ============================================
// DOCUMENT READY
// ============================================

$(document).ready(function () {
    // Initialize common behaviors
    initFlashMessages();
    initDropdowns();
    initDataTables();
});

// ============================================
// FLASH MESSAGES - Auto-hide after 4 seconds
// ============================================

function initFlashMessages() {
    var $flashMessage = $('#flash-message');
    if ($flashMessage.length) {
        setTimeout(function () {
            $flashMessage.fadeOut('slow');
        }, 4000);
    }
}

// ============================================
// DROPDOWNS - Mobile Actions
// ============================================

function initDropdowns() {
    // Toggle mobile dropdown menu
    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-menu')) {
            var dropdown = e.target.closest('.btn-menu').nextElementSibling;
            if (dropdown && dropdown.classList.contains('actions-dropdown')) {
                // Close all other dropdowns
                document.querySelectorAll('.actions-dropdown').forEach(d => {
                    if (d !== dropdown) d.style.display = 'none';
                });
                // Toggle current dropdown
                dropdown.style.display = (dropdown.style.display === 'flex' ? 'none' : 'flex');
            }
        } else if (!e.target.closest('.mobile-actions')) {
            // Close all dropdowns if clicking outside
            document.querySelectorAll('.actions-dropdown').forEach(d => d.style.display = 'none');
        }
    });
}

// Legacy function name for backwards compatibility
function toggleMenu(button) {
    const dropdown = button.nextElementSibling;
    // Close all other dropdowns
    document.querySelectorAll('.actions-dropdown').forEach(d => {
        if (d !== dropdown) d.style.display = 'none';
    });
    // Toggle current dropdown
    dropdown.style.display = (dropdown.style.display === 'flex' ? 'none' : 'flex');
}

// ============================================
// DATA TABLES - Initialization
// ============================================

function initDataTables() {
    // DataTables auto-initialization for any table with class 'datatable'
    if ($.fn.DataTable) {
        var tables = document.querySelectorAll('table.datatable, #shared-projects-table');
        tables.forEach(table => {
            if (!$.fn.DataTable.fnIsDataTable(table)) {
                $(table).DataTable({
                    responsive: true,
                    pageLength: 10
                });
            }
        });
    }
}

// ============================================
// CONFIRMATION DIALOGS
// ============================================

function confirmDelete(message = 'Are you sure?') {
    return confirm(message);
}

function confirmDeleteProject() {
    return confirm('Are you sure you want to delete this project? This action cannot be undone.');
}

function confirmDeleteUser() {
    return confirm('Are you sure you want to delete this user and all their projects/tasks? This action cannot be undone.');
}

function confirmDeleteTask() {
    return confirm('Are you sure?');
}

// ============================================
// FORM VALIDATION HELPERS
// ============================================

function isValidEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function isValidUsername(username) {
    return username && username.length >= 3;
}

function isValidPassword(password) {
    return password && password.length >= 6;
}

// ============================================
// ALERT MESSAGES
// ============================================

function showAlert(container, message, type = 'success') {
    var alertClass = `alert alert-${type}`;
    var alertHTML = `<div class="${alertClass}"><span>${message}</span></div>`;

    $(container).html(alertHTML);

    // Auto-hide after 4 seconds
    setTimeout(function () {
        $(container).fadeOut('slow', function () {
            $(this).empty();
        });
    }, 4000);
}

function showSuccessAlert(container, message) {
    showAlert(container, message, 'success');
}

function showErrorAlert(container, message) {
    showAlert(container, message, 'danger');
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

function formatDate(date) {
    var d = new Date(date);
    var month = '' + (d.getMonth() + 1);
    var day = '' + d.getDate();
    var year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [day, month, year].join('/');
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ============================================
// AJAX HELPERS
// ============================================

function ajaxGet(url, callback, errorCallback) {
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: callback,
        error: errorCallback || function (error) {
            console.error('AJAX Error:', error);
        }
    });
}

function ajaxPost(url, data, callback, errorCallback) {
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: callback,
        error: errorCallback || function (error) {
            console.error('AJAX Error:', error);
        }
    });
}

// ============================================
// LOCAL STORAGE HELPERS
// ============================================

function setLocalStorage(key, value) {
    try {
        localStorage.setItem(key, JSON.stringify(value));
    } catch (e) {
        console.error('localStorage error:', e);
    }
}

function getLocalStorage(key) {
    try {
        return JSON.parse(localStorage.getItem(key));
    } catch (e) {
        console.error('localStorage error:', e);
        return null;
    }
}

function removeLocalStorage(key) {
    try {
        localStorage.removeItem(key);
    } catch (e) {
        console.error('localStorage error:', e);
    }
}

// ============================================
// RESPONSIVE HELPERS
// ============================================

function isMobile() {
    return window.innerWidth <= 768;
}

function isTablet() {
    return window.innerWidth > 768 && window.innerWidth <= 1024;
}

function isDesktop() {
    return window.innerWidth > 1024;
}

// ============================================
// STRING UTILITIES
// ============================================

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function sanitizeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function (m) { return map[m]; });
}

// ============================================
// DOM HELPERS
// ============================================

function addClass(element, className) {
    if (element) {
        element.classList.add(className);
    }
}

function removeClass(element, className) {
    if (element) {
        element.classList.remove(className);
    }
}

function hasClass(element, className) {
    if (element) {
        return element.classList.contains(className);
    }
    return false;
}

function toggleClass(element, className) {
    if (element) {
        element.classList.toggle(className);
    }
}

// ============================================
// PERFORMANCE TRACKING
// ============================================

function logPageLoadTime() {
    if (window.performance) {
        var perfData = window.performance.timing;
        var pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
        console.log('Page Load Time: ' + pageLoadTime + 'ms');
    }
}
