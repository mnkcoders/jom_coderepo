<?php defined('_JEXEC') or die; ?>
<?php if ($this->has_sidebar) : ?>
    <div id="j-sidebar-container" class="j-sidebar-container j-sidebar-visible">
        <?php echo $this->display_sidebar; ?>
    </div>
<?php endif; ?>
<div id="j-main-container" class="span10 j-toggle-main">
    <h1 class="title"><?php echo $this->get_title; ?></h1>
    <ul class="collection">
            <li class="item">
                <button type="button" class="coders-repo-uploader content icon-upload large-icon">
                    <?php print JText::_( 'Upload' ); ?>
                </button>
            </li>
            <?php foreach ($this->list_resources as $item_id => $resource) : ?>
                <li class="item">
                    <!-- <?php print $item_id ?>  -->
                    <div class="content">
                        <a href="<?php print $resource->url; ?>"
                           target="_blank"
                           class="type <?php print $resource->class_type ?>">
                        <?php if( $resource->is_image ) : ?>
                        <img src="<?php print $resource->url ?>" alt="<?php $resource->name ?>" title="<?php print $resource->name ?>" />
                        <?php endif; ?>
                        <span><?php print $resource->name; ?></span>
                        </a>
                    </div>
                </li>
            <?php endforeach; ?>
    </ul>
</div>
