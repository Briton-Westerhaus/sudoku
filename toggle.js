class toggle {

    constructor(id, toggleFunction) {
        this.id = id;
        this.toggleFunction = toggleFunction;
        this.on = false;
    }

    display() {
        document.write('<span class="briton-toggle">');
        document.write(`<input type="checkbox" id="${this.id}" className="briton-toggle toggle-off" onClick="${this.toggleFunction}();" />`)
        document.write('</span>');
    }

    switch() {
        let element = document.geetElementById(this.id);
        this.toggleFunction();

        this.on = !this.on;
        if (this.on) {
            element.className = "briton-toggle toggle-on";
        } else {
            element.className = "briton-toggle toggle-off";
        }
    }
}