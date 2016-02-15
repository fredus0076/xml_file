<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set("memory_limit", "10048M");
date_default_timezone_set('Europe/Paris');

define("RACINE_SERVEUR", "."); 

include("vendor/autoload.php");
$name_file = "";
$error ="";
function randomNumber() {
    $nb_min = 1;
    $nb_max = 100000;
    $nombre = mt_rand($nb_min, $nb_max);

    return $nombre;

}
if(isset($_FILES['fichier'])){

	if(!empty($_FILES['fichier']['name'])){
		//******************************** Si le transfere de la photo c'est mal passer ***************************************
						if ($_FILES['fichier']['error'] > 0 && $_FILES['fichier']['error'] != 4 ){
							$error .= '<b><p style="color: #cc3036; margin-left: 146px;">Erreur lors du transfert image droite<p></b>';
						} 
			//************************************* Erreur sur la taille de l'image pour augmenter la taille ou la diminuer changer la valeur en OCTETS de $max_size ************************
						$addr_img = RACINE_SERVEUR . "/file/original/".randomNumber().$_FILES['fichier']['name'];
						if(file_exists($addr_img)){
							$error .= '<b><p style="color: #cc3036; margin-left: 146px;">Le nom de l\'image droite existe déjà veuillez renommer l\'image<p></b>'; 
						}
			//************************************ Erreur sur le format des images on accepte que les formats les moins gourmant *************************************************
						$name_file = $addr_img;

						copy($_FILES['fichier']['tmp_name'], $addr_img);
			//*************************************** ON Copie maintenant la photo dans le repertoire images/catalogue/id_article.jpg ************************
			if(file_exists($name_file)){
				//echo "OKOKOKOKOKOKOKK";
			}
			echo $error;			
	}
   


function numberToColumnName($number) {
    $abc = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $abc_len = strlen($abc);

    $result_len = 1; // how much characters the column's name will have
    $pow = 0;
    while (($pow += pow($abc_len, $result_len)) < $number) {
        $result_len++;
    }

    $result = "";
    $next = false;
    // add each character to the result...
    for ($i = 1; $i <= $result_len; $i++) {
        $index = ($number % $abc_len) - 1; // calculate the module

        // sometimes the index should be decreased by 1
        if ($next || $next = false) {
            $index--;
        }

        // this is the point that will be calculated in the next iteration
        $number = floor($number / strlen($abc));

        // if the index is negative, convert it to positive
        if ($next = ($index < 0)) {
            $index = $abc_len + $index;
        }

        $result = $abc[$index] . $result; // concatenate the letter
    }
    return $result;
}

function load_xml_translation_to_array($path) {
    $entries = [];
    $content = file_get_contents($path);
    $content = str_replace("tuv xml:lang", "tuv lang", $content);
    $content = preg_replace('/\<bpt(.*)?\>(.*)\<\/bpt\>/im', '${2}', $content);

//var_dump($content);exit();
    $xml = simplexml_load_string($content);
    unset($content);

    foreach ($xml->body->children() as $tu) {
        $id = current($tu->attributes()['tuid']);
        $entry = [];
        foreach ($tu->children() as $tuv) {
            $entry[current($tuv->attributes()['lang'])] = utf8_decode($tuv->seg->__toString());

        }
        $entries[$id] = $entry;
        unset($entry);
    }

    return $entries;
}



		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
		    ->setLastModifiedBy("Maarten Balliauw")
		    ->setTitle("Office 2007 XLSX Test Document")
		    ->setSubject("Office 2007 XLSX Test Document")
		    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
		    ->setKeywords("office 2007 openxml php")
		    ->setCategory("Test result file");

		$entries = load_xml_translation_to_array($name_file);
		$columnId = 1;
    //    print_r($entries);die;
		foreach (current($entries) as $key => $value) {
		    $columnName = (numberToColumnName($columnId) . 1);
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($columnName, $key);
		    $columnId++;
		}


		$rowId = 2;
		foreach ($entries as $id => $data) {
		    $columnId = 1;
		   // var_dump($id);
		    foreach ($data as $value) {
		  // var_dump($value);

		        $columnName = (numberToColumnName($columnId) . $rowId);
		        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($columnName, $value);
		        $columnId++;
		    }

		    $rowId++;
		}

//exit;
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('translations');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$x = time();
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header("Content-Disposition: attachment;filename={$x}.xls");
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
//header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

/*$nom_fichier = "";
function randomNumber() {
    $nb_min = 1;
    $nb_max = 100000;
    $nombre = mt_rand($nb_min, $nb_max);

    return $nombre;

}

function createCsv($xml, $f) {

    foreach ($xml->children() as $item) {

        $hasChild = (count($item->children()) > 0) ? true : false;

        if (!$hasChild) {
            $put_arr = [$item];
            fputcsv($f, $put_arr, ' ', '"');

        } else {
            createCsv($item, $f);
        }
    }

}




    if ($_FILES['file'][error] == 0) {

        $infosfichier = pathinfo($_FILES['file']['name']);
        $extension_upload = $infosfichier['extension'];
        $extensions_autorisees = ['xml', 'txt'];
        if (in_array($extension_upload, $extensions_autorisees)) {
            $nom_fichier = $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], 'file/' . basename($_FILES['file']['name']));
            $data = load_translation_xml_to_array('file/' . basename($_FILES['file']['name']));

            print_r($data);
            echo "Le fichier $nom_fichier a bien été enregistrer";
        }

    } else {
        echo "Le format n'ai pas valide !";
    }


    $filexml = 'file/' . $nom_fichier; // xml file location with file name
    if (file_exists($filexml)) {
        $nombre = 'file/' . randomNumber() . '.csv';
        echo $nombre;
        $xml = simplexml_load_file($filexml);
        $f = fopen($nombre, 'w');
        createCsv($xml, $f);
        fclose($f);
    }
*/

}//if(isset($_FILES['fichier'])){



var_dump($_FILES);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Drag2</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.css">
    <style>
       .container-zone {
            margin-top: 5%;
            width: 400px;
            height: 100px;
            background: transparent;
            border: 1px dotted black;
            overflow: hidden;
        }

        #file {
            background: transparent;
            position: relative;
            top: -4px;
            left: 0;
            height: 109px;
            width: 398px;
            display: block;
            opacity: 0;
            cursor: pointer;
            z-index: 20;
        }

        .input-file-trigger {
            position: relative;
            top: -110px;
            line-height: 50px;
            text-align: center;
            display: block;
            z-index: 0;
        }

        .success .error {
            cursor: pointer;
        }

        .insert {
            margin-bottom: 0px;
        }

        .download {
            font-size: 35px;
        }

        body {
            background: url('img/panda.png');
            background-size: cover;
        }




input[type="file"]::-webkit-file-upload-button {
    height: 88px;
    width: 321px;
}
    </style>
</head>
<body>
<div class="container-fluid heads">

</div>

<div class="container">
    <div class="row">
        <div class="col-sm-5 col-sm-offset-4" >
        <form action="" method="post" id="form_file"  enctype="multipart/form-data">
             <div class="container-zone"> 
                   <input id="file" type="file" name="fichier" onchange="submit()"> 
                <label for="fichier" id="label-drop" class="input-file-trigger" class="input-file-trigger" >

                <p class="insert">Insérer votre fichier EXEL</p>

                <p class="download fa fa-download"></p></label>
              
                
               
            </div>
        </form>
        </div>
    </div>
</div>    




<script>


</script>
</body>
</html>