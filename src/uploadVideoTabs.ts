import Vue from "vue";
import Reportasjer from "./tabs/Reportasjer.vue";
import Hendelser from "./tabs/Hendelser.vue";
import Direktesending from "./tabs/Direktesending.vue";
import $ from "jquery";


export function uploadVideoTabs(activeTab : string) {
    new Vue({
        el: "#uploadVueApp",

        data: { 
            activeTab : activeTab
        },
        
        components: {
            Reportasjer,
            Hendelser,
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
                (<any>window).director.addParam('tabSPA', tabRef);
                
                $('#livestreamStatic').addClass('hide');

                console.log(tabRef);

                this.activeTab = tabRef;
                var objVue = (<Reportasjer>this.$refs[tabRef]);
                objVue.init();
            }
        },
    
        template: /*html*/`
        <div>    
            <div>     
                <div class="tab-items video-buttons">
                    <div class="tab-item">
                        <button :class="{'active' : activeTab == 'reportasje'}" @click="openTab('reportasje');">Reportasjer</button>
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