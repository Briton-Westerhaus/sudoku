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
    let x = 0, y = -1;
    while (!element) {
        y++;
        if (y > 8) {
            y = 0;
            x++;
        }
        element = document.getElementById(x + ':' + y);
    }
    selectInput(x, y);
}

function left() {
    let element;
    let x = activeX, y = activeY;
    while (!element) {
        y--;
        if (y < 0) {
            y = 8;
            x--;
        }
        element = document.getElementById(x + ':' + y);
        if (!element && y <= 0 && x <= 0)
            return;
    }
    selectInput(x, y);
}

function up() {

}

function right() {
    if (activeY == 8 && activeX == 8)
        return;
    let element;
    let x = activeX, y = activeY;
    while (!element) {
        y++;
        if (y > 8) {
            y = 0;
            x++;
        }
        element = document.getElementById(x + ':' + y);
        if (!element && y >= 8 && x >= 8)
            return;
    }
    selectInput(x, y);
}

function down() {

}

document.onkeydown = function(evt) {
    switch(evt.keyCode) {
        case 37: //left arrow
            left();
            break;

        case 38: //up arrow
            up();
            break;
        
        case 39: //right arrow
            right();
            break;

        case 40: //down arrow
            down();
            break;

        default:
            break;
    }
    
}