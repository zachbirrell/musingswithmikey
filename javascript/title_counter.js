var blog_title = document.getElementById("a-title");
var counter = document.getElementById("title-counter");

blog_title.oninput = function () {
    counter.innerText = `${blog_title.value.length}/40`;
};