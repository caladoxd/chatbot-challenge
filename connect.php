<?php
	$keyHeader = 'x-inbenta-key:nyUl7wzXoKtgoHnd2fB0uRrAv0dDyLC+b4Y6xngpJDY=';
	$accessToken='';
	$chatbotApiUrl='';
	$sessionToken='';
	$sessionId='';
	$question=$_POST['message'];
function connect(){
	global $keyHeader,$accessToken,$chatbotApiUrl,$sessionToken,$sessionId;
	$body = ['secret'=> 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJwcm9qZWN0IjoieW9kYV9jaGF0Ym90X2VuIn0.anf_eerFhoNq6J8b36_qbD4VqngX79-yyBKWih_eA1-HyaMe2skiJXkRNpyWxpjmpySYWzPGncwvlwz5ZRE7eg'];
	$ch = curl_init('https://api.inbenta.io/v1/auth');
	$payload = json_encode($body);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array($keyHeader,'Content-Type:application/json'));
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec($ch);
	curl_close($ch);
	
	$response = json_decode($result);
	$accessToken = $response->accessToken;
	$expiration = $response->expiration;
	$chatbotApiUrl = $response->apis->chatbot.'/v1';
	

	$params=[
		["answers"=> [
			"sideBubbleAttributes"=> [
				"SIDEBUBBLE_TEXT"
			],
			"answerAttributes"=> [
				"ANSWER_TEXT"
			],
			"skipLastCheckQuestion"=> true,
			"maxOptions"=> 4,
			"maxRelatedContents"=> 2
		]],
		"forms"=> [
			"allowUserToAbandonForm"=> true,
			"errorRetries"=> 2
		],
		"tracking"=> [
			"userInfo"=> [
				"browserInfo"=> "browser information",
				"custom"=> "custom user information",
				"inbenta_extra_field1"=> "any value to filter by",
				"inbenta_extra_field2"=> "any value to filter by",
				"inbenta_extra_field3"=> "any value to filter by"
			]
		],
		"lang"=> "es",
		"timezone"=> "America/New_York"
	];
	$ch = curl_init($chatbotApiUrl.'/conversation');
	$payload = json_encode($params);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array($keyHeader,'Authorization:Bearer '.$accessToken));
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec($ch);
	curl_close($ch);
	$response = json_decode($result);
	$sessionToken = $response->sessionToken;
	$sessionId = $response->sessionId;
}

	//if($sessionToken==''){
		connect();
	//}
	$message=['message'=>$question];
	$ch = curl_init($chatbotApiUrl.'/conversation/message');
	$payload = json_encode($message);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array($keyHeader,'Authorization:Bearer '.$accessToken,'x-inbenta-session:Bearer '.$sessionToken,'Content-Type:application/json'));
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec($ch);
	curl_close($ch);
	$response = json_decode($result);
	$answer = $response->answers[0]->message;
	if(count($response->answers[0]->flags)>0 && $response->answers[0]->flags[0]=="no-results"){
		echo 'Answer not found.';
	}
	else{echo $answer;}
?>