<template>
    <div>
        <div class="vue-video-item" :class="{'mini' : mini}">
            <a v-if="!video.isPendingUpload()" :href="video.getPreview()" class="video-vue">
                <div class="thumbnail-div">
                    <div v-if="!video.isLagret()" class="video-labels">
                        <span class="label-item">Filmen er ikke publisert</span>
                    </div>
                    <img :src="video.getThumbnail()"  width="100%" height="auto">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M7 6v12l10-6z"></path></svg>
                    <span class="duration">{{ video.getDurationStr() }}</span>
                    <div v-if="!video.isReady()" class="processing" :style="{ 'width': video.getProcessingProgress() + '%' }"><span>{{ video.getProcessingProgress() }}%</span></div>
                </div>
                <div class="text-info">
                    <h4 class="title">{{ video.getTitle() }}</h4>
                    <span class="description">{{ video.getDescription() }}</span>
                </div>
            </a>
            <a v-else class="video-vue">
                <div class="thumbnail-div not-available">
                    <p>Videoen er ikke lastet opp</p>
                </div>
            </a>
            <div v-if="!video.isLagret()" class="publish-info">
                <div v-if="showPublishInfo">
                    <input v-model="tittel" class="as-input-style input" placeholder="navn" :class="ugyldigTittel && tittel.length < 1 ? 'error' : ''"/>
                    <textarea v-model="beskrivelse" class="as-input-style input" placeholder="beskrivelse"></textarea>
                </div>
                <button @click="publishVideo()" class="as-botton-style-simple publiser">Publiser</button>
            </div>
        </div>
    </div>
</template>


<script lang="ts">
// Import av Vue
import { Vue, Component, Prop } from "vue-property-decorator";
import Video from '../objects/Video';
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import $ from "jquery";


declare var ajaxurl: string; // Kommer fra global

@Component
export default class VideoVue extends Vue {
    @Prop() video! : Video;
    @Prop() mini! : boolean;

    private spaInteraction = new SPAInteraction(null, ajaxurl);
    public tittel : string = '';
    public beskrivelse : string = '';
    public showPublishInfo = false;
    public ugyldigTittel = false;

    public async publishVideo() {
        if(!this.showPublishInfo) {
            this.showPublishInfo = true;
            return;
        }
        if(this.tittel.length < 1) {
            this.ugyldigTittel = true;
            return;
        }
        
        var innslagId = this.video.getInnslagId();

        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'saveUploadedVideo',
            tittel: this.tittel,
            description: this.beskrivelse,
            cloudFlareId: this.video.getId(),
            innslagId: innslagId,
            erReportasje: innslagId ? false : true
        };
        
        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        // Midlertidig løsning er å refreshe netsiden
        location.reload();

        return response;
    }
}

// Registrering av komponenten
Vue.component('video-vue', VideoVue);
</script>

<style>
.vue-video-item {
    border-radius: 20px;
    text-decoration: none;
    margin: 10px 0;
}
.vue-video-item .thumbnail-div.not-available {
    border-radius: 20px;
    width: 100%;
    height: 10vw;
    object-fit: cover;
    box-shadow: 0px 0px 9px -1px #0000004f;
    transition: box-shadow .2s;
    background: #000;
    display: flex;
}
.vue-video-item .thumbnail-div.not-available p {
    margin: auto;
    font-size: 15px;
    color: #fff;
}
.vue-video-item .thumbnail-div .processing {
    position: absolute;
    background: #ffffff8c;
    height: 100%;
    width: 20%;
    display: flex;
    min-width: 20% !important;
}
.vue-video-item .thumbnail-div .processing span {
    margin: auto;
    font-size: 15px;
    color: #fff;
    background: #000;
    padding: 4px;
    border-radius: 5px;
}
.vue-video-item .thumbnail-div .duration {
    position: absolute;
    right: 10px;
    bottom: 10px;
    color: #fff;
    background: #000;
    padding: 2px 5px;
    border-radius: 5px;
}
.vue-video-item .text-info .title {
    margin-top: 16px !important;
    margin-bottom: 5px;
    font-size: 15px;
    color: #444;
}
.vue-video-item .text-info .description {
    font-weight: 200;
    font-style: normal;
    color: #6a6a6a;
}
.vue-video-item .text-info .description,
.vue-video-item .text-info .title {
    word-break: break-all;
}
.vue-video-item.mini .text-info .title {
    margin-top: 5px !important;
}
.vue-video-item.mini .text-info {
    margin-left: 10px;
}
a.video-vue{
    text-decoration: none !important;
}

.vue-video-item .thumbnail-div img {
    border-radius: 20px;
    width: 100%;
    height: 10vw;
    object-fit: cover;
    box-shadow: 0px 0px 9px -1px #0000004f;
    transition: box-shadow .2s;
}
.vue-video-item .video-vue:hover .thumbnail-div img {
    box-shadow: 0px 0px 9px -1px #00000094;
    transition: box-shadow .2s;
}
.vue-video-item .thumbnail-div svg {
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
.vue-video-item .video-vue:hover .thumbnail-div svg {
    opacity: 1;
    transition: opacity .2s;
}
.vue-video-item.mini a {
    display: flex;
}
.vue-video-item .thumbnail-div {
    display: flex;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
    border-radius: 20px;
}
.vue-video-item.mini .thumbnail-div {
    width: 10vw;
    min-width: 10vw;
    height: 5vw;
}
.vue-video-item.mini .thumbnail-div img,
.vue-video-item.mini .thumbnail-div.not-available {
    height: 5vw;
}
.video-labels {
    width: 100%;
    position: absolute;
    padding: 10px;
    display: flex;
}
.video-labels .label-item {
    background: #000000b5;
    color: #fff;
    padding: 2px 8px;
    border-radius: 6px;
    margin: auto;
    margin-right: 0;
}
.vue-video-item .thumbnail-div .video-labels .label-item {
    font-size: 12px;
}
.publish-info {
    margin-top: -10px;
}
.publish-info .input {
    margin-top: 5px;
}
.vue-video-item.mini .publish-info {
    margin-top: 10px;
}
button.publiser {
    width: 100%;
    margin-top: 5px;
}
.vue-video-item.mini button.publiser {
    margin-bottom: 20px;
    width: 10vw;
}
.publish-info .input.error {
    border-color: #f00;
    box-shadow: 0px 0px 8px 4px #ff00002e;
}
</style>
