import Vue from "vue";
import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import {uploadVideoTabs} from './uploadVideoTabs';
import { Director } from 'ukm-spa/Director';


var director = new Director();
(<any>window).director = director;

var page = director.getParam('pageSPA');

console.log(page);

if(!page) {
    page = 'upload';
}



director.addEventListener('openPage', function(obj : any) {
    if(obj.id == 'upload') {
        uploadVideoTabs();
    }
    else {
        alert('Unavailable id');
    }
    
    // console.log('aaa');
    // jQuery(".menu-items button.menu-item").removeClass('active');
    // jQuery(".menu-item."+ obj.id).addClass('active');
    // console.log(".menu-item."+ obj.id);
})

director.openPage(page);


// declare var ajaxurl: string; // Kommer fra global
// let v = new Vue({
//     el: "#videoVueApp",
//     data: { 
//         name: "World",
//         activeTab : 'deltabruk',
//         spaInteraction: new SPAInteraction(null, 'https://ukm.dev/wp-admin/admin-ajax.php')
//     },
    
//     components: {

//     },

//     mounted : function() {
//         this.testAjax();
//     },

//     methods : {
//         testAjax : function() {
//             var data = {
//                 action: 'UKMvideo_ajax',
//                 subaction: 'text',
//             };
            
//             console.log(ajaxurl);
    
//             var response = this.spaInteraction.runAjaxCall('/', 'POST', data);

//             console.log(response);
            
//         },
//         // Open tab
//         velgKomponent: function() {

//         }
//     },

//     template: /*html*/`
//     <div>    
//         <div>
//             <h1>Here is the value {{name}}</h1>
//         </div>

//     </div>

//     `
// });
