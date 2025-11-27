const imgPosition = document.querySelectorAll(".aspect-ratio-169 img");
const imgContainer = document.querySelector(".aspect-ratio-169");
const dotItem = document.querySelectorAll(".dot");
let imgNumber = imgPosition.length;
let index = 0;

// Sắp xếp ảnh nằm ngang
imgPosition.forEach(function(image, i){
    image.style.left = i * 100 + "%";
    dotItem[i].addEventListener("click", function(){
        slider(i);
    });
});

// Auto slide
function imgSlider() {
    index++;
    if (index >= imgNumber) {
        index = 0;
    }
    slider(index);
}

setInterval(imgSlider, 5000);

// Hàm chuyển slider
function slider(i) {
    imgContainer.style.left = "-" + i * 100 + "%";

    // FIX lỗi chính tả
    const dotActive = document.querySelector(".dot.active");
    if (dotActive) {
        dotActive.classList.remove("active");
    }

    dotItem[i].classList.add("active");
}
