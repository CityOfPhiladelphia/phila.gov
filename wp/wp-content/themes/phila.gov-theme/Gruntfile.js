module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    sass: {
      dist: {
        options: {
          sourceMap: true,
        },
        files: {
          'css/styles.css': 'css/scss/base.scss'
        }
      }
    },
    cssmin: {
      target: {
        files: [{
          expand: true,
          cwd: 'css',
          src: ['*.css', '!*.min.css', '!lt-ie-9.css'],
          dest: 'css',
          ext: '.min.css'
        }]
      }
    },
    uglify: {
      options: {
        sourceMap: true
      },
      js: {
        files: {
          'js/phila-scripts.min.js': [
             'js/dependencies/jquery-deparam.js',
             'js/dependencies/jquery.ba-hashchange.min.js',
             'js/dependencies/jquery.swiftype.search.js',
             'js/dependencies/jquery.swiftype.autocomplete.js',
             'js/dependencies/js.cookie.js',
             'js/dependencies/mustache.min.js',
             'js/dev/scripts.js',
             'js/dev/filter.js',
             'js/dev/search.js',
             'js/dev/skip-link-focus-fix.js'
          ]
        }
      }
    }
  });

  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');

  grunt.registerTask('default', ['sass', 'cssmin', 'uglify']);
};
