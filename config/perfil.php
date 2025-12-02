<?php
// config/perfil.php

return [

    // Campos de seleção simples
    'sexo' => [
        'M' => 'Masculino',
        'F' => 'Feminino',
        'O' => 'Outro',
        'N' => 'Prefere não informar',
    ],

    'estado_civil' => [
        'SOLTEIRO'   => 'Solteiro(a)',
        'CASADO'     => 'Casado(a)',
        'VIUVO'      => 'Viúvo(a)',
        'DIVORCIADO' => 'Divorciado(a)',
        'UNIAO'      => 'União estável',
    ],

    'cor_raca' => [
        'BRANCO'  => 'Branco(a)',
        'PRETO'   => 'Preto(a)',
        'PARDO'   => 'Pardo(a)',
        'AMARELO' => 'Amarelo(a)',
        'INDIGENA'=> 'Indígena',
        'OUTRO'   => 'Outro',
    ],

    // Domicílio / Infra
    'situacao_moradia' => [
        'PROPRIA' => 'Própria',
        'ALUGADA' => 'Alugada',
        'CEDIDA'  => 'Cedida',
        'INVASAO' => 'Ocupação/Invasão',
        'OUTRA'   => 'Outra',
    ],

    'tipo_construcao' => [
        'ALVENARIA' => 'Alvenaria',
        'MADEIRA'   => 'Madeira',
        'MISTA'     => 'Mista',
        'PALHA'     => 'Palha',
        'OUTRA'     => 'Outra',
    ],

    'energia' => [
        'MEDIDOR'    => 'Com medidor',
        'SEM_PADRAO' => 'Sem padrão',
        'NAO_POSSUI' => 'Não possui',
    ],

    'agua' => [
        'REDE'  => 'Rede pública',
        'POCO'  => 'Poço',
        'FONTE' => 'Fonte',
        'PIPA'  => 'Carro-pipa',
        'OUTRA' => 'Outra',
    ],

    'via' => [
        'ASFALTO'  => 'Asfalto',
        'BLOQUETE' => 'Bloquete',
        'PICARRA'  => 'Piçarra',
        'TERRA'    => 'Terra',
        'OUTRA'    => 'Outra',
    ],

    // Trabalho / Escolaridade
    'situacao_profissional' => [
        'DESEMPREGADO' => 'Desempregado',
        'CLT'          => 'Empregado (CLT)',
        'OCASIONAL'    => 'Trabalho ocasional',
        'INFORMAL'     => 'Informal',
        'AUTONOMO'     => 'Autônomo',
        'APOSENTADO'   => 'Aposentado',
        'OUTRA'        => 'Outra',
    ],

    // 1..13 conforme o padrão usado no DOCX e no controller
    'escolaridade' => [
        1  => 'Sem instrução',
        2  => 'Fundamental I incompleto',
        3  => 'Fundamental I completo',
        4  => 'Fundamental II incompleto',
        5  => 'Fundamental II completo',
        6  => 'Médio incompleto',
        7  => 'Médio completo',
        8  => 'Técnico incompleto',
        9  => 'Técnico completo',
        10 => 'Superior incompleto',
        11 => 'Superior completo',
        12 => 'Pós-graduação lato sensu',
        13 => 'Pós-graduação stricto sensu',
    ],

    // 1..9 conforme o padrão usado no DOCX e no controller
    'parentesco' => [
        1 => 'Cônjuge/Companheiro(a)',
        2 => 'Filho(a)',
        3 => 'Pai/Mãe',
        4 => 'Sogro(a)',
        5 => 'Neto(a)',
        6 => 'Irmão(ã)',
        7 => 'Outro parente',
        8 => 'Não parente',
        9 => 'Outro',
    ],

];
