<?= include "funcoes.php" ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Api Banco Central</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <h1>Dólar comercial (venda e compra)</h1>
        <!-- $_SERVER['PHP_SELF'] é uma variável superglobal que retorna o nome do arquivo do script atualmente em execução.Quando o formulário for enviado, ele enviará os dados de volta para o mesmo script-->
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="get">
            <pre>
                <?php
                //chamar função
                $dadosmoeda = busca_info_moeda();
                //Verificar se o parametro está definido através do método GET, caso não estiver atribuir valor null
                $moeda = $_GET["moeda"] ?? null;
                $dataI = $_GET["dataI"] ?? null;
                $dataF = $_GET["dataF"] ?? null;
                ?>
            </pre>
            <label for="moeda">Tipo de Moeda</label>
            <!-- select com opçoes de moedas obtidas dos dados JSON. 
            Foreach para percorrer matriz "value" e assim criar opçao de moeda com o valor['simbolo'] e o nome['nomeFormatado'].      -->
            <select name="moeda" id="moeda">
                <?php foreach ($dadosmoeda["value"] as $valor) { ?>
                    <option value="<?= $valor['simbolo'] ?>"><?= $valor['nomeFormatado'] ?></option>
                <?php } ?>
            </select>

            <label for="dataI">Data inicio <img src="https://icons.iconarchive.com/icons/famfamfam/silk/16/asterisk-orange-icon.png" width="16" height="16"></label>
            <input type="datetime-local" name="dataI" id="dataI" required="required" value="<?= $dataI ?>">
            <label for="dataF">Data Final <img src="https://icons.iconarchive.com/icons/famfamfam/silk/16/asterisk-orange-icon.png" width="16" height="16"></label>
            <input type="datetime-local" name="dataF" id="dataF" required="required" value="<?= $dataF ?>">
            <label for="campo">Selecione o(s) campo(s) <img src="https://icons.iconarchive.com/icons/famfamfam/silk/16/asterisk-orange-icon.png" width="16" height="16"></label>
            <select name="campo[]" id="campo" multiple="multiple" required="required">
                <option value="tipoBoletim">Tipo de Boletim</option>
                <option value="paridadeCompra">Paridade de compra</option>
                <option value="paridadeVenda">Paridade de venda</option>
                <option value="dataHoraCotacao">Data e hora da cotação</option>
            </select>
            <label for="tipo">Selecione o tipo de boletim</label>
            <select name="tipoBol" id="tipoBol">
                <option value="Abertura">Abertura</option>
                <option value="Intermediário">Intermediário</option>
                <option value="Fechamento">Fechamento</option>
            </select>
            <input type="submit" value="Enviar" name="enviar">
        </form>
    </main>
    <section id="resultado">
        <h1>Resultado</h1>
        <pre>
            <?php
            //** Verificar se há algum parametro na solicitação GET. Caso a contagem de parâmetros for maior que 0, significa que existem parâmetros presentes na URL.
            if (count($_GET) > 0)
                $dados = trata_dados_data_e_hora($moeda, $dataI, $dataF, false); //chamar função
            //var_dump($dados);
            ?>
            <table>
                <thead>
                    <tr>
                        <?php
                        if (isset($_GET['campo'])) { // Verificar se parametro 'campo' esta definido.
                            foreach ($_GET['campo'] as $opcao) { //Inicia um loop para iterar sobre cada valor no array 'campo'e atribuir a variavel $opcao.//
                                //*** Funçoes para verificar se o valor atual no loop é igual a... Caso for criar cabeçalho na tabela . 
                                if ($opcao == 'paridadeCompra') { ?>
                                        <th>Paridade de Compra</th> 
                                    <?php
                                } elseif ($opcao == 'paridadeVenda') { ?>
                                        <th>Paridade de Venda</th>  
                                    <?php
                                } elseif ($opcao == 'tipoBoletim') { ?>
                                        <th>Tipo de Boletim</th> 
                                    <?php
                                } elseif ($opcao == 'dataHoraCotacao') { ?>
                                        <th>Data e Hora</th> 
                                    <?php } ?>  
                            <?php } ?>
                        <?php } ?>                                 
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //***Verificar se o parametro está definido através do método GET, caso não for atribuir null****//
                    $tipo_boletim = $_GET['tipoBol'] ?? null;
                    $dataI = $_GET["dataI"] ?? null;

                    $data_tratada = trata_dados_data_e_hora($moeda, $dataI, $dataF, true); //chamar função data_Hora passando os parametros e recebendo um array
                    $dataInicio = $data_tratada['dataInicio']; //recebe o valor associado à chave 'dataInicio'do array 
                    $dataFinal = $data_tratada['dataFinal']; //revebe o valor associado à chave 'dataFinal'do array 

                    // isset para verififar se 'value' foi definido/existe no array '$dados'.
                    //is_array()/is_object()- verifica se o valor é um array ou objeto.
                    if (isset($dados["value"]) && (is_array($dados["value"]) || is_object($dados["value"]))) {
                        foreach ($dados["value"] as $valor) { //Itera sobre os elementos do array ou objeto 
                            $valor_tratado = filtro_boletim_e_data($valor, $tipo_boletim, $dataInicio, $dataFinal, $dados); //chmar função
                            if ($valor_tratado == false) { //verifica se $valor_tratado é igual a false. Se for, 'continue'é executada, pulando para a próxima iteração.
                                continue;
                            }

                            //***Verificar se o parametro está definido através do método GET, caso não for atribuir null
                            $valor['paridadeCompra'] ?? null;
                            $valor['paridadeVenda'] ?? null;
                    ?>
                                <tr>
                                    <?php
                                    foreach ($_GET['campo'] as $opcao) { //Inicia um loop para iterar sobre cada valor no array 'campo'.
                                        //*** Funçoes para verificar se o valor atual no loop é igual a 'paridadeCompra'. Caso for gera o valor de 'paridadeCompra' do array dentro de uma célula da tabela .
                                        if ($opcao == 'paridadeCompra') { ?>
                                                <td><?= $valor['paridadeCompra'] ?></td>
                                            <?php
                                        } elseif ($opcao == 'paridadeVenda') { ?>
                                                <td><?= $valor['paridadeVenda'] ?></td>  
                                            <?php
                                        } elseif ($opcao == 'tipoBoletim') { ?>
                                                <td><?= $valor['tipoBoletim'] ?></td> 
                                            <?php
                                        } elseif ($opcao == 'dataHoraCotacao') { ?>
                                                <td><?= $valor['dataHoraCotacao'] ?></td> 
                                            <?php } ?>  
                                    <?php } ?>                                       
                                </tr> 
                            <?php }
                    } else {
                        echo "Dados não definidos!!"; //Emitir mensagem de erro caso dados não forem definidos
                    } ?>
                </tbody>
            </table>
        </pre>
    </section>
</body>

</html>