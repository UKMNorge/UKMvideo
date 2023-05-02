import { SPAInteraction } from 'ukm-spa/SPAInteraction';
declare var ajaxurl: string; // Kommer fra global


export default class Video {
    private id : string;
    private title : string;
    private description : string;
    private thumbnail : string;
    private duration : number;
    private status : string;
    private preview : string;
    private spaInteraction = new SPAInteraction(null, ajaxurl);
    private processingProgress : number = 100;
    private lagret : boolean;
    private innslag : number|null;
    private arrangemet : number|null;


    public ready = false;
    
    constructor(id : string, title : string, description : string, thumbnail : string, duration : number, status : string, preview : string, lagret : boolean, creator : string) {
        this.id = id;
        this.title = title;
        this.description = description;
        this.thumbnail = thumbnail;
        this.duration = duration;
        this.status = status;
        this.preview = preview;
        this.lagret = lagret;
        this.innslag = null;
        this.arrangemet = null;

        if(creator.length > 0) {
            // Det er innslag (b for band)
            if((<any>creator).includes('-b-')) {
                var creatorSplit = creator.split('-b-');
                this.arrangemet = creatorSplit.length > 0 ? parseInt(creatorSplit[0]) : null;
                this.innslag = creatorSplit.length == 2 ? parseInt(creatorSplit[1]) : null;
            }
            // Det er kun reportasje (p for place)
            else if((<any>creator).includes('-p-')) {
                var creatorSplit = creator.split('-p-');
                this.arrangemet = creatorSplit.length > 0 ? parseInt(creatorSplit[0]) : null;
            }
        }
        
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
        }, 5000);
    }

    public getId() : string {
        return this.id;
    }

    public getTitle() : string {
        return this.title;
    }

    public getDescription() : string {
        return this.description;
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
        var m = h > 0 && hms.m < 10 ? '0' + hms.m : hms.m;
        var s = hms.s < 10 ? '0' + hms.s : hms.s;


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

    public isLagret() : boolean {
        return this.lagret;
    }

    public getInnslagId() : number|null {
        return this.innslag;
    }

    public getArrangementId() : number|null {
        return this.arrangemet;
    }
}