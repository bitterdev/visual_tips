<?php

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\View\View;

/** @var int $fID */
/** @var array $items */
/** @var BlockView $view */

$app = Application::getFacadeApplication();
/** @var FileManager $fileManager */
/** @noinspection PhpUnhandledExceptionInspection */
$fileManager = $app->make(FileManager::class);
/** @var Form $form */
/** @noinspection PhpUnhandledExceptionInspection */
$form = $app->make(Form::class);

/** @noinspection PhpUnhandledExceptionInspection */
View::element("dashboard/help_blocktypes", [], "visual_tips");
?>

<div class="form-group">
    <?php echo $form->label("fID", t('Image')); ?>
    <?php echo $fileManager->image("fID", "fID", t("Please select image..."), $fID); ?>
</div>

<hr>

<a href="javascript:void(0);" id="ccm-add-item" class="btn btn-primary">
    <?php echo t("Add Item"); ?>
</a>

<div id="no-entries-container" class="<?php echo count($items) > 0 ? "d-none" : "" ?>">
    <?php echo t("Currently, you don't have any items yet."); ?>
</div>

<div id="items-container" class="<?php echo count($items) > 0 ? "" : "d-none" ?>"></div>

<script id="item-template" type="text/template">
    <div class="item" data-index="<%=index%>">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="item-<%=index%>-x" class="form-label">
                        <?php echo t("X"); ?>
                    </label>

                    <div class="input-group">
                        <input id="item-<%=index%>-x" name="items[<%=index%>][x]" type="number"
                               class="form-control x-val"
                               value="<%=x%>" min="0" max="100">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    <label for="item-<%=index%>-y" class="form-label">
                        <?php echo t("Y"); ?>
                    </label>

                    <div class="input-group">
                        <input id="item-<%=index%>-y" name="items[<%=index%>][y]" type="number"
                               class="form-control y-val"
                               value="<%=y%>" min="0" max="100">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>

            <div class="col" style="display: flex">
                <a href="javascript:void(0);" class="btn-secondary btn btn-select-coords"
                   style="margin-top: auto; margin-bottom: 1.5rem;"
                   title="<?php echo h(t("Select Coordinates")); ?>">
                    <i class="fas fa-mouse-pointer"></i>
                </a>
            </div>
        </div>

        <div class="form-group">
            <label for="item-<%=index%>-body" class="form-label">
                <?php echo t("Body"); ?>
            </label>

            <input type="text" id="item-<%=index%>-body" name="items[<%=index%>][body]" class="form-control"
                   value="<%=body%>">
        </div>

        <div class="btn-danger btn btn-remove">
            <?php echo t("Delete Item"); ?>
        </div>
    </div>
</script>

<style>
    .item {
        border: 1px solid #eaecef;
        border-radius: 5px;
        padding: 1rem;
        margin-top: 1rem;
    }
</style>

<div id="imageDialog" title="<?php echo h(t("Click on the image")); ?>" style="display: none;"></div>

<script>
    (function ($) {
        $(function () {
            const $itemsContainer = $("#items-container");
            const $noEntriesContainer = $("#no-entries-container");

            function findNextFreeIndex() {
                let items = $itemsContainer.find('.item');
                let indices = [];

                items.each(function () {
                    let index = $(this).data('index');

                    if (index !== undefined) {
                        indices.push(index);
                    }
                });

                indices.sort(function (a, b) {
                    return a - b;
                });

                let nextIndex = 0;

                for (let i = 0; i < indices.length; i++) {
                    if (indices[i] > nextIndex) {
                        break;
                    }

                    nextIndex = indices[i] + 1;
                }

                return nextIndex;
            }

            let addItems = function (items) {
                for (let item of items) {
                    addItem(item);
                }
            }

            let addItem = function (config) {
                $noEntriesContainer.addClass("d-none");
                $itemsContainer.removeClass("d-none");

                let index = findNextFreeIndex();

                let defaults = {
                    index: index
                };

                let $newItem = $(_.template($("#item-template").html())({...defaults, ...config}));

                $newItem.find(".btn-remove").on("click", deleteItem);
                $newItem.find(".btn-select-coords").on("click", selectCoords);

                $itemsContainer.append($newItem);
            }

            let selectCoords = function (e) {
                e.stopPropagation();
                e.preventDefault();

                let $item = $(this).closest(".item");
                let fID = parseInt($(this).closest("form").find("input[name=fID]").val());

                if (fID > 0) {
                    $.getJSON(CCM_DISPATCHER_FILENAME + "/visual_tips/api/1.0/get_file_info/" + fID, function (r) {
                        let imageUrl = r.url;

                        const $dialog = $("#imageDialog");

                        // Set image
                        $dialog.html(`<img id="dialogImage" src="${imageUrl}" style="width: 100%">`);

                        // Open dialog
                        $dialog.dialog({
                            modal: true,
                            width: 850,
                            close: function () {
                                $dialog.empty(); // Clean up image after closing
                            }
                        });

                        // Handle click on image
                        $dialog.off("click").on("click", "#dialogImage", function (e) {
                            const imgOffset = $(this).offset();
                            const x = e.pageX - imgOffset.left;
                            const y = e.pageY - imgOffset.top;

                            const imgWidth = $(this).width();
                            const imgHeight = $(this).height();

                            const percentX = ((x / imgWidth) * 100).toFixed(0);
                            const percentY = ((y / imgHeight) * 100).toFixed(0);

                            $item.find(".x-val").val(percentX);
                            $item.find(".y-val").val(percentY);

                            // Close dialog
                            $dialog.dialog("close");
                        });

                        $dialog.dialog("show");
                    });
                } else {
                    alert(<?php echo json_encode(t("You need to select a valid image.")); ?>);
                }

                return false;
            }

            let deleteItem = function (e) {
                e.stopPropagation();
                e.preventDefault();

                let $item = $(this).closest(".item");

                $item.remove();

                if ($itemsContainer.find(".item").length === 0) {
                    $itemsContainer.addClass("d-none");
                    $noEntriesContainer.removeClass("d-none");
                } else {
                    $itemsContainer.removeClass("d-none");
                    $noEntriesContainer.addClass("d-none");
                }

                return false;
            };

            $itemsContainer.find(".btn-remove").on("click", deleteItem);

            $("#ccm-add-item").on("click", function (e) {
                e.stopPropagation();
                e.preventDefault();

                addItem({
                    x: null,
                    y: null,
                    body: null
                });

                return false;
            });

            addItems(<?php echo json_encode($items); ?>);

        });
    })(jQuery);
</script>