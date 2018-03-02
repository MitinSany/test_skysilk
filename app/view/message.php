<div class="container">
    <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-<?= $param['style'] ?? 'info' ?>">
            <div class="panel-heading">
                <div class="panel-title"><?= $param['title'] ?></div>
            </div>

            <div class="panel-body">
                <?= $param['message'] ?>
            </div>
        </div>
    </div>
</div>
