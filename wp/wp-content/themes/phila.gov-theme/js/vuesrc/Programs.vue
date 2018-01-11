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
                @click="filterResults"/>
                <label v-bind:for="value.slug">{{ value.name }}</label>
              </div>
          </fieldset>
          <fieldset>
            <h4>Filter by category</h4>
              <div v-for="(value, key) in service_type">
                <input type="checkbox"
                v-bind:value="value.slug"
                v-bind:name="value.slug"
                v-bind:id="value.slug"
                @click="filterResults"/>
                <label v-bind:for="value.slug"><span v-html="value.name"></span></label>
              </div>
          </fieldset>
        </section>
      </div>
      <div class="cell medium-16">
        <div id="program-results" class="grid-x grid-margin-x grid-padding-x">
          <div v-show="loading" class="mtm center">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
          </div>
          <!--<div v-show="emptyResponse" class="h3 mtm center">Sorry, there are no results.</div>
          <div v-show="failure" class="h3 mtm center">Sorry, there was a problem. Please try again.</div>
          <div class="grid-x grid-margin-x grid-padding-x program-archive-results" v-show="!loading && !emptyResponse && !failure"></div>-->
          <div v-for="program in programs"
          :key="program.id"
          class="medium-12 cell mbl">
            <a class="card program-card" v-bind:href="program.link">
              <img v-bind:src="program.image" />
              <div class="content-block">
                <h3>{{program.title}}</h3>
                <p>{{program.short_description}}</p>
              </div>
            </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

const programsEndpoint = '/wp-json/programs/v1/'
const audienceEndpoint = '/wp-json/wp/v2/audience/'
const serviceTypeEndpoint = '/wp-json/wp/v2/service_type/'

function getByServiceType(list, service) {
  if ( !service )
  return list
  //service.forEach(function(e, other){
    console.log(list)
    return list.filter(item => item.service === service)
//  })
}

export default {
  name: 'program-archives',
  components: {
  },
  data: function() {
    return{
      programs: [{ }],

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
      console.log('yeah its happening')
      //TODO use in instead of undefined

      axios.get(programsEndpoint + 'archives', {
        params: {
          'count': 20,
        }
      })
      .then(response => {
        this.programs = response.data
        console.log(response.data)
        this.successfulResponse
      })
      .catch(e => {
        console.log('fail')
        this.failure = true
        this.loading = false
      })
    },
    getAudiences: function () {
      axios.get(audienceEndpoint, {
        params: {
          'count': 30,
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
          'count': 30,
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
        axios.get(programsEndpoint + 'archives', {
          params : {
            's': this.searchedVal,
            'count': 20,
            }
          })
          .then(response => {
            console.log('fired')
            console.log(response.data)
            this.programs = response.data
            this.successfulResponse
          })
          .catch(e => {
            this.failure = true
            this.loading = false
        })
      })
    },
    filterResults: function (event) {
      this.loading = true

      this.$nextTick(function () {
        axios.get(programsEndpoint + 'archives', {
          params : {
            's': this.searchedVal,
            'count': 100,
            'audience' : this.checkedAudiences,
            'service_type': this.checkedServiceType
            }
          })
          .then(response => {
            console.log('fired')
            console.log(response.data)
            this.programs = response.data
            this.successfulResponse
          })
          .catch(e => {
            this.failure = true
            this.loading = false
        })
      })
    },
  },
  computed: {
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
  },
}
</script>

<style>
/* TODO: remove base card styles in standards */
a.card{
  border-bottom: none;
}
</style>
