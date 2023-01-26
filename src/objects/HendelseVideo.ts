import { SPAInteraction } from 'ukm-spa/SPAInteraction';

declare var ajaxurl: string; // Kommer fra global

export default class HendelseVideo {
    private spaInteraction = new SPAInteraction(null, ajaxurl);

    private id : string;
    
    
    constructor(id : string) {
        this.id = id;
    }
    
    public uploadVideo() {
        
    }
    
    public getCloudFlareCreatorId() {
        
    }
    
    private async getVideos() {
        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'getSingleVideo',
            videoId : this.id
        };
        
        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        
        return response;
    }
    
    public getId() : string {
        return this.id;
    }
}