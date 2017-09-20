(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function () {
	"use strict";

	var appUI = require('./ui.js').appUI;

	var account = {

        buttonUpdateAccountDetails: document.getElementById('accountDetailsSubmit'),
        buttonUpdateLoginDetails: document.getElementById('accountLoginSubmit'),

        init: function() {

            $(this.buttonUpdateAccountDetails).on('click', this.updateAccountDetails);
            $(this.buttonUpdateLoginDetails).on('click', this.updateLoginDetails);

        },


        /*
            updates account details
        */
        updateAccountDetails: function() {

            //all fields filled in?

            var allGood = 1;

            if( $('#account_details input#firstname').val() === '' ) {
                $('#account_details input#firstname').closest('.form-group').addClass('has-error');
                allGood = 0;
            } else {
                $('#account_details input#firstname').closest('.form-group').removeClass('has-error');
                allGood = 1;
            }

            if( $('#account_details input#lastname').val() === '' ) {
                $('#account_details input#lastname').closest('.form-group').addClass('has-error');
                allGood = 0;
            } else {
                $('#account_details input#lastname').closest('.form-group').removeClass('has-error');
                allGood = 1;
            }

            if( allGood === 1 ) {

                var theButton = $(this);

                //disable button
                $(this).addClass('disabled');

                //show loader
                $('#account_details .loader').fadeIn(500);

                //remove alerts
                $('#account_details .alerts > *').remove();

                $.ajax({
                    url: appUI.siteUrl+"user/uaccount",
                    type: 'post',
                    dataType: 'json',
                    data: $('#account_details').serialize()
                }).done(function(ret){

                    //enable button
                    theButton.removeClass('disabled');

                    //hide loader
                    $('#account_details .loader').hide();
                    $('#account_details .alerts').append( $(ret.responseHTML) );

                    if( ret.responseCode === 1 ) {//success
                        setTimeout(function () {
                            $('#account_details .alerts > *').fadeOut(500, function () { $(this).remove(); });
                        }, 3000);
                    }
                });

            }

        },


        /*
            updates account login details
        */
        updateLoginDetails: function() {

			console.log(appUI);

            var allGood = 1;

            if( $('#account_login input#email').val() === '' ) {
                $('#account_login input#email').closest('.form-group').addClass('has-error');
                allGood = 0;
            } else {
                $('#account_login input#email').closest('.form-group').removeClass('has-error');
                allGood = 1;
            }

            if( $('#account_login input#password').val() === '' ) {
                $('#account_login input#password').closest('.form-group').addClass('has-error');
                allGood = 0;
            } else {
                $('#account_login input#password').closest('.form-group').removeClass('has-error');
                allGood = 1;
            }

            if( allGood === 1 ) {

                var theButton = $(this);

                //disable button
                $(this).addClass('disabled');

                //show loader
                $('#account_login .loader').fadeIn(500);

                //remove alerts
                $('#account_login .alerts > *').remove();

                $.ajax({
                    url: appUI.siteUrl+"user/ulogin",
                    type: 'post',
                    dataType: 'json',
                    data: $('#account_login').serialize()
                }).done(function(ret){

                    //enable button
                    theButton.removeClass('disabled');

                    //hide loader
                    $('#account_login .loader').hide();
                    $('#account_login .alerts').append( $(ret.responseHTML) );

                    if( ret.responseCode === 1 ) {//success
                        setTimeout(function () {
                            $('#account_login .alerts > *').fadeOut(500, function () { $(this).remove(); });
                        }, 3000);
                    }

                });

            }

        }

    };

    account.init();

}());
},{"./ui.js":4}],2:[function(require,module,exports){
(function () {
	"use strict";

	var appUI = require('./ui.js').appUI;

	var sites = {

        wrapperSites: document.getElementById('sites'),
        selectUser: document.getElementById('userDropDown'),
        selectSort: document.getElementById('sortDropDown'),
        buttonDeleteSite: document.getElementById('deleteSiteButton'),
		buttonsDeleteSite: document.querySelectorAll('.deleteSiteButton'),

        init: function() {

            this.createThumbnails();

            $(this.selectUser).on('change', this.filterUser);
            $(this.selectSort).on('change', this.changeSorting);
            $(this.buttonsDeleteSite).on('click', this.deleteSite);
			$(this.buttonDeleteSite).on('click', this.deleteSite);

        },


        /*
            applies zoomer to create the iframe thubmnails
        */
        createThumbnails: function() {

            $(this.wrapperSites).find('iframe').each(function(){

                var theHeight = $(this).attr('data-height')*0.25;

                $(this).zoomer({
                    zoom: 0.25,
                    height: theHeight,
                    width: $(this).parent().width(),
                    message: "",
                    messageURL: appUI.siteUrl+"site/"+$(this).attr('data-siteid')
                });

                $(this).closest('.site').find('.zoomer-cover > a').attr('target', '');

            });

        },


        /*
            filters the site list by selected user
        */
        filterUser: function() {

            if( $(this).val() === 'All' || $(this).val() === '' ) {
                $('#sites .site').hide().fadeIn(500);
            } else {
                $('#sites .site').hide();
                $('#sites').find('[data-name="'+$(this).val()+'"]').fadeIn(500);
            }

        },


        /*
            chnages the sorting on the site list
        */
        changeSorting: function() {

            var sites;

            if( $(this).val() === 'NoOfPages' ) {

				sites = $('#sites .site');

				sites.sort( function(a,b){

                    var an = a.getAttribute('data-pages');
					var bn = b.getAttribute('data-pages');

					if(an > bn) {
						return 1;
					}

					if(an < bn) {
						return -1;
					}

					return 0;

				} );

				sites.detach().appendTo( $('#sites') );

			} else if( $(this).val() === 'CreationDate' ) {

				sites = $('#sites .site');

				sites.sort( function(a,b){

					var an = a.getAttribute('data-created').replace("-", "");
					var bn = b.getAttribute('data-created').replace("-", "");

					if(an > bn) {
						return 1;
					}

					if(an < bn) {
						return -1;
					}

					return 0;

				} );

				sites.detach().appendTo( $('#sites') );

			} else if( $(this).val() === 'LastUpdate' ) {

				sites = $('#sites .site');

				sites.sort( function(a,b){

					var an = a.getAttribute('data-update').replace("-", "");
					var bn = b.getAttribute('data-update').replace("-", "");

					if(an > bn) {
						return 1;
					}

					if(an < bn) {
						return -1;
					}

				return 0;

				} );

				sites.detach().appendTo( $('#sites') );

			}

        },


        /*
            deletes a site
        */
        deleteSite: function(e) {

            e.preventDefault();

            $('#deleteSiteModal .modal-content p').show();

            //remove old alerts
            $('#deleteSiteModal .modal-alerts > *').remove();
            $('#deleteSiteModal .loader').hide();

            var toDel = $(this).closest('.site');
            var delButton = $(this);

            $('#deleteSiteModal button#deleteSiteButton').show();
            $('#deleteSiteModal').modal('show');

            $('#deleteSiteModal button#deleteSiteButton').unbind('click').click(function(){

                $(this).addClass('disabled');
                $('#deleteSiteModal .loader').fadeIn(500);

                $.ajax({
                    url: appUI.siteUrl+"site/trash/"+delButton.attr('data-siteid'),
                    type: 'get',
                    dataType: 'json'
                }).done(function(ret){

                    $('#deleteSiteModal .loader').hide();
                    $('#deleteSiteModal button#deleteSiteButton').removeClass('disabled');

                    if( ret.responseCode === 0 ) {//error

                        $('#deleteSiteModal .modal-content p').hide();
                        $('#deleteSiteModal .modal-alerts').append( $(ret.responseHTML) );

                    } else if( ret.responseCode === 1 ) {//all good

                        $('#deleteSiteModal .modal-content p').hide();
                        $('#deleteSiteModal .modal-alerts').append( $(ret.responseHTML) );
                        $('#deleteSiteModal button#deleteSiteButton').hide();

                        toDel.fadeOut(800, function(){
                            $(this).remove();
                        });
                    }

                });
            });

        }

    };

    sites.init();

}());
},{"./ui.js":4}],3:[function(require,module,exports){
(function () {
	"use strict";

	var appUI = require('./ui.js').appUI;

	var siteSettings = {

        //buttonSiteSettings: document.getElementById('siteSettingsButton'),
		buttonSiteSettings2: $('.siteSettingsModalButton'),
        buttonSaveSiteSettings: document.getElementById('saveSiteSettingsButton'),

        init: function() {

            //$(this.buttonSiteSettings).on('click', this.siteSettingsModal);
			this.buttonSiteSettings2.on('click', this.siteSettingsModal);
            $(this.buttonSaveSiteSettings).on('click', this.saveSiteSettings);

        },

        /*
            loads the site settings data
        */
        siteSettingsModal: function(e) {

            e.preventDefault();

    		$('#siteSettings').modal('show');

    		//destroy all alerts
    		$('#siteSettings .alert').fadeOut(500, function(){

    			$(this).remove();

    		});

    		//set the siteID
    		$('input#siteID').val( $(this).attr('data-siteid') );

    		//destroy current forms
    		$('#siteSettings .modal-body-content > *').each(function(){
    			$(this).remove();
    		});

            //show loader, hide rest
    		$('#siteSettingsWrapper .loader').show();
    		$('#siteSettingsWrapper > *:not(.loader)').hide();

    		//load site data using ajax
    		$.ajax({
                url: appUI.siteUrl+"siteAjax/"+$(this).attr('data-siteid'),
    			type: 'get',
    			dataType: 'json'
    		}).done(function(ret){

    			if( ret.responseCode === 0 ) {//error

    				//hide loader, show error message
    				$('#siteSettings .loader').fadeOut(500, function(){

    					$('#siteSettings .modal-alerts').append( $(ret.responseHTML) );

    				});

    				//disable submit button
    				$('#saveSiteSettingsButton').addClass('disabled');


    			} else if( ret.responseCode === 1 ) {//all well :)

    				//hide loader, show data

    				$('#siteSettings .loader').fadeOut(500, function(){

    					$('#siteSettings .modal-body-content').append( $(ret.responseHTML) );

                        $('body').trigger('siteSettingsLoad');

    				});

    				//enable submit button
    				$('#saveSiteSettingsButton').removeClass('disabled');

    			}

    		});

        },


        /*
            saves the site settings
        */
        saveSiteSettings: function() {

            //destroy all alerts
    		$('#siteSettings .alert').fadeOut(500, function(){

    			$(this).remove();

    		});

    		//disable button
    		$('#saveSiteSettingsButton').addClass('disabled');

    		//hide form data
    		$('#siteSettings .modal-body-content > *').hide();

    		//show loader
    		$('#siteSettings .loader').show();

    		$.ajax({
                url: appUI.siteUrl+"siteAjaxUpdate",
    			type: 'post',
    			dataType: 'json',
    			data: $('form#siteSettingsForm').serializeArray()
    		}).done(function(ret){

    			if( ret.responseCode === 0 ) {//error

    				$('#siteSettings .loader').fadeOut(500, function(){

    					$('#siteSettings .modal-alerts').append( ret.responseHTML );

    					//show form data
    					$('#siteSettings .modal-body-content > *').show();

    					//enable button
    					$('#saveSiteSettingsButton').removeClass('disabled');

    				});


    			} else if( ret.responseCode === 1 ) {//all is well

    				$('#siteSettings .loader').fadeOut(500, function(){


    					//update site name in top menu
    					$('#siteTitle').text( ret.siteName );

    					$('#siteSettings .modal-alerts').append( ret.responseHTML );

    					//hide form data
    					$('#siteSettings .modal-body-content > *').remove();
    					$('#siteSettings .modal-body-content').append( ret.responseHTML2 );

    					//enable button
    					$('#saveSiteSettingsButton').removeClass('disabled');

    					//is the FTP stuff all good?

    					if( ret.ftpOk === 1 ) {//yes, all good

    						$('#publishPage').removeAttr('data-toggle');
    						$('#publishPage span.text-danger').hide();

    						$('#publishPage').tooltip('destroy');

    					} else {//nope, can't use FTP

    						$('#publishPage').attr('data-toggle', 'tooltip');
    						$('#publishPage span.text-danger').show();

    						$('#publishPage').tooltip('show');

    					}


    					//update the site name in the small window
    					$('#site_'+ret.siteID+' .window .top b').text( ret.siteName );

    				});


    			}

    		});

        },


    };

    siteSettings.init();

}());
},{"./ui.js":4}],4:[function(require,module,exports){
(function () {

/* globals siteUrl:false, baseUrl:false */
    "use strict";
        
    var appUI = {
        
        firstMenuWidth: 190,
        secondMenuWidth: 300,
        loaderAnimation: document.getElementById('loader'),
        secondMenuTriggerContainers: $('#menu #main #elementCats, #menu #main #templatesUl'),
        siteUrl: siteUrl,
        baseUrl: baseUrl,
        
        setup: function(){
            
            // Fade the loader animation
            $(appUI.loaderAnimation).fadeOut(function(){
                $('#menu').animate({'left': -appUI.firstMenuWidth}, 1000);
            });
            
            // Tabs
            $(".nav-tabs a").on('click', function (e) {
                e.preventDefault();
                $(this).tab("show");
            });
            
            $("select.select").select2();
            
            $(':radio, :checkbox').radiocheck();
            
            // Tooltips
            $("[data-toggle=tooltip]").tooltip("hide");
            
            // Table: Toggle all checkboxes
            $('.table .toggle-all :checkbox').on('click', function () {
                var $this = $(this);
                var ch = $this.prop('checked');
                $this.closest('.table').find('tbody :checkbox').radiocheck(!ch ? 'uncheck' : 'check');
            });
            
            // Add style class name to a tooltips
            $(".tooltip").addClass(function() {
                if ($(this).prev().attr("data-tooltip-style")) {
                    return "tooltip-" + $(this).prev().attr("data-tooltip-style");
                }
            });
            
            $(".btn-group").on('click', "a", function() {
                $(this).siblings().removeClass("active").end().addClass("active");
            });
            
            // Focus state for append/prepend inputs
            $('.input-group').on('focus', '.form-control', function () {
                $(this).closest('.input-group, .form-group').addClass('focus');
            }).on('blur', '.form-control', function () {
                $(this).closest('.input-group, .form-group').removeClass('focus');
            });
            
            // Table: Toggle all checkboxes
            $('.table .toggle-all').on('click', function() {
                var ch = $(this).find(':checkbox').prop('checked');
                $(this).closest('.table').find('tbody :checkbox').checkbox(!ch ? 'check' : 'uncheck');
            });
            
            // Table: Add class row selected
            $('.table tbody :checkbox').on('check uncheck toggle', function (e) {
                var $this = $(this)
                , check = $this.prop('checked')
                , toggle = e.type === 'toggle'
                , checkboxes = $('.table tbody :checkbox')
                , checkAll = checkboxes.length === checkboxes.filter(':checked').length;

                $this.closest('tr')[check ? 'addClass' : 'removeClass']('selected-row');
                if (toggle) $this.closest('.table').find('.toggle-all :checkbox').checkbox(checkAll ? 'check' : 'uncheck');
            });
            
            // Switch
            $("[data-toggle='switch']").wrap('<div class="switch" />').parent().bootstrapSwitch();
                        
            appUI.secondMenuTriggerContainers.on('click', 'a:not(.btn)', appUI.secondMenuAnimation);
                        
        },
        
        secondMenuAnimation: function(){
        
            $('#menu #main a').removeClass('active');
            $(this).addClass('active');
	
            //show only the right elements
            $('#menu #second ul li').hide();
            $('#menu #second ul li.'+$(this).attr('id')).show();

            if( $(this).attr('id') === 'all' ) {
                $('#menu #second ul#elements li').show();		
            }
	
            $('.menu .second').css('display', 'block').stop().animate({
                width: appUI.secondMenuWidth
            }, 500);	
                
        }
        
    };
    
    //initiate the UI
    appUI.setup();


    //**** EXPORTS
    module.exports.appUI = appUI;
    
}());
},{}],5:[function(require,module,exports){
(function () {
	"use strict";

	var appUI = require('./ui.js').appUI;

	var users = {

        buttonCreateAccount: document.getElementById('buttonCreateAccount'),
        wrapperUsers: document.getElementById('users'),

        init: function() {

            $(this.buttonCreateAccount).on('click', this.createAccount);
            $(this.wrapperUsers).on('click', '.updateUserButton', this.updateUser);
            $(this.wrapperUsers).on('click', '.passwordReset', this.passwordReset);
            $(this.wrapperUsers).on('click', '.deleteUserButton', this.deleteUser);

        },


        /*
            creates a new user account
        */
        createAccount: function() {

            //all items present?

            var allGood = 1;

            if( $('#newUserModal form input#firstname').val() === '' ) {
                $('#newUserModal form input#firstname').parent().addClass('has-error');
                allGood = 0;
            } else {
                $('#newUserModal form input#firstname').parent().removeClass('has-error');
            }

            if( $('#newUserModal form input#lastname').val() === '' ) {
                $('#newUserModal form input#lastname').parent().addClass('has-error');
                allGood = 0;
            } else {
                $('#newUserModal form input#lastname').parent().removeClass('has-error');
            }

            if( $('#newUserModal form input#email').val() === '' ) {
                $('#newUserModal form input#email').parent().addClass('has-error');
                allGood = 0;
            } else {
                $('#newUserModal form input#email').parent().removeClass('has-error');
            }

            if( $('#newUserModal form input#password').val() === '' ) {
                $('#newUserModal form input#password').parent().addClass('has-error');
                allGood = 0;
            } else {
                $('#newUserModal form input#password').parent().removeClass('has-error');
            }

            if( allGood === 1 ) {

                //remove old alerts
                $('#newUserModal .modal-alerts > *').hide();

                //disable button
                $(this).addClass('disabled');

                //show loader
                $('#newUserModal .loader').fadeIn();

                $.ajax({
                    url: $('#newUserModal form').attr('action'),
                    type: 'post',
                    dataType: 'json',
                    data:  $('#newUserModal form').serialize()
                }).done(function(ret){

                    //enable button
                    $('button#buttonCreateAccount').removeClass('disabled');

                    //hide loader
                    $('#newUserModal .loader').hide();

                    if( ret.responseCode === 0 ) {//error

                        $('#newUserModal .modal-alerts').append( $(ret.responseHTML) );

                    } else {//all good

                        $('#newUserModal .modal-alerts').append( $(ret.responseHTML) );
                        $('#users > *').remove();
                        $('#users').append( $(ret.users) );
                        $('#users form input[type="checkbox"]').checkbox();

                        ('.userSites .site iframe').each(function(){

                            var theHeight = $(this).attr('data-height')*0.25;

                            $(this).width(  );

                            $(this).zoomer({
                                zoom: 0.25,
                                height: theHeight,
                                width: $(this).closest('.tab-pane').width(),
                                message: "",
                                messageURL: appUI.siteUrl+"site/"+$(this).attr('data-siteid')
                            });

                            $(this).closest('.site').find('.zoomer-cover > a').attr('target', '');

                        });

                    }

                });

            }

        },


        /*
            updates a user
        */
        updateUser: function() {

            //disable button
            var theButton = $(this);
            $(this).addClass('disabled');

            //show loader
            $(this).closest('.bottom').find('.loader').fadeIn(500);

            $.ajax({
                url: $(this).closest('form').attr('action'),
                type: 'post',
                dataType: 'json',
                data: $(this).closest('form').serialize()
            }).done(function(ret){

                //enable button
                theButton.removeClass('disabled');

                //hide loader
                theButton.closest('.bottom').find('.loader').hide();

                if( ret.responseCode === 0 ) {//error

                    theButton.closest('.bottom').find('.alerts').append( $(ret.responseHTML) );

                } else if(ret.responseCode === 1) {//all good

                    theButton.closest('.bottom').find('.alerts').append( $(ret.responseHTML) );

                    //append user detail form
                    var thePane = theButton.closest('.tab-pane');

                    setTimeout(function(){
                        thePane.closest('.bottom').find('.alert-success').fadeOut(500, function(){$(this.remove());});
                    }, 3000);

                    theButton.closest('form').remove();

                    thePane.prepend( $(ret.userDetailForm) );
                    thePane.find('form input[type="checkbox"]').checkbox();

                }

            });

        },


        /*
            password reset
        */
        passwordReset: function(e) {

            e.preventDefault();

            var theButton = $(this);

            //disable buttons
            $(this).addClass('disabled');
            $(this).closest('.bottom').find('.updateUserButton').addClass('disabled');

            //show loader
            $(this).closest('.bottom').find('.loader').fadeIn();

            $.ajax({
                url: appUI.siteUrl+"user-pw-reset-email/"+$(this).attr('data-userid'),
                type: 'get',
                dataType: 'json'
            }).done(function(ret){

                //enable buttons
                theButton.removeClass('disabled');
                theButton.closest('.bottom').find('.updateUserButton').removeClass('disabled');

                //hide loader
                theButton.closest('.bottom').find('.loader').hide();
                $(theButton).closest('.bottom').find('.alerts').append( $(ret.responseHTML) );

                if( ret.responseCode === 0 ) {//error

				} else if( ret.responseCode === 1 ) {//all good

                    setTimeout(function(){
                        theButton.closest('.bottom').find('.alerts > *').fadeOut(500, function(){$(this).remove();});
                    }, 3000);

                }

            });

        },


        /*
            deletes a user account
        */
        deleteUser: function(e) {

            e.preventDefault();

            //setup delete link
            $('#deleteUserModal a#deleteUserButton').attr('href', $(this).attr('href'));

            //modal
            $('#deleteUserModal').modal('show');

        }

    };

    users.init();

}());
},{"./ui.js":4}],6:[function(require,module,exports){
(function () {
	"use strict";

	require('./modules/ui');
	require('./modules/users');
	require('./modules/account');
	require('./modules/sitesettings');
	require('./modules/sites');

	$('.userSites .site iframe').each(function(){

        var theHeight = $(this).attr('data-height')*0.25;

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
},{"./modules/account":1,"./modules/sites":2,"./modules/sitesettings":3,"./modules/ui":4,"./modules/users":5}]},{},[6])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJwdWJsaWMvc3JjL2pzL21vZHVsZXMvYWNjb3VudC5qcyIsInB1YmxpYy9zcmMvanMvbW9kdWxlcy9zaXRlcy5qcyIsInB1YmxpYy9zcmMvanMvbW9kdWxlcy9zaXRlc2V0dGluZ3MuanMiLCJwdWJsaWMvc3JjL2pzL21vZHVsZXMvdWkuanMiLCJwdWJsaWMvc3JjL2pzL21vZHVsZXMvdXNlcnMuanMiLCJwdWJsaWMvc3JjL2pzL3VzZXJzLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FDQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDdEpBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUMzTUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3pMQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2hIQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQzNPQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uIGUodCxuLHIpe2Z1bmN0aW9uIHMobyx1KXtpZighbltvXSl7aWYoIXRbb10pe3ZhciBhPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7aWYoIXUmJmEpcmV0dXJuIGEobywhMCk7aWYoaSlyZXR1cm4gaShvLCEwKTt2YXIgZj1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK28rXCInXCIpO3Rocm93IGYuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixmfXZhciBsPW5bb109e2V4cG9ydHM6e319O3Rbb11bMF0uY2FsbChsLmV4cG9ydHMsZnVuY3Rpb24oZSl7dmFyIG49dFtvXVsxXVtlXTtyZXR1cm4gcyhuP246ZSl9LGwsbC5leHBvcnRzLGUsdCxuLHIpfXJldHVybiBuW29dLmV4cG9ydHN9dmFyIGk9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtmb3IodmFyIG89MDtvPHIubGVuZ3RoO28rKylzKHJbb10pO3JldHVybiBzfSkiLCIoZnVuY3Rpb24gKCkge1xuXHRcInVzZSBzdHJpY3RcIjtcblxuXHR2YXIgYXBwVUkgPSByZXF1aXJlKCcuL3VpLmpzJykuYXBwVUk7XG5cblx0dmFyIGFjY291bnQgPSB7XG5cbiAgICAgICAgYnV0dG9uVXBkYXRlQWNjb3VudERldGFpbHM6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdhY2NvdW50RGV0YWlsc1N1Ym1pdCcpLFxuICAgICAgICBidXR0b25VcGRhdGVMb2dpbkRldGFpbHM6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdhY2NvdW50TG9naW5TdWJtaXQnKSxcblxuICAgICAgICBpbml0OiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblVwZGF0ZUFjY291bnREZXRhaWxzKS5vbignY2xpY2snLCB0aGlzLnVwZGF0ZUFjY291bnREZXRhaWxzKTtcbiAgICAgICAgICAgICQodGhpcy5idXR0b25VcGRhdGVMb2dpbkRldGFpbHMpLm9uKCdjbGljaycsIHRoaXMudXBkYXRlTG9naW5EZXRhaWxzKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHVwZGF0ZXMgYWNjb3VudCBkZXRhaWxzXG4gICAgICAgICovXG4gICAgICAgIHVwZGF0ZUFjY291bnREZXRhaWxzOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9hbGwgZmllbGRzIGZpbGxlZCBpbj9cblxuICAgICAgICAgICAgdmFyIGFsbEdvb2QgPSAxO1xuXG4gICAgICAgICAgICBpZiggJCgnI2FjY291bnRfZGV0YWlscyBpbnB1dCNmaXJzdG5hbWUnKS52YWwoKSA9PT0gJycgKSB7XG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfZGV0YWlscyBpbnB1dCNmaXJzdG5hbWUnKS5jbG9zZXN0KCcuZm9ybS1ncm91cCcpLmFkZENsYXNzKCdoYXMtZXJyb3InKTtcbiAgICAgICAgICAgICAgICBhbGxHb29kID0gMDtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfZGV0YWlscyBpbnB1dCNmaXJzdG5hbWUnKS5jbG9zZXN0KCcuZm9ybS1ncm91cCcpLnJlbW92ZUNsYXNzKCdoYXMtZXJyb3InKTtcbiAgICAgICAgICAgICAgICBhbGxHb29kID0gMTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYoICQoJyNhY2NvdW50X2RldGFpbHMgaW5wdXQjbGFzdG5hbWUnKS52YWwoKSA9PT0gJycgKSB7XG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfZGV0YWlscyBpbnB1dCNsYXN0bmFtZScpLmNsb3Nlc3QoJy5mb3JtLWdyb3VwJykuYWRkQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgIGFsbEdvb2QgPSAwO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9kZXRhaWxzIGlucHV0I2xhc3RuYW1lJykuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKS5yZW1vdmVDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICAgICAgYWxsR29vZCA9IDE7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKCBhbGxHb29kID09PSAxICkge1xuXG4gICAgICAgICAgICAgICAgdmFyIHRoZUJ1dHRvbiA9ICQodGhpcyk7XG5cbiAgICAgICAgICAgICAgICAvL2Rpc2FibGUgYnV0dG9uXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgICAgIC8vc2hvdyBsb2FkZXJcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9kZXRhaWxzIC5sb2FkZXInKS5mYWRlSW4oNTAwKTtcblxuICAgICAgICAgICAgICAgIC8vcmVtb3ZlIGFsZXJ0c1xuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2RldGFpbHMgLmFsZXJ0cyA+IConKS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgICAgIHVybDogYXBwVUkuc2l0ZVVybCtcInVzZXIvdWFjY291bnRcIixcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogJ3Bvc3QnLFxuICAgICAgICAgICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgICAgICAgICAgICAgICBkYXRhOiAkKCcjYWNjb3VudF9kZXRhaWxzJykuc2VyaWFsaXplKClcbiAgICAgICAgICAgICAgICB9KS5kb25lKGZ1bmN0aW9uKHJldCl7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9lbmFibGUgYnV0dG9uXG4gICAgICAgICAgICAgICAgICAgIHRoZUJ1dHRvbi5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2hpZGUgbG9hZGVyXG4gICAgICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2RldGFpbHMgLmxvYWRlcicpLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgJCgnI2FjY291bnRfZGV0YWlscyAuYWxlcnRzJykuYXBwZW5kKCAkKHJldC5yZXNwb25zZUhUTUwpICk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDEgKSB7Ly9zdWNjZXNzXG4gICAgICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9kZXRhaWxzIC5hbGVydHMgPiAqJykuZmFkZU91dCg1MDAsIGZ1bmN0aW9uICgpIHsgJCh0aGlzKS5yZW1vdmUoKTsgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9LCAzMDAwKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICB1cGRhdGVzIGFjY291bnQgbG9naW4gZGV0YWlsc1xuICAgICAgICAqL1xuICAgICAgICB1cGRhdGVMb2dpbkRldGFpbHM6IGZ1bmN0aW9uKCkge1xuXG5cdFx0XHRjb25zb2xlLmxvZyhhcHBVSSk7XG5cbiAgICAgICAgICAgIHZhciBhbGxHb29kID0gMTtcblxuICAgICAgICAgICAgaWYoICQoJyNhY2NvdW50X2xvZ2luIGlucHV0I2VtYWlsJykudmFsKCkgPT09ICcnICkge1xuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2xvZ2luIGlucHV0I2VtYWlsJykuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKS5hZGRDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICAgICAgYWxsR29vZCA9IDA7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2xvZ2luIGlucHV0I2VtYWlsJykuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKS5yZW1vdmVDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICAgICAgYWxsR29vZCA9IDE7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKCAkKCcjYWNjb3VudF9sb2dpbiBpbnB1dCNwYXNzd29yZCcpLnZhbCgpID09PSAnJyApIHtcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9sb2dpbiBpbnB1dCNwYXNzd29yZCcpLmNsb3Nlc3QoJy5mb3JtLWdyb3VwJykuYWRkQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgIGFsbEdvb2QgPSAwO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9sb2dpbiBpbnB1dCNwYXNzd29yZCcpLmNsb3Nlc3QoJy5mb3JtLWdyb3VwJykucmVtb3ZlQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgIGFsbEdvb2QgPSAxO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiggYWxsR29vZCA9PT0gMSApIHtcblxuICAgICAgICAgICAgICAgIHZhciB0aGVCdXR0b24gPSAkKHRoaXMpO1xuXG4gICAgICAgICAgICAgICAgLy9kaXNhYmxlIGJ1dHRvblxuICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgICAgICAgICAvL3Nob3cgbG9hZGVyXG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfbG9naW4gLmxvYWRlcicpLmZhZGVJbig1MDApO1xuXG4gICAgICAgICAgICAgICAgLy9yZW1vdmUgYWxlcnRzXG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfbG9naW4gLmFsZXJ0cyA+IConKS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgICAgIHVybDogYXBwVUkuc2l0ZVVybCtcInVzZXIvdWxvZ2luXCIsXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6ICdwb3N0JyxcbiAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgICAgICAgICAgICAgICAgZGF0YTogJCgnI2FjY291bnRfbG9naW4nKS5zZXJpYWxpemUoKVxuICAgICAgICAgICAgICAgIH0pLmRvbmUoZnVuY3Rpb24ocmV0KXtcblxuICAgICAgICAgICAgICAgICAgICAvL2VuYWJsZSBidXR0b25cbiAgICAgICAgICAgICAgICAgICAgdGhlQnV0dG9uLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vaGlkZSBsb2FkZXJcbiAgICAgICAgICAgICAgICAgICAgJCgnI2FjY291bnRfbG9naW4gLmxvYWRlcicpLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgJCgnI2FjY291bnRfbG9naW4gLmFsZXJ0cycpLmFwcGVuZCggJChyZXQucmVzcG9uc2VIVE1MKSApO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCByZXQucmVzcG9uc2VDb2RlID09PSAxICkgey8vc3VjY2Vzc1xuICAgICAgICAgICAgICAgICAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2FjY291bnRfbG9naW4gLmFsZXJ0cyA+IConKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24gKCkgeyAkKHRoaXMpLnJlbW92ZSgpOyB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0sIDMwMDApO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cblxuICAgIH07XG5cbiAgICBhY2NvdW50LmluaXQoKTtcblxufSgpKTsiLCIoZnVuY3Rpb24gKCkge1xuXHRcInVzZSBzdHJpY3RcIjtcblxuXHR2YXIgYXBwVUkgPSByZXF1aXJlKCcuL3VpLmpzJykuYXBwVUk7XG5cblx0dmFyIHNpdGVzID0ge1xuXG4gICAgICAgIHdyYXBwZXJTaXRlczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NpdGVzJyksXG4gICAgICAgIHNlbGVjdFVzZXI6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCd1c2VyRHJvcERvd24nKSxcbiAgICAgICAgc2VsZWN0U29ydDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NvcnREcm9wRG93bicpLFxuICAgICAgICBidXR0b25EZWxldGVTaXRlOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZGVsZXRlU2l0ZUJ1dHRvbicpLFxuXHRcdGJ1dHRvbnNEZWxldGVTaXRlOiBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCcuZGVsZXRlU2l0ZUJ1dHRvbicpLFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB0aGlzLmNyZWF0ZVRodW1ibmFpbHMoKTtcblxuICAgICAgICAgICAgJCh0aGlzLnNlbGVjdFVzZXIpLm9uKCdjaGFuZ2UnLCB0aGlzLmZpbHRlclVzZXIpO1xuICAgICAgICAgICAgJCh0aGlzLnNlbGVjdFNvcnQpLm9uKCdjaGFuZ2UnLCB0aGlzLmNoYW5nZVNvcnRpbmcpO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvbnNEZWxldGVTaXRlKS5vbignY2xpY2snLCB0aGlzLmRlbGV0ZVNpdGUpO1xuXHRcdFx0JCh0aGlzLmJ1dHRvbkRlbGV0ZVNpdGUpLm9uKCdjbGljaycsIHRoaXMuZGVsZXRlU2l0ZSk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBhcHBsaWVzIHpvb21lciB0byBjcmVhdGUgdGhlIGlmcmFtZSB0aHVibW5haWxzXG4gICAgICAgICovXG4gICAgICAgIGNyZWF0ZVRodW1ibmFpbHM6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKHRoaXMud3JhcHBlclNpdGVzKS5maW5kKCdpZnJhbWUnKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICB2YXIgdGhlSGVpZ2h0ID0gJCh0aGlzKS5hdHRyKCdkYXRhLWhlaWdodCcpKjAuMjU7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMpLnpvb21lcih7XG4gICAgICAgICAgICAgICAgICAgIHpvb206IDAuMjUsXG4gICAgICAgICAgICAgICAgICAgIGhlaWdodDogdGhlSGVpZ2h0LFxuICAgICAgICAgICAgICAgICAgICB3aWR0aDogJCh0aGlzKS5wYXJlbnQoKS53aWR0aCgpLFxuICAgICAgICAgICAgICAgICAgICBtZXNzYWdlOiBcIlwiLFxuICAgICAgICAgICAgICAgICAgICBtZXNzYWdlVVJMOiBhcHBVSS5zaXRlVXJsK1wic2l0ZS9cIiskKHRoaXMpLmF0dHIoJ2RhdGEtc2l0ZWlkJylcbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICQodGhpcykuY2xvc2VzdCgnLnNpdGUnKS5maW5kKCcuem9vbWVyLWNvdmVyID4gYScpLmF0dHIoJ3RhcmdldCcsICcnKTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBmaWx0ZXJzIHRoZSBzaXRlIGxpc3QgYnkgc2VsZWN0ZWQgdXNlclxuICAgICAgICAqL1xuICAgICAgICBmaWx0ZXJVc2VyOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgaWYoICQodGhpcykudmFsKCkgPT09ICdBbGwnIHx8ICQodGhpcykudmFsKCkgPT09ICcnICkge1xuICAgICAgICAgICAgICAgICQoJyNzaXRlcyAuc2l0ZScpLmhpZGUoKS5mYWRlSW4oNTAwKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJCgnI3NpdGVzIC5zaXRlJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICQoJyNzaXRlcycpLmZpbmQoJ1tkYXRhLW5hbWU9XCInKyQodGhpcykudmFsKCkrJ1wiXScpLmZhZGVJbig1MDApO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgY2huYWdlcyB0aGUgc29ydGluZyBvbiB0aGUgc2l0ZSBsaXN0XG4gICAgICAgICovXG4gICAgICAgIGNoYW5nZVNvcnRpbmc6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB2YXIgc2l0ZXM7XG5cbiAgICAgICAgICAgIGlmKCAkKHRoaXMpLnZhbCgpID09PSAnTm9PZlBhZ2VzJyApIHtcblxuXHRcdFx0XHRzaXRlcyA9ICQoJyNzaXRlcyAuc2l0ZScpO1xuXG5cdFx0XHRcdHNpdGVzLnNvcnQoIGZ1bmN0aW9uKGEsYil7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIGFuID0gYS5nZXRBdHRyaWJ1dGUoJ2RhdGEtcGFnZXMnKTtcblx0XHRcdFx0XHR2YXIgYm4gPSBiLmdldEF0dHJpYnV0ZSgnZGF0YS1wYWdlcycpO1xuXG5cdFx0XHRcdFx0aWYoYW4gPiBibikge1xuXHRcdFx0XHRcdFx0cmV0dXJuIDE7XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0aWYoYW4gPCBibikge1xuXHRcdFx0XHRcdFx0cmV0dXJuIC0xO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdHJldHVybiAwO1xuXG5cdFx0XHRcdH0gKTtcblxuXHRcdFx0XHRzaXRlcy5kZXRhY2goKS5hcHBlbmRUbyggJCgnI3NpdGVzJykgKTtcblxuXHRcdFx0fSBlbHNlIGlmKCAkKHRoaXMpLnZhbCgpID09PSAnQ3JlYXRpb25EYXRlJyApIHtcblxuXHRcdFx0XHRzaXRlcyA9ICQoJyNzaXRlcyAuc2l0ZScpO1xuXG5cdFx0XHRcdHNpdGVzLnNvcnQoIGZ1bmN0aW9uKGEsYil7XG5cblx0XHRcdFx0XHR2YXIgYW4gPSBhLmdldEF0dHJpYnV0ZSgnZGF0YS1jcmVhdGVkJykucmVwbGFjZShcIi1cIiwgXCJcIik7XG5cdFx0XHRcdFx0dmFyIGJuID0gYi5nZXRBdHRyaWJ1dGUoJ2RhdGEtY3JlYXRlZCcpLnJlcGxhY2UoXCItXCIsIFwiXCIpO1xuXG5cdFx0XHRcdFx0aWYoYW4gPiBibikge1xuXHRcdFx0XHRcdFx0cmV0dXJuIDE7XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0aWYoYW4gPCBibikge1xuXHRcdFx0XHRcdFx0cmV0dXJuIC0xO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdHJldHVybiAwO1xuXG5cdFx0XHRcdH0gKTtcblxuXHRcdFx0XHRzaXRlcy5kZXRhY2goKS5hcHBlbmRUbyggJCgnI3NpdGVzJykgKTtcblxuXHRcdFx0fSBlbHNlIGlmKCAkKHRoaXMpLnZhbCgpID09PSAnTGFzdFVwZGF0ZScgKSB7XG5cblx0XHRcdFx0c2l0ZXMgPSAkKCcjc2l0ZXMgLnNpdGUnKTtcblxuXHRcdFx0XHRzaXRlcy5zb3J0KCBmdW5jdGlvbihhLGIpe1xuXG5cdFx0XHRcdFx0dmFyIGFuID0gYS5nZXRBdHRyaWJ1dGUoJ2RhdGEtdXBkYXRlJykucmVwbGFjZShcIi1cIiwgXCJcIik7XG5cdFx0XHRcdFx0dmFyIGJuID0gYi5nZXRBdHRyaWJ1dGUoJ2RhdGEtdXBkYXRlJykucmVwbGFjZShcIi1cIiwgXCJcIik7XG5cblx0XHRcdFx0XHRpZihhbiA+IGJuKSB7XG5cdFx0XHRcdFx0XHRyZXR1cm4gMTtcblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHRpZihhbiA8IGJuKSB7XG5cdFx0XHRcdFx0XHRyZXR1cm4gLTE7XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdHJldHVybiAwO1xuXG5cdFx0XHRcdH0gKTtcblxuXHRcdFx0XHRzaXRlcy5kZXRhY2goKS5hcHBlbmRUbyggJCgnI3NpdGVzJykgKTtcblxuXHRcdFx0fVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgZGVsZXRlcyBhIHNpdGVcbiAgICAgICAgKi9cbiAgICAgICAgZGVsZXRlU2l0ZTogZnVuY3Rpb24oZSkge1xuXG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgICAgICQoJyNkZWxldGVTaXRlTW9kYWwgLm1vZGFsLWNvbnRlbnQgcCcpLnNob3coKTtcblxuICAgICAgICAgICAgLy9yZW1vdmUgb2xkIGFsZXJ0c1xuICAgICAgICAgICAgJCgnI2RlbGV0ZVNpdGVNb2RhbCAubW9kYWwtYWxlcnRzID4gKicpLnJlbW92ZSgpO1xuICAgICAgICAgICAgJCgnI2RlbGV0ZVNpdGVNb2RhbCAubG9hZGVyJykuaGlkZSgpO1xuXG4gICAgICAgICAgICB2YXIgdG9EZWwgPSAkKHRoaXMpLmNsb3Nlc3QoJy5zaXRlJyk7XG4gICAgICAgICAgICB2YXIgZGVsQnV0dG9uID0gJCh0aGlzKTtcblxuICAgICAgICAgICAgJCgnI2RlbGV0ZVNpdGVNb2RhbCBidXR0b24jZGVsZXRlU2l0ZUJ1dHRvbicpLnNob3coKTtcbiAgICAgICAgICAgICQoJyNkZWxldGVTaXRlTW9kYWwnKS5tb2RhbCgnc2hvdycpO1xuXG4gICAgICAgICAgICAkKCcjZGVsZXRlU2l0ZU1vZGFsIGJ1dHRvbiNkZWxldGVTaXRlQnV0dG9uJykudW5iaW5kKCdjbGljaycpLmNsaWNrKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuICAgICAgICAgICAgICAgICQoJyNkZWxldGVTaXRlTW9kYWwgLmxvYWRlcicpLmZhZGVJbig1MDApO1xuXG4gICAgICAgICAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgICAgICAgICAgdXJsOiBhcHBVSS5zaXRlVXJsK1wic2l0ZS90cmFzaC9cIitkZWxCdXR0b24uYXR0cignZGF0YS1zaXRlaWQnKSxcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogJ2dldCcsXG4gICAgICAgICAgICAgICAgICAgIGRhdGFUeXBlOiAnanNvbidcbiAgICAgICAgICAgICAgICB9KS5kb25lKGZ1bmN0aW9uKHJldCl7XG5cbiAgICAgICAgICAgICAgICAgICAgJCgnI2RlbGV0ZVNpdGVNb2RhbCAubG9hZGVyJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAkKCcjZGVsZXRlU2l0ZU1vZGFsIGJ1dHRvbiNkZWxldGVTaXRlQnV0dG9uJykucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDAgKSB7Ly9lcnJvclxuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjZGVsZXRlU2l0ZU1vZGFsIC5tb2RhbC1jb250ZW50IHAnKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjZGVsZXRlU2l0ZU1vZGFsIC5tb2RhbC1hbGVydHMnKS5hcHBlbmQoICQocmV0LnJlc3BvbnNlSFRNTCkgKTtcblxuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDEgKSB7Ly9hbGwgZ29vZFxuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjZGVsZXRlU2l0ZU1vZGFsIC5tb2RhbC1jb250ZW50IHAnKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjZGVsZXRlU2l0ZU1vZGFsIC5tb2RhbC1hbGVydHMnKS5hcHBlbmQoICQocmV0LnJlc3BvbnNlSFRNTCkgKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyNkZWxldGVTaXRlTW9kYWwgYnV0dG9uI2RlbGV0ZVNpdGVCdXR0b24nKS5oaWRlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRvRGVsLmZhZGVPdXQoODAwLCBmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9XG5cbiAgICB9O1xuXG4gICAgc2l0ZXMuaW5pdCgpO1xuXG59KCkpOyIsIihmdW5jdGlvbiAoKSB7XG5cdFwidXNlIHN0cmljdFwiO1xuXG5cdHZhciBhcHBVSSA9IHJlcXVpcmUoJy4vdWkuanMnKS5hcHBVSTtcblxuXHR2YXIgc2l0ZVNldHRpbmdzID0ge1xuXG4gICAgICAgIC8vYnV0dG9uU2l0ZVNldHRpbmdzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2l0ZVNldHRpbmdzQnV0dG9uJyksXG5cdFx0YnV0dG9uU2l0ZVNldHRpbmdzMjogJCgnLnNpdGVTZXR0aW5nc01vZGFsQnV0dG9uJyksXG4gICAgICAgIGJ1dHRvblNhdmVTaXRlU2V0dGluZ3M6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdzYXZlU2l0ZVNldHRpbmdzQnV0dG9uJyksXG5cbiAgICAgICAgaW5pdDogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIC8vJCh0aGlzLmJ1dHRvblNpdGVTZXR0aW5ncykub24oJ2NsaWNrJywgdGhpcy5zaXRlU2V0dGluZ3NNb2RhbCk7XG5cdFx0XHR0aGlzLmJ1dHRvblNpdGVTZXR0aW5nczIub24oJ2NsaWNrJywgdGhpcy5zaXRlU2V0dGluZ3NNb2RhbCk7XG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uU2F2ZVNpdGVTZXR0aW5ncykub24oJ2NsaWNrJywgdGhpcy5zYXZlU2l0ZVNldHRpbmdzKTtcblxuICAgICAgICB9LFxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBsb2FkcyB0aGUgc2l0ZSBzZXR0aW5ncyBkYXRhXG4gICAgICAgICovXG4gICAgICAgIHNpdGVTZXR0aW5nc01vZGFsOiBmdW5jdGlvbihlKSB7XG5cbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgIFx0XHQkKCcjc2l0ZVNldHRpbmdzJykubW9kYWwoJ3Nob3cnKTtcblxuICAgIFx0XHQvL2Rlc3Ryb3kgYWxsIGFsZXJ0c1xuICAgIFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5hbGVydCcpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbigpe1xuXG4gICAgXHRcdFx0JCh0aGlzKS5yZW1vdmUoKTtcblxuICAgIFx0XHR9KTtcblxuICAgIFx0XHQvL3NldCB0aGUgc2l0ZUlEXG4gICAgXHRcdCQoJ2lucHV0I3NpdGVJRCcpLnZhbCggJCh0aGlzKS5hdHRyKCdkYXRhLXNpdGVpZCcpICk7XG5cbiAgICBcdFx0Ly9kZXN0cm95IGN1cnJlbnQgZm9ybXNcbiAgICBcdFx0JCgnI3NpdGVTZXR0aW5ncyAubW9kYWwtYm9keS1jb250ZW50ID4gKicpLmVhY2goZnVuY3Rpb24oKXtcbiAgICBcdFx0XHQkKHRoaXMpLnJlbW92ZSgpO1xuICAgIFx0XHR9KTtcblxuICAgICAgICAgICAgLy9zaG93IGxvYWRlciwgaGlkZSByZXN0XG4gICAgXHRcdCQoJyNzaXRlU2V0dGluZ3NXcmFwcGVyIC5sb2FkZXInKS5zaG93KCk7XG4gICAgXHRcdCQoJyNzaXRlU2V0dGluZ3NXcmFwcGVyID4gKjpub3QoLmxvYWRlciknKS5oaWRlKCk7XG5cbiAgICBcdFx0Ly9sb2FkIHNpdGUgZGF0YSB1c2luZyBhamF4XG4gICAgXHRcdCQuYWpheCh7XG4gICAgICAgICAgICAgICAgdXJsOiBhcHBVSS5zaXRlVXJsK1wic2l0ZUFqYXgvXCIrJCh0aGlzKS5hdHRyKCdkYXRhLXNpdGVpZCcpLFxuICAgIFx0XHRcdHR5cGU6ICdnZXQnLFxuICAgIFx0XHRcdGRhdGFUeXBlOiAnanNvbidcbiAgICBcdFx0fSkuZG9uZShmdW5jdGlvbihyZXQpe1xuXG4gICAgXHRcdFx0aWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDAgKSB7Ly9lcnJvclxuXG4gICAgXHRcdFx0XHQvL2hpZGUgbG9hZGVyLCBzaG93IGVycm9yIG1lc3NhZ2VcbiAgICBcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3MgLmxvYWRlcicpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbigpe1xuXG4gICAgXHRcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3MgLm1vZGFsLWFsZXJ0cycpLmFwcGVuZCggJChyZXQucmVzcG9uc2VIVE1MKSApO1xuXG4gICAgXHRcdFx0XHR9KTtcblxuICAgIFx0XHRcdFx0Ly9kaXNhYmxlIHN1Ym1pdCBidXR0b25cbiAgICBcdFx0XHRcdCQoJyNzYXZlU2l0ZVNldHRpbmdzQnV0dG9uJykuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cblxuICAgIFx0XHRcdH0gZWxzZSBpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMSApIHsvL2FsbCB3ZWxsIDopXG5cbiAgICBcdFx0XHRcdC8vaGlkZSBsb2FkZXIsIHNob3cgZGF0YVxuXG4gICAgXHRcdFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5sb2FkZXInKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcblxuICAgIFx0XHRcdFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5tb2RhbC1ib2R5LWNvbnRlbnQnKS5hcHBlbmQoICQocmV0LnJlc3BvbnNlSFRNTCkgKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnYm9keScpLnRyaWdnZXIoJ3NpdGVTZXR0aW5nc0xvYWQnKTtcblxuICAgIFx0XHRcdFx0fSk7XG5cbiAgICBcdFx0XHRcdC8vZW5hYmxlIHN1Ym1pdCBidXR0b25cbiAgICBcdFx0XHRcdCQoJyNzYXZlU2l0ZVNldHRpbmdzQnV0dG9uJykucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICBcdFx0XHR9XG5cbiAgICBcdFx0fSk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBzYXZlcyB0aGUgc2l0ZSBzZXR0aW5nc1xuICAgICAgICAqL1xuICAgICAgICBzYXZlU2l0ZVNldHRpbmdzOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9kZXN0cm95IGFsbCBhbGVydHNcbiAgICBcdFx0JCgnI3NpdGVTZXR0aW5ncyAuYWxlcnQnKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcblxuICAgIFx0XHRcdCQodGhpcykucmVtb3ZlKCk7XG5cbiAgICBcdFx0fSk7XG5cbiAgICBcdFx0Ly9kaXNhYmxlIGJ1dHRvblxuICAgIFx0XHQkKCcjc2F2ZVNpdGVTZXR0aW5nc0J1dHRvbicpLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgXHRcdC8vaGlkZSBmb3JtIGRhdGFcbiAgICBcdFx0JCgnI3NpdGVTZXR0aW5ncyAubW9kYWwtYm9keS1jb250ZW50ID4gKicpLmhpZGUoKTtcblxuICAgIFx0XHQvL3Nob3cgbG9hZGVyXG4gICAgXHRcdCQoJyNzaXRlU2V0dGluZ3MgLmxvYWRlcicpLnNob3coKTtcblxuICAgIFx0XHQkLmFqYXgoe1xuICAgICAgICAgICAgICAgIHVybDogYXBwVUkuc2l0ZVVybCtcInNpdGVBamF4VXBkYXRlXCIsXG4gICAgXHRcdFx0dHlwZTogJ3Bvc3QnLFxuICAgIFx0XHRcdGRhdGFUeXBlOiAnanNvbicsXG4gICAgXHRcdFx0ZGF0YTogJCgnZm9ybSNzaXRlU2V0dGluZ3NGb3JtJykuc2VyaWFsaXplQXJyYXkoKVxuICAgIFx0XHR9KS5kb25lKGZ1bmN0aW9uKHJldCl7XG5cbiAgICBcdFx0XHRpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMCApIHsvL2Vycm9yXG5cbiAgICBcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3MgLmxvYWRlcicpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbigpe1xuXG4gICAgXHRcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3MgLm1vZGFsLWFsZXJ0cycpLmFwcGVuZCggcmV0LnJlc3BvbnNlSFRNTCApO1xuXG4gICAgXHRcdFx0XHRcdC8vc2hvdyBmb3JtIGRhdGFcbiAgICBcdFx0XHRcdFx0JCgnI3NpdGVTZXR0aW5ncyAubW9kYWwtYm9keS1jb250ZW50ID4gKicpLnNob3coKTtcblxuICAgIFx0XHRcdFx0XHQvL2VuYWJsZSBidXR0b25cbiAgICBcdFx0XHRcdFx0JCgnI3NhdmVTaXRlU2V0dGluZ3NCdXR0b24nKS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgIFx0XHRcdFx0fSk7XG5cblxuICAgIFx0XHRcdH0gZWxzZSBpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMSApIHsvL2FsbCBpcyB3ZWxsXG5cbiAgICBcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3MgLmxvYWRlcicpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbigpe1xuXG5cbiAgICBcdFx0XHRcdFx0Ly91cGRhdGUgc2l0ZSBuYW1lIGluIHRvcCBtZW51XG4gICAgXHRcdFx0XHRcdCQoJyNzaXRlVGl0bGUnKS50ZXh0KCByZXQuc2l0ZU5hbWUgKTtcblxuICAgIFx0XHRcdFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5tb2RhbC1hbGVydHMnKS5hcHBlbmQoIHJldC5yZXNwb25zZUhUTUwgKTtcblxuICAgIFx0XHRcdFx0XHQvL2hpZGUgZm9ybSBkYXRhXG4gICAgXHRcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3MgLm1vZGFsLWJvZHktY29udGVudCA+IConKS5yZW1vdmUoKTtcbiAgICBcdFx0XHRcdFx0JCgnI3NpdGVTZXR0aW5ncyAubW9kYWwtYm9keS1jb250ZW50JykuYXBwZW5kKCByZXQucmVzcG9uc2VIVE1MMiApO1xuXG4gICAgXHRcdFx0XHRcdC8vZW5hYmxlIGJ1dHRvblxuICAgIFx0XHRcdFx0XHQkKCcjc2F2ZVNpdGVTZXR0aW5nc0J1dHRvbicpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgXHRcdFx0XHRcdC8vaXMgdGhlIEZUUCBzdHVmZiBhbGwgZ29vZD9cblxuICAgIFx0XHRcdFx0XHRpZiggcmV0LmZ0cE9rID09PSAxICkgey8veWVzLCBhbGwgZ29vZFxuXG4gICAgXHRcdFx0XHRcdFx0JCgnI3B1Ymxpc2hQYWdlJykucmVtb3ZlQXR0cignZGF0YS10b2dnbGUnKTtcbiAgICBcdFx0XHRcdFx0XHQkKCcjcHVibGlzaFBhZ2Ugc3Bhbi50ZXh0LWRhbmdlcicpLmhpZGUoKTtcblxuICAgIFx0XHRcdFx0XHRcdCQoJyNwdWJsaXNoUGFnZScpLnRvb2x0aXAoJ2Rlc3Ryb3knKTtcblxuICAgIFx0XHRcdFx0XHR9IGVsc2Ugey8vbm9wZSwgY2FuJ3QgdXNlIEZUUFxuXG4gICAgXHRcdFx0XHRcdFx0JCgnI3B1Ymxpc2hQYWdlJykuYXR0cignZGF0YS10b2dnbGUnLCAndG9vbHRpcCcpO1xuICAgIFx0XHRcdFx0XHRcdCQoJyNwdWJsaXNoUGFnZSBzcGFuLnRleHQtZGFuZ2VyJykuc2hvdygpO1xuXG4gICAgXHRcdFx0XHRcdFx0JCgnI3B1Ymxpc2hQYWdlJykudG9vbHRpcCgnc2hvdycpO1xuXG4gICAgXHRcdFx0XHRcdH1cblxuXG4gICAgXHRcdFx0XHRcdC8vdXBkYXRlIHRoZSBzaXRlIG5hbWUgaW4gdGhlIHNtYWxsIHdpbmRvd1xuICAgIFx0XHRcdFx0XHQkKCcjc2l0ZV8nK3JldC5zaXRlSUQrJyAud2luZG93IC50b3AgYicpLnRleHQoIHJldC5zaXRlTmFtZSApO1xuXG4gICAgXHRcdFx0XHR9KTtcblxuXG4gICAgXHRcdFx0fVxuXG4gICAgXHRcdH0pO1xuXG4gICAgICAgIH0sXG5cblxuICAgIH07XG5cbiAgICBzaXRlU2V0dGluZ3MuaW5pdCgpO1xuXG59KCkpOyIsIihmdW5jdGlvbiAoKSB7XG5cbi8qIGdsb2JhbHMgc2l0ZVVybDpmYWxzZSwgYmFzZVVybDpmYWxzZSAqL1xuICAgIFwidXNlIHN0cmljdFwiO1xuICAgICAgICBcbiAgICB2YXIgYXBwVUkgPSB7XG4gICAgICAgIFxuICAgICAgICBmaXJzdE1lbnVXaWR0aDogMTkwLFxuICAgICAgICBzZWNvbmRNZW51V2lkdGg6IDMwMCxcbiAgICAgICAgbG9hZGVyQW5pbWF0aW9uOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbG9hZGVyJyksXG4gICAgICAgIHNlY29uZE1lbnVUcmlnZ2VyQ29udGFpbmVyczogJCgnI21lbnUgI21haW4gI2VsZW1lbnRDYXRzLCAjbWVudSAjbWFpbiAjdGVtcGxhdGVzVWwnKSxcbiAgICAgICAgc2l0ZVVybDogc2l0ZVVybCxcbiAgICAgICAgYmFzZVVybDogYmFzZVVybCxcbiAgICAgICAgXG4gICAgICAgIHNldHVwOiBmdW5jdGlvbigpe1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBGYWRlIHRoZSBsb2FkZXIgYW5pbWF0aW9uXG4gICAgICAgICAgICAkKGFwcFVJLmxvYWRlckFuaW1hdGlvbikuZmFkZU91dChmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICQoJyNtZW51JykuYW5pbWF0ZSh7J2xlZnQnOiAtYXBwVUkuZmlyc3RNZW51V2lkdGh9LCAxMDAwKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBUYWJzXG4gICAgICAgICAgICAkKFwiLm5hdi10YWJzIGFcIikub24oJ2NsaWNrJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS50YWIoXCJzaG93XCIpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgICQoXCJzZWxlY3Quc2VsZWN0XCIpLnNlbGVjdDIoKTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgJCgnOnJhZGlvLCA6Y2hlY2tib3gnKS5yYWRpb2NoZWNrKCk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgIC8vIFRvb2x0aXBzXG4gICAgICAgICAgICAkKFwiW2RhdGEtdG9nZ2xlPXRvb2x0aXBdXCIpLnRvb2x0aXAoXCJoaWRlXCIpO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBUYWJsZTogVG9nZ2xlIGFsbCBjaGVja2JveGVzXG4gICAgICAgICAgICAkKCcudGFibGUgLnRvZ2dsZS1hbGwgOmNoZWNrYm94Jykub24oJ2NsaWNrJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyk7XG4gICAgICAgICAgICAgICAgdmFyIGNoID0gJHRoaXMucHJvcCgnY2hlY2tlZCcpO1xuICAgICAgICAgICAgICAgICR0aGlzLmNsb3Nlc3QoJy50YWJsZScpLmZpbmQoJ3Rib2R5IDpjaGVja2JveCcpLnJhZGlvY2hlY2soIWNoID8gJ3VuY2hlY2snIDogJ2NoZWNrJyk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgLy8gQWRkIHN0eWxlIGNsYXNzIG5hbWUgdG8gYSB0b29sdGlwc1xuICAgICAgICAgICAgJChcIi50b29sdGlwXCIpLmFkZENsYXNzKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGlmICgkKHRoaXMpLnByZXYoKS5hdHRyKFwiZGF0YS10b29sdGlwLXN0eWxlXCIpKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBcInRvb2x0aXAtXCIgKyAkKHRoaXMpLnByZXYoKS5hdHRyKFwiZGF0YS10b29sdGlwLXN0eWxlXCIpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAkKFwiLmJ0bi1ncm91cFwiKS5vbignY2xpY2snLCBcImFcIiwgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5zaWJsaW5ncygpLnJlbW92ZUNsYXNzKFwiYWN0aXZlXCIpLmVuZCgpLmFkZENsYXNzKFwiYWN0aXZlXCIpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgIC8vIEZvY3VzIHN0YXRlIGZvciBhcHBlbmQvcHJlcGVuZCBpbnB1dHNcbiAgICAgICAgICAgICQoJy5pbnB1dC1ncm91cCcpLm9uKCdmb2N1cycsICcuZm9ybS1jb250cm9sJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICQodGhpcykuY2xvc2VzdCgnLmlucHV0LWdyb3VwLCAuZm9ybS1ncm91cCcpLmFkZENsYXNzKCdmb2N1cycpO1xuICAgICAgICAgICAgfSkub24oJ2JsdXInLCAnLmZvcm0tY29udHJvbCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJy5pbnB1dC1ncm91cCwgLmZvcm0tZ3JvdXAnKS5yZW1vdmVDbGFzcygnZm9jdXMnKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBUYWJsZTogVG9nZ2xlIGFsbCBjaGVja2JveGVzXG4gICAgICAgICAgICAkKCcudGFibGUgLnRvZ2dsZS1hbGwnKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICB2YXIgY2ggPSAkKHRoaXMpLmZpbmQoJzpjaGVja2JveCcpLnByb3AoJ2NoZWNrZWQnKTtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJy50YWJsZScpLmZpbmQoJ3Rib2R5IDpjaGVja2JveCcpLmNoZWNrYm94KCFjaCA/ICdjaGVjaycgOiAndW5jaGVjaycpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgIC8vIFRhYmxlOiBBZGQgY2xhc3Mgcm93IHNlbGVjdGVkXG4gICAgICAgICAgICAkKCcudGFibGUgdGJvZHkgOmNoZWNrYm94Jykub24oJ2NoZWNrIHVuY2hlY2sgdG9nZ2xlJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpXG4gICAgICAgICAgICAgICAgLCBjaGVjayA9ICR0aGlzLnByb3AoJ2NoZWNrZWQnKVxuICAgICAgICAgICAgICAgICwgdG9nZ2xlID0gZS50eXBlID09PSAndG9nZ2xlJ1xuICAgICAgICAgICAgICAgICwgY2hlY2tib3hlcyA9ICQoJy50YWJsZSB0Ym9keSA6Y2hlY2tib3gnKVxuICAgICAgICAgICAgICAgICwgY2hlY2tBbGwgPSBjaGVja2JveGVzLmxlbmd0aCA9PT0gY2hlY2tib3hlcy5maWx0ZXIoJzpjaGVja2VkJykubGVuZ3RoO1xuXG4gICAgICAgICAgICAgICAgJHRoaXMuY2xvc2VzdCgndHInKVtjaGVjayA/ICdhZGRDbGFzcycgOiAncmVtb3ZlQ2xhc3MnXSgnc2VsZWN0ZWQtcm93Jyk7XG4gICAgICAgICAgICAgICAgaWYgKHRvZ2dsZSkgJHRoaXMuY2xvc2VzdCgnLnRhYmxlJykuZmluZCgnLnRvZ2dsZS1hbGwgOmNoZWNrYm94JykuY2hlY2tib3goY2hlY2tBbGwgPyAnY2hlY2snIDogJ3VuY2hlY2snKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBTd2l0Y2hcbiAgICAgICAgICAgICQoXCJbZGF0YS10b2dnbGU9J3N3aXRjaCddXCIpLndyYXAoJzxkaXYgY2xhc3M9XCJzd2l0Y2hcIiAvPicpLnBhcmVudCgpLmJvb3RzdHJhcFN3aXRjaCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgXG4gICAgICAgICAgICBhcHBVSS5zZWNvbmRNZW51VHJpZ2dlckNvbnRhaW5lcnMub24oJ2NsaWNrJywgJ2E6bm90KC5idG4pJywgYXBwVUkuc2Vjb25kTWVudUFuaW1hdGlvbik7XG4gICAgICAgICAgICAgICAgICAgICAgICBcbiAgICAgICAgfSxcbiAgICAgICAgXG4gICAgICAgIHNlY29uZE1lbnVBbmltYXRpb246IGZ1bmN0aW9uKCl7XG4gICAgICAgIFxuICAgICAgICAgICAgJCgnI21lbnUgI21haW4gYScpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcbiAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xuXHRcbiAgICAgICAgICAgIC8vc2hvdyBvbmx5IHRoZSByaWdodCBlbGVtZW50c1xuICAgICAgICAgICAgJCgnI21lbnUgI3NlY29uZCB1bCBsaScpLmhpZGUoKTtcbiAgICAgICAgICAgICQoJyNtZW51ICNzZWNvbmQgdWwgbGkuJyskKHRoaXMpLmF0dHIoJ2lkJykpLnNob3coKTtcblxuICAgICAgICAgICAgaWYoICQodGhpcykuYXR0cignaWQnKSA9PT0gJ2FsbCcgKSB7XG4gICAgICAgICAgICAgICAgJCgnI21lbnUgI3NlY29uZCB1bCNlbGVtZW50cyBsaScpLnNob3coKTtcdFx0XG4gICAgICAgICAgICB9XG5cdFxuICAgICAgICAgICAgJCgnLm1lbnUgLnNlY29uZCcpLmNzcygnZGlzcGxheScsICdibG9jaycpLnN0b3AoKS5hbmltYXRlKHtcbiAgICAgICAgICAgICAgICB3aWR0aDogYXBwVUkuc2Vjb25kTWVudVdpZHRoXG4gICAgICAgICAgICB9LCA1MDApO1x0XG4gICAgICAgICAgICAgICAgXG4gICAgICAgIH1cbiAgICAgICAgXG4gICAgfTtcbiAgICBcbiAgICAvL2luaXRpYXRlIHRoZSBVSVxuICAgIGFwcFVJLnNldHVwKCk7XG5cblxuICAgIC8vKioqKiBFWFBPUlRTXG4gICAgbW9kdWxlLmV4cG9ydHMuYXBwVUkgPSBhcHBVSTtcbiAgICBcbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cblx0dmFyIGFwcFVJID0gcmVxdWlyZSgnLi91aS5qcycpLmFwcFVJO1xuXG5cdHZhciB1c2VycyA9IHtcblxuICAgICAgICBidXR0b25DcmVhdGVBY2NvdW50OiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnYnV0dG9uQ3JlYXRlQWNjb3VudCcpLFxuICAgICAgICB3cmFwcGVyVXNlcnM6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCd1c2VycycpLFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uQ3JlYXRlQWNjb3VudCkub24oJ2NsaWNrJywgdGhpcy5jcmVhdGVBY2NvdW50KTtcbiAgICAgICAgICAgICQodGhpcy53cmFwcGVyVXNlcnMpLm9uKCdjbGljaycsICcudXBkYXRlVXNlckJ1dHRvbicsIHRoaXMudXBkYXRlVXNlcik7XG4gICAgICAgICAgICAkKHRoaXMud3JhcHBlclVzZXJzKS5vbignY2xpY2snLCAnLnBhc3N3b3JkUmVzZXQnLCB0aGlzLnBhc3N3b3JkUmVzZXQpO1xuICAgICAgICAgICAgJCh0aGlzLndyYXBwZXJVc2Vycykub24oJ2NsaWNrJywgJy5kZWxldGVVc2VyQnV0dG9uJywgdGhpcy5kZWxldGVVc2VyKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGNyZWF0ZXMgYSBuZXcgdXNlciBhY2NvdW50XG4gICAgICAgICovXG4gICAgICAgIGNyZWF0ZUFjY291bnQ6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2FsbCBpdGVtcyBwcmVzZW50P1xuXG4gICAgICAgICAgICB2YXIgYWxsR29vZCA9IDE7XG5cbiAgICAgICAgICAgIGlmKCAkKCcjbmV3VXNlck1vZGFsIGZvcm0gaW5wdXQjZmlyc3RuYW1lJykudmFsKCkgPT09ICcnICkge1xuICAgICAgICAgICAgICAgICQoJyNuZXdVc2VyTW9kYWwgZm9ybSBpbnB1dCNmaXJzdG5hbWUnKS5wYXJlbnQoKS5hZGRDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICAgICAgYWxsR29vZCA9IDA7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQoJyNuZXdVc2VyTW9kYWwgZm9ybSBpbnB1dCNmaXJzdG5hbWUnKS5wYXJlbnQoKS5yZW1vdmVDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKCAkKCcjbmV3VXNlck1vZGFsIGZvcm0gaW5wdXQjbGFzdG5hbWUnKS52YWwoKSA9PT0gJycgKSB7XG4gICAgICAgICAgICAgICAgJCgnI25ld1VzZXJNb2RhbCBmb3JtIGlucHV0I2xhc3RuYW1lJykucGFyZW50KCkuYWRkQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgIGFsbEdvb2QgPSAwO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkKCcjbmV3VXNlck1vZGFsIGZvcm0gaW5wdXQjbGFzdG5hbWUnKS5wYXJlbnQoKS5yZW1vdmVDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKCAkKCcjbmV3VXNlck1vZGFsIGZvcm0gaW5wdXQjZW1haWwnKS52YWwoKSA9PT0gJycgKSB7XG4gICAgICAgICAgICAgICAgJCgnI25ld1VzZXJNb2RhbCBmb3JtIGlucHV0I2VtYWlsJykucGFyZW50KCkuYWRkQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgIGFsbEdvb2QgPSAwO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkKCcjbmV3VXNlck1vZGFsIGZvcm0gaW5wdXQjZW1haWwnKS5wYXJlbnQoKS5yZW1vdmVDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKCAkKCcjbmV3VXNlck1vZGFsIGZvcm0gaW5wdXQjcGFzc3dvcmQnKS52YWwoKSA9PT0gJycgKSB7XG4gICAgICAgICAgICAgICAgJCgnI25ld1VzZXJNb2RhbCBmb3JtIGlucHV0I3Bhc3N3b3JkJykucGFyZW50KCkuYWRkQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgIGFsbEdvb2QgPSAwO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkKCcjbmV3VXNlck1vZGFsIGZvcm0gaW5wdXQjcGFzc3dvcmQnKS5wYXJlbnQoKS5yZW1vdmVDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKCBhbGxHb29kID09PSAxICkge1xuXG4gICAgICAgICAgICAgICAgLy9yZW1vdmUgb2xkIGFsZXJ0c1xuICAgICAgICAgICAgICAgICQoJyNuZXdVc2VyTW9kYWwgLm1vZGFsLWFsZXJ0cyA+IConKS5oaWRlKCk7XG5cbiAgICAgICAgICAgICAgICAvL2Rpc2FibGUgYnV0dG9uXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgICAgIC8vc2hvdyBsb2FkZXJcbiAgICAgICAgICAgICAgICAkKCcjbmV3VXNlck1vZGFsIC5sb2FkZXInKS5mYWRlSW4oKTtcblxuICAgICAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgICAgIHVybDogJCgnI25ld1VzZXJNb2RhbCBmb3JtJykuYXR0cignYWN0aW9uJyksXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6ICdwb3N0JyxcbiAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgICAgICAgICAgICAgICAgZGF0YTogICQoJyNuZXdVc2VyTW9kYWwgZm9ybScpLnNlcmlhbGl6ZSgpXG4gICAgICAgICAgICAgICAgfSkuZG9uZShmdW5jdGlvbihyZXQpe1xuXG4gICAgICAgICAgICAgICAgICAgIC8vZW5hYmxlIGJ1dHRvblxuICAgICAgICAgICAgICAgICAgICAkKCdidXR0b24jYnV0dG9uQ3JlYXRlQWNjb3VudCcpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vaGlkZSBsb2FkZXJcbiAgICAgICAgICAgICAgICAgICAgJCgnI25ld1VzZXJNb2RhbCAubG9hZGVyJykuaGlkZSgpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCByZXQucmVzcG9uc2VDb2RlID09PSAwICkgey8vZXJyb3JcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI25ld1VzZXJNb2RhbCAubW9kYWwtYWxlcnRzJykuYXBwZW5kKCAkKHJldC5yZXNwb25zZUhUTUwpICk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHsvL2FsbCBnb29kXG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyNuZXdVc2VyTW9kYWwgLm1vZGFsLWFsZXJ0cycpLmFwcGVuZCggJChyZXQucmVzcG9uc2VIVE1MKSApO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI3VzZXJzID4gKicpLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI3VzZXJzJykuYXBwZW5kKCAkKHJldC51c2VycykgKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyN1c2VycyBmb3JtIGlucHV0W3R5cGU9XCJjaGVja2JveFwiXScpLmNoZWNrYm94KCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICgnLnVzZXJTaXRlcyAuc2l0ZSBpZnJhbWUnKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgdGhlSGVpZ2h0ID0gJCh0aGlzKS5hdHRyKCdkYXRhLWhlaWdodCcpKjAuMjU7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLndpZHRoKCAgKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuem9vbWVyKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgem9vbTogMC4yNSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaGVpZ2h0OiB0aGVIZWlnaHQsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHdpZHRoOiAkKHRoaXMpLmNsb3Nlc3QoJy50YWItcGFuZScpLndpZHRoKCksXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIG1lc3NhZ2U6IFwiXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIG1lc3NhZ2VVUkw6IGFwcFVJLnNpdGVVcmwrXCJzaXRlL1wiKyQodGhpcykuYXR0cignZGF0YS1zaXRlaWQnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5jbG9zZXN0KCcuc2l0ZScpLmZpbmQoJy56b29tZXItY292ZXIgPiBhJykuYXR0cigndGFyZ2V0JywgJycpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgdXBkYXRlcyBhIHVzZXJcbiAgICAgICAgKi9cbiAgICAgICAgdXBkYXRlVXNlcjogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIC8vZGlzYWJsZSBidXR0b25cbiAgICAgICAgICAgIHZhciB0aGVCdXR0b24gPSAkKHRoaXMpO1xuICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgLy9zaG93IGxvYWRlclxuICAgICAgICAgICAgJCh0aGlzKS5jbG9zZXN0KCcuYm90dG9tJykuZmluZCgnLmxvYWRlcicpLmZhZGVJbig1MDApO1xuXG4gICAgICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgICAgIHVybDogJCh0aGlzKS5jbG9zZXN0KCdmb3JtJykuYXR0cignYWN0aW9uJyksXG4gICAgICAgICAgICAgICAgdHlwZTogJ3Bvc3QnLFxuICAgICAgICAgICAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICAgICAgICAgICAgZGF0YTogJCh0aGlzKS5jbG9zZXN0KCdmb3JtJykuc2VyaWFsaXplKClcbiAgICAgICAgICAgIH0pLmRvbmUoZnVuY3Rpb24ocmV0KXtcblxuICAgICAgICAgICAgICAgIC8vZW5hYmxlIGJ1dHRvblxuICAgICAgICAgICAgICAgIHRoZUJ1dHRvbi5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgICAgIC8vaGlkZSBsb2FkZXJcbiAgICAgICAgICAgICAgICB0aGVCdXR0b24uY2xvc2VzdCgnLmJvdHRvbScpLmZpbmQoJy5sb2FkZXInKS5oaWRlKCk7XG5cbiAgICAgICAgICAgICAgICBpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMCApIHsvL2Vycm9yXG5cbiAgICAgICAgICAgICAgICAgICAgdGhlQnV0dG9uLmNsb3Nlc3QoJy5ib3R0b20nKS5maW5kKCcuYWxlcnRzJykuYXBwZW5kKCAkKHJldC5yZXNwb25zZUhUTUwpICk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYocmV0LnJlc3BvbnNlQ29kZSA9PT0gMSkgey8vYWxsIGdvb2RcblxuICAgICAgICAgICAgICAgICAgICB0aGVCdXR0b24uY2xvc2VzdCgnLmJvdHRvbScpLmZpbmQoJy5hbGVydHMnKS5hcHBlbmQoICQocmV0LnJlc3BvbnNlSFRNTCkgKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2FwcGVuZCB1c2VyIGRldGFpbCBmb3JtXG4gICAgICAgICAgICAgICAgICAgIHZhciB0aGVQYW5lID0gdGhlQnV0dG9uLmNsb3Nlc3QoJy50YWItcGFuZScpO1xuXG4gICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoZVBhbmUuY2xvc2VzdCgnLmJvdHRvbScpLmZpbmQoJy5hbGVydC1zdWNjZXNzJykuZmFkZU91dCg1MDAsIGZ1bmN0aW9uKCl7JCh0aGlzLnJlbW92ZSgpKTt9KTtcbiAgICAgICAgICAgICAgICAgICAgfSwgMzAwMCk7XG5cbiAgICAgICAgICAgICAgICAgICAgdGhlQnV0dG9uLmNsb3Nlc3QoJ2Zvcm0nKS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgICAgICB0aGVQYW5lLnByZXBlbmQoICQocmV0LnVzZXJEZXRhaWxGb3JtKSApO1xuICAgICAgICAgICAgICAgICAgICB0aGVQYW5lLmZpbmQoJ2Zvcm0gaW5wdXRbdHlwZT1cImNoZWNrYm94XCJdJykuY2hlY2tib3goKTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBwYXNzd29yZCByZXNldFxuICAgICAgICAqL1xuICAgICAgICBwYXNzd29yZFJlc2V0OiBmdW5jdGlvbihlKSB7XG5cbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICAgICAgdmFyIHRoZUJ1dHRvbiA9ICQodGhpcyk7XG5cbiAgICAgICAgICAgIC8vZGlzYWJsZSBidXR0b25zXG4gICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuICAgICAgICAgICAgJCh0aGlzKS5jbG9zZXN0KCcuYm90dG9tJykuZmluZCgnLnVwZGF0ZVVzZXJCdXR0b24nKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgLy9zaG93IGxvYWRlclxuICAgICAgICAgICAgJCh0aGlzKS5jbG9zZXN0KCcuYm90dG9tJykuZmluZCgnLmxvYWRlcicpLmZhZGVJbigpO1xuXG4gICAgICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgICAgIHVybDogYXBwVUkuc2l0ZVVybCtcInVzZXItcHctcmVzZXQtZW1haWwvXCIrJCh0aGlzKS5hdHRyKCdkYXRhLXVzZXJpZCcpLFxuICAgICAgICAgICAgICAgIHR5cGU6ICdnZXQnLFxuICAgICAgICAgICAgICAgIGRhdGFUeXBlOiAnanNvbidcbiAgICAgICAgICAgIH0pLmRvbmUoZnVuY3Rpb24ocmV0KXtcblxuICAgICAgICAgICAgICAgIC8vZW5hYmxlIGJ1dHRvbnNcbiAgICAgICAgICAgICAgICB0aGVCdXR0b24ucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICAgICAgdGhlQnV0dG9uLmNsb3Nlc3QoJy5ib3R0b20nKS5maW5kKCcudXBkYXRlVXNlckJ1dHRvbicpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICAgICAgLy9oaWRlIGxvYWRlclxuICAgICAgICAgICAgICAgIHRoZUJ1dHRvbi5jbG9zZXN0KCcuYm90dG9tJykuZmluZCgnLmxvYWRlcicpLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAkKHRoZUJ1dHRvbikuY2xvc2VzdCgnLmJvdHRvbScpLmZpbmQoJy5hbGVydHMnKS5hcHBlbmQoICQocmV0LnJlc3BvbnNlSFRNTCkgKTtcblxuICAgICAgICAgICAgICAgIGlmKCByZXQucmVzcG9uc2VDb2RlID09PSAwICkgey8vZXJyb3JcblxuXHRcdFx0XHR9IGVsc2UgaWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDEgKSB7Ly9hbGwgZ29vZFxuXG4gICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoZUJ1dHRvbi5jbG9zZXN0KCcuYm90dG9tJykuZmluZCgnLmFsZXJ0cyA+IConKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXskKHRoaXMpLnJlbW92ZSgpO30pO1xuICAgICAgICAgICAgICAgICAgICB9LCAzMDAwKTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBkZWxldGVzIGEgdXNlciBhY2NvdW50XG4gICAgICAgICovXG4gICAgICAgIGRlbGV0ZVVzZXI6IGZ1bmN0aW9uKGUpIHtcblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICAvL3NldHVwIGRlbGV0ZSBsaW5rXG4gICAgICAgICAgICAkKCcjZGVsZXRlVXNlck1vZGFsIGEjZGVsZXRlVXNlckJ1dHRvbicpLmF0dHIoJ2hyZWYnLCAkKHRoaXMpLmF0dHIoJ2hyZWYnKSk7XG5cbiAgICAgICAgICAgIC8vbW9kYWxcbiAgICAgICAgICAgICQoJyNkZWxldGVVc2VyTW9kYWwnKS5tb2RhbCgnc2hvdycpO1xuXG4gICAgICAgIH1cblxuICAgIH07XG5cbiAgICB1c2Vycy5pbml0KCk7XG5cbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cblx0cmVxdWlyZSgnLi9tb2R1bGVzL3VpJyk7XG5cdHJlcXVpcmUoJy4vbW9kdWxlcy91c2VycycpO1xuXHRyZXF1aXJlKCcuL21vZHVsZXMvYWNjb3VudCcpO1xuXHRyZXF1aXJlKCcuL21vZHVsZXMvc2l0ZXNldHRpbmdzJyk7XG5cdHJlcXVpcmUoJy4vbW9kdWxlcy9zaXRlcycpO1xuXG5cdCQoJy51c2VyU2l0ZXMgLnNpdGUgaWZyYW1lJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgIHZhciB0aGVIZWlnaHQgPSAkKHRoaXMpLmF0dHIoJ2RhdGEtaGVpZ2h0JykqMC4yNTtcblxuICAgICAgICAvL2FsZXJ0KCQodGhpcykuY2xvc2VzdCgnLnRhYi1jb250ZW50JykuaW5uZXJXaWR0aCgpKVxuXG4gICAgICAgICQodGhpcykuem9vbWVyKHtcbiAgICAgICAgICAgIHpvb206IDAuMjAsXG4gICAgICAgICAgICBoZWlnaHQ6IHRoZUhlaWdodCxcbiAgICAgICAgICAgIHdpZHRoOiAkKHRoaXMpLmNsb3Nlc3QoJy50YWItY29udGVudCcpLndpZHRoKCksXG4gICAgICAgICAgICBtZXNzYWdlOiBcIlwiLFxuICAgICAgICAgICAgbWVzc2FnZVVSTDogYXBwVUkuc2l0ZVVybCtcInNpdGUvXCIrJCh0aGlzKS5hdHRyKCdkYXRhLXNpdGVpZCcpXG4gICAgICAgIH0pO1xuXG4gICAgICAgICQodGhpcykuY2xvc2VzdCgnLnNpdGUnKS5maW5kKCcuem9vbWVyLWNvdmVyID4gYScpLmF0dHIoJ3RhcmdldCcsICcnKTtcblxuICAgIH0pXG5cbn0oKSk7Il19
