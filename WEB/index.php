<?php
    require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Meow Industry</title>
        <link rel="icon" href="src/favicon.png" type="image/png">

        <link rel="stylesheet" href="style.css">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <script>
            function validateForm() {
                var selectedOption = document.querySelector('input[name="plan"]:checked');
                if (selectedOption === null) {
                    document.getElementById('loginFirst').innerHTML='Choose Plan First'
                    event.preventDefault();
                    return false;
                }
                return true;
              }

          </script>
        <script src="https://js.stripe.com/v3/"></script>
    </head>
    <body>
        <div class="header">
            <a href="index.php" class="logo">Meow Industry</a> 

            <div class="header-right-side">
                <a href="#contact" class="right-header">CONTACT</a>
                <a href="#faq" class="right-header">FAQ</a>
                <a href="#pricing" class="right-header">PRICING</a>
                <button class="dashboard-button" onclick="location.href='dashboard.php'" type="button">Dashboard</button>
            </div>
        </div>
        <div class="main">
            <p class="areYouTired">Are you tired of taking L's on release?</p>

            <p class="areYouTired2">Rent the best Nike bot and never miss another drop!</p>


            <form action="checkout.php" method="POST" id="myForm">
                <div id="plans">
                    <label class="plan">
                        <p class="plans-header">Daily</p>
                        <p>•24/7 Support <br><br> •VENDOR Discord Server Access <br><br> •24h VENDOR BOT Access</p>
                        <input type="radio" name="plan" id="radio" value="plan1" onChange="wywolajAkcje(this.value)">
                    </label>
                    <label class="plan">
                        <p class="plans-header">Weekly</p>
                        <p>•24/7 Support <br><br> •VENDOR Discord Server Access <br><br> •7 Days VENDOR BOT Access</p>
                        <input type="radio" name="plan" id="radio" value="plan2" onChange="wywolajAkcje(this.value)">
                    </label>
                    <label class="plan">
                        <p class="plans-header">TEST CASE</p>
                        <p>•FREE TEST CASE <br><br> •FREE TEST CASE <br><br> •FREE TEST CASE</p>
                        <input type="radio" name="plan" id="radio" value="plan3" onChange="wywolajAkcje(this.value)">
                    </label>
                </div>
                <div id="loginFirst">
                    <?php
                    if (!isset($_SESSION['logged_in'])) {
                            // echo ;
                            echo 'LOGIN VIA DASHBOARD';
                        } else {
                            echo ' ';
                            
                        }
                    ?>
                </div>
                <div class="rent-button-div">
                    <?php
                        if (!isset($_SESSION['logged_in'])) {
                                echo '<a href="dashboard.php"><button class="rent-button" type="button">RENT</button></a>';
                        } else {
                                echo '<input class="rent-button" type="submit" onclick="validateForm()" value="RENT" />';
                        }
                    ?> 

                    <!-- <input class="rent-button"  type="submit" onclick="validateForm()" value="RENT" /> -->
                </div>
            </form>

            <div id="contact">
                <div class="contact-text">
                    <p class="contact-content">Stay in contact</p>
                    <p class="contact-content2">Do you have questions? Let us know!</p>
                </div>
                <div class="contact-icons">
                    <div class="dstwmail">
                        <img src="src/discord-icon.png" alt="twitter-icon" class="twitter-sign">
                        <label>Meow#2222</label>
                    </div>
                    <div  class="dstwmail">
                        <img src="src/twitter-icon.png" alt="twitter-icon" class="twitter-sign">
                        <label>OneBigMeeeeow</label>
                    </div>
                    <div class="dstwmail">
                        <img src="src/mail-icon.png" alt="twitter-icon" class="twitter-sign">
                        <label>meow.rents@gmail.com</label>
                    </div>
                </div>
            </div>
            
            <div id="faq">
                <div class="contact-text">
                    <p class="contact-content">FAQ</p>
                </div>
                <div class="contact-icons">
                    <p class="question">Can i loose Access to bot despite the payment?</p>
                    <p class="answer">Unfortunatelly yes, if you break the rules of the regulations (§1) <br>you will lose Access to Bot</p>
                    <p class="question">Can i get refund for my rent?</p>
                    <p class="answer">No, we dont offer refunds </p>
                </div>
            </div>

            <div id="pricing">
                <div class="contact-text">
                    <p class="contact-content">PRICING</p>
                </div>
                <div class="contact-icons">
                    <p class="question">Currently we offer three diferent rent plans:</p>
                    <p class="answer">Daily - 5€</p>
                    <p class="answer">Weekly - 10€</p>
                    <p class="answer">Monthly - 20€</p>

                </div>
            </div>
            

            <img src="src/usnrs.png" alt="fotka" class="main-page-photo">
   
        </div>
        <div class="center">
        <div class="footer">
            <p>Powered & Created by Meow Industry</p>
            <a href="therms.html" class="log-out-butt">Terms</a>
        </div>
        </div>
    </body>
</html>