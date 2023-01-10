import Vue from "vue";
import UploadVideo from "./components/UploadVideo.vue";
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import { Director } from 'ukm-spa/Director';

export function uploadVideoTabs() {
    new Vue({
        el: "#uploadVueApp",
        data: { 
            name: "World",
            activeTab : 'upload'
        },
        
        components: {
            UploadVideo,
        },
    
        mounted : function() {
            this.openTab(this.activeTab);
            console.log(SPAInteraction);
            console.log(Director);
        },
    
        methods : {
            // Open tab
            openTab: function(tabRef : string) : void {
                this.activeTab = tabRef;
                var tilbakemeldinger = (<UploadVideo>this.$refs[tabRef]);
                tilbakemeldinger.init();
            }
        },
    
        template: /*html*/`
        <div>    
            <div>
                <div class="tab-items">
                    <div class="tab-item">
                        <button :class="{'active' : activeTab == 'upload'}" @click="openTab('upload');">Bruk</button>
                    </div>
                </div>
    
                
                <div class="tab-content tabs">
                    <div v-show="activeTab == 'upload'">
                        <test-komponent ref="upload" :name="name" :initialEnthusiasm="5" />
                    </div>
                </div>
            </div>
    
        </div>
    
        `
    });
}