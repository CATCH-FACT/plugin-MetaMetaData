<?php
/**
 * MetaMetaData
 *
 * @copyright Copyright 2008-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The MetaMetaData page table class.
 *
 * @package MetaMetaData
 */
class MetaMetaDataTable extends Omeka_Db_Table
{

	/**
     * Returns a mmd for a set of id's
     * @param int $elementId    An item id
     * @param int $recordId    An record id
     * @param int $index        An index id of the record
     * @return MetaMetaData Object A MetaMetaData
     **/
    public function findMetaMetaDataByKeys($recordId, $elementId, $index)
    {
        $db = get_db();
        $alias = $this->getTableAlias();

        // Create a SELECT statement for the Location table
        $select = $db->select()->from(array($alias => $db->MetaMetaData), "$alias.*");
        $select->where("$alias.record_id = ?", $recordId);
        $select->where("$alias.element_id = ?", $elementId);
        $select->where("$alias.index = ?", $index);

        // Get the items
        $metametadata = $this->fetchObjects($select); #$this->fetchObjects($select);
#        _log("POSTINDEX2:" . print_r($metametadata, true));

        return current($metametadata); #ONLY SINGLE VALUES
    }

	/**
     * Returns a mmd for a set of id's
     * @param int $elementId    An item id
     * @param int $recordId    An record id
     * @param int $index        An index id of the record
     * @return MetaMetaData Object A MetaMetaData
     **/
    public function findMetaMetaDatasByKeys($recordId, $elementId = false, $index = false)
    {
        $db = get_db();
        $alias = $this->getTableAlias();

        // Create a SELECT statement for the Location table
        $select = $db->select()->from(array($alias => $db->MetaMetaData), "$alias.*");
        $select->where("$alias.record_id = ?", $recordId);
        if ($elementId){ $select->where("$alias.element_id = ?", $elementId);}
        if ($index){ $select->where("$alias.index = ?", $index);}

        // Get the items
        $metametadata = $this->fetchObjects($select); #$this->fetchObjects($select);
#        _log("POSTINDEX2:" . print_r($metametadata, true));

        return ($metametadata); #ONLY ARRAY
    }

	/**
     * Returns a mmd (or array of mmd) for an item (or array of items)
     * @param array|Item|int $item An item or item id, or an array of items or item ids
     * @param boolean $findOnlyOne Whether or not to return only one location if it exists for the item
     * @return array|Location A location or an array of locations
     **/
    public function findMetaMetaDataByItem($item, $findOnlyOne = true)
    {
        $element = $item['element'];
        $record = $item['record'];
        if (($record instanceof Item) && !$record->exists()) {
            return array();
        } else if (is_array($record) && !count($record)) {
            return array();
        }
        if (($element instanceof Element) && !$element->exists()) {
            return array();
        } else if (is_array($element) && !count($element)) {
            return array();
        }
        if(!is_object($element)){
            return array();
        }
        $elementName = $element->name;
        $elementId = $element->id;
        $recordId = $record->id;
        $index = $item['index'];
        
        $db = get_db();
        $alias = $this->getTableAlias();

        // Create a SELECT statement for the MetaMetaData table
        $select = $db->select()->from(array($alias => $db->MetaMetaData), "$alias.*");
        $select->where("$alias.record_id = ?", $recordId);
        $select->where("$alias.element_id = ?", $elementId);
        $select->where("$alias.index = ?", $index);        
#        print $select;
        
        // Get the items
#        _g("fetching metametadata objects");
        $metametadata = $this->fetchObjects($select);
#        _log("fetched");
#        _log("POSTINDEX:" . print_r($metametadata, true));
        
        // If only a single mmd is request, return the first one found.
        if ($findOnlyOne) {
            return current($metametadata);
        }
        return current($metametadata); #FOR NOW ONLY SINGLE VALUES
    }

    /**
     * Find all metametadata, ordered by $order_by.
     *
     * @return array The pages ordered alphabetically by their slugs
     */
    public function findAllMetaMetaDataOrderBy($order_by)
    {
        $select = $this->getSelect()->order($order_by);
        return $this->fetchObjects($select);
    }
    
    public function findHighScoreMetaMetaData($min_score)
    {
		$select = $this->getSelect()->where('generated_confidence_value >= ?', $min_score);
        return $this->fetchObject($select);
    }

    public function findLowScoreMetaMetaData($max_score)
    {
		$select = $this->getSelect()->where('generated_confidence_value <= ?', $max_score);
        return $this->fetchObject($select);
    }

    public function findByElementId($elementId)
    {
        $select = $this->getSelect()->where('element_id = ?', $elementId);
        return $this->fetchObject($select);
    }

	public function findByElementTextId($elementTextId)
    {
        $select = $this->getSelect()->where('element_text_id = ?', $elementTextId);
        return $this->fetchObject($select);
    }
}
