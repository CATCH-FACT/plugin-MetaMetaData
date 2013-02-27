<?php
/**
 * Meta MetaData
 * 
 * @copyright Iwe Muiser for the Meertens Institute
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Filter forms and add metametadata input items
 * 
 * @package Omeka\Plugins\MetaMetaData
 */
class MetaMetaData_Controller_Plugin_MadFilter extends Zend_Controller_Plugin_Abstract
{
    /**
     * All routes that render an item element form, including those requested 
     * via AJAX.
     * 
     * @var array
     */
    protected $_defaultRoutes = array(
        array('module' => 'default', 'controller' => 'items', 
              'actions' => array('add', 'edit', 'change-type')), 
        array('module' => 'default', 'controller' => 'elements', 
              'actions' => array('element-form')), 
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
            
            // Add the filters if the current route is registered.
            // Loop all elements / or specific set of elements
            $allTerms = $db->getTable('Element')->findAll();
#           print_r($allTerms);
            foreach ($allTerms as $allTerm) {
                $elementSet = $db->getTable('ElementSet')->find($allTerm->element_set_id);
#               print $allTerm->name . " + " . $allTerm->element_set_id . " >> " . $elementSet->name;
                if ($elementSet){
                    add_filter(array('ElementInput', 'Item', $elementSet->name, $allTerm->name), array($this, 'filterElementInputMmd'), 15);
                }
#                add_filter(array('ElementInput', 'Item', $elementSet->name, $allTerm->name), array($this, 'input_filter'), 15);
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
     * @param array $components
     * @param array $args
     * @return array
     */
    public function filterElementInputMmd($components, $args)
    {
        $db = get_db();
        
        $element = $args['element'];
        $record = $args['record'];
        $elementName = $element->name;
        $elementId = $element->id;
        $recordId = $record->id;
        $index = $args['index'];
		$mmdManualTypes = array( "Disputed" => "disputed", 
                                "Erroneous" =>"erroneous");
        $mmdBoxOptions = $db->getTable('MetaMetaData')->findMetaMetaDataByItem($args); #retrieve the metadata for this item
        if (!$mmdBoxOptions){
            $mmdBoxOptions["generated"] = 0;
            $mmdBoxOptions["disputed"] = 0;
            $mmdBoxOptions["erroneous"] = 0;
        }
#        $mmdBoxOptions = array( "disputed" => 0 , "generated" => 1, "proofread" =>1, "erroneous" => 0, "confidence" => "0.6");
        $addHtml = "<div class='metametadata' style='background-color:lightgray;'>";
        if ($mmdBoxOptions["generated"] == 1){
            $checked = ($mmdBoxOptions["proofread"] == 1) ? "checked": "";
            $addHtml .= "<span class='deprecated' style='font-size=6; color:red;'>Computer generated: ".$mmdBoxOptions["generated_confidence_value"]."</span> - ";
            $addHtml .= "Proofread: ";
            $addHtml .= "<input type='checkbox' name='metametadata[$elementId][$index][proofread]' id='metametadata-$recordId-$elementName-proofread' $checked><br>";
        }
        foreach($mmdManualTypes as $mmdType => $lowercaseType){
            $checked = ($mmdBoxOptions[$lowercaseType] == 1) ? "checked": "";
            $addHtml .= "<b>$mmdType: </b>";
            $addHtml .= "<input type='checkbox' name='metametadata[$elementId][$index][$lowercaseType]' id='metametadata-$recordId-$elementName-$lowercaseType' $checked>";
            $addHtml .= " &nbsp&nbsp&nbsp ";
        }
        $addHtml .= "</div>";

		$components['input'] =  $components['input'] . $addHtml; 				#here the original input should be put. 
        if ((boolean)get_option('show_html_box_on_forms')) {$components['html_checkbox'] = false;}
        
        return $components;
    }


    /*
    * Example code by Patrick MJ
    */
    function input_filter($components, $args)
    {
        $mmdTypes = array( "disputed", "generated", "proofread", "erroneous");
        $element = $args['element'];
        $record = $args['record'];
        $elementName = $element->name;
        $recordId = $record->id;
        $inputHtml = $components['input'];
        
        $newHtml = $inputHtml . "<p>Bonus field input: <input type='text' name='Item-$recordId-$elementName-additional'/></p>";
        $components['input'] = $newHtml;
        return $components;
    }

}
