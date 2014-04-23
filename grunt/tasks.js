module.exports = function (grunt) {

    'use strict';

    grunt.registerTask('default', [
        'build',
        'watch'
    ]);

    grunt.registerTask('build', [
        'clean',
        'copy',
        'js',
        'version'
    ]);

    grunt.registerTask('js', [
        'jslint'
    ]);

    grunt.registerTask('version', [
        'replace'
    ]);

};
