// Select all carousel containers
const carousels = document.querySelectorAll('.carousel-container');

carousels.forEach(carousel => {
  const cards = carousel.querySelector('.cards');
  const leftBtn = carousel.querySelector('.left-btn');
  const rightBtn = carousel.querySelector('.right-btn');

  // Scroll left
  leftBtn.addEventListener('click', () => {
    cards.scrollBy({
      left: -200, // Adjust scroll amount
      behavior: 'smooth',
    });
  });

  // Scroll right
  rightBtn.addEventListener('click', () => {
    cards.scrollBy({
      left: 200, // Adjust scroll amount
      behavior: 'smooth',
    });
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
