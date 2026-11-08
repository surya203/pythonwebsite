<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if ($path === '/') {
    header('Content-Type: text/html; charset=UTF-8');
    readfile(__DIR__ . '/index.html');
    exit;
}
?><style>
    .whatsapp_float{
        position:fixed;
        bottom:40px;
        right:20px;
      
    }
</style>


<div class="whatsapp_float">
    <a href="https://wa.me/919154083255" target="_blank">
    <img src="whatsapp.png" width="50px",class="whatsapp_float_btn"/>
    </a>
</div>