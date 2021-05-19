class toggle {

    constructor(id, toggleFunction) {
        this.id = id;
        this.toggleFunction = toggleFunction;
        this.on = false;
    }

    display() {
        document.write(`<span class="briton-toggle toggle-off" id="${this.id}" onClick="switch();">&#x2B24;</span>`);
    }

    switch() {
        let element = document.getElementById(this.id);
        this.toggleFunction();

        this.on = !this.on;
        if (this.on) {
            element.className = "briton-toggle toggle-on";
        } else {
            element.className = "briton-toggle toggle-off";
        }
    }
}