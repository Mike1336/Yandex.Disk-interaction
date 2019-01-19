var gulp         = require('gulp');
var sass         = require('gulp-sass');
var minifyCSS    = require('gulp-clean-css');
var rename       = require("gulp-rename");
var autoprefixer = require('gulp-autoprefixer');

gulp.task('run', function(){
       gulp.src('src/styles/*.scss')
      .pipe(sass({outputStyle:'expanded'}).on('error',sass.logError)) //компиляция из scss в css
      .pipe(gulp.dest('src/styles/')) //сохранение скомпилированного файла

      .pipe(minifyCSS()) //минификация css файла
      .pipe(rename({ //переименование минифицированного файла (добавление ".min" в название)
        suffix: ".min",
        extname: ".css"
        }))
      .pipe(gulp.dest('src/styles/')) //сохранение скомпилированного минифицированного файла
      
      .pipe(autoprefixer({ // Добавление префиксов для кроссбраузерности
        browsers: ['last 5 versions'],
        cascade: false
       }))
       
  });