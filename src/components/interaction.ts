import Vue from 'vue';

// Dialog Components
var dialogComponent = Vue.component('modaldialog-component', { 
    delimiters: ['#{', '}'], // For å bruke det på Twig
    props: {
        alleInnslag : []
    },
    data : function() {
        return {
            melding : '',
            title : '',
            buttons : [], // [{name : 'Accept', class : "confirm-btn", callback : ()=> { ... }]
            onCloseCallback : ()=> {}
        }   
    },
    mounted() {
        this._events();
    },
    methods : {
        openDialog : function(title : string, melding : string, buttons = [], onCloseCallback : ()=>{}) {
            this.melding = melding;
            this.buttons = buttons;
            this.title = title ? title : '';
            this.onCloseCallback = onCloseCallback;
            (<any>jQuery('#mainModalDialog')).modal('show');
        },
        buttonClick : function(button : any) {
            if(button.callback) {
                button.callback();
                // Disable the close event
                jQuery('#mainModalDialog').off('hidden.bs.modal');
            }
        },
        closeClicked : function() {
            if(this.onCloseCallback) {
                this.onCloseCallback();
            }
        },
        _events : function() {
            // event når dialogen lukkes
            jQuery('#mainModalDialog').on('hidden.bs.modal', this.closeClicked);
        }
    },
    template : /*html*/`
        <div class="modal fade" id="mainModalDialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content accordion-item with-shadow with-radius">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">#{title}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        #{melding}
                    </div>
                    <div class="modal-footer">
                        <button v-for="btn in buttons" @click="buttonClick(btn)" type="button" class="round-style-button" :class="btn.class" data-dismiss="modal">#{btn.name}</button>
                    </div>
                </div>
            </div>
        </div>
    `
});


// Message Components
var messageComponent = Vue.component('messagemodal-component', { 
    delimiters: ['#{', '}'], // For å bruke det på Twig
    props: {
        alleInnslag : []
    },
    data : function() {
        return {
            melding : '',
            title : '',
            type : 0, // -1 error, 0 - normal, 1 - warning
            open : false,
            timeout : 0,
            timeoutFunction : -1
        }
    },
    mounted() {

    },
    methods : {
        openMessage : function(title : string, melding : string, type = 0) {
            if(this.timeoutFunction) {
                clearTimeout(this.timeoutFunction);
            }
            
            this.title = title
            this.melding = melding;
            this.type = type;

            this.openModal(type);
            
            this.timeoutFunction = setTimeout(() => {
                this.closeModal();
            }, 5000);
        },
        openModal : function(type : any) {
            if(this.open) {
                this.closeModal();
                setTimeout(() => {
                    this.openMessage(this.title, this.melding, this.type);
                }, 300);
                return;
            }

            (<any>jQuery('#mainMessageDiv')).modal('show');
            this.open = true;
        },
        closeModal : function() {
            (<any>jQuery('#mainMessageDiv')).modal('hide');
            this.open = false;
        }
    },
    template : /*html*/`
        <div class="modal fade" id="mainMessageDiv" tabindex="-1" role="dialog" data-backdrop="false" aria-hidden="false">
            <div :class="{ 'error' : type == -1, 'warning' : type == 1}" class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content accordion-item with-shadow with-radius">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">#{title}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        #{melding}
                    </div>
                </div>
            </div>
        </div>
    `
})


// Loading Components
var loadingComponent = Vue.component('loading-component', { 
    delimiters: ['#{', '}'], // For å bruke det på Twig
    data : function() {
        return {
            show : false
        }
    },
    mounted() {
        this.hideLoading();
    },
    methods : {
        showLoading : function() {
            this.show = true;
        },
        hideLoading : function() {
            this.show = false;
        }
    },
    template : /*html*/`
    <div id="headerMainLoader" :class="{ 'hide' :  !show }">
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    `
})


// The app
export var interactionVue = new Vue({
    delimiters: ['#{', '}'], // For å bruke det på Twig
    el: '#deltaHeader',
    data: {
        alle_innslag : []
    },
    mounted() {
        // this.updateData();
    },
    methods : {
        openDialog : function(title : string, msg : string, buttons = null, onCloseCallback : any) {
            (<any>this.$refs.modalDialog).openDialog(title, msg, buttons, onCloseCallback);
        },
        showMessage : function(title : string, msg : string, type = 0) {
            (<any>this.$refs.messageModal).openMessage(title, msg, type);
        },
        showLoading : function() {
            if(this.$refs.mainLoading) {
                (<any>this.$refs.mainLoading).showLoading();
            }
        },
        hideLoading : function() {
            if(this.$refs.mainLoading) {
                (<any>this.$refs.mainLoading).hideLoading();
            }
        }
    },
    components : { 
        'dialog' : dialogComponent,
        'message' : messageComponent,
        'loading' : loadingComponent
    }
})