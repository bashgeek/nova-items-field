let mix = require('laravel-mix')
let NovaExtension = require('laravel-nova-devtool')

mix.extend('nova', new NovaExtension())

mix
    .setPublicPath('dist')
    .js('resources/js/field.js', 'js')
    .vue({ version: 3 })
    .nova('blendbyte/nova-items-field')
