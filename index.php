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
if ($method === 'POST') {

    $action = $_POST['action'] ?? null;
    if ($action === 'create') {
        $data = filter_input_array(INPUT_POST);
        $result = $connection->insertCustomer($data);

        if ($result) {
            $msg = "Cliente criado com sucesso!";
        } else {
            $msg = "Erro ao criar cliente.";
        }
    }

    if ($action === 'update') {
        $customerId = $_POST['id'];
        $data = filter_input_array(INPUT_POST);
        $result = $connection->updateCustomer($data, $customerId);

        if ($result) {
            $msg = "Cliente atualizado com sucesso!";
        } else {
            $msg = "Erro ao atualizar cliente.";
        }
    }

    if ($action === 'delete') {
        $customerId = $_POST['id'];
        $result = $connection->deleteCustomer($customerId);

        if ($result) {
            $msg = "Cliente removido com sucesso!";
        } else {
            $msg = "Erro ao remover cliente.";
        }
    }

    if ($action === 'load-edit') {
        $customerToEdit = $connection->getById($_POST['id']);
    }
}

$customers = $connection->listCustomers();
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
    <?php if ($msg) { ?>
        <span><?= $msg ?></span>
    <?php } ?>

    <div class="form-wrapper">
        <div class="form-wrapper">
            <form method="post" class="form">
                <?php 
                    if (isset($customerToEdit) && $customerToEdit != null) {
                        echo '<input type="hidden" name="action" value="update">';
                        echo '<input type="hidden" name="id" value="' . $customerToEdit->id . '">';
                    } else {
                        echo '<input type="hidden" name="action" value="create">';
                    }
                ?>
                <div>
                    <label for="">Nome</label>
                    <input name="name" value="<?= $customerToEdit->name ?? '' ?>"></input>
                </div>
                <div>
                    <label for="">Endereço</label>
                    <input name="address" value="<?= $customerToEdit->address ?? '' ?>"></input>
                </div>
                <div>
                    <label for="">Cidade</label>
                    <input name="city" value="<?= $customerToEdit->city ?? '' ?>"></input>
                </div>
                <div>
                    <label for="">Telefone</label>
                    <input name="phone" value="<?= $customerToEdit->phone ?? '' ?>"></input>
                </div>
                <div></div>
                <div class="btn-submit-wrapper">
                    <button type="submit" class="btn-submit">Salvar</button>
                </div>
            </form>
        </div>
    </div>

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
                        <form method="post">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $customer['id'] ?>">
                            <button type="submit" class="btn-remove">X</button>
                        </form>
                        <form method="post">
                            <input type="hidden" name="action" value="load-edit">
                            <input type="hidden" name="id" value="<?= $customer['id'] ?>">
                            <button type="submit" class="btn-edit">Editar</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>