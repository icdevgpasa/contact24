<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Dialogflow\Action\Responses\BasicCard;
use Dialogflow\Action\Questions\ListCard;
use Dialogflow\Action\Questions\ListCard\Option;

function email($subject,$template,$phone) {
$content = '';
if ($template=='1') {
	$content = "Dzień dobry,\r\n\r\nProszę o kontakt z psychoonkologiem.\r\n\r\n ".$phone;
}
if ($template=='2') {
	$content = "Dzień dobry,\r\n\r\nProszę o kontakt w sprawie zostania podopiecznym Fundacji.\r\n\r\n".$phone;
}
if ($template=='3') {
	$content = "Dzień dobry,\r\n\r\nProszę o kontakt w sprawie akcji daj włos.\r\n\r\n".$phone;
}
if ($template=='4') {
	$content = $subject."\r\n\r\n".$phone;
	$subject = 'Prośba o kontakt z infolinii';
}
if ($template=='5') {
	$content = "Dzień dobry,\r\n\r\nProszę o kontakt telefoniczny.\r\n\r\n".$phone;
}
if ($template=='6') {
	$content = $subject;
	$subject = 'Prośba o kontakt telefoniczny';
}
$params = ['content' => $content, 'phone'=>$phone,'subject'=>$subject];
 Mail::send(
          ['text' => 'mail'], //e.g post/mail.blade.php <view file mentioned here>
          $params,
          function($message) use ($params){
              $message->to('raknroll_bot@wp.pl','raknroll_bot@wp.pl');
              $message->subject($params['subject']);
              $message->from('andrzej.gorecki@gpasa.com','RakNRoll Bot');
          }
       );
}
Route::get('/', function () {
    return view('welcome');
});

Route::get('/logs', function() {
$d = file_get_contents ( '/home/rakroll/storage/logs/laravel.log');
$x = explode("\n",$d);
foreach (array_reverse($x) as $line) {
$output.= "<div>".$line."</div>";
}
return view('logs',compact('output'));
});

Route::get('/process_voicebot_2', function() {
$content = "<Response><Say language='pl-PL'>Niestety! Opcja nie została rozpoznana. Do widzenia!</Say><Hangup/></Response>";
if ($_REQUEST['Digits'][0]=='1') { 
		$content ='Możesz to sprawdzić dzięki narzędziu Sprawdź Włos na naszej stronie: aplikacja kropka daj myślink włos kropka pl. Jeżeli nie ma Cię w bazie, a Twoje włosy były wysłane ponad 2 miesiące temu, podaj swój numer na klawiaturze numerycznej i zakończ krzyżykiem, oddzwonimy.';
		$resp = "<Response><Gather action='/process_voicebot_mail/3' method='GET' input='dtmf' timeout='10' language='pl-PL'><Say language='pl-PL'>".$content."</Say></Gather><Hangup/></Response>";
}
if ($_REQUEST['Digits'][0]=='2') { 
	$content = 'Włosy na peruki muszą mieć minimum 25 cm długości, być umyte bez odżywki, splecione w warkocze i nie mogą być rozjaśniane. Jeżeli twoje włosy spełniają powyższe warunki możesz zostać naszym darczyńcą. Wypełnij oświadczenie on-line na stronie i wyślij je razem z włosami. Lokalizacje współpracujących salonów fryzjerskich i wszystkie szczegóły  znajdziesz na stronie raknroll.pl/dajwlos';
	$resp = "<Response><Say language='pl-PL'>".$content."</Say><Hangup/></Response>";
}
return response($resp)->header('Content-Type','application/xml; charset=utf-8');


});

Route::get('/process_voicebot_4', function() {
$content = $_REQUEST['SpeechResult'];
file_put_contents('/home/rakroll/storage/'.$_REQUEST['CallSid'].'-01.log',$content);
$content =  "<Response><Gather action='/process_voicebot_mail/4' method='GET' input='dtmf' timeout='10' language='pl-PL'><Say language='pl-PL'>Proszę podaj swój numer na klawiaturze numerycznej i zakończ krzyżykiem. Oddzwonimy!</Say></Gather></Response>";
return response($content)->header('Content-Type','application/xml; charset=utf-8');
});


Route::get('/process_voicebot_mail/{number}',function($number) {
	if ($number==1) {
		$phone = str_replace("@sip.zadarma.com","",$_REQUEST['Caller']);
		$phone = str_replace("sip:","",$phone);
		$phone = "Numer który podał użytkownik: ".$_REQUEST['Digits'];
		email('Prośba o kontakt z psychoonkologiem','1',$phone);
		$content = "<Response><Say language='pl-PL'>Zapisałam. Skontaktujemy się w najbliższym możliwym terminie.</Say><Hangup/></Response>";
		return response($content)->header('Content-Type','application/xml; charset=utf-8');
	}
	if ($number==2) {
		$phone = str_replace("@sip.zadarma.com","",$_REQUEST['Caller']);
		$phone = str_replace("sip:","",$phone);
		$phone = "Numer który podał użytkownik: ".$_REQUEST['Digits'];
		email('Prośba o kontakt w sprawie podopiecznych','2',$phone);
		$content = "<Response><Say language='pl-PL'>Zapisałam. Skontaktujemy się w najbliższym możliwym terminie.</Say><Hangup/></Response>";
		return response($content)->header('Content-Type','application/xml; charset=utf-8');
	}
        if ($number==3) {
                $phone = str_replace("@sip.zadarma.com","",$_REQUEST['Caller']);
                $phone = str_replace("sip:","",$phone);
                $phone = "Numer który podał użytkownik: ".$_REQUEST['Digits'];
                email('Prośba o kontakt w sprawie akcji daj włos','3',$phone);
		$content = "<Response><Say language='pl-PL'>Zapisałam. Skontaktujemy się w najbliższym możliwym terminie.</Say><Hangup/></Response>";
                return response($content)->header('Content-Type','application/xml; charset=utf-8');
        }
	if ($number==4) {
                $phone = str_replace("@sip.zadarma.com","",$_REQUEST['Caller']);
                $phone = str_replace("sip:","",$phone);
                $phone = "Numer który podał użytkownik: ".$_REQUEST['Digits'];
		//TU MUSI BYC KONTENT Z POPRZEDNIEGO REQUESTU
		$content = file_get_contents('/home/rakroll/storage/'.$_REQUEST['CallSid'].'-01.log');
                email($content,'4',$phone);
		$content = "<Response><Say language='pl-PL'>Zapisałam. Skontaktujemy się w najbliższym możliwym terminie.</Say><Hangup/></Response>";
                return response($content)->header('Content-Type','application/xml; charset=utf-8');
        }


});

Route::get('/process_voicebot', function() {
Log::info('Processing voicebot command',Input::all());

$option = 0;

if (($_REQUEST['Digits'][0]=='1') || (strpos($_REQUEST['SpeechResult'],'wsparcia'))) { $option=1;}
if (($_REQUEST['Digits'][0]=='2') || (strpos($_REQUEST['SpeechResult'],'podopiecznym'))) { $option=2;}
if (($_REQUEST['Digits'][0]=='3') || (strpos($_REQUEST['SpeechResult'],'daj włos'))) { $option=3;}
if (($_REQUEST['Digits'][0]=='5') || (strpos($_REQUEST['SpeechResult'],'inne'))) { $option=4;}
if (($_REQUEST['Digits'][0]=='4') || (strpos($_REQUEST['SpeechResult'],'inne'))) { $option=5;}


switch($option) {
	case 1:
		$content =  "<Response><Gather action='/process_voicebot_mail/1' method='GET' input='dtmf' timeout='10' language='pl-PL'><Say language='pl-PL'>Oferujemy bezpłatne konsultacje z psychoonkologiem. Proszę podaj swój numer na klawiaturze numerycznej i zakończ krzyżykiem, oddzwonimy do Ciebie</Say></Gather><Say language='pl-PL'>Dziękujemy! Do usłyszenia!</Say></Response>";
		break;
	case 2:
		$content = "<Response><Gather action='/process_voicebot_mail/2' method='GET' input='dtmf' timeout='10' language='pl-PL'><Say language='pl-PL'>Aby zostać podopiecznym wypełnij formularz, który znajdziesz na naszej stronie internetowej w zakładce szukam pomocy, zostań podopiecznym. Jeżeli masz dodatkowe pytania, podaj swój numer na klawiaturze numerycznej i zakończ krzyżykiem, oddzwonimy.</Say></Gather></Response>";
		break;
	case 3:
		$content =  "<Response><Gather action='/process_voicebot_2' method='GET' input='dtmf' timeout='10' numDigits='1' language='pl-PL'><Say language='pl-PL'>Jeżeli chcesz sprawdzić, czy dotarły do nas twoje włosy wybierz 1, jeżeli chcesz podarować włosy wybierz 2.</Say></Gather></Response>";
		break;
	case 4:
		$content = "<Response><Gather action='/process_voicebot_4' method='GET' input='speech' timeout='10' speechTimeout='auto' language='pl-PL'><Say language='pl-PL'>Opisz proszę krótko czego chcesz się dowiedzieć.</Say></Gather></Response>";
		break;
	case 5:
		$content= "<Response><Say language='pl-PL'>Jeśli chcesz podjąć współpracę biznesową to wyślij nam email na adres biznes@raknroll.pl.</Say><Hangup/></Response>";
		break;
	default:
		$content = "<Response><Say language='pl-PL'>Wystąpił błąd. Do widzenia!.</Say></Response><Hangup/>";
}

//$content =  "<Response><Say language='pl-PL'>Wcisnąłęś " . $_REQUEST['Digits'] . "</Say></Response>";
return response($content)->header('Content-Type','application/xml; charset=utf-8');

});
Route::post('/voicebot', function(Request $request) {
$content = "
<Response>
<Gather action='/process_voicebot' method='GET' input='dtmf' timeout='5' numDigits='1' language='pl-PL'>
    <Say language='pl-PL'>Cześć, jestem asystentką głosową i postaram Ci się pomóc poza godzinami pracy Fundacji. Wybierz jedną z opcji: Potrzebuję wsparcia wciśnij 1.  Chce zostać podopiecznym fundacji wciśnij 2. Program Daj Włos! wciśnij 3.
Kontakt biznesowy - wciśnij 4. Inne - wciśnij 5.</Say>
</Gather>
<Say language='pl-PL'>Niestety , nie otrzymalem żadnej opcji! . Do widzienia!</Say>
</Response>
";
return response($content)->header('Content-Type','application/xml; charset=utf-8');
});

Route::post('/dialogflow', function(Request $request) {
	Log::info('Processing dialogflow command',Input::all());
	$df_org =  json_decode(request()->getContent());
	$df = $df_org->queryResult->intent->displayName;
	Log::info('Decoding dialogflow command');
	Log::info($df);
	if ($df=='Default Welcome Intent') {
	$agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	Log::info(intval(date('H')));
	if ((intval(date('H'))>16) || (intval(date('H'))<9)) {
	$agent->reply("Cześć! Miło nam, że chcesz się z nami skontaktować. Chcesz porozmawiać z naszym. Chat'n'Roll botem czy wolisz kontakt z człowiekiem?");
	$suggestion = \Dialogflow\RichMessage\Suggestion::create(['Człowiek','Chatbot']);
	$agent->reply($suggestion);
	Log::info($agent->getRequestSource());
        $agent->reply(\Dialogflow\RichMessage\Payload::create(

array (
  'message' => 'Do you want more updates?',
  'platform' => 'kommunicate',
  'metadata' => 
  array (
    'contentType' => '300',
    'templateId' => '6',
    'payload' => 
    array (
      0 => 
      array (
        'title' => 'Yes',
        'message' => 'Cool! send me more.',
      ),
      1 => 
      array (
        'title' => 'No',
        'message' => 'Not at all',
        'replyMetadata' => 
        array (
          'KM_CHAT_CONTEXT' => 
          array (
            'buttonClicked' => true,
          ),
        ),
      ),
    ),
  ),
)

	));
	
	}
	else {
	$agent->reply('Cześć! Jestem Chat\'n\'Roll i chętnie odpowiem na Twoje pytania. W czym mogę pomóc? ');
	$suggestion = \Dialogflow\RichMessage\Suggestion::create(['Wsparcie psychologiczne', 'Daj Włos!','Odbierz perukę','Zostań podopiecznym','Godziny pracy biura/skontaktuj się bezpośrednio']);
	$agent->reply($suggestion);
	}
	}
	elseif ($df=='Default Chatbot Intent') {
	$agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Cześć! Jestem Chat\'n\'Roll i chętnie odpowiem na Twoje pytania. W czym mogę pomóc? ');
	$suggestion = \Dialogflow\RichMessage\Suggestion::create(['Wsparcie psychologiczne', 'Daj Włos!','Odbierz perukę','Zostań podopiecznym','Godziny pracy biura/skontaktuj się bezpośrednio']);
	$agent->reply($suggestion);
	
	}
	elseif ($df=='Default Fallback Intent') {
	$message = $df_org->queryResult->queryText;
	$x = new \App\Misunderstood();
	$x->tekst = $message;
	$x->save();

	$agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Niestety na razie potrafię pomóc tylko w powyższych kwestiach. Czy chcesz skontaktować się z nami bezpośrednio?');
	$suggestion = \Dialogflow\RichMessage\Suggestion::create(['Tak','Nie']);
	$agent->reply($suggestion);
	
	}
        elseif ($df=='Default Fallback Intent - Contact YES') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	if ((intval(date('H'))>16) || (intval(date('H'))<9)) { 
		$agent->reply('Aby połączyć się z pracownikiem fundacji kliknij przycisk');
       		 $suggestion = \Dialogflow\RichMessage\Suggestion::create(['Połącz z pracownikiem fundacji']);
	        $agent->reply($suggestion);

	} else {
  		$agent->reply('Biuro Fundacji jest czynne od poniedziałku do piątku od 9 do 16, możesz do nas napisać lub zadzwonić. Możemy też oddzwonić do Ciebie w godzinach pracy.');
       		 $suggestion = \Dialogflow\RichMessage\Suggestion::create(['Kontakt do biura','Oddzwońcie']);
	        $agent->reply($suggestion);
	}

        }

        elseif ($df=='Default Fallback Intent - Contact NO') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Może w przyszłości będę w stanie pomóc w innych kwestiach. Dzięki za wspólne doświadczenie i życzę miłego dnia');
        }

        elseif ($df=='Default Fallback Intent - Contact Office Info') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('al. Wilanowska 313A, 02-665 Warszawa tel. 22 841-27-47 e-mail: biuro@raknroll.pl');
        }

        elseif ($df=='Default Fallback Intent - Contact Callback') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Zapisane! Napewno oddzwonimy w najbliższym momencie!');
	$telefon = '';
	$message = $df_org->queryResult->queryText;
	email($message,'6',$telefon);
        }
	elseif ($df=='Contact') {
	        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
		$card = \Dialogflow\RichMessage\Card::create()
		    ->title('Fundacja RakNRoll')
    		    ->image('https://geo1.ggpht.com/cbk?panoid=0jS2nPczwnNL4_IwKJmDHg&output=thumbnail&cb_client=search.gws-prod.gps&thumb=2&yaw=200.7367&pitch=0&thumbfov=100&w=128&h=128')
    		    ->button('Pokaż adres na mapie', 'https://goo.gl/maps/tAWmCLxYUNwqdHHL6');
		$agent->reply('Możesz się z nami skontaktować od poniedziałku do piątku w godzinach 9 - 16. al. Wilanowska 313A, 02-665 Warszawa tel. 22 841-27-47 e-mail: biuro@raknroll.pl');
		$agent->reply('Jeżeli potrzebujesz wsparcia psychologa lub psychoonkologa zadzwoń do nas w środę pomiędzy 19 a 21 na numer: 500 459 450');
		$agent->reply($card);
		anything_else($agent);
	}
	elseif ($df=='Pupil') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
        $agent->reply('Fundacja może wspierać Cię w tworzeniu zbiórek na leczenie onkologiczne i przyjmować nowe zgłoszenia. Aby zacząć Ci pomagać musisz wypełnić formularz :)');
	$suggestion = \Dialogflow\RichMessage\Suggestion::create(['Formularz','Kontakt do koordynatorek','Informacje szczegółowe']);
	$agent->reply($suggestion);
        }
        elseif ($df=='Pupil - Form') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$card = \Dialogflow\RichMessage\Card::create()
                    ->title('Zostań podopiecznym')
		    ->image('http://2018.igem.org/wiki/images/f/f7/Safety_form.svg')
                    ->button('Kliknij aby otworzyć formularz', 'https://www.raknroll.pl/szukam-pomocy/zostan-podopiecznym/#formularz');
        $agent->reply($card);
	anything_else($agent);
        }
	elseif ($df=='Pupil - Contact') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Jeżeli chcesz się z nami skontaktować możesz zadzwonić do:');
	$agent->reply('Beaty Wach tel. 510 912 652,');
	$agent->reply('Agnieszki Mirowskiej, tel. 881 922 911,');
	$agent->reply('Aleksandry Skwarek tel. 500 014 416.');
	$agent->reply("Telefon stacjonarny, biuro Rak'n'Roll: (22) 841-27-47");
	anything_else($agent);
        }

	elseif ($df=='Pupil - Details') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
        $agent->reply('Wszystkie informacje na temat bycia podopiecznym i procesu mozesz znaleźć na naszej stronie https://raknroll.pl');
        }
	elseif ($df=='Wig Pickup') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Dzięki programowi Daj Włos! osoby w trakcie leczenia mogą otrzymać za darmo perukę naturalną, a od listopada 2020 r. również z włosów syntetycznych');
	$suggestion = \Dialogflow\RichMessage\Suggestion::create(['Co zrobić aby otrzymać perukę?','Z kim mogę porozmawiać','Jakie peruki są dostępne','Czy mogę zmienić zdanie?','Jak długo będę czekać?']);
	$agent->reply($suggestion);
        }
	elseif ($df=='Wig Pickup - WhatToDo') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Wypełnić formularz :) jest dostępny tutaj: https://www.raknroll.pl/szukam-pomocy/odbierz-peruke/#formularz Spokojnie, zawsze możesz zmienić zdanie. A potem postępować zgodnie z listą dostępną tutaj: https://www.raknroll.pl/szukam-pomocy/odbierz-peruke/#tresc');
        anything_else($agent);
	}
	elseif ($df=='Wig Pickup - WhoToTalk') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Odpowiedzi na najczęściej zadawane pytania możesz znaleźć na naszej stronie https://www.raknroll.pl/szukam-pomocy/odbierz-peruke/#gallery-1');
	$agent->reply('Czy chcesz skontaktować się z koordynatorką programu? ');
	$suggestion = \Dialogflow\RichMessage\Suggestion::create(['Tak','Nie']);
	$agent->reply($suggestion);
	}
        elseif ($df=='Wig Pickup - WhoToTalk - yes') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Koordynatorką programu jest Kamila Stępień. Możesz napisać do niej maila na adres: kamila@raknroll.pl, lub zadzwonić pod numer: 666 099 112');
        anything_else($agent);
        }
        elseif ($df=='Wig Pickup - WhoToTalk - no') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
        anything_else($agent);
        }
	elseif ($df=='Wig Pickup - WigTypes') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('W naszym programie mamy peruki naturalne i syntetyczne. Szczegółowe informacje możesz znaleźć tutaj: https://www.raknroll.pl/szukam-pomocy/odbierz-peruke/#gallery-1');
        anything_else($agent);
        }
	elseif ($df=='Wig Pickup - ChangeMyMind') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Tak :) w każdej chwili możesz się rozmyślić. Jeżeli chcesz zrezygnować z odbioru peruki niezwłocznie poinformuj o tym Kamilę Stępień (kamila@raknroll.pl). Dzięki temu inna osoba potrzebująca będzie mogła mieć możliwość wcześniejszego odbioru. Dziękujemy!');
        anything_else($agent);
        }
	elseif ($df=='Wig Pickup - WaitTime') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('To zależy, czas oczekiwania zmienia się w zależności od miasta. Opiekunka programu Kamila Stępień skontaktuje się z Tobą po otrzymaniu formularza i poinformuje jak wygląda kolejka w mieście, które wybrałaś/łeś.');
        anything_else($agent);
        }
	elseif ($df=='Support') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Dla wszystkich tych, którzy zmagają się z chorobą nowotworową oraz dla rodzin i bliskich oferujemy różne formy bezpłatnego kontaktu');
	$suggestion = \Dialogflow\RichMessage\Suggestion::create(["Linia Pomocowa Rak'n'Roll",'wsparcie psychologa lub psychoonkologa']);
	$agent->reply($suggestion);
        }
	elseif ($df=='Support - HelpLine') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply("Uruchomiliśmy Linię Pomocową Rak’n’Roll, pod którą możecie dzwonić w każdą środę w godzinach od 19:00 do 21:00.");
        $agent->reply("Pod numerem telefonu: 500 459 450 nasi psycholodzy i psychoonkolodzy czekają na Wasze pytania!");
        anything_else($agent);
        }
	//Support - Psychologist
	elseif ($df=='Support - Psychologist') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
        $agent->reply('Zapisane! Napewno oddzwonimy w najbliższym momencie!');
        $telefon = $df_org->queryResult->parameters->telefon;
        email("Prośba o kontakt telefoniczny wsparcie psychoonkologa z Chat'N'Roll",'5',$telefon);
	anything_else($agent);
	}
	//GiveMeHair
	elseif ($df=='GiveMeHair') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Co chcesz zrobić?');
        $suggestion = \Dialogflow\RichMessage\Suggestion::create(['Oddać włosy','sprawdzić czy Twoje włosy dotarły','odebrać perukę']);
        $agent->reply($suggestion);
        }
	//GiveMeHair - Give
	elseif ($df=='GiveMeHair - Give') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
        $agent->reply('Czy Twoje włosy są rozjaśniane?');
	$suggestion = \Dialogflow\RichMessage\Suggestion::create(['Tak','Nie']);
        $agent->reply($suggestion);
        }
	elseif ($df=='GiveMeHair - GETTHERE') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Tutaj możesz sprawdzić, czy Twoje włosy dotarły: https://aplikacja.daj-wlos.pl/');
        anything_else($agent);
        }
	//GiveMeHair - Give - yes
	elseif ($df=='GiveMeHair - Give - yes') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
        $agent->reply('Jeśli Twoje włosy były kiedykolwiek rozjaśniane – niestety, ich struktura nie pozwala na wykorzystanie ich na perukę. Możesz pomóc chorym kupując warkocz na stronie lub wspierając Fundację Rak’n’Roll w inny sposób https://www.raknroll.pl/chce-pomoc/');
        anything_else($agent);
        }
	elseif ($df=='GiveMeHair - Give - no') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Czy Twoje włosy mają powyżej 25 cm długości do ścięcia?');
	$suggestion = \Dialogflow\RichMessage\Suggestion::create(['Tak','Nie']);
        $agent->reply($suggestion);
        }
        //GiveMeHair - Give - no - no
	elseif ($df=='GiveMeHair - Give - no - no') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Ścinane włosy muszą mieć 25 cm lub więcej. Nie może być mniej. Nie jest tu istotne, że bardzo chcesz lub masz specjalną okazję. Długość włosów nie jest przedmiotem negocjacji. Jeśli będą miały mniej, nie będzie można z nich utkać peruki.');
	anything_else($agent);
        }
	elseif ($df=='GiveMeHair - Give - no - yes') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Super! Wygląda na to, że możesz podarować swoje włosy. Gdzie chcesz je ściąć?');
        $suggestion = \Dialogflow\RichMessage\Suggestion::create(['samodzielnie','u swojego fryzjera','w salonie partnerskim']);
        $agent->reply($suggestion);
        }
	//GiveMeHair - Give - no - yes - ByMyself
	elseif ($df=='GiveMeHair - Give - no - yes - ByMyself') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('OK! Po pierwsze przeczytaj instrukcję: https://www.raknroll.pl/wp-content/uploads/2020/06/instrukcja-%C5%9Bcinania-w%C5%82os%C3%B3w.pdf');
	$agent->reply('Po drugie: zetnij włosy :)');
	$agent->reply('Po trzecie: Pobierz i wypełnij oświadczenie: https://daj-wlos.pl/');
	$agent->reply('Po czwarte: Wyślij włosy razem z oświadczeniem na adres Fundacji: al. Wilanowska 313A, 02-665 Warszawa');
	anything_else($agent);
        }
	//GiveMeHair - Give - no - yes - Hairdresser
	elseif ($df=='GiveMeHair - Give - no - yes - Hairdresser') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('OK! Po pierwsze ściągnij i wydrukuj instrukcję: https://www.raknroll.pl/wp-content/uploads/2020/06/instrukcja-%C5%9Bcinania-w%C5%82os%C3%B3w.pdf');
        $agent->reply('Po drugie: umów się do swojego fryzjera i uprzedz go, że będziesz ścinać włosy dla Fundacji. To nic trudnego! Masz szczegółową instrukcję :)');
        $agent->reply('Po trzecie: Pobierz i wypełnij oświadczenie: https://daj-wlos.pl/');
        $agent->reply('Po czwarte: Pójdź do fryzjera razem z oświadczeniem. Nie zapomnij o gumkach na obydwu końcach warkoczyków.');
        $agent->reply('Po piąte: Wyślij włosy razem z oświadczeniem na adres Fundacji: al. Wilanowska 313A, 02-665 Warszawa');
	anything_else($agent);
        }
	//GiveMeHair - Give - no - yes - PartnerSalon
	elseif ($df=='GiveMeHair - Give - no - yes - PartnerSalon') {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('OK! Po pierwsze znajdz salon partnerski, możesz je znaleźć tutaj: https://www.raknroll.pl/wp-content/uploads/2020/10/lista-Salony-30-10-20.pdf');
	$agent->reply('Po drugie: umów się do salonu. Umawiając się na wizytę ustal szczegóły jak w danym salonie wygląda przeprowadzenie usługi.');
	$agent->reply('Czy włosy strzyżone są odpłatnie, ze zniżka czy bezpłatnie? Czy salon samodzielnie wysyła włosy czy robi to darczyńca?');
	$agent->reply('Po trzecie: Zetnij włosy w salonie (na miejscu powinno być oświadczenie)');
	$agent->reply('Jeżeli salon samodzielnie przesyła włosy to nie później niż 10 dni od ścięcia wypisz oświadczenie i pozostaw włosy wraz z dokumentem w salonie. Oni prześlą je do
Fundacji.');
	$agent->reply('Jeżeli salon nie wysyła to odbierz od nich włosy w kopercie i razem z oświadczeniem wyślij nam na adres Fundacji: al. Wilanowska 313A, 02-665 Warszawa');
	anything_else($agent);
        }
	else {
        $agent = \Dialogflow\WebhookClient::fromData($request->json()->all());
	$agent->reply('Przepraszam, niezrozumiałem tego :/ Mam jakiś problem ze sobą...');
	anything_else($agent);
	}
	Log::info('Prepared output');
	$output = $agent->render();
	if ($df_org->originalDetectIntentRequest->payload->messageSource=='8') {
	$output['fulfillmentMessages'][1]['quickReplies']['platform']='FACEBOOK';
	$output['fulfillmentMessages']['platform']='FACEBOOK';
	}
	Log::info($output);
	return response()->json($output);
});

function anything_else($agent) {
	$agent->reply('Czy mogę pomóc Ci w czymś jeszcze? Mogę porozmawiać na te tematy:');
	$suggestion = \Dialogflow\RichMessage\Suggestion::create(['Wsparcie psychologiczne', 'Daj Włos!','Odbierz perukę','Zostań podopiecznym','Godziny pracy biura/skontaktuj się bezpośrednio']);
	$agent->reply($suggestion);
}
