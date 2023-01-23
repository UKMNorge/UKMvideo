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

    public getDurationStr() : string {
        var hms = this.toHoursAndMinutes(Math.floor(this.duration));
        var h = hms.h;
        var m = hms.m;
        var s = hms.s;


        // Time er større en null
        if(h > 0) {
            return h + ':'+ m + ":" + s;
        }
        else if(m > 0) {
            return m + ':' + s;
        }
        else {
            return m + ':' + s;
        }
        
    }

    private toHoursAndMinutes(totalSeconds : number) : { h: number, m: number, s: number } {
        const totalMinutes = Math.floor(totalSeconds / 60);
      
        const seconds = totalSeconds % 60;
        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;
      
        return { h: hours, m: minutes, s: seconds };
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