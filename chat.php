<?php

	if(isset($_POST['nick'])) {
		$messages = array();
		if(file_exists('chat.txt')) {
			$messages = json_decode(file_get_contents('chat.txt'));
		}
		array_push($messages, array('nick' => $_POST['nick'], 'message' => $_POST['message']));

		file_put_contents('chat.txt', json_encode($messages));
	}

	$resp = '';

	if(file_exists('chat.txt')) {
		$messages = json_decode(file_get_contents('chat.txt'));

		for($i = max(0, sizeof($messages) - 17); $i < sizeof($messages); $i++) {
			$message = $messages[$i];
			$resp .= '<p><span class="nick">' . $message->nick . ':</span><span class="message">' . $message->message . '</span></p>';
		}
	}

	print_r($resp);

?>
