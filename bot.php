<?php

require 'config/settings.php';
require 'config/text.php';
require 'TelegramApi/API.php';

use TelegramApi\Api;

$bot = new Api(TOKEN);
$bot->setTokenPay(TOKEN_TRANZZO_PROD);

/**
 * Необхідний для реєстрації webhook один раз
 * https://api.telegram.org/bot[token]/setWebhook?url=https://вашсайт/telebot.php
 */
/*
$response = $bot->setWebhook('https://tychina.kiev.ua/API/IKTestPaymenBot/bot.php');
if ($response->ok) {
    echo "Webhook has been set!";
} else {
    echo "Error setting webhook: " . $response->description;
}*/

date_default_timezone_set('Europe/Kyiv');

$result = $bot->getUpdates();

/**
 * Якщо звертається БОТ припиняємо роботу скрипта
 */
if ($result['message']['from']['is_bot'] == true)
    return false;

/**
 * Відгук на /start
 */
if ($result['message']['text']) {
    
    $bot->setLog(LOG_INPUT, date(DATE_RFC822), $result);
    $bot->setChat($result['message']['chat']['id']);
    $bot->setText($result);
    $bot->setMessageId($result);
    $bot->setFirstName($result);
    $bot->setLastName($result);
    $bot->setIsBot($result);
    $bot->setUserName($result);
    
    if ($bot->text == '/start') {
        $bot->sendMessage($bot->chat_id, $text['start'], 'HTML', true, null, null);
        $keyboard = [
            'inline_keyboard' => [
                [
                    [
                        'text' => '1 USD Pay',
                        'callback_data' => '1dollars_InlineButton'
                    ],
                    [
                        'text' => '29 USD Pay',
                        'callback_data' => '29dollars_InlineButton'
                    ],
                    [
                        'text' => '49 USD Pay',
                        'callback_data' => '490dollars_InlineButton'
                    ]
                ]
            ]
        ];
        $text = 'Оберіть пакет обслуговування:';
        $bot->sendMessage($bot->chat_id, $text, null, true, null, $keyboard);
    } else {
        return false;
    }
}

/**
 * Якщо обрано пакет обслуговування
 */
if ($result['callback_query']) {
    $bot->setLog(LOG_CALLBACK, date(DATE_RFC822), $result);

    $bot->setChat($result['callback_query']['message']['chat']['id']);
    $bot->setCallbackQuery($result);

    switch ($bot->callback_query_text) {
        case '1dollars_InlineButton':
            $header = $text['first_service']['title'];
            $content = $text['first_service']['text'];
            $payload = $text['first_service']['payload'];
            $photo_url = DIR_FOTO . $text['first_service']['photo'];
            $photo_width = 150;
            $photo_height = 300;
            break;
        case '29dollars_InlineButton':
            $header = $text['second_service']['title'];
            $content = $text['second_service']['text'];
            $payload = $text['second_service']['payload'];
            $photo_url = DIR_FOTO . $text['second_service']['photo'];
            $photo_width = 150;
            $photo_height = 300;
            break;
        case '490dollars_InlineButton':
            $header = $text['third_service']['title'];
            $content = $text['third_service']['text'];
            $payload = $text['third_service']['payload'];
            $photo_url = DIR_FOTO . $text['third_service']['photo'];
            $photo_width = 150;
            $photo_height = 300;
            break;
        default:
            break;
    }

    $data = [
        'chat_id' => $bot->chat_id,
        'title' => $header,
        'description' => $content,
        'provider_data' => json_encode([
            'desc' => $content
        ]),
        'payload' => 'specialItem-001',
        'provider_token' => $bot->paytoken,
        'start_parameter' => 'trz-invoice-0001',
        'currency' => 'USD',
        'photo_url' => $photo_url,
        'photo_width' => $photo_width,
        'photo_height' => $photo_height,
        'prices' => json_encode(
            [
                array('label' => $payload['label'], 'amount' => $payload['amount'])
            ]
        )
    ];
    $bot->send($data, 'sendInvoice');
}

/**
 * Клієнт натиснув кнопку оплатити
 */
if ($result['pre_checkout_query']) {
    $bot->setLog(LOG_PRE_CHECKOUT, date(DATE_RFC822), $result);
    $bot->setChat($result['pre_checkout_query']['from']['id']);
    $bot->answerPreCheckoutQuery($result['pre_checkout_query']['id']);
    $data = [
        'pre_checkout_query_id' => $bot->preCheckoutQueryId,
        'ok' => true
    ];
    $bot->send($data, 'answerPreCheckoutQuery');
}

/**
 * Запис успішності оплати, оповіщення клієнта (обов'язково протягом 10сек)
 */
if ($result['message']['successful_payment']) {
    $bot->setLog(LOG_PAYMENT, date(DATE_RFC822), $result);
    $bot->setChat($result['message']['chat']['id']);
    $text_send = $text['thank_you'] . ' Опату було виконано на суму - '. $result['message']['successful_payment']['total_amount'] / 100 . '.00 USD';
    $bot->sendMessage($bot->chat_id, $text_send, 'HTML', true, null, null);
}

