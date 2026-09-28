/**
 * BCA Student Management System - Client Side JavaScript
 * Enhances user experience, client-side confirmations, and image previews.
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Automatically dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 5000);
    });

    // 2. Real-time Image Upload Preview
    const photoInput = document.getElementById('photoInput');
    const imagePreview = document.getElementById('imagePreviewBox');

    if (photoInput && imagePreview) {
        photoInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                // Ensure file is an image
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.style.display = 'none';
                    alert('Please select an image file (JPG, PNG, or WEBP).');
                    photoInput.value = '';
                }
            }
        });
    }

    // 3. Confirm Delete Prompts
    const deleteForms = document.querySelectorAll('.confirm-delete-form');
    deleteForms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const studentName = form.getAttribute('data-student-name') || 'this student';
            const confirmed = confirm(`Are you sure you want to permanently delete ${studentName}? This action cannot be undone.`);
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });
});
