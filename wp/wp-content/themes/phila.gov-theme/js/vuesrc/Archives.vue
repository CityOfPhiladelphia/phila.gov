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
              <div class="cell medium-4 small-11">
                <datepicker
                name="startDate"
                placeholder="Start date"
                v-on:closed="runDateQuery"
                v-model="state.startDate"></datepicker>
              </div>
              <div class="cell medium-1 small-2 mts">
                <i class="fa fa-arrow-right"></i>
              </div>
              <div class="cell medium-4 small-11">
                <datepicker placeholder="End date"
                name="endDate"
                v-on:closed="runDateQuery"
                v-model="state.endDate"></datepicker>
              </div>
              <div class="cell medium-9 small-24 auto filter-by-owner">
                <v-select
                label="slang_name"
                :value.sync="selectedCat"
                :options="categories"
                :on-change="filterByCategory">
                </v-select>
              </div>
              <div class="cell medium-6 small-24">
                <a class="button content-type-featured full" @click="reset">Clear filters</a>
              </div>
            </div>
        </div>
      </div>
    </div>
    <i v-show="loading" class="fa fa-spinner fa-spin fa-2x"></i>
    <div v-show="emptyResponse">Sorry, there are no results.</div>
    <div v-show="failure">Sorry, there was a problem. Please try again.</div>

    <table class="stack theme-light archive-results"  data-sticky-container v-show="!loading && !emptyResponse && !failure">
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
    :limit="3"
    :show-step-links="true"
    :step-links="{
      next: 'Next',
      prev: 'Previous'
    }"
    v-show="!loading && !emptyResponse && !failure"></paginate-links>
  </div>
  </template>

<script>
import moment from 'moment'
import axios from 'axios'
import vSelect from 'vue-select'
import Datepicker from 'vuejs-datepicker';

const endpoint = '/wp-json/the-latest/v1/'

let state = {
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
        press_release : 'Press releases'
      },
      checkedTemplates: [],
      templateFiltered: [],
      searchedVal: '',

      loading: false,
      emptyResponse: false,
      failure: false,

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
    this.getDropdownCategories()
    this.loading = true
  },
  methods: {
    successfulResponse: function(){
      this.loading = false
      this.posts = response.data
      if (this.posts.length == 0) {
        this.emptyResponse = true
        console.log('no data')
      }else{
        this.emptyResponse = false
      }
    },
    getPostCategory(){
      let catID = this.$route.query.category

      if (this.categories.value == this.$route.query.category) {
        console.log('looking for cats')
        return this.categories.label
      }

    },
    getAllPosts: function () {
      this.loading = true
      if ( Object.keys(this.$route.query).length != 0){
        this.parseQueryStrings()
      }else{
        axios.get(endpoint + 'archives', {
          params: {
            'count': -1
          }
        })
        .then(response => {
          this.successfulResponse
        })
        .catch(e => {
          this.failure = true
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
      this.loading = true

      let chosenTemplate = this.$route.query.template
      let chosenCat = this.$route.query.category

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
          this.successfulResponse
        })
        .catch(e => {
          this.failure = true
        })

      function isChosenCat(element) {
        return element.id = chosenCat;
      }

      this.selectedCat = this.categories.find(isChosenCat)

      this.$forceUpdate();

    },
    onSubmit: function (event) {
      this.loading = true
      axios.get(endpoint + 'archives', {
        params : {
          's': this.searchedVal,
          'template': this.checkedTemplates,
          'category': this.selectedCat,
          'count': -1,
          'start_date': this.state.startDate,
          'end_date': this.state.endDate,
          }
        })
        .then(response => {
          this.successfulResponse
        })
        .catch(e => {
          this.failure = true
      })
    },
    filterByCategory: function(selectedVal){
      this.loading = true

      this.selectedCat = selectedVal

      axios.get(endpoint + 'archives', {
        params : {
          //'category' : selectedVal,
          'template' : this.checkedTemplates,
          'category': this.selectedCat,
          'count' : -1,
          's': this.searchedVal,
          'start_date': this.state.startDate,
          'end_date': this.state.endDate,
          }
        })
        .then(response => {
          this.successfulResponse
        })
        .catch(e => {
        console.log(e);
      })
    },
    reset() {
      axios.get(endpoint + 'archives')
        .then(response => {
          this.posts = response.data
          this.loading = false

        })
        .catch(e => {
        console.log(e);
      })

    },
    runDateQuery(){
      if ( !this.state.startDate || !this.state.endDate )
        return;
        this.loading = true

      axios.get(endpoint + 'archives', {
        params : {
          'category' : this.selectedVal,
          'template' : this.checkedTemplates,
          's': this.searchedVal,
          'count' : -1,
          'start_date': this.state.startDate,
          'end_date': this.state.endDate,
          }
        })
        .then(response => {
          this.successfulResponse
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
      this.loading = true
      axios.get(endpoint + 'archives', {
        params : {
          'template' : newVal,
          'category': this.selectedCat,
          'count' : -1,
          's': this.searchedVal,
          'start_date': this.state.startDate,
          'end_date': this.state.endDate,
          }
        })
      .then(response => {
        this.posts = response.data
        this.loading = false
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
.filter-by-owner .v-select input[type=search],
.filter-by-owner .v-select input[type=search]:focus {
  width:7rem !important;
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
.vdp-datepicker [type='text'] {
  height: 2.4rem;
}
.vdp-datepicker input:read-only{
  background: white;
  cursor: pointer;
}
#archive-results .vdp-datepicker__calendar .cell.selected,
#archive-results .vdp-datepicker__calendar .cell.selected.highlighted,
#archive-results .vdp-datepicker__calendar .cell.selected:hover{
  background: #25cef7;
}
</style>
