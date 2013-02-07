<div class="field">
    <div id="meta_metadata_filter_page_content_label" class="two columns alpha">
        <label for="meta_metadata_filter_page_content"><?php echo __('Show metametadata in public view?'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <?php echo get_view()->formCheckbox('meta_metadata_filter_page_content', true, 
        array('checked'=>(boolean)get_option('meta_metadata_filter_page_content'))); ?>
        <p class="explanation"><?php echo __(
            'If checked, the metametadata information will be showed in the public section of the website.'
        ); ?></p>
    </div>
</div>
