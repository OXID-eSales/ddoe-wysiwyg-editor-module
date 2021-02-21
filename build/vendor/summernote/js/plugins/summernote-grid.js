(function (factory) {
    /* Global define */
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(window.jQuery);
    }
}(function ($) {
    var defaultOptions = {
        wrapper: "row",
        columns: [
            "col-md-12",
            "col-md-6",
            "col-md-4",
            "col-md-3",
        ]
    };

    var GridPlugin = function (context) {
        var lang = context.options.langInfo;
        var callbacks = context.options.callbacks;
        var icons = context.options.icons;
        var ui = $.summernote.ui;
        var options = $.extend(defaultOptions, context.options.grid || {});

        var self = this;

        context.memo('button.grid', function () {
            return ui.buttonGroup([
                ui.button({
                    className: 'dropdown-toggle',
                    contents: '<i class="'+ icons.grid +'"/> <span class="note-icon-caret"></span>',
                    tooltip: lang.grid.tooltip,
                    data: {
                        toggle: 'dropdown'
                    }
                }),
                ui.dropdown({
                    className: 'dropdown-menu dropdown-style text-grey-800 bg-white',
                    contents: self.createDropdownContent(),
                    callback: self.createGrid
                })
            ]).render();
        });

        this.createDropdownContent = function() {
            var contents = '';

            for(var i = 0; i < options.columns.length; i++) {
                if (options.columns[i] != null) {
                    contents += this.createDropdownElement(i)
                }
            }

            return contents;
        };

        this.createDropdownElement = function (index) {
            var li = document.createElement('li');
            var a = document.createElement('a');

            a.setAttribute('class', 'text-grey-800');
            a.setAttribute('href', '#');
            a.setAttribute('data-index', index);
            a.innerText = lang.grid.label + ' #' + (index + 1);
            li.appendChild(a);

            return li.outerHTML;
        };

        this.createGrid = function($dropdown) {
            $dropdown.find('li a').each(function () {
                $(this).click(function () {
                    var index = $(this).data('index');
                    var wrap = self.createGridNode(index);

                    if (callbacks.onGridInsert) {
                        context.triggerEvent('grid.insert', wrap);
                    } else {
                        context.invoke("editor.insertNode", wrap);
                    }

                    return false;
                });
            });
        };

        this.createGridNode = function (index) {
            var wrap = document.createElement('div');

            wrap.className = options.wrapper;

            for (var i = 0; i <= index; i++) {
                var col = document.createElement('div');
                var p = document.createElement('p');

                col.className = options.columns[index];
                p.innerHTML = lang.grid.label + " #" + (i + 1);

                col.appendChild(p);
                wrap.appendChild(col);
            }

            return wrap;
        };

    };

    $.extend(true, $.summernote, {
        plugins: {
            gridPlugin: GridPlugin
        },
        options: {
            grid: defaultOptions,
            callbacks: {
                onGridInsert: null
            },
            icons: {
                grid: "fa fa-table"
            }
        },
        lang: {
            'en-US': {
                grid: {
                    tooltip: "Columns",
                    label: "Columns",
                }
            }
        },
    });
}));