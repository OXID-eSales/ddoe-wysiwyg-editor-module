/**
 * This file is part of OXID eSales WYSIWYG module.
 *
 * OXID eSales WYSIWYG module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales WYSIWYG module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales WYSIWYG module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eSales WYSIWYG
 */

+function ( $ )
{
    'use strict';

    // MEDIA LIBRARY CLASS DEFINITION
    // ==============================

    var MediaLibrary = function ()
    {
        var self = this;

        $( document ).on( 'keydown', function ( e )
            {
                if ( e.ctrlKey || e.keyCode === 224 || e.keyCode === 91 || e.keyCode === 93 )
                {
                    self.ctrlKeyPressed = true;
                }
            }
        ).on( 'keyup', function ( e )
            {
                if ( e.ctrlKey || e.keyCode === 224 || e.keyCode === 91 || e.keyCode === 93 )
                {
                    self.ctrlKeyPressed = false;
                }
            }
        );

        // Create pseudo helper if not exists
        if ( typeof ddh == 'undefined' )
        {
            window.ddh = {
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
            }
        }
    };


    // MEDIA LIBRARY MAIN PROPERTIES
    // =============================

    MediaLibrary.VERSION = '1.0.0';

    MediaLibrary.prototype.ctrlKeyPressed = false;

    MediaLibrary.prototype._actionLink   = '';
    MediaLibrary.prototype._resourceLink = '';


    // MEDIA LIBRARY METHODS
    // =====================

    MediaLibrary.prototype.setActionLink = function ( url )
    {
        this._actionLink = decodeURI( url );
    };

    MediaLibrary.prototype.setResourceLink = function ( url )
    {
        this._resourceLink = decodeURI( url );
    };


    MediaLibrary.prototype._loadItemDetails = function ( file, $dialog )
    {
        var ui = this;

        if ( typeof file === 'undefined' )
        {
            file = false;
        }

        if ( typeof $dialog === 'undefined' )
        {
            $dialog = $( '.dd-media' ).first().closest( '.modal' );
        }

        var $detailForm = $( '.dd-media-details-form', $dialog );

        if ( !file )
        {
            $detailForm.hide();
        }
        else
        {
            if ( file.preview )
            {
                $( '.dd-media-details-preview-icon', $detailForm ).hide();
                $( '.dd-media-details-preview', $detailForm ).attr( 'src', file.preview ).show();
            }
            else
            {
                $( '.dd-media-details-preview-icon', $detailForm ).show();
                $( '.dd-media-details-preview', $detailForm ).hide();
            }

            var fileInfo = ( file.imagesize ? file.imagesize + ' | ' : '' ) + ui._formatFileSize( file.filesize );

            $( '.dd-media-details-name', $detailForm ).text( file.file );
            $( '.dd-media-details-infos', $detailForm ).text( fileInfo );

            $( '.dd-media-details-input-url', $detailForm ).val( file.url );

            $detailForm.show();
        }
    };


    MediaLibrary.prototype._formatFileSize = function ( size )
    {
        size = parseInt( size );

        var names = [ 'tb', 'gb', 'mb', 'kb', 'b' ];

        while ( size > 1024 && names.length )
        {
            size = Math.round( ( size / 1024 ) * 100 ) / 100;
            names.pop();
        }

        return size + ' ' + names.pop();
    };


    /**
     * Usage:
     * MediaLibrary.open( [ filter ], [ multiple ], callback );
     *
     * @param callback
     */
    MediaLibrary.prototype.open = function ( callback )
    {
        var actionLink   = this._actionLink;
        var resourceLink = this._resourceLink;
        var filter       = null, multiple = false;

        if ( arguments.length === 2 )
        {
            if ( typeof arguments[ 0 ] === 'string' || arguments[ 0 ] instanceof RegExp )
            {
                filter = arguments[ 0 ];
            }
            else
            {
                if ( typeof arguments[ 0 ] === 'boolean' )
                {
                    multiple = arguments[ 0 ];
                }
            }

            callback = arguments[ 1 ];
        }
        else
        {
            if ( arguments.length === 3 )
            {
                if ( typeof arguments[ 0 ] === 'string' || arguments[ 0 ] instanceof RegExp )
                {
                    filter   = arguments[ 0 ];
                    multiple = arguments[ 1 ];
                }
                else
                {
                    if ( typeof arguments[ 0 ] === 'boolean' )
                    {
                        multiple = arguments[ 0 ];
                        filter   = arguments[ 1 ];
                    }
                }

                callback = arguments[ 2 ];
            }
        }

        var actions = [
            {
                label: ddh.translate( 'DD_CANCEL' ),
                attributes: {
                    'data-dismiss': 'modal'
                }
            },
            {
                label: ddh.translate( 'DD_APPLY' ),
                css: [
                    'btn btn-primary dd-media-submit'
                ],
                action: function ( $dialog )
                {
                    var $item = $( '.dd-media-item.active', $dialog );

                    if ( !$item.length )
                    {
                        return;
                    }

                    if ( typeof callback === 'function' )
                    {
                        var blTypeNotAllowed = false;
                        var files            = [];

                        $item.each( function ()
                            {
                                var filetype = $( this ).data( 'filetype' );

                                if ( filter !== null && ( ( typeof filter === 'string' && filter !== filetype ) || ( filter instanceof RegExp && !filetype.match( filter ) ) ) )
                                {
                                    blTypeNotAllowed = true;
                                }
                                else
                                {
                                    files.push(
                                        {
                                            id: $( this ).data( 'id' ),
                                            file: $( this ).data( 'file' ),
                                            url: resourceLink + $( this ).data( 'file' ),
                                            type: filetype
                                        }
                                    );
                                }
                            }
                        );

                        if ( blTypeNotAllowed )
                        {
                            ddh.alert( ddh.translate( 'DD_MEDIA_FILETYPE_NOT_ALLOWED' ) );
                            return;
                        }

                        if ( multiple )
                        {
                            callback.call( $dialog, files );
                        }
                        else
                        {
                            if ( files.length )
                            {
                                var item = files[ 0 ];
                                callback.call( $dialog, item.id, item.file, item.url, item.type );
                            }
                            else
                            {
                                callback.call( $dialog, false );
                            }
                        }

                    }

                    $dialog.modal( 'hide' );
                }
            },
            {
                label: '<span class="text-danger">' + ddh.translate( 'DD_MEDIA_REMOVE' ) + '</span>',
                css: [
                    'btn btn-link pull-left disabled dd-media-remove'
                ],
                action: function ( $dialog )
                {
                    var $item = $( '.dd-media-item.active', $dialog );
                    var $btn  = this;

                    if ( !$btn.hasClass( 'disabled' ) && $item.length )
                    {
                        ddh.confirm( ddh.translate( 'DD_MEDIA_REMOVE_CONFIRM' ), function ()
                            {
                                $item.addClass( 'dd-media-item-removing' );

                                var deleteIDs = [];

                                $item.each( function ()
                                    {
                                        deleteIDs.push( $( this ).data( 'id' ) );
                                    }
                                );

                                $.get( actionLink + 'cl=ddoewysiwygmedia_view&fnc=remove&id[]=' + deleteIDs.join( '&id[]=' ), function ()
                                    {
                                        $item.each( function ()
                                            {
                                                $( this ).parent().remove();
                                            }
                                        );

                                        $btn.addClass( 'disabled' );

                                        if ( !$( '.dd-media-list-items > .row > .dd-media-col', $dialog ).length )
                                        {
                                            $( '.dd-media-list', $dialog ).addClass( 'empty' );
                                        }
                                    }
                                );
                            }
                        );
                    }

                }
            }
        ];

        if ( multiple )
        {
            actions.unshift(
                {
                    html: '<span class="text-muted" style="font-style: italic; margin-right: 15px;">' + ddh.translate( 'DD_MEDIA_MULTIPLE_INFO' ) + '</span>',
                    css: [
                        'dd-media-multiple-info'
                    ]
                }
            );
        }

        var $dialog = ddh._dialog(
            {
                title: ddh.translate( 'DD_MEDIA_DIALOG' ),
                message: '<div class="dd-dialog-loader"></div>',
                buttons: actions,
                size: 'lg',
                backdrop: true
            }
        );

        $dialog.data( 'media-options',
            {
                multiple: multiple,
                filter: filter
            }
        );

        this._loadMediaContent( $dialog );
    };

    /**
     * Usage:
     * MediaLibrary.init( [ filter ], [ multiple ], callback );
     *
     * @param callback
     */
    MediaLibrary.prototype.init = function ( callback )
    {
        var actionLink   = this._actionLink;
        var resourceLink = this._resourceLink;
        var filter       = null, multiple = false;

        if ( arguments.length === 2 )
        {
            if ( typeof arguments[ 0 ] === 'string' || arguments[ 0 ] instanceof RegExp )
            {
                filter = arguments[ 0 ];
            }
            else
            {
                if ( typeof arguments[ 0 ] === 'boolean' )
                {
                    multiple = arguments[ 0 ];
                }
            }

            callback = arguments[ 1 ];
        }
        else
        {
            if ( arguments.length === 3 )
            {
                if ( typeof arguments[ 0 ] === 'string' || arguments[ 0 ] instanceof RegExp )
                {
                    filter   = arguments[ 0 ];
                    multiple = arguments[ 1 ];
                }
                else
                {
                    if ( typeof arguments[ 0 ] === 'boolean' )
                    {
                        multiple = arguments[ 0 ];
                        filter   = arguments[ 1 ];
                    }
                }

                callback = arguments[ 2 ];
            }
        }

        var $dialog = $( '.dd-media-wrapper' );

        $dialog.data( 'media-options',
            {
                multiple: multiple,
                filter: filter
            }
        );

        // Communicate with Overlay
        if( top.basefrm && top.basefrm.OverlayInstance )
        {
            top.basefrm.OverlayInstance.onContentLoad( function()
                {
                    var self = this;

                    if( $( '.dd-overlay-dialog-footer .dd-overlay-dialog-apply', self.$overlay ).length )
                    {
                        $( '.dd-overlay-dialog-footer .dd-overlay-dialog-apply', self.$overlay ).remove();
                    }

                    if( typeof callback !== 'function' && self.overlayContext )
                    {
                        callback = function( id, file, fullpath )
                        {
                            self.overlayContext.invoke('editor.insertImage', fullpath, function( $image )
                                {
                                    $image.css( 'max-width', '100%' );
                                    $image.attr( 'data-filename', file );
                                    $image.attr( 'data-filepath', fullpath );
                                    $image.attr( 'data-source', 'media' );
                                    $image.addClass( 'dd-wysiwyg-media-image' );
                                }
                            );
                        };
                    }

                    var $applyAction = $( '<button type="button" class="dd-overlay-dialog-button dd-overlay-dialog-apply">' + ddh.translate( 'DD_APPLY' ) + '</button>' );

                    $applyAction.on( 'click', function( e )
                        {
                            e.preventDefault();

                            var $item = $( '.dd-media-item.active', $dialog );

                            if ( !$item.length )
                            {
                                return;
                            }

                            if ( typeof callback === 'function' )
                            {
                                var blTypeNotAllowed = false;
                                var files            = [];

                                $item.each( function ()
                                    {
                                        var filetype = $( this ).data( 'filetype' );

                                        if ( filter !== null && ( ( typeof filter === 'string' && filter !== filetype ) || ( filter instanceof RegExp && !filetype.match( filter ) ) ) )
                                        {
                                            blTypeNotAllowed = true;
                                        }
                                        else
                                        {
                                            files.push(
                                                {
                                                    id: $( this ).data( 'id' ),
                                                    file: $( this ).data( 'file' ),
                                                    url: resourceLink + $( this ).data( 'file' ),
                                                    type: filetype
                                                }
                                            );
                                        }
                                    }
                                );

                                if ( blTypeNotAllowed )
                                {
                                    ddh.alert( ddh.translate( 'DD_MEDIA_FILETYPE_NOT_ALLOWED' ) );
                                    return;
                                }

                                if ( multiple )
                                {
                                    callback.call( $dialog, files );
                                }
                                else
                                {
                                    if ( files.length )
                                    {
                                        var item = files[ 0 ];
                                        callback.call( $dialog, item.id, item.file, item.url, item.type );
                                    }
                                    else
                                    {
                                        callback.call( $dialog, false );
                                    }
                                }

                            }

                            self.hideOverlay();
                        }
                    );

                    $( '.dd-overlay-dialog-footer', self.$overlay ).prepend( $applyAction );
                }
            );
        }

        this._loadMediaContent( $dialog );

    };

    MediaLibrary.prototype.refreshMedia = function ()
    {
        var $media = $( '.dd-media' );

        if ( $media.length )
        {
            var $dialog = $media.closest( '.modal' );

            if( $dialog.length )
            {
                $( '.modal-body', $dialog ).html( '<div class="dd-dialog-loader"></div>' );
            }
            else
            {
                $dialog = $( '.dd-media-wrapper' );

                $( '.dd-content', $dialog ).html( '<div class="dd-dialog-loader"></div>' );
            }

            this._loadMediaContent( $dialog );
        }
    };

    MediaLibrary.prototype.addMediaItem = function ( id, file, filetype, filesize, thumb, imagesize )
    {
        var resourceLink = this._resourceLink;
        var $item        = $( '.dd-media-list-items .dd-media-dz-helper > div' ).clone();

        $( '.dd-media-item', $item ).data(
            {
                'id': id,
                'file': file,
                'filetype': filetype,
                'filesize': filesize,
                'imagesize': imagesize
            }
        );

        if ( !thumb || thumb === undefined )
        {
            $( '.dd-media-thumb', $item ).hide();
            $( '.dd-media-icon', $item ).show();
            $( '.dd-media-item-label', $item ).show();
        }
        else
        {
            $( '.dd-media-thumb', $item ).attr( 'src', resourceLink + 'thumbs/' + thumb );
        }

        $( '.dd-media-list-items > .row' ).append( $item );

    };

    MediaLibrary.prototype._loadMediaContent = function ( $dialog )
    {
        var actionLink   = this._actionLink;
        var resourceLink = this._resourceLink;
        var ui           = this;
        var mediaOptions = $dialog.data( 'media-options' );

        $.get( actionLink + 'cl=ddoewysiwygmedia_view', function ( html )
            {
                $( '.dd-media-remove', $dialog ).addClass( 'disabled' );

                if( $dialog.is( '.dd-media-wrapper' ) )
                {
                    $( '.dd-content', $dialog ).html( html );
                }
                else
                {
                    $( '.modal-body', $dialog ).html( html );
                }

                var $detailForm = $( '.dd-media-details-form', $dialog );

                $( '.dd-media-details-delete-action', $dialog ).on( 'click', function()
                    {
                        var $item = $( '.dd-media-item.active', $dialog ).first();

                        if ( $item.length )
                        {
                            ddh.confirm( ddh.translate( 'DD_MEDIA_REMOVE_CONFIRM' ), function ()
                                {
                                    $item.addClass( 'dd-media-item-removing' );

                                    var deleteIDs = [];

                                    $item.each( function ()
                                        {
                                            deleteIDs.push( $( this ).data( 'id' ) );
                                        }
                                    );

                                    $.get( actionLink + 'cl=ddoewysiwygmedia_view&fnc=remove&id[]=' + deleteIDs.join( '&id[]=' ), function ()
                                        {
                                            $item.each( function ()
                                                {
                                                    $( this ).parent().remove();
                                                }
                                            );

                                            if ( !$( '.dd-media-list-items > .row > .dd-media-col', $dialog ).length )
                                            {
                                                $( '.dd-media-list', $dialog ).addClass( 'empty' );
                                            }

                                            $detailForm.hide();
                                        }
                                    );
                                }
                            );
                        }
                    }
                );

                $( '.dd-media', $dialog ).on( 'click', '.dd-media-item', function ( e )
                    {
                        e.preventDefault();
                        
                        if ( $( this ).parent( '.dz-error' ).length )
                        {
                            return;
                        }

                        if ( mediaOptions && mediaOptions.multiple && ui.ctrlKeyPressed )
                        {
                            $( this ).toggleClass( 'active' );
                        }
                        else
                        {
                            $( '.dd-media-item', $dialog ).removeClass( 'active' );
                            $( this ).addClass( 'active' );
                        }

                        var $detailsItem = null;

                        if ( $( this ).hasClass( 'active' ) )
                        {
                            $detailsItem = $( this );
                        }
                        else
                        {
                            $detailsItem = $( this ).parent().siblings().find( '.dd-media-item.active' ).first();
                        }
 
                        if ( $detailsItem && $detailsItem.length )
                        {
                            var itemData = $detailsItem.data();

                            itemData.url  = resourceLink + $detailsItem.data( 'file' );
                            itemData.type = itemData.filetype;
                            itemData.size = itemData.filesize;

                            if ( $( '.dd-media-thumb', $detailsItem ).length )
                            {
                                itemData.preview = $( '.dd-media-thumb', $detailsItem ).attr( 'src' );
                            }

                            ui._loadItemDetails( itemData, $dialog );
                        }

                        if ( !$( '.dd-media-list-items > .row > .dd-media-col > .active', $dialog ).length )
                        {
                            ui._loadItemDetails( false, $dialog );

                            $( '.dd-media-remove' ).addClass( 'disabled' );
                        }
                        else
                        {
                            $( '.dd-media-remove' ).removeClass( 'disabled' );
                        }
                    }
                ).on( 'dblclick', '.dd-media-item', function ( e )
                    {
                        e.preventDefault();

                        $( '.dd-media-submit', $dialog ).trigger( 'click' );
                    }
                );

                $( '.dd-media', $dialog ).dropzone(
                    {
                        url: actionLink + 'cl=ddoewysiwygmedia_view&fnc=upload',
                        parallelUploads: 10,

                        previewsContainer: $( '.dd-media-list-items > .row', $dialog )[ 0 ],
                        previewTemplate: $( '.dd-media-list-items .dd-media-dz-helper', $dialog ).html(),

                        clickable: $( '.dd-media-upload', $dialog )[ 0 ],

                        //forceFallback: true,
                        fallback: function ()
                        {
                            $( '.dd-media-upload-info', $dialog ).hide();
                            $( '.dd-media-upload-fallback', $dialog ).show();
                        },

                        init: function ()
                        {
                            this.on( 'addedfile', function ()
                                {
                                    $( '.dd-media-list', $dialog ).removeClass( 'empty' );
                                    $( '.dd-media-tabs .nav-tabs a[href="#mediaList"]', $dialog ).tab( 'show' );

                                    $( '.dd-media-list-items', $dialog ).scrollTop( $( '.dd-media-list-items > .row', $dialog ).height() );
                                }
                            );

                            this.on( 'success', function ( file, response )
                                {
                                    $( '.dd-media-item', file.previewElement ).data(
                                        {
                                            'id': response.id,
                                            'file': response.file,
                                            'filetype': response.filetype,
                                            'filesize': response.filesize,
                                            'imagesize': ( response.imagesize || null )
                                        }
                                    ).trigger( 'click' );

                                    $( '.dd-media-file-count', $dialog ).text( parseInt( $( '.dd-media-file-count', $dialog ).text() ) + 1 );
                                }
                            );

                            this.on( 'complete', function ( file )
                                {
                                    if ( !file.type.match( /image\.*/ ) )
                                    {
                                        $( '.dd-media-thumb', file.previewElement ).hide();
                                        $( '.dd-media-icon', file.previewElement ).show();
                                        $( '.dd-media-item-label', file.previewElement ).show();
                                    }
                                }
                            );

                            this.on( 'error', function ( file, errorMessage, xhr )
                                {
                                    console.error( file );
                                }
                            );
                        }
                    }
                );

                $( '.dd-media-search-form' ).on( 'submit', function ( e )
                {
                    e.preventDefault();
                    return false;
                } );

                $( '.dd-media-search-form input' ).on( 'keyup', function ( e )
                    {
                        e.preventDefault();

                        var val = $( this ).val();

                        if ( val === '' )
                        {
                            $( '.dd-media-list-items > .row > .dd-media-col', $dialog ).show();
                        }
                        else
                        {
                            $( '.dd-media-list-items > .row > .dd-media-col', $dialog ).each( function ()
                                {
                                    var $item = $( '.dd-media-item', this );

                                    if ( $item.data( 'file' ).search( val ) > -1 )
                                    {
                                        $( this ).show();
                                    }
                                    else
                                    {
                                        $( this ).hide();
                                    }
                                }
                            );
                        }
                    }
                );

                if ( $( '.dd-media-item', $dialog ).length >= 18 )
                {
                    window.setTimeout( function ()
                        {
                            ui._loadMoreMediaContent();
                        },
                        500
                    );
                }

            }
        );
    };

    MediaLibrary.prototype._loadMoreMediaContent = function ( page )
    {
        if ( !page || page === undefined )
        {
            page = 1;
        }

        var actionLink = this._actionLink;
        var start      = page * 18;
        var ui         = this;

        $.get( actionLink + 'cl=ddoewysiwygmedia_view&fnc=moreFiles&start=' + start, function ( data )
            {
                if ( data.files && data.files.length )
                {
                    $.each( data.files, function ()
                        {
                            ui.addMediaItem( this.OXID, this.DDFILENAME, this.DDFILETYPE, this.DDFILESIZE, ( this.DDTHUMB || false ), ( this.DDIMAGESIZE || null ) );
                        }
                    );
                }

                if ( data.more )
                {
                    ui._loadMoreMediaContent( ( page + 1 ) );
                }
            }
        );

    };

    // Make MediaLibrary public
    window.MediaLibrary = new MediaLibrary();

}( jQuery );
