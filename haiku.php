<?php

include 'pigeon-nelson.php';

$server = new PigeonNelsonServer($_GET);

$server->setName("Haiku");
$server->setDescription("Écouter un haiku selon l'environnement proche");
$server->setEncoding("UTF-8");
$server->setDefaultPeriodBetweenUpdates(0);

if ($server->isRequestedSelfDescription()) {
    print $server->getSelfDescription();
    return;
}

// coordinates is required
if (!$server->hasCoordinatesRequest()) {
    $message = PigeonNelsonMessage::makeTxtMessage("Je n'ai pas réussi à vous localiser.", "fr");
    $message->setPriority(0);
    $server->addMessage($message);
}
if ($server->hasCoordinatesAccuracy() && $server->getCoordinatesAccuracy() > 30) {
    $message = PigeonNelsonMessage::makeTxtMessage("J'ai du mal à vous localiser. Attendez quelques instants, je vais réessayer.", "fr");
    $message->setPriority(0);
    $message->setPeriod(10);
    $server->addMessage($message);

}
else {

    $position = $server->getPositionRequest();

    $lat = $position['lat'];
    $lng = $position['lng'];
    $json = file_get_contents("https://osco.anatidaepho.be/osm-haiku?lat=".$lat."&lng=".$lng);
    $haiku = json_decode($json);

    $message = PigeonNelsonMessage::makeTxtMessage($haiku[0].". ".$haiku[1].". ".$haiku[2], "fr");
    $message->setPriority(0);
    $server->addMessage($message);

}
$server->printMessages();

?>
