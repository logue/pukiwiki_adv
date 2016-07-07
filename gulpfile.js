'use strict';

// 使用するプラグイン
var
	babelify = require("babelify"),
	bower = require('gulp-bower'),
	browserify = require("browserify"),
	compass = require('gulp-compass'),
	composer = require('gulp-composer'),
	concat = require('gulp-concat'),
	del = require('del'),
	gulp = require('gulp'),
	gulpFilter = require('gulp-filter'),
	install = require("gulp-install"),
	modernizr = require('gulp-modernizr'),
	notify = require("gulp-notify"),
	plumber = require("gulp-plumber"),
	rename = require('gulp-rename'),
	sass = require('gulp-ruby-sass'),
	source = require("vinyl-source-stream"),
	uglify = require("gulp-uglify")
;
// 初期設定
var config = {
	jsSrcPath: './webroot/sources/js/',
	jsPath: './webroot/assets/js/',
	scssPath: './webroot/sources/scss/',
	cssPath: './webroot/assets/css/',
	fontsPath: './webroot/assets/fonts/',
	bowerDir: './webroot/sources/bower_components/'
}

gulp.task('bower', function() {
	return bower()
		.pipe(gulp.dest(config.bowerDir))
});

gulp.task('clean', function (cb) {
	return del('./webroot/assets');
});

gulp.task('setup', ['clean'], function () {
	return gulp.src(['./bower.json'])
		.pipe(install())
	;
});

// JavaScriptを圧縮
gulp.task('js.uglify', function() { 
	return gulp.src(config.jsSrcPath + 'app.js')
		.pipe(plumber())
		.pipe(uglify({
			preserveComments: 'some',
			preserveComments: 'license'	// ライセンスを残す
		}))
		.pipe(rename('app.min.js'))
		.pipe(gulp.dest(config.jsPath))
	;
});

// Modernizrの自動処理（出来上がったソースからModernizr使用箇所を自動的に抜き出して専用のModernizrを出力する）
gulp.task('js.modernizr', function() {
	gulp.src(config.jsSrcPath + '*.js')
		.pipe(modernizr())
//		.pipe(uglify())
		.pipe(gulp.dest(config.jsPath))
	;
})

// ES6で書かれたjsを通常のjsに変換
gulp.task('js.babelify', function () {
	browserify({
		entries: "./webroot/sources/js/app.js",
		extensions: [".js"]
	})
	.plugin('licensify')
	.transform(babelify, {presets: ['es2015']})
	.bundle()
	.on("error", function (err) {
		console.log("Error : " + err.message);
		this.emit("end");
	})
	.pipe(source("app.js"))
	.pipe(gulp.dest("./webroot/assets/js"));
});

// JavaScript関係の処理
gulp.task('js', ['js.babelify', 'js.uglify', 'js.modernizr']);

// アイコンフォント
gulp.task('icons', function() {
	return gulp.src(config.bowerDir + 'fontawesome/fonts/**.*')
		.pipe(gulp.dest(config.fontsPath))
		.on('error', notify.onError(function (error) {
			return 'Error: ' + error.message;
		}))
	;
});

// スタイルシートの更新
gulp.task('css', function(){
	gulp.src(config.scssPath + '**/*.scss')
		.pipe(compass({
			config_file: 'config.rb',
			comments: false,
			css: config.cssPath,
			sass: config.scssPath
		}))
		.on('error', function(err) {
			console.log(err.message);
		})
	;
});

// 変更点があった場合の監視
gulp.task('watch', function() {
	gulp.watch(config.sassPath + '*.scss', ['css']);
	gulp.watch(config.jsPath + '*.js', ['js']);
});

// Composerを実行
gulp.task("composer", function () {
	composer({ "self-install": false });
})

gulp.task('default', ['icons', 'css', 'js']);