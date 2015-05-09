<?php
/**
 * @category    Bubble
 * @package     Bubble_Autocomplete
 * @version     1.1.3
 * @copyright   Copyright (c) 2015 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Autocomplete_ProductController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve all products from current store as JSON
     */
    public function jsonAction()
    {
        // simple safety function: skip non ajax requests, XmlHttpRequest is set by jQuery/Scriptaculous/Prototype
        // see: http://stackoverflow.com/questions/10911862/check-if-request-was-sent-by-ajax-or-not
        if (!$this->getRequest()->isXmlHttpRequest()) {
            // return false;
        }

        // $cacheId = 'bubble_autocomplete_' . Mage::app()->getStore()->getId();
        // if (false === ($data = Mage::app()->loadCache($cacheId))) {

        $collection = Mage::getModel('catalog/product')->getCollection();

        $queryString = $this->getRequest()->getParam('query'); // get URL encoded request data

        if (empty($queryString)) {
            // no Query string: return cached product collection
            Mage::dispatchEvent('bubble_autocomplete_product_collection_init', array('collection' => $collection));
        } else {
            // User searches for data which is not already in prefetch, doing a live search..
            Mage::dispatchEvent('bubble_autocomplete_product_collection_livesearch_init',
                array('collection' => $collection, 'queryString' => $queryString));
        }

        if ($collection) {
            $data = json_encode($collection->getData());

            // $lifetime = Mage::helper('bubble_autocomplete')->getCacheLifetime();
            // Mage::app()->saveCache($data, $cacheId, array('block_html'), $lifetime);

            $this->getResponse()
                ->setHeader('Content-Type', 'application/json', true)// overwrite Response Headers to ensure compatibility with Apache/fcgi
                ->setBody($data);
        }

    }
}
