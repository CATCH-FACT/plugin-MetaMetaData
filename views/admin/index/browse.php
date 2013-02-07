<?php
$head = array('bodyclass' => 'metametadata primary',
              'title' => html_escape(__('MetaMetaData | Browse')),
              'content_class' => 'horizontal-nav');
echo head($head);
?>
<ul id="section-nav" class="navigation">
    <li class="<?php if (isset($_GET['view']) &&  $_GET['view'] != 'hierarchy') {echo 'current';} ?>">
	
        <a href="<?php echo html_escape(url('metametadata/index/browse?view=list')); ?>"><?php echo __('List View'); ?></a>

    </li>
    <li class="<?php if (isset($_GET['view']) && $_GET['view'] == 'hierarchy') {echo 'current';} ?>">
	
        <a href="<?php echo html_escape(url('simple-pages/index/browse?view=hierarchy')); ?>"><?php echo __('Hierarchy View'); ?></a>

    </li>
</ul>
<?php echo flash(); ?>

<a class="add-page button small green" href="<?php echo html_escape(url('metametadata/index/add')); ?>"><?php echo __('Add a Page'); ?></a>
<?php if (!has_loop_records('simple_pages_page')): ?>
    <p><?php echo __('There is no metametadata.'); ?> <a href="<?php echo html_escape(url('metametadata/index/add')); ?>"><?php echo __('Add a page.'); ?></a></p>
<?php else: ?>
    <?php if (isset($_GET['view']) && $_GET['view'] == 'hierarchy'): ?>
        <?php echo $this->partial('index/browse-hierarchy.php', array('MetaMetaData' => $simple_pages_pages)); ?>
    <?php else: ?>
        <?php echo $this->partial('index/browse-list.php', array('MetaMetaData' => $simple_pages_pages)); ?>
    <?php endif; ?>    
<?php endif; ?>
<?php echo foot(); ?>
