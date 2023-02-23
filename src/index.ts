import { SPAInteraction } from 'ukm-spa/SPAInteraction';
import {uploadVideoTabs} from './uploadVideoTabs';
import { Director } from 'ukm-spa/Director';

var director = new Director();
(<any>window).director = director;

var activeTab = director.getParam('tabSPA');

// Hvis tab finnes ikke, set det t
if(!activeTab) {
    activeTab = 'reportasje';
}

uploadVideoTabs(activeTab);
