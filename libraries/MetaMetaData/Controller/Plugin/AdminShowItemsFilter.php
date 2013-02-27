<?php
/**
 * Meta MetaData
 * 
 * @copyright Iwe Muiser for the Meertens Institute
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Filter adds mmd to items when present
 * 
 * @package Omeka\Plugins\MetaMetaData
 */
class MetaMetaData_Controller_Plugin_AdminShowItemsFilter extends Zend_Controller_Plugin_Abstract
{
    /**
     * All routes that render an item element form, including those requested 
     * via AJAX.
     * 
     * @var array
     */
    protected $_defaultRoutes = array(
        array('module' => 'default', 'controller' => 'items', 'actions' => array('show')), 
    );
    
    /**
     * Set the filters pre-dispatch only on configured routes.
     * 
     * @param Zend_Controller_Request_Abstract
     */
    public function preDispatch($request)
    {
        $db = get_db();
        
        // Some routes don't have a default module, which resolves to NULL.
        $currentModule = is_null($request->getModuleName()) ? 'default' : $request->getModuleName();
        $currentController = $request->getControllerName();
        $currentAction = $request->getActionName();
        
        // Allow plugins to register routes that contain form inputs rendered by 
        // Omeka_View_Helper_ElementForm::_displayFormInput().
        $routes = apply_filters('meta_meta_data_routes', $this->_defaultRoutes);
        
        // Apply filters to defined routes.
        foreach ($routes as $route) {
            
            // Check registered routed against the current route.
            if ($route['module'] != $currentModule 
             || $route['controller'] != $currentController 
             || !in_array($currentAction, $route['actions']))
            {
                continue;
            }
            $recordId = $this->getCurrentUser();#HOW DO I GET THIS RECORD ID?
#            _log("THIS: " . print_r($request, true));
#            exit();
            // Add the filters if the current route is registered.
            // Loop all elements / or specific set of elements
#            _log("REQUEST: " . get_class($request));
#            _log("THIS: " . get_class($this));
#            $allTerms = $db->getTable('Element')->findAll();
            $mmdsForItem = $db->getTable('MetaMetaData')->findMetaMetaDatasByKeys($recordId);
#            add_filter(array('Display', 'Item', "Dublin Core", "Title"), array($this, 'filterElementShow'), 16);
            foreach ($allTerms as $allTerm) {
#            foreach ($mmdsForItem as $mmdForItem){
                $element = $db->getTable('Element')->find($allTerm->element_id);
                $elementSet = $db->getTable('ElementSet')->find($allTerm->element_set_id);
                add_filter(array('Display', 'Item', $elementSet->name, $allTerm->name), array($this, 'filterElementShow'), 16);
            }
            // Once the filter is applied for one route there is no need to 
            // continue looping the routes.
            break;
        }
    }
    
    public function print_enter($doit){
        print "\n--";
        print_r($doit);
        print "--\n";
    }


    public function print_txt($args){
        
        print "<textarea rows = 6>";
        print_r($args);
        print "</textarea>";
    }    
    
    /**
     * Filter the element input.
     * 
     * @param $components
     * @param array $args
     * @return array
     */
    public function filterElementShow($component, $args)
    {
#        return $component;
#        print_r(array_keys($args));
#        print get_class($args['record']);
        $db = get_db();
        $record = $args['record'];
        $recordId = $record->id;
#        _log("RECORDID: " . print_r($recordId, true));
#        $element = $args['params'];
#        $elementName = $element->name;
#        _log("RECORDID: " . print_r($elementName, true));
#        $elementId = $element->id;
#        $index = $args['index'];
        $mmdsForItem = $db->getTable('MetaMetaData')->findMetaMetaDatasByKeys($recordId);
        $props = false;
        foreach ($mmdsForItem as $mmdForItem){
#            print $mmdForItem->disputed;
            $props = ($mmdForItem->disputed == 1) ? "<span class='deprecated' style='font-size=6; color:red;'>".$component."</span>" : $component;
#            $props = "<span class='deprecated' style='font-size=6; color:green;'>".$component."</span>" ? $mmdForItem->proofread : $component;
#            $props .= " [disputed]" ? $mmdForItem->disputed : "";
#            $props .= " [erroneous]" ? $mmdForItem->erroneous : "";
        }
        return ($props) ? $props : $component;
    }

}
