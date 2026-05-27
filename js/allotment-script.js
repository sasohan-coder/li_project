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
const today = new Date().toISOString().split('T')[0];

flatpickr('#startDate', {
    dateFormat: 'Y-m-d',
    minDate: 'today',
    onChange: function(selectedDates) {
        if (selectedDates[0]) {
            document.getElementById('hiddenStartDate').value = selectedDates[0].toISOString().split('T')[0];
            // Auto-set end date based on subscription type
            updateEndDate();
        }
    }
});

const endDatePicker = flatpickr('#endDate', {
    dateFormat: 'Y-m-d',
    minDate: 'today',
    onChange: function(selectedDates) {
        if (selectedDates[0]) {
            document.getElementById('hiddenEndDate').value = selectedDates[0].toISOString().split('T')[0];
        }
    }
});

function updateEndDate() {
    const startDateInput = document.getElementById('startDate');
    const subscriptionSelect = document.getElementById('subscriptionType');

    if (!startDateInput.value || !subscriptionSelect.value) return;

    const selectedOption = subscriptionSelect.options[subscriptionSelect.selectedIndex];
    const days = parseInt(selectedOption.getAttribute('data-days')) || 30;

    const startDate = new Date(startDateInput.value);
    const endDate = new Date(startDate);
    endDate.setDate(endDate.getDate() + days);

    endDatePicker.setDate(endDate, true);
    document.getElementById('hiddenEndDate').value = endDate.toISOString().split('T')[0];
}

document.getElementById('subscriptionType')?.addEventListener('change', updateEndDate);

// Book Card Click Handlers
const allotButtons = document.querySelectorAll('.allot-btn');
const modal = document.getElementById('allotModal');
const modalClose = document.querySelector('.modal-close');
const btnCancel = document.querySelector('.btn-cancel');

allotButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const card = btn.closest('.book-card');
        const bookName = card.querySelector('.book-title').textContent;
        const bookImage = card.querySelector('.card-image img').src;

        // Set modal data
        document.getElementById('hiddenBookName').value = bookName;
        document.getElementById('modalBookName').textContent = bookName;
        document.getElementById('modalBookImage').src = bookImage;

        // Reset form fields
        document.getElementById('studentName').value = '';
        document.getElementById('subscriptionType').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';

        modal.classList.add('active');
    });
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

// Search Functionality
const searchInput = document.querySelector('.search-box input');
const bookCards = document.querySelectorAll('.book-card');

searchInput?.addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase();

    bookCards.forEach(card => {
        const bookTitle = card.querySelector('.book-title').textContent.toLowerCase();
        const authorName = card.querySelector('.book-author').textContent.toLowerCase();

        const matches = bookTitle.includes(searchTerm) || authorName.includes(searchTerm);
        card.style.display = matches ? '' : 'none';
    });
});

// Log current date
const dateDisplay = document.querySelector('.date');
if (dateDisplay) {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const todayDate = new Date().toLocaleDateString('en-US', options);
    dateDisplay.textContent = todayDate;
}