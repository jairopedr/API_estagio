<?php 
    function busca_info_moeda () {
        $urlmoeda='https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/Moedas?$top=100&$format=json&$select=simbolo,nomeFormatado';// **Variável que recebe url API da moeda no formato JSON //

        //*** Função para tratar os dados da url***//  
        //json_decode - Pega uma string codificada em JSON e a converte em um valor PHP.
        //file_get_contens - Função para buscar/recuperar o conteúdo da URL especificada
        //O parâmetro "true" indica que os dados JSON vão ser decodificadoss em uma matriz associativa/array.
        return json_decode(file_get_contents($urlmoeda), true);//*** Função para tratar os dados da url***//
    }

    function trata_dados_data_e_hora ($moeda,$dataI,$dataF, $com_hora=false) {
        //*** Funçoes para converter a data original para um carimbo de data/hora Unix.
        $dataInicio = strtotime($dataI); 
        $dataFinal = strtotime($dataF); 
        if ($com_hora) {
            //*** Funçoes date para formatar carimbo de data/hora em uma string no formato "Y-m-d H:i:s"
            $dataInicio = date("Y-m-d H:i:s", $dataInicio );
            $dataFinal = date("Y-m-d H:i:s", $dataFinal );
        } else {
            //*** Funçoes date para formatar carimbo de data/hora em uma string no formato "m d Y"
            $dataInicio = date("m-d-Y", $dataInicio );
            $dataFinal = date("m-d-Y", $dataFinal );
        }

        $url= 'https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoMoedaPeriodo(moeda=@moeda,dataInicial=@dataInicial,dataFinalCotacao=@dataFinalCotacao)?@moeda=\''.$moeda.'\'&@dataInicial=\''.$dataInicio.'\'&@dataFinalCotacao=\''.$dataFinal.'\'&$top=100&$select=paridadeCompra,paridadeVenda,tipoBoletim,dataHoraCotacao';//variável que recebe url da API cotacao

        if($com_hora){//Se '$com_hora' for true, retorna um array contendo as datas de início e término formatadas.
            return [
                'dataInicio'=> $dataInicio,
                'dataFinal'=> $dataFinal,
            ]; 
        }else{//Se não retorna dados da url JSON decodificados
            return json_decode(file_get_contents($url),true);
        }
        
    }

    //Função para filtrar tipoBoletim e dataHoraCotacao
    function filtro_boletim_e_data($valor, $tipo_boletim, $dataInicio, $dataFinal) {
        if (isset($valor['tipoBoletim']) || isset($valor['dataHoraCotacao'])) { //verificar se dataHoraCotacao ou tipoBoletim foi definido
            if ($tipo_boletim != $valor['tipoBoletim'] || $dataInicio >= $valor['dataHoraCotacao'] || $dataFinal <= $valor['dataHoraCotacao']) { //Verifica se tipo Boletim é diferente do valor do 'tipoBoletim'. Caso nao for, return false para pular iteracao. Verifica tambem se dataInicio é (maior ou igual) ou se dataFinal é (menor ou igual) a dataHoraCotacao. Caso for, return false para pular iteração.
                return false; 
            }
        } else {
            //Se 'tipoBoletim' ou 'dataHoraCotacao' não estiver definido, atribui como null
            $valor['tipoBoletim']=null;
            $valor['dataHoraCotacao'] = null;
        } 
        return $valor;          
    }
?>