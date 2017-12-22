'use strict';

//https://www.npmjs.com/package/gulp-tag-version
var   gulp        = require('gulp'),
      gutil       = require('gulp-util'),
      git         = require('gulp-git'),
      bump        = require('gulp-bump'),
      filter      = require('gulp-filter'),
      tag_version = require('gulp-tag-version');


//https://github.com/gulpjs/gulp/blob/master/docs/recipes/pass-arguments-from-cli.md
var minimist     = require('minimist'),
    options      = minimist(process.argv.slice(2));


/**
 * Bumping version number and tagging the repository with it. - reference http://semver.org/
 *
 * Use --v=<importance> to signify type of verion bump
 * - <patch>   bug fix                # makes v0.1.0 → v0.1.1
 * - <feature> new functionality      # makes v0.1.1 → v0.2.0
 * - <release> pushed to production   # makes v0.2.1 → v1.0.0
 *
 * To bump the version numbers accordingly after you did a patch,
 * introduced a feature or made a backwards-incompatible release.
 */

function inc(importance, cb) {
    // get all the files to bump version in
     gulp.src(['./package.json'])
        // bump the version number in those files
        .pipe(bump({type: importance}))
        // save it back to filesystem
        .pipe(gulp.dest('./'))
        // commit the changed version number
        // .pipe(git.commit('bumps package version'))
        // read only one file to get the version number
        .pipe(filter('package.json'))
        // **tag it in the repository**
        .pipe(tag_version())
        .on('end', function() { cb(); });
}





gulp.task('versionBump', function(done) {

  if(global.isRelease){
    // always bump to major release version
    // if deploying to production environment
    inc('major', function() { done(); });
  }else if(options.v){
    //if NOT a production release
    // use passed in arguement
    inc(options.v, function() { done(); });

  }else{
    // if not version type specified exit
    // keep current version
    var pkg = require('../../package');
    gutil.log('No version change: v'+pkg.version);
    done();
  }

});

