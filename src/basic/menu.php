<?php
if(!session_id()) {
    session_start();
}
?>

<link rel="stylesheet" href="/styles/menu.css">
<div class="menu">
    <?php
    if(isset($_SESSION['user_id'])) {
        ?>
        <span class="profile menu-button"><a href="/profile">profile</a></span>
        <span class="logout menu-button"><a href="/logout">logout</a></span>
        <span class="home menu-button"><a href="/">home</a></span>
        <?php
    } else {
        ?>
        <script src="/scripts/account.js"></script>
        <span class="home menu-button"><a href="/">home</a></span>
        <span class="login menu-button" onclick="createLogin()">login</span>
        <span class="sign-up menu-button" onclick="createSignUp()">sign up</span>
        <?php 
    } 
    ?>
</div>