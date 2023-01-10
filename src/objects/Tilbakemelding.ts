export default class Tilbakemelding {
    private id : number;
    
    constructor(id : number, sporsmaal : string, svar : string, innslagType? : string) {
        this.id = id;
    }
}