<?php

require_once 'vendor/autoload.php';

$client = new \Contentful\Delivery\Client(
  '12a5376c4a9393f27a497280383f18a7084b2daed22be64f8aa3f0f7bbe37fe2',
  '6dhwblx395xn',
  'master'
);

$query = new \Contentful\Delivery\Query();
$query->setContentType('news')
  ->orderBy('fields.publishDate');

$entries = $client->getEntries($query);

die(var_dump($entries));