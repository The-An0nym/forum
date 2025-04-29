<div class="menu">
    <?php
    if(isset($_SESSION['user_id'])) {
    ?>
    <span class="profile menu-button"><a href="/profile.php">profile</a></span>
    <span class="logout menu-button"><a href="/logout.php">logout</a></span>
    <?php
    } else {
    ?>
    <span class="login menu-button"><a href="/login.php">login</a></span>
    <span class="sign-up menu-button"><a href="/signup.php">sign up</a></span>
    <?php 
    } 
    ?>
</div>