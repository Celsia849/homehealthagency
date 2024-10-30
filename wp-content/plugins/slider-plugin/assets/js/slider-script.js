jQuery(document).ready(function($) {
    let currentIndex = 0; // Current slide index
    const items = $('.slider-item'); // Slider items
    const dots = $('.dot'); // Dots for navigation
    const totalItems = items.length; // Total number of slides

    // Function to show a specific slide
    function showSlide(index) {
        items.hide(); // Hide all slides
        items.eq(index).show(); // Show the current slide
        updateDots(index); // Update dots
    }

    // Function to update active dot
    function updateDots(index) {
        dots.removeClass('active'); // Remove active class from all dots
        dots.eq(index).addClass('active'); // Add active class to the current dot
    }

    // Initial setup
    showSlide(currentIndex); // Show the first slide

    // Navigation handlers
    $('.slider-next').on('click', function() {
        currentIndex = (currentIndex + 1) % totalItems; // Loop to the next slide
        showSlide(currentIndex); // Show the next slide
    });

    $('.slider-prev').on('click', function() {
        currentIndex = (currentIndex - 1 + totalItems) % totalItems; // Loop to the previous slide
        showSlide(currentIndex); // Show the previous slide
    });

    // Dot click handler
    dots.on('click', function() {
        currentIndex = $(this).data('index'); // Get index from the clicked dot
        showSlide(currentIndex); // Show the selected slide
    });
});