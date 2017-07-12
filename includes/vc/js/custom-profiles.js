/**
 * Setup javascript options for visual composer custom shortcodes
 * https://github.com/mmihey/vc-dev-example
 * */
jQuery(document).ready(function ($) {
    "use strict";

    window.ikiVCCustomProfiles = vc.shortcode_view.extend({

        //Called every time when params is changed/appended. Also on first initialisation
        changeShortcodeParams: function (model) {

            window.ikiVCCustomProfiles.__super__.changeShortcodeParams.call(this, model);
            var tag = model.get('shortcode');
            var params = model.get('params');
            var settings = vc.map[tag];
            var _self = this;

            _.each(settings.params, function (param_settings) {
                var $wrapper = _self.$el.find('> .wpb_element_wrapper, > .vc_element-wrapper');

                var valTest = _self.ikiGetValue(param_settings.param_name);
                var valPretty = _self.getKeyByValue(param_settings.value, valTest);

                if (param_settings.type === 'textfield') {
                    valPretty = valTest;
                }

                if (valPretty) {
                    $wrapper.children('[name=' + param_settings.param_name + ']').html(param_settings.heading + '  :  '
                        + valPretty);
                }
            });

        },

        getKeyByValue: function getKeyByValue(haystack, needle) {
            return _.findKey(haystack, function (value, key) {
                if (needle === value) {
                    return true;
                }
            });

        },
        ikiGetValue: function ikiGetValue(valueName) {
            var v = null;
            if (!this.model._changing) {
                //v = this.model._previousAttributes.params[valueName];
                v = this.model.attributes.params[valueName];
            }
            else {
                v = this.model.changed.params[valueName];
            }
            return v;
        }
    });
});