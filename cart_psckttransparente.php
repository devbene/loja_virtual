<h1>Checkout Transparente - Pagseguro</h1>

<h3>Dados Pessoais</h3>

<strong>Nome: </strong><br>
<input type="text" name="name" value="Bene Rodrigues"><br><br>

<strong>CPF:</strong><br>
<input type="text" name="cpf" value="05347965401"><br><br>

<strong>E-mail:</strong><br>
<input type="email" name="email" value="c64202423485350823308@sandbox.pagseguro.com.br"><br><br>

<strong>Senha:</strong><br><br>
<input type="password" name="password" value="lpj2rmJnNE7pCn5D"><br><br>

<h3>Informações de Endereço</h3>

<strong>CEP:</strong><br>
<input type="text" name="zip_code" value="13449899"><br><br>

<strong>Rua:</strong><br>
<input type="text" name="street" value="São Pedro"><br><br>

<strong>Número:</strong><br>
<input type="text" name="number" value="130"><br><br>

<strong>Complemento:</strong><br>
<input type="text" name="complemento" value="Ap-8"><br><br>

<strong>Bairro:</strong><br>
<input type="text" name="district" value="Universitario"><br><br>

<strong>Cidade:</strong><br>
<input type="text" name="city" value="Eng Coelho"><br><br>

<strong>Estado:</strong><br>
<input type="text" name="state" value="SP"><br><br>

<h3>Informações de Pagamento</h3>

<strong>Titular do Cartão:</strong><br>
<input type="text" name="owner_card" value="Bene Rodrigues"><br><br>

<strong>CPF do Titular do cartão:</strong><br>
<input type="text" name="card_cpf" value="04158594332"><br><br>

<strong>Número do Cartão:</strong><br>
<input type="text" name="cartao_num"><br><br>

<strong>Código de Segurança:</strong><br>
<input type="text" name="cartao_cvv"><br><br>

<strong>Validade:</strong><br>
<select name="cartao_mes">
    <option>Mês</option>
    <?php for ($i = 1; $i <= 12; $i++): ?>
        <option><?php echo ($i < 10) ? '0' . $i : $i; ?></option>
    <?php endfor; ?>
</select>
<select name="cartao_ano">
    <option>Ano</option>
    <?php $year = intval(date('Y')); ?>
    <?php for ($i = $year; $i < ($year + 20); $i++): ?>
        <option><?php echo $i; ?></option>
    <?php endfor; ?>
</select><br><br>

<strong>Parcelas:</strong><br>
<select name="parc"></select>

<input type="hidden" value="<?php echo $total_price; ?>" name="total_price">

<button class="btn btn-success execute_order">Efetuar Compra</button>

<script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL . 'assets/js/psckttransparente.js'; ?>"></script>
<script type="text/javascript">
    PagSeguroDirectPayment.setSessionId("<?php echo $session_code ?>");
</script>