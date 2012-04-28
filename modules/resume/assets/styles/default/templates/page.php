<?php
$email_address = $this->email_address;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
    <head>
        <?php echo $this->title; ?>
        <?php echo $this->meta_tags; ?>
        <?php echo $this->css; ?>
        <?php echo $this->javascript; ?>
    </head>
    <body>
        <div id="canvas">
            <div id="top_content">
            	<div id="header">
                    <div id="top_header">
                        <div id="photo_outer_container">
                            <div id="photo_inner_container">
                                <img id="photo" src="<?php echo $this->photo_url; ?>" />
                            </div>
                        </div>
                        <div id="personal_information">
                            <h1><?php echo $this->name; ?></h1>
                            <h3><?php echo $this->specialty; ?></h3>
                            <h4><?php echo $this->address; ?></h4>
                            <?php echo $this->phone_number; ?>
                            <h4><a href="mailto:<?php echo $email_address; ?>"><?php echo $email_address; ?></a></h4>
                        </div>
                        <div id="toolbar">
                            <a id="print_pdf_link" target="_blank" href="<?php echo $this->print_pdf_url; ?>">Print PDF</a> | 
                            <a id="print_word_link" target="_blank" href="<?php echo $this->print_word_url; ?>">Print Word</a> | 
                            <a href="mailto:<?php echo $email_address; ?>">Email</a>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div id="description">
                        <?php echo $this->description; ?>
                    </div>
                    <hr />
            	</div>
            	<div id="tab_bar">
                    <a class="tab" href="#education">Education</a>
                    <a class="tab" href="#skills">Skills</a>
                    <a class="tab" href="#experience">Experience</a>
                    <a class="tab" href="#portfolio">Portfolio</a>
                    <a class="tab" href="#example_code">Code Examples</a>
                    <a class="tab" href="#view_all">View All</a>
                </div>
            	<div id="content">
                    <div id="inner_content">
                        <a id="view_all"></a>
                		<div id="education">
                            <h2>Education</h2>
                            <div id="education_institutions">
                               <?php echo $this->education_institutions; ?>
                            </div>
                            <hr />
                		</div>
                		<div id="skills">
                            <h2>Skills</h2>
                            <?php echo $this->skills_list; ?>
                            <hr />
                		</div>
                		<div id="experience">
                            <h2>Experience</h2>
                            <div id="work_history">
                                <?php echo $this->work_history; ?>
                            </div>
                            <hr />
                		</div>
                		<div id="portfolio">
                            <h2>Portfolio</h2>
                            <div id="portfolio_projects">
                                <?php echo $this->portfolio_projects; ?>
                            </div>
                            <hr />
                		</div>
                		<div id="example_code">
                            <h2>Code Examples</h2>
                            <div id="code_examples">
                                <?php echo $this->code_examples; ?>
                            </div>
                            <hr />
                		</div>
                	</div>
            	</div>
        	</div>
        </div>
    </body>
</html>