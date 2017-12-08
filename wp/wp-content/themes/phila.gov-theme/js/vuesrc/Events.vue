<template>
  <div id="events">
    <form v-on:submit.prevent="onSubmit">
      <div class="search">
        <input id="post-search" type="text" name="search" placeholder="Search by title" class="search-field" ref="search-field"
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
            ref="categorySelect"
            label="slang_name"
            placeholder="All departments"
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
        <tr><th class="title">Title</th><th class="date">Publish date</th><th>Department</th></tr>
      </thead>
      <paginate name="events"
        :list="events"
        class="paginate-list"
        tag="tbody"
        :per="40">
        <tr v-for="event in paginated('events')"
        :key="event.id"
        class="vue-clickable-row"
        v-on:click.stop.prevent="goToDoc(event.link)">
          <td class="title">
            <a v-bind:href="event.link" v-on:click.prevent="goToDoc(event.link)">
              {{ event.title }}
            </a>
          </td>
          <td class="date">{{ event.date  | formatDate }}</td>
          <td class="categories">
            <span v-for="(category, i) in event.categories">
              <span>{{ category.slang_name }}</span><span v-if="i < event.categories.length - 1">,&nbsp;</span>
            </span>
          </td>
        </tr>
      </paginate>
    </table>
    <paginate-links for="events"
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
import Search from './components/phila-search.vue'


const gCalEndpoint = 'https://www.googleapis.com/calendar/v3/calendars/'

let state = {
  date: new Date()
}
const gCalId = g_cal_id

export default {
  name: 'events',
  components: {
    vSelect,
    Datepicker,
    'phila-search': Search
  },
  data: function() {
    return{
      calendars: JSON.parse(g_cal_data.json),
      events: [],
      categories: [{ }],

      selectedCategory: '',

      search: '',
      searchedVal: '',

      loading: false,
      emptyResponse: false,
      failure: false,

      paginate: ['events'],

      state: {
        startDate: '',
        endDate: ''
      },

      //queriedCategory: this.$route.query.category

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
    this.getUpcomingEvents()
    //this.getDropdownCategories()
    this.loading = true
  },
  methods: {
    getUpcomingEvents: function () {
    //  this.loading = true
    const links = []

    const calendars = JSON.parse(g_cal_data.json)

    console.log( Array.from(calendars) )


      for( var i = 0; i < calendars.length; i++ ){
        links.push(gCalEndpoint + calendars[i] + '/events/?key=' + gCalId )
      }
      console.log(links)

        //
        // .then(response => {
        //   this.loading = false
        //   console.log(response.data)
        //   this.events = response.data
        //   //this.successfulResponse
        // })
        // .catch(e => {
        //   this.failure = true
        // })

    },
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
    onSubmit: function (event) {
      this.loading = true

      this.$nextTick(function () {
        axios.get(gCalEndpoint + 'archives', {
          params : {
            's': this.searchedVal,
            'category': this.selectedCategory,
            'count': -1,
            'start_date': this.state.startDate,
            'end_date': this.state.endDate,
            }
          })
          .then(response => {
            this.loading = false
            this.events = response.data
            this.successfulResponse
          })
          .catch(e => {
            this.failure = true
        })
      })
    },
    reset() {
      //this.loading = true
      //console.log(this.$refs.categorySelect)
      //console.log(this.$refs.categorySelect.$el.textContent)
      window.location = window.location.pathname;
      /*this.selectedCategory = ''
      axios.get(gCalEndpoint + 'archives', {
       params : {
          'count': -1
        }
      })
        .then(response => {
          this.events = response.data
          this.loading = false
          this.searchedVal = ''
          this.checkedTemplates = ''
          this.selectedCategory = ''
          this.state.startDate = ''
          this.state.endDate = ''
        })
        .catch(e => {
          this.failure = true
      })
      this.$forceUpdate();
      */
    },
    runDateQuery(){
      if ( !this.state.startDate || !this.state.endDate )
        return;

      this.loading = true

      axios.get(gCalEndpoint + 'archives', {
        params : {
          's': this.searchedVal,
          'category': this.selectedCategory,
          'count': -1,
          'start_date': this.state.startDate,
          'end_date': this.state.endDate,
          }
        })
        .then(response => {
          this.loading = false
          this.events = response.data
          this.successfulResponse
        })
        .catch(e => {
          this.failure = true
      })
    },
    filterByCategory: function(selectedVal){
      this.selectedCategory = selectedVal

      this.$nextTick(function () {

        this.loading = true

        axios.get(gCalEndpoint + 'archives', {
          params : {
            's': this.searchedVal,
            'category': this.selectedCategory.id,
            'count' : -1,
            'start_date': this.state.startDate,
            'end_date': this.state.endDate,
            }
          })
          .then(response => {
            this.loading = false
            //Don't let empty value change the rendered view
            if ( 'id' in selectedVal ){
              this.events = response.data
            }
            this.successfulResponse
          })
          .catch(e => {
            this.failure = true
        })
      })
    },
  },
  computed:{
    successfulResponse: function(){
      if (this.events.length == 0) {
        this.emptyResponse = true
      }else{
        this.emptyResponse = false
      }
    },
  },

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
