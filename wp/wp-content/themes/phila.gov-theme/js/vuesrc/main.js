import Vue from 'vue'
import VueRouter from 'vue-router'
import App from './Archives.vue'
import VuePaginate from 'vue-paginate'

Vue.use(VuePaginate)
Vue.use(VueRouter)

const router = new VueRouter({
 route,
  mode: 'history',
  props: true
})

const route = [
  { path: '/archives' },
]

new Vue({
  el: '#archive-results',
  router,
  render: h => h(App)
})
