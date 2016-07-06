module.exports = function(grunt) {
	grunt.initConfig({
		bower: grunt.file.readJSON('bower.json'),
		clean: {
			dist: ['dist'],
			default: ['default/*.html'],
			topbar: ['topbar/*.html'],
			tmp: ['.tmp'],
			bower: "<%= bower.clean.bower %>"
		},
		copy: {
			html: {
				files: [
					{expand: true, cwd: 'default/', src: '**', dest: 'dist/default/'},
					{expand: true, cwd: 'topbar/', src: '**', dest: 'dist/topbar/'},
					{src: 'index.html', dest: 'dist/index.html'}
				]
			},
			assets: {
				files: [
					{expand: true, cwd: 'assets/', src: ['**', '!**/sass/**'], dest: 'dist/assets/'},
					{expand: true, cwd: 'libs/', src: '**', dest: 'dist/libs/'},
					{expand: true, cwd: 'api/', src: '**', dest: 'dist/api/'},
				]
			},
			libs: {
				files: "<%= bower.copy %>"
			}
		},
		htmlmin: {
			dist: {
				options: { removeComments: true, collapseWhitespace: true },
				files: [
					{ expand: true, cwd: 'dist/default/', src: ['*.html', '**/*.html'], dest: 'dist/default/' },
					{ expand: true, cwd: 'dist/topbar/', src: ['*.html', '**/*.html'], dest: 'dist/topbar/' }
				]
			}
		},
		useminPrepare: {
			html: ['default/*.html', 'topbar/*.html']
		},
		usemin: {
			html: ['dist/default/*.html', 'dist/topbar/*.html']
		},
		assemble: {
			options: {
				layoutdir: 'templates/layouts/',
				data: ['api/json/data.json'],
				flatten: true
			},
			default: {
				options: {
					layout: 'default.html'
				},
				src: [
					'templates/**/*.html',
					'!templates/includes/**',
					'!templates/layouts/**',
					'!templates/misc/**'
				],
				dest: 'default/'
			},
			topbar: {
				options: {
					layout: 'topbar.html'
				},
				src: [
					'templates/**/*.html',
					'!templates/includes/**',
					'!templates/layouts/**',
					'!templates/misc/**'
				],
				dest: 'topbar/'
			},
			misc1: {
				options: {
					layout: 'misc.layout.html'
				},
				src: 'templates/misc/**/*.html',
				dest: 'default/'
			},
			misc2: {
				options: {
					layout: 'misc.layout.html'
				},
				src: 'templates/misc/**/*.html',
				dest: 'topbar/'
			}
		},
		watch: {
			options: {
				livereload: true
			},
			html: {
				files: ['templates/**/*.html'],
				tasks: ['assemble']
			},
			sass: {
				files: ['assets/sass/**/*.scss'],
				tasks: ['sass:app']
			}
		},
		express: {
			all: {
				options: {
					port: 3000,
					bases: '.',
					hostname: 'localhost',
					livereload: true
				}
			}
		},
		sass: {
			options: {
				sourceMap: true
			},
			app: {
				files: [
					{ 'assets/css/app.css': 'assets/sass/app.scss' }
				]
			},
			bootstrap: {
				files: [
					{ 'assets/css/bootstrap.css': 'assets/sass/bootstrap.scss' }
				]
			}
		}
	});

	grunt.loadNpmTasks('grunt-usemin');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-htmlmin');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-assemble');
	grunt.loadNpmTasks('grunt-sass');

	grunt.registerTask('build', [
		'clean:dist',
		'copy:html',
		'useminPrepare',
		'concat',
		'cssmin',
		'uglify',
		'usemin',
		'clean:tmp',
		'htmlmin',
		'copy:assets'
	]);
};