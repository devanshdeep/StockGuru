<?php
session_start();



$newArray =[];
$csvAsArray= [];
$stocks=[];
$final_array=[];
$msg='';

if(isset( $_FILES['file'])) {

    $tmpName = $_FILES['file']['tmp_name'];


    $csvAsArray = array_map('str_getcsv', file($tmpName));
    $_SESSION['csvAsArray'] = $csvAsArray;
}
if(!isset($_REQUEST['same_page'])){
    unset($_SESSION['csvAsArray']);
}

if(isset($_SESSION['csvAsArray'])){
    $csvAsArray=$_SESSION['csvAsArray'];
    array_shift(  $csvAsArray);
    $stocks=array_unique(array_column($csvAsArray, 2));
}
else{
    header('Location: index.php');
}



 //   print_r($csvAsArray);


if(isset($_REQUEST['stock_name']) && isset($_REQUEST['startdate'])  && isset($_REQUEST['enddate'])) {
    //   print_r($csvAsArray);
    $newArray = array_filter($csvAsArray, function ($value, $key) {
//        print_r($value);
//        die();
        if (strtolower($value[2]) == strtolower($_REQUEST['stock_name']) &&
            strtotime($_REQUEST['startdate']) <= strtotime($value[1]) && strtotime($value[1]) <= strtotime($_REQUEST['enddate'])) {
            return true;
        }
    }, ARRAY_FILTER_USE_BOTH);
    $newarrb = [];

    foreach ($newArray as $item) {
        $in = [];
        $in['date'] = strtotime($item[1]);
        $in['price'] = $item[3];
        $in['name'] = $item[2];
        $in['action'] = '-';


        $newarrb[] = $in;
    }
    $mean_array = array_column($newarrb, 'price');
//print_r($mean_array);
    if (count($mean_array) > 0) {
        $mean = number_format((float)array_sum($mean_array) / count($mean_array), 2, '.', '');
    }

//print_r($mean);
    $stand_deviation = number_format((float)Stand_Deviation($mean_array), 2, '.', '');
//print_r($stand_deviation);

    $datesort = array_column($newarrb, 'date');




    array_multisort($datesort, SORT_ASC, $newarrb);
//print_r($newArray);
    $msg = '';

    if (count($newarrb) > 1) {

        $buy = 0;


        for ($i = 0; $i < count($newarrb); $i++) {

            if (!$buy && $i != count($newarrb) - 1) {
                if (isset($newarrb[$i + 1])) {

                    if ($newarrb[$i]['price'] < $newarrb[$i + 1]['price']) {

                        $newarrb[$i]['action'] = 'Buy';
                        $buy = $buy ? 0 : 1;
                        $final_array[] = $newarrb[$i];
                    }
                }


            } else {
                if (isset($newarrb[$i + 1])) {

                    if ($newarrb[$i]['price'] > $newarrb[$i + 1]['price']) {

                        $newarrb[$i]['action'] = 'Sell';
                        $buy = $buy ? 0 : 1;
                        $final_array[] = $newarrb[$i];
                    }
                } else {
                    $newarrb[$i]['action'] = 'Sell';
                    $buy = $buy ? 0 : 1;
                    $final_array[] = $newarrb[$i];

                }

            }


        }


    } else {
        $msg = 'Wrong Selection of Date Range';
    }



}
else if(isset($_REQUEST['first_submit'])){
    $msg= "Kindly fill all requied fields";

}
function Stand_Deviation($arr)
{
    $num_of_elements = count($arr);

    $variance = 0.0;
    if ($num_of_elements > 0) {

        $average = array_sum($arr) / $num_of_elements;
    }
    foreach ($arr as $i) {

        $variance += pow(($i - $average), 2);
    }
    if ($num_of_elements > 0) {
        return (float)sqrt($variance / $num_of_elements);
    }
}
//print_r($newarrb);
?>
<!doctype html>
<html>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Stock Guru</title>
    <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css' rel='stylesheet'>

    <style>
        body {
            color: #000;
            overflow-x: hidden;
            height: 100%;
            background-image: url("https://i.imgur.com/GMmCQHC.png");
            background-repeat: no-repeat;
            background-size: 100% 100%
        }

        .card {
            padding: 30px 40px;
            margin-top: 60px;
            margin-bottom: 60px;
            border: none !important;
            box-shadow: 0 6px 12px 0 rgba(0, 0, 0, 0.2)
        }

        .blue-text {
            color: #00BCD4
        }

        .form-control-label {
            margin-bottom: 0
        }

        input,
        textarea,
        button {
            padding: 8px 15px;
            border-radius: 5px !important;
            margin: 5px 0px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            font-size: 18px !important;
            font-weight: 300
        }

        input:focus,
        textarea:focus {
            -moz-box-shadow: none !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
            border: 1px solid #00BCD4;
            outline-width: 0;
            font-weight: 400
        }

        .btn-block {
            text-transform: uppercase;
            font-size: 15px !important;
            font-weight: 400;
            height: 43px;
            cursor: pointer
        }

        .btn-block:hover {
            color: #fff !important
        }

        button:focus {
            -moz-box-shadow: none !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
            outline-width: 0
        }</style>
    <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
    <script type='text/javascript' src='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js'></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />


</head>
<body oncontextmenu='return false' class='snippet-body'>
<div class="container-fluid px-1 py-5 mx-auto">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-7 col-lg-8 col-md-9 col-11 text-center">
            <h3>Stock Guru</h3>

            <div class="card">
<!--                <h5 class="text-center mb-4">Upload Your CSV File</h5>-->

                    <form class="form-card"  action="brain.php" method="post"   enctype="multipart/form-data">
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-12 flex-column d-flex"> <label class="form-control-label px-3">Search Stock Name<span class="text-danger"> *</span></label>
                                <input type="text" name="stock_name" id="stock_name" required autocomplete="off" placeholder="Stock Name" />
                                <input type="hidden" name="same_page" id="same_page" value="1">
                                <input type="hidden" name="first_submit" id="same_page" value="1"></div>

                        </div>

                        <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-6 flex-column d-flex"> <label class="form-control-label px-3">Start Date <span class="text-danger"> *</span></label>
                                <input type="date" id="startdate" required name="startdate"> </div>
                            <div class="form-group col-sm-6 flex-column d-flex"> <label class="form-control-label px-3">End Date<span class="text-danger"> *</span></label>
                                <input type="date" id="enddate" required name="enddate"> </div>
                        </div>
                        <input type="submit" name="submit" value="Submit" />


                    </form>







<?php if($msg!=''){
  echo "<h3 class='text-danger'>".$msg."</h3>";
}
?>

<div><a href="index.php">Upload Another CSV File</a></div>
<?php
$profit=0;
$final_count=count( $final_array);
if($final_count>0){
echo "<h3>Stock Trading For ".strtoupper($_REQUEST['stock_name']).":</h3>";
echo "<ul>";
$count=1;
    foreach ($final_array as $item){
        echo '<li>'.$item['action']. " on ".date('d-m-Y',$item['date'])." at Price: ".$item['price'];
        if($count<$final_count){
            $profit= $profit + ($final_array[$count]['price']-$final_array[$count-1]['price']);
            $count=$count+2;
        }

    }
    echo "<h3>Total Profit: ".$profit."";
    echo "<br>Total Profit For 200 Stocks: ".($profit*200)."";
    echo "<br>Mean Stock Price:".$mean."";
    echo "<br>Standard Deviation Stock Prices:".$stand_deviation."</h3>";


}

?>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<script>


    $(document).ready(function(){

        $('#stock_name').typeahead({
            source:
                [<?php echo '"'.implode('","', $stocks).'"' ?>]

        });

    });
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>


