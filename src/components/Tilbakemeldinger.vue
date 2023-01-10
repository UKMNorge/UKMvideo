<!-- This is an alternative way to define the Hello component using decorators -->
<template>
    <div v-if="initialized">
        
    </div>
</template>


<script lang="ts">
import { Vue, Component, Prop } from "vue-property-decorator";
import TabInterface from '../interfaces/testInterface';
import TestKomponent from "./TestKomponent.vue";

@Component
export default class TilbakemeldingerKomponent extends Vue implements TabInterface {
    @Prop() name!: string;

    public components = [TestKomponent];


    public initialized : boolean = false;
    
    public init() : void {

    }
}
</script>

<style>

</style>
