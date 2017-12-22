'use strict';


var banner = {
    "name":         "phila.gov-theme",
    "author":       "City of Philadelphia ",
    "authorURI":    "https=//phila.gov",
    "description":  "",
    "origin":       ""
};



var developemnt_theme = './wp/wp-content/themes/'+banner.name,
    production_theme =  './wp/wp-content/themes/'+banner.name;

module.exports = {
  'clientName': banner.name,
  'dev_theme':  developemnt_theme,
  'prod_theme': production_theme,
  'mu_plugins': './wp/wp-content/mu-plugins',

  'banner':banner,

  'proxy':      'https://ec2-52-90-224-227.compute-1.amazonaws.com/', //EC2 dev machine
  'browserSupport': ["last 5 versions","ie 9-10", "Firefox ESR"], //auto-prefixr settings

  'styles': {
    'src' : developemnt_theme+'/css/scss/*.scss',
    'dest': developemnt_theme+'/css/',
    'outFile':'styles.css',
    'prodSourcemap': false,
    'sassIncludePaths': ['node_modules/font-awesome/scss',
                        'node_modules/phila-standards/src/vendor/foundation-sites/scss',
                        'node_modules/phila-standards/src/sass'
                        ]
  },

  // './assets/js/modules/**/*.js', './assets/js/Main.js'
  'scripts': {
    'src' : developemnt_theme+'/assets/js/modules/**/*.js',
    'dest': developemnt_theme+'/assets/js/',
  },

  'images': {
    'src' : developemnt_theme+'/assets/imgs/**/*',
    'dest': production_theme+'/assets/imgs/',
  },

  'fonts': {
    'src' : developemnt_theme+'/assets/css/fonts/**/*',
    'dest': production_theme+'/assets/css/fonts/',
  },


  'browserify': {
    'entries'   : [developemnt_theme+'/assets/js/Main.js'],
    'bundleName': banner.name+'.min.js',
    'prodSourcemap' : false
  },

  env:{
    dev:{
      username:   'ubuntu',
      hostname:   '',
      themes:     '',
      wp_content: '',
    },
    staging:{
      username:   'ubuntu',
      hostname:   '',
      themes:     '',
      wp_content: '',
    },
    production:{
      username:   'ubuntu',
      hostname:   '',
      themes:     '',
      wp_content: '',
    }
  },

  slack:{
    team:    '',
    // user: '',
    url:     '',
      channel: ''
  }
};
