<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require __DIR__.'/vendor/autoload.php';


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals();

switch($request->request->get('source')) {
    case 'eric_antonello':
        sendEricMail($request);
        break;
    default:
        sendSposiMail($request);
        break;
}

function sendSposiMail($request)
{
    if($request->request->has('name') && $request->request->has('email') && $request->request->has('subject') && $request->request->has('message')) {

        // Create the Transport
        $transport = (new Swift_SmtpTransport('smtp.googlemail.com', 465, 'ssl'))
          ->setUsername('saraenricosposi')
          ->setPassword('feta108joy')
        ;

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new Swift_Message($request->request->get('subject')))
          ->setFrom([$request->request->get('email') => $request->request->get('name')])
          ->setTo(['eantonel@gmail.com', 'tavcar.sara@gmail.com'])
          ->setBody($request->request->get('message'));

        // Send the message
        $result = $mailer->send($message);

        $response = new Response(
            'Grazie per aver inviato il messaggio! Ti rispondiamo a brevissimo :)',
            Response::HTTP_OK,
            array('content-type' => 'text/html')
        );

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->send();
    }
}


function sendEricMail($request)
{
    if($request->request->get('name') && $request->request->get('email') && $request->request->get('message') && checkRecaptcha($request)) {

        // Create the Transport
        $transport = (new Swift_SmtpTransport('smtp.googlemail.com', 465, 'ssl'))
          ->setUsername('saraenricosposi')
          ->setPassword('feta108joy')
        ;

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new Swift_Message('Messaggio dal sito "ericantonello.com"'))
          ->setFrom([$request->request->get('email') => $request->request->get('name')])
          ->setTo(['eantonel@gmail.com'])
          ->setBody($request->request->get('message'));

        // Send the message
        $result = $mailer->send($message);

        $response = new Response(
            'ok',
            Response::HTTP_OK,
            array('content-type' => 'text/html')
        );
    } else {
        $response = new Response(
            'fail',
            Response::HTTP_BAD_REQUEST,
            array('content-type' => 'text/html')
        );
    }

    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->send();
}

function checkRecaptcha($request)
{
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'secret'=> '6Lezy1MUAAAAAMEY5XhVBuy8-HXEQOBbeSBj7sNy',
        'response' => $request->request->get('g-recaptcha-response')
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response);

    if(!$data->success) {
        return false;
    }

    return true;
}