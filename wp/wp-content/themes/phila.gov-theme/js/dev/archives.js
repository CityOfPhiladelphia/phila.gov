var Vue = require('vue')
var moment = require('moment')
var axios = require('axios')
var Datepicker = require('vuejs-datepicker')
var VueRouter = require('vue-router')

Vue.use(VueRouter)

var router = new VueRouter({
 route,
 mode: 'history'
})

var route = [
    { path: '/archives',
      component: archives
   },
  ]

Vue.filter('formatDate', function(value) {
  if (value) {
    return moment( String(value) ).format('MMM. DD, YYYY')
  }
})

var endpoint = '/wp-json/the-latest/v1/archives'

var archives = new Vue ({
  el: '.results',
  router,
  components: {
      Datepicker
  },
  template:`
  <div class="root">

    <form v-on:submit.prevent="onSubmit">
      <div class="search">
        <input id="post-search" type="text" name="search" placeholder="Search by title or keyword" class="search-field" ref="search-field">
        <input type="submit" value="submit" class="search-submit">
      </div>
    </form>
    <div class="accordion" data-accordion data-allow-all-closed="true">
      <div id="filter-results" class="accordion-item is-active" data-accordion-item>
        <a class="h5 accordion-title">Filter results</a>
          <div class="accordion-content" data-tab-content>
            <fieldset>
              <div class="grid-x grid-margin-x mbl">
                <div v-for="(value, key) in  templates" class="cell auto">
                  <input type="checkbox" v-model="checkedTemplates" v-bind:value="key" v-bind:name="key" v-bind:id="key" />
                  <label v-bind:for="key" class="post-label" v-bind:class="'post-label--' + key">{{ value }}</label>
                </div>
              </div>
            </fieldset>
            <div class="grid-x grid-margin-x">
              <div class="cell medium-9">
                <datepicker placeholder="Start date" v-on:closed=""></datepicker>
                  <i class="fa fa-arrow-right"></i>
                <datepicker placeholder="End date" v-on:closed=""></datepicker>
              </div>
              <div class="cell medium-9">
                <select id="departments" name="select">
                  <option value="all-departments" selected="selected">All departments</option>
                </select>
              </div>
              <div class="cell medium-6">
                <a class="button content-type-featured full" @click="reset">Clear filters</a>
              </div>
            </div>
        </div>
      </div>
    </div>
    <table class="stack theme-light"  data-sticky-container>
      <thead class="sticky center bg-white" data-sticky data-top-anchor="filter-results:bottom" data-options="marginTop:4.8;">
        <tr><th width="500">Title</th><th width="125">Publish date</th><th>Department</th></tr>
      </thead>
      <tbody>
        <tr v-for="post in filteredPosts"
        :key="post.id"
        class="clickable-row"
        v-on:click="goToPost(post)">
          <td class="title"><a v-bind:href="post.link">
            <span class="prm">
              <span v-if="post.template.includes('post')">
                <i class="fa fa-pencil pride-purple"></i>
              </span>
              <span v-else-if="post.template == 'press_release'">
                <i class="fa fa-file-text-o love-park-red"></i>
              </span>
              <span v-else>
                <i class="fa fa-newspaper-o ben-franklin-blue"></i>
              </span>
            </span>
            {{ post.title }}</a>
          </td>
          <td class="date">{{ post.date  | formatDate }}</td>
          <td class="categories">
            <span v-for="(category, i) in post.categories">
              <span>{{ category.slang_name }}</span><span v-if="i < post.categories.length - 1">,&nbsp;</span>
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
`,
  data: function() {
    return{
      posts: [],
      templates: {
          post : 'Post',
          featured : "Featured",
          press_release : 'Press release'
        },
      checkedTemplates: []
    }
  },
  mounted: function () {
    this.getPosts()
  },
  watch: {
    '$route': 'changePost'
  },
  methods: {
    getPosts: function () {
      axios.get(endpoint)
      .then(response => {

        if (this.$route.query.type){
          this.fetchTypes()
        }

        this.posts = response.data

      })
      .catch(e => {
        console.log(e)
      })
    },
    goToPost: function (post){
      window.location.href = post.link
    },
    fetchTypes: function(){
      var type = this.$route.query.type

      axios.get(endpoint, {
        params : {
          'template' : type
          }
        })
        .then(response => {
          this.posts = response.data
          console.log(response.data);
        })
        .catch(e => {

        console.log(e);
      })

    },
    onSubmit: function (event) {
      axios.get(endpoint, {
        params : {
          's' : event.target.search.value
          }
        })
        .then(response => {
          this.posts = response.data
          console.log(response.data);
        })
        .catch(e => {

        console.log(e);
      })
    },
    reset () {
      location.reload();
    },
    changePost() {
      console.log(this.$route.query)
    }
  },
  computed:{
    filteredPosts(){

      if (!this.checkedTemplates.length)
        return this.posts

      return this.posts.filter(
        j => this.checkedTemplates.includes(j.template)
      )
    }
  },
})
