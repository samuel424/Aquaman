<form action="receive_login.php" method="POST">
    <input type="text" placeholder="Enter name" name="Name"><br>
    <div class="elem-group">
        <label for="captcha">Please Enter the Captcha Text</label><br>
        <img src="captcha.php" alt="CAPTCHA" class="captcha-image"><i class="fas fa-redo refresh-captcha"></i>
        <!-- idk what class does -->
        <br>
        <input type="text" id="captcha" name="captcha_challenge" pattern="[A-Z]{6}">
    </div>
    <input type="submit" value="Send name">
</form>

    