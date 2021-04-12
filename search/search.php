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
    $leconIndex = 1;
    foreach ($contenusLecons->getDocuments() as $contenuLecon) {
        $resultatsContenusLecons[] = [
            'id' => $leconIndex,
            'text' => $contenuLecon->label . ' (' . $contenuLecon->categorie . ')',
            'url' => $contenuLecon->url,
        ];
        $leconIndex++;
    }
}

if (count($resultatsSousCategories) == 0) {
    $resultatsSousCategories[] = [
        'id' => 0,
        'text' => 'Aucun résultat...',
        'url' => '#',
        "disabled": true
    ];
}

if (count($resultatsLecons) == 0) {
    $resultatsLecons[] = [
        'id' => 0,
        'text' => 'Aucun résultat...',
        'url' => '#',
        "disabled": true
    ];
}

if (count($resultatsContenusLecons) == 0) {
    $resultatsContenusLecons[] = [
        'id' => 0,
        'text' => 'Aucun résultat...',
        'url' => '#',
        "disabled": true
    ];
}

$resultats = [
    'results' => [
        [
            'text' => 'Thématiques',
            'children' => $resultatsSousCategories,
        ],
        [
            'text' => 'Leçons',
            'children' => $resultatsLecons,
        ],
        [
            'text' => 'Contenus de leçons',
            'children' => $resultatsContenusLecons,
        ],
    ]
];

header('Content-type: application/json');
header('Access-Control-Allow-Origin: https://paysdufle.fr');

echo json_encode($resultats);