<template>
    <div>
        <div class="hendelser col-xs-12">
            <div v-for="(hendelse, index) in hendelser" :key="index" class="hendelse col-xs-4">
                <div class="inner">
                    <div class="info">
                        <h4>{{ hendelse.getNavn() }}</h4>
                        <p>{{ hendelse.getBeskrivelse() }}</p>
                        <span>{{ hendelse.getSted() }}</span>
                    </div>
                    <div class="type mini-label-style">
                        <span>{{ hendelse.getType() }}</span>
                    </div>
                    <div class="videos">
                        <div v-for="(video, videoIndex) in hendelse.getVideos()" :key="videoIndex">
                            <!--- Show single video -->
                            <video-vue :video="video" :mini="true" />
                        </div>
                    </div>
                    <div class="upload-video-for-hendelse">
                        <!--- Upload video -->
                        <button v-show="!hendelse.isUploadOpen" @click="showUpload(hendelse)" class="round-style-button mini open-upload">+</button>
                        <upload-video v-show="hendelse.isUploadOpen" ref="uploadVideo-reportasje" :erReportasje="false" :miniVersion="true" />
                    </div>
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
import VideoVue from './VideoVue.vue';

import UploadVideo from "./UploadVideo.vue";

declare var ajaxurl: string; // Kommer fra global

@Component
export default class VideoHendelser extends Vue {
    private spaInteraction = new SPAInteraction(null, ajaxurl);
    public chartId = '#progressBar';
    public arrangementId : string = '';
    public hendelser : HendelseVideo[] = [];

    components = {
        UploadVideo,
        VideoVue
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
                var hendelseVideo = new HendelseVideo(
                    hendelse.id,
                    hendelse.navn,
                    hendelse.beskrivelse,
                    hendelse.sted,
                    hendelse.context.type
                );
                this.hendelser.push(hendelseVideo);
            }
        }
        console.log(this.hendelser);

        return response;
    }

    public showUpload(hendelse : HendelseVideo) {
        hendelse.isUploadOpen = true;
    }

}
    

// Registrering av komponenten
Vue.component('video-hendelser', VideoHendelser);
</script>

<style>

</style>
