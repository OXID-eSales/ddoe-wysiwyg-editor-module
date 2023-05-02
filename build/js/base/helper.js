/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

;(function ( $, global )
{
    global.ddh = {
        _dialog: function ( msg, title, buttons, size, css )
        {
            var opt = {
                message: msg,
                title: title,
                buttons: buttons,
                size: size || 'sm',
                css: css || 'dd-dialog',
                backdrop: false,
                keyboard: false
            };

            if ( typeof arguments[ 0 ] === 'object' )
            {
                opt = $.extend( opt, arguments[ 0 ] );
            }

            var modalSize = 'modal-sm';

            if ( typeof opt.size !== 'undefined' )
            {
                modalSize = 'modal-' + opt.size;
            }

            var modalLayout = '<div class="modal fade ' + ( opt.css ? ' ' + opt.css : '' ) + '" tabindex="-1" role="dialog" aria-labelledby="Confirm" aria-hidden="true">' +
                              '    <div class="modal-dialog ' + modalSize + '">' +
                              '        <div class="modal-content">' +
                              '            <div class="modal-header">' +
                              '                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">' + ddh.translate( 'DD_CLOSE' ) + '</span></button>' +
                              '                <h4 class="modal-title" id="myModalLabel"></h4>' +
                              '            </div>' +
                              '            <div class="modal-body"></div>' +
                              '            <div class="modal-footer">' +
                              '            </div>' +
                              '        </div>' +
                              '    </div>' +
                              '</div>';

            var $modal = $( modalLayout );

            if ( opt.title !== undefined && opt.title !== '' )
            {
                $( '.modal-title', $modal ).html( opt.title );
            }

            if ( opt.message !== undefined && opt.message !== '' )
            {
                if ( typeof opt.message === 'string' )
                {
                    $( '.modal-body', $modal ).html( opt.message );
                }
                else
                {
                    if ( typeof opt.message === 'object' )
                    {
                        $( '.modal-body', $modal ).html( '' );
                        $( '.modal-body', $modal ).append( opt.message );
                    }
                }
            }

            if ( opt.buttons.length )
            {
                var defaultAction = {
                    type: 'button',
                    css: [
                        'btn btn-default'
                    ]
                };

                $.each( opt.buttons, function ()
                    {
                        var action = $.extend( {}, defaultAction, this );

                        var $btn = $( ( action.html || '<button type="' + action.type + '" class="' + action.css.join( ' ' ) + '">' + action.label + '</button>' ) );

                        if ( action.attributes )
                        {
                            $btn.attr( action.attributes );
                        }

                        if ( action.action )
                        {
                            $btn.on( 'click', $.proxy( action.action, $btn, $modal ) );
                        }

                        $( '.modal-footer', $modal ).append( $btn );
                    }
                );

            }

            $( 'body' ).append( $modal );

            $modal.modal( {
                backdrop: opt.backdrop,
                keyboard: opt.keyboard
            } ).on( 'hidden.bs.modal', function ()
                {
                    $( this ).remove();
                }
            ).one( 'shown.bs.modal', function ()
                {
                    if ( $( '.modal-body input[type="text"]', this ).length )
                    {
                        $( '.modal-body input[type="text"]', this ).focus();
                    }
                    else
                    {
                        $( '.modal-footer .btn-primary', this ).focus();
                    }
                }
            );

            return $modal;

        },
        confirm: function ( msg, callback, title, warn )
        {
            if ( typeof title === 'undefined' || title === null )
            {
                title = ddh.translate( 'DD_CONFIRM' );
            }

            var cssWarn = '';
            if ( typeof warn !== 'undefined' && warn )
            {
                cssWarn = ' dd-warn';
            }

            var buttons = [
                {
                    html: '<button type="button" class="btn btn-default" data-dismiss="modal">' + ddh.translate( 'DD_CANCEL' ) + '</button>'
                },
                {
                    html: '<button type="button" class="btn btn-primary" autofocus>' + ddh.translate( 'DD_OK' ) + '</button>',
                    action: function ( $modal )
                    {
                        $modal.modal( 'hide' );
                        callback.call( this );
                    }
                }
            ];

            this._dialog( msg, title, buttons, 'sm', 'dd-modal-confirm' + cssWarn );

        },
        prompt: function ( msg, callback, title, value )
        {
            if ( typeof title === 'undefined' )
            {
                title = ddh.translate( 'DD_CONFIRM' );
            }

            value = value || '';

            msg += '<div class="clearfix" style="margin-top: 10px;"><input type="text" name="prompt" class="form-control" value="' + value + '" /></div>';

            var buttons = [
                {
                    html: '<button type="button" class="btn btn-default" data-dismiss="modal">' + ddh.translate( 'DD_CANCEL' ) + '</button>'
                },
                {
                    html: '<button type="button" class="btn btn-primary" autofocus>' + ddh.translate( 'DD_OK' ) + '</button>',
                    action: function ( $modal )
                    {
                        $modal.modal( 'hide' );
                        callback.call( this, $( 'input[name=prompt]', $modal ).val() );
                    }
                }
            ];

            var $modal = this._dialog( msg, title, buttons, 'sm', 'dd-modal-confirm' );

            $( 'input[name=prompt]', $modal ).on( 'keypress', function( e )
                {
                    if( e.keyCode === 13 )
                    {
                        $( '.btn-primary', $modal ).click();
                    }
                }
            );
        },
        alert: function ( msg, title )
        {
            if ( typeof title === 'undefined' )
            {
                title = 'Information';
            }

            var buttons = [
                {
                    html: '<button type="button" class="btn btn-primary"  data-dismiss="modal">' + ddh.translate( 'DD_OK' ) + '</button>'
                }
            ];

            this._dialog( msg, title, buttons, 'sm', 'dd-modal-confirm' );

        },
        translate: function ( string )
        {
            if ( string && typeof i18n === 'object' )
            {
                if ( i18n[ string ] )
                {
                    return i18n[ string ];
                }
            }

            return string;
        }

    };

    // jQuery area select plugin
    $.fn.areaselect = function ()
    {
        return this.each( function ()
            {
                $( this ).on( 'change', function ()
                    {
                        var group = $( this ).data( 'area-group-value' );
                        var area  = null;

                        if ( typeof $().selectize === 'function' && this.selectize )
                        {
                            this.selectize.refreshOptions( false );

                            if ( this.selectize.getValue() )
                            {
                                area = this.selectize.options[ this.selectize.getValue() ].area;
                            }
                        }
                        else
                        {
                            area = $( this ).val();
                        }

                        if ( group )
                        {
                            $( '*[data-area]' + ( group ? '[data-area-group="' + group + '"]' : '' ) ).hide();
                        }

                        if ( area )
                        {
                            $( '*[data-area="' + area + '"]' ).show();
                        }
                    }
                ).trigger( 'change' );
            }
        );
    };

})( jQuery, window );
