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
},{"./ui.js":2}],2:[function(require,module,exports){
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
},{}],3:[function(require,module,exports){
(function () {
	"use strict";

	require('./modules/account');

	$('#configHelp').affix({
        offset: {
            top: 200
        }
    });

    //set the width for the configHelp
    $('.configHelp').width( $('.configHelp').width() )

    //help info
    $('form.settingsForm textarea').focus(function(){

        $('#configHelp > div:first').html( $(this).next().html() );

        $('#configHelp').fadeIn(500);

        //set the width for the configHelp
        $('.configHelp').width( $('.configHelp').width() )

    });

    $('form.settingsForm textarea').blur(function(){

        $('#configHelp').hide();

    });

}());
},{"./modules/account":1}]},{},[3])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJwdWJsaWMvc3JjL2pzL21vZHVsZXMvYWNjb3VudC5qcyIsInB1YmxpYy9zcmMvanMvbW9kdWxlcy91aS5qcyIsInB1YmxpYy9zcmMvanMvc2V0dGluZ3MuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUNBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUN0SkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNoSEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsIihmdW5jdGlvbiAoKSB7XG5cdFwidXNlIHN0cmljdFwiO1xuXG5cdHZhciBhcHBVSSA9IHJlcXVpcmUoJy4vdWkuanMnKS5hcHBVSTtcblxuXHR2YXIgYWNjb3VudCA9IHtcblxuICAgICAgICBidXR0b25VcGRhdGVBY2NvdW50RGV0YWlsczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2FjY291bnREZXRhaWxzU3VibWl0JyksXG4gICAgICAgIGJ1dHRvblVwZGF0ZUxvZ2luRGV0YWlsczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2FjY291bnRMb2dpblN1Ym1pdCcpLFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uVXBkYXRlQWNjb3VudERldGFpbHMpLm9uKCdjbGljaycsIHRoaXMudXBkYXRlQWNjb3VudERldGFpbHMpO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblVwZGF0ZUxvZ2luRGV0YWlscykub24oJ2NsaWNrJywgdGhpcy51cGRhdGVMb2dpbkRldGFpbHMpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgdXBkYXRlcyBhY2NvdW50IGRldGFpbHNcbiAgICAgICAgKi9cbiAgICAgICAgdXBkYXRlQWNjb3VudERldGFpbHM6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2FsbCBmaWVsZHMgZmlsbGVkIGluP1xuXG4gICAgICAgICAgICB2YXIgYWxsR29vZCA9IDE7XG5cbiAgICAgICAgICAgIGlmKCAkKCcjYWNjb3VudF9kZXRhaWxzIGlucHV0I2ZpcnN0bmFtZScpLnZhbCgpID09PSAnJyApIHtcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9kZXRhaWxzIGlucHV0I2ZpcnN0bmFtZScpLmNsb3Nlc3QoJy5mb3JtLWdyb3VwJykuYWRkQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgIGFsbEdvb2QgPSAwO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9kZXRhaWxzIGlucHV0I2ZpcnN0bmFtZScpLmNsb3Nlc3QoJy5mb3JtLWdyb3VwJykucmVtb3ZlQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgIGFsbEdvb2QgPSAxO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiggJCgnI2FjY291bnRfZGV0YWlscyBpbnB1dCNsYXN0bmFtZScpLnZhbCgpID09PSAnJyApIHtcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9kZXRhaWxzIGlucHV0I2xhc3RuYW1lJykuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKS5hZGRDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICAgICAgYWxsR29vZCA9IDA7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2RldGFpbHMgaW5wdXQjbGFzdG5hbWUnKS5jbG9zZXN0KCcuZm9ybS1ncm91cCcpLnJlbW92ZUNsYXNzKCdoYXMtZXJyb3InKTtcbiAgICAgICAgICAgICAgICBhbGxHb29kID0gMTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYoIGFsbEdvb2QgPT09IDEgKSB7XG5cbiAgICAgICAgICAgICAgICB2YXIgdGhlQnV0dG9uID0gJCh0aGlzKTtcblxuICAgICAgICAgICAgICAgIC8vZGlzYWJsZSBidXR0b25cbiAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICAgICAgLy9zaG93IGxvYWRlclxuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2RldGFpbHMgLmxvYWRlcicpLmZhZGVJbig1MDApO1xuXG4gICAgICAgICAgICAgICAgLy9yZW1vdmUgYWxlcnRzXG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfZGV0YWlscyAuYWxlcnRzID4gKicpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgICAgICAgICAgdXJsOiBhcHBVSS5zaXRlVXJsK1widXNlci91YWNjb3VudFwiLFxuICAgICAgICAgICAgICAgICAgICB0eXBlOiAncG9zdCcsXG4gICAgICAgICAgICAgICAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICAgICAgICAgICAgICAgIGRhdGE6ICQoJyNhY2NvdW50X2RldGFpbHMnKS5zZXJpYWxpemUoKVxuICAgICAgICAgICAgICAgIH0pLmRvbmUoZnVuY3Rpb24ocmV0KXtcblxuICAgICAgICAgICAgICAgICAgICAvL2VuYWJsZSBidXR0b25cbiAgICAgICAgICAgICAgICAgICAgdGhlQnV0dG9uLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vaGlkZSBsb2FkZXJcbiAgICAgICAgICAgICAgICAgICAgJCgnI2FjY291bnRfZGV0YWlscyAubG9hZGVyJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9kZXRhaWxzIC5hbGVydHMnKS5hcHBlbmQoICQocmV0LnJlc3BvbnNlSFRNTCkgKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMSApIHsvL3N1Y2Nlc3NcbiAgICAgICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2RldGFpbHMgLmFsZXJ0cyA+IConKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24gKCkgeyAkKHRoaXMpLnJlbW92ZSgpOyB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0sIDMwMDApO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHVwZGF0ZXMgYWNjb3VudCBsb2dpbiBkZXRhaWxzXG4gICAgICAgICovXG4gICAgICAgIHVwZGF0ZUxvZ2luRGV0YWlsczogZnVuY3Rpb24oKSB7XG5cblx0XHRcdGNvbnNvbGUubG9nKGFwcFVJKTtcblxuICAgICAgICAgICAgdmFyIGFsbEdvb2QgPSAxO1xuXG4gICAgICAgICAgICBpZiggJCgnI2FjY291bnRfbG9naW4gaW5wdXQjZW1haWwnKS52YWwoKSA9PT0gJycgKSB7XG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfbG9naW4gaW5wdXQjZW1haWwnKS5jbG9zZXN0KCcuZm9ybS1ncm91cCcpLmFkZENsYXNzKCdoYXMtZXJyb3InKTtcbiAgICAgICAgICAgICAgICBhbGxHb29kID0gMDtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfbG9naW4gaW5wdXQjZW1haWwnKS5jbG9zZXN0KCcuZm9ybS1ncm91cCcpLnJlbW92ZUNsYXNzKCdoYXMtZXJyb3InKTtcbiAgICAgICAgICAgICAgICBhbGxHb29kID0gMTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYoICQoJyNhY2NvdW50X2xvZ2luIGlucHV0I3Bhc3N3b3JkJykudmFsKCkgPT09ICcnICkge1xuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2xvZ2luIGlucHV0I3Bhc3N3b3JkJykuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKS5hZGRDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICAgICAgYWxsR29vZCA9IDA7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2xvZ2luIGlucHV0I3Bhc3N3b3JkJykuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKS5yZW1vdmVDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICAgICAgYWxsR29vZCA9IDE7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKCBhbGxHb29kID09PSAxICkge1xuXG4gICAgICAgICAgICAgICAgdmFyIHRoZUJ1dHRvbiA9ICQodGhpcyk7XG5cbiAgICAgICAgICAgICAgICAvL2Rpc2FibGUgYnV0dG9uXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgICAgIC8vc2hvdyBsb2FkZXJcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9sb2dpbiAubG9hZGVyJykuZmFkZUluKDUwMCk7XG5cbiAgICAgICAgICAgICAgICAvL3JlbW92ZSBhbGVydHNcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9sb2dpbiAuYWxlcnRzID4gKicpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgICAgICAgICAgdXJsOiBhcHBVSS5zaXRlVXJsK1widXNlci91bG9naW5cIixcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogJ3Bvc3QnLFxuICAgICAgICAgICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgICAgICAgICAgICAgICBkYXRhOiAkKCcjYWNjb3VudF9sb2dpbicpLnNlcmlhbGl6ZSgpXG4gICAgICAgICAgICAgICAgfSkuZG9uZShmdW5jdGlvbihyZXQpe1xuXG4gICAgICAgICAgICAgICAgICAgIC8vZW5hYmxlIGJ1dHRvblxuICAgICAgICAgICAgICAgICAgICB0aGVCdXR0b24ucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9oaWRlIGxvYWRlclxuICAgICAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9sb2dpbiAubG9hZGVyJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9sb2dpbiAuYWxlcnRzJykuYXBwZW5kKCAkKHJldC5yZXNwb25zZUhUTUwpICk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDEgKSB7Ly9zdWNjZXNzXG4gICAgICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9sb2dpbiAuYWxlcnRzID4gKicpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbiAoKSB7ICQodGhpcykucmVtb3ZlKCk7IH0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSwgMzAwMCk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfVxuXG4gICAgfTtcblxuICAgIGFjY291bnQuaW5pdCgpO1xuXG59KCkpOyIsIihmdW5jdGlvbiAoKSB7XG5cbi8qIGdsb2JhbHMgc2l0ZVVybDpmYWxzZSwgYmFzZVVybDpmYWxzZSAqL1xuICAgIFwidXNlIHN0cmljdFwiO1xuICAgICAgICBcbiAgICB2YXIgYXBwVUkgPSB7XG4gICAgICAgIFxuICAgICAgICBmaXJzdE1lbnVXaWR0aDogMTkwLFxuICAgICAgICBzZWNvbmRNZW51V2lkdGg6IDMwMCxcbiAgICAgICAgbG9hZGVyQW5pbWF0aW9uOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbG9hZGVyJyksXG4gICAgICAgIHNlY29uZE1lbnVUcmlnZ2VyQ29udGFpbmVyczogJCgnI21lbnUgI21haW4gI2VsZW1lbnRDYXRzLCAjbWVudSAjbWFpbiAjdGVtcGxhdGVzVWwnKSxcbiAgICAgICAgc2l0ZVVybDogc2l0ZVVybCxcbiAgICAgICAgYmFzZVVybDogYmFzZVVybCxcbiAgICAgICAgXG4gICAgICAgIHNldHVwOiBmdW5jdGlvbigpe1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBGYWRlIHRoZSBsb2FkZXIgYW5pbWF0aW9uXG4gICAgICAgICAgICAkKGFwcFVJLmxvYWRlckFuaW1hdGlvbikuZmFkZU91dChmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICQoJyNtZW51JykuYW5pbWF0ZSh7J2xlZnQnOiAtYXBwVUkuZmlyc3RNZW51V2lkdGh9LCAxMDAwKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBUYWJzXG4gICAgICAgICAgICAkKFwiLm5hdi10YWJzIGFcIikub24oJ2NsaWNrJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS50YWIoXCJzaG93XCIpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgICQoXCJzZWxlY3Quc2VsZWN0XCIpLnNlbGVjdDIoKTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgJCgnOnJhZGlvLCA6Y2hlY2tib3gnKS5yYWRpb2NoZWNrKCk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgIC8vIFRvb2x0aXBzXG4gICAgICAgICAgICAkKFwiW2RhdGEtdG9nZ2xlPXRvb2x0aXBdXCIpLnRvb2x0aXAoXCJoaWRlXCIpO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBUYWJsZTogVG9nZ2xlIGFsbCBjaGVja2JveGVzXG4gICAgICAgICAgICAkKCcudGFibGUgLnRvZ2dsZS1hbGwgOmNoZWNrYm94Jykub24oJ2NsaWNrJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyk7XG4gICAgICAgICAgICAgICAgdmFyIGNoID0gJHRoaXMucHJvcCgnY2hlY2tlZCcpO1xuICAgICAgICAgICAgICAgICR0aGlzLmNsb3Nlc3QoJy50YWJsZScpLmZpbmQoJ3Rib2R5IDpjaGVja2JveCcpLnJhZGlvY2hlY2soIWNoID8gJ3VuY2hlY2snIDogJ2NoZWNrJyk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgLy8gQWRkIHN0eWxlIGNsYXNzIG5hbWUgdG8gYSB0b29sdGlwc1xuICAgICAgICAgICAgJChcIi50b29sdGlwXCIpLmFkZENsYXNzKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGlmICgkKHRoaXMpLnByZXYoKS5hdHRyKFwiZGF0YS10b29sdGlwLXN0eWxlXCIpKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBcInRvb2x0aXAtXCIgKyAkKHRoaXMpLnByZXYoKS5hdHRyKFwiZGF0YS10b29sdGlwLXN0eWxlXCIpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAkKFwiLmJ0bi1ncm91cFwiKS5vbignY2xpY2snLCBcImFcIiwgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5zaWJsaW5ncygpLnJlbW92ZUNsYXNzKFwiYWN0aXZlXCIpLmVuZCgpLmFkZENsYXNzKFwiYWN0aXZlXCIpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgIC8vIEZvY3VzIHN0YXRlIGZvciBhcHBlbmQvcHJlcGVuZCBpbnB1dHNcbiAgICAgICAgICAgICQoJy5pbnB1dC1ncm91cCcpLm9uKCdmb2N1cycsICcuZm9ybS1jb250cm9sJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICQodGhpcykuY2xvc2VzdCgnLmlucHV0LWdyb3VwLCAuZm9ybS1ncm91cCcpLmFkZENsYXNzKCdmb2N1cycpO1xuICAgICAgICAgICAgfSkub24oJ2JsdXInLCAnLmZvcm0tY29udHJvbCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJy5pbnB1dC1ncm91cCwgLmZvcm0tZ3JvdXAnKS5yZW1vdmVDbGFzcygnZm9jdXMnKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBUYWJsZTogVG9nZ2xlIGFsbCBjaGVja2JveGVzXG4gICAgICAgICAgICAkKCcudGFibGUgLnRvZ2dsZS1hbGwnKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICB2YXIgY2ggPSAkKHRoaXMpLmZpbmQoJzpjaGVja2JveCcpLnByb3AoJ2NoZWNrZWQnKTtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJy50YWJsZScpLmZpbmQoJ3Rib2R5IDpjaGVja2JveCcpLmNoZWNrYm94KCFjaCA/ICdjaGVjaycgOiAndW5jaGVjaycpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgIC8vIFRhYmxlOiBBZGQgY2xhc3Mgcm93IHNlbGVjdGVkXG4gICAgICAgICAgICAkKCcudGFibGUgdGJvZHkgOmNoZWNrYm94Jykub24oJ2NoZWNrIHVuY2hlY2sgdG9nZ2xlJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpXG4gICAgICAgICAgICAgICAgLCBjaGVjayA9ICR0aGlzLnByb3AoJ2NoZWNrZWQnKVxuICAgICAgICAgICAgICAgICwgdG9nZ2xlID0gZS50eXBlID09PSAndG9nZ2xlJ1xuICAgICAgICAgICAgICAgICwgY2hlY2tib3hlcyA9ICQoJy50YWJsZSB0Ym9keSA6Y2hlY2tib3gnKVxuICAgICAgICAgICAgICAgICwgY2hlY2tBbGwgPSBjaGVja2JveGVzLmxlbmd0aCA9PT0gY2hlY2tib3hlcy5maWx0ZXIoJzpjaGVja2VkJykubGVuZ3RoO1xuXG4gICAgICAgICAgICAgICAgJHRoaXMuY2xvc2VzdCgndHInKVtjaGVjayA/ICdhZGRDbGFzcycgOiAncmVtb3ZlQ2xhc3MnXSgnc2VsZWN0ZWQtcm93Jyk7XG4gICAgICAgICAgICAgICAgaWYgKHRvZ2dsZSkgJHRoaXMuY2xvc2VzdCgnLnRhYmxlJykuZmluZCgnLnRvZ2dsZS1hbGwgOmNoZWNrYm94JykuY2hlY2tib3goY2hlY2tBbGwgPyAnY2hlY2snIDogJ3VuY2hlY2snKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBTd2l0Y2hcbiAgICAgICAgICAgICQoXCJbZGF0YS10b2dnbGU9J3N3aXRjaCddXCIpLndyYXAoJzxkaXYgY2xhc3M9XCJzd2l0Y2hcIiAvPicpLnBhcmVudCgpLmJvb3RzdHJhcFN3aXRjaCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgXG4gICAgICAgICAgICBhcHBVSS5zZWNvbmRNZW51VHJpZ2dlckNvbnRhaW5lcnMub24oJ2NsaWNrJywgJ2E6bm90KC5idG4pJywgYXBwVUkuc2Vjb25kTWVudUFuaW1hdGlvbik7XG4gICAgICAgICAgICAgICAgICAgICAgICBcbiAgICAgICAgfSxcbiAgICAgICAgXG4gICAgICAgIHNlY29uZE1lbnVBbmltYXRpb246IGZ1bmN0aW9uKCl7XG4gICAgICAgIFxuICAgICAgICAgICAgJCgnI21lbnUgI21haW4gYScpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcbiAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xuXHRcbiAgICAgICAgICAgIC8vc2hvdyBvbmx5IHRoZSByaWdodCBlbGVtZW50c1xuICAgICAgICAgICAgJCgnI21lbnUgI3NlY29uZCB1bCBsaScpLmhpZGUoKTtcbiAgICAgICAgICAgICQoJyNtZW51ICNzZWNvbmQgdWwgbGkuJyskKHRoaXMpLmF0dHIoJ2lkJykpLnNob3coKTtcblxuICAgICAgICAgICAgaWYoICQodGhpcykuYXR0cignaWQnKSA9PT0gJ2FsbCcgKSB7XG4gICAgICAgICAgICAgICAgJCgnI21lbnUgI3NlY29uZCB1bCNlbGVtZW50cyBsaScpLnNob3coKTtcdFx0XG4gICAgICAgICAgICB9XG5cdFxuICAgICAgICAgICAgJCgnLm1lbnUgLnNlY29uZCcpLmNzcygnZGlzcGxheScsICdibG9jaycpLnN0b3AoKS5hbmltYXRlKHtcbiAgICAgICAgICAgICAgICB3aWR0aDogYXBwVUkuc2Vjb25kTWVudVdpZHRoXG4gICAgICAgICAgICB9LCA1MDApO1x0XG4gICAgICAgICAgICAgICAgXG4gICAgICAgIH1cbiAgICAgICAgXG4gICAgfTtcbiAgICBcbiAgICAvL2luaXRpYXRlIHRoZSBVSVxuICAgIGFwcFVJLnNldHVwKCk7XG5cblxuICAgIC8vKioqKiBFWFBPUlRTXG4gICAgbW9kdWxlLmV4cG9ydHMuYXBwVUkgPSBhcHBVSTtcbiAgICBcbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cblx0cmVxdWlyZSgnLi9tb2R1bGVzL2FjY291bnQnKTtcblxuXHQkKCcjY29uZmlnSGVscCcpLmFmZml4KHtcbiAgICAgICAgb2Zmc2V0OiB7XG4gICAgICAgICAgICB0b3A6IDIwMFxuICAgICAgICB9XG4gICAgfSk7XG5cbiAgICAvL3NldCB0aGUgd2lkdGggZm9yIHRoZSBjb25maWdIZWxwXG4gICAgJCgnLmNvbmZpZ0hlbHAnKS53aWR0aCggJCgnLmNvbmZpZ0hlbHAnKS53aWR0aCgpIClcblxuICAgIC8vaGVscCBpbmZvXG4gICAgJCgnZm9ybS5zZXR0aW5nc0Zvcm0gdGV4dGFyZWEnKS5mb2N1cyhmdW5jdGlvbigpe1xuXG4gICAgICAgICQoJyNjb25maWdIZWxwID4gZGl2OmZpcnN0JykuaHRtbCggJCh0aGlzKS5uZXh0KCkuaHRtbCgpICk7XG5cbiAgICAgICAgJCgnI2NvbmZpZ0hlbHAnKS5mYWRlSW4oNTAwKTtcblxuICAgICAgICAvL3NldCB0aGUgd2lkdGggZm9yIHRoZSBjb25maWdIZWxwXG4gICAgICAgICQoJy5jb25maWdIZWxwJykud2lkdGgoICQoJy5jb25maWdIZWxwJykud2lkdGgoKSApXG5cbiAgICB9KTtcblxuICAgICQoJ2Zvcm0uc2V0dGluZ3NGb3JtIHRleHRhcmVhJykuYmx1cihmdW5jdGlvbigpe1xuXG4gICAgICAgICQoJyNjb25maWdIZWxwJykuaGlkZSgpO1xuXG4gICAgfSk7XG5cbn0oKSk7Il19
