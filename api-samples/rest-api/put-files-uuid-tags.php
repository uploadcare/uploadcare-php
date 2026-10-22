<?php

$configuration = Uploadcare\Configuration::create(
    (string) $_ENV["UPLOADCARE_PUBLIC_KEY"],
    (string) $_ENV["UPLOADCARE_SECRET_KEY"],
);
$api = new Uploadcare\Api($configuration)->tags();
$result = $api->replaceTags("1bac376c-aa7e-4356-861b-dd2657b5bfd2", [
    "cat",
    "animal",
    "cute",
]);

echo \sprintf(
    "Tags: %s\nAdded: %s\nDeleted: %s\n",
    (string) $result->getTags(),
    \implode(", ", $result->getAdded()),
    \implode(", ", $result->getDeleted()),
);
