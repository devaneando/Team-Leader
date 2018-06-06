<?php

namespace AppBundle\Controller;

use AppBundle\Service\OrderService;

use AppBundle\Traits\OrderServiceTrait;
use AppBundle\Traits\Processor\CategoryFreebiePromotionTrait;
use AppBundle\Traits\Processor\CheapestDiscountPromotionTrait;
use AppBundle\Traits\Processor\OrderPromotionTrait;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(service="app.controller.default")
 */
class DefaultController extends FOSRestController
{
    use OrderServiceTrait;
    use CategoryFreebiePromotionTrait;
    use CheapestDiscountPromotionTrait;
    use OrderPromotionTrait;

    /**
     * @Route("/process/order", name="process_order")
     * @Method({"POST"})
     *
     * @param Request $request
     */
    public function processOrderAction(Request $request)
    {
        $content = $request->getContent();
        if (!$content) {
            return new JsonResponse([], 500);
        }

        $order = $this->getOrderService()->jsonToOrder($request->getContent());

        $this->getOrderPromotion()->setOrder($order);
        $this->getOrderPromotion()->process();
        $order = $this->getOrderPromotion()->getOrder();

        $this->getCategoryFreebiePromotion()->setOrder($order);
        $this->getCategoryFreebiePromotion()->process();
        $order = $this->getCategoryFreebiePromotion()->getOrder();

        $this->getCheapestDiscountPromotion()->setOrder($order);
        $this->getCheapestDiscountPromotion()->process();
        $order = $this->getCheapestDiscountPromotion()->getOrder();

        die(var_dump($this->getOrderService()->orderToJson($order)));
        // // replace this example code with whatever you need
        // return new JsonResponse([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
    }
}
