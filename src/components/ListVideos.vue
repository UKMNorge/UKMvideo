<template>
    <div>
        <div class="all-videos">
            <div v-for="(video, index) in videos" :key="index" class="video-item">
                <a :href="video.getPreview()" class="inner">
                    <div class="thumbnail-div">
                        <img :src="video.getThumbnail()"  width="100%" height="auto">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M7 6v12l10-6z"></path></svg>
                        <span class="duration">{{ video.getDurationStr() }}</span>
                    </div>
                    <h4 class="title">Title her</h4>
                </a>
            </div>
        </div>
    </div>
</template>


<script lang="ts">
// Import av Vue
import { Vue, Component, Prop } from "vue-property-decorator";
import Video from '../objects/Video';
import { SPAInteraction } from 'ukm-spa/SPAInteraction';

declare var ajaxurl: string; // Kommer fra global

@Component
export default class ListVideos extends Vue {
    // data som kommer fra initialisering av komponenten. Eks. <test-komponent :keys="['a', 'b']" :values="['value1', 'value2']"></test-komponent>
    public videos : Video[] = [];
    private spaInteraction = new SPAInteraction(null, ajaxurl);
    
    public mounted() {
        this.fetchAllVideos();
    }

    public async fetchAllVideos() {
        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'getVideos',
        };
        
        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        if(response.result && response.result.success == true) {
            for(var video of response.result.result) {
                
                var videoObj = new Video(
                    video.uid,
                    video.meta.filename,
                    '',
                    video.duration,
                    video.status.state,
                    video.preview
                )
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
    
}

// Registrering av komponenten
Vue.component('list-videos', ListVideos);
</script>

<style>
.all-videos .video-item .inner {
    border-radius: 20px;
    margin: 10px;
    text-decoration: none;
}
.all-videos .video-item .thumbnail-div {
    display: flex;
    flex-wrap: wrap;
    position: relative;
}
.all-videos .video-item .thumbnail-div .duration {
    position: absolute;
    right: 10px;
    bottom: 10px;
    color: #fff;
    background: #000;
    padding: 2px 5px;
    border-radius: 5px;
}
.all-videos .video-item .inner .title {
    margin-top: 20px !important;
    font-size: 15px;
    color: #444;
}
.all-videos .video-item .thumbnail-div img {
    border-radius: 20px;
    width: 100%;
    height: 10vw;
    object-fit: cover;
    box-shadow: 0px 0px 9px -1px #0000004f;
    transition: box-shadow .2s;
}
.all-videos .video-item .inner:hover .thumbnail-div img {
    box-shadow: 0px 0px 9px -1px #00000094;
    transition: box-shadow .2s;
}
.all-videos .video-item .thumbnail-div svg {
    fill: #0006 !important;
    position: absolute;
    height: 100px;
    width: 100px;
    right: 0;
    left: 0;
    top: 0;
    bottom: 0;
    margin: auto;
    opacity: 0;
    transition: opacity .2s;
}
.all-videos .video-item .inner:hover .thumbnail-div svg {
    opacity: 1;
    transition: opacity .2s;
}
* {
  box-sizing: border-box;
}

/* Create three equal columns that floats next to each other */
.all-videos .video-item {
    float: left;
    width: 25%;
    padding: 10px;
}

/* Clear floats after the columns */
.all-videos:after {
    content: "";
    display: table;
    clear: both;
}
</style>
