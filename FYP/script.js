// Select elements
const wrapper = document.querySelector('.wrapper');
const loginLink = document.querySelector('.login-link');
const registerLink = document.querySelector('.register-link');
const iconClose = document.querySelector('.icon-close');
const btnPopup = document.querySelector('.btnLogin-popup');
const navbarToggler = document.querySelector('.navbar-toggler');
const navbarNav = document.querySelector('.navbar-nav');

// Event listener for the Register link
registerLink.addEventListener('click', () => {
    wrapper.classList.add('active');
});

// Event listener for the Login link
loginLink.addEventListener('click', () => {
    wrapper.classList.remove('active');
});

// Event listener for the Login button
btnPopup.addEventListener('click', () => {
    if (window.innerWidth <= 750) {
        navbarNav.style.display = 'none'; // Hide the navbar links on smaller screens
    }
    wrapper.classList.add('active-popup');
    btnPopup.style.display = 'none'; // Hide the login button
});

// Event listener for the Close icon
iconClose.addEventListener('click', () => {
    wrapper.classList.remove('active-popup');
    if (window.innerWidth <= 750) {
        navbarNav.style.display = 'flex'; // Show the navbar links on smaller screens
    }
    btnPopup.style.display = 'block'; // Show the login button
});

// Event listener to toggle navbar links visibility
navbarToggler.addEventListener('click', () => {
    if (wrapper.classList.contains('active-popup')) {
        if (window.innerWidth <= 750) {
            navbarNav.style.display = 'flex'; // Show the navbar links on smaller screens
        }
        btnPopup.style.display = 'none'; // Hide the login button
    } else {
        btnPopup.style.display = 'block'; // Show the login button
    }
});

// Function to handle error-based form toggle
function checkError() {
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    
    if (error == 1) {
        // Show the login form
        document.querySelector('.btnLogin-popup').click(); // Automatically open the login popup
    } else if (error == 2) {
        // Simulate clicking on the register link to show the register form
        document.querySelector('.btnLogin-popup').click(); // Open the popup first
        registerLink.click(); // Simulate clicking the "Register" link to switch to the registration form
    }
}

// Check the error after the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    checkError(); // Run the error check when the page loads
});