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

// splide - slider

var splide = new Splide('#main-carousel', {
    pagination: false,
    rewind: true,
    gap: 30,
});

var thumbnails = document.getElementsByClassName('thumbnail');
var current;

for (var i = 0; i < thumbnails.length; i++) {
    initThumbnail(thumbnails[i], i);
}

function initThumbnail(thumbnail, index) {
    thumbnail.addEventListener('click', function () {
        splide.go(index);
    });
}

splide.on('mounted move', function () {
    var thumbnail = thumbnails[splide.index];

    if (thumbnail) {
        if (current) {
            current.classList.remove('is-active');
        }

        thumbnail.classList.add('is-active');
        current = thumbnail;
    }
});

splide.mount();