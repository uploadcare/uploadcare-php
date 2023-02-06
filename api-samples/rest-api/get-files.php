<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->file();

$list = $api->listFiles();
foreach ($list->getResults() as $result) {
    echo \sprintf('URL: %s', $result->getUrl());
}

while (($next = $api->nextPage($list)) !== null) {
    foreach ($next->getResults() as $result) {
        echo \sprintf('URL: %s', $result->getUrl());
    }
}
