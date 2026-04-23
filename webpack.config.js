const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = (env, argv) => {
    const isDev = argv.mode === 'development';

    return {
        entry: {
            'jux-builder': './assets/src/index.ts',
            'jux-preview': './assets/src/preview.ts',
        },

        output: {
            path: path.resolve(__dirname, 'assets/js'),
            filename: '[name].js',
            clean: false,
        },

        // jQuery, Backbone, Underscore are already provided by WordPress
        // They are enqueued as WP script dependencies, so we mark them external
        externals: {
            jquery: 'jQuery',
            backbone: 'Backbone',
            underscore: '_',
        },

        resolve: {
            extensions: ['.ts', '.js'],
            alias: {
                '@': path.resolve(__dirname, 'assets/src'),
            },
        },

        module: {
            rules: [
                {
                    test: /\.ts$/,
                    use: 'ts-loader',
                    exclude: /node_modules/,
                },
                {
                    test: /\.css$/,
                    use: [MiniCssExtractPlugin.loader, 'css-loader'],
                },
            ],
        },

        plugins: [
            new MiniCssExtractPlugin({
                filename: '../css/[name].css',
            }),
        ],

        devtool: isDev ? 'source-map' : false,

        optimization: {
            // No code splitting — WP requires single JS output per handle
            splitChunks: false,
        },
    };
};
