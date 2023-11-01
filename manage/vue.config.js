const { defineConfig } = require('@vue/cli-service');
const webpack = require('webpack');
module.exports = defineConfig({
  transpileDependencies: true,
  lintOnSave: false,
  productionSourceMap: false
})
plugins: [new webpack.ProvidePlugin({
  'window.Quill': 'quill/dist/quill.js'
})]
