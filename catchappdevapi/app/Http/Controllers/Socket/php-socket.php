<?php

use catchapp\Http\Controllers\Socket\SocketController;
use catchapp\Http\Controllers\Socket\SocketHandler;

define('HOST_NAME',"localhost");
//define('HOST_NAME',"https://catchapp.iapplabz.co.in");
define('PORT',"8090");
define('ADDRESS',"14.98.71.171");
$null = NULL;
require_once("SocketHandler.php");
$chatHandler = new SocketHandler();
error_reporting(E_ALL);
/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

/* Turn on implicit output flushing so we see what we're getting
 * as it comes in. */
ob_implicit_flush();


if (($socketResource = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
}


socket_set_option($socketResource, SOL_SOCKET, SO_REUSEADDR, 1);

if (socket_bind($socketResource, 0, PORT) === false) {
    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($socketResource)) . "\n";
}

if (socket_listen($socketResource,'5')=== false) {
    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($socketResource)) . "\n";
}
$clientSocketArray = array($socketResource);
while (true) {
    $newSocketArray = $clientSocketArray;
    socket_select($newSocketArray, $null, $null, 0, 10);

    if (in_array($socketResource, $newSocketArray)) {
        $newSocket = socket_accept($socketResource);
        $clientSocketArray[] = $newSocket;

        $header = socket_read($newSocket, 1024);
        $chatHandler->doHandshake($header, $newSocket, HOST_NAME, PORT);

        socket_getpeername($newSocket, $client_ip_address);
        $connectionACK = $chatHandler->newConnectionACK($client_ip_address);

        $chatHandler->send($connectionACK);

        $newSocketIndex = array_search($socketResource, $newSocketArray);
        unset($newSocketArray[$newSocketIndex]);
    }

    foreach ($newSocketArray as $newSocketArrayResource) {
        while(socket_recv($newSocketArrayResource, $socketData, 1024, 0) >= 1){
            $socketMessage = $chatHandler->unseal($socketData);
            $messageObj = json_decode($socketMessage);

//            $chat_box_message = $chatHandler->logStory($messageObj->chat_user, $messageObj->chat_message, $messageObj->user_id);
            $chat_box_message = $chatHandler->logStory($messageObj->story_id, $messageObj->user_id);
            $chatHandler->send($chat_box_message);
            break 2;
        }

        $socketData = @socket_read($newSocketArrayResource, 1024, PHP_NORMAL_READ);
        if ($socketData === false) {
            socket_getpeername($newSocketArrayResource, $client_ip_address);
            $connectionACK = $chatHandler->connectionDisconnectACK($client_ip_address);
            $chatHandler->send($connectionACK);
            $newSocketIndex = array_search($newSocketArrayResource, $clientSocketArray);
            unset($clientSocketArray[$newSocketIndex]);
        }
    }
}
socket_close($socketResource);
