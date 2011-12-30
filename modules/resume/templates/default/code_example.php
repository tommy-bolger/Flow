<?php
$organization_name = $this->organization_name;

if(!empty($organization_name)) {
    $organization_name = "
        <p>
            <span class=\"bold\">Organization:&nbsp;</span>{$organization_name}
        </p>
    ";
}

$project_name = $this->project_name;

if(!empty($project_name)) {
    $project_name = "
        <p>
            <span class=\"bold\">Portfolio Project:&nbsp;</span>{$project_name}
        </p>
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
?>
<div class="code_example">
    <h4><?php echo $this->code_example_name; ?></h4>
    <div class="code_example_source">
        <a href="<?php echo $this->source_url; ?>" target="_blank">Download Source</a>
    </div>
    <?php echo $organization_name; ?>
    <?php echo $project_name; ?>
    <?php echo $skills_used; ?>
    <p class="code_example_description">
        <?php echo $this->purpose; ?>
    </p>
    <p class="code_example_description">
        <?php echo $this->description; ?>
    </p>
</div>