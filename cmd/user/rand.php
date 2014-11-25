<?php
$rand1 = (!isset($arguments[1]) or $arguments[1] == '') ? rand(1, 99999) : $arguments[1];
$rand2 = (!isset($arguments[2]) or $arguments[2] == '') ? rand(1, 99999) : $arguments[2];
$this->say('亂數: ' . rand($rand1, $rand2));