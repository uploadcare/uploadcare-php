<?php

$configuration = Uploadcare\Configuration::create((string) $_ENV['UPLOADCARE_PUBLIC_KEY'], (string) $_ENV['UPLOADCARE_SECRET_KEY']);
$api = (new Uploadcare\Api($configuration))->project();
$projectInfo = $api->getProjectInfo();

echo \sprintf("Project %s info:\n", $projectInfo->getName());
echo \sprintf("Public key: %s\n", $projectInfo->getPubKey());
echo \sprintf("Auto-store enabled: %s\n", $projectInfo->isAutostoreEnabled() ? 'yes' : 'no');

foreach ($projectInfo->getCollaborators() as $email => $name) {
    echo \sprintf("%s: %s\n", $name, $email);
}
