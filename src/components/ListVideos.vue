<template>
    <div>
        <div class="all-videos">
            <div v-for="(video, index) in videos" :key="index" class="video-item">
                <div class="inner">
                    <div class="thumbnail-div">
                        <img :src="video.getThumbnail()"  width="100%" height="auto">
                        <span class="duration">{{ video.getDurationStr() }}</span>
                    </div>
                    <h4 class="title">Title her</h4>
                </div>
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
                    video.status.state
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
}
.all-videos .video-item .thumbnail-div img {
    border-radius: 20px;
    width: 100%;
    height: 10vw;
    object-fit: cover;
    box-shadow: 0px 0px 16px -1px #00000021;
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
