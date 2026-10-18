<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/order-confirmation.php';

// Older deployment config files can keep running safely until configured.
foreach (['WHATSAPP_ENABLED'=>'false', 'WHATSAPP_ACCESS_TOKEN'=>'', 'WHATSAPP_PHONE_NUMBER_ID'=>'', 'WHATSAPP_ORDER_TEMPLATE'=>'', 'WHATSAPP_TEMPLATE_LANGUAGE'=>'en', 'WHATSAPP_API_VERSION'=>'v24.0'] as $key => $fallback) {
    if (!defined($key)) {
        $value = getenv($key);
        $value = $value === false ? $fallback : $value;
        define($key, $key === 'WHATSAPP_ENABLED' ? filter_var($value, FILTER_VALIDATE_BOOLEAN) : $value);
    }
}

function shakti_whatsapp_ready(): bool
{
    return WHATSAPP_ENABLED && WHATSAPP_ACCESS_TOKEN !== '' && WHATSAPP_PHONE_NUMBER_ID !== '' && WHATSAPP_ORDER_TEMPLATE !== '';
}

function shakti_whatsapp_payload(array $order): array
{
    $confirmation = shakti_order_confirmation($order);
    if (!preg_match('/^91[6-9][0-9]{9}$/', $confirmation['to'])) throw new InvalidArgumentException('Invalid WhatsApp recipient.');
    $price = $order['pricing'] ?? [];
    $total = $confirmation['total'];
    if ($price) $total .= sprintf('; shipping INR %.2f; prepaid discount INR %.2f; COD fee INR %.2f', $price['shipping'], $price['prepaid_discount'], $price['cod_fee']);
    $values = [$confirmation['customer_name'], $confirmation['order_number'], $confirmation['items'], $total, $confirmation['payment'], $confirmation['address']];
    $parameters = array_map(static function ($value) {
        $text = trim(preg_replace('/\s+/u', ' ', $value));
        if (mb_strlen($text, 'UTF-8') > 1000) $text = mb_substr($text, 0, 997, 'UTF-8') . '...';
        return ['type' => 'text', 'text' => $text];
    }, $values);
    return ['messaging_product' => 'whatsapp', 'to' => $confirmation['to'], 'type' => 'template', 'template' => ['name' => WHATSAPP_ORDER_TEMPLATE, 'language' => ['code' => WHATSAPP_TEMPLATE_LANGUAGE], 'components' => [['type' => 'body', 'parameters' => $parameters]]]];
}

/** Best-effort notification, only after the order transaction has committed. */
function shakti_notify_whatsapp(array $order): array
{
    if (empty($order['billing']['whatsapp_opt_in'])) return ['status' => 'not_requested'];
    if (!shakti_whatsapp_ready()) return ['status' => 'not_configured'];
    if (!preg_match('/^v\d+\.\d+$/', WHATSAPP_API_VERSION) || !ctype_digit(WHATSAPP_PHONE_NUMBER_ID)) return ['status' => 'configuration_error'];
    try {
        $payload = shakti_whatsapp_payload($order);
        $handle = curl_init('https://graph.facebook.com/' . WHATSAPP_API_VERSION . '/' . WHATSAPP_PHONE_NUMBER_ID . '/messages');
        curl_setopt_array($handle, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR), CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . WHATSAPP_ACCESS_TOKEN, 'Content-Type: application/json'], CURLOPT_CONNECTTIMEOUT => 3, CURLOPT_TIMEOUT => 8]);
        $raw = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        $response = $raw === false ? [] : json_decode($raw, true);
        if ($code >= 200 && $code < 300 && !empty($response['messages'][0]['id'])) {
            // API acceptance is not a delivery receipt.
            return ['status' => 'accepted', 'message_id' => $response['messages'][0]['id']];
        }
        error_log('WhatsApp order confirmation failed: HTTP ' . $code . ', code ' . (int) ($response['error']['code'] ?? 0));
        return ['status' => 'failed'];
    } catch (Throwable $error) {
        // Do not log tokens, billing data or gateway response bodies.
        error_log('WhatsApp order confirmation could not be submitted.');
        return ['status' => 'failed'];
    }
}
