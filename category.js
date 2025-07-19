document.addEventListener('DOMContentLoaded', () => {
  const searchForm = document.querySelector('.search-form');
  const genreForm = document.getElementById('genre-form');
  const movieGrid = document.querySelector('.category-grid');

  // Function to fetch and display movies
  async function fetchMovies() {
      // Prepare data to send with the request
      const formData = new FormData(genreForm);
      const searchQuery = document.querySelector('.search-bar').value.trim();
      if (searchQuery) {
          formData.append('query', searchQuery);
      }

      // Send GET request to fetchMovies.php
      try {
          const response = await fetch('php/fetchMovies.php?' + new URLSearchParams([...formData]), {
              method: 'GET',
          });
          const movies = await response.json();

          // Clear the movie grid
          movieGrid.innerHTML = '';

          // Populate the movie grid with fetched data
          if (movies.length > 0) {
              movies.forEach((movie) => {
                  const card = document.createElement('a');
                  card.href = `moviePage.php?id=${movie.MovieID}`;
                  card.className = 'card';
                  card.style.backgroundImage = `url('${movie.poster_path}')`;
                  card.innerHTML = `
                      <div class="card-text">
                          <h3>${movie.Title}</h3>
                          <p>${movie.ReleaseDate} | ${movie.Genre}</p>
                      </div>
                  `;
                  movieGrid.appendChild(card);
              });
          } else {
              // Display a "No movies found" message
              movieGrid.innerHTML = '<p>No movies found matching your criteria.</p>';
          }
      } catch (error) {
          console.error('Error fetching movies:', error);
          movieGrid.innerHTML = '<p>An error occurred while fetching movies. Please try again later.</p>';
      }
  }

  // Event listener for the search form
  searchForm.addEventListener('submit', (e) => {
      e.preventDefault(); // Prevent page reload
      fetchMovies();
  });

  // Event listener for the genre checkboxes
  genreForm.addEventListener('change', fetchMovies);
});
