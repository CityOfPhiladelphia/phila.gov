import Vue from 'vue'
import VueRouter from 'vue-router'
import Posts from './Archives.vue'
import Pubs from './Publications.vue'
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

//check which page we're on before adding Vue
if (window.location.pathname == '/the-latest/archives/') {
  new Vue({
    el: '#archive-results',
    router,
    render: h => h(Posts)
  })
}else{
  new Vue({
    el: '#publication-search',
    render: h => h(Pubs)
  })
}
