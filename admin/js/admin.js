<?php
/**
 * Admin JavaScript for Genuine Landscapers website
 * Handles admin interface interactions and functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('.admin-form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Check required fields
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                    
                    // Add error message if not exists
                    let errorMessage = field.parentNode.querySelector('.error-message');
                    if (!errorMessage) {
                        errorMessage = document.createElement('div');
                        errorMessage.className = 'error-message';
                        errorMessage.textContent = 'This field is required';
                        field.parentNode.appendChild(errorMessage);
                    }
                } else {
                    field.classList.remove('error');
                    const errorMessage = field.parentNode.querySelector('.error-message');
                    if (errorMessage) {
                        errorMessage.remove();
                    }
                }
            });
            
            // Validate email fields
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(function(field) {
                if (field.value.trim() && !isValidEmail(field.value)) {
                    isValid = false;
                    field.classList.add('error');
                    
                    // Add error message if not exists
                    let errorMessage = field.parentNode.querySelector('.error-message');
                    if (!errorMessage) {
                        errorMessage = document.createElement('div');
                        errorMessage.className = 'error-message';
                        errorMessage.textContent = 'Please enter a valid email address';
                        field.parentNode.appendChild(errorMessage);
                    } else {
                        errorMessage.textContent = 'Please enter a valid email address';
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
    
    // Input field focus effects
    const formInputs = document.querySelectorAll('.admin-form input, .admin-form textarea, .admin-form select');
    formInputs.forEach(function(input) {
        // Add focus effects
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('input-focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('input-focused');
            
            // Add 'filled' class if input has value
            if (this.value.trim() !== '') {
                this.parentElement.classList.add('input-filled');
            } else {
                this.parentElement.classList.remove('input-filled');
            }
        });
        
        // Check initial state
        if (input.value.trim() !== '') {
            input.parentElement.classList.add('input-filled');
        }
    });
    
    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn, button[data-action="delete"]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // Status change confirmation
    const statusSelects = document.querySelectorAll('select[name="status"]');
    statusSelects.forEach(function(select) {
        const originalValue = select.value;
        
        select.addEventListener('change', function() {
            const newValue = this.value;
            
            // Confirm status change for certain transitions
            if ((originalValue === 'pending' && (newValue === 'completed' || newValue === 'cancelled')) ||
                (originalValue === 'contacted' && newValue === 'cancelled')) {
                
                if (!confirm(`Are you sure you want to change the status from "${originalValue}" to "${newValue}"?`)) {
                    this.value = originalValue;
                }
            }
        });
    });
    
    // Close flash messages
    const closeButtons = document.querySelectorAll('.flash-message .close');
    closeButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const message = this.parentNode;
            message.classList.add('fade-out');
            setTimeout(function() {
                message.style.display = 'none';
            }, 500);
        });
    });
    
    // Slug generator for title fields
    const titleInputs = document.querySelectorAll('input[name="title"]');
    titleInputs.forEach(function(input) {
        const form = input.closest('form');
        const slugInput = form.querySelector('input[name="slug"]');
        
        if (slugInput) {
            input.addEventListener('blur', function() {
                // Only generate slug if slug field is empty
                if (!slugInput.value.trim()) {
                    slugInput.value = generateSlug(this.value);
                }
            });
        }
    });
    
    // Preview functionality for content
    const previewButtons = document.querySelectorAll('.preview-btn');
    previewButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = this.closest('form');
            const contentField = form.querySelector('textarea[name="content"]');
            const titleField = form.querySelector('input[name="title"]');
            
            if (contentField && titleField) {
                const previewModal = document.createElement('div');
                previewModal.className = 'preview-modal';
                
                const previewContent = document.createElement('div');
                previewContent.className = 'preview-content';
                
                const previewHeader = document.createElement('div');
                previewHeader.className = 'preview-header';
                previewHeader.innerHTML = `
                    <h2>Preview: ${titleField.value}</h2>
                    <button class="close-preview">&times;</button>
                `;
                
                const previewBody = document.createElement('div');
                previewBody.className = 'preview-body';
                previewBody.innerHTML = `
                    <h1>${titleField.value}</h1>
                    <div class="content">${contentField.value}</div>
                `;
                
                previewContent.appendChild(previewHeader);
                previewContent.appendChild(previewBody);
                previewModal.appendChild(previewContent);
                document.body.appendChild(previewModal);
                
                // Close preview
                const closeButton = previewModal.querySelector('.close-preview');
                closeButton.addEventListener('click', function() {
                    previewModal.remove();
                });
                
                // Close on click outside
                previewModal.addEventListener('click', function(e) {
                    if (e.target === previewModal) {
                        previewModal.remove();
                    }
                });
            }
        });
    });
    
    // Helper functions
    function isValidEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    
    function generateSlug(text) {
        return text
            .toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
    }
});
