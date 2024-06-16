<?php
	define('APP_PATH', '7479822506:AAGjV9AJ3L3kX7eCGzMeO_9a9G-mnb-uD_Q');
	$Manager = '6355327273';
	function dump($what)
	{
		echo '<pre>';
		print_r($what);
		echo '</pre>';
	}
	
	function bot($method, $datas = [])
	{
		$url = 'https://api.telegram.org/bot'.API_KEY.'/'.$method;
		$ch = curl_init();
		curl_setopt_array($ch,[
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query($datas)
			
		]);
		$result = curl_exec($ch);
		curl_close($ch);
		if(!curl_errno($ch)) return json_decode($result);
	}
	dump(bot("getMe",[]));
	$hi_text = "Salom, bot ishlamoqda!";
	dump(bot("sendMessage",[
		"chat_id" => 779822506,
		"text" => $hi_text,
		"parse_mode" => "HTML"
	]));
	function html($text)
	{
		return str_replace(['<', '>'], ['&#60', '&#62'], $text);
	}
	
	$update = json_decode(file_get_contents('php://input'), true);
	file_put_contents('php://input', json_encode($update));
	
	https://api.telegram.org/7479822506:AAGjV9AJ3L3kX7eCGzMeO_9a9G-mnb-uD_Q/setWebHook?url=https://media/isakov/a4f915a4-e59c-471c-b5d3-c477c13dd365/Project/Web_bot/index.php