<template>
  <div id="archive-results">
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
                <div v-for="(value, key) in templates" class="cell auto">
                  <input type="radio"
                  :checked="key"
                  v-model="checkedTemplates"
                  v-bind:value="key"
                  v-bind:name="key"
                  v-bind:id="key" />
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
              <div class="cell medium-9 auto filter-by-owner">
                <v-select
                label="slang_name"
                :value.sync="selected"
                :options="categories"
                :on-change="filterByCategory">
              </v-select>
                <!--<select id="departments" name="select" @change="filterByCategory" v-model="selected">
                  <option value="All departments" selected>All departments</option>
                  <option v-for="category in categories" v-bind:value="category.id">{{ category.slang_name }}</option>
                </select>-->
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
        <tr v-for="post in paginatedPosts"
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
    <ul class="phila-paginate float-right">
      <!--<li class="prev-item">
        <a href="" @click.prevent="stepDown(n)">Previous {{n-1}}</a>
      </li>-->
      <li v-for="n in numOfPages">
        <a href=""
        @click.prevent="setPage(n)"
        v-bind:class="{active : isActive}"> {{ n }}</a>
      </li>
    <!--<li class="next-item">
      <a href="" @click.prevent="stepUp(n)">Next {{n+1}}</a>
    </li>-->
    </ul>
  </div>
  </template>

<script>
import Search from './components/phila-search.vue'
import moment from 'moment'
import axios from 'axios'
import vSelect from 'vue-select'

//var Datepicker = require('vuejs-datepicker')

var endpoint = '/wp-json/the-latest/v1/'

export default {
  components: {
    vSelect
  },
  data: function() {
    return{
      posts: [],
      categories: [{
        value: this.id,
        label: this.slang_name
      }],
      selected: (this.$route.query.category ? this.getCategoryName : 'All departments'),

      templates: {
          post : 'Post',
          featured : "Featured",
          press_release : 'Press release'
        },
      checkedTemplates: [],
      currentPage: 1,
      perPage: 40,
      isActive: false,
    }
  },
  filters: {
    'formatDate': function(value) {
      if (value) {
        return moment( String(value) ).format('MMM. DD, YYYY')
      }
    }
  },
  mounted: function () {
    this.getPosts()
    this.getDropdownCategories();
  },
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
      console.log('submit ')
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
    filterByCategory: function(selectedVal){
      axios.get(endpoint + 'archives', {
        params : {
          'category' : selectedVal.id
          }
        })
        .then(response => {
          this.posts = response.data
          console.log(this.posts)

          if (this.posts.length > 0) {
            response.data = "Sorry, nothing matches that category."
          }
        })
        .catch(e => {

        console.log(e);
      })
    },
    reset() {
      window.location = window.location.href.split("?")[0];
    },
    setPage(n) {
      this.currentPage = n
      if(this.currentPage = n)
        this.isActive = true
    },
    stepDown(n) {
      console.log(n)
      this.currentPage = n-1
      this.paginatedPosts
    },
    stepUp(n) {
      console.log(n)
      this.currentPage = n+1;
      this.paginatedPosts
    },
  },
  computed:{
    paginatedPosts (){
      if (this.offset > this.posts.length) {
        this.currentPage = this.numOfPages;
      }
      return this.posts.slice(this.offset, this.limit);
    },
    numOfPages() {
      return Math.ceil(this.posts.length / this.perPage);
    },
    offset() {
        return ((this.currentPage - 1) * this.perPage);
      },
    limit() {
      return (this.offset + this.perPage);
    }
  },
  watch: {
    checkedTemplates: function(newVal, oldVal){
      axios.get(endpoint + 'archives', {
        params : {
          'template' : newVal,
          'count' : -1
          }
        })
      .then(response => {
        console.log(this.$route.query)
          this.posts = response.data
          console.log(response.data)
        })
      }
  }
}
</script>

<style>
.filter-by-owner{
  font-family:"Open Sans", Helvetica, Roboto, Arial, sans-serif !important;
}
.filter-by-owner .v-select .dropdown-toggle{
  border:none;
  background:white;
}
.filter-by-owner .v-select .open-indicator{
  bottom:0;
  top:0;
  right:0;
  background: #0f4d90;
  padding: 1rem 1.5rem 1rem 1rem;
  height: inherit;
}

.filter-by-owner .v-select input[type=search],
.v-select input[type=search]:focus{
  border:none;
}
.filter-by-owner .v-select .open-indicator:before{
  border-color:white;
}
.filter-by-owner ul.dropdown-menu{
  border:none;
  font-weight: bold;
}
.filter-by-owner ul.dropdown-menu li{
  border-bottom: 1px solid #f0f0f0;

}
.filter-by-owner ul.dropdown-menu li a{
  color: #0f4d90;
  padding:1rem;
}
.filter-by-owner ul.dropdown-menu li a:hover{
  background: #0f4d90;
  color:white;
}
.filter-by-owner .v-select .dropdown-menu > .highlight > a {
  background: #0f4d90;
  color: white;
}
.filter-by-owner .v-select.single .selected-tag{
  background-color: #f0f0f0;
  border: none;
}
ul.phila-paginate {
  display: inline-block;
  margin:0;
  padding:0;
}
.phila-paginate li{
  display: inline-block;
  border-right: 2px solid white;
  margin-bottom:1rem;
}
.phila-paginate a:link{
  display: block;
  padding: .5rem;
  background: #0f4d90;
  color:white;
}
.phila-paginate a:link,
.phila-paginate a:visited{
  color:white;
}
.phila-paginate a.activeClass:link{
  background: white;
}
</style>
