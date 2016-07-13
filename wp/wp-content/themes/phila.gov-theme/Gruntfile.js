module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    sass: {
      dist: {
        options: {
          style: 'compressed'
        },
        files: {
          'css/styles.css': 'css/scss/base.scss'
        }
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
             'js/dependencies/mustache.min.js',
             'js/dependencies/jquery.fittext.js',
             'js/dev/scripts.js',
             'js/dev/search.js',
             'js/dev/skip-link-focus-fix.js'

          ]
        }
      }
    }
  });

  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-contrib-uglify');

  grunt.registerTask('default', ['sass', 'uglify']);
};
