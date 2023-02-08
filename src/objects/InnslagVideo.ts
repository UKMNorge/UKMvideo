import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import Video from './Video';


declare var ajaxurl: string; // Kommer fra global

export default class InnslagVideo {
    private spaInteraction = new SPAInteraction(null, ajaxurl);

    private id: string;
    private navn: string;
    private beskrivelse: string;
    private sted: string;
    private type: string;
    private videos : Video[] = [];
    public isUploadOpen = false;

    constructor(id: string, navn: string, beskrivelse: string, sted: string, type: string) {
        this.id = id;
        this.navn = navn;
        this.beskrivelse = beskrivelse;
        this.sted = sted;
        this.type = type;

        this.fetchVideos();
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
    
    public getVideos() : any[] {
        return this.videos;
    }


    public uploadVideo() {
        
    }
    
    public getCloudFlareCreatorId() {
        
    }
    
    private async fetchVideos() {
        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'getVideos',
            erReportasje: false,
            id: this.id
        };
        
        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        if(response.result && response.result.success == true) {
            for(var video of response.result.result) {
                
                var videoObj = new Video(
                    video.uid,
                    video.meta.filename,
                    '',
                    video.duration,
                    video.status.state,
                    video.preview
                )
                this.videos.push(videoObj);

                videoObj.setThumbnail(video.thumbnail);

                // Dette skal fikses senere. Fungerer med en delay fordi browseren hente ikke bildet fra urlen
                setTimeout(() => {
                    const myImage = new Image(100, 200);
                    myImage.src = video.thumbnail;
                }, 100)
            }
        }
        
        
        return response;
    }
    
   
}