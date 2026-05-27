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
// const navItemGroups = document.querySelectorAll('.nav-item-group');
// navItemGroups.forEach(group => {
//     const groupToggle = group.querySelector('.group-toggle');
//     const submenu = group.querySelector('.submenu');
//     const toggleIcon = group.querySelector('.toggle-icon');
//
//     groupToggle?.addEventListener('click', (e) => {
//         e.preventDefault();
//         submenu.classList.toggle('active');
//         toggleIcon.classList.toggle('rotated');
//     });
// });

// Submenu Toggle - FIXED
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
const updateQtyButtons = document.querySelectorAll('.btn-action.update-qty');

editButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const row = e.target.closest('tr');
        const bookName = row.cells[1].textContent;
        const authorName = row.cells[3].textContent;
        const publisherName = row.cells[4].textContent;
        const bookImage = row.cells[2].querySelector('img')?.src || '';

        // Populate edit form
        document.getElementById('editBookName').value = bookName;
        document.getElementById('editAuthorName').value = authorName;
        document.getElementById('editPublisherName').value = publisherName;
        document.getElementById('editBookImage').value = bookImage;

        // Show edit modal
        editModal.classList.add('active');
    });
});

updateQtyButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const row = e.target.closest('tr');
        const currentQty = row.cells[5].textContent;

        // Populate quantity form
        document.getElementById('currentQty').value = currentQty;
        document.getElementById('newQty').value = currentQty;

        // Show update quantity modal
        updateQtyModal.classList.add('active');
    });
});

// Add New Book Button
const addButton = document.querySelector('.btn-primary');
const modal = document.getElementById('addBookModal');
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
const editModal = document.getElementById('editBookModal');
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

// Update Quantity Modal Handlers
const updateQtyModal = document.getElementById('updateQtyModal');
const modalCloseQty = document.querySelector('.modal-close-qty');
const btnCancelQty = document.querySelector('.btn-cancel-qty');

modalCloseQty?.addEventListener('click', () => {
    updateQtyModal.classList.remove('active');
});

btnCancelQty?.addEventListener('click', () => {
    updateQtyModal.classList.remove('active');
});

// Close update quantity modal when clicking outside
updateQtyModal?.addEventListener('click', (e) => {
    if (e.target === updateQtyModal) {
        updateQtyModal.classList.remove('active');
    }
});

// Search Functionality (by book name only)
const searchInput = document.querySelector('.search-box input');
const tableRows = document.querySelectorAll('.data-table tbody tr');

searchInput?.addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase();

    tableRows.forEach(row => {
        const bookName = row.cells[1].textContent.toLowerCase();
        row.style.display = bookName.includes(searchTerm) ? '' : 'none';
    });
});

// Log current date
const dateDisplay = document.querySelector('.date');
if (dateDisplay) {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const today = new Date().toLocaleDateString('en-US', options);
    dateDisplay.textContent = today;
}