let activeX, activeY, pencilMode, autoCheckMode, autoPencilMode;

function selectInput(inputX, inputY) {
    if (activeX == inputX && activeY == inputY)
        return;
    activeX = inputX;
    activeY = inputY;
    let input = document.getElementById(activeX + ':' + activeY);
    input.focus();
    input.select();
}

function isComplete() {
    for (let x = 0; x < 9; x++) {
        for (let y = 0; y < 9; y++) {
            theElement = document.getElementById(x + ':' + y);
            if (theElement == null)
                continue;
            if (theElement.value.length < 1 || theElement.className == "wrong") // Also don't submit if we're still looking at wrong answers.
                return false;
        }
    }
    return true;
}

function inputChanged(evt) {
    let theValue = Number(evt.value);
    if (isNaN(theValue) || theValue == 0 || theValue > 9) {
        document.getElementById(evt.id).value = "";
    }

    if (pencilMode) {
        let pencilMark = document.getElementById(evt.id + ':' + theValue);
        if (pencilMark.innerHTML != theValue) {
            pencilMark.innerHTML = theValue;
        } else {
            pencilMark.innerHTML = "&nbsp;";
        }
        evt.value = '';
    } else {
        let theElement;
        let coords = evt.id.split(':');
        if (isNaN(theValue) || theValue == 0 || theValue > 9) {
            guessGrid[coords[0]][coords[1]] = null;
        } else {
            guessGrid[coords[0]][coords[1]] = theValue;
        }
        // First clear the pencil marks
        for (let i = 1; i <= 9; i++) {
            theElement = document.getElementById(evt.id + ':' + i).innerHTML = "&nbsp;";
        }

        let complete = isComplete();

        if (complete || autoCheckMode) {
            document.getElementById("Completed").value = true;
            document.getElementById("TheForm").submit();
        }
    }
    
    if (autoPencilMode) {
        markPencils();
    }
}

function markPencils() {
    for (let x = 0; x < 9; x++) {
        for (let y = 0; y < 9; y++) {
            if (guessGrid[x][y] != null)
                continue;
            for (let num = 1; num <= 9; num++) {
                if (canBeNumber(x, y, num)) {
                    document.getElementById(x + ":" + y + ":" + num).innerHTML = num;
                } else {
                    document.getElementById(x + ":" + y + ":" + num).innerHTML = '&nbsp;';
                }
            }
        }
    }
}

function clearPencils() {
    for (let x = 0; x < 9; x++) {
        for (let y = 0; y < 9; y++) {
            for (let num = 1; num <= 9; num++) {
                document.getElementById(x + ":" + y + ":" + num).innerHTML = '&nbsp;';
            }
        }
    }
}

function canBeNumber(x, y, num) {    
    for (let i = 0; i < 9; i++) {
        if (guessGrid[x][i] == num)
            return false;
        if (guessGrid[i][y] == num)
            return false;
    }
    let xpos = 2 - (x % 3);
    let ypos = 2 - (y % 3);
    for (let k = -2; k <= 0; k++) {
        for (let l = -2; l <= 0; l++) {
            if (guessGrid[x + xpos + k][y + ypos + l] == num)
                return false;
        }
    }
    return true;
}

function init() {
    let element;
    let x = 0, y = -1;

    // Mode toggles
    pencilMode = (document.getElementById('PencilMode') == null ? false : document.getElementById("PencilMode").value == 'true');
    autoCheckMode = (document.getElementById('AutoCheckMode') == null ? false : document.getElementById("AutoCheckMode").value == 'true');
    autoPencilMode = (document.getElementById('AutoPencilMode') == null ? false : document.getElementById("AutoPencilMode").value == 'true');

    if (autoPencilMode) {
        markPencils();
    }

    while (!element) {
        y++;
        if (y > 8) {
            y = 0;
            x++;
        }
        element = document.getElementById(x + ':' + y);
        if (!element && x > 8)
            break;
    }
    if (element) {
        selectInput(x, y);

        window.setTimeout(clearResults, 3000);
    } else { // Completed
        clearResults();
    }
}

function togglePencil() {
    pencilMode = !pencilMode;
    document.getElementById("PencilMode").value = pencilMode;
}

function toggleAutoCheck() {
    autoCheckMode = !autoCheckMode;
    document.getElementById("AutoCheckMode").value = autoCheckMode;
}

function toggleAutoPencil() {
    autoPencilMode = !autoPencilMode;
    document.getElementById("AutoPencilMode").value = autoPencilMode;
    if (autoPencilMode) {
        markPencils();
    } else {
        clearPencils();
    }
}

function clearResults() {
    let theElement;
    let elements = document.getElementsByClassName("wrong");

    while (!!elements && elements.length > 0) {
        theElement = elements[0];
        theElement.className = "";
        theElement.value = "";
        theElement.attributes["value"].value = "";
        elements = document.getElementsByClassName("wrong");
    }

    elements = document.getElementsByClassName("correct");

    while (!!elements && elements.length > 0) {
        theElement = elements[0];
        theElement.className = "";
        elements = document.getElementsByClassName("correct");
    }
}

function closeModal() {
    document.getElementById("ModalContainer").style.display = "none";
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
        if (!element && x < 0)
            return;
    }
    selectInput(x, y);
}

function up() {
    let element;
    let x = activeX, y = activeY;
    while (!element) {
        x--;
        element = document.getElementById(x + ':' + y);
        if (!element && x < 0)
            return;
    }
    selectInput(x, y);
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
        if (!element && x > 8)
            return;
    }
    selectInput(x, y);
}

function down() {
    let element;
    let x = activeX, y = activeY;
    while (!element) {
        x++;
        element = document.getElementById(x + ':' + y);
        if (!element && x > 8)
            return;
    }
    selectInput(x, y);
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