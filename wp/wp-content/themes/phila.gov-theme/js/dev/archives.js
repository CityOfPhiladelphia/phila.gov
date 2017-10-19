var Vue = require('vue')
var moment = require('moment')
var axios = require('axios')
//var Datepicker = require('vuejs-datepicker')
var VueRouter = require('vue-router')
//var vSelect = require('vue-select')


Vue.use(VueRouter)

var router = new VueRouter({
 route,
  mode: 'history',
  props: true
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

var endpoint = '/wp-json/the-latest/v1/'

var archives = new Vue ({
  el: '#archive-results',
  router,
  components: {
      //Datepicker,
      //vSelect
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
                  <input type="checkbox" v-model="checkedTemplates" v-bind:value="key" v-bind:name="key" v-bind:id="key"/>
                  <label v-bind:for="key" class="post-label" v-bind:class="'post-label--' + key">{{ value }}</label>
                </div>
              </div>
            </fieldset>
            <div class="grid-x grid-margin-x">
            <!--
              <div class="cell medium-9">
                <datepicker placeholder="Start date" v-on:closed=""></datepicker>
                  <i class="fa fa-arrow-right"></i>
                <datepicker placeholder="End date" v-on:closed=""></datepicker>
              </div>
              -->
              <div class="cell medium-9 auto">
                <select id="departments" name="select" @change="filterByCategory" v-model="selected">
                  <option value="All departments" selected>All departments</option>
                  <option v-for="category in categories" v-bind:value="category.id">{{ category.slang_name }}</option>
                </select>
              </div>
              <div class="cell medium-6">
                <a class="button content-type-featured full" @click="reset">Clear filters</a>
              </div>
            </div>
        </div>
      </div>
    </div>
    <table class="stack theme-light archive-results"  data-sticky-container>
      <thead class="sticky center bg-white" data-sticky data-top-anchor="filter-results:bottom" data-btm-anchor="page:bottom" data-options="marginTop:4.8;">
        <tr><th class="title">Title</th><th class="date">Publish date</th><th>Department</th></tr>
      </thead>
      <tbody>
        <tr v-for="post in filteredPosts"
        :key="post.id"
        class="clickable-row"
        v-on:click.stop.prevent="goToPost(post.link)">
          <td class="title">
          <a v-bind:href="post.link" v-on:click.prevent="goToPost(post.link)">
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
            {{ post.title }}
            </a>
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
      categories: [],
      selected: (this.$route.query.category ? this.$route.query.category : 'All departments'),
      templates: {
          post : 'Post',
          featured : "Featured",
          press_release : 'Press release'
        },
      checkedTemplates: [],
    }
  },
  mounted: function () {
    this.getPosts()
    this.getDropdownCategories();
  },
  // watch: {
  //   '$route': 'changePost'
  // },
  methods: {
    getPosts: function () {
      axios.get(endpoint + 'archives')
      .then(response => {
        console.log(this.$route.query)

        if ( Object.keys(this.$route.query).length != 0){

          this.parseQueryStrings()
        }else{
          this.posts = response.data
          console.log(response.data)
          //this.filteredPosts()
        }

      })
      .catch(e => {
        console.log(e)
      })
    },
    getDropdownCategories: function () {
      axios.get(endpoint + 'categories')
      .then(response => {
        this.categories = response.data
      })
      .catch(e => {
        console.log(e)
      })
    },
    goToPost: function (post){
      window.location.href = post
    },
    parseQueryStrings: function(){
      var template = this.$route.query.template
      var chosenCat = this.$route.query.category
      var count = this.$route.query.count

      this.$forceUpdate();

      console.log(template);
      if(template){
        document.getElementById(template).click()
      }

      axios.get(endpoint + 'archives', {
        params : {
          'template' : template,
          'category': chosenCat,
          'count': -1
          }
        })
        .then(response => {

          this.posts = response.data

          console.log(response.data)
          console.log(endpoint)
        })
        .catch(e => {

        console.log(e);
      })

    },
    onSubmit: function (event) {
      axios.get(endpoint + 'archives', {
        params : {
          's' : event.target.search.value
          }
        })
        .then(response => {
          this.posts = response.data
          if (this.posts.length > 0) {
            posts.title = "No titles match."
          }
          console.log(response.data);
        })
        .catch(e => {

        //console.log(e);
      })
    },
    filterByCategory: function(event){
      axios.get(endpoint + 'archives', {
        params : {
          'category' : this.selected
          }
        })
        .then(response => {
          this.posts = response.data
          console.log(this.posts);

          if (this.posts.length > 0) {
            response.data = "Sorry, nothing matches that category."
          }
        })
        .catch(e => {

        console.log(e);
      })
    },
    reset () {
      window.location = window.location.href.split("?")[0];
    },
    changePost() {
      console.log(this.$route.query)
    }
  },
  computed:{
    filteredPosts(){
      // if (this.$route.query.template) {
      //   axios.get(endpoint + 'archives')
      //   .then(response => {
      //     console.log('running');
      //     this.posts = response.data
      //   })
      //   .catch(e => {
      //     console.log(e)
      //   })
      // }
      //
       if (!this.checkedTemplates.length) {
         console.log('none selected')
        return this.posts
      }else{
        return this.posts.filter(
          j => this.checkedTemplates.includes(j.template)
        )
      }

    }
  }
})
