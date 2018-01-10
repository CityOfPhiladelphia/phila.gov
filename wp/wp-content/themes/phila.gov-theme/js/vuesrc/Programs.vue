<template>
  <div id="programs-initiatives-landing">
    <div class="grid-x grid-margin-x">
      <div class="small-24 medium-8 cell">
        <section>
          <div class="panel phm">
            <h3>Search within Programs</h3>
            <form v-on:submit.prevent="onSubmit">
              <div class="search">
                <input id="post-search" type="text" name="search" placeholder="Search by title, or keyword" class="search-field" ref="search-field"
                v-model="searchedVal">
                <input type="submit" value="submit" class="search-submit">
              </div>
            </form>
          </div>
          <fieldset>
            <h4>Filter by audience</h4>
              <div v-for="(value, key) in audience">
                <input type="checkbox"
                v-model="checkedAudiences"
                v-bind:value="value.slug"
                v-bind:name="value.slug"
                v-bind:id="value.slug"
                @click="onSubmit" />
                <label v-bind:for="value.slug">{{ value.name }}</label>
              </div>
          </fieldset>
          <fieldset>
            <h4>Filter by category</h4>
              <div v-for="(value, key) in service_type">
                <input type="checkbox"
                v-model="checkedServiceType"
                v-bind:value="value.slug"
                v-bind:name="value.slug"
                v-bind:id="value.slug"
                @click="onSubmit" />
                <label v-bind:for="value.slug"><span v-html="value.name"></span></label>
              </div>
          </fieldset>
        </section>
      </div>
      <div id="program-results">
        <div v-show="loading" class="mtm center">
          <i class="fa fa-spinner fa-spin fa-3x"></i>
        </div>
        <div v-show="emptyResponse" class="h3 mtm center">Sorry, there are no results.</div>
        <div v-show="failure" class="h3 mtm center">Sorry, there was a problem. Please try again.</div>
        <div class="grid-x grid-margin-x grid-padding-x program-archive-results" v-show="!loading && !emptyResponse && !failure">
          <div class="program-card card medium-12">
            <h3 data-title="program-title"></h3>
            <div data-image></div>
            <div data-template></div>
            <div data-short_description></div>
            <div data-link></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

const programsEndpoint = '/wp-json/programs/v1'
const audienceEndpoint = '/wp-json/wp/v2/audience'
const serviceTypeEndpoint = '/wp-json/wp/v2/service_type'

export default {
  name: 'program-archives',
  components: {
  },
  data: function() {
    return{
      programs: [],

      audience: [{ }],
      service_type: [{ }],

      checkedAudiences: [{ }],
      checkedServiceType: [{ }],

      searchedVal: '',

      loading: false,
      emptyResponse: false,
      failure: false,

      //queriedTemplate: this.$route.query.template,
      //queriedCategory: this.$route.query.category

    }
  },
  filters: {
    decodeHtml: function( html ) {
      var txt = document.createElement('textarea');
      txt.innerHTML = html;
      return txt.value;
    }
  },
  mounted: function () {
    this.getAudiences()
    this.getServices()
    this.getAllPrograms()
    this.loading = true
  },
  methods: {
    getAllPrograms: function () {
      this.loading = true
      //TODO use in instead of undefined
      axios.get(programsEndpoint + 'archives', {
        params: {
          's': this.searchedVal,
          'per_page': 20,
        }
      })
      .then(response => {
        this.programs = response.data
        this.successfulResponse
      })
      .catch(e => {
        this.failure = true
        this.loading = false
      })
    },
    getAudiences: function () {
      axios.get(audienceEndpoint, {
        params: {
          'per_page': 30,
          'hide_empty': true
        }
      })
      .then(response => {
        this.audience = response.data
        console.log(response.data)
      })
      .catch(e => {
        this.audience = 'Sorry, there was a problem.'
      })
    },
    getServices: function () {
      axios.get(serviceTypeEndpoint, {
        params: {
          'per_page': 30,
          'hide_empty': true
        }
      })
      .then(response => {
        this.service_type = response.data
        console.log(response.data)
      })
      .catch(e => {
        this.service_type = 'Sorry, there was a problem.'
      })
    },
    goToPost: function (link){
      window.location.href = link
    },
    onSubmit: function (event) {
      this.loading = true

      this.$nextTick(function () {
        axios.get(programsEndpoint, {
          params : {
            's': this.searchedVal,
            'count': -1,
            }
          })
          .then(response => {
            console.log('fired')
            this.programs = response.data
            this.successfulResponse
          })
          .catch(e => {
            this.failure = true
            this.loading = false
        })
      })
    },
    reset() {
      //this.loading = true
      //console.log(this.$refs.categorySelect)
      //console.log(this.$refs.categorySelect.$el.textContent)
      window.location = window.location.pathname;
      /*this.selectedCategory = ''
      axios.get(programsEndpoint + 'archives', {
       params : {
          'count': -1
        }
      })
        .then(response => {
          this.programs = response.data
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
  },
  computed:{
    successfulResponse: function(){
      if (this.programs.length == 0) {
        this.emptyResponse = true
        this.loading = false
        this.failure = false
      }else{
        this.emptyResponse = false
        this.loading = false
        this.failure = false
      }
    },
    parseCategory: function(){
      let c = this.$route.query.category
      let catName = {}
      if (c) {
        let mycats = this.categories
        this.categories.forEach(function(el){
          if (c == el.id) {
            catName = {
              id: el.id,
              slang_name: el.slang_name
            }
          }
        })
        return catName
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
