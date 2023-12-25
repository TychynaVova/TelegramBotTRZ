Simple telegram bot for payment via Trazzo


## View
[TelegramBot](https://t.me/IKTestPaymentBot)

## License
[MIT](https://choosealicense.com/licenses/mit/)


## Method sendInvoice
```php
$data = [
            'chat_id' => chat id,
            'title' => Product name,
            'description' => Product description,
            'provider_data' => json_encode([
                'desc' => Product description //to correctly transfer the description to the TRZ
            ]),
            'payload' => 'specialItem-001', //Bot-defined invoice payload, 1-128 bytes. This will not be displayed to the user, use for your internal processes
            'provider_token' => Provider token,
            'start_parameter' => 'trz-invoice-0001',
            'currency' => 'USD', //payment currency
            'photo_url' => photo url,
            'photo_width' => photo width,
            'photo_height' => photo height,
            'prices' => json_encode(
                [
                    array('label' => $payload['label'], 'amount' => $payload['amount']) // Array of LabeledPrice(https://core.telegram.org/bots/api#labeledprice)
                ]
            )
        ];

```

## Method answerPreCheckoutQuery
```php
$data = [
    'pre_checkout_query_id' => preCheckoutQueryId, //Unique identifier for the query to be answered
    'ok' => true
];
```

## Method SuccessfulPayment
You need to respond within 10 seconds to the client about the success or failure of payment using the following method sendMessage(https://core.telegram.org/bots/api#sendmessage)
