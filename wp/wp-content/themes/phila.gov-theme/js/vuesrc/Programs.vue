<template>
  <div id="programs-initiatives-landing">
    <div class="grid-x grid-margin-x">
      <div class="small-24 medium-8 cell">
        <section>
          <div class="panel phm">
            <h3>Search within Programs</h3>
            <form v-on:submit.prevent="onSubmit">
              <div class="search">
                <input id="post-search" type="text" name="search" placeholder="Search by title or keyword" class="search-field" ref="search-field"
                v-model="searchedVal">
                <input type="submit" value="submit" class="search-submit">
              </div>
            </form>
          </div>
          <div class="accordion" data-accordion data-allow-all-closed="true"  data-multi-expand="true">
            <div class="accordion-item is-active mtl" data-accordion-item>
              <a href="#" class="h4 accordion-title mbn">Filter by audience</a>
              <div class="accordion-content" data-tab-content>
                <fieldset>
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
              </div>
            </div>
            <div class="accordion-item is-active" data-accordion-item>
              <a href="#" class="h4 accordion-title mbn">Filter by category</a>
                <div class="accordion-content" data-tab-content>
                  <fieldset>
                    <div v-for="(value, key) in service_type">
                      <input type="checkbox"
                      v-model="checkedServiceType"
                      v-bind:value="value.slug"
                      v-bind:name="value.slug"
                      v-bind:id="value.slug"
                      @click="filterResults"/>
                      <label v-bind:for="value.slug"><span v-html="value.name"></span></label>
                    </div>
                </fieldset>
              </div>
            </div>
          </div>
        </section>
      </div>
      <div class="cell medium-16">
        <div>
          <div v-show="loading" class="mtm center">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
          </div>
          <div v-show="emptyResponse" class="h3 mtm center">Sorry, there are no program results.</div>
          <div v-show="failure" class="h3 mtm center">Sorry, there was a problem. Please try again.</div>
          <div class="grid-x grid-margin-x grid-padding-x program-archive-results" v-show="!loading && !emptyResponse && !failure"></div>
        </div>
        <div id="program-results" class="grid-x grid-margin-x">
          <div v-for="program in programs"
          :key="program.id"
          class="medium-12 cell mbl">
            <a class="card program-card" v-bind:href="program.link">
              <img v-bind:src="program.image" alt=""/>
              <div class="content-block">
                <h3>{{program.title}}</h3>
                <p>{{program.short_description}}</p>
              </div>
            </a>
            </div>
          </div>
            <div id="related-services" class="grid-x grid-margin-x grid-padding-x" v-if="relatedServices.length !== 0">
              <div class="medium-24 cell">
                <h3 class="black bg-ghost-gray phm-mu mtl mbm">Related services</h3>
                <ul class="phm-mu">
                  <li v-for="relatedService in relatedServices"
                  :key="relatedService.id">
                  <a v-bind:href="relatedService.link">{{relatedService.name}}</a>
                </li>
                </ul>
              </div>
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

export default {
  name: 'program-archives',
  components: {
  },
  data: function() {
    return{
      programs: [{ }],

      audience: [{ }],
      service_type: [{ }],

      checkedAudiences: [],
      checkedServiceType: [],

      relatedServices: [],

      searchedVal: '',

      loading: false,
      emptyResponse: false,
      failure: false,

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
      //TODO use in instead of undefined

      axios.get(programsEndpoint + 'archives', {
        params: {
          'count': 50,
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
      })
      .catch(e => {
        this.service_type = 'Sorry, there was a problem.'
      })
    },
    getRelatedServices: function(params){

      if ( Object.keys(this.checkedAudiences).length === 0 &&
        Object.keys(this.checkedServiceType).length == '' &&
        this.searchedVal === ''
      ){
        this.relatedServices = ''
      }else{
        this.$nextTick(function () {
          axios.get(programsEndpoint + 'related_service', { params
            })
            .then(response => {
              this.relatedServices = response.data
              this.successfulResponse
            })
            .catch(e => {
              this.failure = true
              this.loading = false
          })
        })
      }
    },
    onSubmit: function (event) {
      this.loading = true

      var params = {
        'count': 50,
        'audience': this.checkedAudiences,
        'service_type': this.checkedServiceType
      }

      if (this.searchedVal != '') {
        params.s = this.searchedVal
        this.getRelatedServices(params)
      }
        axios.get(programsEndpoint + 'archives', { params
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
    filterResults: function (event) {
      this.loading = true

      jQuery('html,body').animate({scrollTop:0},700);

      this.$nextTick(function () {
        var params = {
          'count': 50,
          'audience': this.checkedAudiences,
          'service_type': this.checkedServiceType
        }
        if (this.searchedVal != '')
            params.s = this.searchedVal

          this.getRelatedServices(params)

        axios.get(programsEndpoint + 'archives', {
          params
          })
          .then(response => {
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

<style></style>
