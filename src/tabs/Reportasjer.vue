<template>
    <div>
        <div class="container as-container as-margin-top-space-4 as-margin-bottom-space-4">
            <permanent-notification :typeNotification="'info'" :tittel="notificationTitle" :description="notificationDescription" />
            <div class="as-display-flex">
                <button class="as-margin-auto as-btn-simple primary" @click="showHideNotifiction()">Åpne beskjed</button>
            </div>
        </div>
        
        <div v-show="showNotification" class="container container-as as-card-1 as-padding-space-4 as-padding-left-space-5 as-padding-right-space-5">
            <h3 class="as-margin-top-space-2">Anbefalinger for videoopplastinger?</h3>
            <ul>
                <li>MP4-containers, AAC-audio codec, H264-video codec, 30 eller lavere bilder per sekund</li>
                <li>moov-atom bør være i starten av filen (Fast Start)</li>
                <li>H264 progressiv skanning (ingen interlacing)</li>
                <li>H264 high profile</li>
                <li>Closed GOP</li>
                <li>Innholdet bør kodes og lastes opp i samme bildefrekvens som det ble opptatt</li>
                <li>Mono eller Stereo lyd (Stream vil blande lydspor med mer enn 2 kanaler ned til stereo)</li>
            </ul>
            <h3>Nedenfor er bitrate-anbefalinger for koding av nye videoer for Stream:</h3>
            <table>
                <tr>
                    <th><b>Resolution</b></th>
                    <th><b>Recommended bitrate</b></th>
                </tr>
                <tr>
                    <td>1080p</td>
                    <td>8 Mbps</td>
                </tr>
                <tr>
                    <td>720p</td>
                    <td>4.8 Mbps</td>
                </tr>
                <tr>
                    <td>480p</td>
                    <td>2.4 Mbps</td>
                </tr>
                <tr>
                    <td>360p</td>
                    <td>1 Mbps</td>
                </tr>
            </table>
        </div>
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
import UploadVideo from "../components/UploadVideo.vue";
import ListVideos from "../components/ListVideos.vue";
import PermanentNotificationLocal from "../components/PermanentNotificationLocal.vue";

declare var ajaxurl: string; // Kommer fra global

@Component
export default class Reportasjer extends Vue {
    private listVideos : ListVideos = new ListVideos;
    public showNotification : boolean = false;
    public notificationTitle = '';
    public notificationDescription = '';

    components = {
        UploadVideo,
        ListVideos,
        PermanentNotificationLocal,
    }

    public mounted() {
        this.notificationTitle = 'Viktig beskjed';
        this.notificationDescription = 'Trykk knappen under for veiledning for optimalisering av videoopplastinger';
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

    public showHideNotifiction() {
        this.showNotification = !this.showNotification;
    }
}
    

// Registrering av komponenten
Vue.component('video-reportasjer', Reportasjer);
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
