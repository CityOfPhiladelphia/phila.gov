const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

module.exports = env => {
  return {
    entry: './js/vuesrc/main.js',
    output: {
      filename: 'app.js',
      path: path.resolve(__dirname, './js/'),
    },
    module: {
      rules: [
        {
          test: /\.css$/,
          use: ['style-loader', 'css-loader'],
        },      
        {
          test: /\.vue$/,
          loader: 'vue-loader'
        },
      ],
    },
    plugins: [
      new VueLoaderPlugin()
    ]
  }
}
