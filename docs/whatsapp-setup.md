# Automatic WhatsApp order confirmations

The integration is implemented but **disabled by default**. Creating a WhatsApp Business account/Cloud API app and approving the template must be completed with your business account. No real WhatsApp message is sent by the tests.

Use the [Meta WhatsApp Business Platform documentation](https://www.postman.com/meta/whatsapp-business-platform/documentation/wlk6lh4/whatsapp-cloud-api) for account setup and the messages endpoint.

## Configuration

Set these environment variables, or the corresponding fallback values in `includes/config.php`. Never put the access token into browser JavaScript or share it in chat.

```text
WHATSAPP_ENABLED=true
WHATSAPP_ACCESS_TOKEN=<server-side business access token>
WHATSAPP_PHONE_NUMBER_ID=<sender's phone number ID, not its telephone number>
WHATSAPP_ORDER_TEMPLATE=shakti_order_confirmation
WHATSAPP_TEMPLATE_LANGUAGE=en
WHATSAPP_API_VERSION=v24.0
```

Use PHP cURL and mbstring extensions. The API version is configurable so you can use the version supported by your Meta app.

## Template to submit for approval

Create an English **Utility** template named `shakti_order_confirmation`, with six positional body parameters, no dynamic header or button parameters:

```text
Hi {{1}}, your Shakti Bites order {{2}} is confirmed.

Items: {{3}}
Order total and charges: {{4}}
Payment: {{5}}
Delivery address: {{6}}

Thank you for choosing Shakti Bites.
```

Parameter order: customer name; order number; item names, quantities and line totals; final total plus shipping/prepaid discount/COD fee; payment method/status; delivery address. Match the template language code to the approved template exactly.

## Behaviour

- When configured, checkout shows an unchecked WhatsApp opt-in. Messages are sent only to customers who select it.
- COD confirmation is submitted only after the order is saved successfully.
- Online confirmation is submitted only after Razorpay verification and successful order creation, using the paid cart snapshot.
- Order-page refresh does not resend the message. Closing Razorpay does not send an order confirmation.
- API failures do not roll back a saved order. They are logged without tokens, recipient numbers, addresses or response bodies.
- An `accepted` result means Meta accepted the request; it is **not** a delivered/read receipt. Webhook-based delivery tracking, persistent retries and admin message history are not included.
- An initial checkout number alone does not trigger a message: confirmation requires a completed order.

## Activation checks

1. Configure and approve the template with your actual business sender.
2. Add a permitted test recipient in your Meta setup before testing if using a test sender.
3. Place a controlled COD test order with WhatsApp opt-in and verify the saved order and received message.
4. Test online confirmation using Razorpay test mode before activating live payments.
5. Check failure handling with invalid WhatsApp credentials; order creation must remain successful.

Local checks: `C:/xampp/php/php.exe tests/checkout-pricing.php` and `C:/xampp/php/php.exe tests/order-flow.php`. These use no real payment, database order insert or WhatsApp send.
