<?php

namespace Controllers;

use Core\Controller;
use Models\Store;
use Models\Product;
use Models\User;
use Models\Cart;
use Models\Purchase;

class PsckttransparenteController extends Controller {

    public function index() {
        $data = array();

        $product = new Product();
        $store = new Store();

        $data = $store->getTemplateData();
        $data['filters'] = 0;
        $data['cart'] = 0;
        $data['total_price'] = $this->getTotalPrice();

        try {

            $sessionCode = \PagSeguro\Services\Session::create(
                            \PagSeguro\Configuration\Configure::getAccountCredentials()
            );

            $data['session_code'] = $sessionCode->getResult();
        } catch (Exception $ex) {

        }

        $this->loadTemplate('cart_psckttransparente', $data);
    }

    public function getTotalPrice() {
        $cart = new Cart();

        $list = $cart->getList();
        $total = 0;

        foreach ($list as $item):
            $total += (floatval($item['price']) * intval($item['qtty']));
        endforeach;

        if (!empty($_SESSION['shipping'])) {
            $shipping = $_SESSION['shipping'];

            if (isset($shipping['price'])):
                $frete = floatval(str_replace(',', '.', $shipping['price']));
            else:
                $frete = 0;
            endif;

            $total += $frete;

            return $total;
        }
    }

    public function checkout() {
        /** var name = $('input[name=name]').val();
          var cpf = $('input[name=cpf]').val();
          var email = $('input[name=email]').val();
          var password = $('input[name=password]').val();

          var zip_code = $('input[name=zip_code]').val();
          var street = $('input[name=street]').val();
          var number = $('input[name=number]').val();
          var complemento = $('input[name=complemento]').val();
          var district = $('input[name=district]').val();
          var city = $('input[name=city]').val();
          var state = $('input[name=state]').val();

          var owner_card = $('input[name=owner_card]').val();
          var card_cpf = $('input[name=card_cpf]').val();
          var card_number = $('input[name=cartao_num]').val();
          var cvv = $('input[name=cartao_cvv]').val();
          var m_valid = $('select[name=cartao_mes]').val();
          var y_valid = $('select[name=cartao_ano]').val(); */
        $user = new User();
        $cart = new Cart();
        $purch = new Purchase();

        $id = filter_input(INPUT_POST, $_POST['id']);

        $name = addslashes(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $cpf = addslashes(filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING));
        $email = filter_input(INPUT_POST, 'email');
        $password = addslashes(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
        $zip_code = addslashes(filter_input(INPUT_POST, 'zip_code', FILTER_SANITIZE_STRING));
        $street = addslashes(filter_input(INPUT_POST, 'street', FILTER_SANITIZE_STRING));
        $number = addslashes(filter_input(INPUT_POST, 'number', FILTER_SANITIZE_STRING));
        $complemento = addslashes(filter_input(INPUT_POST, 'complemento', FILTER_SANITIZE_STRING));
        $ditrict = addslashes(filter_input(INPUT_POST, 'district', FILTER_SANITIZE_STRING));
        $city = addslashes(filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING));
        $state = addslashes(filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING));
        $owner_card = addslashes(filter_input(INPUT_POST, 'owner_card', FILTER_SANITIZE_STRING));
        $card_cpf = addslashes(filter_input(INPUT_POST, 'card_cpf', FILTER_SANITIZE_STRING));
        $card_number = addslashes(filter_input(INPUT_POST, 'card_number', FILTER_SANITIZE_STRING));
        $cvv = addslashes(filter_input(INPUT_POST, 'cvv', FILTER_SANITIZE_STRING));
        $cartao_mes = addslashes(filter_input(INPUT_POST, 'm_valid', FILTER_SANITIZE_STRING));
        $cartao_ano = addslashes(filter_input(INPUT_POST, 'y_valid', FILTER_SANITIZE_STRING));
        $token_card = addslashes(filter_input(INPUT_POST, 'token_card', FILTER_SANITIZE_STRING));

        $parc = explode(';', $_POST['parc']);

        //$test = array($name, $cpf, $email, $password, $zip_code, $street, $number, $complemento, $ditrict, $city, $state, $owner_card, $card_cpf, $cvv, $cartao_mes, $cartao_ano, $parc);
        //echo json_encode($parc[1]);
        //exit;
        if ($user->emailExists($email)) {
            $uid = $user->validateUser($email, $password);

            if (empty($uid)) {
                $error = array('error' => true, 'msg' => 'E-mail e/ou senha incorretos');
                echo json_encode($error);
                exit;
            }
        } else {
            $uid = $user->createUser($email, $password);
        }

        if (!empty($uid)) {
            $list = $cart->getList();
            $total = 0;

            foreach ($list as $item):
                $total += (floatval($item['price']) * intval($item['qtty']));
            endforeach;

            if (!empty($_SESSION['shipping'])) {
                $shipping = $_SESSION['shipping'];

                if (isset($shipping['price'])):
                    $frete = floatval(str_replace(',', '.', $shipping['price']));
                else:
                    $frete = 0;
                endif;

                $total += $frete;
            }

            $purchase_id = $purch->createPurchase($uid, $total, PSCKTTRANSPARENTE);

            foreach ($list as $item):
                $purch->addItem($purchase_id, $item['id'], $item['qtty'], $item['price']);
            endforeach;

            $creditCard = new \PagSeguro\Domains\Requests\DirectPayment\CreditCard();
            $creditCard->setReceiverEmail(PAGSEGURO_SELLER);
            $creditCard->setReference($purchase_id);
            $creditCard->setCurrency('BRL');

            foreach ($list as $item) {
                $creditCard->addItems()->withParameters(
                        $item['id'], $item['name'], intval($item['qtty']), floatval($item['price'])
                );
            }

            $creditCard->setSender()->setName($name);
            $creditCard->setSender()->setEmail($email);
            $creditCard->setSender()->setPhone()->withParameters(
                    00, 00000000
            );
            $creditCard->setSender()->setDocument()->withParameters('CPF', $cpf);


            $creditCard->setSender()->setHash($id);
            $creditCard->setSender()->setIp('127.0.0.0');

            $creditCard->setShipping()->setAddress()->withParameters(
                    $street, $number, $ditrict, $zip_code, $city, $state, 'BRA', $complemento
            );

            $creditCard->setBilling()->setAddress()->withParameters(
                    $street, $number, $ditrict, $zip_code, $city, $state, 'BRA', $complemento
            );

            $creditCard->setToken($token_card);

            $creditCard->setInstallment()->withParameters($parc[0], $parc[1], false);
            //$psRequest->addParameter('noInterestInstallmentQuantity', 2);
            //$creditCard->addParameter('noInterestInstallmentQuantity', 10);
            $creditCard->setHolder()->setName($owner_card);
            $creditCard->setHolder()->setDocument()->withParameters('CPF', $card_cpf);
            $creditCard->setMode('DEFAULT');

            try {
                $result = $creditCard->register(
                        \PagSeguro\Configuration\Configure::getAccountCredentials()
                );

                echo json_encode($result);
                exit;
            } catch (Exception $e) {
                echo json_encode(array('error' => true, 'msg' => $e->getMessage()));
                exit;
            }
        }
    }

}
