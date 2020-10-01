module.exports = {
  pluginOptions: {
  },
  devServer: {
    proxy: "http://api.so-politica.online/public/api/"
  },
  publicPath: process.env.NODE_ENV === "production" ? "/" : "/",
};
