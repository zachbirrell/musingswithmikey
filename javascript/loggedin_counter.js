var counter_element = document.getElementById('counter');

var counter = 4;

function countdown(counter) {
    if (counter > 0) {
        counter--;
        counter_element.innerText = `Proceeding to homepage in ${counter} seconds...`;
        setTimeout(function(){countdown(counter)}, 1000);
    } else {
        window.location.replace("index.php");
    }
}

countdown(counter);