<?php
$organization_name = $this->organization_name;

if(!empty($organization_name)) {
    $organization_name = "
        <p>
            <span class=\"bold\">Organization:&nbsp;</span>{$organization_name}
        </p>
    ";
}

$site_url = $this->site_url;

if(!empty($site_url)) {
    $site_url = "
        <a href=\"{$site_url}\" target=\"_blank\">
            <span class=\"bold\">URL</span>:&nbsp;{$site_url}
        </a>
    ";
}

$skills_used = $this->skills_used;

if(!empty($skills_used)) {
    $skills_used = "
        <p>
            <span class=\"bold\">Skills Used:&nbsp;</span>{$skills_used}
        </p>
    ";
}

$involvement_description = $this->involvment_description;

if(!empty($involvment_description)) {
    $involvement_description = "
        <p class=\"portfolio_description\">
            {$involvment_description}
        </p>
    ";
}
?>
<div class="portfolio_project">
    <h4><?php echo $this->project_name; ?></h4>
    <div class="portfolio_project_images">
        <?php echo $this->portfolio_project_images; ?>
    </div>
    <?php echo $organization_name; ?>
    <?php echo $site_url; ?>
    <?php echo $skills_used; ?>
    <p class="portfolio_description">
        <?php echo $this->portfolio_description; ?>
    </p>
    <?php echo $involvement_description; ?>
    <div class="clear"></div>
</div>