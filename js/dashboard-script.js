// Sidebar Toggle
const menuToggle = document.querySelector('.menu-toggle');
const sidebar = document.querySelector('.sidebar');

menuToggle?.addEventListener('click', () => {
    sidebar.classList.toggle('active');
});

// Close sidebar when clicking on a nav item (mobile)
const navItems = document.querySelectorAll('.nav-item');
navItems.forEach(item => {
    item.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('active');
        }
    });
});

// Submenu Toggle
const navItemGroups = document.querySelectorAll('.nav-item-group');
navItemGroups.forEach(group => {
    const groupToggle = group.querySelector('.group-toggle');
    const submenu = group.querySelector('.submenu');

    groupToggle?.addEventListener('click', (e) => {
        e.preventDefault();
        submenu.classList.toggle('show');
        groupToggle.classList.toggle('open');
    });
});

// Close sidebar when clicking outside
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768) {
        if (!e.target.closest('.sidebar') && !e.target.closest('.menu-toggle')) {
            sidebar.classList.remove('active');
        }
    }
});

// Animate metric values on load
function animateMetrics() {
    const metricValues = document.querySelectorAll('.metric-value');

    metricValues.forEach(metric => {
        const finalValue = parseInt(metric.textContent);
        let currentValue = 0;
        const increment = Math.ceil(finalValue / 30);

        const interval = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
                metric.textContent = finalValue;
                clearInterval(interval);
            } else {
                metric.textContent = currentValue;
            }
        }, 30);
    });
}

// Animate books overview bars - FIXED
function animateBooksOverview() {
    const statValues = document.querySelectorAll('.stat-value');

    // Extract just the numbers from the text (e.g., "15 books" -> 15)
    const availableBooks = parseInt(statValues[0]?.textContent) || 0;
    const allottedBooks = parseInt(statValues[1]?.textContent) || 0;
    const totalBooks = availableBooks + allottedBooks;

    if (totalBooks === 0) return;

    const availablePercent = (availableBooks / totalBooks) * 100;
    const allottedPercent = (allottedBooks / totalBooks) * 100;

    const availableFill = document.getElementById('availableFill');
    const allottedFill = document.getElementById('allottedFill');
    const availablePercent_elem = document.getElementById('availablePercent');
    const allottedPercent_elem = document.getElementById('allottedPercent');

    // Animate available books bar
    let currentAvailable = 0;
    const availableInterval = setInterval(() => {
        if (currentAvailable >= availablePercent) {
            currentAvailable = availablePercent;
            clearInterval(availableInterval);
        } else {
            currentAvailable += availablePercent / 30;
        }
        availableFill.style.width = currentAvailable + '%';
        availablePercent_elem.textContent = Math.round(currentAvailable) + '%';
    }, 30);

    // Animate allotted books bar
    let currentAllotted = 0;
    const allottedInterval = setInterval(() => {
        if (currentAllotted >= allottedPercent) {
            currentAllotted = allottedPercent;
            clearInterval(allottedInterval);
        } else {
            currentAllotted += allottedPercent / 30;
        }
        allottedFill.style.width = currentAllotted + '%';
        allottedPercent_elem.textContent = Math.round(currentAllotted) + '%';
    }, 30);
}

// Trigger animation on load
window.addEventListener('load', () => {
    animateMetrics();
    animateBooksOverview();
});

// Action card hovers are now fully handled by premium CSS transitions!

// Log current date
const dateDisplay = document.querySelector('.date');
if (dateDisplay) {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const todayDate = new Date().toLocaleDateString('en-US', options);
    dateDisplay.textContent = todayDate;
}