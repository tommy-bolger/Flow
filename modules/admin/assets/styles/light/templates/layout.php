<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="Cache-Control" content="no-store" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma" content="no-cache" />
    <?php echo $this->meta_tags; ?>
    <?php echo $this->title; ?>
    <?php echo $this->css; ?>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="/admin"><h2>Administration Control Panel</h2></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav">
                <?php echo $this->navigation; ?>
            </ul>
        </div>
        <div class="col-1">
            <small class="text-light">Welcome <strong><?php echo $this->user_name; ?></strong>!</small>
        </div>
        <a class="btn bg-secondary text-light" href="/admin/logout">Logout</a>
    </nav>
    <ol class="breadcrumb">
        <!-- <li class="breadcrumb-item active">Home</li> -->
        <?php echo $this->breadcrumbs; ?>
    </ol>   
    <div class="container-fluid">
        <?php echo $this->content; ?>
    </div>
    <nav class="navbar fixed-bottom navbar-dark bg-primary">
        <div class="row">
            <div class="col-6">
                <a class="navbar-brand text-light" href="https://github.com/tommy-bolger/Flow" target="_blank">Powered by Flow v<?php echo $this->version; ?></a>
            </div>
            <div class="col-6">
                <div class="row align-items-end">
                    <div class="col">
                        <a class="navbar-brand text-light p-0" href="https://github.com/tommy-bolger/Flow" target="_blank">&copy; Tommy Bolger</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <small class="text-light p-0 mt-auto">All rights reserved.</small> 
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <?php echo $this->javascript; ?>
</body>
</html>