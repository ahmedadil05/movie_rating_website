const deleteButton = document.getElementById('deletebtn');
const confirmModal = document.getElementById('confirmation-modal');
const confirmYes = document.getElementById('confirm-yes-delete');
const confirmNo = document.getElementById('confirm-no-delete');
const deleteForm = document.getElementById('delete-form');

// Show confirmation modal when delete button is clicked
deleteButton.addEventListener('click', () => {
    confirmModal.classList.remove('hidden');
    confirmModal.classList.add('visible');
});

confirmYes.addEventListener('click', () => {
    confirmModal.classList.remove('visible');
    confirmModal.classList.add('hidden');

    const formData = new FormData(deleteForm);

    // Fire-and-forget request to the server
    fetch(deleteForm.action, {
        method: 'POST',
        body: formData,
    }).then(() => {
        // Redirect immediately after sending the request
        window.location.href = "index.php";
    }).catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

// Handle "No" cancellation
confirmNo.addEventListener('click', () => {
    confirmModal.classList.remove('visible');
    confirmModal.classList.add('hidden');
});



// Handle general form submissions (e.g., update username, email, password)
document.querySelectorAll('.forms').forEach(form => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault(); // Prevent default form submission

        const formData = new FormData(form);

        try {
            // Send data to the server
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
            });

            const result = await response.json();

            // Display the modal with the server response
            document.getElementById('modal-message').textContent = result.message;
            const modal = document.getElementById('success-modal');
            modal.classList.remove('hidden');
            modal.classList.add('visible');
        } catch (error) {
            // Handle errors
            console.error('Error:', error);
            document.getElementById('modal-message').textContent = 'An error occurred. Please try again.';
            const modal = document.getElementById('success-modal');
            modal.classList.remove('hidden');
            modal.classList.add('visible');
        }
    });
});

// Close modal functionality
document.getElementById('modal-close-btn').addEventListener('click', () => {
    const modal = document.getElementById('success-modal');
    modal.classList.add('hidden');
    modal.classList.remove('visible');
});


