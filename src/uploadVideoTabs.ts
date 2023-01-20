import Vue from "vue";
import UploadVideo from "./components/UploadVideo.vue";
import { SPAInteraction } from 'ukm-spa/SPAInteraction';

export function uploadVideoTabs() {
    new Vue({
        el: "#uploadVueApp",
        
        components: {
            UploadVideo,
        },
    
        mounted : function() {
           
        },
    
        methods : {
            
        },
    
        template: /*html*/`
        <div>    
            <div>                
                <div class="tab-content tabs">
                    <div v-show="activeTab == 'upload'">
                        <upload-video ref="upload" :name="name" :initialEnthusiasm="5" />
                    </div>
                    <div>
                        <p>Alle filmer:</p>
                    </div>
                </div>
            </div>
    
        </div>
    
        `
    });
}