/**
 * Reusable Password Visibility Toggle
 * 
 * To use: 
 * 1. Wrap the password input inside a container with `position-relative`.
 * 2. Add the class `toggle-password-field` to the password input.
 * 3. Add an eye icon button right after the input. Example:
 *    <button type="button" class="btn border-0 position-absolute end-0 top-50 translate-middle-y js-password-toggle" tabindex="-1">
 *        <i class="fas fa-eye text-muted"></i>
 *    </button>
 */

document.addEventListener('DOMContentLoaded', function() {
    // Add CSS rule dynamically for z-index
    const style = document.createElement('style');
    style.textContent = `
        .js-password-toggle { z-index: 10; cursor: pointer; }
    `;
    document.head.appendChild(style);

    // Delegate event in case elements are dynamic
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.js-password-toggle');
        if (!btn) return;
        
        e.preventDefault();
        
        const wrapper = btn.closest('.position-relative');
        if (!wrapper) return;
        
        const input = wrapper.querySelector('.toggle-password-field');
        if (!input) return;
        
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = '<i class="fas fa-eye-slash text-white"></i>';
        } else {
            input.type = 'password';
            btn.innerHTML = '<i class="fas fa-eye text-white"></i>';
        }
    });
});
