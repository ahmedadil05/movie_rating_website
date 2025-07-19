// Function to preview the selected image
function previewImage(event, previewId) {
  const file = event.target.files[0];
  const previewElement = document.getElementById(previewId);
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      previewElement.src = e.target.result; // Set the image preview
    };
    reader.readAsDataURL(file);
  }
}


function validateDateInput(input) {
    // Allow only digits and dashes
    input.value = input.value.replace(/[^0-9-]/g, '');
    
    // Split the input into parts
    const parts = input.value.split('-');

    // Enforce proper format structure
    if (parts.length > 3) {
        input.value = parts.slice(0, 3).join('-'); // Limit to 3 parts
    }

    // Validate and restrict year, month, and day
    if (parts[0] && parts[0].length > 4) {
        parts[0] = parts[0].slice(0, 4); // Limit year to 4 digits
    }
    if (parts[1]) {
        parts[1] = parts[1].slice(0, 2); // Limit month to 2 digits
        if (parseInt(parts[1], 10) > 12) {
            parts[1] = '12'; // Limit month to 12
        }
    }
    if (parts[2]) {
        parts[2] = parts[2].slice(0, 2); // Limit day to 2 digits
        if (parseInt(parts[2], 10) > 31) {
            parts[2] = '31'; // Limit day to 31
        }
    }

    // Update the input value
    input.value = parts.join('-');
}




document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('add-movie-form');
  const modal = document.getElementById('success-modal');
  const modalMessage = document.getElementById('modal-message');
  const closeButton = document.getElementById('confirm-yes');
  const submitButton = document.getElementById('up');

  // Function to reset the form fields
  function resetFormFields() {
    form.reset(); // Reset all form fields to their default state
    // Reset image previews to the default 'plus.png' image
    document.getElementById('poster-preview').src = 'assets/images/plus.png';
    document.getElementById('cover-preview').src = 'assets/images/plus.png';
  }

  // Form submission handler
  form.addEventListener('submit', (event) => {
    event.preventDefault(); // Prevent default form submission

    // Disable the submit button to prevent duplicate submissions
    submitButton.disabled = true;

    // Create a FormData object to gather form data
    const formData = new FormData(form);

    // Send the form data to the server using fetch()
    fetch('addmovie.php', {
      method: 'POST',
      body: formData,
    })
      .then((response) => response.text())
      .then((data) => {
        // Display the response message in the modal
        modalMessage.textContent = data.includes('successfully') ? 'Movie Added Successfully!' : data;
        modal.classList.remove('hidden');
        modal.classList.add('visible');

        // Reset form fields only on success
        if (data.includes('successfully')) {
          resetFormFields();
        }
      })
      .catch((error) => {
        // Handle errors
        console.error('Error:', error);
        modalMessage.textContent = 'An error occurred. Please try again.';
        modal.classList.remove('hidden');
        modal.classList.add('visible');
      })
      .finally(() => {
        // Re-enable the submit button
        submitButton.disabled = false;
      });
  });

  // Modal close button handler
  closeButton.addEventListener('click', () => {
    // Hide the modal when the close button is clicked
    modal.classList.remove('visible');
    modal.classList.add('hidden');
  });
});
