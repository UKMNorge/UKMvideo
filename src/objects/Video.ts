export default class Video {
    private id : string;
    private filename : string;
    private thumbnail : string;
    private duration : number;
    private status : string;

    public ready = false;
    
    constructor(id : string, filename : string, thumbnail : string, duration : number, status : string) {
        this.id = id;
        this.filename = filename;
        this.thumbnail = thumbnail;
        this.duration = duration;
        this.status = status;
    }

    public getId() : string {
        return this.id;
    }

    public getFilename() : string {
        return this.filename;
    }

    public getThumbnail() : string {
        return this.thumbnail;
    }

    public getDuration() : number {
        return this.duration;
    }

    public getStatus() : string {
        return this.status;
    }


    // Sett
    public setId(id : string) : void {
        this.id = id;
    }

    public setThumbnail(thumbnail : string) : void {
        this.thumbnail = thumbnail;
    }

    public setDuration(duration : number) : void {
        this.duration = duration;
    }

    public getEmbed() {
        
    }
}