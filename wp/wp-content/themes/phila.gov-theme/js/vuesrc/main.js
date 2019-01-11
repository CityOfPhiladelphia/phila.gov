import 'babel-polyfill'

import Vue from 'vue'
import VueRouter from 'vue-router'
import VuePaginate from 'vue-paginate'
import vmodal from 'vue-js-modal'
import Posts from './Archives.vue'
import Pubs from './Publications.vue'
import Events from './Events.vue'
import Programs from './Programs.vue'
import AzList from './AzList.vue'
import axios from 'axios'

Vue.config.productionTip = false

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
}else if(window.location.pathname === '/services/'){

  async function getAzListCategories() {
    return axios.get('https://admin.phila.gov/wp-json/services/v1/categories').then((response) => {
      return response.data
    })
  }
  
  async function getAzList() {
    return axios.get('https://admin.phila.gov/wp-json/services/v1/directory').then((response) => {
      return response.data.map((item) => {
  
        let categories = item.categories.map((cat) => {
          
          if (cat) {
            return cat.slug
          }
  
          return ''
          
        })
  
        return {
          title: item.title,
          link: item.link,
          desc: item.desc,
          categories
        }
      })
    })
  }
  
  async function initAzList() {
  
    const categories = await getAzListCategories()
    const list = await getAzList()
  
    new Vue({
      el: '#a-z-filter-list',
      render(h) {
        return h(AzList, {
          props: {
            categories,
            list
          },
        })
      },
    })
  
  }
  
  initAzList()

}else{
  new Vue({
    el: '#all-events',
    router,
    render: h => h(Events)
  })
}
