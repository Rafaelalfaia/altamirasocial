<table>
    <thead>
        <tr>
            <th>Nome Completo</th>
            <th>CPF</th>
            <th>Data de Nascimento</th>
            <th>Telefone</th>
        </tr>
    </thead>
    <tbody>
        @foreach($inscricoes as $inscricao)
            @php
                $cidadao = $inscricao->cidadao;
                $cpf = preg_replace('/\D/', '', $cidadao->cpf);
                $formato = trim(strtolower($lote->formato_cpf ?? 'com_pontos'));

                $cpf_formatado = $formato === 'com_pontos' && strlen($cpf) === 11
                    ? substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9)
                    : $cpf;

                $telefone = preg_replace('/\D/', '', $cidadao->telefone ?? '');
                $telefone_formatado = strlen($telefone) === 11
                    ? '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7)
                    : $telefone;

                $data_nasc = $cidadao->data_nascimento
                    ? \Carbon\Carbon::parse($cidadao->data_nascimento)->format('d/m/Y')
                    : '';
            @endphp
            <tr>
                <td>{{ $cidadao->nome }}</td>
                <td>{{ $cpf_formatado }}</td>
                <td>{{ $data_nasc }}</td>
                <td>{{ $telefone_formatado }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
