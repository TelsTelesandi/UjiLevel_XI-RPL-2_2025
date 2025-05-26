import './bootstrap';
import Alpine from 'alpinejs';

// Start Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Form submission handler
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        let isSubmitting = false;
        
        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return;
            }
            
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton && !submitButton.disabled) {
                isSubmitting = true;
                submitButton.disabled = true;
                const originalText = submitButton.textContent;
                submitButton.textContent = 'Memproses...';
                
                // Re-enable button after 5 seconds if form hasn't submitted
                setTimeout(() => {
                    if (submitButton.disabled) {
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                        isSubmitting = false;
                    }
                }, 5000);
            }
        });
    });
});
