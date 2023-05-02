<template>
    <div>
        <div>
            <div class="upload-reportasje-outside">
                <div class="upload-reportasje">
                    <upload-video ref="uploadVideo-reportasje" :onUploadCallback="onUpload" :erReportasje="true" />
                </div>
            </div>
            <div>
                <list-videos ref="allVideos-reportasje" :erReportasje="true" />
            </div>
        </div>
    </div>
</template>


<script lang="ts">
// Import av Vue
import { Vue, Component, Prop } from "vue-property-decorator";
import UploadVideo from "./UploadVideo.vue";
import ListVideos from "./ListVideos.vue";

declare var ajaxurl: string; // Kommer fra global

@Component
export default class VideoReportasjer extends Vue {
    private listVideos : ListVideos = new ListVideos;


    components = {
        UploadVideo,
        ListVideos
    }
    public init() {
        console.warn('here');
        var vueObjectUV = (<UploadVideo>this.$refs['uploadVideo-reportasje']);
        var vueObjLV = (<ListVideos>this.$refs['allVideos-reportasje']);

        vueObjectUV.init();
        vueObjLV.init();

        this.listVideos = vueObjLV;
    }

    public onUpload(response : any, innslagId : string) {
        this.listVideos.fetchAllVideos()
    }
}
    

// Registrering av komponenten
Vue.component('video-reportasjer', VideoReportasjer);
</script>

<style>  
.upload-reportasje-outside {
    margin: 100px 0;
    display: flex;
}  
.upload-reportasje-outside .upload-reportasje {
    margin: auto;
    max-width: 600px;
}
</style>
