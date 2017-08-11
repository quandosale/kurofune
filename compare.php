<?php
    require_once("backend/db.php");
    //
    authenticate_user();
?>
<!DOCTYPE html>
<html>

<?php include("head.php"); ?>

<body class="top-navigation">

    <div id="wrapper">
        <div id="page-wrapper" class="gray-bg">
        
        <?php include("nav.php"); ?>
        
        <!-- Content BEGIN --> 
        <div class="main-inner">
            <div class="container-fluid">
                <?php
                  $asin = isset($_GET['asin']) ? $_GET['asin'] : "";
                  if($asin!=""){
                ?>
                  <div class="col-md-7 text-center">
                  <iframe id="iframe1" class="compare_iframe1" src="external.php?site=co.jp&asin=<?php echo $asin; ?>"></iframe> 
                  <!-- <iframe id="iframe1" class="compare_iframe1" src="http://www.amazon.co.jp/dp/<?php echo $asin; ?>"></iframe> -->
                </div>
                <div class="col-md-5 text-center">
                  <iframe id="iframe2" class="compare_iframe2" src="external.php?site=com&asin=<?php echo $asin; ?>"></iframe> 
                  <!-- <iframe id="iframe2" class="compare_iframe2" src="http://www.amazon.com/dp/<?php echo $asin; ?>"></iframe> -->
                </div>
                <?php } ?>
            </div>

        </div>
        <!-- Content END --> 
        
        <?php include("footer.php"); ?>

        </div>
    </div>
    <?php include("scripts.php"); ?>

</body>

</html>
