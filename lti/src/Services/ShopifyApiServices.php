<?php

namespace App\Services;

use Psr\Log\LoggerInterface;

class ShopifyApiServices
{

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createShopifyObject($data)
    {
        $this->logger->info('Creating object in shopify');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://pimcorelti.myshopify.com//admin/api/2023-07/products.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $headers = array();
        $headers[] = 'X-Shopify-Access-Token: ' . $_ENV['SHOPIFY_TOKEN_ACCESO'];
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->logger->info('ERROR');
            $this->logger->info($ch);
        }
        curl_close($ch);
        $this->logger->info($result);

        return json_encode($result);
    }

    public function updateShopifyObject($data,$productId){
        $this->logger->info('Updating object in shopify');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://pimcorelti.myshopify.com//admin/api/2023-07/'.$productId.'.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $headers = array();
        $headers[] = 'X-Shopify-Access-Token: ' . $_ENV['SHOPIFY_TOKEN_ACCESO'];
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Accept: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->logger->info('ERROR');
            $this->logger->info($ch);
        }
        $this->logger->info('RESPONSE FROM UPDATE');
        $this->logger->info($result);
        curl_close($ch);
      

        return json_encode($result);
    }
    public function validateSku($sku)
    {
        $this->logger->info('Validating object in shopify');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://pimcorelti.myshopify.com/admin/api/2023-07/products.json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $headers = array();
        $headers[] = 'X-Shopify-Access-Token: ' . $_ENV['SHOPIFY_TOKEN_ACCESO'];
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->logger->info('Error in getting products');
            $this->logger->info($ch);
            return false;
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['products'])) {
            $this->logger->info('There are products');
            foreach ($data['products'] as $product) {
                foreach ($product['variants'] as $variant) {
                    $this->logger->info(print_r($variant,true));
                    $this->logger->info('SKU in shopify');
                    $this->logger->info($variant['sku']);
                    if ($variant['sku'] === $sku) {
                        $this->logger->info('SKU found in shopify');
                        $this->logger->info($product['id']);
                        return $product['id'];
                    }
                }
            }
        }else{
            $this->logger->info('There are no products');
        }
    }
}
