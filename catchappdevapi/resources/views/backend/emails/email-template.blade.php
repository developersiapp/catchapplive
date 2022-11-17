<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 11/6/19
 * Time: 5:18 PM
 */
?>

<div>
    {{ csrf_field() }}
    {!! $mail->mail_content !!}
</div>
