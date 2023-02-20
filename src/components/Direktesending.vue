<template>
    <div>
        <div class="row">
            <div class="col-xs-12 col-md-5">
                <h3 class="bold" style="margin-bottom:0;">OPPSETT</h3>
                <button @click="newLivestream()">Opprett livestream</button>
            </div>
        </div>    
        <div class="row">
            <div v-if="currentLink">
                <iframe :src="currentLink + '/iframe'" style="border: none; position: absolute; top: 0; left: 0; height: 100%; width: 100%;" allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;" allowfullscreen="true">
                </iframe>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
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


import UploadVideo from "./UploadVideo.vue";

declare var ajaxurl: string; // Kommer fra global

@Component
export default class Direktesending extends Vue {
    private spaInteraction = new SPAInteraction(null, ajaxurl);
    public videos : Video[] = [];
    public currentLink = null;

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
        
        
    }

    public async newLivestream() {
        console.log('new livestream');
        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'createLivestreamInput',
        };

        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        return response;
    }

}
    
// Registrering av komponenten
Vue.component('direktesending', Direktesending);
</script>

<style>

</style>
