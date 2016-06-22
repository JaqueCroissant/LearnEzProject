<?php
// It puts the code in the top.
?>

<div class="text-center">
    <div class="col-xs-12"><h1>Login</h1></div>
    
    <div class="col-xs-12">
        <form method="POST" action="" id='login'>
            <input type="text" id="username" placeholder="User Name:" class="login_input"><br/>
            <input type="password" id="password" placeholder="Password:" class="login_input"><br/><br/>
            <input type="hidden" id="token" value="">
            <input type="submit" id="submit" value="Login" class="login_submit">
        </form>
    </div>
</div>
