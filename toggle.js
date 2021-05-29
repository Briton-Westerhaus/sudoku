class toggle {

    constructor(id, toggleFunction, labelText, titleText, statusElementName) {
        this.id = id;
        this.toggleFunction = toggleFunction;
        this.labelText = labelText;
        this.titleText = titleText;
        this.statusElement = document.getElementById(statusElementName);
        this.on = this.statusElement == null || this.statusElement.value == "false" ? false : true;
    }

    display() {
        let toggleClass = this.statusElement == null || this.statusElement.value == "false" ? "toggle-off" : "toggle-on";
        document.write(`<span class="briton-toggle-container" title="${this.titleText}"><label for="${this.id}">${this.labelText}</label><span class="briton-toggle ${toggleClass}" id="${this.id}" onclick="${this.id}.switch()"><span>&#x2B24;</span></span></span><div class="briton-clearfix"></div>`);
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