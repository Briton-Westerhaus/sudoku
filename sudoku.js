const fs = require('fs');

let guessGrid;
let fullGrid;

const getCount = function(guessGrid) {
    let count = 0;
    for (let j = 0; j < 9; j++) {
        for (let k = 0; k < 9; k++) {
            if (guessGrid[j][k] == fullGrid[j][k])
                count++;
        }
    }
    return count;
}


const recurse = function(x, y) {
    let numberExcluded = 0;
    let allowed = [true, true, true, true, true, true, true, true, true];
    let whichNumbers = [];

    for (k = 0; k < 9; k++) {
        if(allowed[fullGrid[k][y] - 1] == true) {
            allowed[fullGrid[k][y] - 1] = false;
            numberExcluded++;
        }
        if(allowed[fullGrid[x][k] - 1] == true) {
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
            if (allowed[fullGrid[x + xpos + k][y + ypos + l] - 1] == true) {
                allowed[fullGrid[x + xpos + k][y + ypos + l] - 1] = false;
                numberExcluded++;
            }
        }
    }

    if (numberExcluded == 9)
        return {'boolean': false, 'value': 0};

    for (let i = 0; i < allowed.length; i++) {
        if (allowed[i] == true)
            whichNumbers.push(i + 1);
    }

    if (x == 8 && y == 8) {
        fullGrid[x][y] = whichNumbers[Math.floor(Math.random() * whichNumbers.length)];
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
        fullGrid[x][y] = whichNumbers[Math.floor(Math.random() * whichNumbers.length)];
        testNext = recurse(nextx, nexty);
        if (testNext['value'] == 0) {
            allowed[fullGrid[x][y] - 1] = false;
            numberExcluded++;
            if (numberExcluded == 9) {
                fullGrid[x][y] = null;
                return {'boolean': false, 'value': 0};
            }
            whichNumbers = [];
            for (let i = 0; i < 9; i++) {
                if (allowed[i] == true)
                    whichNumbers.push(i + 1);
            }
        }

        testNext = testNext['boolean'];
    }
    return {'boolean': true, 'value': fullGrid[x][y]};
}

const removeSome = function() {
    let solvable = true;
    let checkedSolvable = [];
    let x, y;
    for (let x = 0; x < 9; x++) {
        checkedSolvable[x] = [];
        for (let y = 0; y < 9; y++) {
            checkedSolvable[x][y] = false;
        }
    }

    while (solvable) {
        x = Math.floor(Math.random() * 9);
        y = Math.floor(Math.random() * 9);
        if (guessGrid[x][y] == fullGrid[x][y]) {
            guessGrid[x][y] = null;
            checkedSolvable[x][y] = true;
            if (!canSolve(guessGrid, x, y)) {
                guessGrid[x][y] = fullGrid[x][y];
            }

            solvable = false;

            for (i = 0; i < 9; i++) {
                for (j = 0; j < 9; j++) {
                    if (!checkedSolvable[i][j]) {
                        solvable = true;
                        break;
                    }
                }
                if (solvable)
                    break;
            }
        }
    }
    guessGrid[x][y] = fullGrid[x][y];
}

const cloneGrid = function(oldGrid) {
    let newGrid = [];
    oldGrid.forEach(
        array => {
            newGrid.push(array.slice(0));
        }
    )

    return newGrid;
}

const canSolve = function(guessGrid, x = -1, y = -1) {
    guessGrid = cloneGrid(guessGrid);
    if (x > -1 && y > -1) { // It was solvable before we removed the last one, so a shortcut is to see if you can solve for the one just removed. 
        if (solveHelp(x, y, guessGrid)) 
            return true;
    } 
    count = getCount(guessGrid);
    if (count == 81)
        return true;
    // This actually needs to run recursively on the resulted grid to make sure it can be solved the whole way through. 
    for (let i = 0; i < 9; i++) {
        for (let j = 0; j < 9; j++) {
            // TODO: Clone remove stack
            if (guessGrid[i][j] != fullGrid[i][j] && solveHelp(i, j, guessGrid)) {
                // Something bad with chaining back the false from canSolve below?
                guessGrid[i][j] = fullGrid[i][j];
                return canSolve(guessGrid);
            }
        }
    }
    return false;
}

const solveHelp = function(x, y, guessGrid) {
    let canBe = [true, true, true, true, true, true, true, true, true];
    for (let i = 0; i < 9; i++) {
        if (i != y) {
            if (!isNaN(guessGrid[x][i]))
                canBe[guessGrid[x][i] - 1] = false;
        }
        if (i != x) {
            if (!isNaN(guessGrid[i][y]))
                canBe[guessGrid[i][y] - 1] = false;
        }
    }

    let xpos = 2 - (x % 3);
    let ypos = 2 - (y % 3);
    for (let k = -2; k <= 0; k++) {
        for (let l = -2; l <= 0; l++) {
            if (!(xpos + k == 0 && ypos + l == 0)) {
                if (!isNaN(guessGrid[x + xpos + k][y + ypos + l])) 
                    canBe[guessGrid[x + xpos + k][y + ypos + l] - 1] = false;
            }        
        }
    }
    let count = 0;
    canBe.forEach(
        number => {
            if (number == true) {
                count++;
            }
        }
    );
    if (count == 1) {
        return true;
    }
    // Here we need to check for other solving methods, i.e. no other squares in the row/column/grid can be some number. 
    for (let i = 0; i < 9; i++) {
        if (canBe[i]) {
            anotherCanBeNumber = false;
            for (let j = 0; j < 9; j++) {
                if (j != y) {
                    if (canBeNumber(x, j, i + 1, guessGrid)) {
                        anotherCanBeNumber = true;
                        break;
                    }
                }
            }

            if (!anotherCanBeNumber)
                return true;
            
            anotherCanBeNumber = false;
            for (j = 0; j < 9; j++) {
                if (j != x) {
                    if (canBeNumber(j, y, i + 1, guessGrid)) {
                        anotherCanBeNumber = true;
                        break;
                    }
                }
            }
            
            if (!anotherCanBeNumber)
                return true;

            anotherCanBeNumber = false;
            for (let k = -2; k <= 0; k++) {
                for (let l = -2; l <= 0; l++) {
                    
                    if (canBeNumber(x + xpos + k, y + ypos + l, i + 1, guessGrid)) {
                        anotherCanBeNumber = true;
                        break;
                    }
                }
            }
            
            if (!anotherCanBeNumber) 
                return true;
            
        }
    }
    return false;
}

const canBeNumber = function(x, y, num, guessGrid) {
    if (guessGrid[x][y] == fullGrid[x][y]) // We don't check already solved squares.
        return false;
    
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

fs.readdir("sudokus", (err, files) => {
    if (err) {
        console.log(err);
    } else {
        if (files.length >= 10) {
            console.log("We have enough files.");
        } else {

            fullGrid = [];
            for (let i = 0; i < 9; i++)
                fullGrid[i] = [];
            recurse(0, 0);
            guessGrid = [];
            for (let i = 0; i < 9; i++)
                guessGrid[i] = [];
            for (let i = 0; i < 9; i++) {
                for (j = 0; j < 9; j++) {
                    guessGrid[i][j] = fullGrid[i][j];
                }
            }
            
            removeSome();
            
            gridsJson = {
                'guessGrid': guessGrid,
                'fullGrid': fullGrid
            };
            
            fs.writeFile("sudokus/" + (new Date()).getTime() + ".json", JSON.stringify(gridsJson), err => {
                if (err) {
                    console.error(err)
                }
            });

        }
    }
});