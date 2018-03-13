<?php
namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TicketController extends Controller
{
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
                    $v2 = 0;
                    //PERCORRE ARRAY DE PARÂMETROS
                    foreach($arrConfig[0] as $rate => $config){

                        //BUSCA A OCORRÊNCIA DAS PALAVRAS-CHAVE
                        foreach($value as $v => $ta){
                            foreach($ta as $ti => $tv ){
                                foreach($config as $cp => $cv){

                                    //TESTA OCORRÊNCIAS
                                    if($cv["modo"] == "match"){
                                        $v1 = preg_match($cv["valor"],$tv);
                                        if($v1 > 0){
                                            $v2 += $v1;
                                        }
                                    }

                                    if($cv["modo"] == "diff"){
                                        preg_match($cv["valor"],$ticket["DateCreate"],$arrDt);
                                        $dt1 = Carbon::createFromFormat('Y-m-d', $arrDt[0]);
                                        if($dtHoje->diffInDays($dt1) >= $mediaDias){
                                            $v2++;
                                        }
                                    }

                                    if($v2 > 0){
                                        $arrMatriz[$ticket["TicketID"]][$rate] = $v2;
                                    }
                                }
                            }
                        }
                    }
                }

            }

        }

        dd($arrMatriz);

       //return view()->with('tickets',$tickets);
   }

}
?>