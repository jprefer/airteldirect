<?php
require('cgi-bin/conn.php');

$RefID = $_GET['V'];
$date = date("Y-m-d H:i:s");

$select_query = "SELECT VisitCount FROM AurisProject.RechargeSMS WHERE Reference = '$RefID'";
$select = mysql_query($select_query) or die(mysql_error());
$count = mysql_result($select, 0, 0);

$count++;

$update_query = "UPDATE AurisProject.RechargeSMS SET SiteVisit = '$date', VisitCount = '$count', Page = 'A' WHERE Reference = '$RefID'";
mysql_query($update_query) or die(mysql_error());

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>..:: AirTel Recharge ::..</title>
        <meta name="keywords" content="prepaid long distance service, city codes,country codes, Cheap international calls, phone cards, calling card, International calling, cheap long distance, online pin, pinless, low rates, prepaid long distance service, rechargeable pin, toll-free access, local access, cheap international calls, prepaid long distance phone rates, lowest international calling, international phone rates, prepaid calling cards, cheapest phone calls,international calling, international phone calls, prepaid phone cards, long distance, rates, service, phone rate, telephone, company, dial around, dial-around, dialaround, phone, calls, low cost, consumer, quality long distance service" />
        <meta name="description" content="prepaid long distance service" />
        <link href="default.css" rel="stylesheet" type="text/css" media="all" />
        <style type="text/css">
            @import "layout.css";
        </style>


        <script type="text/javascript">
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-2914412-5']);
            _gaq.push(['_trackPageview']);
            (function() {
                var ga = document.createElement('script');
                ga.type = 'text/javascript';
                ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(ga, s);
            })();
        </script>

    </head>
    <body class="homepage">
        <div id="wrapper">
            <div id="wrapper-bgtop">
                <div id="wrapper-bgbtm">
                    <div id="header" class="container">
                        <div id="logo">
                            <a href="index.html"><img alt="" height="68" src="images/logo.gif" width="225" /></a>
                        </div>
                        <div align="right" style="margin-left: 758px;">
                            <table style="width: 120px">
                                <tr>
                                    <td align="center" style="width: 50%"><a href="recharge_sp.html"><img align="middle" src="images/flags/spain.gif" title="Espanol"></img></a></td>
                                    <td align="right" style="width: 50%"><strong><a href="account.html">LOGIN</a></strong></td>
                                </tr>
                                <tr>
                                    <td align="center" style="width: 50%; vertical-align:top"><a href="recharge_sp.html">Espanol</a></td>
                                    <td style="width: 50%"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div id="splash" class="container">
                        <h2>Call anywhere in the world<br />
                            <span>for LESS with Airtel Direct!</span></h2>
                        <p class="byline">No hidden connection fees or surcharges</p>
                        <br />
                        <span id="banner">No PIN to remember with our state of the art<br />PINLESS dialing system</span>


                    </div>
                    <div id="menu-container">
                        <div id="menu">
                            <ul>
                                <li><a href="rates.html" accesskey="1" title="">Rates</a></li>
                                <li><a href="features.html" accesskey="2" title="">Features</a></li>
                                <li><a href="referrals.html" accesskey="3" title="">Earn Free Credit</a></li>
                                <li><a href="account.html" accesskey="4" title="">My Account</a></li>
                                <li><a href="travel.html" accesskey="5" title="">Travel with us</a></li>
                                <li><a href="accessnumbers.php" accesskey="6" title="">Access Numbers</a></li>
                                <li><a href="about.html" accesskey="7" title="">About Us</a></li>
                                <li><a href="faq.html" accesskey="8" title="">Support</a></li>
                            </ul>
                        </div>
                    </div>
                    <div id="page" class="container" style="height:450px">
                        <!-- FAQ starts here -->

                        <br/><br/><br/><br/>

                        <div>
                            <center>
                                <h1>
                                Airtel Direct Gold<br /><br /></h1>

                                <h2><i>The most cost effective and convenient way<br /> 
                                    to make your International long distance calls anywhere</i><br /><br /><br />

                                    
                                Thank you for your recharge purchase, and your continued use of our service! You now qualify to choose to use our Airtel Gold  recharge program, and as a result receive 25% more minutes every time you make a call.
                                <br /><br /><br />

                                <a href="register.html">Register Here</a>
                                <br /><br /><br />

                                Once you’ve registered, now all future recharge purchases can be made online conveniently at home using your computer or if your on the go through your tablet or mobile device! 
                                    </h2>
                            </center>    

                        </div>



                    </div>			


                    <div class="container">
                        <div id="two-columns" class="box-style" align="center">
                            <div class="colA">
                                <p><a href="terms.html" class="link2">Terms</a></p>
                            </div>
                            <div class="colA">
                                <p><a href="contact.html" class="link2">Contact Us</a></p>
                            </div>
                            <div class="colA">
                                <p><a href="about.html" class="link2">About Us</a></p>
                            </div>
                            <div class="colA"  style="border-left: 1px solid #CCCCCC;">
                                <p><a href="index.html" class="link2">Home</a></p>
                            </div>			</div>
                    </div>

                    <div id="footer" class="container" style="height: auto;">
                        <br /><br /><br /><br />
                        <div style="margin: 0 auto; width: 45%;">
                            <div style="float: left; padding-top: 20px; padding-right: 30px;">
                                <img src="images/cc.jpg"  />
                            </div>
                            <div class="AuthorizeNetSeal" style="float: left; padding-right: 30px;"> 
                                <script type="text/javascript" language="javascript">var ANS_customer_id = "8458b063-420b-4d39-9aa2-a09c4416c274";</script> 
                                <script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js" >
                                </script> <a href="http://www.authorize.net/" id="AuthorizeNetText" target="_blank">Transaction Processing</a> 
                            </div>
                            <div style="float: left; padding-top: 20px;" >
                                <span id="siteseal">
                                    <script type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=I8Z60LIjbkqfCV32l9uWvc02aNmHy4ZwfcxB3iLkWdabyxDiGUHSCLwnh05u">
                                    </script>
                                </span>
                            </div>
                        </div>
                        <br /><br /><br /><br />
                        <p>Copyright (c) 2013 AirtelDirect.com. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
