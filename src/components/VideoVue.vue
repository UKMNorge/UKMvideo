<template>
    <div>
        <div class="vue-video-item" :class="{'mini' : mini}">
            <a v-if="!video.isPendingUpload()" :href="video.getPreview()" class="video-vue">
                <div class="thumbnail-div">
                    <div class="right-buttons">
                        <button @click="deleteVideo($event, video)" class="remove-button btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 2 24 20" style="fill: #fff;transform: ;msFilter:;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm4.207 12.793-1.414 1.414L12 13.414l-2.793 2.793-1.414-1.414L10.586 12 7.793 9.207l1.414-1.414L12 10.586l2.793-2.793 1.414 1.414L13.414 12l2.793 2.793z"></path></svg>
                        </button>
                    </div>
                    <div v-if="!video.isLagret()" class="video-labels">
                        <span class="label-item">Filmen er ikke publisert</span>
                    </div>
                    <img :src="video.getThumbnail()"  width="100%" height="auto">
                    <svg class="play-svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M7 6v12l10-6z"></path></svg>
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
                    <div class="right-buttons">
                        <button @click="deleteVideo($event, video)" class="remove-button btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 2 24 20" style="fill: #fff;transform: ;msFilter:;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm4.207 12.793-1.414 1.414L12 13.414l-2.793 2.793-1.414-1.414L10.586 12 7.793 9.207l1.414-1.414L12 10.586l2.793-2.793 1.414 1.414L13.414 12l2.793 2.793z"></path></svg>
                        </button>
                    </div>
                    <p>Filmen er ikke lastet opp</p>
                </div>
            </a>
            <div v-if="!video.isLagret() && !video.isPendingUpload()" class="publish-info">
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
import { interactionVue } from './interaction';
import $ from "jquery";


declare var ajaxurl: string; // Kommer fra global

@Component
export default class VideoVue extends Vue {
    @Prop() video! : Video;
    @Prop() mini! : boolean;

    private spaInteraction = new SPAInteraction(interactionVue, ajaxurl);
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

    public deleteVideo(e : Event, video : Video) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        console.log(video);

        // It is waiting list
        var buttons = [{
            name : 'Slett filmen',
            class : "",
            callback : async ()=> {
                try{
                    var data = {
                        action: 'UKMvideo_ajax',
                        subaction: 'deleteVideo',
                        cfId: video.getId()
                    };

                    var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                    if(response) {
                        location.reload();
                    }

                    return response;
                    
                } catch(err) {
                    console.error(err);
                }
            }}
        ];

        this.spaInteraction.showDialog('Slette filmen', 'Vil du slette '+ video.getTitle() +' permanent?', buttons);
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
.vue-video-item .thumbnail-div svg.play-svg {
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
.vue-video-item .video-vue:hover .thumbnail-div svg.play-svg {
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

.vue-video-item .thumbnail-div .right-buttons {
    position: absolute;
    width: 100%;
    padding: 10px;
    display: flex;
    visibility: hidden;
    opacity: 0;
    transition: visibility .2s, opacity .2s;
    z-index: 5;
}
.vue-video-item .thumbnail-div:hover .right-buttons {
    visibility: visible;  
    opacity: 1;
    transition: visibility .2s, opacity .2s;
}
.vue-video-item .thumbnail-div .right-buttons .btn {
    margin: auto;
    margin-right: 0;
    background: #0000;
    border-radius: 50%;
    display: flex;
    padding: 0;
}
</style>
