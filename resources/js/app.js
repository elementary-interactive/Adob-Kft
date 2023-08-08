import * as Popper from '@popperjs/core'
window.Popper = Popper

import $ from 'jquery';
window.$ = $;

import 'bootstrap'

import.meta.glob([
  '../images/**',
  '../fonts/**',
]);