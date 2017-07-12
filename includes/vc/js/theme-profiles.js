/**
 * Setup javascript options for visual composer custom shortcodes
 * https://github.com/mmihey/vc-dev-example
 * */
jQuery(document).ready(function ($) {
    "use strict";
    if (window.ikiVCCustomProfiles) {
        window.ikiVCThemeProfiles = window.ikiVCCustomProfiles.extend();
    }
});
