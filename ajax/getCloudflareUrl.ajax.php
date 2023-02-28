<?php
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Database\SQL\Query;

require_once('UKMconfig.inc.php');

// Hvis getallheaders() er ikke definert
if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}


$uploadLength = getallheaders()['Upload-Length'];
$uploadMetadata = getallheaders()['Upload-Metadata'];

// Det brukes POST fordi WP tillater POST bare
$handleCall = new HandleAPICall([], ['hendelse_id', 'arrangement_id'], ['GET', 'POST'], false);

$hendelse_id = isset($_GET['hendelse_id']) ? $_GET['hendelse_id'] : null;  // brukes mot kobling til et innslag
$arrangement_id = isset($_GET['arrangement_id']) ? $_GET['arrangement_id'] : null;  // brukes for reportasje

if($hendelse_id == null && $arrangement_id == null) {
    $handleCall->sendErrorToClient('hendelse_id eller arrangement_id må sendes som argument', 400);
    die;
}

$creatorId = $hendelse_id ? '-b-' . $hendelse_id : '-p-' . $arrangement_id;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/accounts/'. UKM_CLOUDFLARE_ACCOUNT_ID .'/stream?direct_user=true');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HEADER, 1);


$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'Tus-Resumable: 1.0.0';
// Sender upload creator for å identifisere brukeren og hva det gjelder for -> b = innslag; p = arrangement
$headers[] = 'Upload-Creator: ' . get_current_user_id() . $creatorId;
$headers[] = 'Upload-Length: ' . $uploadLength;
$headers[] = 'Upload-Metadata: ' . $uploadMetadata;
$headers[] = 'Authorization: Bearer '. UKM_CLOUDFLARE_VIDEO_KEY;
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


$response = curl_exec($ch);

// Retudn headers seperatly from the Response Body
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $header_size);
$body = substr($response, $header_size);

list($headers, $body) = explode("\r\n\r\n", $response, 2);


curl_close($ch);



$headersArr = headersToArray($headers);

// Stor og liten bokstav!
$location = $headersArr['Location'] ? $headersArr['Location'] : $headersArr['location'];

header("HTTP/1.1 200 OK");
header("Access-Control-Expose-Headers:Location");
header("Access-Control-Allow-Headers:*");
header("Access-Control-Allow-Origin:*");
header("Location:" . $location, true, 200);


die;

function headersToArray( $str )
{
    $headers = array();
    $headersTmpArray = explode( "\r\n" , $str );
    for ( $i = 0 ; $i < count( $headersTmpArray ) ; ++$i )
    {
        // we dont care about the two \r\n lines at the end of the headers
        if ( strlen( $headersTmpArray[$i] ) > 0 )
        {
            // the headers start with HTTP status codes, which do not contain a colon so we can filter them out too
            if ( strpos( $headersTmpArray[$i] , ":" ) )
            {
                $headerName = substr( $headersTmpArray[$i] , 0 , strpos( $headersTmpArray[$i] , ":" ) );
                $headerValue = substr( $headersTmpArray[$i] , strpos( $headersTmpArray[$i] , ":" )+1 );
                $headers[$headerName] = $headerValue;
            }
        }
    }
    return $headers;
}