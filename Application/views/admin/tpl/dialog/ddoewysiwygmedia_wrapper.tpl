<!DOCTYPE html>
<html>
<head>
    <title>MediaLibrary</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">

    <link href='//fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>

    [{assign var="oViewConf" value=$oView->getViewConfig()}]
    [{assign var="oConf" value=$oView->getConfig()}]

    [{oxstyle include=$oViewConf->getModuleUrl('ddoewysiwyg','out/src/css/bootstrap.min.css')}]
    [{oxstyle include=$oViewConf->getModuleUrl('ddoewysiwyg','out/src/css/font-awesome.min.css')}]
    [{oxstyle include=$oViewConf->getModuleUrl('ddoewysiwyg','out/src/css/medialibrary.min.css')}]

    [{oxstyle}]
</head>
<body class="dd-media-wrapper[{if $smarty.get.overlay}] dd-overlay[{/if}]">

    [{if !$smarty.get.overlay}]
        <nav class="navbar navbar-default dd-navbar">
            <div class="container-fluid">

                <div class="navbar-header">
                    <a class="navbar-brand" href="javascript:void(null);">
                        [{oxmultilang ident="DD_MEDIA_DIALOG"}]
                    </a>
                </div>

                [{if !$smarty.get.popout}]
                    <ul class="nav navbar-nav navbar-right hidden-xs">
                        <li><a href="[{$oViewConf->getSelfLink()}]cl=[{$oViewConf->getActiveClassName()}]&popout=1" target="_blank" class="dd-admin-popout-action"><i class="fa fa-expand"></i></a></li>
                    </ul>
                [{/if}]

            </div>
        </nav>
    [{/if}]

    <div class="dd-content">

        [{include file="dialog/ddoewysiwygmedia.tpl"}]

    </div>

[{oxscript include=$oViewConf->getSelfLink()|cat:'cl=ddoewysiwyglangjs' priority=1}]

[{oxscript include=$oViewConf->getModuleUrl('ddoewysiwyg','out/src/js/jquery.min.js') priority=1}]
[{oxscript include=$oViewConf->getModuleUrl('ddoewysiwyg','out/src/js/bootstrap.min.js') priority=1}]
[{oxscript include=$oViewConf->getModuleUrl('ddoewysiwyg','out/src/js/medialibrary.min.js') priority=1}]

[{assign var="sActionLink" value=$oViewConf->getSelfLink()|html_entity_decode|cat:"overlay=`$smarty.get.overlay`&"}]
[{assign var="sMediaLink" value=$oConf->getCurrentShopUrl(true)|regex_replace:'/([^\/])(\/admin)/':'$1'|regex_replace:'/http(s)?\:/':''|rtrim:'/'|cat:'/out/pictures/ddmedia/'|html_entity_decode}]

[{oxscript add="MediaLibrary.setActionLink('`$sActionLink`');" priority=10}]
[{oxscript add="MediaLibrary.setResourceLink('`$sMediaLink`');" priority=10}]
[{oxscript add="MediaLibrary.init( /image\/.*/i, null );" priority=10}]


[{oxscript}]

</body>
</html>
