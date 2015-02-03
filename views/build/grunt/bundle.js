module.exports = function(grunt) { 

    var requirejs   = grunt.config('requirejs') || {};
    var clean       = grunt.config('clean') || {};
    var copy        = grunt.config('copy') || {};

    var root        = grunt.option('root');
    var libs        = grunt.option('mainlibs');
    var ext         = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);
    var out         = 'output/taoWfTest';

    /**
     * Remove bundled and bundling files
     */
    clean.taowftestbundle = [out];
    
    /**
     * Compile tao files into a bundle 
     */
    requirejs.taowftestbundle = {
        options: {
            baseUrl : '../js',
            dir : out,
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'taoWfTest' : root + '/taoWfTest/views/js' },
            modules : [{
                name: 'taoWfTest/controller/routes',
                include : ext.getExtensionsControllers(['taoWfTest']),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taowftestbundle = {
        files: [
            { src: [out + '/taoWfTest/controller/routes.js'],  dest: root + '/taoWfTest/views/js/controllers.min.js' },
            { src: [out + '/taoWfTest/controller/routes.js.map'],  dest: root + '/taoWfTest/views/js/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taowftestbundle', ['clean:taowftestbundle', 'requirejs:taowftestbundle', 'copy:taowftestbundle']);
};
