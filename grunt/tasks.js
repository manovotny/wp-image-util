module.exports = function (grunt) {

    'use strict';

    grunt.registerTask('default', [
        'build',
        'watch'
    ]);

    grunt.registerTask('build', [
        'inc',
        'js',
        'bump'
    ]);

    grunt.registerTask('bump', [
        'replace'
    ]);

    grunt.registerTask('inc', [
        'clean',
        'copy'
    ]);

    grunt.registerTask('js', [
        'jslint'
    ]);

};
