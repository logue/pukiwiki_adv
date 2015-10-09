'use strict';
//var plumber = require("gulp-plumber");
var bower = require('gulp-bower');
var browserify = require ('browserify');
var compass = require('gulp-compass');
var composer = require('gulp-composer');
var concat = require('gulp-concat')
var del = require('del');
var gulp = require('gulp');
var gulpFilter = require('gulp-filter');
var install = require("gulp-install");
//var mainBowerFiles = require('main-bower-files');
var notify = require("gulp-notify");
//var sass = require('gulp-sass');
var sass = require('gulp-ruby-sass');
//var source = require('vinyl-source-stream');
//var sourcemaps = require('gulp-sourcemaps');
var uglify = require("gulp-uglify");
//var urlAdjuster = require('gulp-css-url-adjuster');

var config = {
	jsPath: './webroot/assets/js',
	scssPath: './webroot/assets/scss',
	bowerDir: './vendor/bower_components'
}

gulp.task('bower', function() {
	return bower()
		.pipe(gulp.dest(config.bowerDir))
});

gulp.task('clean', function (cb) {
	//return del('./webroot/assets/css');
});

gulp.task('setup', ['clean'], function () {
	return gulp.src(['./bower.json'])
		.pipe(install())
	;
});

gulp.task('js', function() {
	var jsFilter = gulpFilter('**/*.js');
	return gulp.src([
		config.bowerDir + '/jquery/dist',
	])
		.pipe(jsFilter)
		.pipe(uglify())
		.pipe(concat(config.jsPath + '/lib.js'))
		.pipe(gulp.dest('./webroot/assets/js'))
	;
});

gulp.task('icons', function() {
	return gulp.src(config.bowerDir + '/fontawesome/fonts/**.*')
		.pipe(gulp.dest('./webroot/assets/fonts'))
		.on('error', notify.onError(function (error) {
			return 'Error: ' + error.message;
		}))
	;
});

gulp.task('css', function(){
	gulp.src(config.scssPath + '/**/*.scss')
		.pipe(compass({
			config_file: 'config.rb',
			comments: false,
			css: 'webroot/assets/css',
			sass: 'webroot/assets/scss'
		}))
	;
});

gulp.task('watch', function() {
	gulp.watch(config.sassPath + '*.scss', ['css']);
	gulp.watch(config.jsPath + '*.js', ['compress']);
});

gulp.task('composer', function () {
	composer();
});

gulp.task('default', ['composer','icons', 'css']);