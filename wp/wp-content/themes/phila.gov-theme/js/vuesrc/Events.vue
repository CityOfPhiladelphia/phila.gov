<template>
  <div id="events">
    <form v-on:submit.prevent>
      <div class="search">
        <input id="post-search" type="text" name="search"
        placeholder="Filter events by title" class="search-field" ref="search-field"
        v-model="searchedVal">
        <input type="submit" value="submit" class="search-submit">
      </div>
    </form>
    <div id="filter-results" class="bg-ghost-gray pam mbm">
      <div class="h5">Filter results</div>
      <div class="grid-x grid-margin-x">
        <div class="cell medium-4 small-11">
          <datepicker
          placeholder="Start date"
          name="startDate"
          v-on:closed="runDateQuery"
          v-model="state.startDate"
          format="MMM. dd, yyyy"></datepicker>
        </div>
        <div class="cell medium-1 small-2 mts">
          <i class="fa fa-arrow-right"></i>
        </div>
        <div class="cell medium-4 small-11">
          <datepicker
          name="endDate"
          placeholder="End date"
          v-on:closed="runDateQuery"
          v-model="state.endDate"
          format="MMM. dd, yyyy"></datepicker>
        </div>
        <div class="cell medium-9 small-24 filter-by-owner">
          <v-select
          placeholder="All departments"
          :options="dropdown"
          label="name"
          :value="parseCategory"
          :on-change="getByCategory">
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
    <div v-show="!loading && !emptyResponse && !failure">
      <div v-for="(event, index) in filteredEvents"
        :key="event.id">
          <div v-if="event.id" class="event-container">
            <div class="grid-x grid-padding-x event-row medium-collapse"
            @click="$modal.show(event.id)">
              <div class="small-6 medium-3 cell calendar-date pam">
                <div class="align-self-middle">
                  <div class="month">
                    <span v-if="event.start.dateTime">{{ event.start.dateTime | formatMonth }}</span>
                    <span v-else>{{ event.start.date | formatMonth }}</span>
                  </div>
                  <div class="day">
                    <span v-if="event.start.dateTime">{{event.start.dateTime | formatDay}}</span>
                    <span v-else>
                      {{event.start.date | formatDay }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="small-18 medium-21 cell calendar-details pam">
                <div class="post-label post-label--calendar"><i class="fa fa-calendar-o fa-lg" aria-hidden="true"></i>
                  <span>Event</span>
                </div>
                <div class="title">{{event.summary}}</div>
                <div
                v-if="event.start.dateTime"
                class="start-end">
                  {{event.start.dateTime | formatTime }} to {{event.end.dateTime | formatTime }}
                </div>
                <div v-else>
                  All day
                </div>
                <div class="location">{{event.location}}</div>
              </div>
            </div>
          </div>
        </div>
      <div v-if="filteredEvents == ''">
        <p class="h3 mtm center">Sorry, there are no results for that search.</p>
      </div>
    </div>
      <div v-for="(event, index) in events"
        :key="event.index">
        <modal
        :name="event.id"
        height="auto"
        :adaptive="adaptive"
        :scrollable="true">
        <div class="v--modal-container">
          <div slot="top-right">
            <button @click="$modal.hide(event.id)" class="close-button" type="button" aria-label="Close modal">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="post-label post-label--calendar"><i class="fa fa-calendar-o fa-lg" aria-hidden="true"></i>
            <span>Event</span>
          </div>
          <h3>{{event.summary}}</h3>
          <div class="location mvm">{{event.location}}</div>
          <div
          v-if="event.start.dateTime"
          class="start-end mvm">
            {{event.start.dateTime | formatDate }}<br />
            {{event.start.dateTime | formatTime }} to {{event.end.dateTime | formatTime }}<br />
          </div>
          <div class="mvm" v-else>
            {{event.start.date | formatDate }}<br />
            All day
          </div>
          <div class="mbm">
            <div v-html="event.description"></div>
          </div>
          <div class="post-meta mbm reveal-footer">Posted by: <span v-html="event.ownerMarkup"></span>
          </div>

        </div>
      </modal>
    </div>
  </div>
</template>

<script>
import Vue from 'vue'
import moment from 'moment'
import axios from 'axios'
import vSelect from 'vue-select'
import Datepicker from 'vuejs-datepicker';
import Search from './components/phila-search.vue'

const gCalEndpoint = 'https://www.googleapis.com/calendar/v3/calendars/'
const links = []

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
      //g_cal_data & calendar_owners set in the-latest-events-archive.php
      calendars: [JSON.parse(g_cal_data.json)],
      owner: [calendar_owners.json],
      dropdown: Object.values(JSON.parse(calendar_nice_names)),

      eventOwners: [{}],
      eventCategory: [{}],

      calData: [{}],

      events: [{
        id: '',
        ownerMarkup: {},
        ownerCategoryId: '',

        summary: '',
        start: {
          dateTime: '',
          date: ''
        },
        end: {
          dateTime: '',
          date: ''
        },
      }],

      selectedCategory: '',
      queriedCategory: this.$route.query.category,

      search: '',
      searchedVal: '',

      loading: false,
      emptyResponse: false,
      failure: false,

      state: {
        startDate: moment().format(),
        endDate: moment().add(90, 'days').format(),
      },

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
        return moment( String(value) ).format('MMMM DD, YYYY')
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
    this.loading = true
  },
  methods: {
    getUpcomingEvents: function () {
      //set data for use later
      this.selectedCategory = this.queriedCategory

      let cal_ids = this.calendars.map(d=>{ return Object.values(d) });

      //reindex this.owner
      let cal_owners = this.owner.map(d=>{ return Object.values(d) });

      let cal_cat = Object.keys(this.owner[0])

      if ( this.selectedCategory == undefined) {
        for( let i = 0; i < cal_ids[0].length; i++ ){
          //console.log(cal_ids[0][i])
          links.push(gCalEndpoint + cal_ids[0][i] + '/events/?key=' + gCalId + '&maxResults=10&singleEvents=true&orderBy=startTime&timeMin=' + moment().format() )
        }
      }
      axios.all( links.map( l => axios.get( l ) ) )
        .then(response =>  {
          this.calData = response

          for (let j = 0; j < this.calData.length; j++ ){

            for(let k = 0; k < response[j].data.items.length; k++) {

              this.events.push(response[j].data.items[k])
              this.$set(this.events, response[j].data.items,

              response[j].data.items[k])

              //TODO: this is kind of convoluted
              this.eventOwners.push(cal_owners[0][j])
              this.eventCategory.push(cal_cat[j])

            }
          }

          for (let l = 0; l < this.events.length; l++){
            this.$set(this.events[l], 'ownerMarkup', this.eventOwners[l])
            this.$set(this.events[l], 'ownerCategoryId', this.eventCategory[l])

          }

          this.successfulResponse
        })
        .catch( e => {
          this.failure = true
          this.loading = false
        })
    },
    reset() {
      window.location = window.location.pathname;
    },
    runDateQuery(){
      if ( !this.state.startDate || !this.state.endDate )
        return;

      //reset data
      this.events = [{
        id: '',
        ownerMarkup: {},
        ownerCategoryId: '',

        summary: '',
        start: {
          dateTime: '',
          date: ''
        },
        end: {
          dateTime: '',
          date: ''
        },
      }]

      let links = []
      let cal_ids = this.calendars.map(d=>{ return Object.values(d) });

      let cal_owners = this.owner.map(d=>{ return Object.values(d) });

      let cal_cat = Object.keys(this.owner[0])

      if (this.queriedCategory != this.selectedCategory){

        links.push(gCalEndpoint + this.calendars[0][this.selectedCategory] + '/events/?key=' + gCalId + '&singleEvents=true&orderBy=startTime&timeMin='  + moment(String(this.state.startDate)).format() + '&timeMax=' + moment(String(this.state.endDate)).format() )

      }else if (this.queriedCategory != ''){

        links.push(gCalEndpoint + this.calendars[0][this.queriedCategory] + '/events/?key=' + gCalId + '&singleEvents=true&orderBy=startTime&timeMin=' + moment(String(this.state.startDate)).format() + '&timeMax=' + moment(String(this.state.endDate)).format() )
      }

      axios.all( links.map( l => axios.get( l ) ) )
        .then(response =>  {
          this.calData = response

          for (let j = 0; j < this.calData.length; j++ ){

            for(let k = 0; k < response[j].data.items.length; k++) {

              this.events.push(response[j].data.items[k])
              this.$set(this.events, response[j].data.items,response[j].data.items[k])

              //TODO: this is kind of convoluted
              this.eventOwners.push(cal_owners[0][j])
              this.eventCategory.push(cal_cat[j])

            }
          }

          for (let l = 0; l < this.events.length; l++){
            this.$set(this.events[l], 'ownerMarkup', this.eventOwners[l])
            this.$set(this.events[l], 'ownerCategoryId', this.eventCategory[l])

          }

          this.successfulResponse
        })
        .catch( e => {
          this.failure = true
        })

    },
    filteredList: function ( list, searchedVal ) {
      let searched = this.searchedVal.trim()
      return list.filter((event) => {
        //console.log(event)
        if (typeof event.summary === 'undefined'){
          return
        }else{
          return event.summary.toLowerCase().indexOf(searched.toLowerCase()) > -1
        }
      })
    },
    sortedItems: function ( list ) {
      return list.sort((a, b) => {
        return moment(a.start.dateTime) - moment(b.start.dateTime) + moment(a.start.date) - moment(b.start.date)
      })
    },
    getByCategory: function(selectedVal){
      this.loading = true

      //reset data
      this.events = [{
        id: '',
        ownerMarkup: {},
        ownerCategoryId: '',

        summary: '',
        start: {
          dateTime: '',
          date: ''
        },
        end: {
          dateTime: '',
          date: ''
        },
      }]

      this.eventOwners = [{}]
      this.eventCategory = [{}]

      if (selectedVal == null){
        this.getUpcomingEvents()
        return
      }else{
        this.selectedCategory = selectedVal.id
      }

      axios.get(gCalEndpoint + this.calendars[0][selectedVal.id] + '/events/?key=' + gCalId + '&singleEvents=true&orderBy=startTime&timeMin='  + moment(String(this.state.startDate)).format() + '&timeMax=' + moment(String(this.state.endDate)).format() )
      .then(response =>  {
        this.calData = response

        for(let k = 0; k < response.data.items.length; k++) {
          this.events.push(response.data.items[k])
          this.$set(this.events, response.data.items, response.data.items[k])

          //TODO: this is kind of convoluted
          this.eventOwners.push(this.owner[0][selectedVal.id])
          this.eventCategory.push(this.owner[0][selectedVal.id])
        }

        for (let l = 0; l < this.events.length; l++){
          this.$set(this.events[l], 'ownerMarkup', this.eventOwners[l])
          this.$set(this.events[l], 'ownerCategoryId', this.eventCategory[l])

        }

        this.successfulResponse
        })
        .catch(e => {
          this.failure = true
          this.loading = false
        })
    },
  },
  computed:{
    filteredEvents: function(){
        return this.sortedItems(
                this.filteredList(this.events, this.searchedVal
              )
        )
    },
    parseCategory: function(){
      let c = this.$route.query.category
      let catName = {}
      if (c) {
        let mycats = this.dropdown
        this.dropdown.forEach(function(el){
          if (c == el.id) {
            catName = {
              id: el.id,
              name: el.name
            }
          }
        })
        return catName
      }
    },
    successfulResponse: function(){
      //account for empty data object
      if (this.events.length == 1 || this.events.length == 0 ) {
        this.emptyResponse = true
        this.loading = false
        this.failure = false
      }else{
        this.emptyResponse = false
        this.loading = false
        this.failure = false
      }
    },
  },
}
</script>

<style>
@media screen and (max-width: 39.9375em) {
  .v--modal-overlay.scrollable .v--modal-box{
    width:100% !important;
    height:100% important;
  }
}
.v--modal-container{
  position: relative;
}
.v--modal-container button{
  background: transparent;
  padding:0;
}
.v--modal-overlay{
  z-index:9990 !important;
}
.v--modal-overlay .v--modal-box{
  border-bottom:5px solid green;
  padding:1rem;
}
.v--modal{
  border-radius: 0;
  box-shadow:none;
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
