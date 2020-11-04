<?php

require 'src/News.php';

$news = new \Azeroglu\Packagist\News();
$news = $news->setFormat('text');
$news = $news->getApaAz();

print_r($news);
