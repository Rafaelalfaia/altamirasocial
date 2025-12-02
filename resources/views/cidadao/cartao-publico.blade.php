@extends('layouts.public')

@section('title', 'Cartão do Cidadão')

@section('content')
    <div class="w-full max-w-5xl mx-auto px-4 py-8 flex flex-col items-center space-y-8 text-center">

        {{-- Título e Subtítulo --}}
        <div>
            <h1 class="text-3xl font-bold text-indigo-700 mb-1">Cartão Cidadão</h1>
            <p class="text-gray-600 text-sm">Uma forma simples de garantir seus benefícios sociais</p>
        </div>

        {{-- Logos laterais + frase de conscientização --}}
        <div class="flex justify-between items-center w-full">
            <img src="{{ asset('imagens/logo-esquerda.png') }}" class="h-12 w-auto" alt="Logo Esquerda">

            <p class="text-xs text-red-600 max-w-md mx-4">
                ⚠️ Cuidado ao compartilhar suas informações. No QR Code constam dados pessoais do seu cadastro.
            </p>

            <img src="{{ asset('imagens/logo-direita.png') }}" class="h-12 w-auto" alt="Logo Direita">
        </div>

        {{-- Cartões frente e verso --}}
        <div id="cartao" class="flex flex-col md:flex-row justify-center items-center gap-6 w-full">
            {{-- Cartão Frente --}}
            @include('cidadao.cartoes.cartao-frente', ['cidadao' => $cidadao])

            {{-- Cartão Verso --}}
            @include('cidadao.cartoes.cartao-verso', ['cidadao' => $cidadao])
        </div>

        {{-- Botão PDF --}}
        <div>
            <button onclick="gerarPdf()" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">
                Baixar Cartão em PDF
            </button>
        </div>
    </div>

    {{-- JsPDF + html2canvas para gerar PDF --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        async function gerarPdf() {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({ orientation: 'landscape' });
            const cartao = document.getElementById('cartao');

            await html2canvas(cartao).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const imgProps = pdf.getImageProperties(imgData);
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                pdf.addImage(imgData, 'PNG', 0, 10, pdfWidth, pdfHeight);
                pdf.save('cartao-cidadao.pdf');
            });
        }
    </script>
@endsection