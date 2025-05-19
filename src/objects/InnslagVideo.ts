import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import Video from './Video';


declare var ajaxurl: string; // Kommer fra global

export default class InnslagVideo {
    private spaInteraction = new SPAInteraction(null, ajaxurl);

    private id: string;
    private navn: string;
    private type: string;
    private videos: Video[] = [];
    private antallFilmerDB : number;
    private antallFilmer: number;
    public isUploadOpen = false;
    public titles: string = '';
    private samtykker : any[] = [];

    constructor(id: string, navn: string, type: string, antallFilmer?: number, titles?: string, samtykker?: any[]) {
        this.id = id;
        this.navn = navn;
        this.type = type;
        this.antallFilmer = antallFilmer ? antallFilmer : 0;
        
        // Lagrer filmer som kommer for første gang.
        // Variablen antallFilmer kan endres basert på upload, slett og fetch
        this.antallFilmerDB = this.antallFilmer;
        this.titles = titles ? titles : '';
        this.samtykker = samtykker ? samtykker : [];
    }
    
    public getId() : string {
        return this.id;
    }
    public getNavn() : string {
        return this.navn;
    }
    public getType() : string {
        return this.type;
    }
    
    public getVideos() : any[] {
        return this.videos;
    }

    public uploadVideo() {
        
    }
    
    public getCloudFlareCreatorId() {
        
    }

    public getAntallFilmer() : number {
        return this.antallFilmer;
    }
    
    public async fetchVideos() {

        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'getVideos',
            erReportasje: false,
            id: this.id
        };
        
        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        // Empty videos
        this.videos = [];
        if(response.result && response.result.success == true) {
            for(var video of response.result.result) {
                
                var videoObj = new Video(
                    video.uid,
                    video.meta.title ? video.meta.title : '',
                    video.meta.description ? video.meta.description : '',
                    video.thumbnail,
                    video.duration,
                    video.status.state,
                    video.preview,
                    video.meta.lagret ? video.meta.lagret : false,
                    video.creator
                );
                this.videos.push(videoObj);

                videoObj.setThumbnail(video.thumbnail);

                const myImage = new Image(100, 200);
                myImage.src = video.thumbnail;
            }
        }

        this.antallFilmer = this.videos.length;
        
        return response;
    }

    public getTitlesString() : string {
        return this.titles;
    }

    public getNeiSamtykker() : number {
        let countNei = 0;
        for(let samtykke of this.samtykker) {
            let samtykkerNei = false;
            if(samtykke.status.id != 'godkjent') {
                samtykkerNei = true;
            }
            if(samtykke.foresatt && samtykke.foresatt.status.id != 'ikke_sendt' && samtykke.foresatt.status.id != 'godkjent') {
                samtykkerNei = true;
            }
            
            if(samtykkerNei) countNei++;
        }
        return countNei;
    }

    // public getSamtykkerString() : string {
    //     let countAlle = 0;
    //     let countNei = this.getNeiSamtykker();

    //     return countNei > 0 ? countNei + ' deltaker'+ (countNei > 1 ? 'e' : '') +' og/eller foresatte har ikke godkjent eller besvart samtykke' : 'Alle har samtykket';
    // }
    
   
}