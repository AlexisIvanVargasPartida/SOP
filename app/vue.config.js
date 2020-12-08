module.exports = {
  pluginOptions: {},
  devServer: {
    proxy: "http://api.so-politica.online/public/api/"
  },
  pwa: {
    themeColor: '#60b664',
    workboxOptions: {
      skipWaiting: true,
      exclude: [".htaccess"]
    }
  },
  productionSourceMap: false,
  publicPath: "/",
  pages: {
    index: {
      entry: './src/main.js',
      template: 'public/index.html',
      filename: 'index.html'
    }
  }
};
