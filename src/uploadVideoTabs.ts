import Vue from "vue";
import UploadVideo from "./components/UploadVideo.vue";
import ListVideos from "./components/ListVideos.vue";

export function uploadVideoTabs() {
    new Vue({
        el: "#uploadVueApp",

        data: { 
            activeTab : 'reportasje'
        },
        
        components: {
            UploadVideo,
            ListVideos
        },
    
        mounted : function() {
            this.openTab(this.activeTab);
            // console.log(SPAInteraction);
            // console.log(Director);
        },
    
        methods : {
            // Open tab
            openTab: function(tabRef : string) : void {
                this.activeTab = tabRef;
                var vueObjectUV = (<UploadVideo>this.$refs['uploadVideo-' + tabRef]);
                var vueObjLV = (<ListVideos>this.$refs['allVideos-' + tabRef]);
                vueObjectUV.init();
                vueObjLV.init();
            }
        },
    
        template: /*html*/`
        <div>    
            <div>     
                <div class="tab-items video-buttons">
                    <div class="tab-item">
                        <button :class="{'active' : activeTab == 'reportasje'}" @click="openTab('reportasje');">Filmer</button>
                    </div>
                    <div class="tab-item">
                        <button :class="{'active' : activeTab == 'innslag'}" @click="openTab('innslag');">Filmer av innslag</button>
                    </div>
                </div>

                <div class="tab-content tabs">
                    <div v-show="activeTab == 'reportasje'">
                        <div>
                            <upload-video ref="uploadVideo-reportasje" :name="name" :initialEnthusiasm="5" :erReportasje="true" />
                        </div>
                        <div>
                            <list-videos ref="allVideos-reportasje" :erReportasje="true" />
                        </div>
                    </div>
                </div>

                <div class="tab-content tabs">
                    <div v-show="activeTab == 'innslag'">
                        <div>
                            <upload-video ref="uploadVideo-innslag" :name="name" :initialEnthusiasm="5" :erReportasje="true" />
                        </div>
                        <div>
                            <list-videos ref="allVideos-innslag" :erReportasje="true" />
                        </div>
                    </div>
                </div>

            </div>
    
        </div>
    
        `
    });
}