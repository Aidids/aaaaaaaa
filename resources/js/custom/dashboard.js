export function sideBarJs () {
    // Get the side button and target element by their IDs
    const sideButton = document.getElementById('sideButton');
    const arrowIcon = sideButton.querySelector('i');
    const sideBar = document.getElementById('sideBar');
    const navLinks = document.querySelectorAll('.nav-link');
    const sideBarLogo = document.getElementById('sideBarLogo');
    const navBarLogo = document.getElementById('navBarLogo');
    let isHidden = false;

    // Add a click event listener to the side button
    sideButton.addEventListener('click', function() {
        arrowIcon.classList.toggle('rotate');
        navBarLogo.classList.toggle('d-none');
        // Toggle the visibility of the target element
        if (isHidden) {
            sideBar.style.minWidth = ''; // Reset to default width
            sideBarLogo.style.display = '';
            navLinks.forEach(function(link) {
                link.style.display = ''; // Reset to default display
            });
        } else {
            sideBar.style.minWidth = '1rem';
            sideBarLogo.style.display = 'none';
            navLinks.forEach(function(link) {
                link.style.display = 'none';
            });
        }

        // Toggle the flag
        isHidden = !isHidden;
    });
}

