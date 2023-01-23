<template>
    <div class="main-upload-video-div">
        <div v-if="!uploadStarted">
            <div class="dropzone-container" @dragover="dragover" @dragleave="dragleave()" @drop="drop($event)">
                <input type="file" name="file" id="fileInput" class="hidden-input" @change="onChange" ref="file" accept="video/mp4,video/x-m4v,video/*"/>
                <label for="fileInput" class="file-label">
                    <div v-if="isDragging">Slipp her for å laste opp filmen</div>
                    <div v-else>Slipp filmen her eller <u>klikk</u> for å laste opp.</div>
                </label>
            </div>
        </div>

        <div>
            <progress-bar ref="progressBar" :visible="uploadStarted" />
        </div>
    </div>
</template>


<script lang="ts">
// Import av Vue
import { Vue, Component, Prop } from "vue-property-decorator";
import * as tus from "tus-js-client";
import ProgressBar from './ProgressBar.vue';


declare var ajaxurl: string; // Kommer fra global


@Component
export default class UploadVideo extends Vue {
    // data som kommer fra initialisering av komponenten. Eks. <test-komponent :keys="['a', 'b']" :values="['value1', 'value2']"></test-komponent>
    @Prop() keys!: {navn : string, method : string}[];
    @Prop() values!: any[];
    public isDragging = false
    public file : any = null;
    public uploadStarted = false;

    public components = [ProgressBar];


    
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

   
    public uploadVideoTUS(event? : any) {
        var _this = this;
        
        if(event) {
            var file = event.target.files[0];
        } else {
            var file = this.file;
        }

        if(!file) {
            alert('Filen finnes ikke!');
        }

        this.uploadStarted = true;

        // Create a new tus upload
        var upload = new tus.Upload(file, {
            endpoint: "https://ukm.dev/2023-deatnu-tana-deatnu-tananvcfghfhfj/wp-admin/admin-ajax.php?action=UKMvideo_ajax&subaction=getCloudflareUrl",
            retryDelays: [0, 3000, 5000, 10000, 20000],
            metadata: {
                'filename': file.name,
                'filetype': file.type,
                'innslag': 'not-yet'

            },
            onError: function(error) {
                console.log("Failed because: " + error)
            },
            onProgress: function(bytesUploaded, bytesTotal) {
                var percentage = (bytesUploaded / bytesTotal * 100).toFixed(2);

                // Kaller ProgressBar fra html elementet med referanse
                (<ProgressBar>_this.$refs['progressBar']).update(parseInt(percentage));
            },
            onSuccess: function() {
                console.log("Download %s from %s", (<any>upload.file).name, upload.url)
                _this.uploadStarted = false;
            }
        })

        // Check if there are any previous uploads to continue.
        upload.findPreviousUploads().then(function (previousUploads) {
            // Found previous uploads so we select the first one. 
            if (previousUploads.length) {
                upload.resumeFromPreviousUpload(previousUploads[0])
            }

            // Start the upload
            upload.start()
        })
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
    height: 200px;
    justify-content: center;
    text-align: center;
    margin-top: 50px;
}

.dropzone-container {
    padding: 4rem;
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    position: relative;
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
</style>