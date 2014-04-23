module.exports = function (grunt) {

    'use strict';

    grunt.registerTask('default', [
        'build',
        'watch'
    ]);

    grunt.registerTask('build', [
        'inc',
        'js',
        'version'
    ]);

    grunt.registerTask('inc', [
        'clean',
        'copy'
    ]);

    grunt.registerTask('js', [
        'jslint'
    ]);

    grunt.registerTask('version', [
        'replace'
    ]);

};
