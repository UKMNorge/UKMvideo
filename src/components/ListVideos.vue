<template>
    <div>
        <div class="all-videos col-xs-12 flex-row">
            <div v-for="(video, index) in videos" :key="index" class="vue-video-item col-sm-3 col-xs-4">
                <video-vue :onDeleteCallback="onVideoDelete" :onPublishCallback="onVideoPublish" :video="video" />
            </div>
        </div>
    </div>
</template>


<script lang="ts">
// Import av Vue
import { Vue, Component, Prop } from "vue-property-decorator";
import Video from '../objects/Video';
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import $ from "jquery";


declare var ajaxurl: string; // Kommer fra global

@Component
export default class ListVideos extends Vue {
    // data som kommer fra initialisering av komponenten. Eks. <test-komponent :keys="['a', 'b']" :values="['value1', 'value2']"></test-komponent>
    public videos : Video[] = [];
    private spaInteraction = new SPAInteraction(null, ajaxurl);
    @Prop() erReportasje! : boolean;
    private arrangementId : string = '';
    private innslagId : string = '';
    
    public init() {
        var arrangementId = $('#vueArguments').attr('arrangementId');
        if(arrangementId) {
            this.arrangementId = arrangementId;
            this.fetchAllVideos();
        }
        else {
            console.error('arrangementId må være definert');
        }
    }

    public async fetchAllVideos() {
        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'getVideos',
            erReportasje: this.erReportasje,
            id: this.erReportasje ? this.arrangementId : this.innslagId
        };
        
        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        if(response.result && response.result.success == true) {
            this.videos = [];
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

                // Dette skal fikses senere. Fungerer med en delay fordi browseren hente ikke bildet fra urlen
                setTimeout(() => {
                    const myImage = new Image(100, 200);
                    myImage.src = video.thumbnail;
                }, 100)
            }
        }
        return response;
    }

    public onVideoDelete(response : any, video : Video) {
        this.fetchAllVideos();
    }

    public onVideoPublish(response : any, video : Video) {
        this.fetchAllVideos();
    }
    
}

// Registrering av komponenten
Vue.component('list-videos', ListVideos);
</script>

<style>

</style>
