var BASE_URL = "http://localhost/new-ecommerce/";

$(function() {
    $('.execute_order').on('click', function() {
        var id = PagSeguroDirectPayment.getSenderHash();

        var name = $('input[name=name]').val();
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
        var y_valid = $('select[name=cartao_ano]').val();

        var parc = $('select[name=parc]').val();

        if ((card_number != '') && (cvv != '') && (m_valid != '') && (m_valid != '')) {

            PagSeguroDirectPayment.createCardToken({
                cardNumber: card_number,
                brand: window.cardBrand,
                cvv: cvv,
                expirationMonth: m_valid,
                expirationYear: y_valid,
                success: function(r) {

                    window.card_token = r.card.token;

                    $.ajax({
                        url: BASE_URL + 'psckttransparente/checkout',
                        type: "POST",
                        data: {
                            id: id,
                            name: name,
                            cpf: cpf,
                            email: email,
                            password: password,
                            zip_code: zip_code,
                            street: street,
                            number: number,
                            complemento: complemento,
                            district: district,
                            city: city,
                            state: state,
                            owner_card: owner_card,
                            card_cpf: card_cpf,
                            card_number: card_number,
                            cvv: cvv,
                            m_valid: m_valid,
                            y_valid: y_valid,
                            token_card: window.card_token,
                            parc: parc
                        },
                        dataType: 'json',
                        success: function(json) {
                            console.log(json);
                        },
                        error: function() {

                        }
                    });
                },
                error: function(r) {
                    console.log(r);
                },
                complete: function(r) {

                }
            });
        }

    });

    $('input[name=cartao_num]').on('keyup', function(e) {
        if ($(this).val().length >= 6) {

            var total_price = $('input[name=total_price]').val();

            PagSeguroDirectPayment.getBrand({
                cardBin: $(this).val(),
                success: function(r) {
                    window.cardBrand = r.brand.name;
                    var cvvLimit = r.brand.cvvSize;

                    $('input[name=cartao_cvv]').attr('maxlength', cvvLimit);

                    PagSeguroDirectPayment.getInstallments({

                        amount: total_price,
                        brand: window.cardBrand,

                        success: function(r) {

                            if (r.error == false) {

                                var parc = r.installments[window.cardBrand];
                                var html = '';

                                for (var i in parc) {

                                    var value = parc[i].quantity + ';' + parc[i].installmentAmount;

                                    //**if (parc[i].interestFree === true) {
                                    //value += 'true';
                                    //} else {
                                    //   value += 'false';
                                    //}

                                    html += '<option value="' + value + '">' + parc[i].quantity + 'x de R$ ' + parc[i].installmentAmount + '</option>';
                                }

                                $('select[name=parc]').html(html);
                            }
                        }

                    });
                },
                error: function(r) {

                },
                complete: function(r) {

                }
            });
        }
    });
});