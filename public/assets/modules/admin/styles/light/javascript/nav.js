var top_nav_links;

function generate_hover_menu() {
    if(typeof top_nav_links != 'undefined') {
        //Loop through the entire object
        for(top_nav_item in top_nav_links) {
            var top_hover_menu_html = '<ul id="' + top_nav_item + '" class="hover_menu">';
            
            //The sub nav object
            if(top_nav_links.hasOwnProperty(top_nav_item)) {                
                var sub_nav_links = top_nav_links[top_nav_item];
            
                for(sub_nav_header in sub_nav_links) {
                    top_hover_menu_html += '<li class="hover_menu_header">' + sub_nav_header + '</li>';
                
                    //The sub nav section
                    if(sub_nav_links.hasOwnProperty(sub_nav_header)) {
                        sub_nav_section = sub_nav_links[sub_nav_header];
                    
                        for(sub_nav_item in sub_nav_section) {
                            //The sub nav section links
                            if(sub_nav_section.hasOwnProperty(sub_nav_item)) {
                                var sub_nav_url = sub_nav_section[sub_nav_item];
                                
                                top_hover_menu_html += '<li class="hover_menu_item"><a href="' + sub_nav_url + '">' + sub_nav_item + '</a></li>';
                            }
                        }
                    }
                }
            }
            
            top_hover_menu_html += '</ul>';
            
            $('#' + top_nav_item).append(top_hover_menu_html);
        }
    }
}

function set_top_nav_hover() {
    $('.top_nav_hover').mouseover(function() {
        $(this).children('.hover_menu').show();
    });
    
    $('.top_nav_hover').mouseout(function() {
        $(this).children('.hover_menu').hide();
    });
}

$(document).ready(function() {
    generate_hover_menu();
    
    set_top_nav_hover();
});