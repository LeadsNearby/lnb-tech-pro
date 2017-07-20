var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var autoprefixerOptions = {
  browsers: ['last 2 versions', '> 2%', 'Firefox ESR']
};

var postcss = require('gulp-postcss');

// var inputfile = 'sass/style.scss';
var input = 'sass/**/*.scss';
var output = './';

var sassOptions = {
    errLogToConsole: true,
    outputStyle: 'nested',
};

gulp.task('default', ['sass', 'watch']);

gulp.task('sass', function () {
  return gulp
    .src(input)
    .pipe(sass(sassOptions))
    .pipe(postcss([ require('autoprefixer') ]))
    .pipe(gulp.dest(output));
});

gulp.task('watch', function() {
  return gulp
    // Watch the input folder for change,
    // and run `sass` task when something happens
    .watch(input, ['sass'])
    // When there is a change,
    // log a message in the console
    .on('change', function(event) {
      console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
    });
});