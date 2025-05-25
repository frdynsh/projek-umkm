var returnUrl = document.querySelector('#personalDetails').dataset.returnUrl;
var currency = document.querySelector('#personalDetails').dataset.currency;

function confirmGuestOrder(event) {
	event.preventDefault();
	var valid = formValidate();
	if (valid) {
		var itemsArray = [];
		var shippingPrice = $('input[name="transfer"]:checked')
			.closest('label')
			.find('.transfer')
			.text();
		shippingPrice = shippingPrice.replace(/\./g, '').replace(/[^0-9]/g, '');
		shippingPrice = parseInt(shippingPrice, 10) || 0;

		var totalAmt = $('#totalOrderSummary').val();
		totalAmt = totalAmt.replace(/[^\d]/g, ''); // hanya ambil angka

		$('#itemList li').each(function (index) {
			var imagePath = $(this).find('.order-list-img img').attr('src');
			var title = $(this).find('.order-list-details h4').html();
			var quantity = $(this).find('input[name=qty]').val();

			// konversi quantity ke int untuk validasi
			var qtyInt = parseInt(quantity, 10) || 0;

			var itemTotalPrice = $(this).find('.order-list-price').text();
			itemTotalPrice = itemTotalPrice.replace(/[^\d]/g, ''); // hanya angka
			itemTotalPrice = parseInt(itemTotalPrice, 10) || 0;

			var itemPrice = qtyInt > 0 ? itemTotalPrice / qtyInt : 0;

			var arr = title.split('<br>');
			var productName = arr[0];

			itemsArray.push({
				'name': productName,
				'unit_price': itemPrice,
				'quantity': qtyInt
			});
		});

		// cegah kirim jika kosong
		if (itemsArray.length === 0) {
			alert("Tidak ada produk dalam pesanan.");
			return;
		}

		$('#submitOrder').html('Processing...').css('text-align', 'left');
		$('.spinner-icon').show();

		$.ajax({
			contentType: 'application/json',
			url: 'endpoint/ajax/create-order.php',
			type: 'POST',
			data: JSON.stringify({
				items: itemsArray,
				email: document.getElementById('emailCashPayment').value,
				name: document.getElementById('userNameCashPayment').value,
				phone: document.getElementById('phoneCashPayment').value,
				address: document.getElementById('addressCashPayment').value,
				message: document.getElementById('messageCashPayment').value,

				totalAmount: totalAmt,
				shippingTotal: shippingPrice,
				currency: currency
			}),
			success: function (data) {
				if (data != 'error') {
					window.location = returnUrl;
				} else {
					$('#submitOrder').html('Submit');
					$('.spinner-icon').hide();
				}
			}
		});
	}
}

function formValidate() {
	var name = $('#userNameCashPayment').parsley();
	var phone = $('#phoneCashPayment').parsley();
	var email = $('#emailCashPayment').parsley();
	var address = $('#addressCashPayment').parsley();
	var message = $('#messageCashPayment').parsley();
	var terms = $('#cbxCashPayment').parsley();

	if (!name.isValid() || !phone.isValid() || !email.isValid() || !address.isValid() || !message.isValid() || !terms.isValid()) {
		return false;
	}
	return true;
}