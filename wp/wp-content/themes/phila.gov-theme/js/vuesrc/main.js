import 'babel-polyfill'

import Vue from 'vue'
import VueRouter from 'vue-router'
import VuePaginate from 'vue-paginate'
import vmodal from 'vue-js-modal'
import Posts from './Archives.vue'
import Pubs from './Publications.vue'
import Events from './Events.vue'
import Programs from './Programs.vue'


Vue.use(VuePaginate)
Vue.use(VueRouter)
Vue.use(vmodal)

const router = new VueRouter({
 route,
  mode: 'history',
  props: true
})

const route = [
  { path: '/archives' },
]

//check which page we're on before adding Vue
if (window.location.pathname === '/the-latest/archives/') {
  new Vue({
    el: '#archive-results',
    router,
    render: h => h(Posts)
  })
}else if(window.location.pathname === '/documents/') {
  new Vue({
    el: '#publication-search',
    render: h => h(Pubs)
  })
}else if(window.location.pathname === '/programs/'){
  new Vue({
    el: '#programs-initiatives-landing',
    render: h => h(Programs)
  })
}else{
  new Vue({
    el: '#all-events',
    router,
    render: h => h(Events)
  })
}
