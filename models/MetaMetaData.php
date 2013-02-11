<?php
/**
 * MetaMetaData
 *
 * @copyright Copyright 2008-2012 Iwe Muiser
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The MetaMetaData page record class.
 *
 * @package MetaMetaData
 */
class MetaMetaData extends Omeka_Record_AbstractRecord
{
    public $record_id;  ###
    public $element_id;   #### These combined are the key
    public $index;      ###

    public $created_by_user_id;
    public $modified_by_user_id;
    public $meta_metadata_type; #not useful (yet?)
    
    public $disputed;                   #############
    public $generated;                              #
    public $generated_confidence_value;             #### values per index
    public $generated_generator_id;                 #
    public $proofread;                              #
    public $erroneous;                  #############

    public $modified;
    public $added;
    
    public function isDisputed()
    {
        return $this->disputed;
    }
    
    public function isGenerated()
    {
        return array($this->generated, $this->generated_confidence_value);
    }
    
    public function isProofread()
    {
        return $this->proofread;
    }

    public function isErroneous()
    {
        return $this->erroneous;
    }
    

    /**
     * Get the modified by user object.
     * 
     * @return Element
     */
    public function getElementId()
    {
        return $this->getTable('Element')->find($this->element_id);
    }
    
    /**
     * Get the modified by user object.
     * 
     * @return User
     */
    public function getModifiedByUser()
    {
        return $this->getTable('User')->find($this->modified_by_user_id);
    }
    
    /**
     * Get the created by user object.
     * 
     * @return User
     */
    public function getCreatedByUser()
    {
        return $this->getTable('User')->find($this->created_by_user_id);
    }
    
    
    /**
     * Validate the form data.
     */
    protected function _validate()
    {        
        if (empty($this->element_id)) {
            $this->addError('element_id', __('The page must be given a element_id.'));
        }  
    }
    
    /**
     * Prepare special variables before saving the form.
     */
    protected function beforeSave($args)
    {
        if ($this->generated_confidence_value == '') {
            $this->generated_confidence_value = "NULL";
        }
        if ($this->created_by_user_id == ""){
            $this->created_by_user_id = current_user()->id;
        }
        $this->modified_by_user_id = current_user()->id; #(so no need for hidden setting in the form)
        $this->modified = date('Y-m-d H:i:s');
    }
    
    protected function afterSave($args)
    {
        _log("metametadata saved!");
        #No aftershave
    }
    
    public function getProperty($property)
    {
        switch($property) {
            case 'created_username':
                return $this->getCreatedByUser()->username;
            case 'modified_username':
                return $this->getModifiedByUser()->username;
            case 'element_name':
                return $this->getElementId()->name;
                
            default:
                return parent::getProperty($property);
        }
    }
}
