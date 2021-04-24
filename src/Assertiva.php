<?php

namespace App;
use App\Util;

class Assertiva
{
    static public function logar($usuario, $senha, $empresa, $proxy)
    {

        $auth = $empresa . '~ASSERTIVA~' . $usuario . '~ASSERTIVA~' . $senha;
        $auth = base64_encode($auth);

        $url   = 'https://eks-assertiva-prd.assertivasolucoes.com.br/auth2/auth/get-key-session-user';

        $headers = [
            'Authorization: '.$auth,
            'Origin: https://app.assertivasolucoes.com.br',
            'Referer: https://app.assertivasolucoes.com.br/'
        ];

        $ver = Util::curl($url, null, null, true, false, false, $proxy, $headers);
        $token = Util::corta($ver, ',"response":"', '"');
        return $token;
    }

    static public function consultarCpf($doc, $token, $proxy)
    {
        $url = 'https://eks-assertiva-prd.assertivasolucoes.com.br/manager-bigdata/localize/1002/consultar';
        $headers = [
            'Authorization: '.$token,
            'Origin: https://app.assertivasolucoes.com.br',
            'Referer: https://app.assertivasolucoes.com.br/'
        ];
        $ver = Util::curl($url, null, http_build_query(['cpf'=> $doc, 'widget'=> 'false']), false, false, false, $proxy, $headers);
        return $ver;
    }

    static public function consultarCnpj($doc, $token, $proxy)
    {
        $url = 'https://eks-assertiva-prd.assertivasolucoes.com.br/manager-bigdata/localize/1003/consultar';
        $headers = [
            'Authorization: '.$token,
            'Origin: https://app.assertivasolucoes.com.br',
            'Referer: https://app.assertivasolucoes.com.br/'
        ];
        $ver = Util::curl($url, null, http_build_query(['cnpj'=> $doc, 'widget'=> 'false']), false, false, false, $proxy, $headers);
        return $ver;
    }

    static public function consultarNome($bairro, $cidade, $complemento, $dataNascimento, $enderecoOuCep, $errorNumeroFinal, $errorNumeroInicial, $nome,$numeroFinal, $numeroInicial, $sexo, $tipoDoc, $token, $proxy)
    {
        $url = 'https://eks-assertiva-prd.assertivasolucoes.com.br/manager-bigdata/localize/1006/consultar';
        $headers = [
            'Authorization: '.$token,
            'Origin: https://app.assertivasolucoes.com.br',
            'Referer: https://app.assertivasolucoes.com.br/',
            'Content-Type: application/json'
        ];
        $post = [
            'bairro'=> $bairro,
            'cidade'=> $cidade,
            'complemento'=> $complemento,
            'dataNascimento'=> $dataNascimento,
            'enderecoOuCep'=> $enderecoOuCep,
            'errorNumeroFinal'=> $errorNumeroFinal,
            'errorNumeroInicial'=> $errorNumeroInicial,
            'isCpf'=> false,
            'nome'=> $nome,
            'nomeMatchCompleto'=> false,
            'numeroFinal'=> $numeroFinal,
            'numeroInicial'=> $numeroInicial,
            'sexo'=> $sexo,
            'tipoDoc'=> $tipoDoc,
            'tipoVeiculo' => '0'

        ];
        $post = json_encode($post);
        $ver = Util::curl($url, null, $post, false, false, false, $proxy, $headers);
        return $ver;
    }

    static public function consultarEmail($email, $token, $proxy)
    {
        $url = 'https://eks-assertiva-prd.assertivasolucoes.com.br/manager-bigdata/localize/1004/consultar';
        $headers = [
            'Authorization: '.$token,
            'Origin: https://app.assertivasolucoes.com.br',
            'Referer: https://app.assertivasolucoes.com.br/'
        ];
        $ver = Util::curl($url, null, http_build_query(['email'=> $email]), false, false, false, $proxy, $headers);
        return $ver;
    }

    static public function consultarTelefone($numero, $token, $proxy)
    {
        $url = 'https://eks-assertiva-prd.assertivasolucoes.com.br/manager-bigdata/localize/1005/consultar';
        $headers = [
            'Authorization: '.$token,
            'Origin: https://app.assertivasolucoes.com.br',
            'Referer: https://app.assertivasolucoes.com.br/'
        ];
        $ver = Util::curl($url, null, http_build_query(['telefone'=> $numero]), false, false, false, $proxy, $headers);
        return $ver;
    }

}