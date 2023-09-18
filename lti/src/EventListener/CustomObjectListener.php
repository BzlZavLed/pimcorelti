<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use App\Services\ShopifyApiServices;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class CustomObjectListener implements EventSubscriberInterface
{
    private $logger;
    private $shopifyApiService;

    public function __construct(LoggerInterface $logger, ShopifyApiServices $shopifyApiService)
    {
        $this->logger = $logger;
        $this->shopifyApiService = $shopifyApiService;
    }
    public static function getSubscribedEvents()
    {
        // Specify the events you want to listen to
        return [
            'pimcore.dataobject.postUpdate' => 'onPostUpdate',
        ];
    }

    public function onPostUpdate(\Pimcore\Event\Model\DataObjectEvent $event)
    {
        $this->logger->info('Data object updated in the event listener.');
        $dataObject = $event->getObject();
        $this->logger->info($dataObject->get('SKU'));
        $validateSku = $this->shopifyApiService->validateSku($dataObject->get('SKU'));
        $data = [
            'product' => [
                'title' => $dataObject->get('name'),
                'body_html' => '<strong>'.$dataObject->get('name').'</strong>',
                'product_type' => $dataObject->get('type_of'),
                'variants' => [
                    [
                    "option1" => "First ".$dataObject->get('name'),
                    "price" => $dataObject->get('price'),
                    "sku" => $dataObject->get('SKU')
                    ]
                ],
                'status' => 'draft',
            ],
        ];
        if(!$validateSku){
            $this->logger->info(json_encode($data));
            $response = $this->shopifyApiService->createShopifyObject($data);
    
            $this->logger->info($response);
        }else{
            $this->logger->info(json_encode($data));
            $response = $this->shopifyApiService->updateShopifyObject($data,$validateSku);
    
            $this->logger->info($response);

        }
     
    }
}
