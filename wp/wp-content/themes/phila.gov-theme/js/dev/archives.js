var Vue = require('vue')
var VueResource = require('vue-resource')
var moment = require('moment')

Vue.use(VueResource)

Vue.filter('formatDate', function(value) {
  if (value) {
    return moment( String(value) ).format('MMM. DD, YYYY');
  };
})

var config = {
  api: {
    //uses custom endpoint to get only the data we need
    post_data: '/wp-json/the-latest/v1/archives'
  }
};

var archives = new Vue ({
  el: '.results',
  template:`
    <table>
      <thead>
        <tr><th>Title</th><th>Date</th><th>Department</th></tr>
      </thead>
      <tbody>
        <tr v-for="post in posts"
        :key="post.id">
          <td class="title"><a v-bind:href="post.link">
          <span v-if="post.template.includes('post')">
            <i class="fa fa-pencil"></i>
          </span>
          <span v-else>
            <i class="fa fa-file-text-o"></i>
          </span> {{ post.title }}</a>
          </td>
          <td class="date">{{ post.date  | formatDate }}</td>
          <td class="categories">
            <span v-for="(category, i) in post.categories">
              <span>{{ category.slang_name }}</span><span v-if="i < post.categories.length - 1">,&nbsp;</span>
            </span>
          </td>
        </tr>
      </tbody>
    </table>`,
  data: function() {
    return{
      posts: []
    }
  },
  mounted: function () {
    this.getPosts()
  },
  methods: {
    getPosts: function () {
      this.$http.get(config.api.post_data).then(response => {
      this.posts = response.data
      console.log(response.data)

      }, response => {
         console.log('fail')
      });
    },
  },

})
