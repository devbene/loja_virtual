<div class="container">

    <div class="row cart_topbar">
        <div class="col-sm-6"><span class="bag_title">Sacola</span></div>
        <div class="col-sm-3">Quantidade</div>
        <div class="col-sm-3">Preço</div>
    </div>

    <?php $sub_total = 0; ?>
    <?php foreach ($list as $item): ?>
        <?php $sub_total += (floatval($item['price']) * intval($item['qtty'])); ?>
        <div class="row cart_item">
            <div class="col-sm-6">
                <div class="row">
                    <div class="cart_item_img col-sm-4">
                        <img src="<?php echo BASE_URL . 'assets/images/media/products/' . $item['image']; ?>" width="80">
                    </div>

                    <div class="cart_item_info col-sm-8">
                        <div class="row">
                            <div class="col-sm-12 cart_item_description">
                                <p><?php echo $item['description']; ?></p>
                            </div>
                            <div class="col-sm-12 cart_item_shipping">
                                <small>Entregue em até x dias</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-12">
                        <?php echo $item['qtty']; ?>
                    </div>
                    <div class="col-sm-12 delete_btncart_item">
                        <a href="<?php echo BASE_URL . 'cart/delete/' . $item['id']; ?>"><img src="<?php echo BASE_URL . 'assets/images/delete-icon.png'; ?>" width="30"></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-3"><?php echo $item['price']; ?></div>
        </div>
    <?php endforeach; ?>

    <div class="row">
        <div class="col-sm-4 shipping_form_cart">
            Frete para:
            <form method="POST" action="<?php echo BASE_URL . 'cart'; ?>" >
                <div class="">
                    <div class="form-group" >
                        <input type="text" class="form-control" name="zipcode" id="zipcode" required="required">
                    </div>
                </div>

                <div class="">
                    <div class="">
                        <button type="submit" class="btn btn-dark enviar" name="enviar">Calcular</button>
                    </div>
                </div>

            </form>

            <div class="">
                Valor do frete: <?php echo "R$ " . $shipping['price']; ?>
            </div>
        </div>
    </div>

    <div class="row total_amount">
        <div class="col-sm-9 total_amount_text">
            <p>Total: R$ </p>
        </div>
        <div class="col-sm-3 total_amount_bory">
            <div class="col-sm-12">
                <div class="total_price">
                    <?php $frete = floatval(str_replace(',', '.', $shipping['price'])); ?>
                    <?php $total = $sub_total + $frete; ?>
                    <?php echo number_format($total, 2, ',', '.'); ?>
                </div>
            </div>
            <div class="col-sm-12">
                <a href="<?php echo BASE_URL . 'checkout/payment' ?>" class="btn btn-success" id="btn_continue">Continuar</a>
            </div>
        </div>
    </div>
    <hr>
    <form method="POST" action="<?php echo BASE_URL . 'cart/payment_redirect'; ?>">
        <input type="hidden" value="<?php echo $total; ?>" name="total_price">
        <select name="payment_type">
            <option value="">Forma de Pagamento</option>
            <option value="checkout_transparente">Pagseguro Checkout Transparente</option>
        </select>

        <input type="submit" value="Finalizar Compra">
    </form>

</div>