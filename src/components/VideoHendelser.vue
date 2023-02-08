<template>
    <div>
        <div class="hendelser col-xs-12">
            <div v-for="(innslagFilm, index) in innslagFilmer" :key="index" class="hendelse col-xs-12">
                <div class="inner as-box-style">
                    <div class="info">
                        <h4>{{ innslagFilm.getNavn() }}</h4>
                        <p>{{ innslagFilm.getBeskrivelse() }}</p>
                        <span>{{ innslagFilm.getSted() }}</span>
                    </div>
                    <div class="type mini-label-style">
                        <span>{{ innslagFilm.getType() }}</span>
                    </div>
                    <div class="videos">
                        <div v-for="(video, videoIndex) in innslagFilm.getVideos()" :key="videoIndex">
                            <!--- Show single video -->
                            <video-vue :video="video" :mini="true" />
                        </div>
                    </div>
                    <div class="upload-video-for-hendelse">
                        <!--- Upload video -->
                        <button v-show="!innslagFilm.isUploadOpen" @click="showUpload(innslagFilm)" class="round-style-button mini open-upload">+</button>
                        <upload-video v-show="innslagFilm.isUploadOpen" ref="uploadVideo-reportasje" :erReportasje="false" :hendelseId="innslagFilm.getId()" :miniVersion="true" />
                    </div>
                </div>
            </div>
            <div v-if="innslagFilmer.length < 1">
                <p>Du mu ha minst en hendelse i programmet!</p>
            </div>
        </div>
        
    </div>
</template>


<script lang="ts">
// Import av Vue
import { Vue, Component, Prop } from "vue-property-decorator";
import $ from "jquery";
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import InnslagVideo from "../objects/InnslagVideo";
import VideoVue from './VideoVue.vue';

import UploadVideo from "./UploadVideo.vue";

declare var ajaxurl: string; // Kommer fra global

@Component
export default class VideoHendelser extends Vue {
    private spaInteraction = new SPAInteraction(null, ajaxurl);
    public chartId = '#progressBar';
    public arrangementId : string = '';
    public innslagFilmer : InnslagVideo[] = [];

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
            for(var innslag of response) {
                var innslagVideo = new InnslagVideo(
                    innslag.id,
                    innslag.navn,
                    innslag.beskrivelse,
                    innslag.sted,
                    innslag.context.type
                );
                this.innslagFilmer.push(innslagVideo);
            }
        }
        console.log(this.innslagFilmer);

        return response;
    }

    public showUpload(hendelse : InnslagVideo) {
        hendelse.isUploadOpen = true;
    }

}
    

// Registrering av komponenten
Vue.component('video-hendelser', VideoHendelser);
</script>

<style>

</style>
