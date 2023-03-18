<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->group();

$list = $api->listGroups();
foreach ($list->getResults() as $group) {
    \sprintf('Group URL: %s, ID: %s', $group->getUrl(), $group->getUuid());
}

while (($next = $api->nextPage($list)) !== null) {
    foreach ($next->getResults() as $group) {
        \sprintf('Group URL: %s, ID: %s', $group->getUrl(), $group->getUuid());
    }
}
