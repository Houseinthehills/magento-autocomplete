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

        // ToDo: Reimplement caching
        // see: https://gist.github.com/ivanweiler/694c0aaf23deab2a38a9
        // better cache in Collection Data?
        // http://stackoverflow.com/questions/15408003/magento-how-to-cache-a-productcollection

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

            // ToDo: Enhance removing unneeded attributes from json (sadly not possible in getCollection directly without
            // creating a custom db select)

            $arr = array();
            $types = array('grouped', 'configurable', 'bundle');

            foreach ($collection as $product) {
                $tmp = array(
                    'n' => $product['name'],
                    'i' => $product['thumbnail'],
                    'u' => $product['url_path'],
                    'pm' => $product['min_price'],
                    'pf' => $product['final_price'],
                    'p' => $product['price'],
                    't' => in_array($types, $product['type_id']) ? 1 : 0 // 1 if of kind from $types
                );

                array_push($arr, $tmp);
            }

            $data = json_encode($arr);

            // $lifetime = Mage::helper('bubble_autocomplete')->getCacheLifetime();
            // Mage::app()->saveCache($data, $cacheId, array('block_html'), $lifetime);

            $this->getResponse()
                ->setHeader('Content-Type', 'application/json', true)// overwrite Response Headers to ensure compatibility with Apache/fcgi
                ->setBody($data);
        }

    }
}
