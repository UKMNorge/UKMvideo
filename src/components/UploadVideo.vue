<template>
    <div>
      <input @change="uploadVideoTUS" id="videoFile" type="file">
      <p>Progress: {{ uploadProgress }}%</p>

      <button @click="uploadVideo()">Upload video with fetch</button>
      <button @click="testGetURL()">Test get url</button>
    </div>
</template>


<script lang="ts">
// Import av Vue
import { Vue, Component, Prop } from "vue-property-decorator";
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import * as tus from "tus-js-client";

declare var ajaxurl: string; // Kommer fra global


@Component
export default class UploadVideo extends Vue {
    // data som kommer fra initialisering av komponenten. Eks. <test-komponent :keys="['a', 'b']" :values="['value1', 'value2']"></test-komponent>
    @Prop() keys!: {navn : string, method : string}[];
    @Prop() values!: any[];
    public uploadProgress = '0';

    public method() : void {

    }

    public uploadVideoTUS(event : any) {
        var _this = this;

        var file = event.target.files[0];

        // Create a new tus upload
        var upload = new tus.Upload(file, {
            endpoint: "https://ukm.dev/2023-deatnu-tana-deatnu-tananvcfghfhfj/wp-admin/admin-ajax.php?action=UKMvideo_ajax&subaction=getCloudflareUrl",
            retryDelays: [0, 3000, 5000, 10000, 20000],
            metadata: {
                filename: file.name,
                filetype: file.type
            },
            onError: function(error) {
                console.log("Failed because: " + error)
            },
            onProgress: function(bytesUploaded, bytesTotal) {
                var percentage = (bytesUploaded / bytesTotal * 100).toFixed(2);
                _this.uploadProgress = percentage;

                console.log(bytesUploaded, bytesTotal, percentage + "%")
            },
            onSuccess: function() {
                console.log("Download %s from %s", (<any>upload.file).name, upload.url)
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

    public init() : void {

    }
}

// Registrering av komponenten
Vue.component('test-komponent', UploadVideo);
</script>

<style>
  
</style>
