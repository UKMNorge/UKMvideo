<template>
    <div>
        <div class="hendelser col-xs-12 nop">
            <div v-for="(hendelse, index) in hendelser" :key="index" class="hendelse col-xs-12">
                <div class="inner as-box-style" >
                    <!-- Vis mer i hendelsen -->
                <div class="hendelse-buttons">
                    <button class="show-more collapsed" type="button" data-toggle="collapse" :data-target="[ '#hendelse' + hendelse.getId() ]" aria-expanded="false" :aria-controls="[ 'hendelse' + hendelse.getId() ]">
                        <div class="info">
                            <h4>{{ hendelse.getNavn() }}</h4>
                            <span>{{ hendelse.getSted() }}</span>
                            <div class="shadow-hide-content"></div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M16.293 9.293 12 13.586 7.707 9.293l-1.414 1.414L12 16.414l5.707-5.707z"></path></svg>
                    </button>
                </div>
                    <div class="videos collapse" :id="[ 'hendelse' + hendelse.getId() ]">
                        <!-- alle innslag -->
                        <div class="innslags row nop">
                            <div v-for="(innslag, innslagIndex) in hendelse.getInnslags()" :key="innslagIndex" class="innslag col-xs-12">
                                <div class="innslag-inner as-box-style">
    
                                    <!-- Labels -->
                                    <div class="labels">
                                        <div class="label-mini mini-label-style">
                                            <span>{{ innslag.getType() }}</span>
                                        </div>
                                    </div>
                                    <p>{{ innslag.getNavn()  }}</p>
                                    
                                    <div class="buttons">
                                        <button @click="innslag.fetchVideos()" class="btn show-more collapsed" type="button" data-toggle="collapse" :data-target="[ '#allVideos' + hendelse.getId() + innslag.getId() ]" aria-expanded="false" :aria-controls="[ 'allVideos' + hendelse.getId() + innslag.getId() ]">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M16.293 9.293 12 13.586 7.707 9.293l-1.414 1.414L12 16.414l5.707-5.707z"></path></svg>
                                            <p>{{ innslag.getAntallFilmer() }} film{{ innslag.getAntallFilmer() > 1 ? 'er' : '' }}</p>
                                        </button>
                                    </div>
                                    <!-- Filmer i innslag -->
                                    <div class="videos collapse" :id="[ 'allVideos' + hendelse.getId() + innslag.getId() ]">
                                        <div class="inner-videos flex-row">
                                            <!-- Last opp film -->
                                            <div class="col-xs-4 col-sm-3 upload-video-for-hendelse innslag">
                                                <!--- Upload video -->
                                                <button v-show="!innslag.isUploadOpen" @click="showUpload(innslag)" class="round-style-button mini open-upload">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: #4f46e5;transform: ;msFilter:;"><path d="M15 2.013H9V9H2v6h7v6.987h6V15h7V9h-7z"></path></svg>
                                                </button>
                                                <upload-video v-show="innslag.isUploadOpen" ref="uploadVideo-reportasje" :onUploadCallback="onUpload" :erReportasje="false" :innslagId="innslag.getId()" :miniVersion="true" />
                                            </div>
                                            
                                            <div  class="col-xs-3" v-for="(video, videoIndex) in innslag.getVideos()" :key="videoIndex">
                                                <video-vue :onDeleteCallback="onVideoDelete" :onPublishCallback="onVideoPublish" :innslag="innslag" :video="video" :mini="false" />
                                            </div>
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
import Video from '../objects/Video';


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
        // Hendelser ble fetcha og trenger ikke å gjøre det igjen
        if(this.hendelser.length > 0) {
            return;
        }

        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'getHendelser',
            arrangementId: this.arrangementId 
        };
        
        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        if(response) {
            this.hendelser = [];

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
                        innslag.type.name,
                        innslag.antallFilmer
                    );
                    innslags.push(innslagVideoObj);
                }
                hendelseObj.addInnslags(innslags);
                this.hendelser.push(hendelseObj);
            }
        }

        return response;
    }

    public showUpload(hendelse : Hendelse) {
        hendelse.isUploadOpen = true;
    }

    public toggleHendelse(hendelse : Hendelse) {
        hendelse.hendelseOpen = !hendelse.hendelseOpen;
    }

    public onUpload(response : any, innslagId : string) {
        for(var hendelse of this.hendelser) {
            for(var innslag of hendelse.getInnslags()) {
                if(innslag.getId() == innslagId) {
                    innslag.fetchVideos();
                }
            }
        }
    }

    public onVideoDelete(response : any, video : Video, innslag : InnslagVideo) {
        innslag.fetchVideos();
    }

    public onVideoPublish(response : any, video : Video, innslag : InnslagVideo) {
        innslag.fetchVideos()
    }

}
    

// Registrering av komponenten
Vue.component('video-hendelser', VideoHendelser);
</script>

<style>

</style>
