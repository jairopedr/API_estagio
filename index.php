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
        <form action="<?=$_SERVER['PHP_SELF']?>" method="get"> 
            <pre>
            <?php 
                $urlmoeda='https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/Moedas?$top=100&$format=json&$select=simbolo,nomeFormatado';// **Variável que recebe url API da moeda no formato JSON //

                //*** Função para tratar os dados da url***//  
                //json_decode - Pega uma string codificada em JSON e a converte em um valor PHP.
                //file_get_contens - Função para buscar/recuperar o conteúdo da URL especificada
                //O parâmetro "true" indica que os dados JSON vão ser decodificadoss em uma matriz associativa/array.
                $dadosmoeda= json_decode(file_get_contents($urlmoeda), true);

                //*** Funções para verificar se o parâmetro está definido através do método GET.
                if(isset($_GET["moeda"])){
                    $moeda= $_GET["moeda"] ;                   
                }else{
                    $moeda=null;
                }
                
                if(isset($_GET["dataI"])){
                    $dataI= $_GET["dataI"];
                }else{
                    $dataI=null;
                }

                if(isset($_GET["dataF"])){
                    $dataF= $_GET["dataF"];
                }else{
                    $dataF=null;
                }
                //*****//
            ?>
            </pre>
            <label for="moeda">Tipo de Moeda</label>
            <!-- select com opçoes de moedas obtidas dos dados JSON. 
            Foreach para percorrer matriz "value" e assim criar opçao de moeda com o valor['simbolo'] e o nome['nomeFormatado'].      -->
            <select name="moeda" id="moeda">
                <?php foreach ($dadosmoeda["value"] as $valor) {?>
                    <option value="<?=$valor['simbolo']?>"><?= $valor['nomeFormatado']?></option>
                <?php } ?>
            </select>
            
            <label for="dataI">Data inicio <img src="https://icons.iconarchive.com/icons/famfamfam/silk/16/asterisk-orange-icon.png" width="16" height="16"></label>
            <input type="datetime-local" name="dataI" id="dataI" required="required" value="<?=$dataI?>">
            <label for="dataF">Data Final <img src="https://icons.iconarchive.com/icons/famfamfam/silk/16/asterisk-orange-icon.png" width="16" height="16"></label>
            <input type="datetime-local" name="dataF" id="dataF" required="required" value="<?=$dataF?>">
            <label for="campo">Selecione o(s) campo(s) <img src="https://icons.iconarchive.com/icons/famfamfam/silk/16/asterisk-orange-icon.png" width="16" height="16"></label>
            <select name="campo[]" id="campo" multiple="multiple" required="required">
                <option value="tipoBoletim" >Tipo de Boletim</option>
                <option value="paridadeCompra">Paridade de compra</option>
                <option value="paridadeVenda" >Paridade de venda</option>
                <option value="dataHoraCotacao">Data e hora da cotação</option>
            </select>
            <label for="tipo">Selecione o tipo de boletim</label>
            <select name="tipoBol" id="tipoBol" >
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
                //*** Funções para converter a data original para um carimbo de data/hora Unix.
                //*** Função date para formatar carimbo de data/hora em uma string no formato "m d Y"
                $dataInicio = strtotime($dataI);
                $dataInicio = date("m-d-Y", $dataInicio ); 
   
                $dataFinal = strtotime($dataF);
                $dataFinal = date("m-d-Y", $dataFinal );
                //****//
                
                $url='https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoMoedaPeriodo(moeda=@moeda,dataInicial=@dataInicial,dataFinalCotacao=@dataFinalCotacao)?@moeda=\''.$moeda.'\'&@dataInicial=\''.$dataInicio.'\'&@dataFinalCotacao=\''.$dataFinal.'\'&$top=100&$select=paridadeCompra,paridadeVenda,tipoBoletim,dataHoraCotacao'; //variável que recebe url da API cotacao
                
                //** Verificar se há algum parametro na solicitação GET. Caso a contagem de parâmetros for maior que 0, significa que existem parâmetros presentes na URL.
                if (count($_GET) > 0)
                     $dados = json_decode(file_get_contents($url),true); 
                    //Tratar dados da url(mesma forma da variavel $dadosmoeda).
                           
            ?>
            <table>
                <thead>
                    <tr>
                        <?php if(isset($_GET['campo'])){// Verificar se parametro 'campo' esta definido.
                            foreach ($_GET['campo'] as $opcao) {//Inicia um loop para iterar sobre cada valor no array 'campo'e atribuir a variavel $opcao.//
                                //*** Funçoes para verificar se o valor atual no loop é igual a... Caso for criar cabeçalho na tabela . 
                                if ( $opcao == 'paridadeCompra' ) {?>
                                    <th>Paridade de Compra</th> 
                                <?php 
                                }elseif ($opcao == 'paridadeVenda'){?>
                                    <th>Paridade de Venda</th>  
                                <?php 
                                } elseif ($opcao == 'tipoBoletim'){ ?>
                                    <th>Tipo de Boletim</th> 
                                <?php 
                                } elseif ($opcao == 'dataHoraCotacao') {?>
                                    <th>Data e Hora</th> 
                                <?php }
                                //*****//?> 
                                 
                            <?php }?>
                        <?php }?>                                 
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    //***Funções para verificar se o parametro está definido através do método GET****//
                    if(isset($_GET['tipoBol'])){
                        $tipoBol = $_GET['tipoBol'];
                    }else{
                        $tipoBol=null;
                    }

                    if(isset( $_GET["dataI"] )){
                        $dataI= $_GET["dataI"];
                    }else{
                        $dataI = null; 
                    }
                    //****//

                    //**** Funçoes para converter a data original para um carimbo de data/hora Unix. //
                    //**** Função "date" para formatar carimbo de data/hora em uma string no formato "Y m d His"//
                    $dataInicio = strtotime($dataI);
                    $dataInicio = date("Y-m-d H:i:s", $dataInicio );

                    $dataFinal = strtotime($dataF);
                    $dataFinal = date("Y-m-d H:i:s", $dataFinal );
                    //****//

                    // isset para verififar se 'value' foi definido/existe no array '$dados'.
                    //is_array()/is_object()- verifica se o valor é um array ou objeto.
                    if (isset($dados["value"]) && (is_array($dados["value"]) || is_object($dados["value"]))){
                        foreach ($dados["value"] as $valor) {//Itera sobre os elementos do array ou objeto 
                            // isse para Verificar se 'tipoBoletim' existe no array
                            if(isset($valor['tipoBoletim'])){
                                if($tipoBol != $valor['tipoBoletim']){//Função para filtrar tipoBoletim. Verificar se o valor de $tipoBol é diferente do valor do 'tipoBoletim'. Caso nao for, pula a iteração atual do loop.
                                    continue;// pula a iteração atual e passa para proxima
                                }
                            }else{
                                $valor['tipoBoletim']=null;
                            }
                           
                            //Função para filtrar data e hora.
                            if(isset($valor['dataHoraCotacao'])){//verificar se dataHoraCotacao foi definido
                                if($dataInicio >= $valor['dataHoraCotacao']){//Verifica se dataInicio é maior ou igual a dataHoraCotacao . Caso for, pula para proxima iteração atraves de continue
                                    continue;
                                }elseif($dataFinal <= $valor['dataHoraCotacao']){//Verifica se dataFinal é menor ou igual a dataHoraCotacao . Caso for, pula para proxima iteração atraves de continue
                                    continue;
                                }
                            }else{
                                $valor['dataHoraCotacao']=null;
                            }

                            //*** Funções para verificar se o parametro está definido através do método GET.
                            if(isset($valor['paridadeCompra'])){
                                $valor['paridadeCompra'];
                            }else{
                                $valor['paridadeCompra']=null;
                            }
                            
                            if(isset($valor['paridadeVenda'])){
                                $valor['paridadeVenda'];
                            }else{
                                $valor['paridadeVenda']=null;
                            }
                            //****//
                            ?>
                            <tr>
                                <?php foreach ($_GET['campo'] as $opcao) {//Inicia um loop para iterar sobre cada valor no array 'campo'.
                                        //*** Funçoes para verificar se o valor atual no loop é igual a 'paridadeCompra'. Caso for gera o valor de 'paridadeCompra' do array dentro de uma célula da tabela .
                                        if ( $opcao == 'paridadeCompra' ) { ?>
                                            <td><?= $valor['paridadeCompra'] //  ?></td>
                                        <?php 
                                        }elseif ($opcao == 'paridadeVenda'){?>
                                            <td><?=$valor['paridadeVenda']?></td>  
                                        <?php 
                                        } elseif ($opcao == 'tipoBoletim'){ ?>
                                            <td><?= $valor['tipoBoletim'] ?></td> 
                                        <?php 
                                        } elseif($opcao == 'dataHoraCotacao') {?>
                                            <td><?=$valor['dataHoraCotacao'] ?></td> 
                                        <?php }
                                        //***//?>  
                                <?php }?>                                       
                            </tr> 
                        <?php } 
                    } else{
                        echo "Dados não definidos!!";//Emitir mensagem de erro caso dados não forem definidos
                    }?>
                    
                </tbody>
            </table>
        </pre>
    </section>
</body>
</html>