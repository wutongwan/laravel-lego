const path = require('path');
const webpack = require('webpack');
const {CleanWebpackPlugin} = require('clean-webpack-plugin')
const ManifestPlugin = require('webpack-manifest-plugin');

module.exports = {
    mode: 'development',
    entry: {
        index: './resources/assets/index.js',
    },
    output: {
        path: path.resolve(__dirname, 'public/build'),
        filename: 'lego-[hash].js',
        chunkFilename: '[name].bundle.[hash].js',
        libraryTarget: 'window'
    },
    optimization: {
        splitChunks: {
            chunks: 'all',
        },
    },
    plugins: [
        new CleanWebpackPlugin(),
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery"
        }),
        new ManifestPlugin(),
    ],
    module: {
        rules: [
            {
                test: /\.coffee$/,
                loader: 'coffee-loader'
            },
            {
                test: /\.css$/i,
                use: ['style-loader', 'css-loader'],
            },
            {
                test: /\.(ttf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
                loader: 'file-loader',
            }
        ]
    },
    externals: {
        jquery: 'jQuery',
        bootstrap: true,
        vue: 'Vue',
    }
    //     'jquery',
    //     'bootstrap',
    //     'vue',
    // ]
};
