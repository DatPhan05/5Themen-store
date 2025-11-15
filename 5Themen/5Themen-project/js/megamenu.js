const megaData = {
    "nam": [
        { title: "Áo Nam", items: ["Áo thun", "Áo polo", "Áo sơ mi", "Áo khoác"] },
        { title: "Quần Nam", items: ["Quần jeans", "Quần tây", "Quần short"] },
        { title: "Giày dép", items: ["Giày thể thao", "Dép sandal"] }
    ],
    "nu": [
        { title: "Áo Nữ", items: ["Áo kiểu", "Áo thun", "Áo sơ mi", "Áo polo"] },
        { title: "Đầm", items: ["Đầm suông", "Đầm dự tiệc", "Đầm công sở"] },
        { title: "Chân váy", items: ["Váy chữ A", "Váy dài", "Váy ôm"] }
    ],
    "tre-em": [
        { title: "Áo Trẻ Em", items: ["Áo bé trai", "Áo bé gái", "Áo thun", "Áo sơ mi"] },
        { title: "Quần Trẻ Em", items: ["Quần jean", "Quần kaki", "Quần short"] }
    ],
    "sale": [
        { title: "SALE HOT", items: ["Dưới 100k", "100-200k", "200-300k"] },
        { title: "Flash Sale", items: ["24h", "Giảm sâu 70%"] }
    ],
    "bst": [
        { title: "Bộ sưu tập mới", items: ["Thu Đông", "Xuân Hè"] },
        { title: "Limited Edition", items: ["IVY Signature", "Design Premium"] }
    ]
};

const megaMenu = document.getElementById("mega-menu");
const megaContent = document.getElementById("mega-content");

document.querySelectorAll(".menu-item").forEach(item => {
    item.addEventListener("mouseenter", () => {
        const key = item.dataset.menu;
        const data = megaData[key];

        let html = "";
        data.forEach(col => {
            html += `<div>
                        <div class="mega-title">${col.title}</div>
                        ${col.items.map(i => `<a href="#">${i}</a>`).join("")}
                    </div>`;
        });

        megaContent.innerHTML = html;
        megaMenu.classList.add("show");
    });
});

document.querySelector("header").addEventListener("mouseleave", () => {
    megaMenu.classList.remove("show");
});
