// webpack.config.js
var Encore = require('@symfony/webpack-encore');

// конфигурация для frontend приложения
Encore
    .enableSingleRuntimeChunk()
    .setOutputPath('public/assets-frontend/build/')
    .setPublicPath('/assets-frontend/build')
    .addEntry('js/app', './assets/assets-frontend/js/app.js')
    .addStyleEntry('css/main', './assets/assets-frontend/css/main.scss')
    .addStyleEntry('css/app', './assets/assets-frontend/css/app.css')
    .autoProvidejQuery()
    .enableSassLoader(function(sassOptions) {}, {
        resolveUrlLoader: false
    })
    .enableSourceMaps(false)
    .enableVersioning()
    .cleanupOutputBeforeBuild()
;

const frontend = Encore.getWebpackConfig();
frontend.name = 'frontend';

// перезапустить Encore, чтобы построить вторую конфигурацию
Encore.reset();

// конфигурация для backend приложения
Encore
    .enableSingleRuntimeChunk()
    .setOutputPath('public/assets-backend/build/')
    .setPublicPath('/assets-backend/build')
    .addEntry('js/app', './assets/assets-backend/js/app.js')
    .addStyleEntry('css/main', './assets/assets-backend/css/main.scss')
    .addStyleEntry('css/login', './assets/assets-backend/css/login.css')
    .addStyleEntry('css/app', './assets/assets-backend/css/app.css')
    .autoProvidejQuery()
    .enableSassLoader(function(sassOptions) {}, {
        resolveUrlLoader: false
    })
    .enableSourceMaps(false)
    .enableVersioning()
    .cleanupOutputBeforeBuild()
;

const backend = Encore.getWebpackConfig();
backend.name = 'backend';

// экспортировать финальную конфигурацию в качестве массива множества конфигураций
module.exports = [frontend, backend];
