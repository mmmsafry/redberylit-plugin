window.addEventListener("load", function () {

    // store tabs variables
    var tabs = document.querySelectorAll("ul.nav-tabs > li");

    for (i = 0; i < tabs.length; i++) {
        tabs[i].addEventListener("click", switchTab);
    }

    function switchTab(event) {
        event.preventDefault();

        document.querySelector("ul.nav-tabs li.active").classList.remove("active");
        document.querySelector(".tab-pane.active").classList.remove("active");

        var clickedTab = event.currentTarget;
        var anchor = event.target;
        var activePaneID = anchor.getAttribute("href");

        clickedTab.classList.add("active");
        document.querySelector(activePaneID).classList.add("active");

    }

});

jQuery(function ($) {
    $('.post-type-shop_order').find('.page-title-action').hide();
});

jQuery(function ($) {
    $('.toplevel_page_fluent_forms').find('.el-button').hide();
});

jQuery(function ($) {
    $('.post-type-post').find('.page-title-action').hide();
	$('.post-type-post').find('span.trash').hide();
	$('.toplevel_page_emailquote').find('.add-new-h2').hide();
});