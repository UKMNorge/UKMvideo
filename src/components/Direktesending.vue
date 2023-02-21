<template>
    <div>
        <div v-if="loaded" class="aktivator-div">
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
import $ from "jquery";
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import Hendelse from "../objects/Hendelse";
import InnslagVideo from "../objects/InnslagVideo";
import VideoVue from './VideoVue.vue';
import Video from '../objects/Video';
import { ToggleButton } from 'vue-js-toggle-button'
 
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

    components = {
        UploadVideo,
        VideoVue
    }

    public async init() {
        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'getSingleLivestream',
        };

        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        if(response.current_link) {
            this.currentLink = response.current_link;
            this.aktivert = true;
        }

        // det er ikke en liste
        var videos = response.videos.result;
        for(var video of videos) {
            console.log(video);
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
    }

    public async newLivestream() {
        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'createLivestreamInput',
        };

        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        return response;
    }

    public onChangeEventHandler(event : any) {
        this.aktivert = event.value;
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
</style>
