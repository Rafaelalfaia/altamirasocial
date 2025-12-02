<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CpfValido implements Rule
{
    public function passes($attribute, $value): bool
    {
        $cpf = preg_replace('/\D/', '', $value);

        if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/', $cpf))
            return false;

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d)
                return false;
        }

        return true;
    }

    public function message(): string
    {
        return 'O CPF informado é inválido.';
    }
}
