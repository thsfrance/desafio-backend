<?php
namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Mockery\Exception;

class TicketController extends Controller
{
    private function cmpDataCriacao($arrA,$arrB){
        preg_match("/\b[0-9]{4}-(0[1-9]|1[0,1,2])-([1-9]|0[1-9]|[1,2][0-9]|3[0,1])\b/",$arrA["DateCreate"],$dtA);
        $dtA = new DateTime($dtA[0]);
        preg_match("/\b[0-9]{4}-(0[1-9]|1[0,1,2])-([1-9]|0[1-9]|[1,2][0-9]|3[0,1])\b/",$arrB["DateCreate"],$dtB);
        $dtB = new DateTime($dtB[0]);

        return $dtA > $dtB;
    }

    private function cmpDataAtualizacao($arrA,$arrB){
        preg_match("/\b[0-9]{4}-(0[1-9]|1[0,1,2])-([1-9]|0[1-9]|[1,2][0-9]|3[0,1])\b/",$arrA["DateUpdate"],$dtA);
        $dtA = new DateTime($dtA[0]);
        preg_match("/\b[0-9]{4}-(0[1-9]|1[0,1,2])-([1-9]|0[1-9]|[1,2][0-9]|3[0,1])\b/",$arrB["DateUpdate"],$dtB);
        $dtB = new DateTime($dtB[0]);

        return $dtA > $dtB;
    }

    public function search(Request $request){

        $this->rateTickets();

        $arrTickets = json_decode($this->carregaTickets('tickets.json'),true);
        $arrResult = array();

        foreach($arrTickets as $item => $ticket){
            if(!empty($request->route('start')) && !empty($request->route('end'))){
                preg_match("/\b[0-9]{4}-(0[1-9]|1[0,1,2])-([1-9]|0[1-9]|[1,2][0-9]|3[0,1])\b/",$ticket["DateCreate"],$dateMatch);
                $start = new DateTime($request->route('start'));
                $end = new DateTime($request->route('end'));
                $dateMatch = new DateTime($dateMatch[0]);
               // dd($dateMatch);
                if($dateMatch >= $start && $dateMatch <= $end){
                    $arrResult[$item] = $ticket;
                } else {
                    unset($arrResult[$item]);
                    continue;
                }
            }
            if(!empty($request->route('prioridade'))){
                if(strtoupper($request->route('prioridade')) == strtoupper($ticket["prioridade"])){
                    $arrResult[$item] = $ticket;
                } else {
                    unset($arrResult[$item]);
                    continue;
                }
            }
        }

        if(!empty($request->route('orderby'))){
            if($request->route('orderby') == 'datacriacao'){
                usort($arrResult,array($this,'cmpDataCriacao'));
            }

            if($request->route('orderby') == 'dataatualizacao'){
                usort($arrResult,array($this,'cmpDataAtualizacao'));
            }
        }

        unset($dateMatch);
        dd($arrResult);

    }

    private function carregaTickets($filename){
        $jsonTickets = Storage::disk('local')->get($filename, 'Contents');
        return $jsonTickets;
    }

    private function carregaConfig($filename){
        $jsonConfig = Storage::disk('local')->get($filename, 'Contents');
        return $jsonConfig;
    }

    private function carregaParam($filename,$rate){
        $arrConfig = json_decode(Storage::disk('local')->get($filename, 'Contents'),true);
        $arrResult = array();

        foreach($arrConfig as $item => $arrParam){
            foreach ($arrParam[$rate] as $i => $param){
                foreach($param as $p => $val){
                    if($p == "param"){
                        $arrResult[$val] = 0;
                    }
                }
            }
        }

        return $arrResult;
    }

   public function rateTickets(){

        try{
            //CONVERTE JSON DE TICKETS PARA ARRAY
            $arrTicket = json_decode($this->carregaTickets('tickets.json'),true);

            //CONVERTE JSON DE PARÂMETROS PARA ARRAY
            $arrConfig = json_decode($this->carregaConfig('config.json'),true);

            //ESTABELECE MÉDIA DE DIAS
            $dtHoje = Carbon::today();
            $somaDias = 0;
            foreach($arrTicket as $item => $ticket){
               $dtTicket = Carbon::createFromFormat('Y-m-d H:i:s', $ticket["DateCreate"]);
               $somaDias += $dtHoje->diffInDays($dtTicket);
            }
            $mediaDias = $somaDias/count($arrTicket);
            //////////////////////////////////////

            // PERCORRE ARRAY DE TICKETS
            foreach($arrTicket as $item => $ticket){
                foreach($ticket as $fields => $value){
                    if(is_array($value)){

                        //PERCORRE ARRAY DE PARÂMETROS
                        foreach($arrConfig as $pos => $arrConf) {
                            foreach ($arrConf as $rate => $config) {
                                $constante = 0;
                                $v1 = 0;
                                $v2 = 0;
                                $v3 = 0;
                                $score = 0;
                                $arrParam = $this->carregaParam('config.json',$rate);

                                //BUSCA A OCORRÊNCIA DAS PALAVRAS-CHAVE
                                foreach ($value as $v => $ta) {
                                    foreach ($ta as $ti => $tv) {
                                        foreach ($config as $cp => $cv) {

                                            if (count($cv) > 1) {
                                                //TESTA OCORRÊNCIAS
                                                if ($cv["modo"] == "match") {
                                                    $v1 = preg_match($cv["valor"], $tv);
                                                    $v2 += $v1;
                                                    if ($v1 > 0 && $arrParam[$cv["param"]] == 0) {
                                                        $score += $cv["score"];
                                                        $arrParam[$cv["param"]] = 1;
                                                    }
                                                }

                                                if ($cv["modo"] == "mismatch") {
                                                    $v1 = preg_match($cv["valor"], $tv);
                                                    $v2 += $v1;
                                                    if($v1 > 0){
                                                        $arrParam[$cv["param"]]++;
                                                        $v3 =0;
;                                                    }

                                                    if($arrParam[$cv["param"]] == 0){
                                                        $v3 = $cv["score"];
                                                    }

                                                }

                                                if ($cv["modo"] == "compara") {
                                                    if ($cv["param"] == "iteracao") {
                                                        if (count($value) == (int)$cv["valor"] && $arrParam[$cv["param"]] == 0) {
                                                            $score += $cv["score"];
                                                            $arrParam[$cv["param"]] = 1;
                                                        }
                                                    }
                                                }

                                                //TESTA DATA DE CRIAÇÃO
                                                if ($cv["modo"] == "diff") {
                                                    preg_match('/\b'.$cv["valor"].'\b/', $ticket["DateCreate"], $arrDt);
                                                    $dt1 = Carbon::createFromFormat('Y-m-d', $arrDt[0]);
                                                    if ($dtHoje->diffInDays($dt1) >= $mediaDias  && $arrParam[$cv["param"]] == 0) {
                                                        $score += $cv["score"];
                                                        $arrParam[$cv["param"]] = 1;
                                                    }
                                                }
                                            }

                                            if (count($cv) == 1) {
                                                $constante = $cv["constante"];
                                            }
                                        }
                                    }
                                }
                                $score += $v3;

                                $score = $score ? $score : 1;

                                if($constante > 0){
                                    $arrMatriz[$ticket["TicketID"]][$rate] = (($score*100)/$constante)+($v2/$score);
                                }
                            }
                        }
                    }
                }

            }
            //dd($arrMatriz);
            foreach($arrMatriz as $id => $arrRates){
                foreach($arrTicket as $item => $ticket){
                    if($ticket["TicketID"] == $id){
                        $arrTicket[$item]["prioridade"] = array_keys($arrRates,max($arrRates))[0];
                    }
                }
            }

            Storage::disk('local')->put("tickets.json",json_encode($arrTicket));
            //echo "Os registros foram classificados.";
        } catch(Exception $ex){
            throw  new Exception($ex->getMessage(),$ex);
        }
       //return view()->with('tickets',$tickets);
   }

}
?>