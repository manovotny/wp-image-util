module.exports = (function () {

    'use strict';

    return {
        author: {
            email: 'manovotny@gmail.com',
            name: 'Michael Novotny',
            url: 'http://manovotny.com',
            username: 'manovotny'
        },
        files: {
            browserify: 'bundle'
        },
        paths: {
            curl: 'curl_downloads',
            source: 'src',
            translations: 'lang'
        },
        project: {
            composer: 'manovotny/wp-image-util',
            description: 'An image utility for WordPress.',
            git: 'git://github.com/manovotny/wp-image-util.git',
            name: 'WP Image Util',
            slug: 'wp-image-util',
            type: 'plugin', // Should be `plugin` or `theme`.
            url: 'https://github.com/manovotny/wp-image-util',
            version: '0.5.2'
        }
    };

}());
