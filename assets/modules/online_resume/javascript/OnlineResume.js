$(document).ready(function() {
    var active_tab;
    var content;
    var inner_content;
    var education;
    var skills;
    var experience;
    var portfolio;
    var example_code;

    var hide_content = function() {
        education.hide();
        skills.hide();
        experience.hide();
        portfolio.hide();
        example_code.hide();
    };

    //If the browser is not IE then load the Javascript for tab functionality
    if(jQuery.support.leadingWhitespace) {
        active_tab = $('.active_tab');
        content = $('#content');
        inner_content = $('#inner_content');
        education = $('#education');
        skills = $('#skills');
        experience = $('#experience');
        portfolio = $('#portfolio');
        example_code = $('#example_code');
        
        hide_content();
            
        $('.tab').click(function() {
            var selected_tab = $(this);
            var selected_tab_href = selected_tab.attr('href');
    
            if(active_tab.length == 0 || selected_tab_href != active_tab.attr('href')) {
                inner_content.hide(0, function() {
                    //Hide the content sections
                    hide_content();
                    
                    //Remove the active styling on the previous active tab
                    if(active_tab != undefined && active_tab.length > 0) {
                        active_tab.removeClass('active_tab');
                    }
                    
                    //Set the current tab as active
                    selected_tab.addClass('active_tab');
                    
                    active_tab = selected_tab;
                    
                    //Determine the content to select and show it                    
                    switch(selected_tab_href) {
                        case '#education':
                            education.show();
                            break;
                        case '#skills':
                            skills.show();
                            break;
                        case '#experience':
                            experience.show();
                            break;
                        case '#portfolio':
                            portfolio.show();
                            break;
                        case '#example_code':
                            example_code.show();
                            break;
                        case '#view_all':
                            education.show();
                            skills.show();
                            experience.show();
                            portfolio.show();
                            example_code.show();
                            break;
                    }
                    
                    inner_content.show();
                });
            }
            
            return false;
        });
        
        //If a hash exists in the url then attempt to select the corresponding tab
        if(window.location.hash.length > 0) {
            $('a[href="' + window.location.hash + '"]').click();
        }
        //Otherwise select the education tab
        else {
            $('a[href="#education"]').click();
        }
    }
    
    $('div.portfolio_project_images').each(function() {
        $(this).children('a').colorbox({
            transition: "none", 
            width: "75%", 
            height: "75%"
        });
    });
});