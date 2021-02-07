function selectInput(inputNumber) {
    selectedInput = inputNumber;
    let input = document.getElementById("input" + inputNumber);
    input.focus();
    input.select();
}

function inputChanged(evt) {
    if (evt.value.length == 1) {
        selectInput(selectedInput + 1);
    }
}

document.onkeydown = function(evt) {
    switch(evt.keyCode) {
        case 37: //left arrow
            break;

        case 38: //up arrow
            break;
        
        case 39: //right arrow
            break;

        case 40: //down arrow
            break;

        default:
            break;
    }
    
}