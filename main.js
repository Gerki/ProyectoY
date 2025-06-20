// Dashboard JavaScript for Visualgv.com

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all dashboard functionality
    initNavigation();
    initModuleSwitching();
    initSidebarToggle();
    initModal();
    initFileUpload();
    initChat();
    initToast();
    initFormValidation();
    initSearch();
});

// Navigation and Module Switching
function initNavigation() {
    const navLinks = document.querySelectorAll('.nav-link[data-module]');
    const modules = document.querySelectorAll('.dashboard-module');
    const currentModuleName = document.getElementById('currentModuleName');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links and modules
            navLinks.forEach(l => l.classList.remove('active'));
            modules.forEach(m => m.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Show corresponding module
            const moduleId = this.getAttribute('data-module');
            const targetModule = document.getElementById(`module-${moduleId}`);
            if (targetModule) {
                targetModule.classList.add('active');
                currentModuleName.textContent = this.querySelector('span').textContent;
            }
        });
    });

    // Handle submenu toggles
    const submenuToggles = document.querySelectorAll('.has-submenu > .nav-link');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.parentElement;
            parent.classList.toggle('open');
        });
    });
}

// Sidebar Toggle for Mobile
function initSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
    }
}

// Module Creation Modal
function initModal() {
    const addModuleBtn = document.getElementById('addModuleBtn');
    const moduleModal = document.getElementById('moduleModal');
    const modalClose = document.getElementById('modalClose');
    const cancelModule = document.getElementById('cancelModule');
    const moduleForm = document.getElementById('moduleForm');

    if (addModuleBtn && moduleModal) {
        addModuleBtn.addEventListener('click', function() {
            moduleModal.classList.add('active');
        });
    }

    if (modalClose && moduleModal) {
        modalClose.addEventListener('click', function() {
            moduleModal.classList.remove('active');
        });
    }

    if (cancelModule && moduleModal) {
        cancelModule.addEventListener('click', function() {
            moduleModal.classList.remove('active');
        });
    }

    // Close modal when clicking outside
    if (moduleModal) {
        moduleModal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    }

    // Handle module form submission
    if (moduleForm) {
        moduleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const moduleName = document.getElementById('moduleName').value;
            const moduleDescription = document.getElementById('moduleDescription').value;
            const moduleIcon = document.getElementById('moduleIcon').value;
            
            // Here you would typically send this data to your backend
            console.log('Creating module:', { moduleName, moduleDescription, moduleIcon });
            
            // Show success message
            showToast('Módulo creado exitosamente', 'success');
            
            // Close modal and reset form
            moduleModal.classList.remove('active');
            moduleForm.reset();
        });
    }
}

// File Upload Functionality
function initFileUpload() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const browseBtn = document.getElementById('browseBtn');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressFill = document.querySelector('.progress-fill');
    const progressText = document.querySelector('.progress-text');

    if (browseBtn && fileInput) {
        browseBtn.addEventListener('click', function() {
            fileInput.click();
        });
    }

    if (uploadArea && fileInput) {
        // Drag and drop functionality
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            const files = e.dataTransfer.files;
            handleFiles(files);
        });

        uploadArea.addEventListener('click', function() {
            fileInput.click();
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
    }

    function handleFiles(files) {
        if (files.length === 0) return;

        // Show progress
        uploadProgress.style.display = 'block';
        
        // Simulate upload progress
        let progress = 0;
        const interval = setInterval(() => {
            progress += 10;
            progressFill.style.width = progress + '%';
            progressText.textContent = progress + '%';
            
            if (progress >= 100) {
                clearInterval(interval);
                setTimeout(() => {
                    uploadProgress.style.display = 'none';
                    progressFill.style.width = '0%';
                    showToast('Archivos subidos exitosamente', 'success');
                }, 500);
            }
        }, 200);

        // Here you would typically upload files to your server
        console.log('Files to upload:', files);
    }
}

// Chat Functionality
function initChat() {
    const chatInput = document.getElementById('chatInput');
    const sendMessage = document.getElementById('sendMessage');
    const chatMessages = document.getElementById('chatMessages');

    function addMessage(content, isOwn = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isOwn ? 'message-own' : 'message-other'}`;
        
        const now = new Date();
        const time = now.getHours().toString().padStart(2, '0') + ':' + 
                    now.getMinutes().toString().padStart(2, '0');
        
        messageDiv.innerHTML = `
            <div class="message-content">${content}</div>
            <div class="message-time">${time}</div>
        `;
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function sendChatMessage() {
        const message = chatInput.value.trim();
        if (message) {
            addMessage(message, true);
            chatInput.value = '';
            
            // Simulate response
            setTimeout(() => {
                addMessage('Mensaje recibido. Gracias por tu comentario.');
            }, 1000);
        }
    }

    if (sendMessage && chatInput) {
        sendMessage.addEventListener('click', sendChatMessage);
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendChatMessage();
            }
        });
    }
}

// Toast Notifications
function initToast() {
    window.showToast = function(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        const icon = getToastIcon(type);
        
        toast.innerHTML = `
            <i class="${icon}"></i>
            <span>${message}</span>
        `;
        
        toastContainer.appendChild(toast);
        
        // Remove toast after 5 seconds
        setTimeout(() => {
            toast.remove();
        }, 5000);
    };

    function getToastIcon(type) {
        switch (type) {
            case 'success': return 'fas fa-check-circle';
            case 'error': return 'fas fa-exclamation-circle';
            case 'warning': return 'fas fa-exclamation-triangle';
            default: return 'fas fa-info-circle';
        }
    }
}

// Form Validation
function initFormValidation() {
    const forms = document.querySelectorAll('.enhanced-form');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });
            
            if (isValid) {
                // Handle form submission
                const formData = new FormData(form);
                console.log('Form data:', Object.fromEntries(formData));
                showToast('Formulario enviado exitosamente', 'success');
                form.reset();
            }
        });
    });
}

function validateField(field) {
    const feedback = field.parentElement.querySelector('.input-feedback');
    
    // Clear previous errors
    clearFieldError(field);
    
    // Basic validation
    if (field.hasAttribute('required') && !field.value.trim()) {
        showFieldError(field, 'Este campo es requerido');
        return false;
    }
    
    if (field.type === 'email' && field.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(field.value)) {
            showFieldError(field, 'Email inválido');
            return false;
        }
    }
    
    if (field.type === 'url' && field.value) {
        try {
            new URL(field.value);
        } catch {
            showFieldError(field, 'URL inválida');
            return false;
        }
    }
    
    return true;
}

function showFieldError(field, message) {
    const feedback = field.parentElement.querySelector('.input-feedback');
    if (feedback) {
        feedback.textContent = message;
        feedback.classList.add('error');
        field.style.borderColor = 'var(--danger-color)';
    }
}

function clearFieldError(field) {
    const feedback = field.parentElement.querySelector('.input-feedback');
    if (feedback) {
        feedback.textContent = '';
        feedback.classList.remove('error');
        field.style.borderColor = '';
    }
}

// Search Functionality
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            // Search through modules and content
            const modules = document.querySelectorAll('.dashboard-module');
            modules.forEach(module => {
                const content = module.textContent.toLowerCase();
                const isVisible = content.includes(searchTerm);
                module.style.display = isVisible ? 'block' : 'none';
            });
        });
    }
}

// Export and Save functionality
document.addEventListener('DOMContentLoaded', function() {
    const exportBtn = document.getElementById('exportBtn');
    const saveBtn = document.getElementById('saveBtn');
    
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            showToast('Exportando datos...', 'info');
            // Add export functionality here
        });
    }
    
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            showToast('Guardando cambios...', 'success');
            // Add save functionality here
        });
    }
});

// Notification functionality
document.addEventListener('DOMContentLoaded', function() {
    const notificationBtn = document.getElementById('notificationBtn');
    
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function() {
            showToast('No hay nuevas notificaciones', 'info');
        });
    }
}); 