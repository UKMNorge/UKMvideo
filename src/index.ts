import Vue from "vue";
import TilbakemeldingerKomponent from "./components/Tilbakemeldinger.vue";

let v = new Vue({
    el: "#app",
    data: { 
        name: "World",
        activeTab : 'deltabruk'
    },
    
    components: {
        TilbakemeldingerKomponent,
    },

    mounted : function() {
    
    },

    methods : {
        // Open tab
        velgKomponent: function() {
            var tilbakemeldinger = (<TilbakemeldingerKomponent>this.$refs['tilbakemeldinger']);
        }
    },

    template: /*html*/`
    <div>    
        <div>
            <tilbakemeldinger-komponent ref="tilbakemeldinger" :name="name" :initialEnthusiasm="5" />
        </div>

    </div>

    `
});
