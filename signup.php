<?php
    require_once("backend/db.php");
    // require_once("backend/signup.php");
?>
<!DOCTYPE html>
<html>

<?php include("head.php"); ?>

<body class="top-navigation" style="background:#FFF !important;">
    <script>
        function setValue(order_id,field,value){
            $.ajax({
              method: "POST",
              url: "purchase.php",
              data: "cmd=SET&order_id="+order_id+"&field="+field+"&value="+value
            })
            .done(function( msg ) {});
        }
    </script>
    <div id="wrapper">
        <div id="page-wrapper" class="gray-bg">
        
        <div class="col-md-4 center-block">
        <div  class="login-inner" style="padding-top:15px !important;">
            <div class="logo-block">

                <h1 class="logo-name"><img src="img/logo.png"  alt=""/></h1>
                <p class="lead-text">新規会員お申込み</p>

            </div>
          
          <div class="login-bg">
          <h4><b>ご利用料金</b></h4>            
          月額4980円（税込）
          無料期間のご利用は、1度限りとなります。
          ご解約される場合は30日以内にPaypalにてキャンセルをお願い致します。

          <div class="sep"></div>
          <h4><b>対応国</b></h4>
          Amazon.com/Amazon.ca
          ※2ヵ国ご利用頂けます。（最大10万点）

          <div class="sep"></div>
          <h4><b>ご利用にあたって必要な物</b></h4>
          販売国のAmazon大口販売者アカウント
          ※Amazon JPのアカウントは不要です。
          インターネット環境

          <div class="sep"></div>
          <h4><b>動作環境</b></h4>
          Chrome,Firefox,Safari上での動作を確認しております。

          <h4><b>お申込み</b></h4>
          以下のPaypalボタンよりお申込みをお願い致します。

          <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" name="_xclick">
          <input type="hidden" name="cmd" value="_s-xclick">
          <input type="hidden" name="hosted_button_id" value="5PS6GU5NPXP6G">
          <!-- <input type="image" src="https://www.paypalobjects.com/ja_JP/JP/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal - オンラインでより安全・簡単にお支払い"> -->
          <!-- <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"> -->
          <div class="paypal-btn">
           <a href="javascript:{}" onclick="document.forms._xclick.submit();"><img src="img/paypal-btn-bg.png"  alt=""/>  </a>
          </div>
          </form>

          <!-- <form name="_xclick" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
          <input type="hidden" name="cmd" value="_xclick">
          <input type="hidden" name="business" value="info@kurofune.club">
          <input type="hidden" name="currency_code" value="JPY">
          <input type="hidden" name="item_name" value="Kurofune Subscription">
          <input type="hidden" name="amount" value="4200">
          <input type="image" src="http://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
          <div class="paypal-btn">
           <a href="javascript:{}" onclick="document.forms._xclick.submit();"><img src="img/paypal-btn-bg.png"  alt=""/>  </a>
          </div>
          </form> -->

          <div class="center-text">
          <p><a target="_blank" href="http://kurofune.club/tools/entry31.html">Term of uses</a></p>
          <p><a target="_blank" href="http://kurofune.club/category6/entry35.html"><u>Domestic Mercnatile Law</u></a></p>
                </div>
                      </div>
                      <!--<p class="m-t"> <small>Inspinia we app framework base on Bootstrap 3 &copy; 2014</small> </p>-->
                  </div>
              </div>

        </div>
    </div>
    <?php include("scripts.php"); ?>

</body>

</html>
