$(document).ready(function() {
    $('div.menu-item').mouseover(function() {
        $(this).addClass('menu-item-hover');

        $(this).removeClass('menu-item');
    });
    
    $('div.menu-item').mouseout(function() {
        $(this).removeClass('menu-item-hover');
        
        $(this).addClass('menu-item');
    });

    $('div.menu-item').click(function() {
        window.location = $(this).children('a').attr('href');
    
        return false;
    });
});