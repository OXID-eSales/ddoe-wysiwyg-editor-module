<div class="dd-media[{if !$smarty.get.overlay}] dd-media-overview[{/if}]" data-foldername="[{$sFoldername}]" data-folderid="[{$sFolderId}]" data-medialink="[{$sResourceUrl}]">

    <div class="dd-media-drag-helper">
        <span>[{oxmultilang ident="DD_MEDIA_DRAG_INFO"}]</span>
    </div>

    <div role="tabpanel" class="dd-media-tabs">

        <!-- Nav tabs -->
        [{assign var="sActiveTab" value="mediaUpload"}]
        [{if $iFileCount || $sFolderId}]
            [{assign var="sActiveTab" value="mediaList"}]
        [{/if}]
        [{if $sTab}]
            [{assign var="sActiveTab" value=$sTab}]
        [{/if}]
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"[{if $sActiveTab == 'mediaList'}] class="active"[{/if}]>
                <a href="#mediaList" role="tab" data-toggle="tab">[{oxmultilang ident="DD_MEDIA_LIST"}]</a>
            </li>
            <li role="presentation"[{if $sActiveTab == 'mediaUpload'}] class="active"[{/if}]>
                <a href="#mediaUpload" role="tab" data-toggle="tab">[{oxmultilang ident="DD_MEDIA_UPLOAD"}]</a>
            </li>
            <li class="pull-right">
                <form class="dd-media-search-form">
                    <input type="text" class="form-control input-sm" name="msearch" id="mediaSearchField" placeholder="Suche" />
                </form>
            </li>

            [{if $iFileCount}]
                <li class="pull-right">
                    <div class="dd-media-header-info">
                        <span class="dd-media-file-count">[{$iFileCount}]</span>
                        <span>[{oxmultilang ident="DD_MEDIA_FILES_FOUND"}]</span>
                    </div>
                </li>
            [{/if}]
        </ul>

        [{capture name="breadcrumb"}]
            <ol class="breadcrumb">
                <li>[{oxmultilang ident="DD_MEDIA_BREADCRUMB_PATH"}]:</li>

                [{foreach from=$oView->getBreadcrumb() item=oPath}]
                    <li [{if $oPath->active}]class="active"[{/if}]>[{$oPath->name}]</li>
                [{/foreach}]
            </ol>

            <div class="dd-media-list-folder-up">
                <button type="button" class="btn btn-link btn-xs dd-media-folder-up-action">[{oxmultilang ident="DD_MEDIA_LEVEL_UP"}] <i class="fa fa-level-up"></i></button>
            </div>
        [{/capture}]

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane[{if $sActiveTab == 'mediaList'}] active[{/if}]" id="mediaList">

                <div class="dd-media-list[{if !$iFileCount}] empty[{/if}]">

                    <div class="dd-media-list-toolbar">

                        <div class="btn-group btn-group-xs pull-left">
                            <button type="button" class="btn btn-default dd-media-folder-action"><i class="fa fa-folder text-muted"></i> [{oxmultilang ident="DD_MEDIA_NEW_FOLDER"}]</button>
                        </div>

                        <div class="btn-group btn-group-xs pull-right">
                            <button type="button" class="btn btn-default dd-media-rename-action" disabled><i class="fa fa-i-cursor"></i> [{oxmultilang ident="DD_MEDIA_RENAME_FILE_FOLDER_BTN"}]</button>
                            <button type="button" class="btn btn-default dd-media-remove-action" disabled><i class="fa fa-times text-danger"></i> [{oxmultilang ident="DD_MEDIA_DELETE_FILE_FOLDER_BTN"}]</button>
                        </div>

                    </div>

                    <div class="dd-media-list-breadcrumb">
                        [{$smarty.capture.breadcrumb}]
                    </div>

                    <div class="dd-media-list-items">

                        <div class="row">

                            <div class="dd-media-list-empty-info">
                                <div class="col-sm-12">[{oxmultilang ident="DD_MEDIA_EMPTY_LIST"}]</div>
                            </div>

                        <div class="dd-media-dz-helper" style="display: none;">
                                <div class="dd-media-col [{if !$smarty.get.overlay}]col-md-2[{else}]col-md-3[{/if}] col-sm-3 col-xs-4">
                                <a class="dd-media-item" href="javascript:void(null);">
                                    <div class="dd-media-item-preview">
                                        <div class="dd-media-item-centered">
                                            <i class="dd-media-icon dd-media-icon-file fa fa-3x fa-file" style="display: none;"></i>
                                            <i class="dd-media-icon dd-media-icon-folder fa fa-3x fa-folder" style="display: none"></i>
                                            <img class="dd-media-thumb" data-dz-thumbnail />
                                            <div class="dd-media-upload-progress">
                                                <span data-dz-uploadprogress></span>
                                            </div>
                                        </div>
                                        <div class="dd-media-item-label" style="display: none;">
                                            <span data-dz-name></span>
                                            <small class="text-danger" data-dz-errormessage></small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        [{foreach from=$aFiles item="file" name="files"}]

                                <div class="dd-media-col [{if !$smarty.get.overlay}]col-md-2[{else}]col-md-3[{/if}] col-sm-3 col-xs-4">
                                    <a class="dd-media-item[{if !$file.DDTHUMB}] no-thumb[{/if}]" href="javascript:void(null);" data-id="[{$file.OXID}]" data-file="[{$file.DDFILENAME}]" data-filetype="[{$file.DDFILETYPE}]" data-filesize="[{$file.DDFILESIZE}]"[{if $file.DDIMAGESIZE}] data-imagesize="[{$file.DDIMAGESIZE}]"[{/if}][{if $file.DDIMAGESIZE}] data-thumb="[{$file.DDTHUMB}]"[{/if}]>
                                    <div class="dd-media-item-preview">
                                        [{if $file.DDTHUMB}]
                                            <div class="dd-media-item-centered">
                                                <img src="[{$sThumbsUrl}][{$file.DDTHUMB}]" class="dd-media-thumb" />
                                            </div>
                                        [{else}]
                                            <div class="dd-media-item-centered">
                                                    [{if $file.DDFILETYPE == 'directory'}]
                                                        <i class="dd-media-icon dd-media-icon-folder fa fa-3x fa-folder"></i>
                                                    [{else}]
                                                        <i class="dd-media-icon dd-media-icon-file fa fa-3x fa-file"></i>
                                                    [{/if}]
                                                </div>
                                                <div class="dd-media-item-label">
                                                    <span>[{$file.DDFILENAME}]</span>
                                                </div>
                                            [{/if}]
                                        </div>
                                    </a>
                                </div>

                        [{/foreach}]

                        </div>

                    </div>

                    <div class="dd-media-list-details">

                        <div class="clearfix" style="height: 3px;"></div>

                        <form class="form dd-media-details-form" method="post" style="display: none;">

                            <div class="media">
                                <div class="media-left">
                                    <img class="media-object dd-media-details-preview" src="" style="max-width: 50px; max-height: 50px;">
                                    <i class="dd-media-details-preview-icon fa fa-file fa-3x text-muted"></i>
                                    <i class="dd-media-details-dir-icon fa fa-folder fa-3x text-muted"></i>
                                </div>
                                <div class="media-body">
                                    <strong class="media-heading dd-media-details-name"></strong>
                                    <div class="clearfix"></div>
                                    <small class="dd-media-details-infos"></small>
                                </div>
                            </div>

                            <div class="clearfix" style="height: 30px;"></div>

                            <div class="form-group dd-media-url">
                                <label>[{oxmultilang ident="DD_MEDIA_IMG_URL"}]</label>
                                <div class="input-group">
                                    <input type="text" class="form-control dd-media-details-input-url" name="media[url]" value="" readonly="readonly" />
                                    <span class="input-group-btn">
                                        <a class="btn btn-default dd-media-details-link-url" href="#" target="_blank">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    </span>
                                </div>
                            </div>

                        </form>

                    </div>

                </div>

            </div>
            <div role="tabpanel" class="tab-pane[{if $sActiveTab == 'mediaUpload'}] active[{/if}]" id="mediaUpload">

                <div class="dd-media-upload-breadcrumb">
                    [{$smarty.capture.breadcrumb}]
                </div>

                <div class="dd-media-upload">

                    <div class="dd-media-upload-info">
                        [{oxmultilang ident="DD_MEDIA_UPLOAD_INFO"}]
                    </div>

                    <div class="dd-media-upload-fallback" style="display: none;">
                        <span>[{oxmultilang ident="DD_MEDIA_FALLBACK_INFO"}]</span>
                        <br />
                        <iframe src="[{$oViewConf->getSelfLink()}]cl=ddoewysiwygmedia_view&fnc=fallback" frameborder="0" scrolling="no"></iframe>
                    </div>

                </div>

            </div>
        </div>

    </div>



</div>
