<style type="text/css">
    @import url('[{$oViewConf->getModuleUrl('ddoewysiwyg','out/src/css/font-awesome.min.css')}]');
    @import url('[{$oViewConf->getModuleUrl('ddoewysiwyg','out/src/css/backend.min.css')}]');

    #ddoew {
        position: relative;
    }
</style>

<div class="ddoe-wysiwyg" id="ddoew">
    <div class="ddoe-wysiwyg-editor">
        <textarea [{if $blTextEditorDisabled }]disabled [{/if}]id="editor_[{$sEditorField}]" name="[{$sEditorField}]" style="width: [{if $iEditorWidth}][{$iEditorWidth}][{else}]100%[{/if}]; height: [{if $iEditorheight}][{$iEditorheight}][{else}]300px[{/if}];">[{$sEditorValue}]</textarea>
    </div>
</div>

<script type="text/javascript">
    var _win = top.basefrm;
    var _doc = _win.document;

    if( !_win.isOverlayLoaded )
    {
        _win.editorModuleUrl     = '[{$oViewConf->getModuleUrl('ddoewysiwyg')}]';
        _win.editorControllerUrl = '[{$oViewConf->getSelfLink()|html_entity_decode|cat:'cl=ddoewysiwygmedia_wrapper&overlay=1'}]';
        _win.isOverlayLoaded     = true;

        var loadScript = function( src, cb )
        {
            var headElement   = _doc.getElementsByTagName( 'head' )[ 0 ];
            var scriptElement = _doc.createElement( 'script' );

            scriptElement.setAttribute( 'src', src );

            if( typeof cb === 'function' )
            {
                scriptElement.addEventListener( 'load', function( e ) { cb( null, e ); }, false );
            }

            headElement.appendChild( scriptElement );
        };

        loadScript( '[{$oViewConf->getSelfLink()|html_entity_decode|cat:'cl=ddoewysiwyglangjs'}]', function()
            {
                loadScript( '[{$oViewConf->getModuleUrl('ddoewysiwyg','out/src/js/overlay.min.js')}]' );
            }
        );
    }
</script>

[{oxscript include=$oViewConf->getSelfLink()|cat:'cl=ddoewysiwyglangjs' priority=1}]

<script type="text/javascript" src="[{$oViewConf->getModuleUrl('ddoewysiwyg','out/src/js/jquery.min.js')}]"></script>
<script type="text/javascript" src="[{$oViewConf->getModuleUrl('ddoewysiwyg','out/src/js/bootstrap.min.js')}]"></script>
<script type="text/javascript" src="[{$oViewConf->getModuleUrl('ddoewysiwyg','out/src/js/backend.min.js')}]"></script>

[{if $langabbr == 'de'}]
    <script type="text/javascript" src="[{$oViewConf->getModuleUrl('ddoewysiwyg','out/src/js/lang/summernote-de.min.js')}]"></script>
[{/if}]
