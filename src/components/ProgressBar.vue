<template>
    <div :class="{ visible : visible }" class="main-progress-bar">
        <div :id="chartId" class="donut-size">
            <div class="pie-wrapper">
                <span class="label">
                    <span class="num">0</span><span class="smaller">%</span>
                </span>
                <div class="pie">
                    <div class="left-side half-circle"></div>
                    <div class="right-side half-circle"></div>
                </div>
                <div class="shadow"></div>
            </div>
        </div>
    </div>
</template>


<script lang="ts">
// Import av Vue
import { Vue, Component, Prop } from "vue-property-decorator";
import $ from "jquery";
import {v4 as uuidv4} from 'uuid';

declare var ajaxurl: string; // Kommer fra global

@Component
export default class ProgressBar extends Vue {
    @Prop() visible!: boolean;
    public uploadProgress : number = 0;
    public chartId = uuidv4();

    
    public update(uploadProgress : number) {
        this.uploadProgress = uploadProgress;

        var el = this.chartId;
        var percent = this.uploadProgress;
        var donut = true;

        percent = Math.round(percent);
        if (percent > 100) {
            percent = 100;
        } else if (percent < 0) {
            percent = 0;
        }
        var deg = Math.round(360 * (percent / 100));

        if (percent > 50) {
            $(el + ' .pie').css('clip', 'rect(auto, auto, auto, auto)');
            $(el + ' .right-side').css('transform', 'rotate(180deg)');
        } else {
            $(el + ' .pie').css('clip', 'rect(0, 1em, 1em, 0.5em)');
            $(el + ' .right-side').css('transform', 'rotate(0deg)');
        }
        if (donut) {
            $(el + ' .right-side').css('border-width', '0.1em');
            $(el + ' .left-side').css('border-width', '0.1em');
            $(el + ' .shadow').css('border-width', '0.1em');
        } else {
            $(el + ' .right-side').css('border-width', '0.5em');
            $(el + ' .left-side').css('border-width', '0.5em');
            $(el + ' .shadow').css('border-width', '0.5em');
        }
        $(el + ' .num').text(percent);
        $(el + ' .left-side').css('transform', 'rotate(' + deg + 'deg)');
    }
}
    

// Registrering av komponenten
Vue.component('progress-bar', ProgressBar);
</script>

<style>    
    .main-progress-bar {
        display: none;
    }
    .main-progress-bar.visible {
        display: inline;
    }
    .main-progress-bar *,
    .main-progress-bar *:before,
    .main-progress-bar *:after {
      box-sizing: border-box; 
    }
    #percent {
      display: block;
      width: 160px;
      border: 1px solid #CCC;
      border-radius: 5px;
      margin: 50px auto 20px;
      padding: 10px;
      color: #1ABC9C;
      font-family: 'Lato', Tahoma, Geneva, sans-serif;
      font-size: 35px;
      text-align: center; }
    
    #donut {
      display: block;
      margin: 0px auto;
      color: #1ABC9C;
      font-size: 20px;
      text-align: center; }
    
    .main-progress-bar p {
      max-width: 600px;
      margin: 12px auto;
      font-weight: normal;
      font-family: sans-serif; }
    
    code {
      background: #FAFAFA;
      border: 1px solid #DDD;
      border-radius: 3px;
      padding: 0px 4px; }
    
    .donut-size {
      font-size: 12em; }
    
    .pie-wrapper {
      position: relative;
      width: 1em;
      height: 1em;
      margin: 0px auto; }
      .pie-wrapper .pie {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 100%;
        height: 100%;
        clip: rect(0, 1em, 1em, 0.5em); }
      .pie-wrapper .half-circle {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 100%;
        height: 100%;
        border: 0.1em solid #1abc9c;
        border-radius: 50%;
        clip: rect(0em, 0.5em, 1em, 0em); }
      .pie-wrapper .right-side {
        transform: rotate(0deg); }
      .pie-wrapper .label {
        position: absolute;
        top: 0.52em;
        right: 0.4em;
        bottom: 0.4em;
        left: 0.4em;
        display: block;
        background: none;
        border-radius: 50%;
        color: #7F8C8D;
        font-size: 0.25em;
        line-height: 2.6em;
        text-align: center;
        cursor: default;
        z-index: 2; }
      .pie-wrapper .smaller {
        padding-bottom: 20px;
        color: #BDC3C7;
        font-size: .45em;
        vertical-align: super; }
      .pie-wrapper .shadow {
        width: 100%;
        height: 100%;
        border: 0.1em solid #BDC3C7;
        border-radius: 50%; }


</style>
