<?php

namespace Controllers;

use Core\Controller;
use Models\Filter;
use Models\Cart;
use Models\Store;

class CartController extends Controller {

    public function index() {
        $data = array();

        $fil = new Filter();
        $cart = new Cart();
        $store = new Store();

        $filters = array();
        $shipping = array(
            'price' => 0
        );

        if (!isset($_SESSION['cart']) || (isset($_SESSION['cart']) && count($_SESSION['cart']) == 0)):
            header("Location: " . BASE_URL);
            exit;
        endif;

        if (!empty($_POST['zipcode'])):
            $zipCode = intval($_POST['zipcode']);
            $shipping = $cart->shippingCalculate($zipCode);
            $_SESSION['shipping'] = $shipping;
        endif;

        if (!empty($_SESSION['shipping'])):
            $shipping = $_SESSION['shipping'];
        endif;

        $data['filters'] = $fil->getFilters($filters);
        $data['list'] = $cart->getList();
        $data['filters']['sidebar'] = false;

        $data['cart'] = $store->getTemplateData();
        $data['cart']['category_area'] = false;
        $data['shipping'] = $shipping;

        $this->loadTemplate('cart', $data);
    }

    public function add() {
        if (!empty($_POST['product_id'])) {
            $product_id = intval($_POST['product_id']);
            $product_qtty = intval($_POST['product_qtty']);

            if (!isset($_SESSION['cart'])):
                $_SESSION['cart'] = array();
            endif;

            if (isset($_SESSION['cart'][$product_id])):
                $_SESSION['cart'][$product_id] += $product_qtty;
            else:
                $_SESSION['cart'][$product_id] = $product_qtty;
            endif;
        }

        header("Location: " . BASE_URL . "cart");
    }

    public function delete($id) {

        if (!empty($id)):
            unset($_SESSION['cart'][$id]);
        endif;

        header("Location: " . BASE_URL . "cart");
        exit;
    }

    public function payment_redirect() {
        $data = array();

        if (!empty($_POST['payment_type'])) {
            $payment_type = $_POST['payment_type'];

            $data['total_price'] = $_POST['total_price'];

            switch ($payment_type) {
                case 'checkout_transparente':
                    header("Location: " . BASE_URL . "psckttransparente");
                    //$this->loadTemplate('cart_psckttransparent', $data);
                    exit;
                    break;
            }
        }

        header("Location: " . BASE_URL . 'cart');
    }

}
