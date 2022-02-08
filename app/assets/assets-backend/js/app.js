// require jQuery normally
const $ = require('jquery');
// create global $ and jQuery variables
global.$ = global.jQuery = $;

require('bootstrap');
require('select2');
require('webpack-jquery-ui/datepicker');
require('jquery-ui/ui/i18n/datepicker-ru');
require('webpack-jquery-ui/sortable');

$('.select2').select2({
    theme: 'classic',
    width: '100%',
    placeholder: "Выберите значение",
    allowClear: true
});
