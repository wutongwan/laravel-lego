const { mix } = require('laravel-mix');

// global config.
mix.setPublicPath('public');
mix.version();

// for CoffeeScript
mix.webpackConfig({
    module: {
        rules: [
            { test: /\.coffee$/, loader: 'coffee-loader' }
        ]
    }
});


mix.js('resources/assets/grid/batch.coffee', 'public/js');
