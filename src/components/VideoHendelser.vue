<template>
    <div>
        <div class="hendelser">
            <div v-for="(hendelse, index) in hendelser" :key="index" class="hendelse">
                <p>{{ hendelse.getId() }}</p>
                <div class="videos">
                    <span>Filmer her:</span>
                    <list-videos ref="allVideos-reportasje" :erReportasje="false" />
                </div>
                <div class="upload-video-for-hendelse">
                    <upload-video ref="uploadVideo-reportasje" :erReportasje="false" />
                </div>
            </div>
        </div>
        
    </div>
</template>


<script lang="ts">
// Import av Vue
import { Vue, Component, Prop } from "vue-property-decorator";
import $ from "jquery";
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import HendelseVideo from "../objects/HendelseVideo";

import UploadVideo from "./UploadVideo.vue";
import ListVideos from "./ListVideos.vue";

declare var ajaxurl: string; // Kommer fra global

@Component
export default class VideoHendelser extends Vue {
    private spaInteraction = new SPAInteraction(null, ajaxurl);
    public chartId = '#progressBar';
    public arrangementId : string = '';
    public hendelser : HendelseVideo[] = [];

    components = {
        UploadVideo,
        ListVideos
    }


    public init() {
        var arrangementId = $('#vueArguments').attr('arrangementId');
        if(arrangementId) {
            this.arrangementId = arrangementId;
            this.fetchHendelser();
        }
        else {
            console.error('arrangementId må være definert');
        }
    }

    public async fetchHendelser() {
        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'getHendelser',
            arrangementId: this.arrangementId 
        };
        
        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        if(response) {
            // this.hendelser = [];
            for(var hendelse of response) {
                var hendelseVideo = new HendelseVideo(hendelse.id);
                this.hendelser.push(hendelseVideo);
            }
        }
        console.log(this.hendelser);

        return response;
    }

}
    

// Registrering av komponenten
Vue.component('video-hendelser', VideoHendelser);
</script>

<style>
.hendelser .hendelse {
    border: solid 1px red;
}
</style>
