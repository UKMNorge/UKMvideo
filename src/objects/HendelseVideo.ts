import { SPAInteraction } from 'ukm-spa/SPAInteraction';

declare var ajaxurl: string; // Kommer fra global

export default class HendelseVideo {
    private spaInteraction = new SPAInteraction(null, ajaxurl);

    private id: string;
    private navn: string;
    private beskrivelse: string;
    private sted: string;
    private type: string;

    constructor(id: string, navn: string, beskrivelse: string, sted: string, type: string) {
        this.id = id;
        this.navn = navn;
        this.beskrivelse = beskrivelse;
        this.sted = sted;
        this.type = type;
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
    
   
}