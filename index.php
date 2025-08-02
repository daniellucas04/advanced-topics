<?php

use App\Connection\Database;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$connection = new Database();
$customers = $connection->listCustomers(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        table {
            width: 50%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            padding: 8px;
            border: 1px solid #444;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        h1 {
            text-align: center;
        }
    </style>
    <title>Lista de clientes</title>
</head>
<body>
    <table style="border:1px solid black">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Endereço</th>
                <th>Cidade</th>
                <th>Estado</th>
                <th>Telefone</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($customers as $customer) { ?>
                <tr>
                    <td><?=  htmlspecialchars($customer['id']); ?></td>
                    <td><?=  htmlspecialchars($customer['name']); ?></td>
                    <td><?=  htmlspecialchars($customer['address']); ?></td>
                    <td><?=  htmlspecialchars($customer['city']); ?></td>
                    <td><?=  htmlspecialchars($customer['state']); ?></td>
                    <td><?=  htmlspecialchars($customer['phone']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>