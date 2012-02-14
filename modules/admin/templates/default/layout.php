<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
    <head>
        <?php echo $this->title; ?>
        <?php echo $this->meta_tags; ?>
        <?php echo $this->css; ?>
        <?php echo $this->javascript; ?>
    </head>
    <body>
        <div id="canvas">
        	<div id="header">
        		<h1>
                    <a id="home_link" href="<?php echo $this->home_link; ?>">Administration Control Panel</a>
                </h1>
        	</div>
        	<div id="content">
        		<div id="left_content">
                    <div id="login_info" class="normal_size_text">
                        Hello, <b><?php echo $this->user_name; ?></b>
                    </div>
                    <a id="logout_link" href="<?php echo $this->logout_link; ?>">[Logout]</a>
                    <br />
                    <br />
                    <div id="module_menu">
                        <h2>Modules</h2>
                        <?php echo $this->modules_list; ?>
                    </div>
                    <br />
                    <div id="user_menu">
                        <h2>User Management</h2>
                        <?php echo $this->user_management_list; ?>
                    </div>
                    <br />
        			<div id="settings_menu">
                        <h2>Settings</h2>
                        <?php echo $this->settings_list; ?>
                    </div>
        		</div>
        		<div id="right_content" class="menu">
                    <?php echo $this->page_path; ?>
                    <br />
        			<?php echo $this->current_menu_content; ?>
        		</div>
        		<div class="clear"></div>
        	</div>
        	<div id="footer" class="footer_text">
        		Framework and pages created by Tommy Bolger &copy; <?php echo date('Y'); ?>. All rights reserved.
        	</div>
        </div>
    </body>
</html>