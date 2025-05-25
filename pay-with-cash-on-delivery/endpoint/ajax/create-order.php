<?php
session_start();

$content = trim(file_get_contents("php://input"));
$jsondecoded = json_decode($content, true);

if (!empty($jsondecoded)) {
    $shippingTotal = $jsondecoded["shippingTotal"];
    $totalAmount = $jsondecoded["totalAmount"] + $shippingTotal;
    $name = $jsondecoded["name"];
    $email = $jsondecoded["email"];
    $phone = $jsondecoded["phone"];
    $address = $jsondecoded["address"];
    $message = $jsondecoded["message"];
    $currency = $jsondecoded["currency"];

    $itemArrayDecoded = $jsondecoded["items"];
    $customerDetailsArray = array(
        "name" => filter_var($jsondecoded["name"], FILTER_SANITIZE_STRING),
        "email" => filter_var($jsondecoded["email"], FILTER_SANITIZE_EMAIL),
        "phone" => filter_var($jsondecoded["phone"], FILTER_SANITIZE_STRING),
        "address" => filter_var($jsondecoded["address"], FILTER_SANITIZE_STRING),
        "message" => filter_var($jsondecoded["message"], FILTER_SANITIZE_STRING),
        "currency" => $jsondecoded["currency"]
    );

    $_SESSION["foodboard-cart"] = array(
        "items" => $itemArrayDecoded,
        "customerDetails" => $customerDetailsArray,
        "shippingAmount" => $shippingTotal
    );

    // ================================
    // Kirim WhatsApp Invoice via Wablas
    // ================================
    $cart = $_SESSION['foodboard-cart'];
    $items = $cart['items'];
    $buyerName = $cart['customerDetails']['name'];
    $phoneNumber = $cart['customerDetails']['phone'];
    $address = $cart['customerDetails']['address'];
    $buyerNote = $cart['customerDetails']['message'];
    $ongkir = $cart['shippingAmount'];

    // Format nomor telepon ke internasional
    $phoneIntl = preg_replace('/\D/', '', $phoneNumber);
    if (substr($phoneIntl, 0, 2) !== '62') {
        if (substr($phoneIntl, 0, 1) === '0') {
            $phoneIntl = '62' . substr($phoneIntl, 1);
        } else {
            $phoneIntl = '62' . $phoneIntl;
        }
    }

    // Format isi pesan WhatsApp
    $waMessage  = "📦 [PESANAN SEDANG DIPROSES]\n";
    $waMessage .= "Hai $buyerName! Terima kasih telah melakukan pemesanan 🙏\n";
    $waMessage .= "Pesanan kamu saat ini sedang kami proses dengan penuh perhatian 💼✨\n\n";

    $waMessage .= "🧾 INVOICE PEMESANAN\n";
    $waMessage .= "Berikut detail pesanan kamu:\n\n";

    $waMessage .= "👤 Nama Pembeli:\n$buyerName\n\n";
    $waMessage .= "🏠 Alamat Pengiriman:\n$address\n\n";
    $waMessage .= "💬 Pesan dari Pembeli:\n\"$buyerNote\"\n\n";
    $waMessage .= "🛒 Detail Pesanan:\n";

    $subtotal = 0;
    foreach ($items as $item) {
        $productName = $item['name'];
        $qty = $item['quantity'];
        $price = $item['unit_price'];
        $lineTotal = $qty * $price;
        $subtotal += $lineTotal;
        $waMessage .= "$productName x$qty = Rp" . number_format($lineTotal, 0, ',', '.') . "\n";
    }

    $waMessage .= "\n💰 Subtotal:\nRp" . number_format($subtotal, 0, ',', '.') . "\n";
    $waMessage .= "🚚 Ongkos Kirim:\nRp" . number_format($ongkir, 0, ',', '.') . "\n";
    $waMessage .= "🧮 Total Pembayaran:\nRp" . number_format($subtotal + $ongkir, 0, ',', '.') . "\n\n";

    $waMessage .= "🙏 Terima kasih sudah memesan di toko kami!\n";
    $waMessage .= "Kami sangat menghargai kepercayaan kamu 💖\n\n";

    $waMessage .= "📱 Jangan lupa untuk pantau terus sosial media kami ya, karena akan ada banyak promo menarik, info produk baru, dan giveaway seru! 🎉\n";
    $waMessage .= "🔍 IG: @namatoko | TikTok: @namatoko | FB: Nama Toko\n\n";

    $waMessage .= "💬 Bila ada pertanyaan, jangan ragu untuk menghubungi kami. Kami siap membantu kamu sebaik mungkin 🤗\n\n";
    $waMessage .= "🌟 Semoga harimu menyenangkan dan pesanan kamu memuaskan! 🌟";

    // Kirim ke UltraMsg
    $params = array(
        'token' => '67h0ks2kaofqoanl', // Ganti dengan token kamu
        'to' => $phoneIntl,
        'body' => $waMessage
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.ultramsg.com/instance122015/messages/chat",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($params),
        CURLOPT_HTTPHEADER => array(
            "content-type: application/x-www-form-urlencoded"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo $response;
    }
}