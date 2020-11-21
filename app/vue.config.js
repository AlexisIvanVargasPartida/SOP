module.exports = {
  pluginOptions: {},
  devServer: {
    proxy: "http://api.so-politica.online/public/api/"
  },/*
  pwa: {
    workboxOptions: {
      skipWaiting: true
    }
  },*/
  productionSourceMap: false,
  publicPath: process.env.NODE_ENV === "production" ? "/" : "/"
};
