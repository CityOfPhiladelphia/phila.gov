<template>
  <div class="row">
    <div class="medium-7 columns show-for-medium filter" data-desktop-filter-wrapper="">
      <h2 class="h4 mtn">{{ options.labels.filterByText }}</h2> 
      <form>
        <ul class="no-bullet pan">
          <li>
            <input id="all" type="checkbox" @change="uncheckAllCheckboxes();updateResultsList()" :checked="defaultCheckboxChecked">
            <label for="all">{{ options.labels.defaultCheckboxLabel }}</label>
          </li>
          <li v-for="listItem in categories" :key="listItem.slug" >
            <input type="checkbox" :value="listItem.slug" :id="listItem.slug" v-model="checkedItems" @change="updateResultsList()">
            <label :for="listItem.slug">{{ listItem.name }}</label>
          </li>
        </ul>
      </form>
    </div>
    <div id="a-z-filter-list" class="medium-16 columns results a-z-list">
      <div class="search" v-if="options.searchBox">
        <input class="search-field" type="text" v-model="options.searchValue" :placeholder="options.labels.searchPlaceholder" @keyup="updateResultsList()" v-on:keydown.enter.prevent="">
      </div>
      <nav class="show-for-medium" v-if="options.azAnchors && options.azGroup">
        <ul class="inline-list mbm pan mln h4">
          <li v-for="letter in alphabetLetters" :key="letter">
            <a :href="'#l-' + letter" 
            v-scroll-to="getScrollToSettings(letter)" :disabled="isLetterInResults(letter)" :aria-disabled="isLetterInResults(letter)">{{ letter }}</a>
          </li>
        </ul>
      </nav>
      <div class="list">
        <template v-if="hasResults()">
          <template v-if="options.azGroup">
            <div v-for="(list, letter) in resultsList" :key="letter" >
              <div :id="'l-' + letter" class="row collapse a-z-group">
                <hr class="letter separator" :data-alphabet="numericLetterFilter(letter)">
                <div class="small-20 medium-24 columns">
                  <div class="small-21 columns result mvm">
                    <div v-for="(listItem, index) in list" :key="'g-' + letter + index">
                      <a :href="listItem.link">{{ listItem.title }}</a>
                      <p class="hide-for-small-only mbl">{{ listItem.desc }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>
          <template v-else>
            <div class="small-20 medium-24 columns">
              <div v-for="(listItem, lIndex) in resultsList" :key="'l-' + lIndex">
                <a :href="listItem.link">{{ listItem.title }}</a>
                <p class="hide-for-small-only mbl">{{ listItem.desc }}</p>
              </div>
            </div>
          </template>
        </template>
        <template v-else>
          <div class="nothing-found h3">{{ options.labels.noResultsMsg }}</div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>

import Vue from 'vue'
import VueScrollTo from 'vue-scrollto'
import deepMerge from 'lodash/merge'
import Fuse from 'fuse.js'

Vue.prototype.$search = function (term, list, options) {
  return new Promise(function (resolve, reject) {
    var run = new Fuse(list, options)
    var results = run.search(term)
    resolve(results)
  })
}

Vue.use(VueScrollTo)

export default {
  name: 'azlist',
  props: {
    categories: {
      type: [Array],
      default: () => {
        return [{
          name: 'Sample Category 1',
          slug: 'cat-1',
        }]
      },
      validator: (value) => {

        let sample = value[0]

        if (
          sample.hasOwnProperty('name') &&
          sample.hasOwnProperty('slug')
        ) {
          return true
        }
        
        console.log(`The data must be an array of objects with the following keys: name, slug`)

        return false

      },
    },
    list: {
      type: [Array],
      default: () => {
        return [{
          title: 'Sample Entry Label',
          desc: 'Sample Entry Desc... you have no data',
          link: 'http://www.google.com',
          categories: ['cat-1'],
        }]
      },
      validator: (value) => {

        let sample = value[0]

        if (
          sample.hasOwnProperty('title') &&
          sample.hasOwnProperty('desc') &&
          sample.hasOwnProperty('link') &&
          sample.hasOwnProperty('categories')
        ) {
          return true
        }
        
        console.log(`The data must be an array of objects with the following keys: title, desc, link, categories`)

        return false

      },
    },
    propOptions: {
      type: [Object],
    }
  },
  data() {
    return {
      options: {
        azAnchors: true, //display a-z anchors, azGroup must also be true
        azGroup: true, //group results by a-z
        fuseSearchOptions: {
          defaultAll: false,
          keys: [
            'title',
            'desc',
          ],
          matchAllTokens: true,
          threshold: 0.2,
          tokenize: true, 
        },
        labels: {
          noResultsMsg: `Sorry, we couldn't find anything for that search. Please try different terms.`,
          searchPlaceholder: 'Begin typing to filter results by title or description',
          defaultCheckboxLabel: 'All Services',
          filterByText: 'Filter by service category',
        },
        searchBox: true, //display search box
        searchValue: '',
        scrollToSettings: {
          container: "body",
          duration: 1000,
          easing: "ease",
          offset: -70,
          x: false,
          y: true
        }
      },
      alphabet: 'abcdefghijklmnopqrstuvwxyz',
      checkedItems: [],
      defaultCheckboxChecked: true,
      resultsList: [],
    }
  },
  mounted() {
    deepMerge(this.options, this.propOptions)
    this.init()
  },
  computed: {
    alphabetLetters() {
      this.alphabet = this.alphabet.toUpperCase()
      return this.alphabet.split('')
    }
  },
  methods: {
    init() {
      this.resultsList = this.list
      this.updateResultsList()
    },
    hasResults() {
      
      //test if results are not grouped
      if (Array.isArray(this.resultsList) && this.resultsList.length > 0) {
        return true
      }

      //test if results are grouped
      if (this.resultsList === Object(this.resultsList) && Object.keys(this.resultsList).length > 0) {
        return true
      }

      return false
    },
    async updateResultsList() {

      let filteredList = this.list

      //checkboxes filter
      filteredList = await this.filterCheckbox(filteredList)

      //search filter
      if (this.options.searchBox) {
        filteredList = await this.filterSearch(filteredList)
      }
      
      if (this.options.azGroup) {
        filteredList = await this.groupAzList(filteredList)
      } else {
        this.sortResultsListByLabel(filteredList)
      }

      this.resultsList = filteredList

    },
    sortResultsListByLabel(list) {
      return list.sort((a, b) => {
        if (a.title < b.title) {
            return -1
          }
        if (a.title > b.title) {
          return 1
        }
      })
    },
    groupAzList(list) {

      let combinedList = {}
      let orderedCombinedList = {}
      let alpha = {}
      let numeric = {}
      
      list.forEach((item, index) => {
        let letter = item.title.charAt(0)
        
        if (!combinedList.hasOwnProperty(letter)) {
          combinedList[letter] = []
          combinedList[letter].push(item)
        } else {
          combinedList[letter].push(item)
        }
        
        combinedList[letter] = this.sortResultsListByLabel(combinedList[letter])

      }, this)
      
      //sorts letters

      Object.keys(combinedList).sort().forEach(function(key) {
        if (isNaN(key)) {
          alpha[key] = combinedList[key];  
        } else {
          numeric['N-' + key] = combinedList[key];  
        }
      });

      deepMerge(alpha, numeric);

      return alpha

    },
    filterCheckbox(list) {
      if (this.checkedItems.length > 0) {
        this.uncheckDefaultCheckbox()
        dataLayer.push({'serviceCategory': this.checkedItems})
        return list.filter((listItem) => {
          return listItem.categories.some((tag) => {
            return this.checkedItems.includes(tag)
          }, this)
        })

      } else {
        this.uncheckAllCheckboxes()
        return list
      }

    },
    filterSearch(list) {
      if (this.options.searchValue != '') {
        return this.$search(this.options.searchValue, list, this.options.fuseSearchOptions).then(results => {
          return results
        })
      } else {
        return list
      }
    },
    uncheckAllCheckboxes() {
      this.defaultCheckboxChecked = true;
      this.checkedItems = []
    },
    uncheckDefaultCheckbox() {
      this.defaultCheckboxChecked = false;
    },
    isLetterInResults(letter) {
      if (this.options.azAnchors && this.options.azGroup) {
        return !this.resultsList.hasOwnProperty(letter)
      }
    },
    numericLetterFilter(letter) {
      if (typeof letter == 'string') {
        return letter.replace('N-','')
      }
      return letter
    },
    getScrollToSettings(letter) {
      return deepMerge({el: `#l-${letter}`}, this.options.scrollToSettings) 
    }
  }
}
</script>

<style lang="scss"></style>