<?php defined('_JEXEC') or die; ?>
<?php if ($this->has_sidebar) : ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->display_sidebar; ?>
    </div>
<?php endif; ?>
<div id="j-main-container">
    <h1 class="title"><?php echo $this->get_title; ?></h1>
    <ul class="collection container">
        <?php if( intval( $this->get_count ) > 0 ) : ?>
            <?php foreach ($this->list_resources as $item_id => $data) : ?>
                <li class="item">
                    <a href="<?php
                        print '#' . $item_id; ?>" target="_self" class="type <?php
                        print $data['type'] ?>"><?php
                        print $data['title']; ?></a>
                </li>
            <?php endforeach; ?>
        <?php else : ?>
                <li class="empty item centered red">No items</li>
        <?php endif; ?>
    </ul>
</div>
