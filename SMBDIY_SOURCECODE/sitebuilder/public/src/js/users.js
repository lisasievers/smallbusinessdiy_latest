(function () {
	"use strict";

	require('./modules/ui');
	require('./modules/users');
	require('./modules/account');
	require('./modules/sitesettings');
	require('./modules/sites');

	$('.userSites .site iframe').each(function(){

        var theHeight = $(this).attr('data-height')*0.25;
        var appUI = require('.modules/ui.js').appUI;
        //alert($(this).closest('.tab-content').innerWidth())

        $(this).zoomer({
            zoom: 0.20,
            height: theHeight,
            width: $(this).closest('.tab-content').width(),
            message: "",
            messageURL: appUI.siteUrl+"site/"+$(this).attr('data-siteid')
        });

        $(this).closest('.site').find('.zoomer-cover > a').attr('target', '');

    })

}());