import Vue from "vue";
import UploadVideo from "./components/UploadVideo.vue";
import ListVideos from "./components/ListVideos.vue";

export function uploadVideoTabs() {
    new Vue({
        el: "#uploadVueApp",
        
        components: {
            UploadVideo,
            ListVideos
        },
    
        mounted : function() {
           
        },
    
        methods : {
            
        },
    
        template: /*html*/`
        <div>    
            <div>                
                <div class="tab-content tabs">
                    <div>
                        <upload-video ref="upload" :name="name" :initialEnthusiasm="5" :erReportasje="true" />
                    </div>
                    <div>
                        <p>Alle filmer:</p>
                        <list-videos ref="alle-videoer" />
                    </div>
                </div>
            </div>
    
        </div>
    
        `
    });
}