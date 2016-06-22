<div class="pull-left col-xs-3">
    <h1>LearnEZ</h1>
</div>

<div class="pull-right col-xs-3">
    <?php
        // check if user is logged in or not.
        if (true) {
            echo '  <a href="?page=login">
                        <div class="menu_text col-xs-1 pull-right">Login</div>
                    </a>';
        } else {
            echo '  <a href="?page=settings">
                        <div class="menu_text col-xs-1 pull-right">
                            <img src="assets/images/ic_account_box_black_24dp/web/ic_account_box_black_24dp_1x.png" class="menu_icon">
                        </div>
                    </a>';
        }
    ?>
</div>