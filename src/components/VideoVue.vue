<template>
    <div>
        <div class="vue-video-item" :class="{'mini' : mini}">
            <a v-if="!video.isPendingUpload()" :href="video.getPreview()" class="video-vue">
                <div class="thumbnail-div">
                    <img :src="video.getThumbnail()"  width="100%" height="auto">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M7 6v12l10-6z"></path></svg>
                    <span class="duration">{{ video.getDurationStr() }}</span>
                    <div v-if="!video.isReady()" class="processing" :style="{ 'width': video.getProcessingProgress() + '%' }"><span>{{ video.getProcessingProgress() }}%</span></div>
                </div>
                <h4 class="title">Title her</h4>
            </a>
            <a v-else class="video-vue">
                <div class="thumbnail-div not-available">
                    <p>Videoen er ikke lastet opp</p>
                </div>
            </a>
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
}

// Registrering av komponenten
Vue.component('video-vue', VideoVue);
</script>

<style>
.vue-video-item {
    border-radius: 20px;
    text-decoration: none;
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
.vue-video-item .title {
    margin-top: 20px !important;
    font-size: 15px;
    color: #444;
}
.vue-video-item .thumbnail-div img {
    border-radius: 20px;
    width: 100%;
    height: 10vw;
    object-fit: cover;
    box-shadow: 0px 0px 9px -1px #0000004f;
    transition: box-shadow .2s;
}
.vue-video-item:hover .thumbnail-div img {
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
.vue-video-item:hover .thumbnail-div svg {
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
    width: 50%;
    height: 5vw;
}
.vue-video-item.mini .thumbnail-div img,
.vue-video-item.mini .thumbnail-div.not-available {
    height: 5vw;
}
.vue-video-item.mini .title {
    margin-top: 5px !important;
    margin-left: 10px;
}
</style>
