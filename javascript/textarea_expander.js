var textarea = document.getElementById("blog-body");

textarea.oninput = function () {
    textarea.style.height = "";
    textarea.style.height = textarea.scrollHeight + "px";
};