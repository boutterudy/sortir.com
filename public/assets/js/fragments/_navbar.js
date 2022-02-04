window.onload = () => {
    let hamburger = document.getElementById("navbar-hamburger"),
        links = document.getElementById('navbar-mobile-links');

    function showMobileLinks() {
        if(hamburger.getAttribute('name') === 'menu-outline') {
            hamburger.setAttribute('name', 'close-outline');
            links.style.display = 'flex';
        } else {
            hamburger.setAttribute('name', 'menu-outline');
            links.style.display = 'none';
        }
    }

    hamburger.addEventListener("click", showMobileLinks);
}