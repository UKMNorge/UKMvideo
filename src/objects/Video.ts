import { SPAInteraction } from 'ukm-spa/SPAInteraction';
declare var ajaxurl: string; // Kommer fra global


export default class Video {
    private id : string;
    private filename : string;
    private thumbnail : string;
    private duration : number;
    private status : string;
    private preview : string;
    private spaInteraction = new SPAInteraction(null, ajaxurl);
    private processingProgress : number = 100;


    public ready = false;
    
    constructor(id : string, filename : string, thumbnail : string, duration : number, status : string, preview : string) {
        this.id = id;
        this.filename = filename;
        this.thumbnail = thumbnail;
        this.duration = duration;
        this.status = status;
        this.preview = preview;
        
        // The video is being processed, update it
        if(!this.isReady()) {
            this.updateVideo();
        }
    }

    private updateVideo() {
        var interval = setInterval(async () => {

            var data = {
                action: 'UKMvideo_ajax',
                subaction: 'getSingleVideo',
                videoId : this.id
            };
            
            var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);
            
            if(response.result && response.result.success == true) {
                var video = response.result.result;
    
                this.thumbnail = video.thumbnail
                this.duration = video.duration;
                this.status = video.status.state;
                this.preview = video.preview;
                this.processingProgress = video.status.pctComplete ? parseInt(video.status.pctComplete) : 0;
            }
            else {
                clearInterval(interval);
            }
            
            if(this.isReady()) {
                clearInterval(interval);
            }
            return video;
        }, 1000);
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

    public getPreview() : string {
        return this.preview;
    }

    public getProcessingProgress() : number {
        return this.processingProgress;
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

    public isReady() : boolean {
        return this.status == 'ready' || this.status == 'pendingupload';
    }

    public isPendingUpload() : boolean {
        return this.status == 'pendingupload';
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