var tabButtons = document.querySelectorAll(".tabcontainer .tabbtnscontainer button");
var tabContents = document.querySelectorAll(".tabcontainer .tabcontents");

function showContents(contentIndex) {

    tabButtons.forEach(btn => {
        btn.style.backgroundColor="";
        btn.style.color="";
    });

    tabButtons[contentIndex].style.backgroundColor="green";
    tabButtons[contentIndex].style.color="white";

    
    tabContents.forEach(content => {
        content.style.display="none";
    });

    tabContents[contentIndex].style.display="flex";
}
showContents(0);

function comments_disabled() {
    alert("Comments have been disabled for this video.");
}