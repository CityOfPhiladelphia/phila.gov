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
          <div class="cell medium-8 small-11">
            <datepicker
            name="startDate"
            :value="state.onLoad"
            v-on:closed="runDateQuery"
            v-model="state.startDate"></datepicker>
          </div>
          <div class="cell medium-1 small-2 mts">
            <i class="fa fa-arrow-right"></i>
          </div>
          <div class="cell medium-8 small-11">
            <datepicker placeholder="End date"
            name="endDate"
            v-on:closed="runDateQuery"
            v-model="state.endDate"></datepicker>
          </div>
          <div class="cell medium-7 small-24">
            <a class="button content-type-featured full" @click="reset">Clear filters</a>
          </div>
        </div>
      </div>
    <div v-show="loading" class="mtm center">
      <i class="fa fa-spinner fa-spin fa-3x"></i>
    </div>
    <div v-show="emptyResponse" class="h3 mtm center">Sorry, there are no results.</div>
    <div v-show="failure" class="h3 mtm center">Sorry, there was a problem. Please try again.</div>
    <div class="" v-show="!loading && !emptyResponse && !failure">
      <paginate name="events"
        :list="events"
        class="paginate-list"
        tag="div"
        :per="20">
        <div v-for="(event, index) in events"
        :key="event.id">
          <div v-if="event.id">
            <div class="row event-row medium-collapse equal-height"
            :data-open="event.id">
              <div class="small-6 medium-3 columns calendar-date equal">
                <div class="valign">
                  <div class="valign-cell">
                    <div class="month">{{ event.start.dateTime | formatMonth }}</div>
                    <div class="day">
                      <span v-if="event.start.dateTime">{{event.start.dateTime | formatDay}}</span>
                       <span v-else>
                         {{event.start.date | formatDay }}
                       </span>
                       </div>
                  </div>
                </div>
              </div>
              <div class="small-18 medium-21 columns calendar-details equal">
                <div class="post-label post-label--calendar"><i class="fa fa-calendar-o fa-lg" aria-hidden="true"></i>
                <span>Event</span></div>
                <div class="title">{{event.summary}}</div>
                <div class="start-end">
                  {{event.start.dateTime | formatTime }} to {{event.end.dateTime | formatTime }}</div>
                <div class="location">{{event.location}}</div>
              </div>
            </div>
            <div
              v-bind:id="event.id"
              class="reveal reveal--calendar"
              data-reveal=""
              data-deep-link="true"
              data-update-history="true"><button class="close-button" type="button" data-close="" aria-label="Close modal">
            <span aria-hidden="true">×</span>
            </button>
            <div class="post-label post-label--calendar"><i class="fa fa-calendar-o fa-lg" aria-hidden="true"></i> <span>Event</span></div>
            <h3>{{event.summary}}</h3>
            <div class="mbm">{{event.start.dateTime | formatDate}}
              <div class="start-end">[if-whole-day]All Day[/if-whole-day][if-not-whole-day][start-time] to [end-time], [duration][/if-not-whole-day]</div>
              <div class="location">{{event.location}}</div>
              [end-location-link]map[/end-location-link]

              </div>
              {{event.description}}
              <div class="post-meta mbm reveal-footer">[display_category]</div>
            </div>
          </div>
        </div>
        </paginate>
      </div>
      <!--<paginate-links for="events"
      :limit="3"
      :show-step-links="true"
      :step-links="{
        next: 'Next',
        prev: 'Previous'
      }"
      :async="true"
      v-show="!loading && !emptyResponse && !failure"></paginate-links>-->
  </div>
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
  name: 'events-archive',
  components: {
    vSelect,
    Datepicker,
    'phila-search': Search
  },
  data: function() {
    return{
      calendars: JSON.parse(g_cal_data.json),
      calData: [{}],

      events: [{
        start: {
          dateTime: '',
          date: ''
        },
        end: {
          dateTime: '',
          date: ''
        },
      }],

      //selectedCategory: '',

      search: '',
      searchedVal: '',

      loading: false,
      emptyResponse: false,
      failure: false,

      paginate: ['events'],

      state: {
        onLoad: moment(),
        startDate: '',
        endDate: ''
      },

      //queriedCategory: this.$route.query.category

    }
  },
  filters: {
    'formatMonth': function(value) {
      if (value) {
        return moment( String(value) ).format('MMM')
      }
    },
    'formatDay': function(value) {
      if (value) {
        return moment( String(value) ).format('D')
      }
    },
    'formatDate': function(value) {
      if (value) {
        return moment( String(value) ).format('MMM. DD, YYYY')
      }
    },
    'formatTime': function(value) {
      if (value) {
        return moment( String(value) ).format('LT')
      }
    },
  },
  mounted: function () {
    this.getUpcomingEvents()
    //this.getDropdownCategories()
    //this.loading = true
  },
  methods: {
    sortArray: function (prop, arr) {
      console.log('yeah')
      prop = prop.split('.');
      var len = prop.length;

      arr.sort(function (a, b) {
          var i = 0;
          while( i < len ) {
              a = a[prop[i]];
              b = b[prop[i]];
              i++;
          }
          if (a < b) {
            console.log(a)
              return -1;
          } else if (a > b) {
          console.log(b)
              return 1;
          } else {
              return 0;
          }
      });
      return arr;
  },
    getUpcomingEvents: function () {
    //  this.loading = true
    const links = []

    //const calendars = JSON.parse(g_cal_data.json)

      for( var i = 0; i < this.calendars.length; i++ ){
        links.push(gCalEndpoint + this.calendars[i] + '/events/?key=' + gCalId + '&maxResults=20&timeMax=' + moment().format("YYYY-MM-DDTHH:mm:ssZ") )
      }
      console.log(links)

      axios.all( links.map( l => axios.get( l ) ) )
        .then(response =>  {
          this.calData = response
          const temp = []

          for (var j = 0; j < this.calData.length; j++ ){
            for(var k = 0; k < response[j].data.items.length; k++) {
              this.events.push(response[j].data.items[k])
           }
          }

          console.log(this.events)

        })
        .catch( e => {
            this.failure = true
        })

      //
      //
      // axios.get('/wp-json/the-latest/v1/categories')
      //   .then(response => {
      //     this.loading = false
      //     console.log(response.data)
      //     this.events = response.data
      //     //this.successfulResponse
      //   })
      //   .catch(e => {
      //     this.failure = true
      //   })

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
