<template>
    <div>
        <div class="hendelser col-xs-12 nop">
            <div v-for="(hendelse, index) in hendelser" :key="index" class="hendelse col-xs-12">
                <div class="inner as-box-style">
                    <div class="info">
                        <h4>{{ hendelse.getNavn() }}</h4>
                        <p>{{ hendelse.getBeskrivelse() }}</p>
                        <span>{{ hendelse.getSted() }}</span>
                    </div>
                    <!-- alle innslag -->
                    <div class="innslags">
                        <div v-for="(innslag, innslagIndex) in hendelse.getInnslags()" :key="innslagIndex" class="innslag col-sm-4 col-xs-6">
                            <div class="innslag-inner as-box-style">

                                <!-- Labels -->
                                <div class="labels">
                                    <div class="label-mini mini-label-style">
                                        <span>{{ innslag.getType() }}</span>
                                    </div>
                                </div>
                                <p>{{ innslag.getNavn()  }}</p>
                                
                                <div class="buttons">
                                    <button class="btn show-more collapsed" type="button" data-toggle="collapse" :data-target="[ '#allVideos' + innslag.getId() ]" aria-expanded="false" :aria-controls="[ 'allVideos' + innslag.getId() ]">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M16.293 9.293 12 13.586 7.707 9.293l-1.414 1.414L12 16.414l5.707-5.707z"></path></svg>
                                        <p>{{ innslag.getVideos().length }} film{{ innslag.getVideos().length > 1 ? 'er' : '' }}</p>
                                    </button>
                                </div>
                                <!-- Filmer i innslag -->
                                <div class="videos collapse" :id="[ 'allVideos' + innslag.getId() ]">
                                    <div class="inner-videos">
                                        <div v-for="(video, videoIndex) in innslag.getVideos()" :key="videoIndex">
                                            <video-vue :video="video" :mini="true" />
                                        </div>
                                        
                                        <!-- Last opp film -->
                                        <div class="upload-video-for-hendelse innslag">
                                            <!--- Upload video -->
                                            <button v-show="!innslag.isUploadOpen" @click="showUpload(innslag)" class="round-style-button mini open-upload">+</button>
                                            <upload-video v-show="innslag.isUploadOpen" ref="uploadVideo-reportasje" :erReportasje="false" :hendelseId="innslag.getId()" :miniVersion="true" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="hendelser.length < 1">
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
import Hendelse from "../objects/Hendelse";
import InnslagVideo from "../objects/InnslagVideo";
import VideoVue from './VideoVue.vue';

import UploadVideo from "./UploadVideo.vue";

declare var ajaxurl: string; // Kommer fra global

@Component
export default class VideoHendelser extends Vue {
    private spaInteraction = new SPAInteraction(null, ajaxurl);
    public chartId = '#progressBar';
    public arrangementId : string = '';
    public hendelser : Hendelse[] = [];

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
                var hendelseObj = new Hendelse(
                    hendelse.id,
                    hendelse.navn,
                    hendelse.beskrivelse,
                    hendelse.sted,
                    hendelse.context.type
                );

                // Legg til innslag
                var innslags : InnslagVideo[] = [];
                for(var innslag of hendelse.innslag.innslag) {
                    var innslagVideoObj = new InnslagVideo(
                        innslag.id,
                        innslag.navn,
                        innslag.type.name  
                    );
                    innslags.push(innslagVideoObj);
                }
                hendelseObj.addInnslags(innslags);
                this.hendelser.push(hendelseObj);
            }
        }
        console.log(this.hendelser);

        return response;
    }

    public showUpload(hendelse : Hendelse) {
        hendelse.isUploadOpen = true;
    }

}
    

// Registrering av komponenten
Vue.component('video-hendelser', VideoHendelser);
</script>

<style>

</style>
