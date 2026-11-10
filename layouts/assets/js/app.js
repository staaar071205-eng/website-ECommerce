document.addEventListener("DOMContentLoaded", function (event) {

    const menuToggle = document.getElementById('menu-toggle');
    const wrapper = document.getElementById('wrapper');

    menuToggle.addEventListener('click', function () {
        // Menambah atau menghapus class 'toggled' pada wrapper
        wrapper.classList.toggle('toggled');
    });
});