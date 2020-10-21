require("./base")

window.Vue = require("vue")
Vue.prototype.$eventHub = new Vue()


Vue.component('survey-show', require('./components/SurveyShow.vue'))
window.surveyComplete = function(msg=null) {
    return surveyOnComplete(msg);
}

Vue.prototype.surveyOnComplete = surveyComplete;
var vm = new Vue({
    el: "#surveyElement"
})
var bus=
global.vm = vm;
global.bus = Vue.prototype.$eventHub;
