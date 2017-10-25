<template>
  <div id="archive-results">
    <form v-on:submit.prevent="onSubmit">
      <div class="search">
        <input id="post-search" type="text" name="search" placeholder="Search by title or keyword" class="search-field" ref="search-field"
        v-model="searchedVal">
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
              <div class="cell medium-4">
                <datepicker
                placeholder="Start date"
                v-on:closed=""
                v-model="state.startDate"></datepicker>
              </div>
              <div class="cell medium-1">
                <i class="fa fa-arrow-right"></i>
              </div>
              <div class="cell medium-4">
                <datepicker placeholder="End date"
                v-on:closed="runDateQuery"
                v-model="state.endDate"></datepicker>
              </div>
              <div class="cell medium-9 auto filter-by-owner">
                <v-select
                label="slang_name"
                :value.sync="selectedCat"
                :options="categories"
                :on-change="filterByCategory">
                </v-select>
              </div>
              <div class="cell medium-6">
                <a class="button content-type-featured full" @click="reset">Clear filters</a>
              </div>
            </div>
        </div>
      </div>
    </div>
    <i v-show="loading" class="fa fa-spinner fa-spin"></i>
    <table class="stack theme-light archive-results"  data-sticky-container>
      <thead class="sticky center bg-white" data-sticky data-top-anchor="filter-results:bottom" data-btm-anchor="page:bottom" data-options="marginTop:4.8;">
        <tr><th class="title">Title</th><th class="date">Publish date</th><th>Department</th></tr>
      </thead>
      <paginate name="posts"
        :list="posts"
        class="paginate-list"
        tag="tbody"
        :per="40">
        <tr v-for="post in paginated('posts')"
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
      </paginate>
    </table>
    <paginate-links for="posts"
    :limit="10"
    :show-step-links="true"
    :step-links="{
      next: 'Next',
      prev: 'Previous'
    }"></paginate-links>
  </div>
  </template>

<script>
import moment from 'moment'
import axios from 'axios'
import vSelect from 'vue-select'
import Datepicker from 'vuejs-datepicker';

const endpoint = '/wp-json/the-latest/v1/'


var state = {
    date: new Date()
}

export default {
  components: {
    vSelect,
    Datepicker
  },
  data: function() {
    return{
      posts: [],
      categories: [{
        value: this.id,
        label: this.slang_name
      }],
      selectedCat: (this.$route.query.category) ? '' : 'All departments',
      templates: {
        featured : "Featured",
        post : 'Posts',
        press_release : 'Press release'
      },
      checkedTemplates: [],
      templateFiltered: [],
      searchedVal: '',

      loading: false,

      paginate: ['posts'],

      format: 'd MMMM yyyy',
      state: {
        startDate: '',
        endDate: ''
      },

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
    this.getAllPosts()
    this.getDropdownCategories();
  },
  methods: {
    getPostCategory(){
      var catID = this.$route.query.category
      if (this.categories.value == this.$route.query.category) {
        return this.categories.label
      }

    },
    getAllPosts: function () {
      loading: true
      if ( Object.keys(this.$route.query).length != 0){
        this.parseQueryStrings()
      }else{
        axios.get(endpoint + 'archives', {
          params: {
            'count': -1
          }
        })
        .then(response => {
          console.log(this.$route.query)
          loading: false

        })
        .catch(e => {
          console.log(e)
        })
      }
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
      var chosenTemplate = this.$route.query.template
      var chosenCat = this.$route.query.category

      if(chosenTemplate){
        document.getElementById(chosenTemplate).click()
      }

      axios.get(endpoint + 'archives', {
        params : {
          'template' : chosenTemplate,
          'category': chosenCat,
          'count': -1
          }
        })
        .then(response => {
          this.posts = response.data

        })
        .catch(e => {
          console.log(e);
        })

      function isChosenCat(element) {
        return element.id = chosenCat;
      }
      console.log(this.categories.find(isChosenCat))

      this.selectedCat = this.categories.find(isChosenCat)

      this.$forceUpdate();

    },
    onSubmit: function (event) {
      console.log(this.searchedVal)
      console.log(this.checkedTemplates)
      console.log(this.selectedCat)

      axios.get(endpoint + 'archives', {
        params : {
          's': this.searchedVal,
          'template': this.checkedTemplates,
          'category': this.selectedCat,
          'count': -1
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
      console.log( selectedVal )

      this.selectedCat = selectedVal

      console.log(selectedVal)
      console.log('filterByCategory : s : '+ this.searchedVal)

      axios.get(endpoint + 'archives', {
        params : {
          'category' : selectedVal,
          'template' : this.checkedTemplates,
          //'category': this.selectedCat,
          'count' : -1,
          's': this.searchedVal
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
    runDateQuery(){
      console.log(this.state)

      axios.get(endpoint + 'archives', {
        params : {
          //'category' : selectedVal,
          'template' : this.checkedTemplates,
          'count' : -1,
          's': this.searchedVal,
          //'a_y': 2017
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
    }
  },
  computed:{
  },
  watch: {
    checkedTemplates: function(newVal, oldVal){
      console.log( this.selectedCat )
      axios.get(endpoint + 'archives', {
        params : {
          'template' : newVal,
          'category': this.selectedCat,
          'count' : -1,
          's': this.searchedVal
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
ul.paginate-links {
  display: inline-block;
  margin:0;
  padding:0;
  float:right;
}
.paginate-links li{
  display: inline-block;
  border-right: 2px solid white;
  margin-bottom:1rem;
}
.paginate-links a{
  display: block;
  padding: .5rem;
  background: #0f4d90;
  color:white;
}
.paginate-links a{
  color:white;
}
.paginate-links li.active a{
  background: white;
  color: #444;
}
</style>
