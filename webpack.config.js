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
            cacheGroups: { // 缓存组，会继承和覆盖splitChunks的配置
                default: { // 模块缓存规则，设置为false，默认缓存组将禁用
                    minChunks: 2, // 模块被引用>=2次，拆分至vendors公共模块
                    priority: -20, // 优先级
                    reuseExistingChunk: true, // 默认使用已有的模块
                },
                vendors: {
                    test: /[\\/]node_modules[\\/]/, // 表示默认拆分node_modules中的模块
                    priority: -10
                }
            }
        }
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
