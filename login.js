

// Get elements
const loginForm = document.getElementById('login-form');
const createAccountForm = document.getElementById('create-account-form');
const overlay = document.getElementById('overlay');
const showCreateAccount = document.getElementById('show-create-account');
const showLogin = document.getElementById('show-login');
const overlayHead = document.getElementById('overlay-head');
const overlayText = document.getElementById('overlay-text');

// Add event listeners
showCreateAccount.addEventListener('click', () => {
    overlay.classList.add('move-left');
    loginForm.classList.add('hidden');
    createAccountForm.classList.remove('hidden');
    overlayHead.textContent = 'Create Account';
    showLogin.classList.remove('hidden-button');
    showCreateAccount.classList.add('hidden-button');
    overlayText.textContent = 'Already have an account?';
});

showLogin?.addEventListener('click', () => {
    overlay.classList.remove('move-left');
    createAccountForm.classList.add('hidden');
    loginForm.classList.remove('hidden');
    overlayHead.textContent = 'Login';
    showLogin.classList.add('hidden-button');
    showCreateAccount.classList.remove('hidden-button');
    overlayText.textContent = 'Don’t have an account?';
});

document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('register-form');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const usernameMessage = document.getElementById('username-message');
    const emailMessage = document.getElementById('email-message');
    const passwordMessage = document.getElementById('password-message');

    const validateField = async (field, value, messageElement) => {
        try {
            const response = await fetch('php/validate.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ field, value }),
            });
            const result = await response.json();

            if (result.valid) {
                messageElement.style.color = 'green';
                messageElement.textContent = result.message;
                return true;
            } else {
                messageElement.style.color = 'red';
                messageElement.textContent = result.message;
                return false;
            }
        } catch (error) {
            console.error('Error validating field:', error);
            messageElement.textContent = 'An error occurred while validating.';
            messageElement.style.color = 'red';
            return false;
        } finally {
            messageElement.style.display = 'block';
        }
    };

    usernameInput.addEventListener('blur', () => validateField('username', usernameInput.value, usernameMessage));
    emailInput.addEventListener('blur', () => validateField('email', emailInput.value, emailMessage));
    passwordInput.addEventListener('blur', () => validateField('password', passwordInput.value, passwordMessage));

    registerForm.addEventListener('submit', async (event) => {
        event.preventDefault(); // Prevent default form submission

        // Validate all fields
        const isUsernameValid = await validateField('username', usernameInput.value, usernameMessage);
        const isEmailValid = await validateField('email', emailInput.value, emailMessage);
        const isPasswordValid = await validateField('password', passwordInput.value, passwordMessage);

        if (isUsernameValid && isEmailValid && isPasswordValid) {
            // If all inputs are valid, submit the form directly to register.php
            registerForm.action = 'php/register.php'; // Ensure the form action is set
            registerForm.method = 'POST'; // Ensure the form method is POST
            registerForm.submit(); // Submit the form
        } else {
            alert('Please fix the errors before submitting.');
        }
    });
});


