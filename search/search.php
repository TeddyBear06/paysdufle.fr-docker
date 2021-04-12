<?php

# On charge les librairies
require __DIR__ . '/vendor/autoload.php';

use Ehann\RedisRaw\PredisAdapter;
use Ehann\RediSearch\Index;

# Client redis
$redis = (new PredisAdapter())->connect('redis', 6379);
$contenuIndex = new Index($redis, 'contenuIndex');

$recherche = $_GET['term'];

$cleanedTags = str_replace(['\'', '"', ',' , ';', '<', '>'], '', $recherche);
$cleanedTags = str_replace(['-'], ' ', $recherche);

$tags = explode(' ', $cleanedTags);
$queryTags = [];

foreach ($tags as $tag) {
    $queryTags[] = $tag . '*';
}

$sousCategories = $contenuIndex
    ->tagFilter('type', ['sousCategorie'])
    ->search($recherche);

$lecons = $contenuIndex
    ->tagFilter('type', ['lecon'])
    ->search($recherche);

$contenusLecons = $contenuIndex
    ->tagFilter('type', ['contenuLecon'])
    ->tagFilter('tag', $queryTags)
    ->search();

$resultats = [];
$resultatsSousCategories = [];
$resultatsLecons = [];
$resultatsContenusLecons = [];

if ($sousCategories->getCount()) {
    $sousCategorieIndex = 1;
    foreach ($sousCategories->getDocuments() as $sousCategorie) {
        $resultatsSousCategories[] = [
            'id' => $sousCategorieIndex,
            'text' => $sousCategorie->label . ' (' . $sousCategorie->categorie . ')',
            'url' => $sousCategorie->url,
        ];
        $sousCategorieIndex++;
    }
}

if ($lecons->getCount()) {
    $leconIndex = 1;
    foreach ($lecons->getDocuments() as $lecon) {
        $resultatsLecons[] = [
            'id' => $leconIndex,
            'text' => $lecon->label . ' (' . $lecon->categorie . ')',
            'url' => $lecon->url,
        ];
        $leconIndex++;
    }
}

if ($contenusLecons->getCount()) {
    $leconsDejaSuggerees = [];
    $leconIndex = 1;
    foreach ($contenusLecons->getDocuments() as $contenuLecon) {
        if (! in_array($contenuLecon->label, $leconsDejaSuggerees)) {
            $resultatsContenusLecons[] = [
                'id' => $leconIndex,
                'text' => $contenuLecon->tag . ' [' . $contenuLecon->label . '] (' . $contenuLecon->categorie . ')',
                'url' => $contenuLecon->url,
            ];
            $leconIndex++;
            $leconsDejaSuggerees[] = $contenuLecon->label;
        }
    }
}

$results = [];

if (count($resultatsSousCategories) > 0) {
    $results[] = [
        'text' => 'Thématiques',
        'children' => $resultatsSousCategories,
    ];
}

if (count($resultatsLecons) > 0) {
    $results[] = [
        'text' => 'Leçons',
        'children' => $resultatsLecons,
    ];
}

if (count($resultatsContenusLecons) > 0) {
    $results[] = [
        'text' => 'Tag de leçons',
        'children' => $resultatsContenusLecons,
    ];
}

$resultats = [
    'results' => $results
];

header('Content-type: application/json');
header('Access-Control-Allow-Origin: https://paysdufle.fr');

echo json_encode($resultats);