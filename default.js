let activeX, activeY;

function selectInput(inputX, inputY) {
    activeX = inputX;
    activeY = inputY;
    let input = document.getElementById(activeX + ':' + activeY);
    input.focus();
    input.select();
}

function inputChanged(evt) {
    if (evt.value.length == 1) {
        selectInput(selectedInput + 1);
    }
}

function init() {
    let element;
    let x = -1, y = 0;
    while (!element) {
        x++;
        if (x > 8) {
            x = 0;
            y++;
        }
        element = document.getElementById(x + ':' + y);
    }
    selectInput(x, y);
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