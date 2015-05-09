<?php
/**
 * Bubble Autocomplete Observer.
 *
 * @category    Bubble
 * @package     Bubble_Autocomplete
 * @version     1.1.3
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Autocomplete_Model_Observer
{
    /*
     * ToDo:
     *  http://stackoverflow.com/questions/9172755/get-view-count-for-magento-product-based-on-product-id
     *  Make Sort Order controllable from Backend (Asc, Desc)
     */

    /**
     * Attached to: bubble_autocomplete_product_collection_init
     *
     * This is the default collection initialization.
     * Feel free to add some fields by observing the event too or to disable this
     * one and add your custom logic.
     *
     * @link http://www.bubblecode.net/en/2012/01/21/disable-an-event-observer-defined-by-default-in-magento/
     *
     * @param Varien_Event_Observer $observer
     */
    public function onProductCollectionInit(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = $observer->getEvent()->getCollection();

        $storeId = Mage::app()->getStore()->getId();

        $collection
            ->addStoreFilter($storeId)

            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED)

            ->addAttributeToFilter('name', array('notnull' => true))
            ->addAttributeToFilter('thumbnail', array('notnull' => true))
            ->addAttributeToFilter('url_path', array('notnull' => true))

            ->addPriceData()
            ->setOrder('name', Varien_Data_Collection::SORT_ORDER_ASC)

            ->getSelect()
            ->limit(Mage::helper('bubble_autocomplete')->getPrefetchLimit()); // limit prefetched db rows

    }

    /**
     * Attached to: bubble_autocomplete_product_collection_livesearch_init
     *
     * This is the default Ajax live search collection initialization.
     * Feel free to add some fields by observing the event too or to disable this
     * one and add your custom logic.
     *
     * @param Varien_Event_Observer $observer
     */
    public function onProductCollectionLiveSearchInit(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = $observer->getEvent()->getCollection();
        $queryString = $observer->getEvent()->getData('queryString');

        $storeId = Mage::app()->getStore()->getId();

        $collection
            ->addStoreFilter($storeId)

            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED)

            ->addFieldToFilter('name', array('like' => $queryString.'%')) // $queryString is parameterized

            ->addAttributeToFilter('name', array('notnull' => true))
            ->addAttributeToFilter('thumbnail', array('notnull' => true))
            ->addAttributeToFilter('url_path', array('notnull' => true))

            ->addPriceData()
            ->setOrder('name', Varien_Data_Collection::SORT_ORDER_ASC)

            ->getSelect()
            ->limit(Mage::helper('bubble_autocomplete')->getLimit()); // limit db rows requested by ajax
    }
}
