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
  publicPath: process.env.NODE_ENV === "production" ? "/" : "/",
  pages: {
    index: {
      entry: './src/main.js',
      template: 'public/index.html',
      filename: 'index.html'
    }
  }
};
