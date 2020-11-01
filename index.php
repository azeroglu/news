<?php

require 'src/News.php';

$news = \Azeroglu\Packagist\News::report_az();


print_r($news);
