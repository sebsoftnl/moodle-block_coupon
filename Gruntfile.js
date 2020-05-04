module.exports = function(grunt) {

    // Import modules.
    var path = require('path');

    // PHP strings for exec task.
    var moodleroot = path.dirname(path.dirname(__dirname)),
        dirrootopt = grunt.option('dirroot') || process.env.MOODLE_DIR || '';

    // Allow user to explicitly define Moodle root dir.
    if ('' !== dirrootopt) {
        moodleroot = path.resolve(dirrootopt);
    }

    // Production / development.
    var build = grunt.option('build') || 'd'; // Development for 'watch' task.

    if ((build != 'p') && (build != 'd')) {
        build = 'p';
        console.log('-build switch only accepts \'p\' for production or \'d\' for development,');
        console.log('e.g. -build=p or -build=d.  Defaulting to development.');
    }

    var PWD = process.cwd();

    var svgcolour = grunt.option('svgcolour') || '#999999';
    grunt.initConfig({
		// https://github.com/gruntjs/grunt-contrib-watch
        watch: {
			gruntfile: {
				files: ['Gruntfile.js'],
				options: {
					reload: true
				}
			},
			js: {
                files: ['**/amd/src/*.js'],
                tasks: ['amd'],
                options: {
                    spawn: false
                }
            },
            less: {
                files: ['**/less/*.less'],
                tasks: ['less']
            }
        },
        jshint: {
            options: {jshintrc: moodleroot + '/.jshintrc'},
            files: ['**/amd/src/*.js']
        },
        uglify: {
            dynamic_mappings: {
                files: grunt.file.expandMapping(
                    ['**/src/*.js', '!**/node_modules/**'],
                    '',
                    {
                        cwd: PWD,
                        rename: function (destBase, destPath) {
                            destPath = destPath.replace('src', 'build');
                            destPath = destPath.replace('.js', '.min.js');
                            destPath = path.resolve(PWD, destPath);
                            return destPath;
                        }
                    }
                )
            }
        },
        less: {
            // Production config is also available.
            development: {
                options: {
                    // Specifies directories to scan for @import directives when parsing.
                    // Default value is the directory of the source, which is probably what you want.
                    paths: ["less/"],
                    compress: true
                },
                files: [
    {
        expand: true,
        cwd: "less/",
        src: "*.less",
        dest: "style/",
        ext: ".css"
    }
]
            },
        }
    });

// On watch events configure jshint:all to only run on changed file
grunt.event.on('watch', function(action, filepath, target) {
	if (target === 'js') {
		grunt.config('jshint.files', [filepath]);
		grunt.config('uglify.dynamic_mappings.files.src', filepath);
	}
console.log(target + ':' + action + ' on ' + filepath);
console.log(arguments);
console.log(grunt.config('jshint.files'));
console.log(grunt.config('uglify.dynamic_mappings.files'));
});

    // Load core tasks.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks("grunt-contrib-less");
    grunt.loadNpmTasks("grunt-contrib-watch");
//    grunt.loadNpmTasks("grunt-contrib-clean");
    grunt.registerTask("amd", ["jshint", "uglify"]);
    grunt.registerTask("default", ["amd"]);
};

