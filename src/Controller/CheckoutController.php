<?php

namespace App\Controller;

use App\Form\OrderType;
use Omnipay\Omnipay;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{

    /**
     * @Route("/checkout/pay/{id}", name="checkout_pay")
     */
    public function pay($id, Request $request)
    {
        $product = $this->getDoctrine()->getRepository('App:Product')->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product is not defined');
        }

        $params = array(
            'cancelUrl' => 'here you should place the url to which the users will be redirected if they cancel the payment',
            'returnUrl' => 'here you should place the url to which the response of PayPal will be proceeded', // in your case             //  you have registered in the routes 'payment_success'
            'amount' => 20,
        );

        $gateway = Omnipay::create('PayPal_Express');
        $gateway->setUsername('daniyar.san-facilitator_api1.gmail.com'); // here you should place the email of the business sandbox account
        $gateway->setPassword('YDNQA8QQZS9T7NZB'); // here will be the password for the account
        $gateway->setSignature('A0a93u2y-cb0FQaNVINWyk80Zg9aAlAwaulyplCutY0Q1bcK-V0Iy86T'); // and the signature for the account
        $gateway->setTestMode(true); // set it to true when you develop and when you go to production to false
        $response = $gateway->purchase($params)->send(); // here you send details to PayPal

        var_dump($response->getMessage());
        exit;
        if ($response->isRedirect()) {
            // redirect to offsite payment gateway
            $response->redirect();
        } else {
            // payment failed: display message to customer
            echo $response->getMessage();
        }


        $order = $this->createForm(OrderType::class);

        $order->handleRequest($request);

        if ($order->isSubmitted() && $order->isValid()) {

            return $this->redirectToRoute('product_list');
        }

        return $this->render('checkout/index.html.twig', [
            'order' => $order->createView(),
            'product' => $product
        ]);
    }

    /**
     * @Route("/checkout/success", name="checkout_success")
     */
    public function success(Request $request)
    {
        var_dump('success');
        exit;
    }

}
