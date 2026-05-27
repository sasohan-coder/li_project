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
        submenu.classList.toggle('show');  // Changed from 'active' to 'show'
        groupToggle.classList.toggle('open');  // Toggle 'open' on groupToggle
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

// View Details Modal
const viewButtons = document.querySelectorAll('.btn-action.view');
const modal = document.getElementById('viewModal');
const modalClose = document.querySelector('.modal-close');
const btnClose = document.querySelector('.btn-close');

viewButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const row = e.target.closest('tr');

        const bookName = row.cells[1].textContent;
        const studentEmail = row.cells[2].textContent;
        const subscription = row.cells[3].textContent.trim();
        const startDate = row.cells[4].textContent;
        const endDate = row.cells[5].textContent;
        const allotmentDate = row.cells[6].textContent;
        const status = row.cells[7].textContent.trim();

        // Populate modal
        document.getElementById('detailBookName').textContent = bookName;
        document.getElementById('detailStudentEmail').textContent = studentEmail;
        document.getElementById('detailSubscription').textContent = subscription;
        document.getElementById('detailStartDate').textContent = startDate;
        document.getElementById('detailEndDate').textContent = endDate;
        document.getElementById('detailAllotmentDate').textContent = allotmentDate;
        document.getElementById('detailStatus').innerHTML = row.cells[7].innerHTML;

        modal.classList.add('active');
    });
});

modalClose?.addEventListener('click', () => {
    modal.classList.remove('active');
});

btnClose?.addEventListener('click', () => {
    modal.classList.remove('active');
});

// Close modal when clicking outside
modal?.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.classList.remove('active');
    }
});

// Search Functionality
const searchInput = document.querySelector('.search-box input');
const tableRows = document.querySelectorAll('.data-table tbody tr');

searchInput?.addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase();

    tableRows.forEach(row => {
        const bookName = row.cells[1].textContent.toLowerCase();
        const studentEmail = row.cells[2].textContent.toLowerCase();

        const matches = bookName.includes(searchTerm) || studentEmail.includes(searchTerm);
        row.style.display = matches ? '' : 'none';
    });
});

// Calculate Summary Statistics
function calculateSummary() {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    let activeCount = 0;
    let expiredCount = 0;
    let totalCount = 0;

    tableRows.forEach(row => {
        if (row.style.display !== 'none') {
            totalCount++;

            const statusText = row.cells[7].textContent;
            if (statusText.includes('Active')) {
                activeCount++;
            } else if (statusText.includes('Expired')) {
                expiredCount++;
            }
        }
    });

    // Update summary cards
    document.getElementById('activeCount').textContent = activeCount;
    document.getElementById('expiredCount').textContent = expiredCount;
    document.getElementById('totalCount').textContent = totalCount;
}

// Initial calculation
calculateSummary();

// Recalculate on search
searchInput?.addEventListener('input', () => {
    setTimeout(calculateSummary, 100);
});

// Log current date
const dateDisplay = document.querySelector('.date');
if (dateDisplay) {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const todayDate = new Date().toLocaleDateString('en-US', options);
    dateDisplay.textContent = todayDate;
}