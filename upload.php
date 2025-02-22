<?php
// echo "path ".str_replace('\\', '/', dirname(__FILE__)) . '/PHPExcel/Classes/PHPExcel/IOFactory.php'; exit;
include str_replace('\\', '/', dirname(__FILE__)) . '/PHPExcel/Classes/PHPExcel/IOFactory.php';    
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$filename = preg_replace('/\\.[^.\\s]{3,4}$/', '', $_FILES["fileToUpload"]["name"]);

$path = $_FILES["fileToUpload"]["tmp_name"];
$objPHPExcel = PHPExcel_IOFactory::load($path);
$objWorksheet = $objPHPExcel->getActiveSheet();

$rows = $objWorksheet->getHighestRow();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "report_store_rs_import";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$total_inserted = 0;
for ($row=2; $row<=$rows; $row++) { 
    $project_id = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
    $report_id = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
    $var_id1 = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
    $var_id2 = $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
    $var_id3 = $objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
    $product_code = $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
    $visible = $objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
    $findable = $objWorksheet->getCellByColumnAndRow(7, $row)->getValue();
    $on_demand = $objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
    $file = $objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
    $sample_file = $objWorksheet->getCellByColumnAndRow(10, $row)->getValue();
    $image_file = $objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
    $graph_image_file = $objWorksheet->getCellByColumnAndRow(12, $row)->getValue();
    $report_type = $objWorksheet->getCellByColumnAndRow(13, $row)->getValue();
    $title = str_replace("'","\'",$objWorksheet->getCellByColumnAndRow(14, $row)->getValue());
    $single_user_price = $objWorksheet->getCellByColumnAndRow(15, $row)->getValue();
    $site_price = $objWorksheet->getCellByColumnAndRow(16, $row)->getValue();
    $enterprise_price = $objWorksheet->getCellByColumnAndRow(17, $row)->getValue();
    $topics = $objWorksheet->getCellByColumnAndRow(18, $row)->getValue();
    $sectors = $objWorksheet->getCellByColumnAndRow(19, $row)->getValue();
    $hot_topics = $objWorksheet->getCellByColumnAndRow(20, $row)->getValue();
    $geography = $objWorksheet->getCellByColumnAndRow(21, $row)->getValue();
    $num_pages = $objWorksheet->getCellByColumnAndRow(22, $row)->getValue();
    $publication_date = $objWorksheet->getCellByColumnAndRow(23, $row)->getValue();
    $synopsis = str_replace("'","\'",$objWorksheet->getCellByColumnAndRow(24, $row)->getValue());
    $exec_summ = str_replace("'","\'",$objWorksheet->getCellByColumnAndRow(25, $row)->getValue());
    $scope = str_replace("'","\'",$objWorksheet->getCellByColumnAndRow(26, $row)->getValue());
    $reason_to_buy = str_replace("'","\'",$objWorksheet->getCellByColumnAndRow(27, $row)->getValue());
    $key_highlights = $objWorksheet->getCellByColumnAndRow(28, $row)->getValue();
    $keywords = $objWorksheet->getCellByColumnAndRow(29, $row)->getValue();
    $companies_mentioned = $objWorksheet->getCellByColumnAndRow(30, $row)->getValue();
    $toc = $objWorksheet->getCellByColumnAndRow(31, $row)->getValue();
    $lot = $objWorksheet->getCellByColumnAndRow(32, $row)->getValue();
    $lof = $objWorksheet->getCellByColumnAndRow(33, $row)->getValue();
    $project_value = $objWorksheet->getCellByColumnAndRow(34, $row)->getValue();
    $project_stage = $objWorksheet->getCellByColumnAndRow(35, $row)->getValue();
    $quote = $objWorksheet->getCellByColumnAndRow(36, $row)->getValue();
    $quote_source = $objWorksheet->getCellByColumnAndRow(37, $row)->getValue();
    $redirect_url = $objWorksheet->getCellByColumnAndRow(38, $row)->getValue();
    $tags = $objWorksheet->getCellByColumnAndRow(39, $row)->getValue();
    $is_company = $objWorksheet->getCellByColumnAndRow(40, $row)->getValue();
    $current_uri = $objWorksheet->getCellByColumnAndRow(41, $row)->getValue();
    $topic_id = $objWorksheet->getCellByColumnAndRow(42, $row)->getValue();
    $methodology = $objWorksheet->getCellByColumnAndRow(43, $row)->getValue();
    $sector = $objWorksheet->getCellByColumnAndRow(44, $row)->getValue();
    $subsector = $objWorksheet->getCellByColumnAndRow(45, $row)->getValue();
    $stage = $objWorksheet->getCellByColumnAndRow(46, $row)->getValue();
    $status = $objWorksheet->getCellByColumnAndRow(47, $row)->getValue();

   //if($project_id != '')
   // {
      $sql ="INSERT INTO `rs_import` (`ProjectID`, `ReportID`, `variationid_1`, `variationid_2`, `variationid_3`, `ProductCode`, `Visible`, `Findable`, `OnDemand`, `File`, `SampleFile`, `ImageFile`, `GraphImageFile`, `ReportType`, `Title`, `SingleUserPrice`, `SitePrice`, `EnterprisePrice`, `Topic`, `Sectors`, `Hottopics`, `Geography`, `NumberOfPages`, `PublicationDate`, `Synopsis`, `ExecutiveSummary`, `Scope`, `ReasonsToBuy`, `KeyHighlights`, `Keywords`, `CompaniesMentioned`, `TableOfContents`, `ListOfTables`, `ListOfFigures`, `ProjectValue`, `projectStage`, `Quote`, `QuoteSource`, `RedirectURL`, `Tags`, `IsCompany`, `CurrentURI`, `TopicId`, `Methodology`, `Sector`, `Subsector`, `Stage`,`Status`) VALUES ('".$project_id."','".$report_id."','".$var_id1."','".$var_id2."','".$var_id3."','".$product_code."','".$visible."','".$findable."','".$on_demand."','".$file."','".$sample_file."','".$image_file."','".$graph_image_file."','".$report_type."','".$title."','".$single_user_price."','".$site_price."','".$enterprise_price."','".$topics."','".$sectors."','".$hot_topics."','".$geography."','".$num_pages."','".$publication_date."','".$synopsis."','".$exec_summ."','".$scope."','".$reason_to_buy."', '".$key_highlights."', '".$keywords."', '".$companies_mentioned."', '".$toc."', '".$lot."', '".$project_value."','".$project_stage."', '".$quote."', '".$quote_source."', '".$redirect_url."', '".$tags."', '".$is_company."','".$is_company."','".$current_uri."','".$topic_id."','".$methodology."','".$sector."','".$subsector."','".$stage."','".$status."')";

        if ($conn->query($sql) === TRUE) {
            // echo "New record created successfully";
            $total_inserted++;
        } else {
            // echo "Error: " . $sql . "<br>" . $conn->error;
            echo "Error: ". $conn->error;
        }
    //}

}


echo "Import Successful. Total $total_inserted rows imported";      
$conn->close();

?>