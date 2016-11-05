/* global app, ko */

'use strict';

require(['app'], function() {
    var form = app.forms['h-page-editor-page-form'];

    var PageForm = function() {
        this.name = ko.observable(form.inputs.name.val());

        // the page title
        this.title = ko.observable(form.inputs.title.val());

        // The page URI
        this.uri = ko.observable(form.inputs.uri.val());
        if(!this.uri()) {
            this.uri = ko.computed(function() {
                var uri = this.title();

                uri = uri.replace(/[^a-zA-Z0-9_\-\{\}\/]/g, '-')
                        .toLowerCase()
                        .replace(/[\-]{2,}/g, '-')
                        .replace(/[\-]$/, '');

                return uri;
            }.bind(this));
        }

        this.uri.subscribe(function(value) {
            var route = app.getRouteFromUri(value);

            if(route) {
                if(value.match(/.*\-(\d)+$/)) {
                    this.uri(value.replace(/\-(\d)+$/, function(index) {
                        return '-' + (index + 1);
                    }));
                }
                else{
                    this.uri(value + '-1');
                }
            }
        }.bind(this));

        this.content = ko.observable(form.inputs.content.val());
    };

    PageForm.prototype.preview = function() {
        app.load(app.getUri('h-page-editor-preview-page'), {
            newtab : true,
            post : {
                name : this.name(),
                content : this.content()
            }
        });
    };

    var model = new PageForm();

    form.onsuccess = function(data) {
        if(data.action) {
            app.routes['page-editor-page-' + data.id] = {
                url : model.uri(),
                pattern : model.uri(),
                where : {},
                args : {}
            };
        }
        else{
            delete app.routes['h-page-editor-page-' + data.id];
        }
        app.load(app.getUri('h-page-editor-manage-pages'));
    };

    ko.applyBindings(model, document.getElementById(form.id));
});