// Delete Button
document.getElementById('delete-movie-btn')?.addEventListener('click', () => {
    const modal = document.createElement('div');
    modal.className = 'modal visible';
    modal.innerHTML = `
      <div class="modal-content">
        <h2>Are you sure you want to delete this movie?</h2>
        <button class="confirm-yes">Yes</button>
        <button class="confirm-no">No</button>
      </div>
    `;
    document.body.appendChild(modal);
  
    modal.querySelector('.confirm-yes').addEventListener('click', async () => {
      const response = await fetch(`php/delete_movie.php?id=${movieId}`, { method: 'POST' });
      if (response.ok) {
        window.location.href = 'index.php';
      } else {
        alert('Failed to delete movie.');
      }
    });
  
    modal.querySelector('.confirm-no').addEventListener('click', () => {
      document.body.removeChild(modal);
    });
  });
  
  // Edit Button
  document.getElementById('edit-movie-btn')?.addEventListener('click', () => {
    const modal = document.createElement('div');
    modal.className = 'modal visible';
    modal.innerHTML = `
      <div class="modal-content">
        <form id="edit-movie-form" action="php/edit_movie.php" method="POST" enctype="multipart/form-data">
          <h2>Edit Movie</h2>
          <input type="hidden" name="movie_id" value="${movieId}">
          <input type="text" name="movie_name" value="${movieTitle}" required>
          <textarea name="description">${movieDescription}</textarea>
          <input type="file" name="poster">
          <input type="file" name="cover">
          <button type="submit" class="confirm-yes">Save</button>
          <button type="button" class="confirm-no">Cancel</button>
        </form>
      </div>
    `;
    document.body.appendChild(modal);
  
    modal.querySelector('.confirm-no').addEventListener('click', () => {
      document.body.removeChild(modal);
    });
  });
  