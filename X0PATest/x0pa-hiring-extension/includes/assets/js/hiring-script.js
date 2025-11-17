// Hiring Page Template - JavaScript
// Scroll Spy for Jump Links Navigation

function initScrollSpy(initialSectionId) {
    // Get all jump links and sections
    const jumpLinks = document.querySelectorAll('.jump-links__link');
    const sections = document.querySelectorAll('.content-section');

    // Track currently active section
    let currentActiveId = initialSectionId;

    // Function to update active link
    function setActiveLink(sectionId) {
        if (currentActiveId === sectionId) return; // Avoid unnecessary updates
        currentActiveId = sectionId;

        // Remove active class from all links
        jumpLinks.forEach(link => {
            link.classList.remove('jump-links__link--active');
        });

        // Add active class to matching link
        const activeLink = document.querySelector(`.jump-links__link[href="#${sectionId}"]`);
        if (activeLink) {
            activeLink.classList.add('jump-links__link--active');
        }
    }

    // Intersection Observer options
    const observerOptions = {
        root: null, // Use viewport as root
        rootMargin: '-20% 0px -60% 0px', // Trigger when section is near top of viewport
        threshold: 0
    };

    // Track which sections are currently intersecting
    const intersectingSections = new Set();

    // Create Intersection Observer
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            const sectionId = entry.target.getAttribute('id');
            if (entry.isIntersecting) {
                // Add to intersecting sections
                intersectingSections.add(sectionId);
            } else {
                // Remove from intersecting sections
                intersectingSections.delete(sectionId);
            }
        });

        // If we have intersecting sections, activate the topmost one
        if (intersectingSections.size > 0) {
            // Get the topmost intersecting section
            let topmostSection = null;
            let topmostPosition = Infinity;

            intersectingSections.forEach(sectionId => {
                const section = document.getElementById(sectionId);
                if (section) {
                    const rect = section.getBoundingClientRect();
                    if (rect.top < topmostPosition) {
                        topmostPosition = rect.top;
                        topmostSection = sectionId;
                    }
                }
            });

            if (topmostSection) {
                setActiveLink(topmostSection);
            }
        }
    }, observerOptions);

    // Observe all sections
    sections.forEach(section => {
        observer.observe(section);
    });

    // Handle smooth scroll when clicking jump links
    jumpLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                // Immediately update active state
                setActiveLink(targetId);
            }
        });
    });
}

// Initialize TOC toggle for mobile
function initTOCToggle() {
    const toggleButton = document.querySelector('.jump-links__toggle');
    const tocList = document.querySelector('.jump-links__list');

    if (!toggleButton || !tocList) return;

    toggleButton.addEventListener('click', function() {
        const isExpanded = this.getAttribute('aria-expanded') === 'true';

        // Toggle aria-expanded attribute
        this.setAttribute('aria-expanded', !isExpanded);

        // Toggle expanded class on list
        if (isExpanded) {
            tocList.classList.remove('jump-links__list--expanded');
        } else {
            tocList.classList.add('jump-links__list--expanded');
        }
    });

    // Close TOC when clicking a link on mobile
    const tocLinks = document.querySelectorAll('.jump-links__link');
    tocLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Only auto-close on mobile (when toggle is visible)
            if (window.innerWidth < 1024) { // lg breakpoint
                tocList.classList.remove('jump-links__list--expanded');
                toggleButton.setAttribute('aria-expanded', 'false');
            }
        });
    });
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TOC toggle
    initTOCToggle();

    // Wait for page-specific initialization call
    // Call initScrollSpy('section-id') from your page
});
