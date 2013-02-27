<div class="field">
    <div id="meta_meta_data_show_public_label" class="two columns alpha">
        <label for="meta_meta_data_show_public"><?php echo __('Show metametadata in public view?'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <?php echo get_view()->formCheckbox('meta_meta_data_show_public', true, 
        array('checked'=>(boolean)get_option('meta_meta_data_show_public'))); ?>
        <p class="explanation"><?php echo __('If checked, the metametadata information will be showed in the public section of the website.'); ?></p>
        </div>
        
        
    <div id="meta_metadata_filter_page_content_label" class="two columns alpha">
        <label for="meta_metadata_filter_page_content"><?php echo __('Turn off html checkbox in add item view?'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <?php echo get_view()->formCheckbox('show_html_box_on_forms', true, 
        array('checked'=>(boolean)get_option('show_html_box_on_forms'))); ?>
        <p class="explanation"><?php echo __('If checked, the html input checkbox will disappear in the input forms.'); ?></p>
    </div>
</div>
