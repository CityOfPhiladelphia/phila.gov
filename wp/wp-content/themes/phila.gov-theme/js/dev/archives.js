var Vue = require('vue')
var VueResource = require('vue-resource')
var moment = require('moment')

Vue.use(VueResource)

Vue.filter('formatDate', function(value) {
  if (value) {
    return moment( String(value) ).format('MMM. DD, YYYY');
  };
})

//TODO: create custom endpoint instead of hitting WP API

var config = {
  api: {
    post_data: '/wp-json/wp/v2/posts'
  }
};

var archives = new Vue ({
  el: '.results',
  template:`
    <table>
    <thead>
    <tr><th>Title</th><th>Date</th><th>Department</th></tr>
    </thead>
      <tr v-for="post in posts">
        <td class="title"><a v-bind:href="post.link">
        <span v-if="post.meta.phila_template_select === 'post'">
          <i class="fa fa-pencil"></i>
        </span>
        <span v-else>
          <i class="fa fa-file-text-o"></i>
        </span> {{ post.title.rendered }}</a>
        </td>
        <td class="date">{{ post.date  | formatDate }}</td>
        <td class="categories">{{ post.categories }}</td>
      </tr>
    </table>`,
  data: function() {
    return{
      //validate post.meta.phila_template_select in here
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
         console.log('fail');
      });
    },
  },
})
