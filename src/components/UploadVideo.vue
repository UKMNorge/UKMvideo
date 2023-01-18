<template>
    <div>
      <input id="videoFile" type="file">
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
    private cloudflareUrl = '';

    public method() : void {

    }
    
    init() {

    }
}

// Registrering av komponenten
Vue.component('test-komponent', UploadVideo);
</script>

<style>
  
</style>
