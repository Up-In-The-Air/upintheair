// var button = require('lib/material-ui-vue/components/buttons/button.vue');

var app = new Vue({
  el: '#app',
  data: {
    message: 'Hello Vue!'
  },
  // components: {
  //   'float-button': button
  // }
});

import Vue from 'vue'

// 1. Require the module
import VueMaterialComponents from 'vue-material-components'

// 2. Require the Materialize CSS (or import it in your HTML)
import 'vue-material-components/assets/css/materialize.min.css'

// 3. Install the plugin
Vue.use(VueMaterialComponents)
