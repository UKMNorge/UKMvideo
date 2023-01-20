<template>
    <div>
        <div class="all-videos">
            <div v-for="(video, index) in videos" :key="index" class="video-item">
                <div class="thumbnail-div">
                    <img :src="video.getThumbnail()"  width="100%" height="auto">
                </div>
                <h4 clas="title">Title her</h4>
                <p>Duration: {{ video.getDuration() }}</p>
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
                    video.maxDurationSeconds,
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
.all-videos {
    display: flex;
}
.all-videos .video-item {
    background: #fff;
    border-radius: 20px;;
    width: 33%;
    margin: 20px 50px;
    padding: 20px;
}
.all-videos .video-item .thumbnail-div {
    display: flex;
}
.all-videos .video-item .thumbnail-div img {
    border-radius: 10px;
    height: 150px;
    width: auto;
    margin: auto;
}

</style>
