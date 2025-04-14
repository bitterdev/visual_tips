<?php

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\File;
use Concrete\Core\Entity\File\File as FileEntity;

/** @var int $fID */
/** @var array $items */

$imageUrl = null;
$imageAltText = null;

$f = File::getByID($fID);

if ($f instanceof FileEntity) {
    $fv = $f->getApprovedVersion();

    if ($fv instanceof Version) {
        $imageUrl = $fv->getURL();
        $imageAltText = $fv->getTitle();
    }
}
?>

<div class="image-container">
    <img src="<?php echo h($imageUrl); ?>" alt="<?php echo h($imageAltText); ?>">

    <?php foreach ($items as $item) { ?>
        <div class="hotspot"
             data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo h($item["body"]); ?>"
             style="top: <?php echo (int)$item["y"] ?? 0 ?>%; left: <?php echo (int)$item["x"] ?? 0 ?>%; position: absolute;">
        </div>
    <?php } ?>
</div>