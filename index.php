<?php

use App\Connection\Database;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$connection = new Database();
$customers = $connection->listCustomers();

$msg = null;
if ($uri == '/create' && $method === 'POST') {
    $data = filter_input_array(INPUT_POST);
    $result = $connection->insertCustomer($data);

    if ($result) {
        header('Location: /');
        exit();
    }

    $msg = 'Não foi possível salvar o novo cliente';
}

if (str_contains($uri, 'edit') && $method === 'POST') {
    $customerId = explode('/', $uri)[2];
    $data = filter_input_array(INPUT_POST);
    $result = $connection->updateCustomer($data, $customerId);

    if ($result) {
        header('Location: /');
        exit();
    }

    $msg = 'Não foi possível atualizar o cliente';
}

if (str_contains($uri, 'remove') && $method === 'POST') {
    $customerId = explode('/', $uri)[2];
    $result = $connection->deleteCustomer($customerId);

    if ($result) {
        header('Location: /');
        exit();
    }

    $msg = 'Não foi possível remover o cliente';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Dashboard */
        nav > ul {
            display: flex;
            gap: 24px;
            font-size: 24px;
            list-style-type: none;
            align-items: center;
            justify-content: center;
        }
        .btn-nav {
            background-color: #ccc;
            padding: 4px 8px;
            border-radius: 8px;
            text-decoration: none;
            color: black;
            transition: all 0.2s ease-in;
        }
        .btn-nav:hover {
            background-color: #a7a7a7ff;
        }
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
        tr:nth-child(even) {
            background-color: #f2f2f2; /* Cor para linhas pares */
        }

        tr:nth-child(odd) {
            background-color: #ffffff; /* Cor para linhas ímpares */
        }

        /* Form */
        .form-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form {
            display: grid;
            grid-template-columns: repeat(2, 200px);
            border: 1px solid grey;
            width: fit-content;
            gap: 20px;
            padding: 16px;
            border-radius: 10px;
        }
        label {
            font-size: 20px;
            font-weight: bold;
        }
        input {
            padding: 8px 14px;
        }
        .btn-submit-wrapper {
            width: 100%;
            display: inline-flex;
            justify-content: end;
        }
        .btn-submit {
            background-color: #ccc;
            padding: 8px 12px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #949494ff;
            color: black;
            transition: all 0.2s ease-in;
        }
        .btn-submit:hover {
            background-color: #a7a7a7ff;
        } 
        .form-actions {
            display: inline-flex;
            border: none;
            gap: 10px;
        }
        .btn-edit {
            background-color: #add8e6;
            padding: 2px 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: all 0.2s ease-in;
        }
        .btn-edit > a {
            text-decoration: none;
        }
        .btn-remove {
            background-color: #f08080;
            padding: 2px 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease-in;
        }
        .btn-edit:hover {
            background-color: #6ecbebff;
        }
        .btn-remove:hover {
            background-color: #f54f4fff;
        }
    </style>
    <title>Lista de clientes</title>
</head>
<body>
    <nav>
        <ul>
            <li>
                <a href="/" class="btn-nav">Dashboard</a>
            </li>
            <li>
                <a href="/create" class="btn-nav">Novo registro</a>
            </li>
        </ul>
    </nav>
    <?php if ($uri == '/' && $method == 'GET') { ?>
        <?php if ($msg) { ?>
            <span><?= $msg ?></span>
        <?php } ?>
        <table style="border:1px solid black">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Endereço</th>
                    <th>Cidade</th>
                    <th>Telefone</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($customers as $customer) { ?>
                    <tr>
                        <td><?=  htmlspecialchars($customer['id']); ?></td>
                        <td><?=  htmlspecialchars($customer['name']); ?></td>
                        <td><?=  htmlspecialchars($customer['address']); ?></td>
                        <td><?=  htmlspecialchars($customer['city']); ?></td>
                        <td><?=  htmlspecialchars($customer['phone']); ?></td>
                        <td class="form-actions">
                            <form action="/remove/<?= $customer['id'] ?>" method="post">
                                <button type="submit" class="btn-remove">X</button>
                            </form>
                            <span class="btn-edit">
                                <a href="/edit/<?= $customer['id'] ?>">Editar</a>
                            </span>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>

    <?php if ($uri == '/create' && $method == 'GET') { ?>
        <div class="form-wrapper">
            <form method="post" class="form">
                <div>
                    <label for="">Nome</label>
                    <input name="name"></input>
                </div>
                <div>
                    <label for="">Endereço</label>
                    <input name="address"></input>
                </div>
                <div>
                    <label for="">Cidade</label>
                    <input name="city"></input>
                </div>
                <div>
                    <label for="">Telefone</label>
                    <input name="phone"></input>
                </div>
                <div></div>
                <div class="btn-submit-wrapper">
                    <button type="submit" class="btn-submit">Salvar</button>
                </div>
            </form>
        </div>
    <?php } ?>

    <?php if (str_contains($uri, '/edit') && $method == 'GET') { ?>
        <?php 
            $customerId = explode('/', $uri)[2];
            $customer = $connection->getById($customerId);    
        ?>
        <div class="form-wrapper">
            <form method="post" class="form">
                <div>
                    <label for="">Nome</label>
                    <input name="name" value="<?= $customer->name ?>"></input>
                </div>
                <div>
                    <label for="">Endereço</label>
                    <input name="address" value="<?= $customer->address ?>"></input>
                </div>
                <div>
                    <label for="">Cidade</label>
                    <input name="city" value="<?= $customer->city ?>"></input>
                </div>
                <div>
                    <label for="">Telefone</label>
                    <input name="phone" value="<?= $customer->phone ?>"></input>
                </div>
                <div></div>
                <div class="btn-submit-wrapper">
                    <button type="submit" class="btn-submit">Salvar</button>
                </div>
            </form>
        </div>
    <?php } ?>
</body>
</html>