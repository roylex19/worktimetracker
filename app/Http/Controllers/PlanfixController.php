<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Project;
use Illuminate\Http\Request;
use SimpleXMLElement;

class PlanfixController extends Controller
{
    public function getTasksList(){

        $api_server = 'https://api.planfix.ru/xml/';
        $api_key = 'c24d51741413d9ea9bd4416be639f6fc';
        $api_token = '857b9ef1bbc97b58392e5110ceaf25c1';

        $requestXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><request method="task.getList"><account></account><pageSize>100</pageSize><filters><filter><type>12</type><operator>equal</operator><value><datetype>today</datetype></value></filter></filters></request>');

        $requestXml->account = 'nialcom';
        //$requestXml->pageCurrent = 1;
// остальные параметры являются необязательными, поэкспериментируйте сами

        $ch = curl_init($api_server);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // не выводи ответ на stdout
        curl_setopt($ch, CURLOPT_HEADER, 1);   // получаем заголовки
// не проверять SSL сертификат
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
// не проверять Host SSL сертификата
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $api_key . ':' . $api_token);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXml->asXML());

        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $responseBody = substr($response, $header_size);

        curl_close($ch);

        $xml = simplexml_load_string($responseBody);

        $projects = [];

        foreach ($xml->tasks->task as $a){
            if(preg_match_all("/^\d{3}-\d{3}/", $a->title, $out)) {
                if(in_array($out[0][0], $projects))
                    continue;
                $projects[] = $out[0][0];
            }
        }

        foreach ($projects as $project){
            Project::firstOrCreate(['title' => $project]);
        }

    }

    public function getProject(Request $request){
        return response()->json($request);
    }
}
