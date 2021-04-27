<?php

# On charge les librairies
require __DIR__ . '/vendor/autoload.php';

use MeiliSearch\Client;

$meilisearch_master_key = $_ENV["MEILISEARCH_MASTER_KEY"] ?? null;

if ($meilisearch_master_key !== null) {
    $client = new Client('http://meilisearch:7700', $meilisearch_master_key);
    $subCategoriesIndex = $client->index('subCategories');
    $lessonsIndex = $client->index('lessons');
}

$recherche = $_GET['term'];

$resultats = [];
$resultatsSousCategories = [];
$resultatsContenusLecons = [];

$subCategories = $subCategoriesIndex->search($recherche)->getHits();

if (! empty($subCategories)) {
    $sousCategorieIndex = 1;
    foreach ($subCategories as $sousCategorie) {
        $resultatsSousCategories[] = [
            'id' => $sousCategorieIndex,
            'text' => $sousCategorie['label'].' ('.$sousCategorie['categorie'].')',
            'url' => $sousCategorie['url'],
        ];
        $sousCategorieIndex++;
    }
}

$lessons = $lessonsIndex->search($recherche)->getHits();

if (! empty($lessons)) {
    $leconIndex = 1;
    foreach ($lessons as $contenuLecon) {
        $resultatsContenusLecons[] = [
            'id' => $leconIndex,
            'text' => $contenuLecon['label'].' ('.$contenuLecon['categorie'].')',
            'url' => $contenuLecon['url'],
        ];
        $leconIndex++;
    }
}

$results = [];

if (count($resultatsSousCategories) > 0) {
    $results[] = [
        'text' => 'Thématiques',
        'children' => $resultatsSousCategories,
    ];
}

if (count($resultatsContenusLecons) > 0) {
    $results[] = [
        'text' => 'Leçons',
        'children' => $resultatsContenusLecons,
    ];
}

$resultats = [
    'results' => $results
];

header('Content-type: application/json');
header('Access-Control-Allow-Origin: https://paysdufle.fr');

echo json_encode($resultats);