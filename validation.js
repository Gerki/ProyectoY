// Form validation functions
document.addEventListener('DOMContentLoaded', function() {
    // Registration form validation
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            let isValid = true;
            let errorMessage = '';
            
            // Clear previous error messages
            clearErrorMessages();
            
            // Validate name
            if (name === '') {
                errorMessage = 'Name is required';
                showError('name', errorMessage);
                isValid = false;
            }
            
            // Validate email
            if (email === '') {
                errorMessage = 'Email is required';
                showError('email', errorMessage);
                isValid = false;
            } else if (!isValidEmail(email)) {
                errorMessage = 'Please enter a valid email address';
                showError('email', errorMessage);
                isValid = false;
            }
            
            // Validate password
            if (password === '') {
                errorMessage = 'Password is required';
                showError('password', errorMessage);
                isValid = false;
            } else if (password.length < 6) {
                errorMessage = 'Password must be at least 6 characters long';
                showError('password', errorMessage);
                isValid = false;
            }
            
            // Validate confirm password
            if (confirmPassword === '') {
                errorMessage = 'Please confirm your password';
                showError('confirm_password', errorMessage);
                isValid = false;
            } else if (password !== confirmPassword) {
                errorMessage = 'Passwords do not match';
                showError('confirm_password', errorMessage);
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Login form validation
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            let isValid = true;
            let errorMessage = '';
            
            // Clear previous error messages
            clearErrorMessages();
            
            // Validate email
            if (email === '') {
                errorMessage = 'Email is required';
                showError('email', errorMessage);
                isValid = false;
            } else if (!isValidEmail(email)) {
                errorMessage = 'Please enter a valid email address';
                showError('email', errorMessage);
                isValid = false;
            }
            
            // Validate password
            if (password === '') {
                errorMessage = 'Password is required';
                showError('password', errorMessage);
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Profile form validation
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            let isValid = true;
            let errorMessage = '';
            
            // Clear previous error messages
            clearErrorMessages();
            
            // Validate name
            if (name === '') {
                errorMessage = 'Name is required';
                showError('name', errorMessage);
                isValid = false;
            }
            
            // Password validation only if the user is changing password
            if (currentPassword !== '' || newPassword !== '' || confirmPassword !== '') {
                if (currentPassword === '') {
                    errorMessage = 'Current password is required';
                    showError('current_password', errorMessage);
                    isValid = false;
                }
                
                if (newPassword === '') {
                    errorMessage = 'New password is required';
                    showError('new_password', errorMessage);
                    isValid = false;
                } else if (newPassword.length < 6) {
                    errorMessage = 'New password must be at least 6 characters long';
                    showError('new_password', errorMessage);
                    isValid = false;
                }
                
                if (confirmPassword === '') {
                    errorMessage = 'Please confirm your new password';
                    showError('confirm_password', errorMessage);
                    isValid = false;
                } else if (newPassword !== confirmPassword) {
                    errorMessage = 'New passwords do not match';
                    showError('confirm_password', errorMessage);
                    isValid = false;
                }
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});

// Helper functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showError(inputId, message) {
    const input = document.getElementById(inputId);
    const errorElement = document.createElement('div');
    errorElement.className = 'validation-error';
    errorElement.innerHTML = message;
    input.parentNode.appendChild(errorElement);
    input.classList.add('error-input');
}

function clearErrorMessages() {
    const errorMessages = document.querySelectorAll('.validation-error');
    const errorInputs = document.querySelectorAll('.error-input');
    
    errorMessages.forEach(function(element) {
        element.remove();
    });
    
    errorInputs.forEach(function(element) {
        element.classList.remove('error-input');
    });
} 