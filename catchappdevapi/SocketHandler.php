<?php


class SocketHandler
{

    function send($message) {
        global $clientSocketArray;
        $messageLength = strlen($message);
        foreach($clientSocketArray as $clientSocket)
        {
            @socket_write($clientSocket,$message,$messageLength);
        }
        return true;
    }

    function unseal($socketData) {
        $length = ord($socketData[1]) & 127;
        if($length == 126) {
            $masks = substr($socketData, 4, 4);
            $data = substr($socketData, 8);
        }
        elseif($length == 127) {
            $masks = substr($socketData, 10, 4);
            $data = substr($socketData, 14);
        }
        else {
            $masks = substr($socketData, 2, 4);
            $data = substr($socketData, 6);
        }
        $socketData = "";
        for ($i = 0; $i < strlen($data); ++$i) {
            $socketData .= $data[$i] ^ $masks[$i%4];
        }
        return $socketData;
    }

    function seal($socketData) {
        $b1 = 0x80 | (0x1 & 0x0f);
        $length = strlen($socketData);

        if($length <= 125)
            $header = pack('CC', $b1, $length);
        elseif($length > 125 && $length < 65536)
            $header = pack('CCn', $b1, 126, $length);
        elseif($length >= 65536)
            $header = pack('CCNN', $b1, 127, $length);
        return $header.$socketData;
    }

    function doHandshake($received_header,$client_socket_resource, $host_name, $port) {
        $headers = array();
        $lines = preg_split("/\r\n/", $received_header);
        foreach($lines as $line)
        {
            $line = chop($line);
            if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
            {
                $headers[$matches[1]] = $matches[2];
            }
        }

        $secKey = $headers['Sec-WebSocket-Key'];
        $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        $buffer  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "WebSocket-Origin: $host_name\r\n" .
            "WebSocket-Location: wss://$host_name:$port\r\n".
//            "WebSocket-Location: ws://$host_name:$port\r\n".
            "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
        socket_write($client_socket_resource,$buffer,strlen($buffer));
    }

    function newConnectionACK($client_ip_address) {
        $message = 'New client ' . $client_ip_address.' joined';
        $messageArray = array('error' => false, 'message'=>$message,'code' =>200);
        $ACK = $this->seal(json_encode($messageArray));
        return $ACK;
    }

    function connectionDisconnectACK($client_ip_address) {
        $message = 'Client 321' . $client_ip_address.' disconnected';
        $messageArray = array('error' => true,'message'=>$message,'code' => 200);
        $ACK = $this->seal(json_encode($messageArray));
        return $ACK;
    }

    function createChatBoxMessage($chat_user,$chat_box_message, $user_id) {
        $message = $chat_user . ": <div class='chat-box-message'>" . $chat_box_message . "</div>";
        $messageArray = array('message'=>$message,'message_type'=>$user_id);
        $chatMessage = $this->seal(json_encode($messageArray));
        return $chatMessage;
    }

    function logStory($story_id, $user_id) {

        if (empty($story_id))
        {
            $messageArray = array('error'=> true, 'message' => 'Please send story_id (required field)', 'code' => 200);
            $chatMessage = $this->seal(json_encode($messageArray));
            return $chatMessage;
        }
        if (empty($user_id))
        {
            $messageArray = array('error'=> true, 'message' => 'Please send user_id (required field)', 'code' => 200);
            $chatMessage = $this->seal(json_encode($messageArray));
            return $chatMessage;
        }

//        $servername = "localhost";
//        $password = "welcome";
//
        $servername = "https://catchapp.iapplabz.co.in";
        $password = "welcome@123";

        $username = "root";
        $dbname = "catchapp";


        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            $status="Connection failed: " . $conn->connect_error;
            $messageArray = array('error'=> true, 'message' => 'SQL Connect Status', 'status' => $status, 'code'=>400);
            $chatMessage = $this->seal(json_encode($messageArray));
            return $chatMessage;
        }
        $sql = "SELECT * FROM users WHERE id=$user_id ";
        $res = mysqli_query($conn, $sql);
        if (mysqli_num_rows($res)==0) {
            $messageArray = array('error'=> true, 'message' => 'No user found with given user_id', 'code' => 401);
            $chatMessage = $this->seal(json_encode($messageArray));
            return $chatMessage;
        }
        $sql = "SELECT * FROM user_stories WHERE id=$story_id";
        $res = mysqli_query($conn, $sql);
        if (mysqli_num_rows($res) == 0) {
            $messageArray = array('error'=> true, 'message' => 'No story found with given story_id', 'code' => 401);
            $chatMessage = $this->seal(json_encode($messageArray));
            return $chatMessage;
        }
        $row = mysqli_fetch_assoc($res);

        if ($row['user_id'] == $user_id)
        {
            $messageArray = array('error'=> true, 'message' => 'Can\'t log as Story is uploaded by same user', 'code' => 401);
            $chatMessage = $this->seal(json_encode($messageArray));
            return $chatMessage;
        }

        $sql = "SELECT * FROM seen_stories WHERE story_id=$story_id AND user_id= $user_id";
        $res = mysqli_query($conn, $sql);
        if (mysqli_num_rows($res) > 0) {
            $status =true;

            $messageArray = array('error'=> false, 'message' => 'Story status already logged.', 'status' => $status,'code'=> 200);
            $chatMessage = $this->seal(json_encode($messageArray));
            return $chatMessage;
        } else {
            $status= false;
            $sql = "INSERT INTO seen_stories (story_id, user_id) VALUES ($story_id, $user_id)";

            if ($conn->query($sql) === TRUE) {
                $status =true;
            }
            $conn->close();
            $messageArray = array('error'=> false,'message' =>'Story status is logged.', 'status' => $status,'code'=> 200);
            $chatMessage = $this->seal(json_encode($messageArray));
            return $chatMessage;
        }
    }
}
