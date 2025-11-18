document.addEventListener("DOMContentLoaded", function () {

    const menuItems = document.querySelectorAll(".menu-item");
    let hideTimer;

    menuItems.forEach(item => {

        const mega = item.querySelector(".mega-menu");

        if (!mega) return;

        // Hover vào menu cha → hiện
        item.addEventListener("mouseenter", () => {
            clearTimeout(hideTimer);
            mega.style.display = "block";
        });

        // Rời menu cha → ẩn
        item.addEventListener("mouseleave", () => {
            hideTimer = setTimeout(() => mega.style.display = "none", 150);
        });

        // Nếu rê chuột vào mega menu → vẫn hiện
        mega.addEventListener("mouseenter", () => {
            clearTimeout(hideTimer);
            mega.style.display = "block";
        });

        // Rời khỏi mega menu → ẩn
        mega.addEventListener("mouseleave", () => {
            hideTimer = setTimeout(() => mega.style.display = "none", 150);
        });

    });

});
