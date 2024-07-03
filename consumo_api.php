<?php

// URL da API
$url = 'https://dummyjson.com/users?limit=100';

// Obtendo os dados da API
$data = file_get_contents($url);

// Decodificando os dados JSON para um array associativo
$response = json_decode($data, true);

if (!$response || !isset($response['users'])) {
    die('Erro ao acessar a API ou dados não foram retornados corretamente.');
}

// Array para armazenar usuários agrupados por estado
$usersByState = [];

// Iterando sobre os usuários para agrupar por estado
foreach ($response['users'] as $user) {
    $state = $user['address']['state'] ?? 'Desconhecido'; // Usando 'Desconhecido' se 'state' não estiver definido
    $usersByState[$state][] = $user;
}

// Ordenando os usuários por nome dentro de cada estado
foreach ($usersByState as &$stateUsers) {
    usort($stateUsers, function ($a, $b) {
        // Concatenando nome completo para ordenação
        $nameA = $a['firstName'] . ' ' . $a['lastName'];
        $nameB = $b['firstName'] . ' ' . $b['lastName'];
        return strcmp($nameA, $nameB);
    });
}

// Montando o objeto JSON final conforme o exemplo esperado
$result = [
    'users' => $usersByState,
    'total' => $response['total'],
    'skip' => $response['skip'],
    'limit' => $response['limit']
];

// Exibindo o resultado final como JSON
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
