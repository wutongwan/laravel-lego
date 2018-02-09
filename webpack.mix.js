const {mix} = require('laravel-mix');
const fs = require('file-system');
const del = require('del');
const _ = require('lodash');
const jsonfile = require('jsonfile');

const mixManifest = 'public/mix-manifest.json';

// global config.
mix.setPublicPath('public');


// for CoffeeScript
mix.webpackConfig({
    module: {
        rules: [
            {test: /\.coffee$/, loader: 'coffee-loader'}
        ]
    }
});

mix.js('resources/assets/grid/batch.coffee', 'public/js');

mix.version()
    .then(function () {
        jsonfile.readFile(mixManifest, function (err, obj) {
            const newJson = {};
            _.forIn(obj, function (value, key) {
                const newFilename = value.replace(/([^\.]+)\.([^\?]+)\?id=(.+)$/g, '$1.$3.$2');
                const oldAsGlob = value.replace(/([^\.]+)\.([^\?]+)\?id=(.+)$/g, '$1.*.$2');
                // delete old versioned file
                del.sync(['public' + oldAsGlob]);
                // copy as new versioned
                fs.copyFile('public' + key, 'public' + newFilename, function (err) {
                    if (err) console.error(err);
                });
                newJson[key] = newFilename;
            });
            jsonfile.writeFile(mixManifest, newJson, {spaces: 2}, function (err) {
                if (err) console.error(err);
            });
        });
    });
