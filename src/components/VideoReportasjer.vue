<template>
    <div>
        <div>
            <div>
                <upload-video ref="uploadVideo-reportasje" :erReportasje="true" />
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
    }
}
    

// Registrering av komponenten
Vue.component('video-reportasjer', VideoReportasjer);
</script>

<style>    

</style>
