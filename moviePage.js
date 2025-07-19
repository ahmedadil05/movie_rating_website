document.addEventListener('DOMContentLoaded', () => {
  const editBtns = document.querySelectorAll('.edit-btn'); // Select all Edit buttons
  const modal = document.getElementById('edit-modal'); // Edit modal
  const closeModalBtn = document.getElementById('close-edit-modal'); // Close modal button
  const submitChangesBtn = document.getElementById('submit-changes'); // Submit button
  const form = document.getElementById('edit-movie-form'); // Form for editing

  // Attach event listeners to all Edit buttons
  editBtns.forEach((editBtn) => {
      editBtn.addEventListener('click', async () => {
          const movieId = editBtn.getAttribute('data-movie-id'); // Get the MovieID
          console.log("Clicked Movie ID:", movieId);

          try {
              // Fetch movie data from the server
              const response = await fetch(`php/get_movie.php?id=${movieId}`);
              const movie = await response.json();
              console.log("Fetched Movie Data:", movie);

              if (movie.status === 'success') {
                  const data = movie.data;

                  // Populate the form with movie details
                  form['movie_id'].value = movieId;
                  form['movie_name'].value = data.Title || '';
                  form['genre'].value = data.Genre || '';
                  form['actors'].value = data.Actors || '';
                  form['director'].value = data.Directors || '';
                  form['duration'].value = data.Duration || '';
                  form['rating'].value = data.Rating || '';
                  form['trailerurl'].value = data.TrailerURL || '';
                  form['movieurl'].value = data.MovieURL || '';
                  form['realasedate'].value = data.ReleaseDate || '';
                  form['description'].value = data.Description || '';

                  // Update previews
                  document.getElementById('poster-preview').src = data.poster_path || 'assets/images/plus.png';
                  document.getElementById('cover-preview').src = data.cover_path || 'assets/images/plus.png';

                  // Show the modal
                  modal.classList.add('visible');
              } else {
                  alert(movie.message); // Alert if the server returns an error
              }
          } catch (error) {
              console.error("Error fetching movie data:", error);
              alert("Failed to load movie data.");
          }
      });
  });

  // Handle form submission
  submitChangesBtn.addEventListener('click', async (event) => {
      event.preventDefault(); // Prevent default form submission

      const formData = new FormData(form); // Collect form data

      // Debug: Log formData keys and values
      for (let [key, value] of formData.entries()) {
          console.log(`${key}:`, value);
      }

      try {
          // Submit the updated data
          const response = await fetch('php/edit_movie.php', {
              method: 'POST',
              body: formData,
          });

          const result = await response.json();
          console.log("Server Response:", result); // Debugging

          if (result.status === 'success') {
              alert('Movie updated successfully!');
              modal.classList.remove('visible'); // Close the modal
              window.location.reload(); // Reload the page to reflect changes
          } else {
              console.error("Error from server:", result.message);
              alert(`Failed to update movie: ${result.message}`);
          }
      } catch (error) {
          console.error("Error submitting movie data:", error);
          alert("Failed to update movie.");
      }
  });

  // Close modal
  closeModalBtn.addEventListener('click', () => {
      modal.classList.remove('visible'); // Close the modal
  });

  // Function to preview the selected image
  function previewImage(event, previewId) {
      const file = event.target.files[0]; // Get the selected file
      const previewElement = document.getElementById(previewId); // Get the preview element

      if (file) {
          const reader = new FileReader(); // Create a FileReader to read the file
          reader.onload = function (e) {
              previewElement.src = e.target.result; // Set the image preview
          };
          reader.readAsDataURL(file); // Read the file as a data URL
      }
  }

  // Add event listeners for file input changes
  document.getElementById('movie_poster_input').addEventListener('change', (event) => {
      previewImage(event, 'poster-preview'); // Update poster preview
  });

  document.getElementById('cover_image_input').addEventListener('change', (event) => {
      previewImage(event, 'cover-preview'); // Update cover preview
  });
});



document.addEventListener('DOMContentLoaded', () => {
  const deleteBtn = document.querySelector('.delete-btn'); // Your delete button
  const modal = document.getElementById('confirmation-modal');
  const confirmYes = document.getElementById('confirm-yes-delete');
  const confirmNo = document.getElementById('confirm-no-delete');

  // Show modal when clicking the delete button
  deleteBtn.addEventListener('click', () => {
      modal.classList.add('visible');
      const movieId = deleteBtn.getAttribute('data-movie-id'); // Get MovieID
      confirmYes.setAttribute('data-movie-id', movieId); // Attach MovieID to Yes button
  });

  // Handle the "No" button in the modal
  confirmNo.addEventListener('click', () => {
      modal.classList.remove('visible'); // Close the modal
  });

  // Handle the "Yes" button by submitting the form
  confirmYes.addEventListener('click', () => {
      const movieId = confirmYes.getAttribute('data-movie-id'); // Get attached MovieID
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'php/delete_movie.php';

      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'id';
      input.value = movieId;

      form.appendChild(input);
      document.body.appendChild(form);
      form.submit(); // Submit the form to PHP
  });
});




document.querySelector('.post-btn').addEventListener('click', async function () {
    const commentText = document.getElementById('comment-text').value.trim();
    const movieId = this.getAttribute('data-movie-id');
  
    if (!commentText) {
      alert('Comment cannot be empty!');
      return;
    }
  
    try {
      const response = await fetch('php/add_review.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `movie_id=${movieId}&comment=${encodeURIComponent(commentText)}`,
      });
  
      const result = await response.json();
  
      if (result.status === 'success') {
        alert(result.message);
        location.reload(); // Reload to show the new comment
      } else {
        alert(result.message);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('An unexpected error occurred. Please try again.');
    }
  });
  document.querySelectorAll('.delete-icon').forEach((icon) => {
    icon.addEventListener('click', async function () {
      const reviewId = this.getAttribute('data-review-id');
  
      if (confirm('Are you sure you want to delete this comment?')) {
        try {
          const response = await fetch('php/delete_review.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `review_id=${reviewId}`,
          });
  
          const result = await response.json();
  
          if (result.status === 'success') {
            alert(result.message);
            this.parentElement.parentElement.remove(); // Remove the comment card
          } else {
            alert(result.message);
          }
        } catch (error) {
          console.error('Error:', error);
          alert('An unexpected error occurred. Please try again.');
        }
      }
    });
  });
  // Handle favorite button functionality
document.querySelectorAll('.btn-favorite').forEach(button => {
  button.addEventListener('click', async function () {
    const movieId = this.getAttribute('data-movie-id');
    const isActive = this.classList.contains('active'); // Check current state
    const action = isActive ? 'remove' : 'add'; // Determine action

    try {
      const response = await fetch('php/add_to_wishlist.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `movie_id=${movieId}&action=${action}`,
      });

      const result = await response.json();

      if (result.status === 'success') {
        this.classList.toggle('active'); // Toggle the active class
        alert(result.message);
      } else {
        alert(result.message);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('An unexpected error occurred. Please try again.');
    }
  });
});

