const { mix } = require('laravel-mix');

// global config.
mix.setPublicPath('public');

// for CoffeeScript
mix.webpackConfig({
    module: {
        rules: [
            { test: /\.coffee$/, loader: 'coffee-loader' }
        ]
    }
});

mix.js('resources/assets/grid/batch.coffee', 'public/js');

mix.version();
