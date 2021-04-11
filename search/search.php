<?php

# On charge les librairies
require __DIR__ . '/vendor/autoload.php';

use Ehann\RedisRaw\PredisAdapter;
use Ehann\RediSearch\Index;

# Client redis
$redis = (new PredisAdapter())->connect('redis', 6379);
$contenuIndex = new Index($redis, 'contenuIndex');

$recherche = $_GET['term'] . '*';

$sousCategories = $contenuIndex
    ->tagFilter('type', ['sousCategorie'])
    ->search($recherche);

$lecons = $contenuIndex
    ->tagFilter('type', ['lecon'])
    ->search($recherche);

$resultats = [];
$resultatsSousCategories = [];
$resultatsLecons = [];

if ($sousCategories->getCount()) {
    $sousCategorieIndex = 1;
    foreach ($sousCategories->getDocuments() as $sousCategorie) {
        $resultatsSousCategories[] = [
            'id' => $sousCategorieIndex,
            'text' => $sousCategorie->nom . ' (' . $sousCategorie->categorie . ')',
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
            'text' => $lecon->nom . ' (' . $lecon->categorie . ')',
            'url' => $lecon->url,
        ];
        $leconIndex++;
    }
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
    ]
];

header('Content-type: application/json');
header('Access-Control-Allow-Origin: https://paysdufle.fr');

echo json_encode($resultats);