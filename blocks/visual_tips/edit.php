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

<div id="items-container">
    kommt hier rein
</div>

<script id="item-template" type="text/template">
    <div class="well">
        x, y, body

        <div class="btn-danger btn btn-sm">
            <?php echo t("Delete Item"); ?>
        </div>
    </div>
</script>

<!--suppress JSUnresolvedFunction, JSCheckFunctionSignatures -->
<script type="text/javascript">
    (function ($) {
        let nextInsertId = 0;
        let items = <?php echo json_encode($items);?>;

        let addItem = function (data) {
            let defaults = {
                id: nextInsertId
            };

            let combinedData = {...defaults, ...data};

            let $item = $(_.template($("#item-template").html())(combinedData));

            nextInsertId++;

            $item.find(".btn-danger").click(function () {
                $(this).parent().remove();
            });

            $("#items-container").append($item);
        };

        for (let item of items) {
            addItem(item);
        }

        $("#ccm-add-item").click(function (e) {
            e.preventDefault();
            addItem({
                mediaType: 'image',
                imagefID: null,
                webmfID: null,
                oggfID: null,
                mp4fID: null
            });
            return true;
        });
    })(jQuery);
</script>