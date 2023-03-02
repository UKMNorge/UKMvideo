<template>
    <div class="main-upload-video-div" :class="{'mini-version' : miniVersion}">
        <div v-if="!uploadStarted && !showSavingInfo">
            <div class="dropzone-container" @dragover="dragover" @dragleave="dragleave()" @drop="drop($event)">
                <input type="file" name="file" id="fileInput" class="hidden-input" @change="onChange" ref="file" accept="video/mp4,video/x-m4v,video/*"/>
                <label for="fileInput" class="file-label">
                    <div v-if="isDragging">Slipp her for å laste opp filmen</div>
                    <div v-else>Slipp filmen her eller <u>klikk</u> for å laste opp.</div>
                </label>
            </div>
        </div>

        <div>
            <progress-bar v-show="!showLoadingText" ref="progressBar" :uploadProgress="uploadProgress" :visible="uploadStarted" />
            <div class="vent-text" v-show="showLoadingText"><h3>Vi gjør filmen klar, vennligst vent!</h3></div>

            <div class="info-upload" v-show="showSavingInfo && !lagringCompleted">
                <input v-model="navn" class="as-input-style input" :class="ugyldigNavn && navn.length < 1 ? 'error' : ''" placeholder="navn"/>
                <textarea v-model="beskrivelse" class="as-input-style input" placeholder="beskrivelse"></textarea>
                <button @click="lagre()" class="as-botton-style-simple">Lagre filmen</button>
            </div>
        </div>
    </div>
</template>


<script lang="ts">
// Import av Vue
import { Vue, Component, Prop } from "vue-property-decorator";
import * as tus from "tus-js-client";
import ProgressBar from './ProgressBar.vue';
import $ from "jquery";
import { SPAInteraction } from 'ukm-spa/SPAInteraction';

// Promise finnes i Javascript
declare var Promise: any;



declare var ajaxurl: string; // Kommer fra global


@Component
export default class UploadVideo extends Vue {
    // data som kommer fra initialisering av komponenten. Eks. <test-komponent :keys="['a', 'b']" :values="['value1', 'value2']"></test-komponent>
    @Prop() keys!: {navn : string, method : string}[];
    @Prop() values!: any[];
    @Prop() erReportasje! : boolean;
    @Prop() miniVersion! : boolean;
    @Prop() innslagId! : string;
    @Prop() callbackLagre! : ()=>{};


    private spaInteraction = new SPAInteraction(null, ajaxurl);

    public cloudFlareId : string = '';

    public navn = '';
    public ugyldigNavn = false;
    public beskrivelse = '';

    public isDragging = false
    public file : any = null;
    public uploadStarted = false;
    public showLoadingText = false;
    public showSavingInfo = false;
    public lagringCompleted = false;
    
    public uploadProgress : number = 0;

    public arrangementId : string = '';

    public components = [ProgressBar];

    public init() {
        var arrangementId = $('#vueArguments').attr('arrangementId');
        if(arrangementId) {
            this.arrangementId = arrangementId;
        }
        else {
            console.error('arrangementId må være definert');
        }
    }
    
    public dragover(e : any) {
        e.preventDefault();
        this.isDragging = true;
    }
    
    public dragleave() {
        this.isDragging = false;
    }
    
    public drop(e : any) {
        e.preventDefault();
        
        this.file = e.dataTransfer.files[0]
        this.isDragging = false;
        
        this.uploadVideoTUS();
    }

    public onChange(e : any) {
        e.preventDefault();

        this.file = e.target.files[0];

        this.uploadVideoTUS();
    }

    public async uploadVideoTUS() {
        var _this = this;
        var id = this.erReportasje ? this.arrangementId : this.innslagId;
        
        this.uploadProgress = 0;
        this.cloudFlareId = '';

        var file = this.file;

        if(!file) {
            alert('Filen finnes ikke!');
        }

        this.uploadStarted = true;
        var videoLength = await this.getVideoDuration(file);
        this.showSavingInfo = true;

        // Create a new tus upload
        var upload = new tus.Upload(file, {
            endpoint: ajaxurl + '?action=UKMvideo_ajax&subaction=getCloudflareUrl&videoLength=' + videoLength + '&' + (this.erReportasje ? 'arrangement_id' : 'innslag_id') + '=' + id,
            retryDelays: [0, 3000, 5000, 10000, 20000],
            chunkSize: 150 * 1024 * 1024,
            onError: function(error) {
                console.log("Failed because: " + error)
            },
            onProgress: function(bytesUploaded, bytesTotal) {
                var percentage = (bytesUploaded / bytesTotal * 100).toFixed(2);

                // Kaller ProgressBar fra html elementet med referanse
                _this.uploadProgress = parseInt(percentage);
                (<ProgressBar>_this.$refs['progressBar']).update();
                
                if(parseInt(percentage) == 100) {
                    _this.showLoadingText = true;
                }

            },
            onSuccess: function() {
                _this.uploadStarted = false;
                _this.showLoadingText = false;
            },
            onAfterResponse: function (req, res) {
                var cloudFlareId = res.getHeader('stream-media-id');
                if(cloudFlareId) {
                    _this.cloudFlareId = cloudFlareId;
                }

                // Hvis brukeren velger å lagre videoen uten at videoen er lastet opp, da kalles lagre her
                if(_this.lagringCompleted) {
                    _this.saveInnslagVideo();
                }
            }
        })

        // Check if there are any previous uploads to continue.
        upload.findPreviousUploads().then(function (previousUploads) {
            // Found previous uploads so we select the first one. 
            // if (previousUploads.length) {
            //     upload.resumeFromPreviousUpload(previousUploads[0])
            // }

            // Start the upload
            upload.start();
        })

        return videoLength;
    }

    // Hent video lengde fra fil
    private async getVideoDuration(file : File) {
        return new Promise((resolve : any, reject : any) => {
            const reader = new FileReader();
            reader.onload = () => {
                const media = new Audio((<any>reader).result);
                media.onloadedmetadata = () => resolve(media.duration);
            };
            reader.readAsDataURL(file);
            reader.onerror = error => reject(error);
        });
    }

    private async saveInnslagVideo() {
        if(this.navn.length < 1) {
            this.lagringCompleted = false;
            this.ugyldigNavn = true;
            return;
        }
        this.lagringCompleted = true;


        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'saveUploadedVideo',
            tittel: this.navn,
            description: this.beskrivelse,
            cloudFlareId: this.cloudFlareId,
            innslagId: this.innslagId,
            erReportasje: this.erReportasje
        };
        
        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        // Midlertidig løsning er å refreshe netsiden
        location.reload();

        if(response && this.callbackLagre) {
            this.callbackLagre();
        }
        this.reset();

        return response;    
    }

    private reset() {
        this.cloudFlareId = '';
        this.navn = '';
        this.beskrivelse = '';
        this.isDragging = false
        this.file = null;
        this.uploadStarted = false;
        this.showLoadingText = false;
        this.showSavingInfo = false;
        this.lagringCompleted = false;
        this.uploadProgress = 0;
        this.ugyldigNavn = false;
    }

    public async lagre() {
        if(this.navn.length > 0) {
            this.lagringCompleted = true;
        }

        // Hvis cloudflareId er ikke generert ennå, returner
        if(!this.cloudFlareId) {
            return;
        }
        var res = await this.saveInnslagVideo();
        return res;
    }

}

// Registrering av komponenten
Vue.component('upload-video', UploadVideo);
</script>

<style>
.main-upload-video-div {
    display: flex;
    flex-grow: 1;
    align-items: center;
    min-height: 200px;
    justify-content: center;
    text-align: center;
    margin-top: 50px;
    margin-bottom: 30px;
}
.main-upload-video-div.mini-version {
    margin: 10px 0;
    height: auto;
}
.main-upload-video-div.mini-version .dropzone-container {
    padding: 15px;
}    
.main-upload-video-div.mini-version .dropzone-container .file-label {
    font-size: 17px;
}

.dropzone-container {
    padding: 4rem;
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    position: relative;
    border-radius: 15px;
    border: solid 1px #4f46e5;
    box-shadow: 0px 0px 13px -4px #0000003d;
    transition: .2s;
}
.dropzone-container:hover {
    background: #4f46e514;
    transition: .2s;
}
.dropzone-container input {
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    height: 100%;
}

.hidden-input {
    opacity: 0;
    overflow: hidden;
    position: absolute;
    width: 1px;
    height: 1px;
}

.file-label {
    font-size: 20px;
    display: block;
    cursor: pointer;
}
.info-upload {
    display: grid;
}
.info-upload > * {
    margin: 10px;
    border: solid 1px #4f46e5;
    border-radius: 10px !important;
    font-size: 15px;
    width: 100%;
    height: auto;
}
.info-upload .input {
    padding: 5px;
}
.info-upload .input.error {
    border-color: #f00;
    box-shadow: 0px 0px 8px 4px #ff00002e;
}
.info-upload button {
    margin-bottom: 50px;
}
</style>
