var gulp = require('gulp');
var less = require('gulp-less');
var LessCleanCss = require('less-plugin-clean-css');
var LessAutoprefix = require('less-plugin-autoprefix');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var fs = require('fs');
var browserify = require('browserify');
var source = require('vinyl-source-stream');
var tsify = require('tsify');
var buffer = require('vinyl-buffer');
var ts = require('gulp-typescript');
var tscConfig = require('./tsconfig.json');
var merge = require('merge-stream');


gulp.task('fonts', function(){
	return gulp.src([
		'node_modules/bootstrap/fonts/**.*',
		'node_modules/font-awesome/fonts/**.*',
		'src/fonts/**.*'
	])
	.pipe(gulp.dest('dest/fonts/'))
});
gulp.task('images', function(cb){
	return gulp.src([
		'src/images/**/*.png',
		'src/images/**/*.jpg',
		'src/images/**/*.jpeg',
		'src/images/**/*.gif',
		'src/images/**/*.svg',
		'src/images/**/*.ico'
	])
	.pipe(gulp.dest('dest/images/'));
});

gulp.task('js:ts', function(){
	return browserify({
		basedir: __dirname,
		debug: true,
		paths:[
			""
		]
	})
	.require(__dirname + '../../../base/frontend/src/js/Router.ts', {expose:"Router"})
	.require(__dirname + '../../../base/frontend/src/js/Options.ts', {expose:"Options"})
	.require(__dirname + '../../../base/frontend/src/js/Translator.ts', {expose:"Translator"})
	.add(__dirname + '/src/ts/events/main.ts')
	.plugin(tsify)
	.bundle()
	.pipe(source('typescripts.js'))
	.pipe(gulp.dest('dest/js'));
});
gulp.task('js:combine',['js:ts'],function(cb){
	return gulp.src([
		'node_modules/jquery/dist/jquery.min.js',
		'node_modules/bootstrap/dist/js/bootstrap.min.js',
		'node_modules/jquery.growl/javascripts/jquery.growl.js',
		'node_modules/jquery-ui/ui/version.js',
		'node_modules/jquery-ui/ui/effect.js',
		'node_modules/jquery-ui/ui/position.js',
		'node_modules/jquery-ui/ui/keycode.js',
		'node_modules/jquery-ui/ui/unique-id.js',
		'node_modules/jquery-ui/ui/safe-active-element.js',
		'node_modules/jquery-ui/ui/widget.js',
		'node_modules/jquery-ui/ui/widgets/menu.js',
		'node_modules/jquery-ui/ui/widgets/autocomplete.js',
		'node_modules/jquery-ui/ui/effects/*.js',
		'src/plugins/bootstrap-inputmsg/bootstrap-inputmsg.js',
		'dest/js/typescripts.js',
	])
	.pipe(concat('scripts.js'))
	.pipe(uglify())
	.pipe(gulp.dest('dest/js'));
});
gulp.task('js:clean', ['js:combine'], function(){
	fs.unlinkSync('dest/js/typescripts.js', function(error){

	});
})
gulp.task('js', ['js:clean'], function(){
	
})
gulp.task('css',function(){
	var autoprefix = new LessAutoprefix({ browsers: ['last 2 versions'] });
	var cleanCSS = new LessCleanCss({advanced: true});

	return gulp.src([
		'node_modules/bootstrap/less/bootstrap.less',
		'node_modules/bootstrap-rtl/less/bootstrap-rtl.less',
		'node_modules/font-awesome/less/font-awesome.less',
		'node_modules/jquery.growl/stylesheets/jquery.growl.css',
		'node_modules/jquery-ui/themes/base/core.css',
		'node_modules/jquery-ui/themes/base/autocomplete.css',
		'node_modules/bootstrap-select/less/bootstrap-select.less',
		'src/less/main.less'
	])
		.pipe(less({
			plugins: [autoprefix, cleanCSS]
		}))
		.pipe(concat('style.css'))
		.pipe(gulp.dest('dest/css'));
});
gulp.task('watch', function () {
	var LessWatcher = gulp.watch('src/less/*.less', ['css']);
	LessWatcher.on('change', function(event) {
		console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
	});
	var imageWatcher = gulp.watch('src/images/**/*.*', ['images']);
	imageWatcher.on('change', function(event) {
		console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
	});
	var fontsWatcher = gulp.watch('src/fonts/**/*.*', ['fonts']);
	fontsWatcher.on('change', function(event) {
		console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
	});
	var jsWatcher = gulp.watch(['src/js/**/*.js','src/ts/**/*.ts'], ['js']);
	jsWatcher.on('change', function(event) {
		console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
	});
});
gulp.task('default', [ 'css','fonts','images','js' ]);
