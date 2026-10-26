<?php

use Uploadcare\Search\SearchQuery;

$configuration = Uploadcare\Configuration::create(
    (string) $_ENV["UPLOADCARE_PUBLIC_KEY"],
    (string) $_ENV["UPLOADCARE_SECRET_KEY"],
);
$api = (new Uploadcare\Api($configuration))->search();

$query = (new SearchQuery())
    ->isImage()
    ->tagsAll(["outdoor"])
    ->tagsAny(["cat", "dog"])
    ->tagsNone(["archived"])
    ->sort("-datetime_uploaded");

$result = $api->search($query, 50);
echo \sprintf("Found %d file(s)\n", $result->getTotal());

do {
    foreach ($result->getResults() as $file) {
        echo \sprintf("%s %s\n", $file->getUuid(), $file->getOriginalFilename());
    }
} while (($result = $api->nextPage($result)) !== null);
