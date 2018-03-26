<template>
  <div id="publications">
    <form v-on:submit.prevent>
      <div class="search">
        <input id="post-search" type="text" name="search" placeholder="Begin typing to filter by title" class="search-field" ref="search-field"
        v-model="searchedVal">
        <input type="submit" value="submit" class="search-submit">
      </div>
    </form>
    <div id="filter-results" class="bg-ghost-gray pam">
      <div class="h5">Filter results</div>
        <div class="grid-x grid-margin-x">
          <div class="cell medium-4 small-11">
            <datepicker
            name="startDate"
            placeholder="Start date"
            v-on:closed="runDateQuery"
            v-model="state.startDate"
            format="MMM. dd, yyyy"
            :disabled="state.disabled"></datepicker>
          </div>
          <div class="cell medium-1 small-2 mts">
            <i class="fa fa-arrow-right"></i>
          </div>
          <div class="cell medium-4 small-11">
            <datepicker
            placeholder="End date"
            name="endDate"
            v-on:closed="runDateQuery"
            v-model="state.endDate"
            format="MMM. dd, yyyy"
            :disabled="state.disabled"></datepicker>
          </div>
          <div class="cell medium-9 small-24 auto filter-by-owner">
            <v-select
            label="slang_name"
            placeholder="All departments"
            v-model="selected"
            :options="categories"
            :on-change="filterByCategory">
            </v-select>
          </div>
          <div class="cell medium-6 small-24">
            <a class="button content-type-featured full" @click="reset">Clear filters</a>
          </div>
        </div>
      </div>
    <div v-show="loading" class="mtm center">
      <i class="fa fa-spinner fa-spin fa-3x"></i>
    </div>
    <div v-show="emptyResponse" class="h3 mtm center">Sorry, there are no results.</div>
    <div v-show="failure" class="h3 mtm center">Sorry, there was a problem. Please try again.</div>

    <table class="stack theme-light archive-results"  data-sticky-container v-show="!loading && !emptyResponse && !failure">
      <thead class="sticky center bg-white" data-sticky data-top-anchor="filter-results:bottom" data-btm-anchor="page:bottom" data-options="marginTop:4.8;">
        <tr>
          <th class="table-sort title"
          @click="sort('title')" v-bind:class="sortTitle"><span>Title</span></th>

          <th class="table-sort date"
          @click="sort('date')"
          v-bind:class="sortDate"><span>Publish date</span></th>
          <th class="department">Department</th>
        </tr>
      </thead>
      <paginate name="documents"
        :list="sortedDocuments"
        class="paginate-list"
        tag="tbody"
        :per="40">
        <tr v-for="document in paginated('documents')"
        :key="document.id"
        class="vue-clickable-row"
        v-on:click.stop.prevent="goToDoc(document.link)">
          <td class="title">
            <a v-bind:href="document.link" v-on:click.prevent="goToDoc(document.link)">
              {{ document.title }}
            </a>
          </td>
          <td class="date">{{ document.date  | formatDate }}</td>
          <td class="categories">
            <span v-for="(category, i) in document.categories">
              <span>{{ category.slang_name }}</span><span v-if="i < document.categories.length - 1">,&nbsp;</span>
            </span>
          </td>
        </tr>
      </paginate>
    </table>
    <paginate-links for="documents"
    :limit="3"
    :show-step-links="true"
    :hide-single-page="true"
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
import Search from './components/phila-search.vue'


const pubsEndpoint = '/wp-json/publications/v1/'

export default {
  name: 'publications',
  components: {
    vSelect,
    Datepicker,
    'phila-search': Search
  },
  data: function() {
    return{
      documents: [],
      categories: [{ }],

      currentSort:'date',
      currentSortDir:'desc',

      selected: null,

      selectedCategory: '',

      search: '',
      searchedVal: '',

      loading: false,
      emptyResponse: false,
      failure: false,

      paginate: ['documents'],

      state: {
        startDate: '',
        endDate: '',
        disabled: {
          to: new Date(2015, 1, 1),
          from: new Date()
        }
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
    this.getDropdownCategories()
    this.loading = true
  },
  created: function(){
    this.loading = true

    axios.get(pubsEndpoint + 'archives', {
      params: {
        'count': -1,
      }
    })
    .then(response => {
      this.documents = response.data
      this.successfulResponse
    })
    .catch(e => {
      this.failure = true
      this.loading = false
    })
  },
  methods: {
    getDropdownCategories: function () {
      axios.get('/wp-json/the-latest/v1/categories')
      .then(response => {
        this.categories = response.data
      })
      .catch(e => {
        this.categories = 'Sorry, there was a problem.'
      })
    },
    goToDoc: function (link){
      window.location.href = link
    },
    reset() {

      this.searchedVal = ''
      this.state.startDate = ''
      this.state.endDate = ''
      //a little convoluted, but will change the state of selected if the reset button is used mutiple times in a session
      this.selected = (this.selected == null ? '' : null)
      this.runDateQuery()
      this.filterByCategory()

    },
    runDateQuery(){
      if ( !this.state.startDate || !this.state.endDate )
        return;

      this.loading = true

      axios.get(pubsEndpoint + 'archives', {
        params : {
          's': this.searchedVal,
          'category': this.selectedCategory,
          'count': -1,
          'start_date': this.state.startDate,
          'end_date': this.state.endDate,
          }
        })
        .then(response => {
          this.documents = response.data
          this.successfulResponse
        })
        .catch(e => {
          this.failure = true
          this.loading = false
      })
    },
    filterByCategory: function(selectedVal){
      if (selectedVal == null){
        this.selectedCategory = ''
      }else{
        this.selectedCategory = selectedVal.id
      }

      this.loading = true

      axios.get(pubsEndpoint + 'archives', {
        params : {
          's': this.searchedVal,
          'category': this.selectedCategory,
          'count' : -1,
          'start_date': this.state.startDate,
          'end_date': this.state.endDate,
          }
        })
        .then(response => {
          this.loading = false
          this.documents = response.data

          this.successfulResponse
        })
        .catch(e => {
          this.failure = true
          this.loading = false
      })
    },
    sort: function( column ) {
      console.log( column )
      //if column == current sort, reverse
      if(column === this.currentSort) {
        this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' : 'asc';
      }
      this.currentSort = column;
   },
   filteredList: function ( list, searchedVal ) {
     let searched = this.searchedVal.trim()
     return list.filter((document) => {
        return document.title.toLowerCase().indexOf(searched.toLowerCase()) > -1
     })
   },
  },
  computed:{
    sortTitle: function(clicked){
      if (this.currentSort == 'title') {
        return this.currentSortDir
      }
    },
    sortDate: function(){
      if (this.currentSort == 'date'){
        return this.currentSortDir
      }

    },
    successfulResponse: function(){
      if (this.documents.length == 0) {
        this.emptyResponse = true
        this.loading = false
        this.failure = false
      }else{
        this.emptyResponse = false
        this.loading = false
        this.failure = false
      }
    },
    sortedDocuments:function() {
      return this.filteredList(this.documents.sort((a,b) => {
        let modifier = 1;
        if(this.currentSortDir === 'desc') modifier = -1;
        if(a[this.currentSort] < b[this.currentSort]) return -1 * modifier;
        if(a[this.currentSort] > b[this.currentSort]) return 1 * modifier;
        return 0;
      }), this.searchedVal)
    },
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
  padding: .6rem 1.5rem 1rem .8rem;
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
  width:8rem !important;
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
