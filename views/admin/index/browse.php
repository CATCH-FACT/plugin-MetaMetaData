<?php
$head = array('bodyclass' => 'metametadata primary',
              'title' => html_escape(__('MetaMetaData | Browse')),
              'content_class' => 'horizontal-nav');
echo head($head);
?>
<ul id="section-nav" class="navigation">
    <li class="<?php if (isset($_GET['view']) &&  $_GET['view'] == 'list') {echo 'current';} ?>">
        <a href="<?php echo html_escape(url('meta-meta-data/index/browse?view=list')); ?>"><?php echo __('List View'); ?></a>
    </li>
    <li class="<?php if (isset($_GET['view']) && $_GET['view'] == 'more') {echo 'current';} ?>">
        <a href="<?php echo html_escape(url('meta-meta-data/index/browse?view=more')); ?>"><?php echo __('More'); ?></a>
    </li>
</ul>
<?php echo flash(); ?>

<?php if (!has_loop_records('meta_meta_data')): ?>
    <p><?php echo __('There is no metametadata.'); ?></p>
<?php else: ?>
    <?php if (isset($_GET['view']) && $_GET['view'] == 'list'): ?>
        <?php echo $this->partial('index/browse-list.php', array('MetaMetaData' => $meta_meta_datas)); ?>
    <?php else: ?>
        <H2><?php echo __('Metametadata Overview'); ?></H2>
        <?php echo $this->partial('index/browse-list.php', array('MetaMetaData' => $meta_meta_datas)); ?>
    <?php endif; ?>    
<?php endif; ?>
<?php echo foot(); ?>
