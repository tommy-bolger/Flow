<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
    <head>
        <?php echo $this->title; ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
        <?php echo $this->meta_tags; ?>
        <?php echo $this->css; ?>
    </head>
    <body>
        <div id="canvas">
        	<div id="header">
                <div id="header_top">
                    <div id="header_logo">
                		<h1>
                            <a id="home_link" href="<?php echo $this->home_link; ?>">Administration Control Panel</a>
                        </h1>
                    </div>
                    <div id="login_info" class="normal_size_text">
                        Logged in as <strong><?php echo $this->user_name; ?></strong> | <?php echo date('l, F jS, Y'); ?> | 
                        <a id="logout_link" href="<?php echo $this->logout_link; ?>">Log Out</a>
                    </div>
                    <div class="clear"></div>
                </div>
                <div id="module_menu">
                    <?php echo $this->modules_list; ?>
                    <div class="clear"></div>
                </div>
        	</div>
        	<div id="content">
        		<div id="left_content">
                    <div id="sub_nav">
                        <?php echo $this->sub_nav; ?>
                    </div>
        		</div>
        		<div id="right_content" class="menu">
                    <?php echo $this->page_path; ?>
        			<?php echo $this->current_menu_content; ?>
        		</div>
        		<div class="clear"></div>
        	</div>
        	<div id="footer" class="footer_text">
                <div id="footer_transition_1"></div>
                <div id="footer_transition_2"></div>
                <div id="footer_text">
                    <div id="author_site">
                        <a href="https://github.com/tommy-bolger/Flow" target="_blank">Powered by Flow v<?php echo $this->version; ?></a>
                    </div>
                    <div id="legality">
                        Copyright &copy; <?php echo date('Y'); ?> Tommy Bolger.
                        <br />
                        All rights reserved.
                    </div>
                    <div class="clear"></div>
                </div>
        	</div>
        </div>
        <?php echo $this->javascript; ?>
    </body>
</html>