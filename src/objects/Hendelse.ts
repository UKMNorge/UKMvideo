import InnslagVideo from "../objects/InnslagVideo";

export default class Hendelse {
    private id: string; // Readonly
    private navn: string; // Readonly
    private beskrivelse: string; // Readonly
    private sted: string; // Readonly
    private type: string; // Readonly
    private innslags : InnslagVideo[] = [];
    public isUploadOpen = false;
    public hendelseOpen = false;


    constructor(id: string, navn: string, beskrivelse: string, sted: string, type: string) {
        this.id = id;
        this.navn = navn;
        this.beskrivelse = beskrivelse;
        this.sted = sted;
        this.type = type;
    }

    public addInnslags(innslags : InnslagVideo[]) : void {
        this.innslags = innslags;
    }

    public getInnslags() : InnslagVideo[] {
        return this.innslags;
    }

    public getId() : string {
        return this.id;
    }
    public getNavn() : string {
        return this.navn;
    }
    public getBeskrivelse() : string {
        return this.beskrivelse;
    }
    public getSted() : string {
        return this.sted;
    }
    public getType() : string {
        return this.type;
    }
}
