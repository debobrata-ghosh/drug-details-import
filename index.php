<?php
/**
 * Plugin Name: Drug-Details-Import
 * Description: Drug-Details-Import
 * Author: pgm Developer
 * Version: 1.0
 */

//If this file is called directly, abort.
if (!defined( 'WPINC' )) {
    die;
}
require_once(ABSPATH . "wp-admin" . '/includes/image.php');
require_once(ABSPATH . "wp-admin" . '/includes/file.php');
require_once(ABSPATH . "wp-admin" . '/includes/media.php');
//Define Constants
if ( !defined('pmgexceltoimportr')) {
    define('pmgexceltoimportr', '1.0.0');
}
if ( !defined('pmgexceltoimport')) {
    define('pmgexceltoimport', plugin_dir_url( __FILE__ ));
}

add_action('admin_menu' , function(){
    add_menu_page('Drug-Details-Import','Drug-Details-Import','manage_options', 'drug-details-import', 'Drug_details_import_fn', 'dashicons-editor-table','2');
    add_submenu_page( 'drug-details-import', 'Drug Details Update ', 'Drug Details Update',
		'manage_options','Converter_csv_update','Drug_details_update_fn');   
});

function Drug_details_import_fn(){

if(isset($_POST['submit'])){
	
	 //Taking the files from input
    $file = $_FILES['file'];
	//print_r($file);
    //Getting the file name of the uploaded file
    $fileName = $_FILES['file']['name'];
	
    //Getting the Temporary file name of the uploaded file
    $fileTempName = $_FILES['file']['tmp_name'];
    //Getting the file size of the uploaded file
    $fileSize = $_FILES['file']['size'];
    //getting the no. of error in uploading the file
    $fileError = $_FILES['file']['error'];
    //Getting the file type of the uploaded file
    $fileType = $_FILES['file']['type'];

    //Getting the file ext
    $fileExt = explode('.',$fileName);
    $fileActualExt = strtolower(end($fileExt));

    //Array of Allowed file type
    $allowedExt = array("xlsx","XLSX");
    $msg ="";
    //Checking, Is file extentation is in allowed extentation array
    if(in_array($fileActualExt, $allowedExt)){
        //Checking, Is there any file error
        if($fileError == 0){
            //Checking,The file size is bellow than the allowed file size
            if($fileSize < 10000000){
                //Creating a unique name for file
                //$fileNemeNew = uniqid('',true).".".$fileActualExt;
                //File destination
                $fileDestination = $_SERVER['DOCUMENT_ROOT'].'/convert-to-csv-wpallimport/wp-content/plugins/drug-details-import/'.$fileName;
				//print_r($fileDestination);
				//exit;
                //function to move temp location to permanent location
                move_uploaded_file($fileTempName, $fileDestination);
                //Message after success
                $msg = "File Uploaded successfully";
				
				
	  //Had to change this path to point to IOFactory.php.
	  //Do not change the contents of the PHPExcel-1.8 folder at all.
	  include('Classes/PHPExcel/IOFactory.php');

	  //Use whatever path to an Excel file you need.
	  $inputFileName = $_SERVER['DOCUMENT_ROOT']. '/convert-to-csv-wpallimport/wp-content/plugins/drug-details-import/'.$fileName;
	  
	  if (!is_readable($inputFileName)) {
			chmod($inputFileName, 0744);
		}

  try {
    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($inputFileName);
  } catch (Exception $e) {
    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . 
        $e->getMessage());
  }
	
$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);

$objWorksheet = $objPHPExcel->getActiveSheet();

$rows = $objWorksheet->getHighestRow();	
	
	
  //$rows = $objPHPExcel->getSheet(0);
 // $highestRow = $rows->getHighestRow();
 // $highestColumn = $rows->getHighestColumn();
	}else{
        //Message,If this is not a valid file type
        echo "You can't upload this extention of file";
    }
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "convert-to-csv-wpallimport";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$total_inserted = 0;
for ($row=2; $row<=$rows; $row++) { 
    $Drug_ID = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
    $Drug_Name = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
    $Key_Manufacturers = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
    $Indication_Name = $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
    $Report_Segments  = $objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
    $Administration_Pathway = $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
    $Therapeutic_Areas  = $objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
	$Drug_Development_Status = $objWorksheet->getCellByColumnAndRow(7, $row)->getValue();
    $Unique_ID = $objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
    $Drug_Type = $objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
    
   //if($project_id != '')
   // {
      $sql ="INSERT INTO `loa_npv_drug_details` (`Drug_ID`, `Drug_Name`, `Key_Manufacturers`, `Indication`, `Report_Segments`, `Administration_Pathway`, `Therapeutic_Areas`, `Drug_Development_Status`, `Unique_ID`, `Drug_Type`) VALUES ('".$Drug_ID."','".$Drug_Name."','".$Key_Manufacturers."','".$Indication_Name."','".$Report_Segments."','".$Administration_Pathway."','".$Therapeutic_Areas."','".$$Drug_Development_Status."','".$Unique_ID."','".$Drug_Type."')";

        if ($conn->query($sql) === TRUE) {
            // echo "New record created successfully";
            $total_inserted++;
        } else {
            // echo "Error: " . $sql . "<br>" . $conn->error;
            echo "Error: ". $conn->error;
        }
    //}
	

	
}
	
$msg = "Import Successful. Total $total_inserted rows imported";      
$conn->close();
	

}
	
}

}
?>

<style>
        /* ANIMATION RIGHT TO LEFT*/
            @-webkit-keyframes rightToLeft{ 
                0% {
                    opacity: 0;
                    -webkit-transform: translateX(300px);
                    -moz-transform: translateX(300px);
                    transform: translateX(300px) ;

                }
                30% {
                    opacity: 1;
                    -webkit-transform: translateX(0px) ;
                    -moz-transform: translateX(0px) ;
                    transform: translateX(0px) ;

                }
                100% {
                    opacity: 1;
                    -webkit-transform: scale(1) ;
                    -moz-transform:  scale(1) ;
                    transform:  scale(1) ;

                }
            }

            .rightToLeft{
                -webkit-animation: rightToLeft 2s ease-in-out;
                -moz-animation: rightToLeft 2s ease-in-out;
                -o-animation: rightToLeft 2s ease-in-out;
                -ms-animation: rightToLeft 2s ease-in-out;
                animation: rightToLeft 2s ease-in-out;	

            }

            .pmg-reports-excel-importer{
                overflow:hidden;
            }

            .pmg-reports-excel-importer h2{
                font-size:14px!important;
            }
            .pmg-reports-excel-importer h3{
                font-size:12px !important;
            }
            .pmg-reports-excel-importer h4{
                font-size:12px !important;
            }
            .pmg-reports-excel-importer ,.pmg-reports-excel-importer p.submit {
                font-size:12px !important;
            }

            .pmg-reports-excel-importer .left_wrap{
                float:left;
                width:70%;
            }

            .pmg-reports-excel-importer  .right_wrap{
                float:right;
                padding-left:10px;		
                width:25%;
                background: #fff;
                border:5px solid #0085ba;
                border-right:1px solid #fff;
                font-size:1.3em;
            }

            .right_wrap h2{
                border-bottom:1px solid #0085ba;
                padding-bottom:5px;
                font-size:1.7em !important;
            }

            .premium_button{
                background:#0085ba;
                color:#fff;
                padding:5px;
                margin:5px;
                text-decoration:none;
                border-radius:5px;
                font-size:1.5em;
            }
            .premium_button:hover{
                color:#fff;
            }

            .web_logo{
                float:right;
            }
            .web_logo img{
                width:150px;
                height:100px;
            }

            .pmg-reports-excel-importer .premium_img{
                width:150px;
                height:150px;	
            }


            .center{
                text-align:center;
            }

            .pmg-reports-excel-importer #tabs{
                overflow-x:scroll;
            }

            .pmg-reports-excel-importer #tabs li ,#tabs li a{
                display:inline;
                padding-right:10px;
            }

            .pmg-reports-excel-importer table{
                text-align:center;
            }
            .pmg-reports-excel-importer th{
                background:#777;
                color:#fff;
                padding:5px;
            }

            #instructionsVideo{
                display:none;
            }

            /* === ajaxify === */
            body.loading .pmg-reports-excel-importer> * {
                opacity:0.2;
            }

            body.loading .pmg-reports-excel-importer:before {
                position:fixed;
                content: "Loading...";
                font-size:2em;
                padding: 22px;
                background: #000;
                background: url(../images/loading.gif) no-repeat center center;
                color: #777;
                width: 50%;
                height:50%;
                margin-left:15%;
                box-sizing: border-box;
                text-align:center;
            }


            .pmg-reports-excel-importer .premium_msg{
                display:none;
                /*background:#F08080;*/
                border:1px solid red;
                text-align:center;
                padding:10px;
                margin:10px;
            }



            .pmg-reports-excel-importer .importMessageSussess{
                background:lightGreen;
                padding:5px;
                border:1px solid green;
                color:#fff;
            }
            .pmg-reports-excel-importer .importMessageSussess a, .pmg-reports-excel-importer .success a{
                color:#000;
            }

            .pmg-reports-excel-importer .uploader {
                position:relative;
                width:99%; 
                max-width: 660px;
                height:300px;
                background:#f3f3f3; 
                border:1px dashed #e8e8e8;
                background-size:cover;
                text-align:center;
            }
            .pmg-reports-excel-importer #file{		
                width:100%;
                position:absolute;	
                height:300px;
                top:0;
                left:0;
                z-index:2;
                opacity:0;
                cursor:pointer;
            }
            .pmg-reports-excel-importer .uploader .userSelected{
                max-width:90%;
                width:90%;
                z-index:1;
                border:none;
                display:none;
            }

            .pmg-reports-excel-importer .nav-tab-wrapper a[href*="instructions"]{
                background:green;
                color:#fff;
                border:1px solid green;	
            }

            .pmg-reports-excel-importer input[type=text],.pmg-reports-excel-importer input[type=number], .pmg-reports-excel-importer textarea{
                border:none;
                border-bottom:1px solid #0073aa;
                transition:all .3s ease-in-out;
                cursor:text;
            }
            .pmg-reports-excel-importer input[type=text]:hover,.pmg-reports-excel-importer input[type=number]:hover, .pmg-reports-excel-importer textarea:hover{
                background:#ffffcc;
            }

            #myProgress {
                width: 100%;
                background-color: #ddd;
            }

            #myBar {
                width: 1%;
                height: 30px;
                background-color: #4CAF50;
            }

            @media(max-width:980px){
                .pmg-reports-excel-importer  .left_wrap, .pmg-reports-excel-importer .right_wrap{
                    float:none;
                    width:100%;
                    border-right:none;
                }
            }

            p#filnamedisplay {
                float: left;
                font-size: 16px;
                font-weight: bold;
            }

            .alert-success {
                color: #3c763d !important;
                background-color: #dff0d8 !important;
                border-color: #d6e9c6 !important;
            }

            .alert {
                padding: 15px !important;
                margin-bottom: 20px !important;
                border: 1px solid transparent !important;
                border-radius: 4px !important;
            }

            .alert-danger {
                color: #a94442 !important;
                background-color: #f2dede !important;
                border-color: #ebccd1 !important;
            }

            .alert-info {
                color: #31708f !important;
                background-color: #d9edf7 !important;
                border-color: #bce8f1 !important;
            }

            #result{
                width:50%;
            }
            #myProgress{
                width:50%;
            }

            #resultmessage{
                float: left;
            }

            .blockOverlay {
                z-index: 1000;
                border: medium none;
                margin: 0px;
                padding: 0px;
                width: 100%;
                height: 100%;
                top: 0px;
                left: 0px;
                background-color: rgb(0, 0, 0);
                opacity: 0.6;
                cursor: wait;
                position: fixed;
                display:none;
            }
            body.loading #adminmenuwrap,body.loading #wpadminbar{z-index: 90;}
            body.loading .blockOverlay{display: block;}
            .alert-warning {
                color: #8a6d3b;
                background-color: #f2dede;
                border-color: #ebccd1;
            }
            .text-danger{
                color: #a94442 !important;
            }

            .success{color:green !important;}

        </style>
        <div class="blockUI blockOverlay"></div>
        <div class="row pmg-reports-excel-importer">
            <h2>Upload Excel File To Import Drug Details</h2>
            <form action="" method="post" id='pmg-reports-import-from' enctype="multipart/form-data">
                <p style="color:red;font-size: 14px;">Allowed Excel file is less than 2MB</p>
                <!--<p> <a href='<?php //echo plugins_url('/RS_Template.xlsx', __FILE__); ?>'>Click Here to download the sample excel file template for update resports.</a></p> -->
				<p> <a href='<?php echo pmgexceltoimport.'Sample_LOA_NPV_Drug-details.xlsx'; ?>'>Click Here to download the sample excel file template To Import Drug Details.</a></p>
                 <!--   <p id="pmg-reports-upload-progress" style="color:red;font-size: 14px;"></p> -->
                <input id="pmg-reports-import-file" type="file" name="file"  /><br><br>
                <input id="pmg-reports-import-submit" class="button button-primary" name="submit" type="submit" value="Upload"/>
            </form>
			
			<?php 
			if (!empty($msg)) { ?>
			
			  <h2><?php echo $msg; ?></h2>
			  
			<?php } ?>
			
			
        </div>
        <div id='result' class="result" style="display:none;"></div>
        <div class="progressText" id="myProgress" style="display:none;"> <div id="myBar"></div></div>
        <div id='resultmessage' class="resultmessage" style="display:none;"></div>

        <div id="reports-import-dialog" class="hidden" style="max-width:600px">
            <h3> Uploading Reports, Uploading may take up to 30 minutes, please wait...</h3>
        </div>
<?php } ?>
<!-- This is for Drug Details Update -->

<?php 
function Drug_details_update_fn(){
     //Taking the files from input
     $file = $_FILES['file'];
     //print_r($file);
     //Getting the file name of the uploaded file
     $fileName = $_FILES['file']['name'];
     
     //Getting the Temporary file name of the uploaded file
     $fileTempName = $_FILES['file']['tmp_name'];
     //Getting the file size of the uploaded file
     $fileSize = $_FILES['file']['size'];
     //getting the no. of error in uploading the file
     $fileError = $_FILES['file']['error'];
     //Getting the file type of the uploaded file
     $fileType = $_FILES['file']['type'];
 
     //Getting the file ext
     $fileExt = explode('.',$fileName);
     $fileActualExt = strtolower(end($fileExt));
 
     //Array of Allowed file type
     $allowedExt = array("xlsx","XLSX");
     $msg ="";
     //Checking, Is file extentation is in allowed extentation array
     if(in_array($fileActualExt, $allowedExt)){
         //Checking, Is there any file error
         if($fileError == 0){
             //Checking,The file size is bellow than the allowed file size
             if($fileSize < 10000000){
                 //Creating a unique name for file
                 //$fileNemeNew = uniqid('',true).".".$fileActualExt;
                 //File destination
                 $fileDestination = $_SERVER['DOCUMENT_ROOT'].'/convert-to-csv-wpallimport/wp-content/plugins/drug-details-import/'.$fileName;
                 //print_r($fileDestination);
                 //exit;
                 //function to move temp location to permanent location
                 move_uploaded_file($fileTempName, $fileDestination);
                 //Message after success
                 $msg = "File Uploaded successfully";
                 
                 
       //Had to change this path to point to IOFactory.php.
       //Do not change the contents of the PHPExcel-1.8 folder at all.
       include('Classes/PHPExcel/IOFactory.php');
 
       //Use whatever path to an Excel file you need.
       $inputFileName = $_SERVER['DOCUMENT_ROOT']. '/convert-to-csv-wpallimport/wp-content/plugins/drug-details-import/'.$fileName;
       
       if (!is_readable($inputFileName)) {
             chmod($inputFileName, 0744);
         }
 
   try {
     $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
     $objReader = PHPExcel_IOFactory::createReader($inputFileType);
     $objPHPExcel = $objReader->load($inputFileName);
   } catch (Exception $e) {
     die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . 
         $e->getMessage());
   }
     
 $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
 
 $objWorksheet = $objPHPExcel->getActiveSheet();
 
 $rows = $objWorksheet->getHighestRow();	
     
     
   //$rows = $objPHPExcel->getSheet(0);
  // $highestRow = $rows->getHighestRow();
  // $highestColumn = $rows->getHighestColumn();
     }else{
         //Message,If this is not a valid file type
         echo "You can't upload this extention of file";
     }
 $servername = "localhost";
 $username = "root";
 $password = "";
 $dbname = "convert-to-csv-wpallimport";
 
 $conn = new mysqli($servername, $username, $password, $dbname);
 if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
 }
 
 $total_inserted = 0;
 for ($row=2; $row<=$rows; $row++) { 
     $Drug_ID = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
     $Drug_Name = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
     $Key_Manufacturers = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
     $Indication_Name = $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
     $Report_Segments  = $objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
     $Administration_Pathway = $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
     $Therapeutic_Areas  = $objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
     $Drug_Development_Status = $objWorksheet->getCellByColumnAndRow(7, $row)->getValue();
     $Unique_ID = $objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
     $Drug_Type = $objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
     
    //if($project_id != '')
    // {

       $sql ="UPDATE `loa_npv_drug_details` SET Drug_ID='$Drug_ID', Drug_Name='$Drug_Name', Key_Manufacturers='$Key_Manufacturers', Indication='$Indication_Name', Report_Segments='$Report_Segments', Administration_Pathway='$Administration_Pathway', Therapeutic_Areas='$Therapeutic_Areas', Drug_Development_Status='$Drug_Development_Status' WHERE Unique_ID='$Unique_ID' AND Drug_Type='$Drug_Type'";
 
         if ($conn->query($sql) === TRUE) {
             // echo "New record created successfully";
             $total_inserted++;
         } else {
             // echo "Error: " . $sql . "<br>" . $conn->error;
             echo "Error: ". $conn->error;
         }
     //}
     
 
     
 }
     
 $msg = "Drug Details. Total $total_inserted rows Updated";      
 $conn->close();
     
 
 }
     
 }
 ?>

    <style>
    /* ANIMATION RIGHT TO LEFT*/
        @-webkit-keyframes rightToLeft{ 
            0% {
                opacity: 0;
                -webkit-transform: translateX(300px);
                -moz-transform: translateX(300px);
                transform: translateX(300px) ;

            }
            30% {
                opacity: 1;
                -webkit-transform: translateX(0px) ;
                -moz-transform: translateX(0px) ;
                transform: translateX(0px) ;

            }
            100% {
                opacity: 1;
                -webkit-transform: scale(1) ;
                -moz-transform:  scale(1) ;
                transform:  scale(1) ;

            }
        }

        .rightToLeft{
            -webkit-animation: rightToLeft 2s ease-in-out;
            -moz-animation: rightToLeft 2s ease-in-out;
            -o-animation: rightToLeft 2s ease-in-out;
            -ms-animation: rightToLeft 2s ease-in-out;
            animation: rightToLeft 2s ease-in-out;	

        }

        .pmg-reports-excel-importer{
            overflow:hidden;
        }

        .pmg-reports-excel-importer h2{
            font-size:14px!important;
        }
        .pmg-reports-excel-importer h3{
            font-size:12px !important;
        }
        .pmg-reports-excel-importer h4{
            font-size:12px !important;
        }
        .pmg-reports-excel-importer ,.pmg-reports-excel-importer p.submit {
            font-size:12px !important;
        }

        .pmg-reports-excel-importer .left_wrap{
            float:left;
            width:70%;
        }

        .pmg-reports-excel-importer  .right_wrap{
            float:right;
            padding-left:10px;		
            width:25%;
            background: #fff;
            border:5px solid #0085ba;
            border-right:1px solid #fff;
            font-size:1.3em;
        }

        .right_wrap h2{
            border-bottom:1px solid #0085ba;
            padding-bottom:5px;
            font-size:1.7em !important;
        }

        .premium_button{
            background:#0085ba;
            color:#fff;
            padding:5px;
            margin:5px;
            text-decoration:none;
            border-radius:5px;
            font-size:1.5em;
        }
        .premium_button:hover{
            color:#fff;
        }

        .web_logo{
            float:right;
        }
        .web_logo img{
            width:150px;
            height:100px;
        }

        .pmg-reports-excel-importer .premium_img{
            width:150px;
            height:150px;	
        }


        .center{
            text-align:center;
        }

        .pmg-reports-excel-importer #tabs{
            overflow-x:scroll;
        }

        .pmg-reports-excel-importer #tabs li ,#tabs li a{
            display:inline;
            padding-right:10px;
        }

        .pmg-reports-excel-importer table{
            text-align:center;
        }
        .pmg-reports-excel-importer th{
            background:#777;
            color:#fff;
            padding:5px;
        }

        #instructionsVideo{
            display:none;
        }

        /* === ajaxify === */
        body.loading .pmg-reports-excel-importer> * {
            opacity:0.2;
        }

        body.loading .pmg-reports-excel-importer:before {
            position:fixed;
            content: "Loading...";
            font-size:2em;
            padding: 22px;
            background: #000;
            background: url(../images/loading.gif) no-repeat center center;
            color: #777;
            width: 50%;
            height:50%;
            margin-left:15%;
            box-sizing: border-box;
            text-align:center;
        }


        .pmg-reports-excel-importer .premium_msg{
            display:none;
            /*background:#F08080;*/
            border:1px solid red;
            text-align:center;
            padding:10px;
            margin:10px;
        }



        .pmg-reports-excel-importer .importMessageSussess{
            background:lightGreen;
            padding:5px;
            border:1px solid green;
            color:#fff;
        }
        .pmg-reports-excel-importer .importMessageSussess a, .pmg-reports-excel-importer .success a{
            color:#000;
        }

        .pmg-reports-excel-importer .uploader {
            position:relative;
            width:99%; 
            max-width: 660px;
            height:300px;
            background:#f3f3f3; 
            border:1px dashed #e8e8e8;
            background-size:cover;
            text-align:center;
        }
        .pmg-reports-excel-importer #file{		
            width:100%;
            position:absolute;	
            height:300px;
            top:0;
            left:0;
            z-index:2;
            opacity:0;
            cursor:pointer;
        }
        .pmg-reports-excel-importer .uploader .userSelected{
            max-width:90%;
            width:90%;
            z-index:1;
            border:none;
            display:none;
        }

        .pmg-reports-excel-importer .nav-tab-wrapper a[href*="instructions"]{
            background:green;
            color:#fff;
            border:1px solid green;	
        }

        .pmg-reports-excel-importer input[type=text],.pmg-reports-excel-importer input[type=number], .pmg-reports-excel-importer textarea{
            border:none;
            border-bottom:1px solid #0073aa;
            transition:all .3s ease-in-out;
            cursor:text;
        }
        .pmg-reports-excel-importer input[type=text]:hover,.pmg-reports-excel-importer input[type=number]:hover, .pmg-reports-excel-importer textarea:hover{
            background:#ffffcc;
        }

        #myProgress {
            width: 100%;
            background-color: #ddd;
        }

        #myBar {
            width: 1%;
            height: 30px;
            background-color: #4CAF50;
        }

        @media(max-width:980px){
            .pmg-reports-excel-importer  .left_wrap, .pmg-reports-excel-importer .right_wrap{
                float:none;
                width:100%;
                border-right:none;
            }
        }

        p#filnamedisplay {
            float: left;
            font-size: 16px;
            font-weight: bold;
        }

        .alert-success {
            color: #3c763d !important;
            background-color: #dff0d8 !important;
            border-color: #d6e9c6 !important;
        }

        .alert {
            padding: 15px !important;
            margin-bottom: 20px !important;
            border: 1px solid transparent !important;
            border-radius: 4px !important;
        }

        .alert-danger {
            color: #a94442 !important;
            background-color: #f2dede !important;
            border-color: #ebccd1 !important;
        }

        .alert-info {
            color: #31708f !important;
            background-color: #d9edf7 !important;
            border-color: #bce8f1 !important;
        }

        #result{
            width:50%;
        }
        #myProgress{
            width:50%;
        }

        #resultmessage{
            float: left;
        }

        .blockOverlay {
            z-index: 1000;
            border: medium none;
            margin: 0px;
            padding: 0px;
            width: 100%;
            height: 100%;
            top: 0px;
            left: 0px;
            background-color: rgb(0, 0, 0);
            opacity: 0.6;
            cursor: wait;
            position: fixed;
            display:none;
        }
        body.loading #adminmenuwrap,body.loading #wpadminbar{z-index: 90;}
        body.loading .blockOverlay{display: block;}
        .alert-warning {
            color: #8a6d3b;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .text-danger{
            color: #a94442 !important;
        }

        .success{color:green !important;}

    </style>
    <div class="blockUI blockOverlay"></div>
    <div class="row pmg-reports-excel-importer">
        <h2>Upload Excel File To Update Drug Details</h2>
        <form action="" method="post" id='pmg-reports-import-from' enctype="multipart/form-data">
            <p style="color:red;font-size: 14px;">Allowed Excel file is less than 2MB</p>
            <!--<p> <a href='<?php //echo plugins_url('/RS_Template.xlsx', __FILE__); ?>'>Click Here to download the sample excel file template for update resports.</a></p> -->
            <p> <a href='<?php echo pmgexceltoimport.'Sample_LOA_NPV_Drug-details.xlsx'; ?>'>Click Here to download the sample excel file template To Import Drug Details.</a></p>
             <!--   <p id="pmg-reports-upload-progress" style="color:red;font-size: 14px;"></p> -->
            <input id="pmg-reports-import-file" type="file" name="file"  /><br><br>
            <input id="pmg-reports-import-submit" class="button button-primary" name="submit" type="submit" value="Upload"/>
        </form>
        
        <?php 
        if (!empty($msg)) { ?>
        
          <h2><?php echo $msg; ?></h2>
          
        <?php } ?>
        
        
    </div>
    <div id='result' class="result" style="display:none;"></div>
    <div class="progressText" id="myProgress" style="display:none;"> <div id="myBar"></div></div>
    <div id='resultmessage' class="resultmessage" style="display:none;"></div>

    <div id="reports-import-dialog" class="hidden" style="max-width:600px">
        <h3> Uploading Reports, Uploading may take up to 30 minutes, please wait...</h3>
    </div>

<?php
}
?>