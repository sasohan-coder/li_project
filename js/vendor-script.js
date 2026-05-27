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

// Table Action Handlers
const editButtons = document.querySelectorAll('.btn-action.edit');

editButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const row = e.target.closest('tr');
        const name = row.cells[1].textContent;
        const email = row.cells[2].textContent;
        const phone = row.cells[3].textContent;
        
        // Populate edit form
        document.getElementById('editVendorName').value = name;
        document.getElementById('editEmail').value = email;
        document.getElementById('editPhone').value = phone;
        
        // Show edit modal
        editModal.classList.add('active');
    });
});

// Add New Vendor Button
const addButton = document.querySelector('.btn-primary');
const modal = document.getElementById('addVendorModal');
const modalClose = document.querySelector('.modal-close');
const btnCancel = document.querySelector('.btn-cancel');

addButton?.addEventListener('click', () => {
    modal.classList.add('active');
});

modalClose?.addEventListener('click', () => {
    modal.classList.remove('active');
});

btnCancel?.addEventListener('click', () => {
    modal.classList.remove('active');
});

// Close modal when clicking outside
modal?.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.classList.remove('active');
    }
});

// Edit Modal Handlers
const editModal = document.getElementById('editVendorModal');
const modalCloseEdit = document.querySelector('.modal-close-edit');
const btnCancelEdit = document.querySelector('.btn-cancel-edit');

modalCloseEdit?.addEventListener('click', () => {
    editModal.classList.remove('active');
});

btnCancelEdit?.addEventListener('click', () => {
    editModal.classList.remove('active');
});

// Close edit modal when clicking outside
editModal?.addEventListener('click', (e) => {
    if (e.target === editModal) {
        editModal.classList.remove('active');
    }
});

// Search Functionality
const searchInput = document.querySelector('.search-box input');
const tableRows = document.querySelectorAll('.data-table tbody tr');

searchInput?.addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase();

    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Log current date
const dateDisplay = document.querySelector('.date');
if (dateDisplay) {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const today = new Date().toLocaleDateString('en-US', options);
    dateDisplay.textContent = today;
}