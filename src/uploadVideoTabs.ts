import Vue from "vue";
import VideoReportasjer from "./components/VideoReportasjer.vue";
import VideoHendelser from "./components/VideoHendelser.vue";
import Direktesending from "./components/Direktesending.vue";


export function uploadVideoTabs() {
    new Vue({
        el: "#uploadVueApp",

        data: { 
            activeTab : 'reportasje'
        },
        
        components: {
            VideoReportasjer,
            VideoHendelser,
            Direktesending
        },
    
        mounted : function() {
            this.openTab(this.activeTab);
            // console.log(SPAInteraction);
            // console.log(Director);
        },
    
        methods : {
            // Open tab
            openTab: function(tabRef : string) : void {
                console.log(tabRef);

                this.activeTab = tabRef;
                var objVue = (<VideoReportasjer>this.$refs[tabRef]);
                objVue.init();
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
                        <button :class="{'active' : activeTab == 'hendelser'}" @click="openTab('hendelser');">Filmer av innslag</button>
                    </div>
                    <div class="tab-item">
                        <button :class="{'active' : activeTab == 'direktesending'}" @click="openTab('direktesending');">Direktesending</button>
                    </div>
                </div>

                <div class="tab-content tabs">
                    <div v-show="activeTab == 'reportasje'">
                        <video-reportasjer ref="reportasje" />
                    </div>
                </div>

                <div class="tab-content tabs">
                    <div v-show="activeTab == 'hendelser'">
                        <video-hendelser ref="hendelser" />
                    </div>
                </div>

                <div class="tab-content tabs">
                    <div v-show="activeTab == 'direktesending'">
                        <direktesending ref="direktesending" />
                    </div>
                </div>

            </div>
    
        </div>
    
        `
    });
}