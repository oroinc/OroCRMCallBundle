define(function(require) {
    'use strict';

    const _ = require('underscore');
    const Select2Component = require('oro/select2-component');

    const Select2CallPhoneComponent = Select2Component.extend({
        /** @property {Array} */
        suggestions: [],

        /** @property {String} */
        value: '',

        /**
         * @inheritdoc
         */
        constructor: function Select2CallPhoneComponent(options) {
            Select2CallPhoneComponent.__super__.constructor.call(this, options);
        },

        /**
         * @inheritdoc
         */
        initialize: function(options) {
            this.suggestions = _.result(options, 'suggestions') || this.suggestions;
            this.value = _.result(options, 'value') || this.value;
            Select2CallPhoneComponent.__super__.initialize.call(this, options);
        },

        preConfig: function(config) {
            const that = this;
            Select2CallPhoneComponent.__super__.preConfig.call(this, config);
            config.minimumResultsForSearch = 0;
            if (this.value !== false) {
                config.initSelection = function(element, callback) {
                    const val = element.val();
                    callback({id: val, text: val});
                };
            }
            config.query = function(options) {
                const data = {results: []};
                const items = _.clone(that.suggestions);
                const initialVal = that.value.trim();
                const currentVal = options.element.val().trim();
                const term = options.term.trim();
                if (initialVal && _.indexOf(items, initialVal) === -1) {
                    items.unshift(initialVal);
                }
                if (currentVal && _.indexOf(items, currentVal) === -1) {
                    items.unshift(currentVal);
                }
                if (term && _.indexOf(items, term) === -1) {
                    items.unshift(term);
                }
                _.each(items, function(item) {
                    data.results.push({id: item, text: item});
                });
                options.callback(data);
            };

            return config;
        }
    });
    return Select2CallPhoneComponent;
});
