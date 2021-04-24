<?php
$config = parse_ini_file('.env' , true);
$_ENV = $config;
require 'vendor/autoload.php';

use \React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

function pdo_connect_mysql() {
    $DATABASE_HOST = $_ENV['DATABASE_HOST'];
    $DATABASE_USER = $_ENV['DATABASE_USER'];
    $DATABASE_PASS = $_ENV['DATABASE_PASS'];
    $DATABASE_NAME = $_ENV['DATABASE_NAME'];
    try {
    	return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $exception) {
    	exit('Failed to connect to database!');
    }
}

function saveCount($id, $pdo)
{
    $stmt = $pdo->prepare('UPDATE senhas SET usado=usado+1 WHERE id=:id');
    return $stmt->execute(array( ':id' => $id));
}

function getDados($pdo)
{
    $stmt = $pdo->prepare('SELECT * FROM senhas WHERE servico="Assertiva" and status=1 ORDER BY usado ASC');
    $stmt->execute();
    $con = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $con;
}

function saveCookie($cookie, $id, $pdo)
{
    $stmt = $pdo->prepare('UPDATE senhas SET cookie=:cookie WHERE id=:id');
    $stmt->execute(array( ':id' => $id, ':cookie' => $cookie));
    return $stmt->rowCount();
}

$loop = React\EventLoop\Factory::create();
$pdo = pdo_connect_mysql();

$init = function (ServerRequestInterface $request) use(&$pdo){
    $dados = getDados($pdo);
    $path   = $request->getUri()->getPath();

    if ($path === '/Consulta') {
        $doc = $request->getQueryParams();
        $proxy = $dados[0]['proxy'];
        $usuario = $dados[0]['usuario'];
        $empresa = $dados[0]['empresa'];
        $senha   = $dados[0]['senha'];
        $token = App\Assertiva::logar($usuario, $senha, $empresa, $proxy);


        if(array_key_exists('cpf', $doc)){
            $ver = App\Assertiva::consultarCpf($doc['cpf'], $token, $proxy);
            saveCount($dados[0]['id'], $pdo);
        }elseif(array_key_exists('cnpj', $doc)){
            $ver = App\Assertiva::consultarCnpj($doc['cnpj'], $token, $proxy);
            saveCount($dados[0]['id'], $pdo);
        }elseif(array_key_exists('email', $doc)) {
            $ver = App\Assertiva::consultarEmail($doc['email'], $token, $proxy);
            saveCount($dados[0]['id'], $pdo);
        }elseif(array_key_exists('telefone', $doc)){
            $numero = $doc['telefone'];
            $ver = App\Assertiva::consultarTelefone($numero, $token, $proxy);
            saveCount($dados[0]['id'], $pdo);
        }elseif(array_key_exists('nome', $doc)){

            $bairro = $doc['bairro'] ?? '';
            $cidade = $doc['cidade'] ?? '';
            $complemento    = $doc['complemento'] ?? '';
            $dataNascimento = $doc['dataNascimento'] ?? '';
            $enderecoOuCep  = $doc['enderecoOuCep'] ?? '';
            $errorNumeroFinal   = $doc['errorNumeroFinal'] ?? '';
            $errorNumeroInicial = $doc['errorNumeroInicial'] ?? '';
            $nome          = $doc['nome'] ?? '';
            $numeroFinal   = $doc['numeroFinal'] ?? '';
            $numeroInicial = $doc['numeroInicial'] ?? '';
            $sexo    = $doc['sexo'] ?? '';
            $tipoDoc = $doc['tipoDoc'] ?? '';
            $ver = App\Assertiva::consultarNome($bairro, $cidade, $complemento, $dataNascimento, $enderecoOuCep, $errorNumeroFinal, $errorNumeroInicial, $nome,$numeroFinal, $numeroInicial, $sexo, $tipoDoc, $token, $proxy);
            saveCount($dados[0]['id'], $pdo);
        }

        $dadosx = $ver;
        return new Response( 200, array( 'Content-Type' => 'text/json'), $dadosx);
    }else{
        return new Response( 200, array( 'Content-Type' => 'text/html'), []);     
    }
};

$server = new React\Http\Server($loop, $init);
$socket = new React\Socket\Server("0.0.0.0:8282", $loop);
$server->listen($socket);
echo "Server running at http://127.0.0.1:8282\n";
$loop->run();
