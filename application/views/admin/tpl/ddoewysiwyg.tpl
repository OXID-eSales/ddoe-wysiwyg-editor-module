<style type="text/css">
    @import url('[{$oViewConf->getModuleUrl('ddoewysiwyg','out/src/css/font-awesome.min.css')}]');
    @import url('[{$oViewConf->getModuleUrl('ddoewysiwyg','out/src/css/backend.min.css')}]');

    #ddoew {
        position: relative;
    }
</style>

<div class="ddoe-wysiwyg" id="ddoew">
    <div class="ddoe-wysiwyg-editor">
        <textarea id="editor_[{$sEditorField}]" name="[{$sEditorField}]" style="width: [{if $iEditorWidth}][{$iEditorWidth}][{else}]100%[{/if}]; height: [{if $iEditorheight}][{$iEditorheight}][{else}]300px[{/if}];">[{$sEditorValue}]</textarea>
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

        var headElement = _doc.getElementsByTagName( 'head' )[ 0 ];
        var scriptElement = _doc.createElement( 'script' );

        scriptElement.setAttribute( 'src', '[{$oViewConf->getModuleUrl('ddoewysiwyg','out/src/js/overlay.min.js')}]' );
        headElement.appendChild( scriptElement );
    }
</script>

<script type="text/javascript" src="[{$oViewConf->getModuleUrl('ddoewysiwyg','out/src/js/jquery.min.js')}]"></script>
<script type="text/javascript" src="[{$oViewConf->getModuleUrl('ddoewysiwyg','out/src/js/bootstrap.min.js')}]"></script>
<script type="text/javascript" src="[{$oViewConf->getModuleUrl('ddoewysiwyg','out/src/js/backend.min.js')}]"></script>