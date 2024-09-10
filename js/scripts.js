document.addEventListener("DOMContentLoaded", () => {
    const navLinks = document.querySelectorAll('nav ul li a');

    // Highlight the active link based on the current URL
    navLinks.forEach(link => {
        // Check if the link's href matches the current URL
        if (link.href === window.location.href) {
            link.parentElement.classList.add('active'); // Add active class
            link.style.color = 'red'; // Optional: Change color of the active link
        }
    });
});
