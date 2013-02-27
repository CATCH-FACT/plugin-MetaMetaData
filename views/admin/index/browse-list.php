<div class="pagination"><?php echo pagination_links(); ?></div>
<table class="full">
    <thead>
        <tr>
            <?php echo browse_sort_links(array(
                __('Record') => 'record_id',
                __('Element') => 'element_id',
                __('Disputed') => 'disputed',
                __('Erroneous') => 'erroneous',
                __('Generated (score)') => 'generated',
                __('Last Modified By') => 'updated'), array('link_tag' => 'th scope="col"', 'list_tag' => ''));
            ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach (loop('meta_meta_datas') as $metaMetaData): ?>
        <tr>
            <td>
                <span class="title">
                    <a href="<?php echo html_escape(url('items/show/' . metadata('meta_meta_datas', 'record_id'))); ?>">
                        <?php echo metadata('meta_meta_datas', 'record_id'); ?>
                    </a>
                </span>
            </td>
            <td>
                <span class="title">
                    <a href="<?php echo html_escape(url('items/edit/' . metadata('meta_meta_datas', 'record_id'))); ?>">
                        <?php echo __(metadata('meta_meta_datas', 'element_name')); ?>
                    </a>
                </span>
            </td>
            <td><?php if(metadata('meta_meta_datas', 'disputed')): ?>
                <?php echo __("<span class='deprecated' style='font-size=6; color:red;'>Yes</span>"); ?>
                <?php else: ?>
                <?php echo __("<span class='deprecated' style='font-size=6; color:lightgreen;'>No</span>"); ?>
                <?php endif; ?>
            </td>           
            <td><?php if(metadata('meta_meta_datas', 'erroneous')): ?>
                <?php echo __("<span class='deprecated' style='font-size=6; color:red;'>Yes</span>"); ?>
                <?php else: ?>
                <?php echo __("<span class='deprecated' style='font-size=6; color:lightgreen;'>No</span>"); ?>
                <?php endif; ?>
            </td>
            <td><?php if(metadata('meta_meta_datas', 'generated') && metadata('meta_meta_datas', 'generated_confidence_value')): ?>
                    <?php if(metadata('meta_meta_datas', 'proofread')): ?>
                        <?php $prcolor = "lightgreen"; ?>
                    <?php else: ?>
                        <?php $prcolor = "red"; ?>
                    <?php endif; ?>
                    <?php echo __("<span class='deprecated' style='font-size=6; color:$prcolor;'>".metadata('meta_meta_datas', 'generated_confidence_value')."</span>"); ?>
                <?php else: ?>
                    <?php echo __('No'); ?>
                <?php endif; ?>
            </td>
            <td><?php if(metadata('meta_meta_datas', 'modified_by_user_id')): ?>
                <?php echo __('<strong>%1$s</strong> on %2$s',
                metadata('meta_meta_datas', 'modified_username'),
                html_escape(format_date(metadata('meta_meta_datas', 'modified'), Zend_Date::DATETIME_SHORT))); ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<div class="pagination"><?php echo pagination_links(); ?></div>