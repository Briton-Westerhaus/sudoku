class toggle {

    constructor(id, toggleFunction) {
        this.id = id;
        this.toggleFunction = toggleFunction;
        this.on = false;
    }

    display() {
        document.write(`<input type="checkbox" id="${this.id}" className="toggleOff" onClick="${this.toggleFunction}();" />`)
    }

    switch() {
        let element = document.geetElementById(this.id);
        this.toggleFunction();

        this.on = !this.on;
        if (this.on) {
            element.className = "toggleOn";
        } else {
            element.className = "toggleOff";
        }
    }
}