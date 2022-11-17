<?php
 function PushNotificationToShowAnimation($token,$message,$badge,$randomNotificationType)
	  {

        $sound = 'default';
        $development = true;//make it false if it is not in development mode
        $passphrase='';//your passphrase

        $payload = array();
        $payload['aps'] = array('alert' => $message, 'badge' => intval($badge), 'sound' => $sound, 'NotificationType'=>$randomNotificationType,'mutable-content'=>1);
        $payload = json_encode($payload);

        $apns_url = NULL;
        $apns_cert = NULL;
        $apns_port = 2195;

        if($development)
        {
            $apns_url = 'gateway.sandbox.push.apple.com';
            //$apns_cert = dirname(Yii::app()->request->scriptFile).'/FixitDevPush.pem';

			$apns_cert = dirname(Yii::app()->request->scriptFile).'/Certificates_Push_fixit.pem';
        }
        else
        {
            $apns_url = 'gateway.push.apple.com';
            //$apns_cert = dirname(Yii::app()->request->scriptFile).'/FixitDevPush.pem';
			//$apns_cert = dirname(Yii::app()->request->scriptFile).'/Certificates___.pem';
			$apns_cert = dirname(Yii::app()->request->scriptFile).'/Certificates_Push_fixit.pem';

        }
        $stream_context = stream_context_create();
        stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
        stream_context_set_option($stream_context, 'ssl', 'passphrase', $passphrase);

        $apns = stream_socket_client('ssl://' . $apns_url . ':' . $apns_port, $error, $error_string, 2, STREAM_CLIENT_CONNECT,$stream_context);
		//$token="d280fa71bfb1b59912d0ab1c111ad7c404b14c1a949671c947bda850feeca15c";
        $device_tokens=  str_replace("<","",$token);
        $device_tokens1=  str_replace(">","",$device_tokens);
        $device_tokens2= str_replace(' ', '', $device_tokens1);
		$device_tokens3= str_replace('-', '', $device_tokens2);

        $apns_message = chr(0) . pack('n', 32) . pack('H*', $device_tokens3) . chr(0) . chr(strlen($payload)) . $payload;
        $msg=fwrite($apns, $apns_message);
		if(!$msg){
			echo 'Message not delivered' . PHP_EOL;
			//exit;
		}else{
			//echo 'Message successfully delivered' . PHP_EOL;
			//exit;
		}
        @socket_close($apns);
        @fclose($apns);


	}
    ?>
