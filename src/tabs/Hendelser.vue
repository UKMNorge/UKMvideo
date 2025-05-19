<template>
    <div>
        <!-- Melding om direktesending -->
        <div v-show="hendelser.length > 0">
            <div class="container as-container as-margin-top-space-4 as-margin-bottom-space-4">
                <permanent-notification :typeNotification="'info'" :tittel="'Viktig beskjed'" :description="'Trykk knappen under for veiledning for optimalisering av videoopplastinger'" />
                <div class="as-display-flex">
                    <button class="as-margin-auto as-btn-simple primary" @click="showHideNotifiction()">Åpne beskjed</button>
                </div>
            </div>
            
            <div v-show="showNotification" class="container container-as as-card-1 as-padding-space-4 as-padding-left-space-5 as-padding-right-space-5 as-margin-bottom-space-2">
                <h3 class="as-margin-top-space-2">Anbefalinger for videoopplastinger?</h3>
                <ul>
                    <li>MP4-containers, AAC-audio codec, H264-video codec, 30 eller lavere bilder per sekund</li>
                    <li>moov-atom bør være i starten av filen (Fast Start)</li>
                    <li>H264 progressiv skanning (ingen interlacing)</li>
                    <li>H264 high profile</li>
                    <li>Closed GOP</li>
                    <li>Innholdet bør kodes og lastes opp i samme bildefrekvens som det ble opptatt</li>
                    <li>Mono eller Stereo lyd (Stream vil blande lydspor med mer enn 2 kanaler ned til stereo)</li>
                </ul>
                <h3>Nedenfor er bitrate-anbefalinger for koding av nye videoer for Stream:</h3>
                <table>
                    <tr>
                        <th><b>Resolution</b></th>
                        <th><b>Recommended bitrate</b></th>
                    </tr>
                    <tr>
                        <td>1080p</td>
                        <td>8 Mbps</td>
                    </tr>
                    <tr>
                        <td>720p</td>
                        <td>4.8 Mbps</td>
                    </tr>
                    <tr>
                        <td>480p</td>
                        <td>2.4 Mbps</td>
                    </tr>
                    <tr>
                        <td>360p</td>
                        <td>1 Mbps</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="hendelser col-xs-12 nop">
            <div v-for="(hendelse, index) in hendelser" :key="index" class="hendelse col-xs-12">
                <div class="inner as-box-style" >
                    <!-- Vis mer i hendelsen -->
                <div class="hendelse-buttons">
                    <button class="show-more collapsed" type="button" data-toggle="collapse" :data-target="[ '#hendelse' + hendelse.getId() ]" aria-expanded="false" :aria-controls="[ 'hendelse' + hendelse.getId() ]">
                        <div class="info">
                            <h4>{{ hendelse.getNavn() }}</h4>
                            <span>{{ hendelse.getSted() }}</span>
                            <div class="shadow-hide-content"></div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M16.293 9.293 12 13.586 7.707 9.293l-1.414 1.414L12 16.414l5.707-5.707z"></path></svg>
                    </button>
                </div>
                    <div class="videos collapse" :id="[ 'hendelse' + hendelse.getId() ]">
                        <!-- alle innslag -->
                        <div class="innslags row nop">
                            <div v-for="(innslag, innslagIndex) in hendelse.getInnslags()" :key="innslagIndex" class="innslag col-xs-12">
                                <div class="innslag-inner as-box-style">
    
                                    <!-- Labels -->
                                    <div class="labels">
                                        <div class="label-mini mini-label-style">
                                            <span>{{ innslag.getType() }}</span>
                                        </div>
                                    </div>
                                    <p><b>{{ innslag.getNavn()  }}</b>{{ innslag.getTitlesString() ? ' - ' + innslag.getTitlesString() : '' }}</p>
                                    
                                    <div class="buttons">
                                        <button @click="innslag.fetchVideos()" class="btn show-more collapsed" type="button" data-toggle="collapse" :data-target="[ '#allVideos' + hendelse.getId() + innslag.getId() ]" aria-expanded="false" :aria-controls="[ 'allVideos' + hendelse.getId() + innslag.getId() ]">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M16.293 9.293 12 13.586 7.707 9.293l-1.414 1.414L12 16.414l5.707-5.707z"></path></svg>
                                            <p>{{ innslag.getAntallFilmer() }} film{{ innslag.getAntallFilmer() > 1 ? 'er' : '' }}</p>
                                        </button>
                                    </div>
                                    <!-- Filmer i innslag -->
                                    
                                    <div class="videos collapse" :id="[ 'allVideos' + hendelse.getId() + innslag.getId() ]">
                                        <div>
                                            <p class="samtykke" :class="innslag.getNeiSamtykker() > 0 ? 'ikke-godkjent' : ''">{{ innslag.getNeiSamtykker() > 0 ? innslag.getNeiSamtykker() + ' deltaker'+ (innslag.getNeiSamtykker() > 1 ? 'e' : '') +' og/eller foresatte har ikke godkjent eller besvart samtykke' : 'Alle har samtykket' }}</p>
                                        </div>

                                        <div class="inner-videos flex-row">
                                            <!-- Last opp film -->
                                            <div class="col-xs-4 col-sm-3 upload-video-for-hendelse innslag">
                                                <!--- Upload video -->
                                                <button v-show="!innslag.isUploadOpen" @click="showUpload(innslag)" class="round-style-button mini open-upload">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: #4f46e5;transform: ;msFilter:;"><path d="M15 2.013H9V9H2v6h7v6.987h6V15h7V9h-7z"></path></svg>
                                                </button>
                                                <upload-video v-show="innslag.isUploadOpen" ref="uploadVideo-reportasje" :onUploadCallback="onUpload" :erReportasje="false" :innslagId="innslag.getId()" :miniVersion="true" />
                                            </div>
                                            
                                            <div  class="col-xs-3" v-for="(video, videoIndex) in innslag.getVideos()" :key="videoIndex">
                                                <video-vue :onDeleteCallback="onVideoDelete" :onPublishCallback="onVideoPublish" :innslag="innslag" :video="video" :mini="false" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div v-if="hendelser.length < 1">
                <p>Du mu ha minst en hendelse i programmet!</p>
            </div>
        </div>
        
    </div>
</template>


<script lang="ts">
// Import av Vue
import { Vue, Component, Prop } from "vue-property-decorator";
import $ from "jquery";
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import Hendelse from "../objects/Hendelse";
import InnslagVideo from "../objects/InnslagVideo";
import VideoVue from '../components/VideoVue.vue';
import Video from '../objects/Video';


import UploadVideo from "../components/UploadVideo.vue";

declare var ajaxurl: string; // Kommer fra global

@Component
export default class Hendelser extends Vue {
    private spaInteraction = new SPAInteraction(null, ajaxurl);
    public chartId = '#progressBar';
    public arrangementId : string = '';
    public hendelser : Hendelse[] = [];
    public showNotification = false;

    components = {
        UploadVideo,
        VideoVue
    }

    public showHideNotifiction() {
        this.showNotification = !this.showNotification;
    }

    public init() {
        var arrangementId = $('#vueArguments').attr('arrangementId');
        if(arrangementId) {
            this.arrangementId = arrangementId;
            this.fetchHendelser();
        }
        else {
            console.error('arrangementId må være definert');
        }
    }

    public async fetchHendelser() {
        // Hendelser ble fetcha og trenger ikke å gjøre det igjen
        if(this.hendelser.length > 0) {
            return;
        }

        var data = {
            action: 'UKMvideo_ajax',
            subaction: 'getHendelser'
        };
        
        var response = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        if(response) {
            this.hendelser = [];

            console.log(response);
            for(var key in response) {
                console.log('Hendelse: ', response[key]);
                var hendelse = response[key].hendelse;

                var hendelseObj = new Hendelse(
                    hendelse.id,
                    hendelse.navn,
                    hendelse.beskrivelse,
                    hendelse.sted,
                    hendelse.context.type
                );

                // Legg til innslag
                var innslags : InnslagVideo[] = [];
                for(var innslag of hendelse.innslag.innslag) {
                    let titlesStr : string = '';
                    let samtykker : any[] = [];

                    if(response[key].titler[innslag.id] != undefined) {
                        for(let title of response[key].titler[innslag.id]) {
                            titlesStr += (titlesStr.length > 0 ? ', ' : '') + title.tittel;
                        }
                    }

                    if(response[key].samtykker[innslag.id] != undefined) {
                        console.log('Samtykker: ', response[key].samtykker[innslag.id]);
                        for(let samtykke of response[key].samtykker[innslag.id]) {
                            samtykker.push(samtykke);
                        }
                    }
                

                    var innslagVideoObj = new InnslagVideo(
                        innslag.id,
                        innslag.navn,
                        innslag.type.name,
                        innslag.antallFilmer,
                        titlesStr,
                        samtykker ?? [],
                    );
                    innslags.push(innslagVideoObj);
                }
                hendelseObj.addInnslags(innslags);
                this.hendelser.push(hendelseObj);
            }
        }

        return response;
    }

    public showUpload(hendelse : Hendelse) {
        hendelse.isUploadOpen = true;
    }

    public toggleHendelse(hendelse : Hendelse) {
        hendelse.hendelseOpen = !hendelse.hendelseOpen;
    }

    public onUpload(response : any, innslagId : string) {
        for(var hendelse of this.hendelser) {
            for(var innslag of hendelse.getInnslags()) {
                if(innslag.getId() == innslagId) {
                    innslag.fetchVideos();
                }
            }
        }
    }

    public onVideoDelete(response : any, video : Video, innslag : InnslagVideo) {
        innslag.fetchVideos();
    }

    public onVideoPublish(response : any, video : Video, innslag : InnslagVideo) {
        innslag.fetchVideos()
    }

}
    

// Registrering av komponenten
Vue.component('video-hendelser', Hendelser);
</script>

<style scoped>
.samtykke {
    color: var(--as-color-primary-success-darker);
}
.samtykke.ikke-godkjent {
    color: var(--as-color-primary-danger);
}
</style>
