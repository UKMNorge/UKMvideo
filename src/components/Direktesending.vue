<template>
    <div>
        <div v-if="loaded" class="aktivator-div">
            <div v-if="!aktivert" class="melding-aktivator">
                <div class="text">
                    <h4 class="">Direktesending er deaktivert</h4>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="m11.293 17.293 1.414 1.414L19.414 12l-6.707-6.707-1.414 1.414L15.586 11H6v2h9.586z"></path></svg>
                </div>
            </div>
            <div class="toggle-div">
                <toggle-button v-model="aktivert" @change="onChangeEventHandler($event)" 
                :width="70"
                :height="40"/>
            </div>
        </div>
        <div v-else>
            <p>Vennligst vent...</p>
        </div>
        <div v-if="aktivert">
            <div v-if="currentLink" class="col-xs-12 live-iframe-div as-box-style">
                <iframe :src="currentLink + '/iframe'" style="height: 600px; width: 100%;" allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;" allowfullscreen="true">
                </iframe>

                <!-- rtmps keys -->
                <div class="rtmps-div">
                    <h4 class="title">RTMPS URL: </h4>
                    <h4 class="as-box-style">{{ rtmpsUrl }}</h4>
                </div>
                <div class="rtmps-div">
                    <h4 class="title">RTMPS Key: </h4>
                    <h4 class="as-box-style">{{ rtmpsKey }}</h4>
                </div>
            </div>
            
            <div v-if="videos.length > 0" class="col-xs-12">
                <h2>
                    Tidligere direktesendinger
                </h2>
            </div>

            <div v-for="(video, index) in videos" :key="index" class="filmer col-xs-12">
                <div class="col-xs-4">
                    <video-vue :video="video" />
                </div>
            </div>
        </div>
    </div>
</template>


<script lang="ts">
// Import av Vue
import { Vue, Component, Prop } from "vue-property-decorator";
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import VideoVue from './VideoVue.vue';
import Video from '../objects/Video';
import { ToggleButton } from 'vue-js-toggle-button';
import $ from "jquery";

Vue.component('ToggleButton', ToggleButton)


import UploadVideo from "./UploadVideo.vue";

declare var ajaxurl: string; // Kommer fra global

@Component
export default class Direktesending extends Vue {
    private spaInteraction = new SPAInteraction(null, ajaxurl);
    public videos : Video[] = [];
    public currentLink = null;
    public aktivert = false;
    public loaded = false;
    public rtmpsUrl = null;
    public rtmpsKey = null;

    components = {
        UploadVideo,
        VideoVue
    }
    
    public async init() {
        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'getSingleLivestream',
        };

        this.domEvents();

        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        if(response.current_link) {
            this.currentLink = response.current_link;
        }

        this.aktivert = response.status == true;
        this.rtmpsUrl = response.rtmps_url;
        this.rtmpsKey = response.rtmps_key;

        // det er ikke en liste
        var videos = response.videos.result ? response.videos.result : [];
        for(var video of videos) {
            var videoObj = new Video(
                video.uid,
                video.meta.filename,
                video.thumbnail,
                video.duration,
                video.status.state,
                video.preview
            );
            
            this.videos.push(videoObj);
        }

        if(response) {
            this.loaded = true;
        }

        this.hendelserShowHide();
    }

    private hendelserShowHide() {
        if(this.aktivert) {
            $('#livestreamStatic').removeClass('hide');
            return;
        }
        $('#livestreamStatic').addClass('hide');
    }

    // Opprett eller hent eller set status til deaktivert via API
    private async setLivestreamStatus() {
        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'createLivestreamInput',
            status: this.aktivert
        };

        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        this.aktivert = response.status;
        this.currentLink = response.current_link;
        this.rtmpsUrl = response.rtmps_url;
        this.rtmpsKey = response.rtmps_key;
        this.hendelserShowHide();
        
        return response;
    }
    
    public onChangeEventHandler(event : any) {
        this.setLivestreamStatus();
    }
    
    private domEvents() {
        var _this = this;
        $("#livestreamHendelserForm").off('submit').on('submit', async function(e : any) {
            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            var data = form.serialize() + '&action=UKMvideo_ajax&subaction=livestreamHendleser';
            var response = await _this.spaInteraction.runAjaxCall('/', 'POST', data);
            
            if(response) {
                alert('Lagret!');
            }
            return response;
        });
    }
}
    
// Registrering av komponenten
Vue.component('direktesending', Direktesending);
</script>

<style>
.live-iframe-div {
    padding: 20px;
    margin: 50px;
    max-width: calc(100% - 100px);
}
.aktivator-div {
    display: flex;
}
.aktivator-div .toggle-div {
    margin: auto;
    margin-right: 40px;
    margin-top: 50px;
}
.rtmps-div {
    margin-top: 20px;
    display: flex;
}
.rtmps-div * {
    width: 100%;
    margin: 0;
}
.rtmps-div .title {
    width: 200px;
    font-weight: bold;
    margin: auto;
}
.melding-aktivator {
    width: 100%;
    display: flex;
}
.melding-aktivator .text {
    margin: auto;
    margin-right: 50px;
    margin-bottom: 5px;
    display: flex;
    color: #525252;
}
.melding-aktivator .text svg {
    margin: auto;
    margin-left: 10px;
    fill: #525252 !important;
}
</style>
