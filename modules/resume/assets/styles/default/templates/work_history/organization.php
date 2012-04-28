<div class="work_history_organization">
    <div class="organization_position">
        <p><span class="bold"><?php echo $this->job_title; ?></span></p>
        <p><?php echo $this->organization_name; ?></p>
    </div>
    <div class="organization_duration">
        <?php echo $this->organization_duration; ?>
    </div>
    <div class="clear"></div>
    <?php $this->organization_tasks; ?>
</div>