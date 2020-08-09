const path = require('path');
const webpack = require('webpack');
const {CleanWebpackPlugin} = require('clean-webpack-plugin')
const ManifestPlugin = require('webpack-manifest-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');

let config = {
    entry: {
        index: './resources/assets/index.js',
    },
    output: {
        path: path.resolve(__dirname, 'public'),
        filename: 'lego-[hash].js',
        chunkFilename: '[name].[chunkhash].js',
        libraryTarget: 'window',
        publicPath: '/packages/wutongwan/lego/'
    },
    optimization: {
        moduleIds: 'hashed',
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
        new CopyWebpackPlugin({
            patterns: [{
                from: 'node_modules/tinymce/skins',
                to: 'skins',
            }],
        }),
        new ManifestPlugin({
            filter: (fd) => fd.isInitial,
        }),
        new ManifestPlugin({
            fileName: path.resolve(__dirname, 'resources/views/scripts.blade.php'),
            filter: (fd) => fd.isInitial,
            serialize: function (manifest) {
                const fs = require('fs')
                const templateFilepath = path.resolve(__dirname, 'resources/views/scripts.template.blade.php')
                const scripts = Object.values(manifest).map(p => `<script src="${p}"></script>`).join('\n')
                return fs.readFileSync(templateFilepath).toString()
                    .replace(
                        '{{-- webpack scripts --}}',
                        '{{-- Generated: 此文件基于同目录 `scripts.template.blade.php` 生成得来，请勿手动修改 --}}\n'
                        + scripts
                    )
            }
        }),
    ],
    module: {
        rules: [
            {
                test: /\.m?js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            },
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
};


module.exports = (env, argv) => {
    if (argv.mode === 'development') {
        config.devtool = 'inline-source-map';
    }
    if (argv.mode === 'production') {
    }
    return config;
};
