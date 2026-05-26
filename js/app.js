document.addEventListener("DOMContentLoaded", function () {
    var deleteLinks = document.querySelectorAll(".delete-link");

    deleteLinks.forEach(function (link) {
        link.addEventListener("click", function (event) {
            var ok = window.confirm("Are you sure you want to delete this item?");
            if (!ok) {
                event.preventDefault();
            }
        });
    });
});
