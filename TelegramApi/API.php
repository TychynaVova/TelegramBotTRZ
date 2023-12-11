<?php

namespace TelegramApi;

class Api
{
    private $token;
    public $paytoken;
    public $message_id;
    public $chat_id;
    public $text;
    public $username;
    public $first_name;
    public $last_name;
    public $is_bot;
    public $date;
    public $callback_query_text;
    public $preCheckoutQueryId;
    public $currency;
    public $total_amount;
    public $invoice_payload;
    public $order_info;
    public $telegram_payment_charge_id;
    public $provider_payment_charge_id;


    public function __construct($token)
    {
        $this->token = $token;
    }

    public function setTokenPay($token)
    {
        $this->paytoken = $token;
    }

    public function setMessageId($data)
    {
        $this->message_id = $data['message']['message_id'];
    }

    public function setChat($id)
    {
        $this->chat_id = $id;
    }

    public function setText($data)
    {
        $this->text = $data['message']['text'];
    }

    public function setUserName($data)
    {
        $this->username = $data['message']['from']['username'];
    }

    public function setFirstName($data)
    {
        $this->first_name = $data['message']['from']['first_name'];
    }

    public function setLastName($data)
    {
        $this->last_name = $data['message']['from']['last_name'];
    }

    public function setIsBot($data)
    {
        $this->is_bot = $data['message']['from']['is_bot'];
    }

    public function setDatetime($data)
    {
        $this->date = $data['message']['date'];
    }

    public function setCallbackQuery($data)
    {
        $this->callback_query_text = $data['callback_query']['data'];
    }
    public function answerPreCheckoutQuery($data)
    {
        $this->preCheckoutQueryId = $data;
    }

    public function setCurrency($data)
    {
        $this->currency = $data;
    }

    public function setTotalAmount($data)
    {
        $this->total_amount = $data;
    }

    public function setInvoicePayload($data)
    {
        $this->invoice_payload = $data;
    }

    public function setOrderInfo($data)
    {
        $this->order_info = $data;
    }

    public function setTelegramPaymentChargeId($data)
    {
        $this->telegram_payment_charge_id = $data;
    }

    public function setProviderPaymentChargeId($data)
    {
        $this->provider_payment_charge_id = $data;
    }

    public function setLog($file, $date, $data)
    {
        file_put_contents($file, $date . ' - ' . var_export($data, true) . "\n", FILE_APPEND);
    }

    public function setWebhook($url)
    {

        $data = [
            'url' => $url,
        ];

        return $this->send($data, 'setWebhook');
    }

    public function getUpdates()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function sendMessage($chat_id, $text, $parse_mode = null, $disable_web_page_preview = null, $reply_to_message_id = null, $reply_markup = null)
    {
        $data = array(
            'chat_id' => $chat_id,
            'text' => $text,
        );

        if ($parse_mode !== null) {
            $data['parse_mode'] = $parse_mode;
        }

        if ($disable_web_page_preview !== null) {
            $data['disable_web_page_preview'] = $disable_web_page_preview;
        }

        if ($reply_to_message_id !== null) {
            $data['reply_to_message_id'] = $reply_to_message_id;
        }

        if ($reply_markup !== null) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        return $this->send($data, 'sendMessage');
    }

    public function send($data, $command, $header = null)
    {
        $url = "https://api.telegram.org/bot{$this->token}/$command";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if ($header == 'true') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: multipart/form-data',
            ]);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        file_put_contents(LOG_SEND, date(DATE_RFC822) . ' - ' . json_encode($data, true) . ' - ' . $response . "\n", FILE_APPEND);
    }
}
