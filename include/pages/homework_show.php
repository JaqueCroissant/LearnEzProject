<?php
require_once 'require.php';
require_once '../../include/handler/homeworkHandler.php';

$current_user = SessionKeyHandler::get_from_session("user", true);
$homeworkHandler = new HomeworkHandler();
$homeworkHandler->get_user_homework();
?>

<div class="profile-header" style="margin: -1.5rem -1.5rem 1.5rem -1.5rem !important;background: #fff;padding: 20px 0px;">
    <div class="row" style="margin:0px !important;">
        <div class="col-md-5 col-center">
            <div class="fc-toolbar">
                <table style="width:100%;">
                    <tr>
                        <td style="text-align:left;">Titel</td>
                        <td style="text-align:right;">lol</td>
                    </tr>
                    <tr>
                        <td style="text-align:left;">lol</td>
                        <td style="text-align:right;">lol</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>