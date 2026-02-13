<?php

$apiKey = "API_KEY";
$baseUrl = "https://api.infusionsoft.com/crm/rest/v2";

function createContact($baseUrl, $apiKey) {
    $payload = json_encode([
        "family_name" => "John",
        "given_name" => "Doe",
        "email_addresses" => [
            ["email" => "johndoe@yopmail.com", "field" => "EMAIL1"]
        ]
    ]);

    $ch = curl_init("$baseUrl/contacts");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey"
        ],
        CURLOPT_POSTFIELDS => $payload
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    echo "Created contact:\n";
    print_r($data);
    return $data;
}

function listContacts($baseUrl, $apiKey) {
    $ch = curl_init("$baseUrl/contacts?page_size=10");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $apiKey"
        ]
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    echo "Contacts:\n";
    print_r($data);
    return $data;
}

createContact($baseUrl, $apiKey);
listContacts($baseUrl, $apiKey);