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

// Datepicker initialization
flatpickr('#purchaseDate', {
    dateFormat: 'Y-m-d',
    defaultDate: new Date()
});

flatpickr('#editPurchaseDate', {
    dateFormat: 'Y-m-d'
});

// Calculate total amount for Add form
const quantityInput = document.getElementById('quantity');
const perBookPriceInput = document.getElementById('perBookPrice');
const totalAmountDisplay = document.getElementById('totalAmount');

function calculateTotal() {
    const quantity = parseFloat(quantityInput.value) || 0;
    const perBookPrice = parseFloat(perBookPriceInput.value) || 0;
    const total = (quantity * perBookPrice).toFixed(2);
    totalAmountDisplay.value = total;
}

quantityInput?.addEventListener('input', calculateTotal);
perBookPriceInput?.addEventListener('input', calculateTotal);

// Calculate total amount for Edit form
const editQuantityInput = document.getElementById('editQuantity');
const editPerBookPriceInput = document.getElementById('editPerBookPrice');
const editTotalAmountDisplay = document.getElementById('editTotalAmount');

function calculateEditTotal() {
    const quantity = parseFloat(editQuantityInput.value) || 0;
    const perBookPrice = parseFloat(editPerBookPriceInput.value) || 0;
    const total = (quantity * perBookPrice).toFixed(2);
    editTotalAmountDisplay.value = total;
}

editQuantityInput?.addEventListener('input', calculateEditTotal);
editPerBookPriceInput?.addEventListener('input', calculateEditTotal);

// Table Action Handlers
const editButtons = document.querySelectorAll('.btn-action.edit');

editButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const row = e.target.closest('tr');
        const bookName = row.cells[1].textContent;
        const vendor = row.cells[2].textContent;
        const quantity = row.cells[3].textContent;
        const perBookPrice = row.cells[4].textContent.replace('₹', '').trim();
        const purchaseDate = row.cells[6].textContent;

        // Populate edit form
        const purchaseId = row.cells[0].textContent.trim();
        if (document.getElementById('editPurchaseId')) {
            document.getElementById('editPurchaseId').value = purchaseId;
        }
        document.getElementById('editBookName').value = bookName;
        document.getElementById('editVendor').value = vendor;
        document.getElementById('editQuantity').value = quantity;
        document.getElementById('editPerBookPrice').value = perBookPrice;
        document.getElementById('editPurchaseDate').value = purchaseDate;

        // Calculate total
        calculateEditTotal();

        // Show edit modal
        editModal.classList.add('active');
    });
});

// Add New Purchase Button
const addButton = document.querySelector('.btn-primary');
const modal = document.getElementById('addPurchaseModal');
const modalClose = document.querySelector('.modal-close');
const btnCancel = document.querySelector('.btn-cancel');

addButton?.addEventListener('click', () => {
    // Reset form
    document.querySelector('.modal-form').reset();
    totalAmountDisplay.value = '0.00';
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
const editModal = document.getElementById('editPurchaseModal');
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