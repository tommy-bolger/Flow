<li class="nav-item<?php if($this->page_name == 'home'): ?> active<?php endif; ?>">
    <a class="nav-link" href="/admin">Home</a>
</li>
<li class="nav-item<?php if($this->page_name == 'errors'): ?> active<?php endif; ?>">
    <a class="nav-link" href="/admin/errors">Errors</a>
</li>
<li class="nav-item<?php if($this->page_name == 'settings'): ?> active<?php endif; ?>">
    <a class="nav-link" href="/admin/settings/general">Settings</a>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle<?php if($this->page_name == 'settings'): ?> active<?php endif; ?>" href="/admin/settings" id="settings_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Settings
    </a>
    <div class="dropdown-menu" aria-labelledby="settings_dropdown">
        <a class="dropdown-item<?php if($this->sub_page_name == 'general'): ?> active<?php endif; ?>" href="/admin/settings/general">General</a>
        <a class="dropdown-item<?php if($this->sub_page_name == 'meta'): ?> active<?php endif; ?>" href="/admin/settings/meta/manage">Meta Tags</a>
        <a class="dropdown-item<?php if($this->sub_page_name == 'permissions'): ?> active<?php endif; ?>" href="/admin/settings/permissions/manage">Permissions</a>
        <a class="dropdown-item<?php if($this->sub_page_name == 'roles'): ?> active<?php endif; ?>" href="/admin/settings/roles/manage">Roles</a>
        <a class="dropdown-item<?php if($this->sub_page_name == 'static_pages'): ?> active<?php endif; ?>" href="/admin/settings/staticpages/manage">Static Pages</a>
    </div>
</li>
<li class="nav-item<?php if($this->page_name == 'administrators'): ?> active<?php endif; ?>">
    <a class="nav-link" href="/admin/administrators/manage">Administrators</a>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle<?php if($this->page_name == 'bans'): ?> active<?php endif; ?>" href="#" id="bans_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Bans
    </a>
    <div class="dropdown-menu" aria-labelledby="bans_dropdown">
        <a class="dropdown-item<?php if($this->sub_page_name == 'ip_addresses'): ?> active<?php endif; ?>" href="/admin/bans/ipaddresses/manage">IP Addresses</a>
        <a class="dropdown-item<?php if($this->sub_page_name == 'words'): ?> active<?php endif; ?>" href="/admin/bans/words/manage">Words</a>
    </div>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle<?php if($this->page_name == 'ads'): ?> active<?php endif; ?>" href="#" id="ads_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Ads
    </a>
    <div class="dropdown-menu" aria-labelledby="ads_dropdown">
        <a class="dropdown-item<?php if($this->sub_page_name == 'ads'): ?> active<?php endif; ?>" href="/admin/ads/manage">Ads</a>
        <a class="dropdown-item<?php if($this->sub_page_name == 'campaigns'): ?> active<?php endif; ?>" href="/admin/ads/campaigns/manage">Campaigns</a>
        <a class="dropdown-item<?php if($this->sub_page_name == 'campaign_ads'): ?> active<?php endif; ?>" href="/admin/ads/campaigns/ads/manage">Campaign Ads  </a>
    </div>
</li>