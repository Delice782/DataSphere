                                                          
/**
 * Smooth scrolling functionality for DataSphere website
 * This script handles anchor links to page section
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get all links with hash
    const links = document.querySelectorAll('a[href*="#"]');
    
    // Add smooth scroll to each link
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            // Get the target path and hash
            const href = this.getAttribute('href');
            const path = href.split('#')[0];
            const hash = href.split('#')[1];
            
            // Only apply smooth scrolling when on the correct page
            if (hash && (path === '' || path === window.location.pathname || path === '../views/index.php')) {
                e.preventDefault();
                
                // If we're not on index.php, redirect to it with the hash
                if (window.location.pathname.indexOf('index.php') === -1 && path.indexOf('index.php') !== -1) {
                    window.location.href = path + '#' + hash;
                    return;
                }
                
                const targetElement = document.getElementById(hash);
                
                if (targetElement) {
                    // Get height of fixed header if exists
                    const headerHeight = document.querySelector('header').offsetHeight;
                    
                    window.scrollTo({
                        top: targetElement.offsetTop - headerHeight - 20, 
                        behavior: 'smooth'
                    });
                    
                    // Update URL hash without jump
                    history.pushState(null, null, '#' + hash);
                }
            }
        });
    });
    
    // Handle direct access with hash in URL
    if (window.location.hash) {
        setTimeout(function() {
            const hash = window.location.hash.substring(1);
            const targetElement = document.getElementById(hash);
            
            if (targetElement) {
                const headerHeight = document.querySelector('header').offsetHeight;
                
                window.scrollTo({
                    top: targetElement.offsetTop - headerHeight - 20,
                    behavior: 'smooth'
                });
            }
        }, 100);
    }
});

































































