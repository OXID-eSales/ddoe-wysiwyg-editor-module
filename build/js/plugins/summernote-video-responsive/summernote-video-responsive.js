(function ( factory )
{
    if ( typeof define === 'function' && define.amd )
    {
        define( ['jquery'], factory );
    }
    else if ( typeof module === 'object' && module.exports )
    {
        module.exports = factory( require( 'jquery' ) );
    }
    else
    {
        factory( window.jQuery );
    }
}( function ( $ )
   {
       $.extend( true, $.summernote.lang, {
           'en-US': { /* English */
               videoResponsive: {
                   dialogTitle: 'Insert Video',
                   tooltip:     'Video',
                   pluginTitle: 'Video',
                   href:        'URL',
                   ok:          'OK',
                   providers:   '(YouTube, Vimeo, Vine, Instagram, DailyMotion or Youku)'
               }
           },
           'de-DE': { /* English */
               videoResponsive: {
                   dialogTitle: 'Video einfügen',
                   tooltip:     'Video',
                   pluginTitle: 'Video',
                   href:        'URL',
                   ok:          'OK',
                   providers:   '(YouTube, Vimeo, Vine, Instagram, DailyMotion oder Youku)'
               }
           }
       } );
       $.extend( $.summernote.options, {
           videoResponsive: {
               icon: '<i class="note-icon-video"></i>'
           }
       } );
       $.extend( $.summernote.plugins, {
           'videoResponsive': function ( context )
                              {
                                  var self      = this,
                                      ui        = $.summernote.ui,
                                      $editor   = context.layoutInfo.editor,
                                      $editable = context.layoutInfo.editable,
                                      options   = context.options,
                                      lang      = options.langInfo;
                                  context.memo( 'button.videoResponsive', function ()
                                  {
                                      var button = ui.button( {
                                                                  contents: options.videoResponsive.icon,
                                                                  tooltip:  lang.videoResponsive.tooltip,
                                                                  click:    function ( e )
                                                                            {
                                                                                context.invoke( 'saveRange' );
                                                                                context.invoke( 'videoResponsive.show' );
                                                                            }
                                                              } );
                                      return button.render();
                                  } );
                                  this.initialize     = function ()
                                  {
                                      var optionsId = options.id;
                                      var $container = options.dialogsInBody ? $( document.body ) : $editor;
                                      var body       =
                                              '<div class="form-group note-form-group row-fluid">' +
                                              '  <label for="note-video-responsive-href-' + optionsId + '" class="note-form-label">' + lang.videoResponsive.href + ' <small class="text-muted">' + lang.videoResponsive.providers + '</small></label>' +
                                              '  <input type="text" id="note-video-responsive-href-' + optionsId + '" class="note-video-responsive-href form-control note-form-control note-input">' +
                                              '</div>';
                                      this.$dialog   = ui.dialog( {
                                                                      title:  lang.videoResponsive.dialogTitle,
                                                                      body:   body,
                                                                      footer: '<button href="#" class="btn btn-primary note-video-responsive-btn">' + lang.videoResponsive.ok + '</button>'
                                                                  } ).render().appendTo( $container );
                                  };
                                  this.destroy        = function ()
                                  {
                                      ui.hideDialog( this.$dialog );
                                      this.$dialog.remove();
                                  };
                                  this.bindEnterKey   = function ( $input, $btn )
                                  {
                                      $input.on( 'keypress', function ( e )
                                      {
                                          if ( e.keyCode === 13 )
                                          {
                                              $btn.trigger( 'click' );
                                          }
                                      } );
                                  };
                                  this.bindLabels     = function ()
                                  {
                                      self.$dialog.find( '.form-control:first' ).focus().select();
                                      self.$dialog.find( 'label' ).on( 'click', function ()
                                      {
                                          $( this ).parent().find( '.form-control:first' ).focus();
                                      } );
                                  };
                                  this.show           = function ()
                                  {
                                      var $vid    = $( $editable.data( 'target' ) );
                                      var vidInfo = {
                                          vidDom: $vid,
                                          href:   $vid.attr( 'href' )
                                      };
                                      this.showLinkDialog( vidInfo ).then( function ( vidInfo )
                                                                           {
                                                                               ui.hideDialog( self.$dialog );
                                                                               var $vid            = vidInfo.vidDom,
                                                                                   $videoHref      = self.$dialog.find( '.note-video-responsive-href' ),
                                                                                   url             = $videoHref.val(),
                                                                                   $videoHTML      = $( '<div/>' );
                                                                                   $videoHTML.addClass( 'embed-responsive embed-responsive-16by9' );
                                                                               var videoWidth  = 'auto',
                                                                                   videoHeight = 'auto';
                                                                               // video url patterns(youtube, instagram, vimeo, dailymotion, youku, mp4, ogg, webm)
                                                                               var ytRegExp = /\/\/(?:(?:www|m)\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w|-]{11})(?:(?:[\?&]t=)(\S+))?$/;
                                                                               var ytRegExpForStart = /^(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?$/;
                                                                               var ytMatch = url.match(ytRegExp);
                                                                               var igRegExp = /(?:www\.|\/\/)instagram\.com\/p\/(.[a-zA-Z0-9_-]*)/;
                                                                               var igMatch = url.match(igRegExp);
                                                                               var vRegExp = /\/\/vine\.co\/v\/([a-zA-Z0-9]+)/;
                                                                               var vMatch = url.match(vRegExp);
                                                                               var vimRegExp = /\/\/(player\.)?vimeo\.com\/([a-z]*\/)*(\d+)[?]?.*/;
                                                                               var vimMatch = url.match(vimRegExp);
                                                                               var dmRegExp = /.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/;
                                                                               var dmMatch = url.match(dmRegExp);
                                                                               var youkuRegExp = /\/\/v\.youku\.com\/v_show\/id_(\w+)=*\.html/;
                                                                               var youkuMatch = url.match(youkuRegExp);
                                                                               var qqRegExp = /\/\/v\.qq\.com.*?vid=(.+)/;
                                                                               var qqMatch = url.match(qqRegExp);
                                                                               var qqRegExp2 = /\/\/v\.qq\.com\/x?\/?(page|cover).*?\/([^\/]+)\.html\??.*/;
                                                                               var qqMatch2 = url.match(qqRegExp2);
                                                                               var mp4RegExp = /^.+.(mp4|m4v)$/;
                                                                               var mp4Match = url.match(mp4RegExp);
                                                                               var oggRegExp = /^.+.(ogg|ogv)$/;
                                                                               var oggMatch = url.match(oggRegExp);
                                                                               var webmRegExp = /^.+.(webm)$/;
                                                                               var webmMatch = url.match(webmRegExp);
                                                                               var fbRegExp = /(?:www\.|\/\/)facebook\.com\/([^\/]+)\/videos\/([0-9]+)/;
                                                                               var fbMatch = url.match(fbRegExp);
                                                                               var $video;
                                                                               var urlVars    = '';
                                                                               if ( ytMatch && ytMatch[ 1 ].length === 11 )
                                                                               {
                                                                                   var youtubeId = ytMatch[ 1 ];
                                                                                   var start = 0;

                                                                                   if (typeof ytMatch[2] !== 'undefined') {
                                                                                       var ytMatchForStart = ytMatch[2].match(ytRegExpForStart);

                                                                                       if (ytMatchForStart) {
                                                                                           for (var n = [3600, 60, 1], i = 0, r = n.length; i < r; i++) {
                                                                                               start += typeof ytMatchForStart[i + 1] !== 'undefined' ? n[i] * parseInt(ytMatchForStart[i + 1], 10) : 0;
                                                                                           }
                                                                                       }
                                                                                   }

                                                                                   $video        = $( '<iframe>' )
                                                                                       .attr( 'frameborder', 0 )
                                                                                       .attr( 'src', '//www.youtube-nocookie.com/embed/' + youtubeId + (start > 0 ? '?start=' + start : '') )
                                                                                       .attr( 'width', videoWidth )
                                                                                       .attr( 'height', videoHeight );
                                                                               }
                                                                               else if ( igMatch && igMatch[ 0 ].length )
                                                                               {
                                                                                   $video = $( '<iframe>' )
                                                                                       .attr( 'frameborder', 0 )
                                                                                       .attr( 'src', 'https://instagram.com/p/' + igMatch[ 1 ] + '/embed/' )
                                                                                       .attr( 'width', videoWidth )
                                                                                       .attr( 'height', videoHeight )
                                                                                       .attr( 'scrolling', 'no' )
                                                                                       .attr( 'allowtransparency', 'true' );
                                                                               }
                                                                               else if ( vMatch && vMatch[ 0 ].length )
                                                                               {
                                                                                   $video = $( '<iframe>' )
                                                                                       .attr( 'frameborder', 0 )
                                                                                       .attr( 'src', vMatch[ 0 ] + '/embed/simple' )
                                                                                       .attr( 'width', videoWidth )
                                                                                       .attr( 'height', videoHeight )
                                                                                       .attr( 'class', 'vine-embed' );
                                                                               }
                                                                               else if ( vimMatch && vimMatch[ 3 ].length )
                                                                               {
                                                                                   $video = $( '<iframe webkitallowfullscreen mozallowfullscreen allowfullscreen>' )
                                                                                       .attr( 'frameborder', 0 )
                                                                                       .attr( 'src', '//player.vimeo.com/video/' + vimMatch[ 3 ] )
                                                                                       .attr( 'width', videoWidth )
                                                                                       .attr( 'height', videoHeight );
                                                                               }
                                                                               else if ( dmMatch && dmMatch[ 2 ].length )
                                                                               {
                                                                                   $video = $( '<iframe>' )
                                                                                       .attr( 'frameborder', 0 )
                                                                                       .attr( 'src', '//www.dailymotion.com/embed/video/' + dmMatch[ 2 ] )
                                                                                       .attr( 'width', videoWidth )
                                                                                       .attr( 'height', videoHeight );
                                                                               }
                                                                               else if ( youkuMatch && youkuMatch[ 1 ].length )
                                                                               {
                                                                                   $video = $( '<iframe webkitallowfullscreen mozallowfullscreen allowfullscreen>' )
                                                                                       .attr( 'frameborder', 0 )
                                                                                       .attr( 'width', videoWidth )
                                                                                       .attr( 'height', videoHeight )
                                                                                       .attr( 'src', '//player.youku.com/embed/' + youkuMatch[ 1 ] );
                                                                               }
                                                                               else if (qqMatch && qqMatch[1].length || qqMatch2 && qqMatch2[2].length)
                                                                               {
                                                                                   var vid = qqMatch && qqMatch[1].length ? qqMatch[1] : qqMatch2[2];
                                                                                   $video = $( '<iframe webkitallowfullscreen mozallowfullscreen allowfullscreen>' )
                                                                                       .attr( 'frameborder', 0 )
                                                                                       .attr( 'width', videoWidth )
                                                                                       .attr( 'height', videoHeight )
                                                                                       .attr( 'src', '//v.qq.com/txp/iframe/player.html?vid=' + vid + '&amp;auto=0' );
                                                                               }
                                                                               else if ( mp4Match || oggMatch || webmMatch )
                                                                               {
                                                                                   $video = $( '<video controls>' )
                                                                                       .attr( 'src', url )
                                                                                       .attr( 'width', videoWidth )
                                                                                       .attr( 'height', videoHeight );
                                                                               }
                                                                               else if (fbMatch && fbMatch[0].length)
                                                                               {
                                                                                   $video = $( '<iframe>' )
                                                                                       .attr( 'frameborder', 0 )
                                                                                       .attr( 'src', 'https://www.facebook.com/plugins/video.php?href=' + encodeURIComponent( fbMatch[ 0 ] ) + '&show_text=0' )
                                                                                       .attr( 'width', videoWidth )
                                                                                       .attr( 'height', videoHeight )
                                                                                       .attr( 'scrolling', 'no' )
                                                                                       .attr( 'allowtransparency', 'true' );
                                                                               }
                                                                               else
                                                                               {
                                                                                   return false;
                                                                               }

                                                                               $video.addClass( 'embed-responsive' );
                                                                               $video.addClass( 'note-video-clip' );
                                                                               $videoHTML.html( $video );
                                                                               context.invoke( 'restoreRange' );
                                                                               context.invoke( 'editor.insertNode', $videoHTML[ 0 ] );
                                                                           } );
                                  };
                                  this.showLinkDialog = function ( vidInfo )
                                  {
                                      return $.Deferred( function ( deferred )
                                                         {
                                                             var $videoHref = self.$dialog.find( '.note-video-responsive-href' );
                                                             $editBtn       = self.$dialog.find( '.note-video-responsive-btn' );
                                                             ui.onDialogShown( self.$dialog, function ()
                                                             {
                                                                 context.triggerEvent( 'dialog.shown' );
                                                                 $editBtn.click( function ( e )
                                                                                 {
                                                                                     e.preventDefault();
                                                                                     deferred.resolve( {
                                                                                                           vidDom: vidInfo.vidDom,
                                                                                                           href:   $videoHref.val()
                                                                                                       } );
                                                                                 } );
                                                                 $videoHref.val( vidInfo.href ).focus;
                                                                 self.bindEnterKey( $editBtn );
                                                                 self.bindLabels();
                                                             } );
                                                             ui.onDialogHidden( self.$dialog, function ()
                                                             {
                                                                 $editBtn.off( 'click' );
                                                                 if ( deferred.state() === 'pending' )
                                                                 {
                                                                     deferred.reject();
                                                                 }
                                                             } );
                                                             ui.showDialog( self.$dialog );
                                                         } );
                                  };
                              }
       } );
   } ));
