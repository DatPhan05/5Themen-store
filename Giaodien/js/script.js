const top = document.querySelector(".top")
window.addEventListener("scroll",function(){
    const X = this.pageYOffset;
    if(X>1){top.classList.add("active")}
    else {
        top.classList.remove("active")
    }
})