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
        filename: '[name]-[contenthash].js',
        chunkFilename: '[name].[chunkhash].js',
        libraryTarget: 'window',
        publicPath: '/packages/wutongwan/lego/'
    },
    optimization: {
        // moduleIds: 'hashed',
        // chunkIds: 'named',
        splitChunks: {
            chunks: "async", // 共有三个值可选：initial(初始模块)、async(按需加载模块)和all(全部模块)
        }
    },
    plugins: [
        new CleanWebpackPlugin(),
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery"
        }),
        new CopyWebpackPlugin({
            patterns: [
                {from: 'node_modules/tinymce/skins', to: 'skins'}, // tinymce skin
                // externals
                {from: 'node_modules/bootstrap/dist/css/bootstrap.min.css', to: 'externals/bootstrap/css/bootstrap.min.css'},
                {from: 'node_modules/bootstrap/dist/js/bootstrap.min.js', to: 'externals/bootstrap/js/bootstrap.min.js'},
                {from: 'node_modules/bootstrap/dist/fonts', to: 'externals/bootstrap/fonts'},
                {from: 'node_modules/jquery/dist/jquery.min.js', to: 'externals/jquery/jquery.min.js'},
            ],
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

                return '{{-- Generated: 此文件基于同目录 `scripts.template.blade.php` 生成得来，请勿手动修改 --}}\n'
                    + fs.readFileSync(templateFilepath).toString()
                        .replace('{{-- webpack scripts --}}', scripts)
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
        // 测试环境生成到 ignore 的目录，避免影响 prod 的版本管理
        config.output.path += '/dev'
        config.output.publicPath += 'dev/'
    }

    if (argv.mode === 'production') {
    }
    return config;
};
