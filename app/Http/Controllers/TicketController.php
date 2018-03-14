<?php
namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TicketController extends Controller
{
    private function carregaParam(){
        $arrConfig = json_decode(Storage::disk('local')->get('config.json', 'Contents'),true);
        foreach($arrConfig as $pos => $arrConf) {
            foreach ($arrConf as $rate => $config) {
                foreach ($config as $cp => $cv) {
                    if(count($cv)>1){
                        $arrParam[$cv["param"]] = 0;
                    }
                }
            }
        }
        return $arrParam;
    }

   public function rateTickets(){

        //CONVERTE JSON DE TICKETS PARA ARRAY
        $arrTicket = json_decode(Storage::disk('local')->get('tickets.json', 'Contents'),true);

        //CONVERTE JSON DE PARÂMETROS PARA ARRAY
        $arrConfig = json_decode(Storage::disk('local')->get('config.json', 'Contents'),true);

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
                            $v2 = 0;
                            $v1 = 0;
                            $arrParam = $this->carregaParam();
                            $i = 1;

                            //BUSCA A OCORRÊNCIA DAS PALAVRAS-CHAVE
                            foreach ($value as $v => $ta) {
                                foreach ($ta as $ti => $tv) {
                                    foreach ($config as $cp => $cv) {

                                        if (count($cv) > 1) {
                                            //TESTA OCORRÊNCIAS
                                            if ($cv["modo"] == "match") {
                                                $v1 += preg_match($cv["valor"], $tv);
                                                if ($v1 > 0 && $arrParam[$cv["param"]] == 0) {
                                                    $v2++;
                                                    $arrParam[$cv["param"]]++;
                                                }
                                            }

                                            if ($cv["modo"] == "mismatch" && $arrParam[$cv["param"]] == 0) {
                                                $v1 += preg_match($cv["valor"], $tv);
                                                if (count($value) > $i) {
                                                    $i++;
                                                } else if ($v1 == 0) {
                                                    $v2++;
                                                    $arrParam[$cv["param"]]++;
                                                }
                                            }

                                            if ($cv["modo"] == "compara") {
                                                if ($cv["param"] == "iteracao" && $arrParam[$cv["param"]] == 0) {
                                                    if (count($value) == (int)$cv["valor"]) {
                                                        $v2++;
                                                        $arrParam[$cv["param"]]++;
                                                    }
                                                }
                                            }

                                            //TESTA DATA DE CRIAÇÃO
                                            if ($cv["modo"] == "diff" && $arrParam[$cv["param"]] == 0) {
                                                preg_match($cv["valor"], $ticket["DateCreate"], $arrDt);
                                                $dt1 = Carbon::createFromFormat('Y-m-d', $arrDt[0]);
                                                if ($dtHoje->diffInDays($dt1) >= $mediaDias) {
                                                    $v2++;
                                                    $arrParam[$cv["param"]]++;
                                                }
                                            }
                                        }

                                        if (count($cv) == 1) {
                                            $constante = $cv["constante"];
                                        }

                                        if ($v2 > 0 && $constante > 0) {
                                            $arrMatriz[$ticket["TicketID"]][$rate] = ((($v2 * 100) / $constante) + (($v1 / 100) * $constante));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }

        foreach($arrMatriz as $id => $arrRates){
            foreach($arrTicket as $item => $ticket){
                foreach($arrRates as $rate => $valor){
                    if($ticket["TicketID"] == $id){
                        if($valor > 70){
                            $arrTicket[$item]["prioridade"] = $rate;
                        } else{
                            $arrTicket[$item]["prioridade"] = array_keys($arrRates,max($arrRates))[0];
                        }
                    }
                }
            }
        }

        dd($arrTicket);

       //return view()->with('tickets',$tickets);
   }

}
?>