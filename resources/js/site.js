import Splide from '@splidejs/splide';

window.onload = function () {
    const bodyElemment = document.querySelector("body")

    bodyElemment.classList.remove("preload");
}

// search box

const searchBox = document.querySelector(".search-box");
const searchBtn = document.querySelector(".search-icon");
const cancelBtn = document.querySelector(".cancel-icon");
const searchInput = document.querySelector("input");
searchBtn.onclick = () => {
    searchBox.classList.add("active");
    searchBtn.classList.add("active");
    searchInput.classList.add("active");
    cancelBtn.classList.add("active");
    searchInput.focus();
}
cancelBtn.onclick = () => {
    searchBox.classList.remove("active");
    searchBtn.classList.remove("active");
    searchInput.classList.remove("active");
    cancelBtn.classList.remove("active");
    searchInput.value = "";
}