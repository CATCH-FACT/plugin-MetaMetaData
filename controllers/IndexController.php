<?php
/**
 * Meta-Meta data plugin
 *
 * @copyright Copyright 2008-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Meta-Meta data index controller class.
 *
 * @package MetaMetaData
 */
class MetaMetaData_IndexController extends Omeka_Controller_AbstractActionController
{    
    public function init()
    {
        // Set the model class so this controller can perform some functions, 
        // such as $this->findById()
        $this->_helper->db->setDefaultModelName('MetaMetaData');
    }
    
    public function indexAction()
    {
        // Always go to browse.
        $this->_helper->redirector('browse');
        return;
    }
    
    public function addAction()
    {
        // Create a new metametadata Item.
#        $mmdI = new MetaMetaData;
        
        // Set the created by user ID.
#        $mmdI->created_by_user_id = current_user()->id;
#        $mmdI->$element_id = get_current_record('item')->id; #WORKS?
#        $mmdI->$element_id = ; #WORKS?
#        $mmdI->added = date('Y-m-d H:i:s')
#        $this->view->form = $this->_getForm($page);        
#        $this->_processPageForm($page, 'add');
    }
    
    public function editAction()
    {
        // Get the requested page.
#        $page = $this->_helper->db->findById();
#        $this->view->form = $this->_getForm($page);
#        $this->_processPageForm($page, 'edit');
    }
    
    protected function _getForm($page = null)
    { 
        $formOptions = array('type' => 'simple_pages_page', 'hasPublicPage'=>true);
        if($page && $page->exists()) {
            $formOptions['record'] = $page;
        }
        
        $form = new Omeka_Form_Admin($formOptions);
        $form->addElementToEditGroup('text',
                        'title',
                        array('id'=>'simple-pages-title',
                                'size'  => 40,
                                'value' => metadata($page, 'title'),
                                'label' => 'Title',
                                'description' => 'The title of the page (required).',
                                'required' => true
                        ));
        
        $form->addElementToEditGroup('text',
                        'slug',
                        array('id'=>'simple-pages-slug',
                                'size'  => 40,
                                'value'=> metadata($page, 'slug'),
                                'label' => 'Slug',
                                'description'=>'The URL slug for the page. Automatically created from the title if not entered. Allowed characters: alphanumeric, underscores, dashes, and forward slashes.'
                        ));
        
        $form->addElementToEditGroup('checkbox', 'use_tiny_mce',
                        array('id' => 'simple-pages-use-tiny-mce',
                                'checked'=> metadata($page, 'use_tiny_mce'),
                                'values'=> array(1, 0),
                                'label' => 'Use HTML editor?',
                                'description'=>'This will enable an HTML editor for the simple page text below. <strong>Warning</strong>: if enabled, PHP code will not be evaluated and may be hidden from view. Be sure to remove PHP code that you don\'t want exposed in the HTML source.'
                        ));
         
        $form->addElementToEditGroup('textarea', 'text',
                        array('id'    => 'simple-pages-text',
                                'cols'  => 50,
                                'rows'  => 25,
                                'value' => metadata($page, 'text'),
                                'label' => 'Text',
                                'description' => 'The content for the page (optional). HTML markup is allowed. PHP code is allowed if you are not using the HTML editor.'
                        ));
        
        $parentOptions = simple_pages_get_parent_options($page);
        
        $form->addElementToSaveGroup('select', 'parent_id',
                        array('id' => 'simple-pages-parent-id',
                                'multiOptions' => $parentOptions,
                                'value' => $page->parent_id,
                                'label' => 'Parent',
                                'description' => 'The parent page.'
                        ));
        
        $form->addElementToSaveGroup('text', 'order',
                        array('value' => metadata($page, 'order'),
                                'label' => 'Order',
                                'description' => 'The order of the page relative to the other pages with the same parent.'
        
                        ));
        
        
        $form->addElementToSaveGroup('checkbox', 'is_published',
                        array('id' => 'simple_pages_is_published',
                                'values' => array(1, 0),
                                'checked' => metadata($page, 'is_published'),
                                'label' => 'Publish this page?',
                                'description' => 'Checking this box will make the page public.'
                        ));
        
        return $form;
        
    }
    

    protected function _getDeleteSuccessMessage($record)
    {
        return __('The metametadata Item "%s" has been deleted.', $record->meta_metadata_type);
    }
    
    /**
     * Goes to results page based off value in text input.
     */
    public function paginationAction()
    {
        $pageNumber = (int)$_POST['page'];
        $baseUrl = $this->getRequest()->getBaseUrl().'/items/browse/';
    	$request = Zend_Controller_Front::getInstance()->getRequest(); 
    	$requestArray = $request->getParams();        
        if($currentPage = $this->current) {
            $paginationUrl = $baseUrl.$currentPage;
        } else {
            $paginationUrl = $baseUrl;
        }
    }
}
