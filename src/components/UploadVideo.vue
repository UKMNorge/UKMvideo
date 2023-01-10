<template>
    <div>
      <p>HER KOMMER TEKSTEN</p>
    </div>
</template>


<script lang="ts">
// Import av Vue
import { Vue, Component, Prop } from "vue-property-decorator";
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
declare var ajaxurl: string; // Kommer fra global


@Component
export default class UploadVideo extends Vue {
    // data som kommer fra initialisering av komponenten. Eks. <test-komponent :keys="['a', 'b']" :values="['value1', 'value2']"></test-komponent>
    @Prop() keys!: {navn : string, method : string}[];
    @Prop() values!: any[];
    private spaInteraction = new SPAInteraction(null, ajaxurl);


    public method() : void {

    }

    public async uploadVideo() {
        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'uploadVideo',
        };

        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        console.log(response);
        return response;
    }

    public init() : void {
      this.uploadVideo();
    }
}

// Registrering av komponenten
Vue.component('test-komponent', UploadVideo);
</script>

<style>
  
</style>
