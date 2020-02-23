<?php
/**
 * @package     CODERS.CodeRepo
 * @subpackage  com_coderepo
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

?>
<div class="container">
    <h1 class="title"><?php echo $this->title; ?></h1>
    <ul class="collection">
        <?php foreach( $this->list_collection as $item ) : ?>
        <li><?php print $item; ?></li>
        <?php endforeach; ?>
    </ul>
</div>



