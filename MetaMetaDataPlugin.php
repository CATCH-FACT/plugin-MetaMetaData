<?php
/**
 * Meta-Metadata plugin
 *
 * @copyright Copyright 2008-2012 Iwe Muiser for the Meertens Institute / University of Twente
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

define('METAMETADATA_PLUGIN_DIR', dirname(__FILE__));
define('METAMETADATA_HELPERS_DIR', METAMETADATA_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'helpers');

class MetaMetaDataPlugin extends Omeka_Plugin_AbstractPlugin
{
	
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array('install', 
							  'uninstall', 
							'initialize',
							'define_acl', 
							'define_routes',
							'config_form', 
							'config',
                            'public_items_show',
							'admin_items_show_sidebar',
							'admin_items_panel_buttons',
							'after_save_item'
							);

	/**
     * @var array Filters for the plugin.
     */
    protected $_filters = array('admin_navigation_main',
								'search_record_types');

	/**
     * @var array Options and their default values.
     */
    protected $_options = array(
        'meta_meta_data_show_public' => 0,
        'show_html_box_on_forms' => 1,
        'show_in_public_view' => 0,
        'maximum_score_disputed' => '1.0',
		'metametadata_types' => ""
    );
    
    /**
     * Filter the element input.
     * @param array $args
     * @return array
     */
    function filterElement($args)
    {
        return "VALUE TEST";
    }
    
    
    public function hookPublicItemsShow($args){
        if ((boolean)get_option('meta_meta_data_show_public')){
            $this->sidebarShow($args);
        }
    }
    
    
    public function hookAdminItems($args){
        $item = $args['item'];
        $recordId = $item->id;
#        print "RECORDID: " . $recordId;
#        print_pre($args);
#        exit();
        #First we check existing MMD items in the database. If no mmdPost data exists for them, the proofread/disputed/erroneous settings have to be emptied
        $mmdsForItem = $this->_db->getTable('MetaMetaData')->findMetaMetaDatasByKeys($recordId);
        foreach ($mmdsForItem as $mmdForItem){
            $element = $this->_db->getTable('Element')->find($mmdForItem->element_id);
            $elementSet = $this->_db->getTable('ElementSet')->find($element->element_set_id);
#            _log("FILTERED: " . $elementSet->name . " - " . $element->name);
#            print "FILTERED: " . $elementSet->name . " - " . $element->name;
            add_filter(array('Display', 'Item', $elementSet->name, $element->name), 'filterElement');
        }
        add_filter(array('Display', 'Item', 'Dublin Core', 'Title'), array($this, 'filterElement'));
#        add_filter(array('Display', 'Item', 'Dublin Core', 'Title'), 'filterElement');
    }
    
    public function hookAfterSaveItem($args)
    {
        $post = $args['post'];
        $record = $args['record'];
        $recordId = $record->id;

        // If we have automatically generated data, then submit to the db
        //retrieve post data if exist
        $mmdPost = $post['metametadata'];

        #First we check existing MMD items in the database. If no mmdPost data exists for them, the proofread/disputed/erroneous settings have to be emptied
        $mmdsForItem = $this->_db->getTable('MetaMetaData')->findMetaMetaDatasByKeys($recordId);
        foreach ($mmdsForItem as $mmdForItem){
            if (!isset($mmdPost[$mmdForItem->record_id][$mmdForItem->element_id][$mmdForItem->index])){
                #we never remove mmd values in the db!
                $mmdForItem->proofread =  "";
                $mmdForItem->disputed =  "";
                $mmdForItem->erroneous =  "";
                $mmdForItem->save();
            }
        }
        if (!empty($mmdPost)){
            #elements needed setting here: record_id, element_id, index
            #elements taken over if existed before: $generated_confidence_value, generated_generator_id, added
            #LOOP ITEMS (for each index a new MetaMetaData object must be created)
            foreach($mmdPost as $mmdPostElementId => $mmdPostElement){
                foreach($mmdPostElement as $mmdPostIndexId => $mmdPostIndex){
                    $metametadata = $this->_db->getTable('MetaMetaData')->findMetaMetaDataByKeys($recordId, $mmdPostElementId, $mmdPostIndexId);
                    if (!$metametadata) {
                        _log("newMetaMetaDataItemCreated: " . $mmdPostElementId. " - " . $recordId . " - " . $mmdPostIndexId);
                        $metametadata = new MetaMetaData;
                        $metametadata->record_id = $recordId;
                        $metametadata->element_id = $mmdPostElementId;
                        $metametadata->index = $mmdPostIndexId;
                    }
                    $metametadata->proofread = ($mmdPostIndex["proofread"] == "on") ? "1" : "";
                    $metametadata->disputed = ($mmdPostIndex["disputed"] == "on") ? "1" : "";
                    $metametadata->erroneous = ($mmdPostIndex["erroneous"] == "on") ? "1" : "";
                    $metametadata->save();
                }
            }
        // If the form is empty, then we want to delete whatever metametadata is currently stored
        } else {
            _log("mmdPost empty");
        }
    }

    public function hookAdminItemsPanelButtons($args)
    {
        #No longer necessary. Core changes made.
#        echo js_tag('elements-advanced'); #naughty place to drop some javascript to replace elements.js
    }

	/**
 	* Install the plugin.
	*/
	public function hookInstall()
    {
        $db = $this->_db;
		
        $sql_new = "
		CREATE TABLE IF NOT EXISTS $db->MetaMetaData (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `record_id` int(10) unsigned NOT NULL,
            `element_id` int(10) unsigned NOT NULL,
            `index` tinyint(4) unsigned NOT NULL,
            `created_by_user_id` int(10) unsigned NOT NULL,
            `modified_by_user_id` int(10) unsigned NOT NULL,
            `meta_metadata_type` VARCHAR( 127 ) NULL ,
            `disputed` tinyint(4) NULL,
            `proofread` tinyint(4) NULL,
            `erroneous` tinyint(4) NULL,
            `generated` tinyint(4) NULL,
            `generated_confidence_value` FLOAT unsigned NULL ,
            `generated_generator_id` VARCHAR( 127 ) NULL ,
            `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (`id`),
            KEY `record_id` (`record_id`),
            KEY `element_id` (`element_id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $db->query($sql_new);
    }
    
    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        $db = $this->_db;
		$db->query("DROP TABLE IF EXISTS $db->MetaMetaData");  
    }
    
    public function hookInitialize()
    {
	    // Register the select filter controller plugin.
		$front = Zend_Controller_Front::getInstance();
		$front->registerPlugin(new MetaMetaData_Controller_Plugin_MadFilter);
#		$front->registerPlugin(new MetaMetaData_Controller_Plugin_AdminShowItemsFilter); #work on this one later
    }

	public function print_pre($whatever){
		print "<PRE>";
		print ".\n.\n.\n.<br>.<br>.<br>";
		print_r($whatever);
		print "</PRE>";
	}


    public function sidebarShow($args){
        $item = $args['item'];
        $recordId = $item->id;
        #First we check existing MMD items in the database. If no mmdPost data exists for them, the proofread/disputed/erroneous settings have to be emptied
        $mmdsForItem = $this->_db->getTable('MetaMetaData')->findMetaMetaDatasByKeys($recordId);
        echo "<div class='panel'>";
        echo "<h4>" . __("MetaMetaData") . "</h4><br>";
        $html = "";
        if (!empty($mmdsForItem)) {
            foreach ($mmdsForItem as $mmdForItem){
                $element = $this->_db->getTable('Element')->find($mmdForItem->element_id);
                $html .= "<div style='background-color:#DDD;width:100%;float:left;margin-top:5px;'>";
                $html .= "<li><b>" . __($element->name) . ": </b></li>";
                if ($mmdForItem->generated == 1){
                    $color = ($mmdForItem->proofread == 1) ? "green": "red";
                    $html .= "<span class='deprecated' style='font-size=6; color:$color;'>Generated (".$mmdForItem->generated_confidence_value . ")<br>";
                    $html .= ($mmdForItem->generated_generator_id) ? "Tool: " . $mmdForItem->generated_generator_id . "<br>" : "";
                    $html .= "</span>";
                }
                $html .= ($mmdForItem->disputed == 1) ? "<span class='deprecated' style='font-size=6; color:orange;'>Disputed</span><br>" : "";
                $html .= ($mmdForItem->erroneous == 1) ?  "<span class='deprecated' style='font-size=6; color:red;'>Erroneous</span><br>" : "";
                $html .= ($mmdForItem->created_by_user_id) ? "Add: " . $this->_db->getTable('User')->find($mmdForItem->created_by_user_id)->name : "";
                $html .= ($mmdForItem->modified_by_user_id) ? " / Mod: " . $this->_db->getTable('User')->find($mmdForItem->modified_by_user_id)->name . "<br>" : "";
                $html .= "</div>";
            }
            echo $html;
        }
        else{
            print "<span class='deprecated' style='font-size=6; color:green;'>No issues.</span><br>";
        }
        echo "</div>";
    }


	/**admin_items_show_sidebar
	*   ToDo: Add style sheets in stead of style in html code
	**/
    public function hookAdminItemsShowSidebar($args){
        $this->sidebarShow($args);
	}
	
    /**
     * Define the ACL.
     *
     * @param Omeka_Acl
     */
    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $indexResource = new Zend_Acl_Resource('MetaMetaData_Index');
        $acl->add($indexResource);
        $pageResource = new Zend_Acl_Resource('MetaMetaData_Page');
        $acl->add($pageResource);
        $acl->allow(array('super', 'admin'), array('MetaMetaData_Index', 'MetaMetaData_Page'));
        $acl->allow(null, 'MetaMetaData_Page', 'show');
#        $acl->deny(null, 'MetaMetaData_Page', 'show-unpublished');
    }

    /**
     * Display the plugin config form.
     */
    public function hookConfigForm()
    {
        require dirname(__FILE__) . '/config_form.php';
    }

    /**
     * Set the options from the config form input.
     */
    public function hookConfig()
    {
        set_option('meta_meta_data_show_public', (int)(boolean)$_POST['meta_meta_data_show_public']);
        set_option('show_html_box_on_forms', (int)(boolean)$_POST['show_html_box_on_forms']);
    }

	/**
     * Add the Simple Pages link to the admin main navigation.
     * 
     * @param array Navigation array.
     * @return array Filtered navigation array.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('MetaMetaData'),
            'uri' => url('meta-meta-data'),
            'resource' => 'MetaMetaData_Index',
            'privilege' => 'browse'
        );
        return $nav;
    }

    /**
     * Add SimplePagesPage as a searchable type.
     */
    public function filterSearchRecordTypes($recordTypes)
    {
        $recordTypes['MetaMetaDataPage'] = __('MetaMetaData Page');
        return $recordTypes;
    }

    /**
     * Add the routes for accessing simple pages by slug.
     * 
     * @param Zend_Controller_Router_Rewrite $router
     */
    public function hookDefineRoutes($args)
    {
        // Don't add these routes on the admin side to avoid conflicts.
         if (is_admin_theme()) {
            return;
        }
    }
}