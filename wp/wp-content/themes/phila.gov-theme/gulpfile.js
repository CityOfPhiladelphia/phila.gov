var gulp = require('gulp');
var sass = require('gulp-sass');
var browserSync = require('browser-sync');

gulp.task('sass', function () {
    gulp.src('css/scss/base.scss')
        .pipe(sass({includePaths: ['scss','node_modules/font-awesome/scss','node_modules/phila-standards/src/vendor/foundation-sites/scss','node_modules/phila-standards/src/sass']}))
        .pipe(gulp.dest('css'));
});

gulp.task('browser-sync', function() {
    browserSync.init(["css/*.css", "js/*.js"], {
        server: {
            baseDir: "./"
        }
    });
});

gulp.task('default', ['sass', 'browser-sync'], function () {
    gulp.watch("css/scss/*.scss", ['sass']);
});
