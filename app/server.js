var express = require("express");
var path = require("path");
var serveStatic = require("serve-static");
app = express();
app.use(require('connect-history-api-fallback')());
app.use(serveStatic(path.join(__dirname)));
var port = process.env.PORT || 8080;
app.listen(port);
console.log("server started " + port);
