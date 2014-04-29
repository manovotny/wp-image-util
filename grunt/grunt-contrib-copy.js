module.exports = function (grunt) {

    'use strict';

    grunt.config('copy', {
        composer: {
            files: [
                {
                    expand: true,
                    cwd: 'vendor/querypath/querypath/src',
                    src: [
                        '**/*'
                    ],
                    dest: 'lib/querypath'
                }
            ]
        }
    });

};