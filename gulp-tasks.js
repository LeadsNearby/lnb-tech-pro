import gulp from 'gulp';
import cleancss from 'gulp-clean-css';
import postcss from 'gulp-postcss';
import rename from 'gulp-rename';
import sass from 'gulp-sass';
import sourcemaps from 'gulp-sourcemaps';
import bulkSass from 'gulp-sass-bulk-import';
import autoprefixer from 'gulp-autoprefixer';
import errorHandler from 'gulp-error-handle';
import babel from 'gulp-babel';

const jsMinify = require('gulp-minify');

const autoprefixerOptions = {
  grid: true,
};

const sassOptions = {
  errLogToConsole: true,
  outputStyle: 'nested',
};

const inputdir = './src/scss/*.scss';
const input = './src/scss/*.scss';
const output = './dist/css';

const log = (error) => console.log(error.message);

const compileMyCss = () => {
  return (
    gulp
      .src(input)
      // .pipe( setTimeout( () => { return }, 500 ) )
      .pipe(errorHandler(log))
      .pipe(sourcemaps.init())
      .pipe(bulkSass())
      .pipe(sass(sassOptions))
      .pipe(postcss([require('autoprefixer')]))
      .pipe(autoprefixer(autoprefixerOptions))
      .pipe(
        cleancss({
          format: {
            breaks: {
              afterAtRule: true,
              afterBlockBegins: true,
              afterBlockEnds: true,
              afterComment: true,
              afterProperty: true,
              afterRuleBegins: true,
              afterRuleEnds: true,
              beforeBlockEnds: true,
              betweenSelectors: true,
            },
            indentBy: 2,
            indentWith: 'space',
            spaces: {
              aroundSelectorRelation: true,
              beforeBlockBegins: true,
              beforeValue: false,
            },
            wrapAt: false,
          },
          level: 2,
        })
      )
      .pipe(sourcemaps.write('./'))
      .pipe(gulp.dest(output))
  );
};

const minifyMyCss = () => {
  return gulp
    .src(input)
    .pipe(errorHandler(log))
    .pipe(sourcemaps.init())
    .pipe(bulkSass())
    .pipe(sass(sassOptions))
    .pipe(postcss([require('autoprefixer')]))
    .pipe(autoprefixer(autoprefixerOptions))
    .pipe(
      cleancss({
        level: {
          1: {
            all: true,
            specialComments: 0,
          },
        },
      })
    )
    .pipe(rename({ suffix: '.min' }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('./dist/css'));
};

const watchCss = () => {
  const watcher = gulp.watch(
    [input, inputdir],
    gulp.series(compileMyCss, minifyMyCss)
  );
  watcher.on('change', function (file) {
    console.log('File ' + file + ' was changed, running tasks...');
  });
};

const transpileMinifyMyJs = () => {
  return gulp
    .src('src/js/*.js')
    .pipe(sourcemaps.init())
    .pipe(babel())
    .pipe(
      jsMinify({
        noSource: true,
      })
    )
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('dist/js'));
};

const watchJs = () => {
  const watcher = gulp.watch('src/js/*.js', gulp.series(transpileMinifyMyJs));
  watcher.on('change', function (file) {
    console.log('File ' + file + ' was changed, running tasks...');
  });
};

const buildCss = gulp.series(compileMyCss, minifyMyCss);
const buildWatch = gulp.parallel(watchCss, watchJs);

export { buildCss, buildWatch, transpileMinifyMyJs, watchJs };
export default buildWatch;
