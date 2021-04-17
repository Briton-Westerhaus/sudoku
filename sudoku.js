let guessGrid;
let fullGrid;

const recurse = function(x, y) {
    numberExcluded = 0;
    allowed = [true, true, true, true, true, true, true, true, true];
    for (k = 0; k < 9; k++) {
        if(allowed[fullGrid[k][y] - 1] != false){
            allowed[fullGrid[k][y] - 1] = false;
            numberExcluded++;
        }
        if(allowed[fullGrid[x][k] - 1] != false){
            allowed[fullGrid[x][k] - 1] = false;
            numberExcluded++;
        }
    }
    if (numberExcluded == 9)
        return {'boolean': false, 'value': 0};

    let xpos = 2 - (x % 3);
    let ypos = 2 - (y % 3);
    
    for (let k = -2; k <= 0; k++) {
        for (let l = -2; l <= 0; l++) {
            if (allowed[fullGrid[x + xpos + k][y + ypos + l] + 1] != false) {
                allowed[fullGrid[x + xpos + k][y + ypos + l] + 1] = false;
                numberExcluded++;
            }
        }
    }

    if (numberExcluded == 9)
        return {'boolean': false, 'value': 0};

    for (let i = 0; i < allowed.length; i++) {
        if (allowed[i] == true)
            whichNumbers.push(i);
    }

    if (x == 8 && y == 8) {
        fullGrid[x][y] = whichNumbers[Math.round(Math.random() * (whichNumbers.length - 1))];
        return {'boolean': true, 'value': fullGrid[x][y]};
    }

    let nexty = y;
    let nextx;

    if (x == 8) {
        nextx = 0;
        nexty++;
    } else {
        nextx = x + 1;
    }

    let testNext = false;

    while (!testNext) {
        fullGrid[x][y] = whichNumbers[rand(0, count(whichNumbers) - 1)];
        testNext = recurse(nextx, nexty);
        if (testNext['value'] == 0) {
            allowed[fullGrid[x][y]] = false;
            numberExcluded++;
            if (numberExcluded == 9) {
                fullGrid[x][y];
                return {'boolean': false, 'value': 0};
            }
            whichNumbers = [];
            for (let i = 0; i < 9; i++) {
                if (allowed[i] == true)
                    whichNumbers.push(i);
            }
        }

        testNext = testNext['boolean'];
    }
    return {'boolean': true, 'value': fullGrid[x][y]};
}

const removeSome = function() {

}

fullGrid = [];
for (i = 0; i < 9; i++)
    fullGrid[i] = [];
recurse(0, 0);
guessGrid = [];
for (i = 0; i < 9; i++)
    guessGrid[i] = [];
for (i = 0; i < 9; i++) {
    for (j = 0; j < 9; j++) {
        guessGrid[i][j] = fullGrid[i][j];
    }
}
removeSome();