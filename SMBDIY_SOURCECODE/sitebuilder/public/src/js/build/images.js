(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function () {
	"use strict";

	require('./modules/ui');
	require('./modules/builder');
	require('./modules/config');
	require('./modules/imageLibrary');
	require('./modules/account');

}());
},{"./modules/account":2,"./modules/builder":3,"./modules/config":5,"./modules/imageLibrary":6,"./modules/ui":8}],2:[function(require,module,exports){
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
},{"./ui.js":8}],3:[function(require,module,exports){
(function () {
	"use strict";

    var siteBuilderUtils = require('./utils.js');
    var bConfig = require('./config.js');
    var appUI = require('./ui.js').appUI;


	 /*
        Basic Builder UI initialisation
    */
    var builderUI = {

        allBlocks: {},                                              //holds all blocks loaded from the server
        menuWrapper: document.getElementById('menu'),
        primarySideMenuWrapper: document.getElementById('main'),
        buttonBack: document.getElementById('backButton'),
        buttonBackConfirm: document.getElementById('leavePageButton'),

        siteBuilderModes: document.getElementById('siteBuilderModes'),
        aceEditors: {},
        frameContents: '',                                      //holds frame contents
        templateID: 0,                                          //holds the template ID for a page (???)
        radioBlockMode: document.getElementById('modeBlock'),

        modalDeleteBlock: document.getElementById('deleteBlock'),
        modalResetBlock: document.getElementById('resetBlock'),
        modalDeletePage: document.getElementById('deletePage'),
        buttonDeletePageConfirm: document.getElementById('deletePageConfirm'),

        dropdownPageLinks: document.getElementById('internalLinksDropdown'),

        pageInUrl: null,

        tempFrame: {},

        init: function(){

            //load blocks
            $.getJSON(appUI.baseUrl+'/elements.json?v=12345678', function(data){ builderUI.allBlocks = data; builderUI.implementBlocks(); });

            //sitebar hover animation action
            $(this.menuWrapper).on('mouseenter', function(){

                $(this).stop().animate({'left': '0px'}, 500);

            }).on('mouseleave', function(){

                $(this).stop().animate({'left': '-190px'}, 500);

                $('#menu #main a').removeClass('active');
                $('.menu .second').stop().animate({
                    width: 0
                }, 500, function(){
                    $('#menu #second').hide();
                });

            });

            //prevent click event on ancors in the block section of the sidebar
            $(this.primarySideMenuWrapper).on('click', 'a:not(.actionButtons)', function(e){e.preventDefault();});

            $(this.buttonBack).on('click', this.backButton);
            $(this.buttonBackConfirm).on('click', this.backButtonConfirm);

            //notify the user of pending chnages when clicking the back button
            $(window).bind('beforeunload', function(){
                if( site.pendingChanges === true ) {
                    return 'Your site contains changed which haven\'t been saved yet. Are you sure you want to leave?';
                }
            });

            //make sure we start in block mode
            $(this.radioBlockMode).radiocheck('check').on('click', this.activateBlockMode);

            //URL parameters
            builderUI.pageInUrl = siteBuilderUtils.getParameterByName('p');

        },


        /*
            builds the blocks into the site bar
        */
        implementBlocks: function() {

            var newItem, loaderFunction;

            for( var key in this.allBlocks.elements ) {

                var niceKey = key.toLowerCase().replace(" ", "_");

                $('<li><a href="" id="'+niceKey+'">'+key+'</a></li>').appendTo('#menu #main ul#elementCats');

                for( var x = 0; x < this.allBlocks.elements[key].length; x++ ) {

                    if( this.allBlocks.elements[key][x].thumbnail === null ) {//we'll need an iframe

                        //build us some iframes!

                        if( this.allBlocks.elements[key][x].sandbox ) {

                            if( this.allBlocks.elements[key][x].loaderFunction ) {
                                loaderFunction = 'data-loaderfunction="'+this.allBlocks.elements[key][x].loaderFunction+'"';
                            }

                            newItem = $('<li class="element '+niceKey+'"><iframe src="'+appUI.baseUrl+this.allBlocks.elements[key][x].url+'" scrolling="no" sandbox="allow-same-origin"></iframe></li>');

                        } else {

                            newItem = $('<li class="element '+niceKey+'"><iframe src="about:blank" scrolling="no"></iframe></li>');

                        }

                        newItem.find('iframe').uniqueId();
                        newItem.find('iframe').attr('src', appUI.baseUrl+this.allBlocks.elements[key][x].url);

                    } else {//we've got a thumbnail

                        if( this.allBlocks.elements[key][x].sandbox ) {

                            if( this.allBlocks.elements[key][x].loaderFunction ) {
                                loaderFunction = 'data-loaderfunction="'+this.allBlocks.elements[key][x].loaderFunction+'"';
                            }

                            newItem = $('<li class="element '+niceKey+'"><img src="'+appUI.baseUrl+this.allBlocks.elements[key][x].thumbnail+'" data-srcc="'+appUI.baseUrl+this.allBlocks.elements[key][x].url+'" data-height="'+this.allBlocks.elements[key][x].height+'" data-sandbox="" '+loaderFunction+'></li>');

                        } else {

                            newItem = $('<li class="element '+niceKey+'"><img src="'+appUI.baseUrl+this.allBlocks.elements[key][x].thumbnail+'" data-srcc="'+appUI.baseUrl+this.allBlocks.elements[key][x].url+'" data-height="'+this.allBlocks.elements[key][x].height+'"></li>');

                        }
                    }

                    newItem.appendTo('#menu #second ul#elements');

                    //zoomer works

                    var theHeight;

                    if( this.allBlocks.elements[key][x].height ) {

                        theHeight = this.allBlocks.elements[key][x].height*0.25;

                    } else {

                        theHeight = 'auto';

                    }

                    newItem.find('iframe').zoomer({
                        zoom: 0.25,
                        width: 270,
                        height: theHeight,
                        message: "Drag&Drop Me!"
                    });

                }

            }

            //draggables
            builderUI.makeDraggable();

        },


        /*
            event handler for when the back link is clicked
        */
        backButton: function() {

            if( site.pendingChanges === true ) {
                $('#backModal').modal('show');
                return false;
            }

        },


        /*
            button for confirming leaving the page
        */
        backButtonConfirm: function() {

            site.pendingChanges = false;//prevent the JS alert after confirming user wants to leave

        },


        /*
            activates block mode
        */
        activateBlockMode: function() {

            site.activePage.toggleFrameCovers('On');

            //trigger custom event
            $('body').trigger('modeBlocks');

        },


        /*
            makes the blocks and templates in the sidebar draggable onto the canvas
        */
        makeDraggable: function() {

            $('#elements li, #templates li').each(function(){

                $(this).draggable({
                    helper: function() {
                        return $('<div style="height: 100px; width: 300px; background: #F9FAFA; box-shadow: 5px 5px 1px rgba(0,0,0,0.1); text-align: center; line-height: 100px; font-size: 28px; color: #16A085"><span class="fui-list"></span></div>');
                    },
                    revert: 'invalid',
                    appendTo: 'body',
                    connectToSortable: '#pageList > ul',
                    start: function(){

                        //switch to block mode
                        $('input:radio[name=mode]').parent().addClass('disabled');
                        $('input:radio[name=mode]#modeBlock').radiocheck('check');

                    }

                });

            });

            $('#elements li a').each(function(){

                $(this).unbind('click').bind('click', function(e){
                    e.preventDefault();
                });

            });

        },


        /*
            Implements the site on the canvas, called from the Site object when the siteData has completed loading
        */
        populateCanvas: function() {

            var i;

            //if we have any blocks at all, activate the modes
            if( Object.keys(site.pages).length > 0 ) {
                var modes = builderUI.siteBuilderModes.querySelectorAll('input[type="radio"]');
                for( i = 0; i < modes.length; i++ ) {
                    modes[i].removeAttribute('disabled');
                }
            }

            var counter = 1;

            //loop through the pages

            for( i in site.pages ) {

                var newPage = new Page(i, site.pages[i], counter);

                counter++;

                //set this page as active?
                if( builderUI.pageInUrl === i ) {
                    newPage.selectPage();
                }

            }

            //activate the first page
            if(site.sitePages.length > 0 && builderUI.pageInUrl === null) {
                site.sitePages[0].selectPage();
            }

        }

    };


    /*
        Page constructor
    */
    function Page (pageName, page, counter) {

        this.name = pageName || "";
        this.pageID = page.pages_id || 0;
        this.blocks = [];
        this.parentUL = {}; //parent UL on the canvas
        this.status = '';//'', 'new' or 'changed'
        this.scripts = [];//tracks script URLs used on this page

        this.pageSettings = {
            title: page.pages_title || '',
            meta_description: page.meta_description || '',
            meta_keywords: page.meta_keywords || '',
            header_includes: page.header_includes || '',
            page_css: page.page_css || ''
        };

        this.pageMenuTemplate = '<a href="" class="menuItemLink">page</a><span class="pageButtons"><a href="" class="fileEdit fui-new"></a><a href="" class="fileDel fui-cross"><a class="btn btn-xs btn-primary btn-embossed fileSave fui-check" href="#"></a></span></a></span>';

        this.menuItem = {};//reference to the pages menu item for this page instance
        this.linksDropdownItem = {};//reference to the links dropdown item for this page instance

        this.parentUL = document.createElement('UL');
        this.parentUL.setAttribute('id', "page"+counter);

        /*
            makes the clicked page active
        */
        this.selectPage = function() {

            //console.log('select:');
            //console.log(this.pageSettings);

            //mark the menu item as active
            site.deActivateAll();
            $(this.menuItem).addClass('active');

            //let Site know which page is currently active
            site.setActive(this);

            //display the name of the active page on the canvas
            site.pageTitle.innerHTML = this.name;

            //load the page settings into the page settings modal
            site.inputPageSettingsTitle.value = this.pageSettings.title;
            site.inputPageSettingsMetaDescription.value = this.pageSettings.meta_description;
            site.inputPageSettingsMetaKeywords.value = this.pageSettings.meta_keywords;
            site.inputPageSettingsIncludes.value = this.pageSettings.header_includes;
            site.inputPageSettingsPageCss.value = this.pageSettings.page_css;

            //trigger custom event
            $('body').trigger('changePage');

            //reset the heights for the blocks on the current page
            for( var i in this.blocks ) {

                if( Object.keys(this.blocks[i].frameDocument).length > 0 ){
                    this.blocks[i].heightAdjustment();
                }

            }

            //show the empty message?
            this.isEmpty();

        };

        /*
            changed the location/order of a block within a page
        */
        this.setPosition = function(frameID, newPos) {

            //we'll need the block object connected to iframe with frameID

            for(var i in this.blocks) {

                if( this.blocks[i].frame.getAttribute('id') === frameID ) {

                    //change the position of this block in the blocks array
                    this.blocks.splice(newPos, 0, this.blocks.splice(i, 1)[0]);

                }

            }

        };

        /*
            delete block from blocks array
        */
        this.deleteBlock = function(block) {

            //remove from blocks array
            for( var i in this.blocks ) {
                if( this.blocks[i] === block ) {
                    //found it, remove from blocks array
                    this.blocks.splice(i, 1);
                }
            }

            site.setPendingChanges(true);

        };

        /*
            toggles all block frameCovers on this page
        */
        this.toggleFrameCovers = function(onOrOff) {

            for( var i in this.blocks ) {

                this.blocks[i].toggleCover(onOrOff);

            }

        };

        /*
            setup for editing a page name
        */
        this.editPageName = function() {

            if( !this.menuItem.classList.contains('edit') ) {

                //hide the link
                this.menuItem.querySelector('a.menuItemLink').style.display = 'none';

                //insert the input field
                var newInput = document.createElement('input');
                newInput.type = 'text';
                newInput.setAttribute('name', 'page');
                newInput.setAttribute('value', this.name);
                this.menuItem.insertBefore(newInput, this.menuItem.firstChild);

                newInput.focus();

                var tmpStr = newInput.getAttribute('value');
                newInput.setAttribute('value', '');
                newInput.setAttribute('value', tmpStr);

                this.menuItem.classList.add('edit');

            }

        };

        /*
            Updates this page's name (event handler for the save button)
        */
        this.updatePageNameEvent = function(el) {

            if( this.menuItem.classList.contains('edit') ) {

                //el is the clicked button, we'll need access to the input
                var theInput = this.menuItem.querySelector('input[name="page"]');

                //make sure the page's name is OK
                if( site.checkPageName(theInput.value) ) {

                    this.name = site.prepPageName( theInput.value );

                    this.menuItem.querySelector('input[name="page"]').remove();
                    this.menuItem.querySelector('a.menuItemLink').innerHTML = this.name;
                    this.menuItem.querySelector('a.menuItemLink').style.display = 'block';

                    this.menuItem.classList.remove('edit');

                    //update the links dropdown item
                    this.linksDropdownItem.text = this.name;
                    this.linksDropdownItem.setAttribute('value', this.name+".html");

                    //update the page name on the canvas
                    site.pageTitle.innerHTML = this.name;

                    //changed page title, we've got pending changes
                    site.setPendingChanges(true);

                } else {

                    alert(site.pageNameError);

                }

            }

        };

        /*
            deletes this entire page
        */
        this.delete = function() {

            //delete from the Site
            for( var i in site.sitePages ) {

                if( site.sitePages[i] === this ) {//got a match!

                    //delete from site.sitePages
                    site.sitePages.splice(i, 1);

                    //delete from canvas
                    this.parentUL.remove();

                    //add to deleted pages
                    site.pagesToDelete.push(this.name);

                    //delete the page's menu item
                    this.menuItem.remove();

                    //delet the pages link dropdown item
                    this.linksDropdownItem.remove();

                    //activate the first page
                    site.sitePages[0].selectPage();

                    //page was deleted, so we've got pending changes
                    site.setPendingChanges(true);

                }

            }

        };

        /*
            checks if the page is empty, if so show the 'empty' message
        */
        this.isEmpty = function() {

            if( this.blocks.length === 0 ) {

                site.messageStart.style.display = 'block';
                site.divFrameWrapper.classList.add('empty');

            } else {

                site.messageStart.style.display = 'none';
                site.divFrameWrapper.classList.remove('empty');

            }

        };

        /*
            preps/strips this page data for a pending ajax request
        */
        this.prepForSave = function() {

            var page = {};

            page.name = this.name;
            page.pageSettings = this.pageSettings;
            page.status = this.status;
            page.blocks = [];

            //process the blocks

            for( var x = 0; x < this.blocks.length; x++ ) {

                var block = {};

                if( this.blocks[x].sandbox ) {

                    block.frameContent = "<html>"+$('#sandboxes #'+this.blocks[x].sandbox).contents().find('html').html()+"</html>";
                    block.sandbox = true;
                    block.loaderFunction = this.blocks[x].sandbox_loader;

                } else {

                    block.frameContent = this.blocks[x].getSource();
                    block.sandbox = false;
                    block.loaderFunction = '';

                }

                block.frameHeight = this.blocks[x].frameHeight;
                block.originalUrl = this.blocks[x].originalUrl;

                page.blocks.push(block);

            }

            return page;

        };

        /*
            generates the full page, using skeleton.html
        */
        this.fullPage = function() {

            var page = this;//reference to self for later
            page.scripts = [];//make sure it's empty, we'll store script URLs in there later

            var newDocMainParent = $('iframe#skeleton').contents().find( bConfig.pageContainer );

            //empty out the skeleton first
            $('iframe#skeleton').contents().find( bConfig.pageContainer ).html('');

            //remove old script tags
            $('iframe#skeleton').contents().find( 'script' ).each(function(){
                $(this).remove();
            });

            var theContents;

            for( var i in this.blocks ) {

                //grab the block content
                if (this.blocks[i].sandbox !== false) {

                    theContents = $('#sandboxes #'+this.blocks[i].sandbox).contents().find( bConfig.pageContainer ).clone();

                } else {

                    theContents = $(this.blocks[i].frameDocument.body).find( bConfig.pageContainer ).clone();

                }

                //remove video frameCovers
                theContents.find('.frameCover').each(function () {
                    $(this).remove();
                });

                //remove video frameWrappers
                theContents.find('.videoWrapper').each(function(){

                    var cnt = $(this).contents();
                    $(this).replaceWith(cnt);

                });

                //remove style leftovers from the style editor
                for( var key in bConfig.editableItems ) {

                    theContents.find( key ).each(function(){

                        $(this).removeAttr('data-selector');

                        $(this).css('outline', '');
                        $(this).css('outline-offset', '');
                        $(this).css('cursor', '');

                        if( $(this).attr('style') === '' ) {

                            $(this).removeAttr('style');

                        }

                    });

                }

                //remove style leftovers from the content editor
                for ( var x = 0; x < bConfig.editableContent.length; ++x) {

                    theContents.find( bConfig.editableContent[x] ).each(function(){

                        $(this).removeAttr('data-selector');

                    });

                }

                //append to DOM in the skeleton
                newDocMainParent.append( $(theContents.html()) );

                //do we need to inject any scripts?
                var scripts = $(this.blocks[i].frameDocument.body).find('script');
                var theIframe = document.getElementById("skeleton");

                if( scripts.size() > 0 ) {

                    scripts.each(function(){

                        var script;

                        if( $(this).text() !== '' ) {//script tags with content

                            script = theIframe.contentWindow.document.createElement("script");
                            script.type = 'text/javascript';
                            script.innerHTML = $(this).text();

                            theIframe.contentWindow.document.body.appendChild(script);

                        } else if( $(this).attr('src') !== null && page.scripts.indexOf($(this).attr('src')) === -1 ) {
                            //use indexOf to make sure each script only appears on the produced page once

                            script = theIframe.contentWindow.document.createElement("script");
                            script.type = 'text/javascript';
                            script.src = $(this).attr('src');

                            theIframe.contentWindow.document.body.appendChild(script);

                            page.scripts.push($(this).attr('src'));

                        }

                    });

                }

            }

            console.log(this.scripts);

        };

        /*
            clear out this page
        */
        this.clear = function() {

            var block = this.blocks.pop();

            while( block !== undefined ) {

                block.delete();

                block = this.blocks.pop();

            }

        };


        //loop through the frames/blocks

        if( page.hasOwnProperty('blocks') ) {

            for( var x = 0; x < page.blocks.length; x++ ) {

                //create new Block

                var newBlock = new Block();

                page.blocks[x].src = appUI.siteUrl+"site/getframe/"+page.blocks[x].id;

                //sandboxed block?
                if( page.blocks[x].frames_sandbox === '1') {

                    newBlock.sandbox = true;
                    newBlock.sandbox_loader = page.blocks[x].frames_loaderfunction;

                }

                newBlock.frameID = page.blocks[x].frames_id;
                newBlock.createParentLI(page.blocks[x].frames_height);
                newBlock.createFrame(page.blocks[x]);
                newBlock.createFrameCover();
                newBlock.insertBlockIntoDom(this.parentUL);

                //add the block to the new page
                this.blocks.push(newBlock);

            }

        }

        //add this page to the site object
        site.sitePages.push( this );

        //plant the new UL in the DOM (on the canvas)
        site.divCanvas.appendChild(this.parentUL);

        //make the blocks/frames in each page sortable

        var thePage = this;

        $(this.parentUL).sortable({
            revert: true,
            placeholder: "drop-hover",
            stop: function () {
                site.setPendingChanges(true);
            },
            beforeStop: function(event, ui){

                //template or regular block?
                var attr = ui.item.attr('data-frames');

                var newBlock;

                if (typeof attr !== typeof undefined && attr !== false) {//template, build it

                    $('#start').hide();

                    //clear out all blocks on this page
                    thePage.clear();

                    //create the new frames
                    var frameIDs = ui.item.attr('data-frames').split('-');
                    var heights = ui.item.attr('data-heights').split('-');
                    var urls = ui.item.attr('data-originalurls').split('-');

                    for( var x = 0; x < frameIDs.length; x++) {

                        newBlock = new Block();
                        newBlock.createParentLI(heights[x]);

                        var frameData = {};

                        frameData.src = appUI.siteUrl+'site/getframe/'+frameIDs[x];
                        frameData.frames_original_url = appUI.siteUrl+'site/getframe/'+frameIDs[x];
                        frameData.frames_height = heights[x];

                        newBlock.createFrame( frameData );
                        newBlock.createFrameCover();
                        newBlock.insertBlockIntoDom(thePage.parentUL);

                        //add the block to the new page
                        thePage.blocks.push(newBlock);

                        //dropped element, so we've got pending changes
                        site.setPendingChanges(true);

                    }

                    //set the tempateID
                    builderUI.templateID = ui.item.attr('data-pageid');

                    //make sure nothing gets dropped in the lsit
                    ui.item.html(null);

                    //delete drag place holder
                    $('body .ui-sortable-helper').remove();

                } else {//regular block

                    //are we dealing with a new block being dropped onto the canvas, or a reordering og blocks already on the canvas?

                    if( ui.item.find('.frameCover > button').size() > 0 ) {//re-ordering of blocks on canvas

                        //no need to create a new block object, we simply need to make sure the position of the existing block in the Site object
                        //is changed to reflect the new position of the block on th canvas

                        var frameID = ui.item.find('iframe').attr('id');
                        var newPos = ui.item.index();

                        site.activePage.setPosition(frameID, newPos);

                    } else {//new block on canvas

                        //new block
                        newBlock = new Block();

                        newBlock.placeOnCanvas(ui);

                    }

                }

            },
            start: function(event, ui){

                if( ui.item.find('.frameCover').size() !== 0 ) {
                    builderUI.frameContents = ui.item.find('iframe').contents().find( bConfig.pageContainer ).html();
                }

            },
            over: function(){

                $('#start').hide();

            }
        });

        //add to the pages menu
        this.menuItem = document.createElement('LI');
        this.menuItem.innerHTML = this.pageMenuTemplate;

        $(this.menuItem).find('a:first').text(pageName).attr('href', '#page'+counter);

        var theLink = $(this.menuItem).find('a:first').get(0);

        //bind some events
        this.menuItem.addEventListener('click', this, false);

        this.menuItem.querySelector('a.fileEdit').addEventListener('click', this, false);
        this.menuItem.querySelector('a.fileSave').addEventListener('click', this, false);
        this.menuItem.querySelector('a.fileDel').addEventListener('click', this, false);

        //add to the page link dropdown
        this.linksDropdownItem = document.createElement('OPTION');
        this.linksDropdownItem.setAttribute('value', pageName+".html");
        this.linksDropdownItem.text = pageName;

        builderUI.dropdownPageLinks.appendChild( this.linksDropdownItem );

        site.pagesMenu.appendChild(this.menuItem);

    }

    Page.prototype.handleEvent = function(event) {
        switch (event.type) {
            case "click":

                if( event.target.classList.contains('fileEdit') ) {

                    this.editPageName();

                } else if( event.target.classList.contains('fileSave') ) {

                    this.updatePageNameEvent(event.target);

                } else if( event.target.classList.contains('fileDel') ) {

                    var thePage = this;

                    $(builderUI.modalDeletePage).modal('show');

                    $(builderUI.modalDeletePage).off('click', '#deletePageConfirm').on('click', '#deletePageConfirm', function() {

                        thePage.delete();

                        $(builderUI.modalDeletePage).modal('hide');

                    });

                } else {

                    this.selectPage();

                }

        }
    };


    /*
        Block constructor
    */
    function Block () {

        this.frameID = 0;
        this.sandbox = false;
        this.sandbox_loader = '';
        this.status = '';//'', 'changed' or 'new'
        this.originalUrl = '';

        this.parentLI = {};
        this.frameCover = {};
        this.frame = {};
        this.frameDocument = {};
        this.frameHeight = 0;

        this.annot = {};
        this.annotTimeout = {};

        /*
            creates the parent container (LI)
        */
        this.createParentLI = function(height) {

            this.parentLI = document.createElement('LI');
            this.parentLI.setAttribute('class', 'element');
            //this.parentLI.setAttribute('style', 'height: '+height+'px');

        };

        /*
            creates the iframe on the canvas
        */
        this.createFrame = function(frame) {

            this.frame = document.createElement('IFRAME');
            this.frame.setAttribute('frameborder', 0);
            this.frame.setAttribute('scrolling', 0);
            this.frame.setAttribute('src', frame.src);
            this.frame.setAttribute('data-originalurl', frame.frames_original_url);
            this.originalUrl = frame.frames_original_url;
            //this.frame.setAttribute('data-height', frame.frames_height);
            //this.frameHeight = frame.frames_height;
            this.frame.setAttribute('style', 'background: '+"#ffffff url('"+appUI.baseUrl+"src/images/loading.gif') 50% 50% no-repeat");

            $(this.frame).uniqueId();

            //sandbox?
            if( this.sandbox !== false ) {

                this.frame.setAttribute('data-loaderfunction', this.sandbox_loader);
                this.frame.setAttribute('data-sandbox', this.sandbox);

                //recreate the sandboxed iframe elsewhere
                var sandboxedFrame = $('<iframe src="'+frame.src+'" id="'+this.sandbox+'" sandbox="allow-same-origin"></iframe>');
                $('#sandboxes').append( sandboxedFrame );

            }

        };

        /*
            insert the iframe into the DOM on the canvas
        */
        this.insertBlockIntoDom = function(theUL) {

            this.parentLI.appendChild(this.frame);
            theUL.appendChild( this.parentLI );

            this.frame.addEventListener('load', this, false);

        };

        /*
            sets the frame document for the block's iframe
        */
        this.setFrameDocument = function() {

            //set the frame document as well
            if( this.frame.contentDocument ) {
                this.frameDocument = this.frame.contentDocument;
            } else {
                this.frameDocument = this.frame.contentWindow.document;
            }

            //this.heightAdjustment();

        };

        /*
            creates the frame cover and block action button
        */
        this.createFrameCover = function() {

            //build the frame cover and block action buttons
            this.frameCover = document.createElement('DIV');
            this.frameCover.classList.add('frameCover');
            this.frameCover.classList.add('fresh');
            this.frameCover.style.height = this.frameHeight+"px";

            var delButton = document.createElement('BUTTON');
            delButton.setAttribute('class', 'btn btn-danger deleteBlock');
            delButton.setAttribute('type', 'button');
            delButton.innerHTML = '<span class="fui-trash"></span> remove';
            delButton.addEventListener('click', this, false);

            var resetButton = document.createElement('BUTTON');
            resetButton.setAttribute('class', 'btn btn-warning resetBlock');
            resetButton.setAttribute('type', 'button');
            resetButton.innerHTML = '<i class="fa fa-refresh"></i> reset';
            resetButton.addEventListener('click', this, false);

            var htmlButton = document.createElement('BUTTON');
            htmlButton.setAttribute('class', 'btn btn-inverse htmlBlock');
            htmlButton.setAttribute('type', 'button');
            htmlButton.innerHTML = '<i class="fa fa-code"></i> source';
            htmlButton.addEventListener('click', this, false);

            this.frameCover.appendChild(delButton);
            this.frameCover.appendChild(resetButton);
            this.frameCover.appendChild(htmlButton);

            this.parentLI.appendChild(this.frameCover);

        };

        /*
            automatically corrects the height of the block's iframe depending on its content
        */
        this.heightAdjustment = function() {

            var pageContainer = this.frameDocument.body;
            var height = pageContainer.scrollHeight;

            this.frame.style.height = height+"px";
            this.parentLI.style.height = height+"px";
            this.frameCover.style.height = height+"px";

            this.frameHeight = height;

        };

        /*
            deletes a block
        */
        this.delete = function() {

            //remove from DOM/canvas with a nice animation
            $(this.frame.parentNode).fadeOut(500, function(){

                this.remove();

                site.activePage.isEmpty();

            });

            //remove from blocks array in the active page
            site.activePage.deleteBlock(this);

            //sanbox
            if( this.sanbdox ) {
                document.getElementById( this.sandbox ).remove();
            }

            //element was deleted, so we've got pending change
            site.setPendingChanges(true);

        };

        /*
            resets a block to it's orignal state
        */
        this.reset = function() {

            //reset frame by reloading it
            this.frame.contentWindow.location.reload();

            //sandbox?
            if( this.sandbox ) {
                var sandboxFrame = document.getElementById(this.sandbox).contentWindow.location.reload();
            }

            //element was deleted, so we've got pending changes
            site.setPendingChanges(true);

        };

        /*
            launches the source code editor
        */
        this.source = function() {

            //hide the iframe
            this.frame.style.display = 'none';

            //disable sortable on the parentLI
            $(this.parentLI.parentNode).sortable('disable');

            //built editor element
            var theEditor = document.createElement('DIV');
            theEditor.classList.add('aceEditor');
            $(theEditor).uniqueId();

            this.parentLI.appendChild(theEditor);

            //build and append error drawer
            var newLI = document.createElement('LI');
            var errorDrawer = document.createElement('DIV');
            errorDrawer.classList.add('errorDrawer');
            errorDrawer.setAttribute('id', 'div_errorDrawer');
            errorDrawer.innerHTML = '<button type="button" class="btn btn-xs btn-embossed btn-default button_clearErrorDrawer" id="button_clearErrorDrawer">CLEAR</button>';
            newLI.appendChild(errorDrawer);
            errorDrawer.querySelector('button').addEventListener('click', this, false);
            this.parentLI.parentNode.insertBefore(newLI, this.parentLI.nextSibling);


            var theId = theEditor.getAttribute('id');
            var editor = ace.edit( theId );

            var pageContainer = this.frameDocument.querySelector( bConfig.pageContainer );
            var theHTML = pageContainer.innerHTML;

            editor.setValue( theHTML );
            editor.setTheme("ace/theme/twilight");
            editor.getSession().setMode("ace/mode/html");

            var block = this;


            editor.getSession().on("changeAnnotation", function(){

                block.annot = editor.getSession().getAnnotations();

                clearTimeout(block.annotTimeout);

                var timeoutCount;

                if( $('#div_errorDrawer p').size() === 0 ) {
                    timeoutCount = bConfig.sourceCodeEditSyntaxDelay;
                } else {
                    timeoutCount = 100;
                }

                block.annotTimeout = setTimeout(function(){

                    for (var key in block.annot){

                        if (block.annot.hasOwnProperty(key)) {

                            if( block.annot[key].text !== "Start tag seen without seeing a doctype first. Expected e.g. <!DOCTYPE html>." ) {

                                var newLine = $('<p></p>');
                                var newKey = $('<b>'+block.annot[key].type+': </b>');
                                var newInfo = $('<span> '+block.annot[key].text + "on line " + " <b>" + block.annot[key].row+'</b></span>');
                                newLine.append( newKey );
                                newLine.append( newInfo );

                                $('#div_errorDrawer').append( newLine );

                            }

                        }

                    }

                    if( $('#div_errorDrawer').css('display') === 'none' && $('#div_errorDrawer').find('p').size() > 0 ) {
                        $('#div_errorDrawer').slideDown();
                    }

                }, timeoutCount);


            });

            //buttons
            var cancelButton = document.createElement('BUTTON');
            cancelButton.setAttribute('type', 'button');
            cancelButton.classList.add('btn');
            cancelButton.classList.add('btn-danger');
            cancelButton.classList.add('editCancelButton');
            cancelButton.classList.add('btn-wide');
            cancelButton.innerHTML = '<span class="fui-cross"></span> Cancel';
            cancelButton.addEventListener('click', this, false);

            var saveButton = document.createElement('BUTTON');
            saveButton.setAttribute('type', 'button');
            saveButton.classList.add('btn');
            saveButton.classList.add('btn-primary');
            saveButton.classList.add('editSaveButton');
            saveButton.classList.add('btn-wide');
            saveButton.innerHTML = '<span class="fui-check"></span> Save';
            saveButton.addEventListener('click', this, false);

            var buttonWrapper = document.createElement('DIV');
            buttonWrapper.classList.add('editorButtons');

            buttonWrapper.appendChild( cancelButton );
            buttonWrapper.appendChild( saveButton );

            this.parentLI.appendChild( buttonWrapper );

            builderUI.aceEditors[ theId ] = editor;

        };

        /*
            cancels the block source code editor
        */
        this.cancelSourceBlock = function() {

            //enable draggable on the LI
            $(this.parentLI.parentNode).sortable('enable');

            //delete the errorDrawer
            $(this.parentLI.nextSibling).remove();

            //delete the editor
            this.parentLI.querySelector('.aceEditor').remove();
            $(this.frame).fadeIn(500);

            $(this.parentLI.querySelector('.editorButtons')).fadeOut(500, function(){
                $(this).remove();
            });

        };

        /*
            updates the blocks source code
        */
        this.saveSourceBlock = function() {

            //enable draggable on the LI
            $(this.parentLI.parentNode).sortable('enable');

            var theId = this.parentLI.querySelector('.aceEditor').getAttribute('id');
            var theContent = builderUI.aceEditors[theId].getValue();

            //delete the errorDrawer
            document.getElementById('div_errorDrawer').parentNode.remove();

            //delete the editor
            this.parentLI.querySelector('.aceEditor').remove();

            //update the frame's content
            this.frameDocument.querySelector( bConfig.pageContainer ).innerHTML = theContent;
            this.frame.style.display = 'block';

            //sandboxed?
            if( this.sandbox ) {

                var sandboxFrame = document.getElementById( this.sandbox );
                var sandboxFrameDocument = sandboxFrame.contentDocument || sandboxFrame.contentWindow.document;

                builderUI.tempFrame = sandboxFrame;

                sandboxFrameDocument.querySelector( bConfig.pageContainer ).innerHTML = theContent;

                //do we need to execute a loader function?
                if( this.sandbox_loader !== '' ) {

                    /*
                    var codeToExecute = "sandboxFrame.contentWindow."+this.sandbox_loader+"()";
                    var tmpFunc = new Function(codeToExecute);
                    tmpFunc();
                    */

                }

            }

            $(this.parentLI.querySelector('.editorButtons')).fadeOut(500, function(){
                $(this).remove();
            });

            //adjust height of the frame
            this.heightAdjustment();

            //new page added, we've got pending changes
            site.setPendingChanges(true);

            //block has changed
            this.status = 'changed';

        };

        /*
            clears out the error drawer
        */
        this.clearErrorDrawer = function() {

            var ps = this.parentLI.nextSibling.querySelectorAll('p');

            for( var i = 0; i < ps.length; i++ ) {
                ps[i].remove();
            }

        };

        /*
            toggles the visibility of this block's frameCover
        */
        this.toggleCover = function(onOrOff) {

            if( onOrOff === 'On' ) {

                this.parentLI.querySelector('.frameCover').style.display = 'block';

            } else if( onOrOff === 'Off' ) {

                this.parentLI.querySelector('.frameCover').style.display = 'none';

            }

        };

        /*
            returns the full source code of the block's frame
        */
        this.getSource = function() {

            var source = "<html>";
            source += this.frameDocument.head.outerHTML;
            source += this.frameDocument.body.outerHTML;

            return source;

        };

        /*
            places a dragged/dropped block from the left sidebar onto the canvas
        */
        this.placeOnCanvas = function(ui) {

            //frame data, we'll need this before messing with the item's content HTML
            var frameData = {}, attr;

            if( ui.item.find('iframe').size() > 0 ) {//iframe thumbnail

                frameData.src = ui.item.find('iframe').attr('src');
                frameData.frames_original_url = ui.item.find('iframe').attr('src');
                frameData.frames_height = ui.item.height();

                //sandboxed block?
                attr = ui.item.find('iframe').attr('sandbox');

                if (typeof attr !== typeof undefined && attr !== false) {
                    this.sandbox = siteBuilderUtils.getRandomArbitrary(10000, 1000000000);
                    this.sandbox_loader = ui.item.find('iframe').attr('data-loaderfunction');
                }

            } else {//image thumbnail

                frameData.src = ui.item.find('img').attr('data-srcc');
                frameData.frames_original_url = ui.item.find('img').attr('data-srcc');
                frameData.frames_height = ui.item.find('img').attr('data-height');

                //sandboxed block?
                attr = ui.item.find('img').attr('data-sandbox');

                if (typeof attr !== typeof undefined && attr !== false) {
                    this.sandbox = siteBuilderUtils.getRandomArbitrary(10000, 1000000000);
                    this.sandbox_loader = ui.item.find('img').attr('data-loaderfunction');
                }

            }

            //create the new block object
            this.frameID = 0;
            this.parentLI = ui.item.get(0);
            this.parentLI.innerHTML = '';
            this.status = 'new';
            this.createFrame(frameData);
            this.parentLI.style.height = this.frameHeight+"px";
            this.createFrameCover();

            this.frame.addEventListener('load', this);

            //insert the created iframe
            ui.item.append($(this.frame));

            //add the block to the current page
            site.activePage.blocks.splice(ui.item.index(), 0, this);

            //custom event
            ui.item.find('iframe').trigger('canvasupdated');

            //dropped element, so we've got pending changes
            site.setPendingChanges(true);

        };


    }

    Block.prototype.handleEvent = function(event) {
        switch (event.type) {
            case "load":
                this.setFrameDocument();
                this.heightAdjustment();

                $(this.frameCover).removeClass('fresh', 500);

                break;

            case "click":

                var theBlock = this;

                //figure out what to do next

                if( event.target.classList.contains('deleteBlock') ) {//delete this block

                    $(builderUI.modalDeleteBlock).modal('show');

                    $(builderUI.modalDeleteBlock).off('click', '#deleteBlockConfirm').on('click', '#deleteBlockConfirm', function(){
                        theBlock.delete(event);
                        $(builderUI.modalDeleteBlock).modal('hide');
                    });

                } else if( event.target.classList.contains('resetBlock') ) {//reset the block

                    $(builderUI.modalResetBlock).modal('show');

                    $(builderUI.modalResetBlock).off('click', '#resetBlockConfirm').on('click', '#resetBlockConfirm', function(){
                        theBlock.reset();
                        $(builderUI.modalResetBlock).modal('hide');
                    });

                } else if( event.target.classList.contains('htmlBlock') ) {//source code editor

                    theBlock.source();

                } else if( event.target.classList.contains('editCancelButton') ) {//cancel source code editor

                    theBlock.cancelSourceBlock();

                } else if( event.target.classList.contains('editSaveButton') ) {//save source code

                    theBlock.saveSourceBlock();

                } else if( event.target.classList.contains('button_clearErrorDrawer') ) {//clear error drawer

                    theBlock.clearErrorDrawer();

                }

        }
    };


    /*
        Site object literal
    */
    /*jshint -W003 */
    var site = {

        pendingChanges: false,      //pending changes or no?
        pages: {},                  //array containing all pages, including the child frames, loaded from the server on page load
        is_admin: 0,                //0 for non-admin, 1 for admin
        data: {},                   //container for ajax loaded site data
        pagesToDelete: [],          //contains pages to be deleted

        sitePages: [],              //this is the only var containing the recent canvas contents

        sitePagesReadyForServer: {},     //contains the site data ready to be sent to the server

        activePage: {},             //holds a reference to the page currently open on the canvas

        pageTitle: document.getElementById('pageTitle'),//holds the page title of the current page on the canvas

        divCanvas: document.getElementById('pageList'),//DIV containing all pages on the canvas

        pagesMenu: document.getElementById('pages'), //UL containing the pages menu in the sidebar

        buttonNewPage: document.getElementById('addPage'),
        liNewPage: document.getElementById('newPageLI'),

        inputPageSettingsTitle: document.getElementById('pageData_title'),
        inputPageSettingsMetaDescription: document.getElementById('pageData_metaDescription'),
        inputPageSettingsMetaKeywords: document.getElementById('pageData_metaKeywords'),
        inputPageSettingsIncludes: document.getElementById('pageData_headerIncludes'),
        inputPageSettingsPageCss: document.getElementById('pageData_headerCss'),

        buttonSubmitPageSettings: document.getElementById('pageSettingsSubmittButton'),

        modalPageSettings: document.getElementById('pageSettingsModal'),

        buttonSave: document.getElementById('savePage'),

        messageStart: document.getElementById('start'),
        divFrameWrapper: document.getElementById('frameWrapper'),

        skeleton: document.getElementById('skeleton'),

		autoSaveTimer: {},

        init: function() {

            $.getJSON(appUI.siteUrl+"siteData", function(data){

                if( data.site !== undefined ) {
                    site.data = data.site;
                }
                if( data.pages !== undefined ) {
                    site.pages = data.pages;
                }

                site.is_admin = data.is_admin;

				if( $('#pageList').size() > 0 ) {
                	builderUI.populateCanvas();
				}

                //fire custom event
                $('body').trigger('siteDataLoaded');

            });

            $(this.buttonNewPage).on('click', site.newPage);
            $(this.modalPageSettings).on('show.bs.modal', site.loadPageSettings);
            $(this.buttonSubmitPageSettings).on('click', site.updatePageSettings);
            $(this.buttonSave).on('click', function(){site.save(true);});

            //auto save time
            this.autoSaveTimer = setTimeout(site.autoSave, bConfig.autoSaveTimeout);

        },

        autoSave: function(){

            if(site.pendingChanges) {
                site.save(false);
            }

			window.clearInterval(this.autoSaveTimer);
            this.autoSaveTimer = setTimeout(site.autoSave, bConfig.autoSaveTimeout);

        },

        setPendingChanges: function(value) {

            this.pendingChanges = value;

            if( value === true ) {

				//reset timer
				window.clearInterval(this.autoSaveTimer);
            	this.autoSaveTimer = setTimeout(site.autoSave, bConfig.autoSaveTimeout);

                $('#savePage .bLabel').text("Save now (!)");

                if( site.activePage.status !== 'new' ) {

                    site.activePage.status = 'changed';

                }

            } else {

                $('#savePage .bLabel').text("Nothing to save");

                site.updatePageStatus('');

            }

        },

        save: function(showConfirmModal) {

            //fire custom event
            $('body').trigger('beforeSave');

            //disable button
            $("a#savePage").addClass('disabled');

            //remove old alerts
            $('#errorModal .modal-body > *, #successModal .modal-body > *').each(function(){
                $(this).remove();
            });

            site.prepForSave(false);

            var serverData = {};
            serverData.pages = this.sitePagesReadyForServer;
            if( this.pagesToDelete.length > 0 ) {
                serverData.toDelete = this.pagesToDelete;
            }
            serverData.siteData = this.data;

            $.ajax({
                url: appUI.siteUrl+"site/save",
                type: "POST",
                dataType: "json",
                data: serverData,
            }).done(function(res){

                //enable button
                $("a#savePage").removeClass('disabled');

                if( res.responseCode === 0 ) {

                    if( showConfirmModal ) {

                        $('#errorModal .modal-body').append( $(res.responseHTML) );
                        $('#errorModal').modal('show');

                    }

                } else if( res.responseCode === 1 ) {

                    if( showConfirmModal ) {

                        $('#successModal .modal-body').append( $(res.responseHTML) );
                        $('#successModal').modal('show');

                    }


                    //no more pending changes
                    site.setPendingChanges(false);


                    //update revisions?
                    $('body').trigger('changePage');

                }
            });

        },

        /*
            preps the site data before sending it to the server
        */
        prepForSave: function(template) {

            this.sitePagesReadyForServer = {};

            if( template ) {//saving template, only the activePage is needed

                this.sitePagesReadyForServer[this.activePage.name] = this.activePage.prepForSave();

                this.activePage.fullPage();

            } else {//regular save

                //find the pages which need to be send to the server
                for( var i = 0; i < this.sitePages.length; i++ ) {

                    if( this.sitePages[i].status !== '' ) {

                        this.sitePagesReadyForServer[this.sitePages[i].name] = this.sitePages[i].prepForSave();

                    }

                }

            }

        },


        /*
            sets a page as the active one
        */
        setActive: function(page) {

            //reference to the active page
            this.activePage = page;

            //hide other pages
            for(var i in this.sitePages) {
                this.sitePages[i].parentUL.style.display = 'none';
            }

            //display active one
            this.activePage.parentUL.style.display = 'block';

        },


        /*
            de-active all page menu items
        */
        deActivateAll: function() {

            var pages = this.pagesMenu.querySelectorAll('li');

            for( var i = 0; i < pages.length; i++ ) {
                pages[i].classList.remove('active');
            }

        },


        /*
            adds a new page to the site
        */
        newPage: function() {

            site.deActivateAll();

            //create the new page instance

            var pageData = [];
            var temp = {
                pages_id: 0
            };
            pageData[0] = temp;

            var newPageName = 'page'+(site.sitePages.length+1);

            var newPage = new Page(newPageName, pageData, site.sitePages.length+1);

            newPage.status = 'new';

            newPage.selectPage();
            newPage.editPageName();

            newPage.isEmpty();

            site.setPendingChanges(true);

        },


        /*
            checks if the name of a page is allowed
        */
        checkPageName: function(pageName) {

            //make sure the name is unique
            for( var i in this.sitePages ) {

                if( this.sitePages[i].name === pageName && this.activePage !== this.sitePages[i] ) {
                    this.pageNameError = "The page name must be unique.";
                    return false;
                }

            }

            return true;

        },


        /*
            removes unallowed characters from the page name
        */
        prepPageName: function(pageName) {

            pageName = pageName.replace(' ', '');
            pageName = pageName.replace(/[?*!.|&#;$%@"<>()+,]/g, "");

            return pageName;

        },


        /*
            save page settings for the current page
        */
        updatePageSettings: function() {

            site.activePage.pageSettings.title = site.inputPageSettingsTitle.value;
            site.activePage.pageSettings.meta_description = site.inputPageSettingsMetaDescription.value;
            site.activePage.pageSettings.meta_keywords = site.inputPageSettingsMetaKeywords.value;
            site.activePage.pageSettings.header_includes = site.inputPageSettingsIncludes.value;
            site.activePage.pageSettings.page_css = site.inputPageSettingsPageCss.value;

            site.setPendingChanges(true);

            $(site.modalPageSettings).modal('hide');

        },


        /*
            update page statuses
        */
        updatePageStatus: function(status) {

            for( var i in this.sitePages ) {
                this.sitePages[i].status = status;
            }

        }

    };

    builderUI.init(); site.init();


    //**** EXPORTS
    module.exports.site = site;
    module.exports.builderUI = builderUI;

}());
},{"./config.js":5,"./ui.js":8,"./utils.js":9}],4:[function(require,module,exports){
(function () {
    "use strict";

    var siteBuilder = require('./builder.js');

    /*
        constructor function for Element
    */
    module.exports.Element = function (el) {

        this.element = el;
        this.sandbox = false;
        this.parentFrame = {};
        this.parentBlock = {};//reference to the parent block element

        //make current element active/open (being worked on)
        this.setOpen = function() {

            $(this.element).off('mouseenter mouseleave click');

            if( $(this.element).closest('body').width() !== $(this.element).width() ) {

                $(this.element).css({'outline': '3px dashed red', 'cursor': 'pointer'});

            } else {

                $(this.element).css({'outline': '3px dashed red', 'outline-offset':'-3px',  'cursor': 'pointer'});

            }

        };

        //sets up hover and click events, making the element active on the canvas
        this.activate = function() {

            var element = this;

            $(this.element).css({'outline': 'none', 'cursor': 'inherit'});

            $(this.element).on('mouseenter', function() {

                if( $(this).closest('body').width() !== $(this).width() ) {

                    $(this).css({'outline': '3px dashed red', 'cursor': 'pointer'});

                } else {

                    $(this).css({'outline': '3px dashed red', 'outline-offset': '-3px', 'cursor': 'pointer'});

                }

            }).on('mouseleave', function() {

                $(this).css({'outline': '', 'cursor': '', 'outline-offset': ''});

            }).on('click', function(e) {

                e.preventDefault();
                e.stopPropagation();

                element.clickHandler(this);

            });

        };

        this.deactivate = function() {

            $(this.element).off('mouseenter mouseleave click');
            $(this.element).css({'outline': 'none', 'cursor': 'inherit'});

        };

        //removes the elements outline
        this.removeOutline = function() {

            $(this.element).css({'outline': 'none', 'cursor': 'inherit'});

        };

        //sets the parent iframe
        this.setParentFrame = function() {

            var doc = this.element.ownerDocument;
            var w = doc.defaultView || doc.parentWindow;
            var frames = w.parent.document.getElementsByTagName('iframe');

            for (var i= frames.length; i-->0;) {

                var frame= frames[i];

                try {
                    var d= frame.contentDocument || frame.contentWindow.document;
                    if (d===doc)
                        this.parentFrame = frame;
                } catch(e) {}
            }

        };

        //sets this element's parent block reference
        this.setParentBlock = function() {

            //loop through all the blocks on the canvas
            for( var i = 0; i < siteBuilder.site.sitePages.length; i++ ) {

                for( var x = 0; x < siteBuilder.site.sitePages[i].blocks.length; x++ ) {

                    //if the block's frame matches this element's parent frame
                    if( siteBuilder.site.sitePages[i].blocks[x].frame === this.parentFrame ) {
                        //create a reference to that block and store it in this.parentBlock
                        this.parentBlock = siteBuilder.site.sitePages[i].blocks[x];
                    }

                }

            }

        };


        this.setParentFrame();

        /*
            is this block sandboxed?
        */

        if( this.parentFrame.getAttribute('data-sandbox') ) {
            this.sandbox = this.parentFrame.getAttribute('data-sandbox');
        }

    };

}());
},{"./builder.js":3}],5:[function(require,module,exports){
(function () {
	"use strict";

    module.exports.pageContainer = "#page";

    module.exports.editableItems = {
        'span.fa': ['color', 'font-size'],
        '.bg.bg1': ['background-color'],
        'nav a, a.edit': ['color', 'font-weight', 'text-transform'],
        'h1, h2, h3, h4, h5, p': ['color', 'font-size', 'background-color', 'font-family'],
        'a.btn, button.btn': ['border-radius', 'font-size', 'background-color'],

        'img': ['border-top-left-radius', 'border-top-right-radius', 'border-bottom-left-radius', 'border-bottom-right-radius', 'border-color', 'border-style', 'border-width'],
        'hr.dashed': ['border-color', 'border-width'],
        '.divider > span': ['color', 'font-size'],
        'hr.shadowDown': ['margin-top', 'margin-bottom'],
        '.footer a': ['color'],
        '.bg.bg1, .bg.bg2, .header10, .header11': ['background-image', 'background-color'],
        '.frameCover': []
    };

    module.exports.editableItemOptions = {
        'nav a : font-weight': ['400', '700'],
        'a.btn : border-radius': ['0px', '4px', '10px'],
        'img : border-style': ['none', 'dotted', 'dashed', 'solid'],
        'img : border-width': ['1px', '2px', '3px', '4px'],
        'h1, h2, h3, h4, h5, p : font-family': ['default', 'Lato', 'Helvetica', 'Arial', 'Times New Roman'],
        'h2 : font-family': ['default', 'Lato', 'Helvetica', 'Arial', 'Times New Roman'],
        'h3 : font-family': ['default', 'Lato', 'Helvetica', 'Arial', 'Times New Roman'],
        'p : font-family': ['default', 'Lato', 'Helvetica', 'Arial', 'Times New Roman']
    };

    module.exports.editableContent = ['.editContent', '.navbar a', 'button', 'a.btn', '.footer a:not(.fa)', '.tableWrapper', 'h1', 'h2'];

    module.exports.autoSaveTimeout = 60000;

    module.exports.sourceCodeEditSyntaxDelay = 10000;

}());
},{}],6:[function(require,module,exports){
(function (){
	"use strict";

    var bConfig = require('./config.js');
    var siteBuilder = require('./builder.js');
    var editor = require('./styleeditor.js').styleeditor;
    var appUI = require('./ui.js').appUI;

    var imageLibrary = {

        imageModal: document.getElementById('imageModal'),
        inputImageUpload: document.getElementById('imageFile'),
        buttonUploadImage: document.getElementById('uploadImageButton'),
        imageLibraryLinks: document.querySelectorAll('.images > .image .buttons .btn-primary, .images .imageWrap > a'),//used in the library, outside the builder UI
        myImages: document.getElementById('myImages'),//used in the image library, outside the builder UI

        init: function(){

            $(this.imageModal).on('show.bs.modal', this.imageLibrary);
            $(this.inputImageUpload).on('change', this.imageInputChange);
            $(this.buttonUploadImage).on('click', this.uploadImage);
            $(this.imageLibraryLinks).on('click', this.imageInModal);
            $(this.myImages).on('click', '.buttons .btn-danger', this.deleteImage);

        },


        /*
            image library modal
        */
        imageLibrary: function() {

            $('#imageModal').off('click', '.image button.useImage');

            $('#imageModal').on('click', '.image button.useImage', function(){

                //update live image
                $(editor.activeElement.element).attr('src', $(this).attr('data-url'));

                //update image URL field
                $('input#imageURL').val( $(this).attr('data-url') );

                //hide modal
                $('#imageModal').modal('hide');

                //height adjustment of the iframe heightAdjustment
				editor.activeElement.parentBlock.heightAdjustment();

                //we've got pending changes
                siteBuilder.site.setPendingChanges(true);

                $(this).unbind('click');

            });

        },


        /*
            image upload input chaneg event handler
        */
        imageInputChange: function() {

            if( $(this).val() === '' ) {
                //no file, disable submit button
                $('button#uploadImageButton').addClass('disabled');
            } else {
                //got a file, enable button
                $('button#uploadImageButton').removeClass('disabled');
            }

        },


        /*
            upload an image to the image library
        */
        uploadImage: function() {

            if( $('input#imageFile').val() !== '' ) {

                //remove old alerts
                $('#imageModal .modal-alerts > *').remove();

                //disable button
                $('button#uploadImageButton').addClass('disable');

                //show loader
                $('#imageModal .loader').fadeIn(500);

                var form = $('form#imageUploadForm');
                var formdata = false;

                if (window.FormData){
                    formdata = new FormData(form[0]);
                }

                var formAction = form.attr('action');

                $.ajax({
                    url : formAction,
                    data : formdata ? formdata : form.serialize(),
                    cache : false,
                    contentType : false,
                    processData : false,
                    dataType: "json",
                    type : 'POST'
                }).done(function(ret){

                    //enable button
                    $('button#uploadImageButton').addClass('disable');

                    //hide loader
                    $('#imageModal .loader').fadeOut(500);

                    if( ret.responseCode === 0 ) {//error

                        $('#imageModal .modal-alerts').append( $(ret.responseHTML) );

                    } else if( ret.responseCode === 1 ) {//success

                        //append my image
                        $('#myImagesTab > *').remove();
                        $('#myImagesTab').append( $(ret.myImages) );
                        $('#imageModal .modal-alerts').append( $(ret.responseHTML) );

                        setTimeout(function(){$('#imageModal .modal-alerts > *').fadeOut(500);}, 3000);

                    }

                });

            } else {

                alert('No image selected');

            }

        },


        /*
            displays image in modal
        */
        imageInModal: function(e) {

            e.preventDefault();

    		var theSrc = $(this).closest('.image').find('img').attr('src');

    		$('img#thePic').attr('src', theSrc);

    		$('#viewPic').modal('show');

        },


        /*
            deletes an image from the library
        */
        deleteImage: function(e) {

            e.preventDefault();

    		var toDel = $(this).closest('.image');
    		var theURL = $(this).attr('data-img');

    		$('#deleteImageModal').modal('show');

    		$('button#deleteImageButton').click(function(){

    			$(this).addClass('disabled');

    			var theButton = $(this);

    			$.ajax({
                    url: appUI.siteUrl+"assets/delImage",
    				data: {file: theURL},
    				type: 'post'
    			}).done(function(){

    				theButton.removeClass('disabled');

    				$('#deleteImageModal').modal('hide');

    				toDel.fadeOut(800, function(){

    					$(this).remove();

    				});

    			});


    		});

        }

    };

    imageLibrary.init();

}());
},{"./builder.js":3,"./config.js":5,"./styleeditor.js":7,"./ui.js":8}],7:[function(require,module,exports){
(function (){
	"use strict";

	var canvasElement = require('./canvasElement.js').Element;
	var bConfig = require('./config.js');
	var siteBuilder = require('./builder.js');

    var styleeditor = {

        radioStyle: document.getElementById('modeStyle'),
        labelStyleMode: document.getElementById('modeStyleLabel'),
        buttonSaveChanges: document.getElementById('saveStyling'),
        activeElement: {}, //holds the element currenty being edited
        allStyleItemsOnCanvas: [],
        _oldIcon: [],
        styleEditor: document.getElementById('styleEditor'),
        formStyle: document.getElementById('stylingForm'),
        buttonRemoveElement: document.getElementById('deleteElementConfirm'),
        buttonCloneElement: document.getElementById('cloneElementButton'),
        buttonResetElement: document.getElementById('resetStyleButton'),
        selectLinksInernal: document.getElementById('internalLinksDropdown'),
        selectLinksPages: document.getElementById('pageLinksDropdown'),
        videoInputYoutube: document.getElementById('youtubeID'),
        videoInputVimeo: document.getElementById('vimeoID'),
        inputCustomLink: document.getElementById('internalLinksCustom'),
        selectIcons: document.getElementById('icons'),
        buttonDetailsAppliedHide: document.getElementById('detailsAppliedMessageHide'),
        buttonCloseStyleEditor: document.querySelector('#styleEditor > a.close'),
        ulPageList: document.getElementById('pageList'),

        init: function() {

            //events
            $(this.radioStyle).on('click', this.activateStyleMode);
            $(this.buttonSaveChanges).on('click', this.updateStyling);
            $(this.formStyle).on('focus', 'input', this.animateStyleInputIn).on('blur', 'input', this.animateStyleInputOut);
            $(this.buttonRemoveElement).on('click', this.deleteElement);
            $(this.buttonCloneElement).on('click', this.cloneElement);
            $(this.buttonResetElement).on('click', this.resetElement);
            $(this.selectLinksInernal).on('change', this.resetSelectLinksInternal);
            $(this.selectLinksPages).on('change', this.resetSelectLinksPages);
            $(this.videoInputYoutube).on('focus', function(){ $(styleeditor.videoInputVimeo).val(''); });
            $(this.videoInputVimeo).on('focus', function(){ $(styleeditor.videoInputYoutube).val(''); });
            $(this.inputCustomLink).on('focus', this.resetSelectAllLinks);
            $(this.buttonDetailsAppliedHide).on('click', function(){$(this).parent().fadeOut(500);});
            $(this.buttonCloseStyleEditor).on('click', this.closeStyleEditor);
            $(document).on('modeContent modeBlocks', 'body', this.deActivateMode);

            //chosen font-awesome dropdown
            $(this.selectIcons).chosen({'search_contains': true});

            //check if formData is supported
            if (!window.FormData){
                this.hideFileUploads();
            }

            //show the style mode radio button
            $(this.labelStyleMode).show();

            //listen for the beforeSave event
            $('body').on('beforeSave', this.closeStyleEditor);

        },


        /*
            Activates style editor mode
        */
        activateStyleMode: function() {

            var i;

            //Element object extention
            canvasElement.prototype.clickHandler = function(el) {
                styleeditor.styleClick(el);
            };

            // Remove overlay span from portfolio
            for(i = 1; i <= $("ul#page1 li").length; i++){
                var id = "#ui-id-" + i;
                $(id).contents().find(".overlay").remove();
            }


            //trigger custom event
            $('body').trigger('modeDetails');

            //disable frameCovers
            for( i = 0; i < siteBuilder.site.sitePages.length; i++ ) {
                siteBuilder.site.sitePages[i].toggleFrameCovers('Off');
            }

            //create an object for every editable element on the canvas and setup it's events

            for( i = 0; i < siteBuilder.site.sitePages.length; i++ ) {

                for( var x = 0; x < siteBuilder.site.sitePages[i].blocks.length; x++ ) {

                    for( var key in bConfig.editableItems ) {

                        $(siteBuilder.site.sitePages[i].blocks[x].frame).contents().find( bConfig.pageContainer + ' '+ key ).each(function(){

                            var newElement = new canvasElement(this);

                            newElement.activate();

                            styleeditor.allStyleItemsOnCanvas.push( newElement );

                            $(this).attr('data-selector', key);

                        });

                    }

                }

            }

            /*$('#pageList ul li iframe').each(function(){

                for( var key in bConfig.editableItems ) {

                    $(this).contents().find( bConfig.pageContainer + ' '+ key ).each(function(){

                        var newElement = new canvasElement(this);

                        newElement.activate();

                        styleeditor.allStyleItemsOnCanvas.push( newElement );

                        $(this).attr('data-selector', key);

                    });

                }

            });*/

        },


        /*
            Event handler for when the style editor is envoked on an item
        */
        styleClick: function(el) {

            //if we have an active element, make it unactive
            if( Object.keys(this.activeElement).length !== 0) {
                this.activeElement.activate();
            }

            //set the active element
            var activeElement = new canvasElement(el);
            activeElement.setParentBlock();
            this.activeElement = activeElement;

            //unbind hover and click events and make this item active
            this.activeElement.setOpen();

            var theSelector = $(this.activeElement.element).attr('data-selector');

            $('#editingElement').text( theSelector );

            //activate first tab
            $('#detailTabs a:first').click();

            //hide all by default
            $('ul#detailTabs li:gt(0)').hide();

            //what are we dealing with?
            if( $(this.activeElement.element).prop('tagName') === 'A' || $(this.activeElement.element).parent().prop('tagName') === 'A' ) {

                this.editLink(this.activeElement.element);

            }

			if( $(this.activeElement.element).prop('tagName') === 'IMG' ){

                this.editImage(this.activeElement.element);

            }

			if( $(this.activeElement.element).attr('data-type') === 'video' ) {

                this.editVideo(this.activeElement.element);

            }

			if( $(this.activeElement.element).hasClass('fa') ) {

                this.editIcon(this.activeElement.element);

            }

            //load the attributes
            this.buildeStyleElements(theSelector);

            //open side panel
            this.toggleSidePanel('open');
        },


        /*
            dynamically generates the form fields for editing an elements style attributes
        */
        buildeStyleElements: function(theSelector) {

            //delete the old ones first
            $('#styleElements > *:not(#styleElTemplate)').each(function(){

                $(this).remove();

            });

            for( var x=0; x<bConfig.editableItems[theSelector].length; x++ ) {

                //create style elements
                var newStyleEl = $('#styleElTemplate').clone();
                newStyleEl.attr('id', '');
                newStyleEl.find('.control-label').text( bConfig.editableItems[theSelector][x]+":" );

                if( theSelector + " : " + bConfig.editableItems[theSelector][x] in bConfig.editableItemOptions) {//we've got a dropdown instead of open text input

                    newStyleEl.find('input').remove();

                    var newDropDown = $('<select class="form-control select select-primary btn-block select-sm"></select>');
                    newDropDown.attr('name', bConfig.editableItems[theSelector][x]);


                    for( var z=0; z<bConfig.editableItemOptions[ theSelector+" : "+bConfig.editableItems[theSelector][x] ].length; z++ ) {

                        var newOption = $('<option value="'+bConfig.editableItemOptions[theSelector+" : "+bConfig.editableItems[theSelector][x]][z]+'">'+bConfig.editableItemOptions[theSelector+" : "+bConfig.editableItems[theSelector][x]][z]+'</option>');


                        if( bConfig.editableItemOptions[theSelector+" : "+bConfig.editableItems[theSelector][x]][z] === $(styleeditor.activeElement.element).css( bConfig.editableItems[theSelector][x] ) ) {
                            //current value, marked as selected
                            newOption.attr('selected', 'true');

                        }

                        newDropDown.append( newOption );

                    }

                    newStyleEl.append( newDropDown );
                    newDropDown.select2();

                } else {

                    newStyleEl.find('input').val( $(styleeditor.activeElement.element).css( bConfig.editableItems[theSelector][x] ) ).attr('name', bConfig.editableItems[theSelector][x]);

                    if( bConfig.editableItems[theSelector][x] === 'background-image' ) {

                        newStyleEl.find('input').bind('focus', function(){

                            var theInput = $(this);

                            $('#imageModal').modal('show');
                            $('#imageModal .image button.useImage').unbind('click');
                            $('#imageModal').on('click', '.image button.useImage', function(){

                                $(styleeditor.activeElement.element).css('background-image',  'url("'+$(this).attr('data-url')+'")');

                                //update live image
                                theInput.val( 'url("'+$(this).attr('data-url')+'")' );

                                //hide modal
                                $('#imageModal').modal('hide');

                                //we've got pending changes
                                siteBuilder.site.setPendingChanges(true);

                            });

                        });

                    } else if( bConfig.editableItems[theSelector][x].indexOf("color") > -1 ) {

                        if( $(styleeditor.activeElement.element).css( bConfig.editableItems[theSelector][x] ) !== 'transparent' && $(styleeditor.activeElement.element).css( bConfig.editableItems[theSelector][x] ) !== 'none' && $(styleeditor.activeElement.element).css( bConfig.editableItems[theSelector][x] ) !== '' ) {

                            newStyleEl.val( $(styleeditor.activeElement.element).css( bConfig.editableItems[theSelector][x] ) );

                        }

                        newStyleEl.find('input').spectrum({
                            preferredFormat: "hex",
                            showPalette: true,
                            allowEmpty: true,
                            showInput: true,
                            palette: [
                                ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
                                ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
                                ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
                                ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
                                ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
                                ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
                                ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
                                ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
                            ]
                        });

                    }

                }

                newStyleEl.css('display', 'block');

                $('#styleElements').append( newStyleEl );

                $('#styleEditor form#stylingForm').height('auto');

            }

        },


        /*
            Applies updated styling to the canvas
        */
        updateStyling: function() {

            var elementID;

            $('#styleEditor #tab1 .form-group:not(#styleElTemplate) input, #styleEditor #tab1 .form-group:not(#styleElTemplate) select').each(function(){

				if( $(this).attr('name') !== undefined ) {

                	$(styleeditor.activeElement.element).css( $(this).attr('name'),  $(this).val());

				}

                /* SANDBOX */

                if( styleeditor.activeElement.sandbox ) {

                    elementID = $(styleeditor.activeElement.element).attr('id');

                    $('#'+styleeditor.activeElement.sandbox).contents().find('#'+elementID).css( $(this).attr('name'),  $(this).val() );

                }

                /* END SANDBOX */

            });

            //links
            if( $(styleeditor.activeElement.element).prop('tagName') === 'A' ) {

                //change the href prop?
                if( $('select#internalLinksDropdown').val() !== '#' ) {

                    $(styleeditor.activeElement.element).attr('href', $('select#internalLinksDropdown').val());

                } else if( $('select#pageLinksDropdown').val() !== '#' ) {

                    $(styleeditor.activeElement.element).attr('href', $('select#pageLinksDropdown').val() );

                } else if( $('input#internalLinksCustom').val() !== '' ) {

                    $(styleeditor.activeElement.element).attr('href', $('input#internalLinksCustom').val());

                }

                /* SANDBOX */

                if( styleeditor.activeElement.sandbox ) {

                    elementID = $(styleeditor.activeElement.element).attr('id');

                    if( $('select#internalLinksDropdown').val() !== '#' ) {

                        $('#'+styleeditor.activeElement.sandbox).contents().find('#'+elementID).attr('href', $('select#internalLinksDropdown').val());

                    } else if( $('select#pageLinksDropdown').val() !== '#' ) {

                        $('#'+styleeditor.activeElement.sandbox).contents().find('#'+elementID).attr('href', $('select#pageLinksDropdown').val() );

                    } else if( $('input#internalLinksCustom').val() !== '' ) {

                        $('#'+styleeditor.activeElement.sandbox).contents().find('#'+elementID).attr('href', $('input#internalLinksCustom').val());

                    }

                }

                /* END SANDBOX */

            }

            if( $(styleeditor.activeElement.element).parent().prop('tagName') === 'A' ) {

                //change the href prop?
				if( $('select#internalLinksDropdown').val() !== '#' ) {

                    $(styleeditor.activeElement.element).parent().attr('href', $('select#internalLinksDropdown').val());

                } else if( $('select#pageLinksDropdown').val() !== '#' ) {

                    $(styleeditor.activeElement.element).parent().attr('href', $('select#pageLinksDropdown').val() );

                } else if( $('input#internalLinksCustom').val() !== '' ) {

                    $(styleeditor.activeElement.element).parent().attr('href', $('input#internalLinksCustom').val());

                }

                /* SANDBOX */

                if( styleeditor.activeElement.sandbox ) {

                    elementID = $(styleeditor.activeElement.element).attr('id');

                    if( $('select#internalLinksDropdown').val() !== '#' ) {

                        $('#'+styleeditor.activeElement.sandbox).contents().find('#'+elementID).parent().attr('href', $('select#internalLinksDropdown').val());

                    } else if( $('select#pageLinksDropdown').val() !== '#' ) {

                        $('#'+styleeditor.activeElement.sandbox).contents().find('#'+elementID).parent().attr('href', $('select#pageLinksDropdown').val() );

                    } else if( $('input#internalLinksCustom').val() !== '' ) {

                        $('#'+styleeditor.activeElement.sandbox).contents().find('#'+elementID).parent().attr('href', $('input#internalLinksCustom').val());

                    }

                }

                /* END SANDBOX */

            }

            //icons
            if( $(styleeditor.activeElement.element).hasClass('fa') ) {

                //out with the old, in with the new :)
                //get icon class name, starting with fa-
                var get = $.grep(styleeditor.activeElement.element.className.split(" "), function(v, i){

                    return v.indexOf('fa-') === 0;

                }).join();

                //if the icons is being changed, save the old one so we can reset it if needed

                if( get !== $('select#icons').val() ) {

                    $(styleeditor.activeElement.element).uniqueId();
                    styleeditor._oldIcon[$(styleeditor.activeElement.element).attr('id')] = get;

                }

                $(styleeditor.activeElement.element).removeClass( get ).addClass( $('select#icons').val() );


                /* SANDBOX */

                if( styleeditor.activeElement.sandbox ) {

                    elementID = $(styleeditor.activeElement.element).attr('id');
                    $('#'+styleeditor.activeElement.sandbox).contents().find('#'+elementID).removeClass( get ).addClass( $('select#icons').val() );

                }

                /* END SANDBOX */

            }

            //video URL
            if( $(styleeditor.activeElement.element).attr('data-type') === 'video' ) {

                if( $('input#youtubeID').val() !== '' ) {

                    $(styleeditor.activeElement.element).prev().attr('src', "//www.youtube.com/embed/"+$('#video_Tab input#youtubeID').val());

                } else if( $('input#vimeoID').val() !== '' ) {

                    $(styleeditor.activeElement.element).prev().attr('src', "//player.vimeo.com/video/"+$('#video_Tab input#vimeoID').val()+"?title=0&amp;byline=0&amp;portrait=0");

                }

                /* SANDBOX */

                if( styleeditor.activeElement.sandbox ) {

                    elementID = $(styleeditor.activeElement.element).attr('id');

                    if( $('input#youtubeID').val() !== '' ) {

                        $('#'+styleeditor.activeElement.sandbox).contents().find('#'+elementID).prev().attr('src', "//www.youtube.com/embed/"+$('#video_Tab input#youtubeID').val());

                    } else if( $('input#vimeoID').val() !== '' ) {

                        $('#'+styleeditor.activeElement.sandbox).contents().find('#'+elementID).prev().attr('src', "//player.vimeo.com/video/"+$('#video_Tab input#vimeoID').val()+"?title=0&amp;byline=0&amp;portrait=0");

                    }

                }

                /* END SANDBOX */

            }

            $('#detailsAppliedMessage').fadeIn(600, function(){

                setTimeout(function(){ $('#detailsAppliedMessage').fadeOut(1000); }, 3000);

            });

            //adjust frame height
            styleeditor.activeElement.parentBlock.heightAdjustment();


            //we've got pending changes
            siteBuilder.site.setPendingChanges(true);

        },


        /*
            on focus, we'll make the input fields wider
        */
        animateStyleInputIn: function() {

            $(this).css('position', 'absolute');
            $(this).css('right', '0px');
            $(this).animate({'width': '100%'}, 500);
            $(this).focus(function(){
                this.select();
            });

        },


        /*
            on blur, we'll revert the input fields to their original size
        */
        animateStyleInputOut: function() {

            $(this).animate({'width': '42%'}, 500, function(){
                $(this).css('position', 'relative');
                $(this).css('right', 'auto');
            });

        },


        /*
            when the clicked element is an anchor tag (or has a parent anchor tag)
        */
        editLink: function(el) {

            $('a#link_Link').parent().show();

            var theHref;

            if( $(el).prop('tagName') === 'A' ) {

                theHref = $(el).attr('href');

            } else if( $(el).parent().prop('tagName') === 'A' ) {

                theHref = $(el).parent().attr('href');

            }

            var zIndex = 0;

            var pageLink = false;

            //the actual select

            $('select#internalLinksDropdown').prop('selectedIndex', 0);

            //set the correct item to "selected"
            $('select#internalLinksDropdown option').each(function(){

                if( $(this).attr('value') === theHref ) {

                    $(this).attr('selected', true);

                    zIndex = $(this).index();

                    pageLink = true;

                }

            });


            //the pretty dropdown
            $('.link_Tab .btn-group.select .dropdown-menu li').removeClass('selected');
            $('.link_Tab .btn-group.select .dropdown-menu li:eq('+zIndex+')').addClass('selected');
            $('.link_Tab .btn-group.select:eq(0) .filter-option').text( $('select#internalLinksDropdown option:selected').text() );
            $('.link_Tab .btn-group.select:eq(1) .filter-option').text( $('select#pageLinksDropdown option:selected').text() );

            if( pageLink === true ) {

                $('input#internalLinksCustom').val('');

            } else {

                if( $(el).prop('tagName') === 'A' ) {

                    if( $(el).attr('href')[0] !== '#' ) {
                        $('input#internalLinksCustom').val( $(el).attr('href') );
                    } else {
                        $('input#internalLinksCustom').val( '' );
                    }

                } else if( $(el).parent().prop('tagName') === 'A' ) {

                    if( $(el).parent().attr('href')[0] !== '#' ) {
                        $('input#internalLinksCustom').val( $(el).parent().attr('href') );
                    } else {
                        $('input#internalLinksCustom').val( '' );
                    }

                }

            }

            //list available blocks on this page, remove old ones first

            $('select#pageLinksDropdown option:not(:first)').remove();
            $('#pageList ul:visible iframe').each(function(){

                if( $(this).contents().find( bConfig.pageContainer + " > *:first" ).attr('id') !== undefined ) {

                    var newOption;

                    if( $(el).attr('href') === '#'+$(this).contents().find( bConfig.pageContainer + " > *:first" ).attr('id') ) {

                        newOption = '<option selected value=#'+$(this).contents().find( bConfig.pageContainer + " > *:first" ).attr('id')+'>#'+$(this).contents().find( bConfig.pageContainer + " > *:first" ).attr('id')+'</option>';

                    } else {

                        newOption = '<option value=#'+$(this).contents().find( bConfig.pageContainer + " > *:first" ).attr('id')+'>#'+$(this).contents().find( bConfig.pageContainer + " > *:first" ).attr('id')+'</option>';

                    }

                    $('select#pageLinksDropdown').append( newOption );

                }

            });

            //if there aren't any blocks to list, hide the dropdown

            if( $('select#pageLinksDropdown option').size() === 1 ) {

                $('select#pageLinksDropdown').next().hide();
                $('select#pageLinksDropdown').next().next().hide();

            } else {

                $('select#pageLinksDropdown').next().show();
                $('select#pageLinksDropdown').next().next().show();

            }

        },


        /*
            when the clicked element is an image
        */
        editImage: function(el) {

            $('a#img_Link').parent().show();

            //set the current SRC
            $('.imageFileTab').find('input#imageURL').val( $(el).attr('src') );

            //reset the file upload
            $('.imageFileTab').find('a.fileinput-exists').click();

        },


        /*
            when the clicked element is a video element
        */
        editVideo: function(el) {

            var matchResults;

            $('a#video_Link').parent().show();
            $('a#video_Link').click();

            //inject current video ID,check if we're dealing with Youtube or Vimeo

            if( $(el).prev().attr('src').indexOf("vimeo.com") > -1 ) {//vimeo

                matchResults = $(el).prev().attr('src').match(/player\.vimeo\.com\/video\/([0-9]*)/);

                $('#video_Tab input#vimeoID').val( matchResults[matchResults.length-1] );
                $('#video_Tab input#youtubeID').val('');

            } else {//youtube

                //temp = $(el).prev().attr('src').split('/');
                var regExp = /.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/;
                matchResults = $(el).prev().attr('src').match(regExp);

                $('#video_Tab input#youtubeID').val( matchResults[1] );
                $('#video_Tab input#vimeoID').val('');

            }

        },


        /*
            when the clicked element is an fa icon
        */
        editIcon: function() {

            $('a#icon_Link').parent().show();

            //get icon class name, starting with fa-
            var get = $.grep(this.activeElement.element.className.split(" "), function(v, i){

                return v.indexOf('fa-') === 0;

            }).join();

            $('select#icons option').each(function(){

                if( $(this).val() === get ) {

                    $(this).attr('selected', true);

                    $('#icons').trigger('chosen:updated');

                }

            });

        },


        /*
            delete selected element
        */
        deleteElement: function() {

            var toDel;

            //determine what to delete
            if( $(styleeditor.activeElement.element).prop('tagName') === 'A' ) {//ancor

                if( $(styleeditor.activeElement.element).parent().prop('tagName') ==='LI' ) {//clone the LI

                    toDel = $(styleeditor.activeElement.element).parent();

                } else {

                    toDel = $(styleeditor.activeElement.element);

                }

            } else if( $(styleeditor.activeElement.element).prop('tagName') === 'IMG' ) {//image

                if( $(styleeditor.activeElement.element).parent().prop('tagName') === 'A' ) {//clone the A

                    toDel = $(styleeditor.activeElement.element).parent();

                } else {

                    toDel = $(styleeditor.activeElement.element);

                }

            } else {//everything else

                toDel = $(styleeditor.activeElement.element);

            }


            toDel.fadeOut(500, function(){

                var randomEl = $(this).closest('body').find('*:first');

                toDel.remove();

                /* SANDBOX */

                var elementID = $(styleeditor.activeElement.element).attr('id');

                $('#'+styleeditor.activeElement.sandbox).contents().find('#'+elementID).remove();

                /* END SANDBOX */

                styleeditor.activeElement.parentBlock.heightAdjustment();

                //we've got pending changes
                siteBuilder.site.setPendingChanges(true);

            });

            $('#deleteElement').modal('hide');

            styleeditor.closeStyleEditor();

        },


        /*
            clones the selected element
        */
        cloneElement: function() {

            var theClone, theClone2, theOne, cloned, cloneParent, elementID;

            if( $(styleeditor.activeElement.element).parent().hasClass('propClone') ) {//clone the parent element

                theClone = $(styleeditor.activeElement.element).parent().clone();
                theClone.find( $(styleeditor.activeElement.element).prop('tagName') ).attr('style', '');

                theClone2 = $(styleeditor.activeElement.element).parent().clone();
                theClone2.find( $(styleeditor.activeElement.element).prop('tagName') ).attr('style', '');

                theOne = theClone.find( $(styleeditor.activeElement.element).prop('tagName') );
                cloned = $(styleeditor.activeElement.element).parent();

                cloneParent = $(styleeditor.activeElement.element).parent().parent();

            } else {//clone the element itself

                theClone = $(styleeditor.activeElement.element).clone();

                theClone.attr('style', '');

                /*if( styleeditor.activeElement.sandbox ) {
                    theClone.attr('id', '').uniqueId();
                }*/

                theClone2 = $(styleeditor.activeElement.element).clone();
                theClone2.attr('style', '');

                /*
                if( styleeditor.activeElement.sandbox ) {
                    theClone2.attr('id', theClone.attr('id'));
                }*/

                theOne = theClone;
                cloned = $(styleeditor.activeElement.element);

                cloneParent = $(styleeditor.activeElement.element).parent();

            }

            cloned.after( theClone );

            /* SANDBOX */

            if( styleeditor.activeElement.sandbox ) {

                elementID = $(styleeditor.activeElement.element).attr('id');
                $('#'+styleeditor.activeElement.sandbox).contents().find('#'+elementID).after( theClone2 );

            }

            /* END SANDBOX */

            //make sure the new element gets the proper events set on it
            var newElement = new canvasElement(theOne.get(0));
            newElement.activate();

            //possible height adjustments
            styleeditor.activeElement.parentBlock.heightAdjustment();

            //we've got pending changes
            siteBuilder.site.setPendingChanges(true);

        },


        /*
            resets the active element
        */
        resetElement: function() {

            if( $(styleeditor.activeElement.element).closest('body').width() !== $(styleeditor.activeElement.element).width() ) {

                $(styleeditor.activeElement.element).attr('style', '').css({'outline': '3px dashed red', 'cursor': 'pointer'});

            } else {

                $(styleeditor.activeElement.element).attr('style', '').css({'outline': '3px dashed red', 'outline-offset':'-3px', 'cursor': 'pointer'});

            }

            /* SANDBOX */

            if( styleeditor.activeElement.sandbox ) {

                var elementID = $(styleeditor.activeElement.element).attr('id');
                $('#'+styleeditor.activeElement.sandbox).contents().find('#'+elementID).attr('style', '');

            }

            /* END SANDBOX */

            $('#styleEditor form#stylingForm').height( $('#styleEditor form#stylingForm').height()+"px" );

            $('#styleEditor form#stylingForm .form-group:not(#styleElTemplate)').fadeOut(500, function(){

                $(this).remove();

            });


            //reset icon

            if( styleeditor._oldIcon[$(styleeditor.activeElement.element).attr('id')] !== null ) {

                var get = $.grep(styleeditor.activeElement.element.className.split(" "), function(v, i){

                    return v.indexOf('fa-') === 0;

                }).join();

                $(styleeditor.activeElement.element).removeClass( get ).addClass( styleeditor._oldIcon[$(styleeditor.activeElement.element).attr('id')] );

                $('select#icons option').each(function(){

                    if( $(this).val() === styleeditor._oldIcon[$(styleeditor.activeElement.element).attr('id')] ) {

                        $(this).attr('selected', true);
                        $('#icons').trigger('chosen:updated');

                    }

                });

            }

            setTimeout( function(){styleeditor.buildeStyleElements( $(styleeditor.activeElement.element).attr('data-selector') );}, 550);

            siteBuilder.site.setPendingChanges(true);

        },


        resetSelectLinksPages: function() {

            $('#internalLinksDropdown').select2('val', '#');

        },

        resetSelectLinksInternal: function() {

            $('#pageLinksDropdown').select2('val', '#');

        },

        resetSelectAllLinks: function() {

            $('#internalLinksDropdown').select2('val', '#');
            $('#pageLinksDropdown').select2('val', '#');
            this.select();

        },

        /*
            hides file upload forms
        */
        hideFileUploads: function() {

            $('form#imageUploadForm').hide();
            $('#imageModal #uploadTabLI').hide();

        },


        /*
            closes the style editor
        */
        closeStyleEditor: function(){

            if( Object.keys(styleeditor.activeElement).length > 0 ) {
                styleeditor.activeElement.removeOutline();
                styleeditor.activeElement.activate();
            }

            if( $('#styleEditor').css('left') === '0px' ) {

                styleeditor.toggleSidePanel('close');

            }

        },


        /*
            toggles the side panel
        */
        toggleSidePanel: function(val) {

            if( val === 'open' && $('#styleEditor').css('left') === '-300px' ) {
                $('#styleEditor').animate({'left': '0px'}, 250);
            } else if( val === 'close' && $('#styleEditor').css('left') === '0px' ) {
                $('#styleEditor').animate({'left': '-300px'}, 250);
            }

        },


        /*
            Event handler for when this mode gets deactivated
        */
        deActivateMode: function() {

            if( Object.keys( styleeditor.activeElement ).length > 0 ) {
                styleeditor.closeStyleEditor();
            }

            //deactivate all style items on the canvas
            for( var i =0; i < styleeditor.allStyleItemsOnCanvas.length; i++ ) {
                styleeditor.allStyleItemsOnCanvas[i].deactivate();
            }

            //Add overlay again
            // for(var i = 1; i <= $("ul#page1 li").length; i++){
            //     var id = "#ui-id-" + i;
            //     alert(id);
            //     // overlay = $('<span class="overlay"><span class="fui-eye"></span></span>');
            //     // $(id).contents().find('a.over').append( overlay );
            // }

        }

    };

    styleeditor.init();

    exports.styleeditor = styleeditor;

}());
},{"./builder.js":3,"./canvasElement.js":4,"./config.js":5}],8:[function(require,module,exports){
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
},{}],9:[function(require,module,exports){
(function () {
    "use strict";
    
    exports.getRandomArbitrary = function(min, max) {
        return Math.floor(Math.random() * (max - min) + min);
    };

    exports.getParameterByName = function (name, url) {

        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
        
    };
    
}());
},{}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJwdWJsaWMvc3JjL2pzL2ltYWdlcy5qcyIsInB1YmxpYy9zcmMvanMvbW9kdWxlcy9hY2NvdW50LmpzIiwicHVibGljL3NyYy9qcy9tb2R1bGVzL2J1aWxkZXIuanMiLCJwdWJsaWMvc3JjL2pzL21vZHVsZXMvY2FudmFzRWxlbWVudC5qcyIsInB1YmxpYy9zcmMvanMvbW9kdWxlcy9jb25maWcuanMiLCJwdWJsaWMvc3JjL2pzL21vZHVsZXMvaW1hZ2VMaWJyYXJ5LmpzIiwicHVibGljL3NyYy9qcy9tb2R1bGVzL3N0eWxlZWRpdG9yLmpzIiwicHVibGljL3NyYy9qcy9tb2R1bGVzL3VpLmpzIiwicHVibGljL3NyYy9qcy9tb2R1bGVzL3V0aWxzLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FDQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDVEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDdEpBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDbHhEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3JJQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDdENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDMU1BO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2poQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNoSEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uIGUodCxuLHIpe2Z1bmN0aW9uIHMobyx1KXtpZighbltvXSl7aWYoIXRbb10pe3ZhciBhPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7aWYoIXUmJmEpcmV0dXJuIGEobywhMCk7aWYoaSlyZXR1cm4gaShvLCEwKTt2YXIgZj1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK28rXCInXCIpO3Rocm93IGYuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixmfXZhciBsPW5bb109e2V4cG9ydHM6e319O3Rbb11bMF0uY2FsbChsLmV4cG9ydHMsZnVuY3Rpb24oZSl7dmFyIG49dFtvXVsxXVtlXTtyZXR1cm4gcyhuP246ZSl9LGwsbC5leHBvcnRzLGUsdCxuLHIpfXJldHVybiBuW29dLmV4cG9ydHN9dmFyIGk9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtmb3IodmFyIG89MDtvPHIubGVuZ3RoO28rKylzKHJbb10pO3JldHVybiBzfSkiLCIoZnVuY3Rpb24gKCkge1xuXHRcInVzZSBzdHJpY3RcIjtcblxuXHRyZXF1aXJlKCcuL21vZHVsZXMvdWknKTtcblx0cmVxdWlyZSgnLi9tb2R1bGVzL2J1aWxkZXInKTtcblx0cmVxdWlyZSgnLi9tb2R1bGVzL2NvbmZpZycpO1xuXHRyZXF1aXJlKCcuL21vZHVsZXMvaW1hZ2VMaWJyYXJ5Jyk7XG5cdHJlcXVpcmUoJy4vbW9kdWxlcy9hY2NvdW50Jyk7XG5cbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cblx0dmFyIGFwcFVJID0gcmVxdWlyZSgnLi91aS5qcycpLmFwcFVJO1xuXG5cdHZhciBhY2NvdW50ID0ge1xuXG4gICAgICAgIGJ1dHRvblVwZGF0ZUFjY291bnREZXRhaWxzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnYWNjb3VudERldGFpbHNTdWJtaXQnKSxcbiAgICAgICAgYnV0dG9uVXBkYXRlTG9naW5EZXRhaWxzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnYWNjb3VudExvZ2luU3VibWl0JyksXG5cbiAgICAgICAgaW5pdDogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICQodGhpcy5idXR0b25VcGRhdGVBY2NvdW50RGV0YWlscykub24oJ2NsaWNrJywgdGhpcy51cGRhdGVBY2NvdW50RGV0YWlscyk7XG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uVXBkYXRlTG9naW5EZXRhaWxzKS5vbignY2xpY2snLCB0aGlzLnVwZGF0ZUxvZ2luRGV0YWlscyk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICB1cGRhdGVzIGFjY291bnQgZGV0YWlsc1xuICAgICAgICAqL1xuICAgICAgICB1cGRhdGVBY2NvdW50RGV0YWlsczogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIC8vYWxsIGZpZWxkcyBmaWxsZWQgaW4/XG5cbiAgICAgICAgICAgIHZhciBhbGxHb29kID0gMTtcblxuICAgICAgICAgICAgaWYoICQoJyNhY2NvdW50X2RldGFpbHMgaW5wdXQjZmlyc3RuYW1lJykudmFsKCkgPT09ICcnICkge1xuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2RldGFpbHMgaW5wdXQjZmlyc3RuYW1lJykuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKS5hZGRDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICAgICAgYWxsR29vZCA9IDA7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2RldGFpbHMgaW5wdXQjZmlyc3RuYW1lJykuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKS5yZW1vdmVDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICAgICAgYWxsR29vZCA9IDE7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKCAkKCcjYWNjb3VudF9kZXRhaWxzIGlucHV0I2xhc3RuYW1lJykudmFsKCkgPT09ICcnICkge1xuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2RldGFpbHMgaW5wdXQjbGFzdG5hbWUnKS5jbG9zZXN0KCcuZm9ybS1ncm91cCcpLmFkZENsYXNzKCdoYXMtZXJyb3InKTtcbiAgICAgICAgICAgICAgICBhbGxHb29kID0gMDtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfZGV0YWlscyBpbnB1dCNsYXN0bmFtZScpLmNsb3Nlc3QoJy5mb3JtLWdyb3VwJykucmVtb3ZlQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgIGFsbEdvb2QgPSAxO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiggYWxsR29vZCA9PT0gMSApIHtcblxuICAgICAgICAgICAgICAgIHZhciB0aGVCdXR0b24gPSAkKHRoaXMpO1xuXG4gICAgICAgICAgICAgICAgLy9kaXNhYmxlIGJ1dHRvblxuICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgICAgICAgICAvL3Nob3cgbG9hZGVyXG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfZGV0YWlscyAubG9hZGVyJykuZmFkZUluKDUwMCk7XG5cbiAgICAgICAgICAgICAgICAvL3JlbW92ZSBhbGVydHNcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9kZXRhaWxzIC5hbGVydHMgPiAqJykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgICAgICAgICB1cmw6IGFwcFVJLnNpdGVVcmwrXCJ1c2VyL3VhY2NvdW50XCIsXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6ICdwb3N0JyxcbiAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcbiAgICAgICAgICAgICAgICAgICAgZGF0YTogJCgnI2FjY291bnRfZGV0YWlscycpLnNlcmlhbGl6ZSgpXG4gICAgICAgICAgICAgICAgfSkuZG9uZShmdW5jdGlvbihyZXQpe1xuXG4gICAgICAgICAgICAgICAgICAgIC8vZW5hYmxlIGJ1dHRvblxuICAgICAgICAgICAgICAgICAgICB0aGVCdXR0b24ucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9oaWRlIGxvYWRlclxuICAgICAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9kZXRhaWxzIC5sb2FkZXInKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2RldGFpbHMgLmFsZXJ0cycpLmFwcGVuZCggJChyZXQucmVzcG9uc2VIVE1MKSApO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCByZXQucmVzcG9uc2VDb2RlID09PSAxICkgey8vc3VjY2Vzc1xuICAgICAgICAgICAgICAgICAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2FjY291bnRfZGV0YWlscyAuYWxlcnRzID4gKicpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbiAoKSB7ICQodGhpcykucmVtb3ZlKCk7IH0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSwgMzAwMCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgdXBkYXRlcyBhY2NvdW50IGxvZ2luIGRldGFpbHNcbiAgICAgICAgKi9cbiAgICAgICAgdXBkYXRlTG9naW5EZXRhaWxzOiBmdW5jdGlvbigpIHtcblxuXHRcdFx0Y29uc29sZS5sb2coYXBwVUkpO1xuXG4gICAgICAgICAgICB2YXIgYWxsR29vZCA9IDE7XG5cbiAgICAgICAgICAgIGlmKCAkKCcjYWNjb3VudF9sb2dpbiBpbnB1dCNlbWFpbCcpLnZhbCgpID09PSAnJyApIHtcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9sb2dpbiBpbnB1dCNlbWFpbCcpLmNsb3Nlc3QoJy5mb3JtLWdyb3VwJykuYWRkQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgIGFsbEdvb2QgPSAwO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9sb2dpbiBpbnB1dCNlbWFpbCcpLmNsb3Nlc3QoJy5mb3JtLWdyb3VwJykucmVtb3ZlQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgIGFsbEdvb2QgPSAxO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiggJCgnI2FjY291bnRfbG9naW4gaW5wdXQjcGFzc3dvcmQnKS52YWwoKSA9PT0gJycgKSB7XG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfbG9naW4gaW5wdXQjcGFzc3dvcmQnKS5jbG9zZXN0KCcuZm9ybS1ncm91cCcpLmFkZENsYXNzKCdoYXMtZXJyb3InKTtcbiAgICAgICAgICAgICAgICBhbGxHb29kID0gMDtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfbG9naW4gaW5wdXQjcGFzc3dvcmQnKS5jbG9zZXN0KCcuZm9ybS1ncm91cCcpLnJlbW92ZUNsYXNzKCdoYXMtZXJyb3InKTtcbiAgICAgICAgICAgICAgICBhbGxHb29kID0gMTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYoIGFsbEdvb2QgPT09IDEgKSB7XG5cbiAgICAgICAgICAgICAgICB2YXIgdGhlQnV0dG9uID0gJCh0aGlzKTtcblxuICAgICAgICAgICAgICAgIC8vZGlzYWJsZSBidXR0b25cbiAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICAgICAgLy9zaG93IGxvYWRlclxuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2xvZ2luIC5sb2FkZXInKS5mYWRlSW4oNTAwKTtcblxuICAgICAgICAgICAgICAgIC8vcmVtb3ZlIGFsZXJ0c1xuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2xvZ2luIC5hbGVydHMgPiAqJykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgICAgICAgICB1cmw6IGFwcFVJLnNpdGVVcmwrXCJ1c2VyL3Vsb2dpblwiLFxuICAgICAgICAgICAgICAgICAgICB0eXBlOiAncG9zdCcsXG4gICAgICAgICAgICAgICAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICAgICAgICAgICAgICAgIGRhdGE6ICQoJyNhY2NvdW50X2xvZ2luJykuc2VyaWFsaXplKClcbiAgICAgICAgICAgICAgICB9KS5kb25lKGZ1bmN0aW9uKHJldCl7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9lbmFibGUgYnV0dG9uXG4gICAgICAgICAgICAgICAgICAgIHRoZUJ1dHRvbi5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2hpZGUgbG9hZGVyXG4gICAgICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2xvZ2luIC5sb2FkZXInKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2xvZ2luIC5hbGVydHMnKS5hcHBlbmQoICQocmV0LnJlc3BvbnNlSFRNTCkgKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMSApIHsvL3N1Y2Nlc3NcbiAgICAgICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2xvZ2luIC5hbGVydHMgPiAqJykuZmFkZU91dCg1MDAsIGZ1bmN0aW9uICgpIHsgJCh0aGlzKS5yZW1vdmUoKTsgfSk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9LCAzMDAwKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9XG5cbiAgICB9O1xuXG4gICAgYWNjb3VudC5pbml0KCk7XG5cbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cbiAgICB2YXIgc2l0ZUJ1aWxkZXJVdGlscyA9IHJlcXVpcmUoJy4vdXRpbHMuanMnKTtcbiAgICB2YXIgYkNvbmZpZyA9IHJlcXVpcmUoJy4vY29uZmlnLmpzJyk7XG4gICAgdmFyIGFwcFVJID0gcmVxdWlyZSgnLi91aS5qcycpLmFwcFVJO1xuXG5cblx0IC8qXG4gICAgICAgIEJhc2ljIEJ1aWxkZXIgVUkgaW5pdGlhbGlzYXRpb25cbiAgICAqL1xuICAgIHZhciBidWlsZGVyVUkgPSB7XG5cbiAgICAgICAgYWxsQmxvY2tzOiB7fSwgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy9ob2xkcyBhbGwgYmxvY2tzIGxvYWRlZCBmcm9tIHRoZSBzZXJ2ZXJcbiAgICAgICAgbWVudVdyYXBwZXI6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtZW51JyksXG4gICAgICAgIHByaW1hcnlTaWRlTWVudVdyYXBwZXI6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtYWluJyksXG4gICAgICAgIGJ1dHRvbkJhY2s6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdiYWNrQnV0dG9uJyksXG4gICAgICAgIGJ1dHRvbkJhY2tDb25maXJtOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbGVhdmVQYWdlQnV0dG9uJyksXG5cbiAgICAgICAgc2l0ZUJ1aWxkZXJNb2RlczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NpdGVCdWlsZGVyTW9kZXMnKSxcbiAgICAgICAgYWNlRWRpdG9yczoge30sXG4gICAgICAgIGZyYW1lQ29udGVudHM6ICcnLCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy9ob2xkcyBmcmFtZSBjb250ZW50c1xuICAgICAgICB0ZW1wbGF0ZUlEOiAwLCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vaG9sZHMgdGhlIHRlbXBsYXRlIElEIGZvciBhIHBhZ2UgKD8/PylcbiAgICAgICAgcmFkaW9CbG9ja01vZGU6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtb2RlQmxvY2snKSxcblxuICAgICAgICBtb2RhbERlbGV0ZUJsb2NrOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZGVsZXRlQmxvY2snKSxcbiAgICAgICAgbW9kYWxSZXNldEJsb2NrOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncmVzZXRCbG9jaycpLFxuICAgICAgICBtb2RhbERlbGV0ZVBhZ2U6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdkZWxldGVQYWdlJyksXG4gICAgICAgIGJ1dHRvbkRlbGV0ZVBhZ2VDb25maXJtOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZGVsZXRlUGFnZUNvbmZpcm0nKSxcblxuICAgICAgICBkcm9wZG93blBhZ2VMaW5rczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2ludGVybmFsTGlua3NEcm9wZG93bicpLFxuXG4gICAgICAgIHBhZ2VJblVybDogbnVsbCxcblxuICAgICAgICB0ZW1wRnJhbWU6IHt9LFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgIC8vbG9hZCBibG9ja3NcbiAgICAgICAgICAgICQuZ2V0SlNPTihhcHBVSS5iYXNlVXJsKycvZWxlbWVudHMuanNvbj92PTEyMzQ1Njc4JywgZnVuY3Rpb24oZGF0YSl7IGJ1aWxkZXJVSS5hbGxCbG9ja3MgPSBkYXRhOyBidWlsZGVyVUkuaW1wbGVtZW50QmxvY2tzKCk7IH0pO1xuXG4gICAgICAgICAgICAvL3NpdGViYXIgaG92ZXIgYW5pbWF0aW9uIGFjdGlvblxuICAgICAgICAgICAgJCh0aGlzLm1lbnVXcmFwcGVyKS5vbignbW91c2VlbnRlcicsIGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMpLnN0b3AoKS5hbmltYXRlKHsnbGVmdCc6ICcwcHgnfSwgNTAwKTtcblxuICAgICAgICAgICAgfSkub24oJ21vdXNlbGVhdmUnLCBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5zdG9wKCkuYW5pbWF0ZSh7J2xlZnQnOiAnLTE5MHB4J30sIDUwMCk7XG5cbiAgICAgICAgICAgICAgICAkKCcjbWVudSAjbWFpbiBhJykucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xuICAgICAgICAgICAgICAgICQoJy5tZW51IC5zZWNvbmQnKS5zdG9wKCkuYW5pbWF0ZSh7XG4gICAgICAgICAgICAgICAgICAgIHdpZHRoOiAwXG4gICAgICAgICAgICAgICAgfSwgNTAwLCBmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICAgICAkKCcjbWVudSAjc2Vjb25kJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy9wcmV2ZW50IGNsaWNrIGV2ZW50IG9uIGFuY29ycyBpbiB0aGUgYmxvY2sgc2VjdGlvbiBvZiB0aGUgc2lkZWJhclxuICAgICAgICAgICAgJCh0aGlzLnByaW1hcnlTaWRlTWVudVdyYXBwZXIpLm9uKCdjbGljaycsICdhOm5vdCguYWN0aW9uQnV0dG9ucyknLCBmdW5jdGlvbihlKXtlLnByZXZlbnREZWZhdWx0KCk7fSk7XG5cbiAgICAgICAgICAgICQodGhpcy5idXR0b25CYWNrKS5vbignY2xpY2snLCB0aGlzLmJhY2tCdXR0b24pO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvbkJhY2tDb25maXJtKS5vbignY2xpY2snLCB0aGlzLmJhY2tCdXR0b25Db25maXJtKTtcblxuICAgICAgICAgICAgLy9ub3RpZnkgdGhlIHVzZXIgb2YgcGVuZGluZyBjaG5hZ2VzIHdoZW4gY2xpY2tpbmcgdGhlIGJhY2sgYnV0dG9uXG4gICAgICAgICAgICAkKHdpbmRvdykuYmluZCgnYmVmb3JldW5sb2FkJywgZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICBpZiggc2l0ZS5wZW5kaW5nQ2hhbmdlcyA9PT0gdHJ1ZSApIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuICdZb3VyIHNpdGUgY29udGFpbnMgY2hhbmdlZCB3aGljaCBoYXZlblxcJ3QgYmVlbiBzYXZlZCB5ZXQuIEFyZSB5b3Ugc3VyZSB5b3Ugd2FudCB0byBsZWF2ZT8nO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvL21ha2Ugc3VyZSB3ZSBzdGFydCBpbiBibG9jayBtb2RlXG4gICAgICAgICAgICAkKHRoaXMucmFkaW9CbG9ja01vZGUpLnJhZGlvY2hlY2soJ2NoZWNrJykub24oJ2NsaWNrJywgdGhpcy5hY3RpdmF0ZUJsb2NrTW9kZSk7XG5cbiAgICAgICAgICAgIC8vVVJMIHBhcmFtZXRlcnNcbiAgICAgICAgICAgIGJ1aWxkZXJVSS5wYWdlSW5VcmwgPSBzaXRlQnVpbGRlclV0aWxzLmdldFBhcmFtZXRlckJ5TmFtZSgncCcpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgYnVpbGRzIHRoZSBibG9ja3MgaW50byB0aGUgc2l0ZSBiYXJcbiAgICAgICAgKi9cbiAgICAgICAgaW1wbGVtZW50QmxvY2tzOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIG5ld0l0ZW0sIGxvYWRlckZ1bmN0aW9uO1xuXG4gICAgICAgICAgICBmb3IoIHZhciBrZXkgaW4gdGhpcy5hbGxCbG9ja3MuZWxlbWVudHMgKSB7XG5cbiAgICAgICAgICAgICAgICB2YXIgbmljZUtleSA9IGtleS50b0xvd2VyQ2FzZSgpLnJlcGxhY2UoXCIgXCIsIFwiX1wiKTtcblxuICAgICAgICAgICAgICAgICQoJzxsaT48YSBocmVmPVwiXCIgaWQ9XCInK25pY2VLZXkrJ1wiPicra2V5Kyc8L2E+PC9saT4nKS5hcHBlbmRUbygnI21lbnUgI21haW4gdWwjZWxlbWVudENhdHMnKTtcblxuICAgICAgICAgICAgICAgIGZvciggdmFyIHggPSAwOyB4IDwgdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XS5sZW5ndGg7IHgrKyApIHtcblxuICAgICAgICAgICAgICAgICAgICBpZiggdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS50aHVtYm5haWwgPT09IG51bGwgKSB7Ly93ZSdsbCBuZWVkIGFuIGlmcmFtZVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL2J1aWxkIHVzIHNvbWUgaWZyYW1lcyFcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoIHRoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0uc2FuZGJveCApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKCB0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLmxvYWRlckZ1bmN0aW9uICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsb2FkZXJGdW5jdGlvbiA9ICdkYXRhLWxvYWRlcmZ1bmN0aW9uPVwiJyt0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLmxvYWRlckZ1bmN0aW9uKydcIic7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbmV3SXRlbSA9ICQoJzxsaSBjbGFzcz1cImVsZW1lbnQgJytuaWNlS2V5KydcIj48aWZyYW1lIHNyYz1cIicrYXBwVUkuYmFzZVVybCt0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLnVybCsnXCIgc2Nyb2xsaW5nPVwibm9cIiBzYW5kYm94PVwiYWxsb3ctc2FtZS1vcmlnaW5cIj48L2lmcmFtZT48L2xpPicpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbmV3SXRlbSA9ICQoJzxsaSBjbGFzcz1cImVsZW1lbnQgJytuaWNlS2V5KydcIj48aWZyYW1lIHNyYz1cImFib3V0OmJsYW5rXCIgc2Nyb2xsaW5nPVwibm9cIj48L2lmcmFtZT48L2xpPicpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0l0ZW0uZmluZCgnaWZyYW1lJykudW5pcXVlSWQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0l0ZW0uZmluZCgnaWZyYW1lJykuYXR0cignc3JjJywgYXBwVUkuYmFzZVVybCt0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLnVybCk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHsvL3dlJ3ZlIGdvdCBhIHRodW1ibmFpbFxuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiggdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS5zYW5kYm94ICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoIHRoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0ubG9hZGVyRnVuY3Rpb24gKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxvYWRlckZ1bmN0aW9uID0gJ2RhdGEtbG9hZGVyZnVuY3Rpb249XCInK3RoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0ubG9hZGVyRnVuY3Rpb24rJ1wiJztcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBuZXdJdGVtID0gJCgnPGxpIGNsYXNzPVwiZWxlbWVudCAnK25pY2VLZXkrJ1wiPjxpbWcgc3JjPVwiJythcHBVSS5iYXNlVXJsK3RoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0udGh1bWJuYWlsKydcIiBkYXRhLXNyY2M9XCInK2FwcFVJLmJhc2VVcmwrdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS51cmwrJ1wiIGRhdGEtaGVpZ2h0PVwiJyt0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLmhlaWdodCsnXCIgZGF0YS1zYW5kYm94PVwiXCIgJytsb2FkZXJGdW5jdGlvbisnPjwvbGk+Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBuZXdJdGVtID0gJCgnPGxpIGNsYXNzPVwiZWxlbWVudCAnK25pY2VLZXkrJ1wiPjxpbWcgc3JjPVwiJythcHBVSS5iYXNlVXJsK3RoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0udGh1bWJuYWlsKydcIiBkYXRhLXNyY2M9XCInK2FwcFVJLmJhc2VVcmwrdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS51cmwrJ1wiIGRhdGEtaGVpZ2h0PVwiJyt0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLmhlaWdodCsnXCI+PC9saT4nKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgbmV3SXRlbS5hcHBlbmRUbygnI21lbnUgI3NlY29uZCB1bCNlbGVtZW50cycpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vem9vbWVyIHdvcmtzXG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIHRoZUhlaWdodDtcblxuICAgICAgICAgICAgICAgICAgICBpZiggdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS5oZWlnaHQgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRoZUhlaWdodCA9IHRoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0uaGVpZ2h0KjAuMjU7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdGhlSGVpZ2h0ID0gJ2F1dG8nO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICBuZXdJdGVtLmZpbmQoJ2lmcmFtZScpLnpvb21lcih7XG4gICAgICAgICAgICAgICAgICAgICAgICB6b29tOiAwLjI1LFxuICAgICAgICAgICAgICAgICAgICAgICAgd2lkdGg6IDI3MCxcbiAgICAgICAgICAgICAgICAgICAgICAgIGhlaWdodDogdGhlSGVpZ2h0LFxuICAgICAgICAgICAgICAgICAgICAgICAgbWVzc2FnZTogXCJEcmFnJkRyb3AgTWUhXCJcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9kcmFnZ2FibGVzXG4gICAgICAgICAgICBidWlsZGVyVUkubWFrZURyYWdnYWJsZSgpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgZXZlbnQgaGFuZGxlciBmb3Igd2hlbiB0aGUgYmFjayBsaW5rIGlzIGNsaWNrZWRcbiAgICAgICAgKi9cbiAgICAgICAgYmFja0J1dHRvbjogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIGlmKCBzaXRlLnBlbmRpbmdDaGFuZ2VzID09PSB0cnVlICkge1xuICAgICAgICAgICAgICAgICQoJyNiYWNrTW9kYWwnKS5tb2RhbCgnc2hvdycpO1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGJ1dHRvbiBmb3IgY29uZmlybWluZyBsZWF2aW5nIHRoZSBwYWdlXG4gICAgICAgICovXG4gICAgICAgIGJhY2tCdXR0b25Db25maXJtOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgc2l0ZS5wZW5kaW5nQ2hhbmdlcyA9IGZhbHNlOy8vcHJldmVudCB0aGUgSlMgYWxlcnQgYWZ0ZXIgY29uZmlybWluZyB1c2VyIHdhbnRzIHRvIGxlYXZlXG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBhY3RpdmF0ZXMgYmxvY2sgbW9kZVxuICAgICAgICAqL1xuICAgICAgICBhY3RpdmF0ZUJsb2NrTW9kZTogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHNpdGUuYWN0aXZlUGFnZS50b2dnbGVGcmFtZUNvdmVycygnT24nKTtcblxuICAgICAgICAgICAgLy90cmlnZ2VyIGN1c3RvbSBldmVudFxuICAgICAgICAgICAgJCgnYm9keScpLnRyaWdnZXIoJ21vZGVCbG9ja3MnKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIG1ha2VzIHRoZSBibG9ja3MgYW5kIHRlbXBsYXRlcyBpbiB0aGUgc2lkZWJhciBkcmFnZ2FibGUgb250byB0aGUgY2FudmFzXG4gICAgICAgICovXG4gICAgICAgIG1ha2VEcmFnZ2FibGU6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKCcjZWxlbWVudHMgbGksICN0ZW1wbGF0ZXMgbGknKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMpLmRyYWdnYWJsZSh7XG4gICAgICAgICAgICAgICAgICAgIGhlbHBlcjogZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gJCgnPGRpdiBzdHlsZT1cImhlaWdodDogMTAwcHg7IHdpZHRoOiAzMDBweDsgYmFja2dyb3VuZDogI0Y5RkFGQTsgYm94LXNoYWRvdzogNXB4IDVweCAxcHggcmdiYSgwLDAsMCwwLjEpOyB0ZXh0LWFsaWduOiBjZW50ZXI7IGxpbmUtaGVpZ2h0OiAxMDBweDsgZm9udC1zaXplOiAyOHB4OyBjb2xvcjogIzE2QTA4NVwiPjxzcGFuIGNsYXNzPVwiZnVpLWxpc3RcIj48L3NwYW4+PC9kaXY+Jyk7XG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHJldmVydDogJ2ludmFsaWQnLFxuICAgICAgICAgICAgICAgICAgICBhcHBlbmRUbzogJ2JvZHknLFxuICAgICAgICAgICAgICAgICAgICBjb25uZWN0VG9Tb3J0YWJsZTogJyNwYWdlTGlzdCA+IHVsJyxcbiAgICAgICAgICAgICAgICAgICAgc3RhcnQ6IGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vc3dpdGNoIHRvIGJsb2NrIG1vZGVcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJ2lucHV0OnJhZGlvW25hbWU9bW9kZV0nKS5wYXJlbnQoKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJ2lucHV0OnJhZGlvW25hbWU9bW9kZV0jbW9kZUJsb2NrJykucmFkaW9jaGVjaygnY2hlY2snKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICQoJyNlbGVtZW50cyBsaSBhJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS51bmJpbmQoJ2NsaWNrJykuYmluZCgnY2xpY2snLCBmdW5jdGlvbihlKXtcbiAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIEltcGxlbWVudHMgdGhlIHNpdGUgb24gdGhlIGNhbnZhcywgY2FsbGVkIGZyb20gdGhlIFNpdGUgb2JqZWN0IHdoZW4gdGhlIHNpdGVEYXRhIGhhcyBjb21wbGV0ZWQgbG9hZGluZ1xuICAgICAgICAqL1xuICAgICAgICBwb3B1bGF0ZUNhbnZhczogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciBpO1xuXG4gICAgICAgICAgICAvL2lmIHdlIGhhdmUgYW55IGJsb2NrcyBhdCBhbGwsIGFjdGl2YXRlIHRoZSBtb2Rlc1xuICAgICAgICAgICAgaWYoIE9iamVjdC5rZXlzKHNpdGUucGFnZXMpLmxlbmd0aCA+IDAgKSB7XG4gICAgICAgICAgICAgICAgdmFyIG1vZGVzID0gYnVpbGRlclVJLnNpdGVCdWlsZGVyTW9kZXMucXVlcnlTZWxlY3RvckFsbCgnaW5wdXRbdHlwZT1cInJhZGlvXCJdJyk7XG4gICAgICAgICAgICAgICAgZm9yKCBpID0gMDsgaSA8IG1vZGVzLmxlbmd0aDsgaSsrICkge1xuICAgICAgICAgICAgICAgICAgICBtb2Rlc1tpXS5yZW1vdmVBdHRyaWJ1dGUoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgY291bnRlciA9IDE7XG5cbiAgICAgICAgICAgIC8vbG9vcCB0aHJvdWdoIHRoZSBwYWdlc1xuXG4gICAgICAgICAgICBmb3IoIGkgaW4gc2l0ZS5wYWdlcyApIHtcblxuICAgICAgICAgICAgICAgIHZhciBuZXdQYWdlID0gbmV3IFBhZ2UoaSwgc2l0ZS5wYWdlc1tpXSwgY291bnRlcik7XG5cbiAgICAgICAgICAgICAgICBjb3VudGVyKys7XG5cbiAgICAgICAgICAgICAgICAvL3NldCB0aGlzIHBhZ2UgYXMgYWN0aXZlP1xuICAgICAgICAgICAgICAgIGlmKCBidWlsZGVyVUkucGFnZUluVXJsID09PSBpICkge1xuICAgICAgICAgICAgICAgICAgICBuZXdQYWdlLnNlbGVjdFBhZ2UoKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9hY3RpdmF0ZSB0aGUgZmlyc3QgcGFnZVxuICAgICAgICAgICAgaWYoc2l0ZS5zaXRlUGFnZXMubGVuZ3RoID4gMCAmJiBidWlsZGVyVUkucGFnZUluVXJsID09PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgc2l0ZS5zaXRlUGFnZXNbMF0uc2VsZWN0UGFnZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cblxuICAgIH07XG5cblxuICAgIC8qXG4gICAgICAgIFBhZ2UgY29uc3RydWN0b3JcbiAgICAqL1xuICAgIGZ1bmN0aW9uIFBhZ2UgKHBhZ2VOYW1lLCBwYWdlLCBjb3VudGVyKSB7XG5cbiAgICAgICAgdGhpcy5uYW1lID0gcGFnZU5hbWUgfHwgXCJcIjtcbiAgICAgICAgdGhpcy5wYWdlSUQgPSBwYWdlLnBhZ2VzX2lkIHx8IDA7XG4gICAgICAgIHRoaXMuYmxvY2tzID0gW107XG4gICAgICAgIHRoaXMucGFyZW50VUwgPSB7fTsgLy9wYXJlbnQgVUwgb24gdGhlIGNhbnZhc1xuICAgICAgICB0aGlzLnN0YXR1cyA9ICcnOy8vJycsICduZXcnIG9yICdjaGFuZ2VkJ1xuICAgICAgICB0aGlzLnNjcmlwdHMgPSBbXTsvL3RyYWNrcyBzY3JpcHQgVVJMcyB1c2VkIG9uIHRoaXMgcGFnZVxuXG4gICAgICAgIHRoaXMucGFnZVNldHRpbmdzID0ge1xuICAgICAgICAgICAgdGl0bGU6IHBhZ2UucGFnZXNfdGl0bGUgfHwgJycsXG4gICAgICAgICAgICBtZXRhX2Rlc2NyaXB0aW9uOiBwYWdlLm1ldGFfZGVzY3JpcHRpb24gfHwgJycsXG4gICAgICAgICAgICBtZXRhX2tleXdvcmRzOiBwYWdlLm1ldGFfa2V5d29yZHMgfHwgJycsXG4gICAgICAgICAgICBoZWFkZXJfaW5jbHVkZXM6IHBhZ2UuaGVhZGVyX2luY2x1ZGVzIHx8ICcnLFxuICAgICAgICAgICAgcGFnZV9jc3M6IHBhZ2UucGFnZV9jc3MgfHwgJydcbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLnBhZ2VNZW51VGVtcGxhdGUgPSAnPGEgaHJlZj1cIlwiIGNsYXNzPVwibWVudUl0ZW1MaW5rXCI+cGFnZTwvYT48c3BhbiBjbGFzcz1cInBhZ2VCdXR0b25zXCI+PGEgaHJlZj1cIlwiIGNsYXNzPVwiZmlsZUVkaXQgZnVpLW5ld1wiPjwvYT48YSBocmVmPVwiXCIgY2xhc3M9XCJmaWxlRGVsIGZ1aS1jcm9zc1wiPjxhIGNsYXNzPVwiYnRuIGJ0bi14cyBidG4tcHJpbWFyeSBidG4tZW1ib3NzZWQgZmlsZVNhdmUgZnVpLWNoZWNrXCIgaHJlZj1cIiNcIj48L2E+PC9zcGFuPjwvYT48L3NwYW4+JztcblxuICAgICAgICB0aGlzLm1lbnVJdGVtID0ge307Ly9yZWZlcmVuY2UgdG8gdGhlIHBhZ2VzIG1lbnUgaXRlbSBmb3IgdGhpcyBwYWdlIGluc3RhbmNlXG4gICAgICAgIHRoaXMubGlua3NEcm9wZG93bkl0ZW0gPSB7fTsvL3JlZmVyZW5jZSB0byB0aGUgbGlua3MgZHJvcGRvd24gaXRlbSBmb3IgdGhpcyBwYWdlIGluc3RhbmNlXG5cbiAgICAgICAgdGhpcy5wYXJlbnRVTCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ1VMJyk7XG4gICAgICAgIHRoaXMucGFyZW50VUwuc2V0QXR0cmlidXRlKCdpZCcsIFwicGFnZVwiK2NvdW50ZXIpO1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBtYWtlcyB0aGUgY2xpY2tlZCBwYWdlIGFjdGl2ZVxuICAgICAgICAqL1xuICAgICAgICB0aGlzLnNlbGVjdFBhZ2UgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9jb25zb2xlLmxvZygnc2VsZWN0OicpO1xuICAgICAgICAgICAgLy9jb25zb2xlLmxvZyh0aGlzLnBhZ2VTZXR0aW5ncyk7XG5cbiAgICAgICAgICAgIC8vbWFyayB0aGUgbWVudSBpdGVtIGFzIGFjdGl2ZVxuICAgICAgICAgICAgc2l0ZS5kZUFjdGl2YXRlQWxsKCk7XG4gICAgICAgICAgICAkKHRoaXMubWVudUl0ZW0pLmFkZENsYXNzKCdhY3RpdmUnKTtcblxuICAgICAgICAgICAgLy9sZXQgU2l0ZSBrbm93IHdoaWNoIHBhZ2UgaXMgY3VycmVudGx5IGFjdGl2ZVxuICAgICAgICAgICAgc2l0ZS5zZXRBY3RpdmUodGhpcyk7XG5cbiAgICAgICAgICAgIC8vZGlzcGxheSB0aGUgbmFtZSBvZiB0aGUgYWN0aXZlIHBhZ2Ugb24gdGhlIGNhbnZhc1xuICAgICAgICAgICAgc2l0ZS5wYWdlVGl0bGUuaW5uZXJIVE1MID0gdGhpcy5uYW1lO1xuXG4gICAgICAgICAgICAvL2xvYWQgdGhlIHBhZ2Ugc2V0dGluZ3MgaW50byB0aGUgcGFnZSBzZXR0aW5ncyBtb2RhbFxuICAgICAgICAgICAgc2l0ZS5pbnB1dFBhZ2VTZXR0aW5nc1RpdGxlLnZhbHVlID0gdGhpcy5wYWdlU2V0dGluZ3MudGl0bGU7XG4gICAgICAgICAgICBzaXRlLmlucHV0UGFnZVNldHRpbmdzTWV0YURlc2NyaXB0aW9uLnZhbHVlID0gdGhpcy5wYWdlU2V0dGluZ3MubWV0YV9kZXNjcmlwdGlvbjtcbiAgICAgICAgICAgIHNpdGUuaW5wdXRQYWdlU2V0dGluZ3NNZXRhS2V5d29yZHMudmFsdWUgPSB0aGlzLnBhZ2VTZXR0aW5ncy5tZXRhX2tleXdvcmRzO1xuICAgICAgICAgICAgc2l0ZS5pbnB1dFBhZ2VTZXR0aW5nc0luY2x1ZGVzLnZhbHVlID0gdGhpcy5wYWdlU2V0dGluZ3MuaGVhZGVyX2luY2x1ZGVzO1xuICAgICAgICAgICAgc2l0ZS5pbnB1dFBhZ2VTZXR0aW5nc1BhZ2VDc3MudmFsdWUgPSB0aGlzLnBhZ2VTZXR0aW5ncy5wYWdlX2NzcztcblxuICAgICAgICAgICAgLy90cmlnZ2VyIGN1c3RvbSBldmVudFxuICAgICAgICAgICAgJCgnYm9keScpLnRyaWdnZXIoJ2NoYW5nZVBhZ2UnKTtcblxuICAgICAgICAgICAgLy9yZXNldCB0aGUgaGVpZ2h0cyBmb3IgdGhlIGJsb2NrcyBvbiB0aGUgY3VycmVudCBwYWdlXG4gICAgICAgICAgICBmb3IoIHZhciBpIGluIHRoaXMuYmxvY2tzICkge1xuXG4gICAgICAgICAgICAgICAgaWYoIE9iamVjdC5rZXlzKHRoaXMuYmxvY2tzW2ldLmZyYW1lRG9jdW1lbnQpLmxlbmd0aCA+IDAgKXtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5ibG9ja3NbaV0uaGVpZ2h0QWRqdXN0bWVudCgpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL3Nob3cgdGhlIGVtcHR5IG1lc3NhZ2U/XG4gICAgICAgICAgICB0aGlzLmlzRW1wdHkoKTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBjaGFuZ2VkIHRoZSBsb2NhdGlvbi9vcmRlciBvZiBhIGJsb2NrIHdpdGhpbiBhIHBhZ2VcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5zZXRQb3NpdGlvbiA9IGZ1bmN0aW9uKGZyYW1lSUQsIG5ld1Bvcykge1xuXG4gICAgICAgICAgICAvL3dlJ2xsIG5lZWQgdGhlIGJsb2NrIG9iamVjdCBjb25uZWN0ZWQgdG8gaWZyYW1lIHdpdGggZnJhbWVJRFxuXG4gICAgICAgICAgICBmb3IodmFyIGkgaW4gdGhpcy5ibG9ja3MpIHtcblxuICAgICAgICAgICAgICAgIGlmKCB0aGlzLmJsb2Nrc1tpXS5mcmFtZS5nZXRBdHRyaWJ1dGUoJ2lkJykgPT09IGZyYW1lSUQgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9jaGFuZ2UgdGhlIHBvc2l0aW9uIG9mIHRoaXMgYmxvY2sgaW4gdGhlIGJsb2NrcyBhcnJheVxuICAgICAgICAgICAgICAgICAgICB0aGlzLmJsb2Nrcy5zcGxpY2UobmV3UG9zLCAwLCB0aGlzLmJsb2Nrcy5zcGxpY2UoaSwgMSlbMF0pO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgZGVsZXRlIGJsb2NrIGZyb20gYmxvY2tzIGFycmF5XG4gICAgICAgICovXG4gICAgICAgIHRoaXMuZGVsZXRlQmxvY2sgPSBmdW5jdGlvbihibG9jaykge1xuXG4gICAgICAgICAgICAvL3JlbW92ZSBmcm9tIGJsb2NrcyBhcnJheVxuICAgICAgICAgICAgZm9yKCB2YXIgaSBpbiB0aGlzLmJsb2NrcyApIHtcbiAgICAgICAgICAgICAgICBpZiggdGhpcy5ibG9ja3NbaV0gPT09IGJsb2NrICkge1xuICAgICAgICAgICAgICAgICAgICAvL2ZvdW5kIGl0LCByZW1vdmUgZnJvbSBibG9ja3MgYXJyYXlcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5ibG9ja3Muc3BsaWNlKGksIDEpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICB0b2dnbGVzIGFsbCBibG9jayBmcmFtZUNvdmVycyBvbiB0aGlzIHBhZ2VcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy50b2dnbGVGcmFtZUNvdmVycyA9IGZ1bmN0aW9uKG9uT3JPZmYpIHtcblxuICAgICAgICAgICAgZm9yKCB2YXIgaSBpbiB0aGlzLmJsb2NrcyApIHtcblxuICAgICAgICAgICAgICAgIHRoaXMuYmxvY2tzW2ldLnRvZ2dsZUNvdmVyKG9uT3JPZmYpO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgc2V0dXAgZm9yIGVkaXRpbmcgYSBwYWdlIG5hbWVcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5lZGl0UGFnZU5hbWUgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgaWYoICF0aGlzLm1lbnVJdGVtLmNsYXNzTGlzdC5jb250YWlucygnZWRpdCcpICkge1xuXG4gICAgICAgICAgICAgICAgLy9oaWRlIHRoZSBsaW5rXG4gICAgICAgICAgICAgICAgdGhpcy5tZW51SXRlbS5xdWVyeVNlbGVjdG9yKCdhLm1lbnVJdGVtTGluaycpLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG5cbiAgICAgICAgICAgICAgICAvL2luc2VydCB0aGUgaW5wdXQgZmllbGRcbiAgICAgICAgICAgICAgICB2YXIgbmV3SW5wdXQgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdpbnB1dCcpO1xuICAgICAgICAgICAgICAgIG5ld0lucHV0LnR5cGUgPSAndGV4dCc7XG4gICAgICAgICAgICAgICAgbmV3SW5wdXQuc2V0QXR0cmlidXRlKCduYW1lJywgJ3BhZ2UnKTtcbiAgICAgICAgICAgICAgICBuZXdJbnB1dC5zZXRBdHRyaWJ1dGUoJ3ZhbHVlJywgdGhpcy5uYW1lKTtcbiAgICAgICAgICAgICAgICB0aGlzLm1lbnVJdGVtLmluc2VydEJlZm9yZShuZXdJbnB1dCwgdGhpcy5tZW51SXRlbS5maXJzdENoaWxkKTtcblxuICAgICAgICAgICAgICAgIG5ld0lucHV0LmZvY3VzKCk7XG5cbiAgICAgICAgICAgICAgICB2YXIgdG1wU3RyID0gbmV3SW5wdXQuZ2V0QXR0cmlidXRlKCd2YWx1ZScpO1xuICAgICAgICAgICAgICAgIG5ld0lucHV0LnNldEF0dHJpYnV0ZSgndmFsdWUnLCAnJyk7XG4gICAgICAgICAgICAgICAgbmV3SW5wdXQuc2V0QXR0cmlidXRlKCd2YWx1ZScsIHRtcFN0cik7XG5cbiAgICAgICAgICAgICAgICB0aGlzLm1lbnVJdGVtLmNsYXNzTGlzdC5hZGQoJ2VkaXQnKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIFVwZGF0ZXMgdGhpcyBwYWdlJ3MgbmFtZSAoZXZlbnQgaGFuZGxlciBmb3IgdGhlIHNhdmUgYnV0dG9uKVxuICAgICAgICAqL1xuICAgICAgICB0aGlzLnVwZGF0ZVBhZ2VOYW1lRXZlbnQgPSBmdW5jdGlvbihlbCkge1xuXG4gICAgICAgICAgICBpZiggdGhpcy5tZW51SXRlbS5jbGFzc0xpc3QuY29udGFpbnMoJ2VkaXQnKSApIHtcblxuICAgICAgICAgICAgICAgIC8vZWwgaXMgdGhlIGNsaWNrZWQgYnV0dG9uLCB3ZSdsbCBuZWVkIGFjY2VzcyB0byB0aGUgaW5wdXRcbiAgICAgICAgICAgICAgICB2YXIgdGhlSW5wdXQgPSB0aGlzLm1lbnVJdGVtLnF1ZXJ5U2VsZWN0b3IoJ2lucHV0W25hbWU9XCJwYWdlXCJdJyk7XG5cbiAgICAgICAgICAgICAgICAvL21ha2Ugc3VyZSB0aGUgcGFnZSdzIG5hbWUgaXMgT0tcbiAgICAgICAgICAgICAgICBpZiggc2l0ZS5jaGVja1BhZ2VOYW1lKHRoZUlucHV0LnZhbHVlKSApIHtcblxuICAgICAgICAgICAgICAgICAgICB0aGlzLm5hbWUgPSBzaXRlLnByZXBQYWdlTmFtZSggdGhlSW5wdXQudmFsdWUgKTtcblxuICAgICAgICAgICAgICAgICAgICB0aGlzLm1lbnVJdGVtLnF1ZXJ5U2VsZWN0b3IoJ2lucHV0W25hbWU9XCJwYWdlXCJdJykucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMubWVudUl0ZW0ucXVlcnlTZWxlY3RvcignYS5tZW51SXRlbUxpbmsnKS5pbm5lckhUTUwgPSB0aGlzLm5hbWU7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMubWVudUl0ZW0ucXVlcnlTZWxlY3RvcignYS5tZW51SXRlbUxpbmsnKS5zdHlsZS5kaXNwbGF5ID0gJ2Jsb2NrJztcblxuICAgICAgICAgICAgICAgICAgICB0aGlzLm1lbnVJdGVtLmNsYXNzTGlzdC5yZW1vdmUoJ2VkaXQnKTtcblxuICAgICAgICAgICAgICAgICAgICAvL3VwZGF0ZSB0aGUgbGlua3MgZHJvcGRvd24gaXRlbVxuICAgICAgICAgICAgICAgICAgICB0aGlzLmxpbmtzRHJvcGRvd25JdGVtLnRleHQgPSB0aGlzLm5hbWU7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMubGlua3NEcm9wZG93bkl0ZW0uc2V0QXR0cmlidXRlKCd2YWx1ZScsIHRoaXMubmFtZStcIi5odG1sXCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vdXBkYXRlIHRoZSBwYWdlIG5hbWUgb24gdGhlIGNhbnZhc1xuICAgICAgICAgICAgICAgICAgICBzaXRlLnBhZ2VUaXRsZS5pbm5lckhUTUwgPSB0aGlzLm5hbWU7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9jaGFuZ2VkIHBhZ2UgdGl0bGUsIHdlJ3ZlIGdvdCBwZW5kaW5nIGNoYW5nZXNcbiAgICAgICAgICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAgICAgYWxlcnQoc2l0ZS5wYWdlTmFtZUVycm9yKTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGRlbGV0ZXMgdGhpcyBlbnRpcmUgcGFnZVxuICAgICAgICAqL1xuICAgICAgICB0aGlzLmRlbGV0ZSA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2RlbGV0ZSBmcm9tIHRoZSBTaXRlXG4gICAgICAgICAgICBmb3IoIHZhciBpIGluIHNpdGUuc2l0ZVBhZ2VzICkge1xuXG4gICAgICAgICAgICAgICAgaWYoIHNpdGUuc2l0ZVBhZ2VzW2ldID09PSB0aGlzICkgey8vZ290IGEgbWF0Y2ghXG5cbiAgICAgICAgICAgICAgICAgICAgLy9kZWxldGUgZnJvbSBzaXRlLnNpdGVQYWdlc1xuICAgICAgICAgICAgICAgICAgICBzaXRlLnNpdGVQYWdlcy5zcGxpY2UoaSwgMSk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9kZWxldGUgZnJvbSBjYW52YXNcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5wYXJlbnRVTC5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2FkZCB0byBkZWxldGVkIHBhZ2VzXG4gICAgICAgICAgICAgICAgICAgIHNpdGUucGFnZXNUb0RlbGV0ZS5wdXNoKHRoaXMubmFtZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9kZWxldGUgdGhlIHBhZ2UncyBtZW51IGl0ZW1cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5tZW51SXRlbS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2RlbGV0IHRoZSBwYWdlcyBsaW5rIGRyb3Bkb3duIGl0ZW1cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5saW5rc0Ryb3Bkb3duSXRlbS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2FjdGl2YXRlIHRoZSBmaXJzdCBwYWdlXG4gICAgICAgICAgICAgICAgICAgIHNpdGUuc2l0ZVBhZ2VzWzBdLnNlbGVjdFBhZ2UoKTtcblxuICAgICAgICAgICAgICAgICAgICAvL3BhZ2Ugd2FzIGRlbGV0ZWQsIHNvIHdlJ3ZlIGdvdCBwZW5kaW5nIGNoYW5nZXNcbiAgICAgICAgICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGNoZWNrcyBpZiB0aGUgcGFnZSBpcyBlbXB0eSwgaWYgc28gc2hvdyB0aGUgJ2VtcHR5JyBtZXNzYWdlXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuaXNFbXB0eSA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICBpZiggdGhpcy5ibG9ja3MubGVuZ3RoID09PSAwICkge1xuXG4gICAgICAgICAgICAgICAgc2l0ZS5tZXNzYWdlU3RhcnQuc3R5bGUuZGlzcGxheSA9ICdibG9jayc7XG4gICAgICAgICAgICAgICAgc2l0ZS5kaXZGcmFtZVdyYXBwZXIuY2xhc3NMaXN0LmFkZCgnZW1wdHknKTtcblxuICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgIHNpdGUubWVzc2FnZVN0YXJ0LnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgICAgICAgICAgc2l0ZS5kaXZGcmFtZVdyYXBwZXIuY2xhc3NMaXN0LnJlbW92ZSgnZW1wdHknKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHByZXBzL3N0cmlwcyB0aGlzIHBhZ2UgZGF0YSBmb3IgYSBwZW5kaW5nIGFqYXggcmVxdWVzdFxuICAgICAgICAqL1xuICAgICAgICB0aGlzLnByZXBGb3JTYXZlID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciBwYWdlID0ge307XG5cbiAgICAgICAgICAgIHBhZ2UubmFtZSA9IHRoaXMubmFtZTtcbiAgICAgICAgICAgIHBhZ2UucGFnZVNldHRpbmdzID0gdGhpcy5wYWdlU2V0dGluZ3M7XG4gICAgICAgICAgICBwYWdlLnN0YXR1cyA9IHRoaXMuc3RhdHVzO1xuICAgICAgICAgICAgcGFnZS5ibG9ja3MgPSBbXTtcblxuICAgICAgICAgICAgLy9wcm9jZXNzIHRoZSBibG9ja3NcblxuICAgICAgICAgICAgZm9yKCB2YXIgeCA9IDA7IHggPCB0aGlzLmJsb2Nrcy5sZW5ndGg7IHgrKyApIHtcblxuICAgICAgICAgICAgICAgIHZhciBibG9jayA9IHt9O1xuXG4gICAgICAgICAgICAgICAgaWYoIHRoaXMuYmxvY2tzW3hdLnNhbmRib3ggKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgYmxvY2suZnJhbWVDb250ZW50ID0gXCI8aHRtbD5cIiskKCcjc2FuZGJveGVzICMnK3RoaXMuYmxvY2tzW3hdLnNhbmRib3gpLmNvbnRlbnRzKCkuZmluZCgnaHRtbCcpLmh0bWwoKStcIjwvaHRtbD5cIjtcbiAgICAgICAgICAgICAgICAgICAgYmxvY2suc2FuZGJveCA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIGJsb2NrLmxvYWRlckZ1bmN0aW9uID0gdGhpcy5ibG9ja3NbeF0uc2FuZGJveF9sb2FkZXI7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgICAgIGJsb2NrLmZyYW1lQ29udGVudCA9IHRoaXMuYmxvY2tzW3hdLmdldFNvdXJjZSgpO1xuICAgICAgICAgICAgICAgICAgICBibG9jay5zYW5kYm94ID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgIGJsb2NrLmxvYWRlckZ1bmN0aW9uID0gJyc7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBibG9jay5mcmFtZUhlaWdodCA9IHRoaXMuYmxvY2tzW3hdLmZyYW1lSGVpZ2h0O1xuICAgICAgICAgICAgICAgIGJsb2NrLm9yaWdpbmFsVXJsID0gdGhpcy5ibG9ja3NbeF0ub3JpZ2luYWxVcmw7XG5cbiAgICAgICAgICAgICAgICBwYWdlLmJsb2Nrcy5wdXNoKGJsb2NrKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gcGFnZTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBnZW5lcmF0ZXMgdGhlIGZ1bGwgcGFnZSwgdXNpbmcgc2tlbGV0b24uaHRtbFxuICAgICAgICAqL1xuICAgICAgICB0aGlzLmZ1bGxQYWdlID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciBwYWdlID0gdGhpczsvL3JlZmVyZW5jZSB0byBzZWxmIGZvciBsYXRlclxuICAgICAgICAgICAgcGFnZS5zY3JpcHRzID0gW107Ly9tYWtlIHN1cmUgaXQncyBlbXB0eSwgd2UnbGwgc3RvcmUgc2NyaXB0IFVSTHMgaW4gdGhlcmUgbGF0ZXJcblxuICAgICAgICAgICAgdmFyIG5ld0RvY01haW5QYXJlbnQgPSAkKCdpZnJhbWUjc2tlbGV0b24nKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciApO1xuXG4gICAgICAgICAgICAvL2VtcHR5IG91dCB0aGUgc2tlbGV0b24gZmlyc3RcbiAgICAgICAgICAgICQoJ2lmcmFtZSNza2VsZXRvbicpLmNvbnRlbnRzKCkuZmluZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyICkuaHRtbCgnJyk7XG5cbiAgICAgICAgICAgIC8vcmVtb3ZlIG9sZCBzY3JpcHQgdGFnc1xuICAgICAgICAgICAgJCgnaWZyYW1lI3NrZWxldG9uJykuY29udGVudHMoKS5maW5kKCAnc2NyaXB0JyApLmVhY2goZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHZhciB0aGVDb250ZW50cztcblxuICAgICAgICAgICAgZm9yKCB2YXIgaSBpbiB0aGlzLmJsb2NrcyApIHtcblxuICAgICAgICAgICAgICAgIC8vZ3JhYiB0aGUgYmxvY2sgY29udGVudFxuICAgICAgICAgICAgICAgIGlmICh0aGlzLmJsb2Nrc1tpXS5zYW5kYm94ICE9PSBmYWxzZSkge1xuXG4gICAgICAgICAgICAgICAgICAgIHRoZUNvbnRlbnRzID0gJCgnI3NhbmRib3hlcyAjJyt0aGlzLmJsb2Nrc1tpXS5zYW5kYm94KS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciApLmNsb25lKCk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgICAgIHRoZUNvbnRlbnRzID0gJCh0aGlzLmJsb2Nrc1tpXS5mcmFtZURvY3VtZW50LmJvZHkpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciApLmNsb25lKCk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvL3JlbW92ZSB2aWRlbyBmcmFtZUNvdmVyc1xuICAgICAgICAgICAgICAgIHRoZUNvbnRlbnRzLmZpbmQoJy5mcmFtZUNvdmVyJykuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvL3JlbW92ZSB2aWRlbyBmcmFtZVdyYXBwZXJzXG4gICAgICAgICAgICAgICAgdGhlQ29udGVudHMuZmluZCgnLnZpZGVvV3JhcHBlcicpLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgY250ID0gJCh0aGlzKS5jb250ZW50cygpO1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnJlcGxhY2VXaXRoKGNudCk7XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vcmVtb3ZlIHN0eWxlIGxlZnRvdmVycyBmcm9tIHRoZSBzdHlsZSBlZGl0b3JcbiAgICAgICAgICAgICAgICBmb3IoIHZhciBrZXkgaW4gYkNvbmZpZy5lZGl0YWJsZUl0ZW1zICkge1xuXG4gICAgICAgICAgICAgICAgICAgIHRoZUNvbnRlbnRzLmZpbmQoIGtleSApLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmVBdHRyKCdkYXRhLXNlbGVjdG9yJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuY3NzKCdvdXRsaW5lJywgJycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5jc3MoJ291dGxpbmUtb2Zmc2V0JywgJycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5jc3MoJ2N1cnNvcicsICcnKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoICQodGhpcykuYXR0cignc3R5bGUnKSA9PT0gJycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZUF0dHIoJ3N0eWxlJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vcmVtb3ZlIHN0eWxlIGxlZnRvdmVycyBmcm9tIHRoZSBjb250ZW50IGVkaXRvclxuICAgICAgICAgICAgICAgIGZvciAoIHZhciB4ID0gMDsgeCA8IGJDb25maWcuZWRpdGFibGVDb250ZW50Lmxlbmd0aDsgKyt4KSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdGhlQ29udGVudHMuZmluZCggYkNvbmZpZy5lZGl0YWJsZUNvbnRlbnRbeF0gKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQXR0cignZGF0YS1zZWxlY3RvcicpO1xuXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLy9hcHBlbmQgdG8gRE9NIGluIHRoZSBza2VsZXRvblxuICAgICAgICAgICAgICAgIG5ld0RvY01haW5QYXJlbnQuYXBwZW5kKCAkKHRoZUNvbnRlbnRzLmh0bWwoKSkgKTtcblxuICAgICAgICAgICAgICAgIC8vZG8gd2UgbmVlZCB0byBpbmplY3QgYW55IHNjcmlwdHM/XG4gICAgICAgICAgICAgICAgdmFyIHNjcmlwdHMgPSAkKHRoaXMuYmxvY2tzW2ldLmZyYW1lRG9jdW1lbnQuYm9keSkuZmluZCgnc2NyaXB0Jyk7XG4gICAgICAgICAgICAgICAgdmFyIHRoZUlmcmFtZSA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKFwic2tlbGV0b25cIik7XG5cbiAgICAgICAgICAgICAgICBpZiggc2NyaXB0cy5zaXplKCkgPiAwICkge1xuXG4gICAgICAgICAgICAgICAgICAgIHNjcmlwdHMuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgc2NyaXB0O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiggJCh0aGlzKS50ZXh0KCkgIT09ICcnICkgey8vc2NyaXB0IHRhZ3Mgd2l0aCBjb250ZW50XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzY3JpcHQgPSB0aGVJZnJhbWUuY29udGVudFdpbmRvdy5kb2N1bWVudC5jcmVhdGVFbGVtZW50KFwic2NyaXB0XCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdC50eXBlID0gJ3RleHQvamF2YXNjcmlwdCc7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2NyaXB0LmlubmVySFRNTCA9ICQodGhpcykudGV4dCgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhlSWZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZChzY3JpcHQpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYoICQodGhpcykuYXR0cignc3JjJykgIT09IG51bGwgJiYgcGFnZS5zY3JpcHRzLmluZGV4T2YoJCh0aGlzKS5hdHRyKCdzcmMnKSkgPT09IC0xICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vdXNlIGluZGV4T2YgdG8gbWFrZSBzdXJlIGVhY2ggc2NyaXB0IG9ubHkgYXBwZWFycyBvbiB0aGUgcHJvZHVjZWQgcGFnZSBvbmNlXG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzY3JpcHQgPSB0aGVJZnJhbWUuY29udGVudFdpbmRvdy5kb2N1bWVudC5jcmVhdGVFbGVtZW50KFwic2NyaXB0XCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdC50eXBlID0gJ3RleHQvamF2YXNjcmlwdCc7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2NyaXB0LnNyYyA9ICQodGhpcykuYXR0cignc3JjJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGVJZnJhbWUuY29udGVudFdpbmRvdy5kb2N1bWVudC5ib2R5LmFwcGVuZENoaWxkKHNjcmlwdCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBwYWdlLnNjcmlwdHMucHVzaCgkKHRoaXMpLmF0dHIoJ3NyYycpKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnNvbGUubG9nKHRoaXMuc2NyaXB0cyk7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgY2xlYXIgb3V0IHRoaXMgcGFnZVxuICAgICAgICAqL1xuICAgICAgICB0aGlzLmNsZWFyID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciBibG9jayA9IHRoaXMuYmxvY2tzLnBvcCgpO1xuXG4gICAgICAgICAgICB3aGlsZSggYmxvY2sgIT09IHVuZGVmaW5lZCApIHtcblxuICAgICAgICAgICAgICAgIGJsb2NrLmRlbGV0ZSgpO1xuXG4gICAgICAgICAgICAgICAgYmxvY2sgPSB0aGlzLmJsb2Nrcy5wb3AoKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cblxuICAgICAgICAvL2xvb3AgdGhyb3VnaCB0aGUgZnJhbWVzL2Jsb2Nrc1xuXG4gICAgICAgIGlmKCBwYWdlLmhhc093blByb3BlcnR5KCdibG9ja3MnKSApIHtcblxuICAgICAgICAgICAgZm9yKCB2YXIgeCA9IDA7IHggPCBwYWdlLmJsb2Nrcy5sZW5ndGg7IHgrKyApIHtcblxuICAgICAgICAgICAgICAgIC8vY3JlYXRlIG5ldyBCbG9ja1xuXG4gICAgICAgICAgICAgICAgdmFyIG5ld0Jsb2NrID0gbmV3IEJsb2NrKCk7XG5cbiAgICAgICAgICAgICAgICBwYWdlLmJsb2Nrc1t4XS5zcmMgPSBhcHBVSS5zaXRlVXJsK1wic2l0ZS9nZXRmcmFtZS9cIitwYWdlLmJsb2Nrc1t4XS5pZDtcblxuICAgICAgICAgICAgICAgIC8vc2FuZGJveGVkIGJsb2NrP1xuICAgICAgICAgICAgICAgIGlmKCBwYWdlLmJsb2Nrc1t4XS5mcmFtZXNfc2FuZGJveCA9PT0gJzEnKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgbmV3QmxvY2suc2FuZGJveCA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIG5ld0Jsb2NrLnNhbmRib3hfbG9hZGVyID0gcGFnZS5ibG9ja3NbeF0uZnJhbWVzX2xvYWRlcmZ1bmN0aW9uO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgbmV3QmxvY2suZnJhbWVJRCA9IHBhZ2UuYmxvY2tzW3hdLmZyYW1lc19pZDtcbiAgICAgICAgICAgICAgICBuZXdCbG9jay5jcmVhdGVQYXJlbnRMSShwYWdlLmJsb2Nrc1t4XS5mcmFtZXNfaGVpZ2h0KTtcbiAgICAgICAgICAgICAgICBuZXdCbG9jay5jcmVhdGVGcmFtZShwYWdlLmJsb2Nrc1t4XSk7XG4gICAgICAgICAgICAgICAgbmV3QmxvY2suY3JlYXRlRnJhbWVDb3ZlcigpO1xuICAgICAgICAgICAgICAgIG5ld0Jsb2NrLmluc2VydEJsb2NrSW50b0RvbSh0aGlzLnBhcmVudFVMKTtcblxuICAgICAgICAgICAgICAgIC8vYWRkIHRoZSBibG9jayB0byB0aGUgbmV3IHBhZ2VcbiAgICAgICAgICAgICAgICB0aGlzLmJsb2Nrcy5wdXNoKG5ld0Jsb2NrKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cblxuICAgICAgICAvL2FkZCB0aGlzIHBhZ2UgdG8gdGhlIHNpdGUgb2JqZWN0XG4gICAgICAgIHNpdGUuc2l0ZVBhZ2VzLnB1c2goIHRoaXMgKTtcblxuICAgICAgICAvL3BsYW50IHRoZSBuZXcgVUwgaW4gdGhlIERPTSAob24gdGhlIGNhbnZhcylcbiAgICAgICAgc2l0ZS5kaXZDYW52YXMuYXBwZW5kQ2hpbGQodGhpcy5wYXJlbnRVTCk7XG5cbiAgICAgICAgLy9tYWtlIHRoZSBibG9ja3MvZnJhbWVzIGluIGVhY2ggcGFnZSBzb3J0YWJsZVxuXG4gICAgICAgIHZhciB0aGVQYWdlID0gdGhpcztcblxuICAgICAgICAkKHRoaXMucGFyZW50VUwpLnNvcnRhYmxlKHtcbiAgICAgICAgICAgIHJldmVydDogdHJ1ZSxcbiAgICAgICAgICAgIHBsYWNlaG9sZGVyOiBcImRyb3AtaG92ZXJcIixcbiAgICAgICAgICAgIHN0b3A6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBzaXRlLnNldFBlbmRpbmdDaGFuZ2VzKHRydWUpO1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGJlZm9yZVN0b3A6IGZ1bmN0aW9uKGV2ZW50LCB1aSl7XG5cbiAgICAgICAgICAgICAgICAvL3RlbXBsYXRlIG9yIHJlZ3VsYXIgYmxvY2s/XG4gICAgICAgICAgICAgICAgdmFyIGF0dHIgPSB1aS5pdGVtLmF0dHIoJ2RhdGEtZnJhbWVzJyk7XG5cbiAgICAgICAgICAgICAgICB2YXIgbmV3QmxvY2s7XG5cbiAgICAgICAgICAgICAgICBpZiAodHlwZW9mIGF0dHIgIT09IHR5cGVvZiB1bmRlZmluZWQgJiYgYXR0ciAhPT0gZmFsc2UpIHsvL3RlbXBsYXRlLCBidWlsZCBpdFxuXG4gICAgICAgICAgICAgICAgICAgICQoJyNzdGFydCcpLmhpZGUoKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2NsZWFyIG91dCBhbGwgYmxvY2tzIG9uIHRoaXMgcGFnZVxuICAgICAgICAgICAgICAgICAgICB0aGVQYWdlLmNsZWFyKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9jcmVhdGUgdGhlIG5ldyBmcmFtZXNcbiAgICAgICAgICAgICAgICAgICAgdmFyIGZyYW1lSURzID0gdWkuaXRlbS5hdHRyKCdkYXRhLWZyYW1lcycpLnNwbGl0KCctJyk7XG4gICAgICAgICAgICAgICAgICAgIHZhciBoZWlnaHRzID0gdWkuaXRlbS5hdHRyKCdkYXRhLWhlaWdodHMnKS5zcGxpdCgnLScpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgdXJscyA9IHVpLml0ZW0uYXR0cignZGF0YS1vcmlnaW5hbHVybHMnKS5zcGxpdCgnLScpO1xuXG4gICAgICAgICAgICAgICAgICAgIGZvciggdmFyIHggPSAwOyB4IDwgZnJhbWVJRHMubGVuZ3RoOyB4KyspIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgbmV3QmxvY2sgPSBuZXcgQmxvY2soKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0Jsb2NrLmNyZWF0ZVBhcmVudExJKGhlaWdodHNbeF0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgZnJhbWVEYXRhID0ge307XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGZyYW1lRGF0YS5zcmMgPSBhcHBVSS5zaXRlVXJsKydzaXRlL2dldGZyYW1lLycrZnJhbWVJRHNbeF07XG4gICAgICAgICAgICAgICAgICAgICAgICBmcmFtZURhdGEuZnJhbWVzX29yaWdpbmFsX3VybCA9IGFwcFVJLnNpdGVVcmwrJ3NpdGUvZ2V0ZnJhbWUvJytmcmFtZUlEc1t4XTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGZyYW1lRGF0YS5mcmFtZXNfaGVpZ2h0ID0gaGVpZ2h0c1t4XTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgbmV3QmxvY2suY3JlYXRlRnJhbWUoIGZyYW1lRGF0YSApO1xuICAgICAgICAgICAgICAgICAgICAgICAgbmV3QmxvY2suY3JlYXRlRnJhbWVDb3ZlcigpO1xuICAgICAgICAgICAgICAgICAgICAgICAgbmV3QmxvY2suaW5zZXJ0QmxvY2tJbnRvRG9tKHRoZVBhZ2UucGFyZW50VUwpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL2FkZCB0aGUgYmxvY2sgdG8gdGhlIG5ldyBwYWdlXG4gICAgICAgICAgICAgICAgICAgICAgICB0aGVQYWdlLmJsb2Nrcy5wdXNoKG5ld0Jsb2NrKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9kcm9wcGVkIGVsZW1lbnQsIHNvIHdlJ3ZlIGdvdCBwZW5kaW5nIGNoYW5nZXNcbiAgICAgICAgICAgICAgICAgICAgICAgIHNpdGUuc2V0UGVuZGluZ0NoYW5nZXModHJ1ZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIC8vc2V0IHRoZSB0ZW1wYXRlSURcbiAgICAgICAgICAgICAgICAgICAgYnVpbGRlclVJLnRlbXBsYXRlSUQgPSB1aS5pdGVtLmF0dHIoJ2RhdGEtcGFnZWlkJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9tYWtlIHN1cmUgbm90aGluZyBnZXRzIGRyb3BwZWQgaW4gdGhlIGxzaXRcbiAgICAgICAgICAgICAgICAgICAgdWkuaXRlbS5odG1sKG51bGwpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vZGVsZXRlIGRyYWcgcGxhY2UgaG9sZGVyXG4gICAgICAgICAgICAgICAgICAgICQoJ2JvZHkgLnVpLXNvcnRhYmxlLWhlbHBlcicpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHsvL3JlZ3VsYXIgYmxvY2tcblxuICAgICAgICAgICAgICAgICAgICAvL2FyZSB3ZSBkZWFsaW5nIHdpdGggYSBuZXcgYmxvY2sgYmVpbmcgZHJvcHBlZCBvbnRvIHRoZSBjYW52YXMsIG9yIGEgcmVvcmRlcmluZyBvZyBibG9ja3MgYWxyZWFkeSBvbiB0aGUgY2FudmFzP1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCB1aS5pdGVtLmZpbmQoJy5mcmFtZUNvdmVyID4gYnV0dG9uJykuc2l6ZSgpID4gMCApIHsvL3JlLW9yZGVyaW5nIG9mIGJsb2NrcyBvbiBjYW52YXNcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9ubyBuZWVkIHRvIGNyZWF0ZSBhIG5ldyBibG9jayBvYmplY3QsIHdlIHNpbXBseSBuZWVkIHRvIG1ha2Ugc3VyZSB0aGUgcG9zaXRpb24gb2YgdGhlIGV4aXN0aW5nIGJsb2NrIGluIHRoZSBTaXRlIG9iamVjdFxuICAgICAgICAgICAgICAgICAgICAgICAgLy9pcyBjaGFuZ2VkIHRvIHJlZmxlY3QgdGhlIG5ldyBwb3NpdGlvbiBvZiB0aGUgYmxvY2sgb24gdGggY2FudmFzXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBmcmFtZUlEID0gdWkuaXRlbS5maW5kKCdpZnJhbWUnKS5hdHRyKCdpZCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIG5ld1BvcyA9IHVpLml0ZW0uaW5kZXgoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgc2l0ZS5hY3RpdmVQYWdlLnNldFBvc2l0aW9uKGZyYW1lSUQsIG5ld1Bvcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHsvL25ldyBibG9jayBvbiBjYW52YXNcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9uZXcgYmxvY2tcbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0Jsb2NrID0gbmV3IEJsb2NrKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0Jsb2NrLnBsYWNlT25DYW52YXModWkpO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHN0YXJ0OiBmdW5jdGlvbihldmVudCwgdWkpe1xuXG4gICAgICAgICAgICAgICAgaWYoIHVpLml0ZW0uZmluZCgnLmZyYW1lQ292ZXInKS5zaXplKCkgIT09IDAgKSB7XG4gICAgICAgICAgICAgICAgICAgIGJ1aWxkZXJVSS5mcmFtZUNvbnRlbnRzID0gdWkuaXRlbS5maW5kKCdpZnJhbWUnKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciApLmh0bWwoKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBvdmVyOiBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgJCgnI3N0YXJ0JykuaGlkZSgpO1xuXG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vYWRkIHRvIHRoZSBwYWdlcyBtZW51XG4gICAgICAgIHRoaXMubWVudUl0ZW0gPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdMSScpO1xuICAgICAgICB0aGlzLm1lbnVJdGVtLmlubmVySFRNTCA9IHRoaXMucGFnZU1lbnVUZW1wbGF0ZTtcblxuICAgICAgICAkKHRoaXMubWVudUl0ZW0pLmZpbmQoJ2E6Zmlyc3QnKS50ZXh0KHBhZ2VOYW1lKS5hdHRyKCdocmVmJywgJyNwYWdlJytjb3VudGVyKTtcblxuICAgICAgICB2YXIgdGhlTGluayA9ICQodGhpcy5tZW51SXRlbSkuZmluZCgnYTpmaXJzdCcpLmdldCgwKTtcblxuICAgICAgICAvL2JpbmQgc29tZSBldmVudHNcbiAgICAgICAgdGhpcy5tZW51SXRlbS5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIHRoaXMsIGZhbHNlKTtcblxuICAgICAgICB0aGlzLm1lbnVJdGVtLnF1ZXJ5U2VsZWN0b3IoJ2EuZmlsZUVkaXQnKS5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIHRoaXMsIGZhbHNlKTtcbiAgICAgICAgdGhpcy5tZW51SXRlbS5xdWVyeVNlbGVjdG9yKCdhLmZpbGVTYXZlJykuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCB0aGlzLCBmYWxzZSk7XG4gICAgICAgIHRoaXMubWVudUl0ZW0ucXVlcnlTZWxlY3RvcignYS5maWxlRGVsJykuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCB0aGlzLCBmYWxzZSk7XG5cbiAgICAgICAgLy9hZGQgdG8gdGhlIHBhZ2UgbGluayBkcm9wZG93blxuICAgICAgICB0aGlzLmxpbmtzRHJvcGRvd25JdGVtID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnT1BUSU9OJyk7XG4gICAgICAgIHRoaXMubGlua3NEcm9wZG93bkl0ZW0uc2V0QXR0cmlidXRlKCd2YWx1ZScsIHBhZ2VOYW1lK1wiLmh0bWxcIik7XG4gICAgICAgIHRoaXMubGlua3NEcm9wZG93bkl0ZW0udGV4dCA9IHBhZ2VOYW1lO1xuXG4gICAgICAgIGJ1aWxkZXJVSS5kcm9wZG93blBhZ2VMaW5rcy5hcHBlbmRDaGlsZCggdGhpcy5saW5rc0Ryb3Bkb3duSXRlbSApO1xuXG4gICAgICAgIHNpdGUucGFnZXNNZW51LmFwcGVuZENoaWxkKHRoaXMubWVudUl0ZW0pO1xuXG4gICAgfVxuXG4gICAgUGFnZS5wcm90b3R5cGUuaGFuZGxlRXZlbnQgPSBmdW5jdGlvbihldmVudCkge1xuICAgICAgICBzd2l0Y2ggKGV2ZW50LnR5cGUpIHtcbiAgICAgICAgICAgIGNhc2UgXCJjbGlja1wiOlxuXG4gICAgICAgICAgICAgICAgaWYoIGV2ZW50LnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ2ZpbGVFZGl0JykgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5lZGl0UGFnZU5hbWUoKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggZXZlbnQudGFyZ2V0LmNsYXNzTGlzdC5jb250YWlucygnZmlsZVNhdmUnKSApIHtcblxuICAgICAgICAgICAgICAgICAgICB0aGlzLnVwZGF0ZVBhZ2VOYW1lRXZlbnQoZXZlbnQudGFyZ2V0KTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggZXZlbnQudGFyZ2V0LmNsYXNzTGlzdC5jb250YWlucygnZmlsZURlbCcpICkge1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciB0aGVQYWdlID0gdGhpcztcblxuICAgICAgICAgICAgICAgICAgICAkKGJ1aWxkZXJVSS5tb2RhbERlbGV0ZVBhZ2UpLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgJChidWlsZGVyVUkubW9kYWxEZWxldGVQYWdlKS5vZmYoJ2NsaWNrJywgJyNkZWxldGVQYWdlQ29uZmlybScpLm9uKCdjbGljaycsICcjZGVsZXRlUGFnZUNvbmZpcm0nLCBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdGhlUGFnZS5kZWxldGUoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJChidWlsZGVyVUkubW9kYWxEZWxldGVQYWdlKS5tb2RhbCgnaGlkZScpO1xuXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICAgICB0aGlzLnNlbGVjdFBhZ2UoKTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICB9XG4gICAgfTtcblxuXG4gICAgLypcbiAgICAgICAgQmxvY2sgY29uc3RydWN0b3JcbiAgICAqL1xuICAgIGZ1bmN0aW9uIEJsb2NrICgpIHtcblxuICAgICAgICB0aGlzLmZyYW1lSUQgPSAwO1xuICAgICAgICB0aGlzLnNhbmRib3ggPSBmYWxzZTtcbiAgICAgICAgdGhpcy5zYW5kYm94X2xvYWRlciA9ICcnO1xuICAgICAgICB0aGlzLnN0YXR1cyA9ICcnOy8vJycsICdjaGFuZ2VkJyBvciAnbmV3J1xuICAgICAgICB0aGlzLm9yaWdpbmFsVXJsID0gJyc7XG5cbiAgICAgICAgdGhpcy5wYXJlbnRMSSA9IHt9O1xuICAgICAgICB0aGlzLmZyYW1lQ292ZXIgPSB7fTtcbiAgICAgICAgdGhpcy5mcmFtZSA9IHt9O1xuICAgICAgICB0aGlzLmZyYW1lRG9jdW1lbnQgPSB7fTtcbiAgICAgICAgdGhpcy5mcmFtZUhlaWdodCA9IDA7XG5cbiAgICAgICAgdGhpcy5hbm5vdCA9IHt9O1xuICAgICAgICB0aGlzLmFubm90VGltZW91dCA9IHt9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBjcmVhdGVzIHRoZSBwYXJlbnQgY29udGFpbmVyIChMSSlcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5jcmVhdGVQYXJlbnRMSSA9IGZ1bmN0aW9uKGhlaWdodCkge1xuXG4gICAgICAgICAgICB0aGlzLnBhcmVudExJID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnTEknKTtcbiAgICAgICAgICAgIHRoaXMucGFyZW50TEkuc2V0QXR0cmlidXRlKCdjbGFzcycsICdlbGVtZW50Jyk7XG4gICAgICAgICAgICAvL3RoaXMucGFyZW50TEkuc2V0QXR0cmlidXRlKCdzdHlsZScsICdoZWlnaHQ6ICcraGVpZ2h0KydweCcpO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGNyZWF0ZXMgdGhlIGlmcmFtZSBvbiB0aGUgY2FudmFzXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuY3JlYXRlRnJhbWUgPSBmdW5jdGlvbihmcmFtZSkge1xuXG4gICAgICAgICAgICB0aGlzLmZyYW1lID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnSUZSQU1FJyk7XG4gICAgICAgICAgICB0aGlzLmZyYW1lLnNldEF0dHJpYnV0ZSgnZnJhbWVib3JkZXInLCAwKTtcbiAgICAgICAgICAgIHRoaXMuZnJhbWUuc2V0QXR0cmlidXRlKCdzY3JvbGxpbmcnLCAwKTtcbiAgICAgICAgICAgIHRoaXMuZnJhbWUuc2V0QXR0cmlidXRlKCdzcmMnLCBmcmFtZS5zcmMpO1xuICAgICAgICAgICAgdGhpcy5mcmFtZS5zZXRBdHRyaWJ1dGUoJ2RhdGEtb3JpZ2luYWx1cmwnLCBmcmFtZS5mcmFtZXNfb3JpZ2luYWxfdXJsKTtcbiAgICAgICAgICAgIHRoaXMub3JpZ2luYWxVcmwgPSBmcmFtZS5mcmFtZXNfb3JpZ2luYWxfdXJsO1xuICAgICAgICAgICAgLy90aGlzLmZyYW1lLnNldEF0dHJpYnV0ZSgnZGF0YS1oZWlnaHQnLCBmcmFtZS5mcmFtZXNfaGVpZ2h0KTtcbiAgICAgICAgICAgIC8vdGhpcy5mcmFtZUhlaWdodCA9IGZyYW1lLmZyYW1lc19oZWlnaHQ7XG4gICAgICAgICAgICB0aGlzLmZyYW1lLnNldEF0dHJpYnV0ZSgnc3R5bGUnLCAnYmFja2dyb3VuZDogJytcIiNmZmZmZmYgdXJsKCdcIithcHBVSS5iYXNlVXJsK1wic3JjL2ltYWdlcy9sb2FkaW5nLmdpZicpIDUwJSA1MCUgbm8tcmVwZWF0XCIpO1xuXG4gICAgICAgICAgICAkKHRoaXMuZnJhbWUpLnVuaXF1ZUlkKCk7XG5cbiAgICAgICAgICAgIC8vc2FuZGJveD9cbiAgICAgICAgICAgIGlmKCB0aGlzLnNhbmRib3ggIT09IGZhbHNlICkge1xuXG4gICAgICAgICAgICAgICAgdGhpcy5mcmFtZS5zZXRBdHRyaWJ1dGUoJ2RhdGEtbG9hZGVyZnVuY3Rpb24nLCB0aGlzLnNhbmRib3hfbG9hZGVyKTtcbiAgICAgICAgICAgICAgICB0aGlzLmZyYW1lLnNldEF0dHJpYnV0ZSgnZGF0YS1zYW5kYm94JywgdGhpcy5zYW5kYm94KTtcblxuICAgICAgICAgICAgICAgIC8vcmVjcmVhdGUgdGhlIHNhbmRib3hlZCBpZnJhbWUgZWxzZXdoZXJlXG4gICAgICAgICAgICAgICAgdmFyIHNhbmRib3hlZEZyYW1lID0gJCgnPGlmcmFtZSBzcmM9XCInK2ZyYW1lLnNyYysnXCIgaWQ9XCInK3RoaXMuc2FuZGJveCsnXCIgc2FuZGJveD1cImFsbG93LXNhbWUtb3JpZ2luXCI+PC9pZnJhbWU+Jyk7XG4gICAgICAgICAgICAgICAgJCgnI3NhbmRib3hlcycpLmFwcGVuZCggc2FuZGJveGVkRnJhbWUgKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGluc2VydCB0aGUgaWZyYW1lIGludG8gdGhlIERPTSBvbiB0aGUgY2FudmFzXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuaW5zZXJ0QmxvY2tJbnRvRG9tID0gZnVuY3Rpb24odGhlVUwpIHtcblxuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5hcHBlbmRDaGlsZCh0aGlzLmZyYW1lKTtcbiAgICAgICAgICAgIHRoZVVMLmFwcGVuZENoaWxkKCB0aGlzLnBhcmVudExJICk7XG5cbiAgICAgICAgICAgIHRoaXMuZnJhbWUuYWRkRXZlbnRMaXN0ZW5lcignbG9hZCcsIHRoaXMsIGZhbHNlKTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBzZXRzIHRoZSBmcmFtZSBkb2N1bWVudCBmb3IgdGhlIGJsb2NrJ3MgaWZyYW1lXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuc2V0RnJhbWVEb2N1bWVudCA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL3NldCB0aGUgZnJhbWUgZG9jdW1lbnQgYXMgd2VsbFxuICAgICAgICAgICAgaWYoIHRoaXMuZnJhbWUuY29udGVudERvY3VtZW50ICkge1xuICAgICAgICAgICAgICAgIHRoaXMuZnJhbWVEb2N1bWVudCA9IHRoaXMuZnJhbWUuY29udGVudERvY3VtZW50O1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB0aGlzLmZyYW1lRG9jdW1lbnQgPSB0aGlzLmZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQ7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vdGhpcy5oZWlnaHRBZGp1c3RtZW50KCk7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgY3JlYXRlcyB0aGUgZnJhbWUgY292ZXIgYW5kIGJsb2NrIGFjdGlvbiBidXR0b25cbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5jcmVhdGVGcmFtZUNvdmVyID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIC8vYnVpbGQgdGhlIGZyYW1lIGNvdmVyIGFuZCBibG9jayBhY3Rpb24gYnV0dG9uc1xuICAgICAgICAgICAgdGhpcy5mcmFtZUNvdmVyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnRElWJyk7XG4gICAgICAgICAgICB0aGlzLmZyYW1lQ292ZXIuY2xhc3NMaXN0LmFkZCgnZnJhbWVDb3ZlcicpO1xuICAgICAgICAgICAgdGhpcy5mcmFtZUNvdmVyLmNsYXNzTGlzdC5hZGQoJ2ZyZXNoJyk7XG4gICAgICAgICAgICB0aGlzLmZyYW1lQ292ZXIuc3R5bGUuaGVpZ2h0ID0gdGhpcy5mcmFtZUhlaWdodCtcInB4XCI7XG5cbiAgICAgICAgICAgIHZhciBkZWxCdXR0b24gPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdCVVRUT04nKTtcbiAgICAgICAgICAgIGRlbEJ1dHRvbi5zZXRBdHRyaWJ1dGUoJ2NsYXNzJywgJ2J0biBidG4tZGFuZ2VyIGRlbGV0ZUJsb2NrJyk7XG4gICAgICAgICAgICBkZWxCdXR0b24uc2V0QXR0cmlidXRlKCd0eXBlJywgJ2J1dHRvbicpO1xuICAgICAgICAgICAgZGVsQnV0dG9uLmlubmVySFRNTCA9ICc8c3BhbiBjbGFzcz1cImZ1aS10cmFzaFwiPjwvc3Bhbj4gcmVtb3ZlJztcbiAgICAgICAgICAgIGRlbEJ1dHRvbi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIHRoaXMsIGZhbHNlKTtcblxuICAgICAgICAgICAgdmFyIHJlc2V0QnV0dG9uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnQlVUVE9OJyk7XG4gICAgICAgICAgICByZXNldEJ1dHRvbi5zZXRBdHRyaWJ1dGUoJ2NsYXNzJywgJ2J0biBidG4td2FybmluZyByZXNldEJsb2NrJyk7XG4gICAgICAgICAgICByZXNldEJ1dHRvbi5zZXRBdHRyaWJ1dGUoJ3R5cGUnLCAnYnV0dG9uJyk7XG4gICAgICAgICAgICByZXNldEJ1dHRvbi5pbm5lckhUTUwgPSAnPGkgY2xhc3M9XCJmYSBmYS1yZWZyZXNoXCI+PC9pPiByZXNldCc7XG4gICAgICAgICAgICByZXNldEJ1dHRvbi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIHRoaXMsIGZhbHNlKTtcblxuICAgICAgICAgICAgdmFyIGh0bWxCdXR0b24gPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdCVVRUT04nKTtcbiAgICAgICAgICAgIGh0bWxCdXR0b24uc2V0QXR0cmlidXRlKCdjbGFzcycsICdidG4gYnRuLWludmVyc2UgaHRtbEJsb2NrJyk7XG4gICAgICAgICAgICBodG1sQnV0dG9uLnNldEF0dHJpYnV0ZSgndHlwZScsICdidXR0b24nKTtcbiAgICAgICAgICAgIGh0bWxCdXR0b24uaW5uZXJIVE1MID0gJzxpIGNsYXNzPVwiZmEgZmEtY29kZVwiPjwvaT4gc291cmNlJztcbiAgICAgICAgICAgIGh0bWxCdXR0b24uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCB0aGlzLCBmYWxzZSk7XG5cbiAgICAgICAgICAgIHRoaXMuZnJhbWVDb3Zlci5hcHBlbmRDaGlsZChkZWxCdXR0b24pO1xuICAgICAgICAgICAgdGhpcy5mcmFtZUNvdmVyLmFwcGVuZENoaWxkKHJlc2V0QnV0dG9uKTtcbiAgICAgICAgICAgIHRoaXMuZnJhbWVDb3Zlci5hcHBlbmRDaGlsZChodG1sQnV0dG9uKTtcblxuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5hcHBlbmRDaGlsZCh0aGlzLmZyYW1lQ292ZXIpO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGF1dG9tYXRpY2FsbHkgY29ycmVjdHMgdGhlIGhlaWdodCBvZiB0aGUgYmxvY2sncyBpZnJhbWUgZGVwZW5kaW5nIG9uIGl0cyBjb250ZW50XG4gICAgICAgICovXG4gICAgICAgIHRoaXMuaGVpZ2h0QWRqdXN0bWVudCA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB2YXIgcGFnZUNvbnRhaW5lciA9IHRoaXMuZnJhbWVEb2N1bWVudC5ib2R5O1xuICAgICAgICAgICAgdmFyIGhlaWdodCA9IHBhZ2VDb250YWluZXIuc2Nyb2xsSGVpZ2h0O1xuXG4gICAgICAgICAgICB0aGlzLmZyYW1lLnN0eWxlLmhlaWdodCA9IGhlaWdodCtcInB4XCI7XG4gICAgICAgICAgICB0aGlzLnBhcmVudExJLnN0eWxlLmhlaWdodCA9IGhlaWdodCtcInB4XCI7XG4gICAgICAgICAgICB0aGlzLmZyYW1lQ292ZXIuc3R5bGUuaGVpZ2h0ID0gaGVpZ2h0K1wicHhcIjtcblxuICAgICAgICAgICAgdGhpcy5mcmFtZUhlaWdodCA9IGhlaWdodDtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBkZWxldGVzIGEgYmxvY2tcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5kZWxldGUgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9yZW1vdmUgZnJvbSBET00vY2FudmFzIHdpdGggYSBuaWNlIGFuaW1hdGlvblxuICAgICAgICAgICAgJCh0aGlzLmZyYW1lLnBhcmVudE5vZGUpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgdGhpcy5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgIHNpdGUuYWN0aXZlUGFnZS5pc0VtcHR5KCk7XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvL3JlbW92ZSBmcm9tIGJsb2NrcyBhcnJheSBpbiB0aGUgYWN0aXZlIHBhZ2VcbiAgICAgICAgICAgIHNpdGUuYWN0aXZlUGFnZS5kZWxldGVCbG9jayh0aGlzKTtcblxuICAgICAgICAgICAgLy9zYW5ib3hcbiAgICAgICAgICAgIGlmKCB0aGlzLnNhbmJkb3ggKSB7XG4gICAgICAgICAgICAgICAgZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoIHRoaXMuc2FuZGJveCApLnJlbW92ZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL2VsZW1lbnQgd2FzIGRlbGV0ZWQsIHNvIHdlJ3ZlIGdvdCBwZW5kaW5nIGNoYW5nZVxuICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICByZXNldHMgYSBibG9jayB0byBpdCdzIG9yaWduYWwgc3RhdGVcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5yZXNldCA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL3Jlc2V0IGZyYW1lIGJ5IHJlbG9hZGluZyBpdFxuICAgICAgICAgICAgdGhpcy5mcmFtZS5jb250ZW50V2luZG93LmxvY2F0aW9uLnJlbG9hZCgpO1xuXG4gICAgICAgICAgICAvL3NhbmRib3g/XG4gICAgICAgICAgICBpZiggdGhpcy5zYW5kYm94ICkge1xuICAgICAgICAgICAgICAgIHZhciBzYW5kYm94RnJhbWUgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCh0aGlzLnNhbmRib3gpLmNvbnRlbnRXaW5kb3cubG9jYXRpb24ucmVsb2FkKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vZWxlbWVudCB3YXMgZGVsZXRlZCwgc28gd2UndmUgZ290IHBlbmRpbmcgY2hhbmdlc1xuICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBsYXVuY2hlcyB0aGUgc291cmNlIGNvZGUgZWRpdG9yXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuc291cmNlID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIC8vaGlkZSB0aGUgaWZyYW1lXG4gICAgICAgICAgICB0aGlzLmZyYW1lLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG5cbiAgICAgICAgICAgIC8vZGlzYWJsZSBzb3J0YWJsZSBvbiB0aGUgcGFyZW50TElcbiAgICAgICAgICAgICQodGhpcy5wYXJlbnRMSS5wYXJlbnROb2RlKS5zb3J0YWJsZSgnZGlzYWJsZScpO1xuXG4gICAgICAgICAgICAvL2J1aWx0IGVkaXRvciBlbGVtZW50XG4gICAgICAgICAgICB2YXIgdGhlRWRpdG9yID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnRElWJyk7XG4gICAgICAgICAgICB0aGVFZGl0b3IuY2xhc3NMaXN0LmFkZCgnYWNlRWRpdG9yJyk7XG4gICAgICAgICAgICAkKHRoZUVkaXRvcikudW5pcXVlSWQoKTtcblxuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5hcHBlbmRDaGlsZCh0aGVFZGl0b3IpO1xuXG4gICAgICAgICAgICAvL2J1aWxkIGFuZCBhcHBlbmQgZXJyb3IgZHJhd2VyXG4gICAgICAgICAgICB2YXIgbmV3TEkgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdMSScpO1xuICAgICAgICAgICAgdmFyIGVycm9yRHJhd2VyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnRElWJyk7XG4gICAgICAgICAgICBlcnJvckRyYXdlci5jbGFzc0xpc3QuYWRkKCdlcnJvckRyYXdlcicpO1xuICAgICAgICAgICAgZXJyb3JEcmF3ZXIuc2V0QXR0cmlidXRlKCdpZCcsICdkaXZfZXJyb3JEcmF3ZXInKTtcbiAgICAgICAgICAgIGVycm9yRHJhd2VyLmlubmVySFRNTCA9ICc8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImJ0biBidG4teHMgYnRuLWVtYm9zc2VkIGJ0bi1kZWZhdWx0IGJ1dHRvbl9jbGVhckVycm9yRHJhd2VyXCIgaWQ9XCJidXR0b25fY2xlYXJFcnJvckRyYXdlclwiPkNMRUFSPC9idXR0b24+JztcbiAgICAgICAgICAgIG5ld0xJLmFwcGVuZENoaWxkKGVycm9yRHJhd2VyKTtcbiAgICAgICAgICAgIGVycm9yRHJhd2VyLnF1ZXJ5U2VsZWN0b3IoJ2J1dHRvbicpLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgdGhpcywgZmFsc2UpO1xuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5wYXJlbnROb2RlLmluc2VydEJlZm9yZShuZXdMSSwgdGhpcy5wYXJlbnRMSS5uZXh0U2libGluZyk7XG5cblxuICAgICAgICAgICAgdmFyIHRoZUlkID0gdGhlRWRpdG9yLmdldEF0dHJpYnV0ZSgnaWQnKTtcbiAgICAgICAgICAgIHZhciBlZGl0b3IgPSBhY2UuZWRpdCggdGhlSWQgKTtcblxuICAgICAgICAgICAgdmFyIHBhZ2VDb250YWluZXIgPSB0aGlzLmZyYW1lRG9jdW1lbnQucXVlcnlTZWxlY3RvciggYkNvbmZpZy5wYWdlQ29udGFpbmVyICk7XG4gICAgICAgICAgICB2YXIgdGhlSFRNTCA9IHBhZ2VDb250YWluZXIuaW5uZXJIVE1MO1xuXG4gICAgICAgICAgICBlZGl0b3Iuc2V0VmFsdWUoIHRoZUhUTUwgKTtcbiAgICAgICAgICAgIGVkaXRvci5zZXRUaGVtZShcImFjZS90aGVtZS90d2lsaWdodFwiKTtcbiAgICAgICAgICAgIGVkaXRvci5nZXRTZXNzaW9uKCkuc2V0TW9kZShcImFjZS9tb2RlL2h0bWxcIik7XG5cbiAgICAgICAgICAgIHZhciBibG9jayA9IHRoaXM7XG5cblxuICAgICAgICAgICAgZWRpdG9yLmdldFNlc3Npb24oKS5vbihcImNoYW5nZUFubm90YXRpb25cIiwgZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgIGJsb2NrLmFubm90ID0gZWRpdG9yLmdldFNlc3Npb24oKS5nZXRBbm5vdGF0aW9ucygpO1xuXG4gICAgICAgICAgICAgICAgY2xlYXJUaW1lb3V0KGJsb2NrLmFubm90VGltZW91dCk7XG5cbiAgICAgICAgICAgICAgICB2YXIgdGltZW91dENvdW50O1xuXG4gICAgICAgICAgICAgICAgaWYoICQoJyNkaXZfZXJyb3JEcmF3ZXIgcCcpLnNpemUoKSA9PT0gMCApIHtcbiAgICAgICAgICAgICAgICAgICAgdGltZW91dENvdW50ID0gYkNvbmZpZy5zb3VyY2VDb2RlRWRpdFN5bnRheERlbGF5O1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIHRpbWVvdXRDb3VudCA9IDEwMDtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBibG9jay5hbm5vdFRpbWVvdXQgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgZm9yICh2YXIga2V5IGluIGJsb2NrLmFubm90KXtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGJsb2NrLmFubm90Lmhhc093blByb3BlcnR5KGtleSkpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKCBibG9jay5hbm5vdFtrZXldLnRleHQgIT09IFwiU3RhcnQgdGFnIHNlZW4gd2l0aG91dCBzZWVpbmcgYSBkb2N0eXBlIGZpcnN0LiBFeHBlY3RlZCBlLmcuIDwhRE9DVFlQRSBodG1sPi5cIiApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgbmV3TGluZSA9ICQoJzxwPjwvcD4nKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIG5ld0tleSA9ICQoJzxiPicrYmxvY2suYW5ub3Rba2V5XS50eXBlKyc6IDwvYj4nKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIG5ld0luZm8gPSAkKCc8c3Bhbj4gJytibG9jay5hbm5vdFtrZXldLnRleHQgKyBcIm9uIGxpbmUgXCIgKyBcIiA8Yj5cIiArIGJsb2NrLmFubm90W2tleV0ucm93Kyc8L2I+PC9zcGFuPicpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBuZXdMaW5lLmFwcGVuZCggbmV3S2V5ICk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIG5ld0xpbmUuYXBwZW5kKCBuZXdJbmZvICk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2Rpdl9lcnJvckRyYXdlcicpLmFwcGVuZCggbmV3TGluZSApO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIGlmKCAkKCcjZGl2X2Vycm9yRHJhd2VyJykuY3NzKCdkaXNwbGF5JykgPT09ICdub25lJyAmJiAkKCcjZGl2X2Vycm9yRHJhd2VyJykuZmluZCgncCcpLnNpemUoKSA+IDAgKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjZGl2X2Vycm9yRHJhd2VyJykuc2xpZGVEb3duKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH0sIHRpbWVvdXRDb3VudCk7XG5cblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8vYnV0dG9uc1xuICAgICAgICAgICAgdmFyIGNhbmNlbEJ1dHRvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ0JVVFRPTicpO1xuICAgICAgICAgICAgY2FuY2VsQnV0dG9uLnNldEF0dHJpYnV0ZSgndHlwZScsICdidXR0b24nKTtcbiAgICAgICAgICAgIGNhbmNlbEJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdidG4nKTtcbiAgICAgICAgICAgIGNhbmNlbEJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdidG4tZGFuZ2VyJyk7XG4gICAgICAgICAgICBjYW5jZWxCdXR0b24uY2xhc3NMaXN0LmFkZCgnZWRpdENhbmNlbEJ1dHRvbicpO1xuICAgICAgICAgICAgY2FuY2VsQnV0dG9uLmNsYXNzTGlzdC5hZGQoJ2J0bi13aWRlJyk7XG4gICAgICAgICAgICBjYW5jZWxCdXR0b24uaW5uZXJIVE1MID0gJzxzcGFuIGNsYXNzPVwiZnVpLWNyb3NzXCI+PC9zcGFuPiBDYW5jZWwnO1xuICAgICAgICAgICAgY2FuY2VsQnV0dG9uLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgdGhpcywgZmFsc2UpO1xuXG4gICAgICAgICAgICB2YXIgc2F2ZUJ1dHRvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ0JVVFRPTicpO1xuICAgICAgICAgICAgc2F2ZUJ1dHRvbi5zZXRBdHRyaWJ1dGUoJ3R5cGUnLCAnYnV0dG9uJyk7XG4gICAgICAgICAgICBzYXZlQnV0dG9uLmNsYXNzTGlzdC5hZGQoJ2J0bicpO1xuICAgICAgICAgICAgc2F2ZUJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdidG4tcHJpbWFyeScpO1xuICAgICAgICAgICAgc2F2ZUJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdlZGl0U2F2ZUJ1dHRvbicpO1xuICAgICAgICAgICAgc2F2ZUJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdidG4td2lkZScpO1xuICAgICAgICAgICAgc2F2ZUJ1dHRvbi5pbm5lckhUTUwgPSAnPHNwYW4gY2xhc3M9XCJmdWktY2hlY2tcIj48L3NwYW4+IFNhdmUnO1xuICAgICAgICAgICAgc2F2ZUJ1dHRvbi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIHRoaXMsIGZhbHNlKTtcblxuICAgICAgICAgICAgdmFyIGJ1dHRvbldyYXBwZXIgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdESVYnKTtcbiAgICAgICAgICAgIGJ1dHRvbldyYXBwZXIuY2xhc3NMaXN0LmFkZCgnZWRpdG9yQnV0dG9ucycpO1xuXG4gICAgICAgICAgICBidXR0b25XcmFwcGVyLmFwcGVuZENoaWxkKCBjYW5jZWxCdXR0b24gKTtcbiAgICAgICAgICAgIGJ1dHRvbldyYXBwZXIuYXBwZW5kQ2hpbGQoIHNhdmVCdXR0b24gKTtcblxuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5hcHBlbmRDaGlsZCggYnV0dG9uV3JhcHBlciApO1xuXG4gICAgICAgICAgICBidWlsZGVyVUkuYWNlRWRpdG9yc1sgdGhlSWQgXSA9IGVkaXRvcjtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBjYW5jZWxzIHRoZSBibG9jayBzb3VyY2UgY29kZSBlZGl0b3JcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5jYW5jZWxTb3VyY2VCbG9jayA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2VuYWJsZSBkcmFnZ2FibGUgb24gdGhlIExJXG4gICAgICAgICAgICAkKHRoaXMucGFyZW50TEkucGFyZW50Tm9kZSkuc29ydGFibGUoJ2VuYWJsZScpO1xuXG4gICAgICAgICAgICAvL2RlbGV0ZSB0aGUgZXJyb3JEcmF3ZXJcbiAgICAgICAgICAgICQodGhpcy5wYXJlbnRMSS5uZXh0U2libGluZykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgIC8vZGVsZXRlIHRoZSBlZGl0b3JcbiAgICAgICAgICAgIHRoaXMucGFyZW50TEkucXVlcnlTZWxlY3RvcignLmFjZUVkaXRvcicpLnJlbW92ZSgpO1xuICAgICAgICAgICAgJCh0aGlzLmZyYW1lKS5mYWRlSW4oNTAwKTtcblxuICAgICAgICAgICAgJCh0aGlzLnBhcmVudExJLnF1ZXJ5U2VsZWN0b3IoJy5lZGl0b3JCdXR0b25zJykpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlKCk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICB1cGRhdGVzIHRoZSBibG9ja3Mgc291cmNlIGNvZGVcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5zYXZlU291cmNlQmxvY2sgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9lbmFibGUgZHJhZ2dhYmxlIG9uIHRoZSBMSVxuICAgICAgICAgICAgJCh0aGlzLnBhcmVudExJLnBhcmVudE5vZGUpLnNvcnRhYmxlKCdlbmFibGUnKTtcblxuICAgICAgICAgICAgdmFyIHRoZUlkID0gdGhpcy5wYXJlbnRMSS5xdWVyeVNlbGVjdG9yKCcuYWNlRWRpdG9yJykuZ2V0QXR0cmlidXRlKCdpZCcpO1xuICAgICAgICAgICAgdmFyIHRoZUNvbnRlbnQgPSBidWlsZGVyVUkuYWNlRWRpdG9yc1t0aGVJZF0uZ2V0VmFsdWUoKTtcblxuICAgICAgICAgICAgLy9kZWxldGUgdGhlIGVycm9yRHJhd2VyXG4gICAgICAgICAgICBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZGl2X2Vycm9yRHJhd2VyJykucGFyZW50Tm9kZS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgLy9kZWxldGUgdGhlIGVkaXRvclxuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5xdWVyeVNlbGVjdG9yKCcuYWNlRWRpdG9yJykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgIC8vdXBkYXRlIHRoZSBmcmFtZSdzIGNvbnRlbnRcbiAgICAgICAgICAgIHRoaXMuZnJhbWVEb2N1bWVudC5xdWVyeVNlbGVjdG9yKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKS5pbm5lckhUTUwgPSB0aGVDb250ZW50O1xuICAgICAgICAgICAgdGhpcy5mcmFtZS5zdHlsZS5kaXNwbGF5ID0gJ2Jsb2NrJztcblxuICAgICAgICAgICAgLy9zYW5kYm94ZWQ/XG4gICAgICAgICAgICBpZiggdGhpcy5zYW5kYm94ICkge1xuXG4gICAgICAgICAgICAgICAgdmFyIHNhbmRib3hGcmFtZSA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCB0aGlzLnNhbmRib3ggKTtcbiAgICAgICAgICAgICAgICB2YXIgc2FuZGJveEZyYW1lRG9jdW1lbnQgPSBzYW5kYm94RnJhbWUuY29udGVudERvY3VtZW50IHx8IHNhbmRib3hGcmFtZS5jb250ZW50V2luZG93LmRvY3VtZW50O1xuXG4gICAgICAgICAgICAgICAgYnVpbGRlclVJLnRlbXBGcmFtZSA9IHNhbmRib3hGcmFtZTtcblxuICAgICAgICAgICAgICAgIHNhbmRib3hGcmFtZURvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoIGJDb25maWcucGFnZUNvbnRhaW5lciApLmlubmVySFRNTCA9IHRoZUNvbnRlbnQ7XG5cbiAgICAgICAgICAgICAgICAvL2RvIHdlIG5lZWQgdG8gZXhlY3V0ZSBhIGxvYWRlciBmdW5jdGlvbj9cbiAgICAgICAgICAgICAgICBpZiggdGhpcy5zYW5kYm94X2xvYWRlciAhPT0gJycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgLypcbiAgICAgICAgICAgICAgICAgICAgdmFyIGNvZGVUb0V4ZWN1dGUgPSBcInNhbmRib3hGcmFtZS5jb250ZW50V2luZG93LlwiK3RoaXMuc2FuZGJveF9sb2FkZXIrXCIoKVwiO1xuICAgICAgICAgICAgICAgICAgICB2YXIgdG1wRnVuYyA9IG5ldyBGdW5jdGlvbihjb2RlVG9FeGVjdXRlKTtcbiAgICAgICAgICAgICAgICAgICAgdG1wRnVuYygpO1xuICAgICAgICAgICAgICAgICAgICAqL1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICQodGhpcy5wYXJlbnRMSS5xdWVyeVNlbGVjdG9yKCcuZWRpdG9yQnV0dG9ucycpKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8vYWRqdXN0IGhlaWdodCBvZiB0aGUgZnJhbWVcbiAgICAgICAgICAgIHRoaXMuaGVpZ2h0QWRqdXN0bWVudCgpO1xuXG4gICAgICAgICAgICAvL25ldyBwYWdlIGFkZGVkLCB3ZSd2ZSBnb3QgcGVuZGluZyBjaGFuZ2VzXG4gICAgICAgICAgICBzaXRlLnNldFBlbmRpbmdDaGFuZ2VzKHRydWUpO1xuXG4gICAgICAgICAgICAvL2Jsb2NrIGhhcyBjaGFuZ2VkXG4gICAgICAgICAgICB0aGlzLnN0YXR1cyA9ICdjaGFuZ2VkJztcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBjbGVhcnMgb3V0IHRoZSBlcnJvciBkcmF3ZXJcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5jbGVhckVycm9yRHJhd2VyID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciBwcyA9IHRoaXMucGFyZW50TEkubmV4dFNpYmxpbmcucXVlcnlTZWxlY3RvckFsbCgncCcpO1xuXG4gICAgICAgICAgICBmb3IoIHZhciBpID0gMDsgaSA8IHBzLmxlbmd0aDsgaSsrICkge1xuICAgICAgICAgICAgICAgIHBzW2ldLnJlbW92ZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHRvZ2dsZXMgdGhlIHZpc2liaWxpdHkgb2YgdGhpcyBibG9jaydzIGZyYW1lQ292ZXJcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy50b2dnbGVDb3ZlciA9IGZ1bmN0aW9uKG9uT3JPZmYpIHtcblxuICAgICAgICAgICAgaWYoIG9uT3JPZmYgPT09ICdPbicgKSB7XG5cbiAgICAgICAgICAgICAgICB0aGlzLnBhcmVudExJLnF1ZXJ5U2VsZWN0b3IoJy5mcmFtZUNvdmVyJykuc3R5bGUuZGlzcGxheSA9ICdibG9jayc7XG5cbiAgICAgICAgICAgIH0gZWxzZSBpZiggb25Pck9mZiA9PT0gJ09mZicgKSB7XG5cbiAgICAgICAgICAgICAgICB0aGlzLnBhcmVudExJLnF1ZXJ5U2VsZWN0b3IoJy5mcmFtZUNvdmVyJykuc3R5bGUuZGlzcGxheSA9ICdub25lJztcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHJldHVybnMgdGhlIGZ1bGwgc291cmNlIGNvZGUgb2YgdGhlIGJsb2NrJ3MgZnJhbWVcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5nZXRTb3VyY2UgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIHNvdXJjZSA9IFwiPGh0bWw+XCI7XG4gICAgICAgICAgICBzb3VyY2UgKz0gdGhpcy5mcmFtZURvY3VtZW50LmhlYWQub3V0ZXJIVE1MO1xuICAgICAgICAgICAgc291cmNlICs9IHRoaXMuZnJhbWVEb2N1bWVudC5ib2R5Lm91dGVySFRNTDtcblxuICAgICAgICAgICAgcmV0dXJuIHNvdXJjZTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBwbGFjZXMgYSBkcmFnZ2VkL2Ryb3BwZWQgYmxvY2sgZnJvbSB0aGUgbGVmdCBzaWRlYmFyIG9udG8gdGhlIGNhbnZhc1xuICAgICAgICAqL1xuICAgICAgICB0aGlzLnBsYWNlT25DYW52YXMgPSBmdW5jdGlvbih1aSkge1xuXG4gICAgICAgICAgICAvL2ZyYW1lIGRhdGEsIHdlJ2xsIG5lZWQgdGhpcyBiZWZvcmUgbWVzc2luZyB3aXRoIHRoZSBpdGVtJ3MgY29udGVudCBIVE1MXG4gICAgICAgICAgICB2YXIgZnJhbWVEYXRhID0ge30sIGF0dHI7XG5cbiAgICAgICAgICAgIGlmKCB1aS5pdGVtLmZpbmQoJ2lmcmFtZScpLnNpemUoKSA+IDAgKSB7Ly9pZnJhbWUgdGh1bWJuYWlsXG5cbiAgICAgICAgICAgICAgICBmcmFtZURhdGEuc3JjID0gdWkuaXRlbS5maW5kKCdpZnJhbWUnKS5hdHRyKCdzcmMnKTtcbiAgICAgICAgICAgICAgICBmcmFtZURhdGEuZnJhbWVzX29yaWdpbmFsX3VybCA9IHVpLml0ZW0uZmluZCgnaWZyYW1lJykuYXR0cignc3JjJyk7XG4gICAgICAgICAgICAgICAgZnJhbWVEYXRhLmZyYW1lc19oZWlnaHQgPSB1aS5pdGVtLmhlaWdodCgpO1xuXG4gICAgICAgICAgICAgICAgLy9zYW5kYm94ZWQgYmxvY2s/XG4gICAgICAgICAgICAgICAgYXR0ciA9IHVpLml0ZW0uZmluZCgnaWZyYW1lJykuYXR0cignc2FuZGJveCcpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHR5cGVvZiBhdHRyICE9PSB0eXBlb2YgdW5kZWZpbmVkICYmIGF0dHIgIT09IGZhbHNlKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2FuZGJveCA9IHNpdGVCdWlsZGVyVXRpbHMuZ2V0UmFuZG9tQXJiaXRyYXJ5KDEwMDAwLCAxMDAwMDAwMDAwKTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zYW5kYm94X2xvYWRlciA9IHVpLml0ZW0uZmluZCgnaWZyYW1lJykuYXR0cignZGF0YS1sb2FkZXJmdW5jdGlvbicpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSBlbHNlIHsvL2ltYWdlIHRodW1ibmFpbFxuXG4gICAgICAgICAgICAgICAgZnJhbWVEYXRhLnNyYyA9IHVpLml0ZW0uZmluZCgnaW1nJykuYXR0cignZGF0YS1zcmNjJyk7XG4gICAgICAgICAgICAgICAgZnJhbWVEYXRhLmZyYW1lc19vcmlnaW5hbF91cmwgPSB1aS5pdGVtLmZpbmQoJ2ltZycpLmF0dHIoJ2RhdGEtc3JjYycpO1xuICAgICAgICAgICAgICAgIGZyYW1lRGF0YS5mcmFtZXNfaGVpZ2h0ID0gdWkuaXRlbS5maW5kKCdpbWcnKS5hdHRyKCdkYXRhLWhlaWdodCcpO1xuXG4gICAgICAgICAgICAgICAgLy9zYW5kYm94ZWQgYmxvY2s/XG4gICAgICAgICAgICAgICAgYXR0ciA9IHVpLml0ZW0uZmluZCgnaW1nJykuYXR0cignZGF0YS1zYW5kYm94Jyk7XG5cbiAgICAgICAgICAgICAgICBpZiAodHlwZW9mIGF0dHIgIT09IHR5cGVvZiB1bmRlZmluZWQgJiYgYXR0ciAhPT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zYW5kYm94ID0gc2l0ZUJ1aWxkZXJVdGlscy5nZXRSYW5kb21BcmJpdHJhcnkoMTAwMDAsIDEwMDAwMDAwMDApO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNhbmRib3hfbG9hZGVyID0gdWkuaXRlbS5maW5kKCdpbWcnKS5hdHRyKCdkYXRhLWxvYWRlcmZ1bmN0aW9uJyk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vY3JlYXRlIHRoZSBuZXcgYmxvY2sgb2JqZWN0XG4gICAgICAgICAgICB0aGlzLmZyYW1lSUQgPSAwO1xuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSSA9IHVpLml0ZW0uZ2V0KDApO1xuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5pbm5lckhUTUwgPSAnJztcbiAgICAgICAgICAgIHRoaXMuc3RhdHVzID0gJ25ldyc7XG4gICAgICAgICAgICB0aGlzLmNyZWF0ZUZyYW1lKGZyYW1lRGF0YSk7XG4gICAgICAgICAgICB0aGlzLnBhcmVudExJLnN0eWxlLmhlaWdodCA9IHRoaXMuZnJhbWVIZWlnaHQrXCJweFwiO1xuICAgICAgICAgICAgdGhpcy5jcmVhdGVGcmFtZUNvdmVyKCk7XG5cbiAgICAgICAgICAgIHRoaXMuZnJhbWUuYWRkRXZlbnRMaXN0ZW5lcignbG9hZCcsIHRoaXMpO1xuXG4gICAgICAgICAgICAvL2luc2VydCB0aGUgY3JlYXRlZCBpZnJhbWVcbiAgICAgICAgICAgIHVpLml0ZW0uYXBwZW5kKCQodGhpcy5mcmFtZSkpO1xuXG4gICAgICAgICAgICAvL2FkZCB0aGUgYmxvY2sgdG8gdGhlIGN1cnJlbnQgcGFnZVxuICAgICAgICAgICAgc2l0ZS5hY3RpdmVQYWdlLmJsb2Nrcy5zcGxpY2UodWkuaXRlbS5pbmRleCgpLCAwLCB0aGlzKTtcblxuICAgICAgICAgICAgLy9jdXN0b20gZXZlbnRcbiAgICAgICAgICAgIHVpLml0ZW0uZmluZCgnaWZyYW1lJykudHJpZ2dlcignY2FudmFzdXBkYXRlZCcpO1xuXG4gICAgICAgICAgICAvL2Ryb3BwZWQgZWxlbWVudCwgc28gd2UndmUgZ290IHBlbmRpbmcgY2hhbmdlc1xuICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICB9O1xuXG5cbiAgICB9XG5cbiAgICBCbG9jay5wcm90b3R5cGUuaGFuZGxlRXZlbnQgPSBmdW5jdGlvbihldmVudCkge1xuICAgICAgICBzd2l0Y2ggKGV2ZW50LnR5cGUpIHtcbiAgICAgICAgICAgIGNhc2UgXCJsb2FkXCI6XG4gICAgICAgICAgICAgICAgdGhpcy5zZXRGcmFtZURvY3VtZW50KCk7XG4gICAgICAgICAgICAgICAgdGhpcy5oZWlnaHRBZGp1c3RtZW50KCk7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMuZnJhbWVDb3ZlcikucmVtb3ZlQ2xhc3MoJ2ZyZXNoJywgNTAwKTtcblxuICAgICAgICAgICAgICAgIGJyZWFrO1xuXG4gICAgICAgICAgICBjYXNlIFwiY2xpY2tcIjpcblxuICAgICAgICAgICAgICAgIHZhciB0aGVCbG9jayA9IHRoaXM7XG5cbiAgICAgICAgICAgICAgICAvL2ZpZ3VyZSBvdXQgd2hhdCB0byBkbyBuZXh0XG5cbiAgICAgICAgICAgICAgICBpZiggZXZlbnQudGFyZ2V0LmNsYXNzTGlzdC5jb250YWlucygnZGVsZXRlQmxvY2snKSApIHsvL2RlbGV0ZSB0aGlzIGJsb2NrXG5cbiAgICAgICAgICAgICAgICAgICAgJChidWlsZGVyVUkubW9kYWxEZWxldGVCbG9jaykubW9kYWwoJ3Nob3cnKTtcblxuICAgICAgICAgICAgICAgICAgICAkKGJ1aWxkZXJVSS5tb2RhbERlbGV0ZUJsb2NrKS5vZmYoJ2NsaWNrJywgJyNkZWxldGVCbG9ja0NvbmZpcm0nKS5vbignY2xpY2snLCAnI2RlbGV0ZUJsb2NrQ29uZmlybScsIGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgICAgICAgICB0aGVCbG9jay5kZWxldGUoZXZlbnQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJChidWlsZGVyVUkubW9kYWxEZWxldGVCbG9jaykubW9kYWwoJ2hpZGUnKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIGV2ZW50LnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ3Jlc2V0QmxvY2snKSApIHsvL3Jlc2V0IHRoZSBibG9ja1xuXG4gICAgICAgICAgICAgICAgICAgICQoYnVpbGRlclVJLm1vZGFsUmVzZXRCbG9jaykubW9kYWwoJ3Nob3cnKTtcblxuICAgICAgICAgICAgICAgICAgICAkKGJ1aWxkZXJVSS5tb2RhbFJlc2V0QmxvY2spLm9mZignY2xpY2snLCAnI3Jlc2V0QmxvY2tDb25maXJtJykub24oJ2NsaWNrJywgJyNyZXNldEJsb2NrQ29uZmlybScsIGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgICAgICAgICB0aGVCbG9jay5yZXNldCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJChidWlsZGVyVUkubW9kYWxSZXNldEJsb2NrKS5tb2RhbCgnaGlkZScpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggZXZlbnQudGFyZ2V0LmNsYXNzTGlzdC5jb250YWlucygnaHRtbEJsb2NrJykgKSB7Ly9zb3VyY2UgY29kZSBlZGl0b3JcblxuICAgICAgICAgICAgICAgICAgICB0aGVCbG9jay5zb3VyY2UoKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggZXZlbnQudGFyZ2V0LmNsYXNzTGlzdC5jb250YWlucygnZWRpdENhbmNlbEJ1dHRvbicpICkgey8vY2FuY2VsIHNvdXJjZSBjb2RlIGVkaXRvclxuXG4gICAgICAgICAgICAgICAgICAgIHRoZUJsb2NrLmNhbmNlbFNvdXJjZUJsb2NrKCk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIGV2ZW50LnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ2VkaXRTYXZlQnV0dG9uJykgKSB7Ly9zYXZlIHNvdXJjZSBjb2RlXG5cbiAgICAgICAgICAgICAgICAgICAgdGhlQmxvY2suc2F2ZVNvdXJjZUJsb2NrKCk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIGV2ZW50LnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ2J1dHRvbl9jbGVhckVycm9yRHJhd2VyJykgKSB7Ly9jbGVhciBlcnJvciBkcmF3ZXJcblxuICAgICAgICAgICAgICAgICAgICB0aGVCbG9jay5jbGVhckVycm9yRHJhd2VyKCk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgfVxuICAgIH07XG5cblxuICAgIC8qXG4gICAgICAgIFNpdGUgb2JqZWN0IGxpdGVyYWxcbiAgICAqL1xuICAgIC8qanNoaW50IC1XMDAzICovXG4gICAgdmFyIHNpdGUgPSB7XG5cbiAgICAgICAgcGVuZGluZ0NoYW5nZXM6IGZhbHNlLCAgICAgIC8vcGVuZGluZyBjaGFuZ2VzIG9yIG5vP1xuICAgICAgICBwYWdlczoge30sICAgICAgICAgICAgICAgICAgLy9hcnJheSBjb250YWluaW5nIGFsbCBwYWdlcywgaW5jbHVkaW5nIHRoZSBjaGlsZCBmcmFtZXMsIGxvYWRlZCBmcm9tIHRoZSBzZXJ2ZXIgb24gcGFnZSBsb2FkXG4gICAgICAgIGlzX2FkbWluOiAwLCAgICAgICAgICAgICAgICAvLzAgZm9yIG5vbi1hZG1pbiwgMSBmb3IgYWRtaW5cbiAgICAgICAgZGF0YToge30sICAgICAgICAgICAgICAgICAgIC8vY29udGFpbmVyIGZvciBhamF4IGxvYWRlZCBzaXRlIGRhdGFcbiAgICAgICAgcGFnZXNUb0RlbGV0ZTogW10sICAgICAgICAgIC8vY29udGFpbnMgcGFnZXMgdG8gYmUgZGVsZXRlZFxuXG4gICAgICAgIHNpdGVQYWdlczogW10sICAgICAgICAgICAgICAvL3RoaXMgaXMgdGhlIG9ubHkgdmFyIGNvbnRhaW5pbmcgdGhlIHJlY2VudCBjYW52YXMgY29udGVudHNcblxuICAgICAgICBzaXRlUGFnZXNSZWFkeUZvclNlcnZlcjoge30sICAgICAvL2NvbnRhaW5zIHRoZSBzaXRlIGRhdGEgcmVhZHkgdG8gYmUgc2VudCB0byB0aGUgc2VydmVyXG5cbiAgICAgICAgYWN0aXZlUGFnZToge30sICAgICAgICAgICAgIC8vaG9sZHMgYSByZWZlcmVuY2UgdG8gdGhlIHBhZ2UgY3VycmVudGx5IG9wZW4gb24gdGhlIGNhbnZhc1xuXG4gICAgICAgIHBhZ2VUaXRsZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3BhZ2VUaXRsZScpLC8vaG9sZHMgdGhlIHBhZ2UgdGl0bGUgb2YgdGhlIGN1cnJlbnQgcGFnZSBvbiB0aGUgY2FudmFzXG5cbiAgICAgICAgZGl2Q2FudmFzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncGFnZUxpc3QnKSwvL0RJViBjb250YWluaW5nIGFsbCBwYWdlcyBvbiB0aGUgY2FudmFzXG5cbiAgICAgICAgcGFnZXNNZW51OiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncGFnZXMnKSwgLy9VTCBjb250YWluaW5nIHRoZSBwYWdlcyBtZW51IGluIHRoZSBzaWRlYmFyXG5cbiAgICAgICAgYnV0dG9uTmV3UGFnZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2FkZFBhZ2UnKSxcbiAgICAgICAgbGlOZXdQYWdlOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbmV3UGFnZUxJJyksXG5cbiAgICAgICAgaW5wdXRQYWdlU2V0dGluZ3NUaXRsZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3BhZ2VEYXRhX3RpdGxlJyksXG4gICAgICAgIGlucHV0UGFnZVNldHRpbmdzTWV0YURlc2NyaXB0aW9uOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncGFnZURhdGFfbWV0YURlc2NyaXB0aW9uJyksXG4gICAgICAgIGlucHV0UGFnZVNldHRpbmdzTWV0YUtleXdvcmRzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncGFnZURhdGFfbWV0YUtleXdvcmRzJyksXG4gICAgICAgIGlucHV0UGFnZVNldHRpbmdzSW5jbHVkZXM6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwYWdlRGF0YV9oZWFkZXJJbmNsdWRlcycpLFxuICAgICAgICBpbnB1dFBhZ2VTZXR0aW5nc1BhZ2VDc3M6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwYWdlRGF0YV9oZWFkZXJDc3MnKSxcblxuICAgICAgICBidXR0b25TdWJtaXRQYWdlU2V0dGluZ3M6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwYWdlU2V0dGluZ3NTdWJtaXR0QnV0dG9uJyksXG5cbiAgICAgICAgbW9kYWxQYWdlU2V0dGluZ3M6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwYWdlU2V0dGluZ3NNb2RhbCcpLFxuXG4gICAgICAgIGJ1dHRvblNhdmU6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdzYXZlUGFnZScpLFxuXG4gICAgICAgIG1lc3NhZ2VTdGFydDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3N0YXJ0JyksXG4gICAgICAgIGRpdkZyYW1lV3JhcHBlcjogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2ZyYW1lV3JhcHBlcicpLFxuXG4gICAgICAgIHNrZWxldG9uOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2tlbGV0b24nKSxcblxuXHRcdGF1dG9TYXZlVGltZXI6IHt9LFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkLmdldEpTT04oYXBwVUkuc2l0ZVVybCtcInNpdGVEYXRhXCIsIGZ1bmN0aW9uKGRhdGEpe1xuXG4gICAgICAgICAgICAgICAgaWYoIGRhdGEuc2l0ZSAhPT0gdW5kZWZpbmVkICkge1xuICAgICAgICAgICAgICAgICAgICBzaXRlLmRhdGEgPSBkYXRhLnNpdGU7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGlmKCBkYXRhLnBhZ2VzICE9PSB1bmRlZmluZWQgKSB7XG4gICAgICAgICAgICAgICAgICAgIHNpdGUucGFnZXMgPSBkYXRhLnBhZ2VzO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHNpdGUuaXNfYWRtaW4gPSBkYXRhLmlzX2FkbWluO1xuXG5cdFx0XHRcdGlmKCAkKCcjcGFnZUxpc3QnKS5zaXplKCkgPiAwICkge1xuICAgICAgICAgICAgICAgIFx0YnVpbGRlclVJLnBvcHVsYXRlQ2FudmFzKCk7XG5cdFx0XHRcdH1cblxuICAgICAgICAgICAgICAgIC8vZmlyZSBjdXN0b20gZXZlbnRcbiAgICAgICAgICAgICAgICAkKCdib2R5JykudHJpZ2dlcignc2l0ZURhdGFMb2FkZWQnKTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICQodGhpcy5idXR0b25OZXdQYWdlKS5vbignY2xpY2snLCBzaXRlLm5ld1BhZ2UpO1xuICAgICAgICAgICAgJCh0aGlzLm1vZGFsUGFnZVNldHRpbmdzKS5vbignc2hvdy5icy5tb2RhbCcsIHNpdGUubG9hZFBhZ2VTZXR0aW5ncyk7XG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uU3VibWl0UGFnZVNldHRpbmdzKS5vbignY2xpY2snLCBzaXRlLnVwZGF0ZVBhZ2VTZXR0aW5ncyk7XG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uU2F2ZSkub24oJ2NsaWNrJywgZnVuY3Rpb24oKXtzaXRlLnNhdmUodHJ1ZSk7fSk7XG5cbiAgICAgICAgICAgIC8vYXV0byBzYXZlIHRpbWVcbiAgICAgICAgICAgIHRoaXMuYXV0b1NhdmVUaW1lciA9IHNldFRpbWVvdXQoc2l0ZS5hdXRvU2F2ZSwgYkNvbmZpZy5hdXRvU2F2ZVRpbWVvdXQpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgYXV0b1NhdmU6IGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgIGlmKHNpdGUucGVuZGluZ0NoYW5nZXMpIHtcbiAgICAgICAgICAgICAgICBzaXRlLnNhdmUoZmFsc2UpO1xuICAgICAgICAgICAgfVxuXG5cdFx0XHR3aW5kb3cuY2xlYXJJbnRlcnZhbCh0aGlzLmF1dG9TYXZlVGltZXIpO1xuICAgICAgICAgICAgdGhpcy5hdXRvU2F2ZVRpbWVyID0gc2V0VGltZW91dChzaXRlLmF1dG9TYXZlLCBiQ29uZmlnLmF1dG9TYXZlVGltZW91dCk7XG5cbiAgICAgICAgfSxcblxuICAgICAgICBzZXRQZW5kaW5nQ2hhbmdlczogZnVuY3Rpb24odmFsdWUpIHtcblxuICAgICAgICAgICAgdGhpcy5wZW5kaW5nQ2hhbmdlcyA9IHZhbHVlO1xuXG4gICAgICAgICAgICBpZiggdmFsdWUgPT09IHRydWUgKSB7XG5cblx0XHRcdFx0Ly9yZXNldCB0aW1lclxuXHRcdFx0XHR3aW5kb3cuY2xlYXJJbnRlcnZhbCh0aGlzLmF1dG9TYXZlVGltZXIpO1xuICAgICAgICAgICAgXHR0aGlzLmF1dG9TYXZlVGltZXIgPSBzZXRUaW1lb3V0KHNpdGUuYXV0b1NhdmUsIGJDb25maWcuYXV0b1NhdmVUaW1lb3V0KTtcblxuICAgICAgICAgICAgICAgICQoJyNzYXZlUGFnZSAuYkxhYmVsJykudGV4dChcIlNhdmUgbm93ICghKVwiKTtcblxuICAgICAgICAgICAgICAgIGlmKCBzaXRlLmFjdGl2ZVBhZ2Uuc3RhdHVzICE9PSAnbmV3JyApIHtcblxuICAgICAgICAgICAgICAgICAgICBzaXRlLmFjdGl2ZVBhZ2Uuc3RhdHVzID0gJ2NoYW5nZWQnO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgJCgnI3NhdmVQYWdlIC5iTGFiZWwnKS50ZXh0KFwiTm90aGluZyB0byBzYXZlXCIpO1xuXG4gICAgICAgICAgICAgICAgc2l0ZS51cGRhdGVQYWdlU3RhdHVzKCcnKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAgc2F2ZTogZnVuY3Rpb24oc2hvd0NvbmZpcm1Nb2RhbCkge1xuXG4gICAgICAgICAgICAvL2ZpcmUgY3VzdG9tIGV2ZW50XG4gICAgICAgICAgICAkKCdib2R5JykudHJpZ2dlcignYmVmb3JlU2F2ZScpO1xuXG4gICAgICAgICAgICAvL2Rpc2FibGUgYnV0dG9uXG4gICAgICAgICAgICAkKFwiYSNzYXZlUGFnZVwiKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgLy9yZW1vdmUgb2xkIGFsZXJ0c1xuICAgICAgICAgICAgJCgnI2Vycm9yTW9kYWwgLm1vZGFsLWJvZHkgPiAqLCAjc3VjY2Vzc01vZGFsIC5tb2RhbC1ib2R5ID4gKicpLmVhY2goZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHNpdGUucHJlcEZvclNhdmUoZmFsc2UpO1xuXG4gICAgICAgICAgICB2YXIgc2VydmVyRGF0YSA9IHt9O1xuICAgICAgICAgICAgc2VydmVyRGF0YS5wYWdlcyA9IHRoaXMuc2l0ZVBhZ2VzUmVhZHlGb3JTZXJ2ZXI7XG4gICAgICAgICAgICBpZiggdGhpcy5wYWdlc1RvRGVsZXRlLmxlbmd0aCA+IDAgKSB7XG4gICAgICAgICAgICAgICAgc2VydmVyRGF0YS50b0RlbGV0ZSA9IHRoaXMucGFnZXNUb0RlbGV0ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHNlcnZlckRhdGEuc2l0ZURhdGEgPSB0aGlzLmRhdGE7XG5cbiAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgdXJsOiBhcHBVSS5zaXRlVXJsK1wic2l0ZS9zYXZlXCIsXG4gICAgICAgICAgICAgICAgdHlwZTogXCJQT1NUXCIsXG4gICAgICAgICAgICAgICAgZGF0YVR5cGU6IFwianNvblwiLFxuICAgICAgICAgICAgICAgIGRhdGE6IHNlcnZlckRhdGEsXG4gICAgICAgICAgICB9KS5kb25lKGZ1bmN0aW9uKHJlcyl7XG5cbiAgICAgICAgICAgICAgICAvL2VuYWJsZSBidXR0b25cbiAgICAgICAgICAgICAgICAkKFwiYSNzYXZlUGFnZVwiKS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgICAgIGlmKCByZXMucmVzcG9uc2VDb2RlID09PSAwICkge1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCBzaG93Q29uZmlybU1vZGFsICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjZXJyb3JNb2RhbCAubW9kYWwtYm9keScpLmFwcGVuZCggJChyZXMucmVzcG9uc2VIVE1MKSApO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2Vycm9yTW9kYWwnKS5tb2RhbCgnc2hvdycpO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggcmVzLnJlc3BvbnNlQ29kZSA9PT0gMSApIHtcblxuICAgICAgICAgICAgICAgICAgICBpZiggc2hvd0NvbmZpcm1Nb2RhbCApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI3N1Y2Nlc3NNb2RhbCAubW9kYWwtYm9keScpLmFwcGVuZCggJChyZXMucmVzcG9uc2VIVE1MKSApO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI3N1Y2Nlc3NNb2RhbCcpLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG5cbiAgICAgICAgICAgICAgICAgICAgLy9ubyBtb3JlIHBlbmRpbmcgY2hhbmdlc1xuICAgICAgICAgICAgICAgICAgICBzaXRlLnNldFBlbmRpbmdDaGFuZ2VzKGZhbHNlKTtcblxuXG4gICAgICAgICAgICAgICAgICAgIC8vdXBkYXRlIHJldmlzaW9ucz9cbiAgICAgICAgICAgICAgICAgICAgJCgnYm9keScpLnRyaWdnZXIoJ2NoYW5nZVBhZ2UnKTtcblxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHByZXBzIHRoZSBzaXRlIGRhdGEgYmVmb3JlIHNlbmRpbmcgaXQgdG8gdGhlIHNlcnZlclxuICAgICAgICAqL1xuICAgICAgICBwcmVwRm9yU2F2ZTogZnVuY3Rpb24odGVtcGxhdGUpIHtcblxuICAgICAgICAgICAgdGhpcy5zaXRlUGFnZXNSZWFkeUZvclNlcnZlciA9IHt9O1xuXG4gICAgICAgICAgICBpZiggdGVtcGxhdGUgKSB7Ly9zYXZpbmcgdGVtcGxhdGUsIG9ubHkgdGhlIGFjdGl2ZVBhZ2UgaXMgbmVlZGVkXG5cbiAgICAgICAgICAgICAgICB0aGlzLnNpdGVQYWdlc1JlYWR5Rm9yU2VydmVyW3RoaXMuYWN0aXZlUGFnZS5uYW1lXSA9IHRoaXMuYWN0aXZlUGFnZS5wcmVwRm9yU2F2ZSgpO1xuXG4gICAgICAgICAgICAgICAgdGhpcy5hY3RpdmVQYWdlLmZ1bGxQYWdlKCk7XG5cbiAgICAgICAgICAgIH0gZWxzZSB7Ly9yZWd1bGFyIHNhdmVcblxuICAgICAgICAgICAgICAgIC8vZmluZCB0aGUgcGFnZXMgd2hpY2ggbmVlZCB0byBiZSBzZW5kIHRvIHRoZSBzZXJ2ZXJcbiAgICAgICAgICAgICAgICBmb3IoIHZhciBpID0gMDsgaSA8IHRoaXMuc2l0ZVBhZ2VzLmxlbmd0aDsgaSsrICkge1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCB0aGlzLnNpdGVQYWdlc1tpXS5zdGF0dXMgIT09ICcnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLnNpdGVQYWdlc1JlYWR5Rm9yU2VydmVyW3RoaXMuc2l0ZVBhZ2VzW2ldLm5hbWVdID0gdGhpcy5zaXRlUGFnZXNbaV0ucHJlcEZvclNhdmUoKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHNldHMgYSBwYWdlIGFzIHRoZSBhY3RpdmUgb25lXG4gICAgICAgICovXG4gICAgICAgIHNldEFjdGl2ZTogZnVuY3Rpb24ocGFnZSkge1xuXG4gICAgICAgICAgICAvL3JlZmVyZW5jZSB0byB0aGUgYWN0aXZlIHBhZ2VcbiAgICAgICAgICAgIHRoaXMuYWN0aXZlUGFnZSA9IHBhZ2U7XG5cbiAgICAgICAgICAgIC8vaGlkZSBvdGhlciBwYWdlc1xuICAgICAgICAgICAgZm9yKHZhciBpIGluIHRoaXMuc2l0ZVBhZ2VzKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5zaXRlUGFnZXNbaV0ucGFyZW50VUwuc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9kaXNwbGF5IGFjdGl2ZSBvbmVcbiAgICAgICAgICAgIHRoaXMuYWN0aXZlUGFnZS5wYXJlbnRVTC5zdHlsZS5kaXNwbGF5ID0gJ2Jsb2NrJztcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGRlLWFjdGl2ZSBhbGwgcGFnZSBtZW51IGl0ZW1zXG4gICAgICAgICovXG4gICAgICAgIGRlQWN0aXZhdGVBbGw6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB2YXIgcGFnZXMgPSB0aGlzLnBhZ2VzTWVudS5xdWVyeVNlbGVjdG9yQWxsKCdsaScpO1xuXG4gICAgICAgICAgICBmb3IoIHZhciBpID0gMDsgaSA8IHBhZ2VzLmxlbmd0aDsgaSsrICkge1xuICAgICAgICAgICAgICAgIHBhZ2VzW2ldLmNsYXNzTGlzdC5yZW1vdmUoJ2FjdGl2ZScpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgYWRkcyBhIG5ldyBwYWdlIHRvIHRoZSBzaXRlXG4gICAgICAgICovXG4gICAgICAgIG5ld1BhZ2U6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICBzaXRlLmRlQWN0aXZhdGVBbGwoKTtcblxuICAgICAgICAgICAgLy9jcmVhdGUgdGhlIG5ldyBwYWdlIGluc3RhbmNlXG5cbiAgICAgICAgICAgIHZhciBwYWdlRGF0YSA9IFtdO1xuICAgICAgICAgICAgdmFyIHRlbXAgPSB7XG4gICAgICAgICAgICAgICAgcGFnZXNfaWQ6IDBcbiAgICAgICAgICAgIH07XG4gICAgICAgICAgICBwYWdlRGF0YVswXSA9IHRlbXA7XG5cbiAgICAgICAgICAgIHZhciBuZXdQYWdlTmFtZSA9ICdwYWdlJysoc2l0ZS5zaXRlUGFnZXMubGVuZ3RoKzEpO1xuXG4gICAgICAgICAgICB2YXIgbmV3UGFnZSA9IG5ldyBQYWdlKG5ld1BhZ2VOYW1lLCBwYWdlRGF0YSwgc2l0ZS5zaXRlUGFnZXMubGVuZ3RoKzEpO1xuXG4gICAgICAgICAgICBuZXdQYWdlLnN0YXR1cyA9ICduZXcnO1xuXG4gICAgICAgICAgICBuZXdQYWdlLnNlbGVjdFBhZ2UoKTtcbiAgICAgICAgICAgIG5ld1BhZ2UuZWRpdFBhZ2VOYW1lKCk7XG5cbiAgICAgICAgICAgIG5ld1BhZ2UuaXNFbXB0eSgpO1xuXG4gICAgICAgICAgICBzaXRlLnNldFBlbmRpbmdDaGFuZ2VzKHRydWUpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgY2hlY2tzIGlmIHRoZSBuYW1lIG9mIGEgcGFnZSBpcyBhbGxvd2VkXG4gICAgICAgICovXG4gICAgICAgIGNoZWNrUGFnZU5hbWU6IGZ1bmN0aW9uKHBhZ2VOYW1lKSB7XG5cbiAgICAgICAgICAgIC8vbWFrZSBzdXJlIHRoZSBuYW1lIGlzIHVuaXF1ZVxuICAgICAgICAgICAgZm9yKCB2YXIgaSBpbiB0aGlzLnNpdGVQYWdlcyApIHtcblxuICAgICAgICAgICAgICAgIGlmKCB0aGlzLnNpdGVQYWdlc1tpXS5uYW1lID09PSBwYWdlTmFtZSAmJiB0aGlzLmFjdGl2ZVBhZ2UgIT09IHRoaXMuc2l0ZVBhZ2VzW2ldICkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnBhZ2VOYW1lRXJyb3IgPSBcIlRoZSBwYWdlIG5hbWUgbXVzdCBiZSB1bmlxdWUuXCI7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIHRydWU7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICByZW1vdmVzIHVuYWxsb3dlZCBjaGFyYWN0ZXJzIGZyb20gdGhlIHBhZ2UgbmFtZVxuICAgICAgICAqL1xuICAgICAgICBwcmVwUGFnZU5hbWU6IGZ1bmN0aW9uKHBhZ2VOYW1lKSB7XG5cbiAgICAgICAgICAgIHBhZ2VOYW1lID0gcGFnZU5hbWUucmVwbGFjZSgnICcsICcnKTtcbiAgICAgICAgICAgIHBhZ2VOYW1lID0gcGFnZU5hbWUucmVwbGFjZSgvWz8qIS58JiM7JCVAXCI8PigpKyxdL2csIFwiXCIpO1xuXG4gICAgICAgICAgICByZXR1cm4gcGFnZU5hbWU7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBzYXZlIHBhZ2Ugc2V0dGluZ3MgZm9yIHRoZSBjdXJyZW50IHBhZ2VcbiAgICAgICAgKi9cbiAgICAgICAgdXBkYXRlUGFnZVNldHRpbmdzOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgc2l0ZS5hY3RpdmVQYWdlLnBhZ2VTZXR0aW5ncy50aXRsZSA9IHNpdGUuaW5wdXRQYWdlU2V0dGluZ3NUaXRsZS52YWx1ZTtcbiAgICAgICAgICAgIHNpdGUuYWN0aXZlUGFnZS5wYWdlU2V0dGluZ3MubWV0YV9kZXNjcmlwdGlvbiA9IHNpdGUuaW5wdXRQYWdlU2V0dGluZ3NNZXRhRGVzY3JpcHRpb24udmFsdWU7XG4gICAgICAgICAgICBzaXRlLmFjdGl2ZVBhZ2UucGFnZVNldHRpbmdzLm1ldGFfa2V5d29yZHMgPSBzaXRlLmlucHV0UGFnZVNldHRpbmdzTWV0YUtleXdvcmRzLnZhbHVlO1xuICAgICAgICAgICAgc2l0ZS5hY3RpdmVQYWdlLnBhZ2VTZXR0aW5ncy5oZWFkZXJfaW5jbHVkZXMgPSBzaXRlLmlucHV0UGFnZVNldHRpbmdzSW5jbHVkZXMudmFsdWU7XG4gICAgICAgICAgICBzaXRlLmFjdGl2ZVBhZ2UucGFnZVNldHRpbmdzLnBhZ2VfY3NzID0gc2l0ZS5pbnB1dFBhZ2VTZXR0aW5nc1BhZ2VDc3MudmFsdWU7XG5cbiAgICAgICAgICAgIHNpdGUuc2V0UGVuZGluZ0NoYW5nZXModHJ1ZSk7XG5cbiAgICAgICAgICAgICQoc2l0ZS5tb2RhbFBhZ2VTZXR0aW5ncykubW9kYWwoJ2hpZGUnKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHVwZGF0ZSBwYWdlIHN0YXR1c2VzXG4gICAgICAgICovXG4gICAgICAgIHVwZGF0ZVBhZ2VTdGF0dXM6IGZ1bmN0aW9uKHN0YXR1cykge1xuXG4gICAgICAgICAgICBmb3IoIHZhciBpIGluIHRoaXMuc2l0ZVBhZ2VzICkge1xuICAgICAgICAgICAgICAgIHRoaXMuc2l0ZVBhZ2VzW2ldLnN0YXR1cyA9IHN0YXR1cztcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9XG5cbiAgICB9O1xuXG4gICAgYnVpbGRlclVJLmluaXQoKTsgc2l0ZS5pbml0KCk7XG5cblxuICAgIC8vKioqKiBFWFBPUlRTXG4gICAgbW9kdWxlLmV4cG9ydHMuc2l0ZSA9IHNpdGU7XG4gICAgbW9kdWxlLmV4cG9ydHMuYnVpbGRlclVJID0gYnVpbGRlclVJO1xuXG59KCkpOyIsIihmdW5jdGlvbiAoKSB7XG4gICAgXCJ1c2Ugc3RyaWN0XCI7XG5cbiAgICB2YXIgc2l0ZUJ1aWxkZXIgPSByZXF1aXJlKCcuL2J1aWxkZXIuanMnKTtcblxuICAgIC8qXG4gICAgICAgIGNvbnN0cnVjdG9yIGZ1bmN0aW9uIGZvciBFbGVtZW50XG4gICAgKi9cbiAgICBtb2R1bGUuZXhwb3J0cy5FbGVtZW50ID0gZnVuY3Rpb24gKGVsKSB7XG5cbiAgICAgICAgdGhpcy5lbGVtZW50ID0gZWw7XG4gICAgICAgIHRoaXMuc2FuZGJveCA9IGZhbHNlO1xuICAgICAgICB0aGlzLnBhcmVudEZyYW1lID0ge307XG4gICAgICAgIHRoaXMucGFyZW50QmxvY2sgPSB7fTsvL3JlZmVyZW5jZSB0byB0aGUgcGFyZW50IGJsb2NrIGVsZW1lbnRcblxuICAgICAgICAvL21ha2UgY3VycmVudCBlbGVtZW50IGFjdGl2ZS9vcGVuIChiZWluZyB3b3JrZWQgb24pXG4gICAgICAgIHRoaXMuc2V0T3BlbiA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKHRoaXMuZWxlbWVudCkub2ZmKCdtb3VzZWVudGVyIG1vdXNlbGVhdmUgY2xpY2snKTtcblxuICAgICAgICAgICAgaWYoICQodGhpcy5lbGVtZW50KS5jbG9zZXN0KCdib2R5Jykud2lkdGgoKSAhPT0gJCh0aGlzLmVsZW1lbnQpLndpZHRoKCkgKSB7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMuZWxlbWVudCkuY3NzKHsnb3V0bGluZSc6ICczcHggZGFzaGVkIHJlZCcsICdjdXJzb3InOiAncG9pbnRlcid9KTtcblxuICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICQodGhpcy5lbGVtZW50KS5jc3MoeydvdXRsaW5lJzogJzNweCBkYXNoZWQgcmVkJywgJ291dGxpbmUtb2Zmc2V0JzonLTNweCcsICAnY3Vyc29yJzogJ3BvaW50ZXInfSk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8vc2V0cyB1cCBob3ZlciBhbmQgY2xpY2sgZXZlbnRzLCBtYWtpbmcgdGhlIGVsZW1lbnQgYWN0aXZlIG9uIHRoZSBjYW52YXNcbiAgICAgICAgdGhpcy5hY3RpdmF0ZSA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB2YXIgZWxlbWVudCA9IHRoaXM7XG5cbiAgICAgICAgICAgICQodGhpcy5lbGVtZW50KS5jc3MoeydvdXRsaW5lJzogJ25vbmUnLCAnY3Vyc29yJzogJ2luaGVyaXQnfSk7XG5cbiAgICAgICAgICAgICQodGhpcy5lbGVtZW50KS5vbignbW91c2VlbnRlcicsIGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAgICAgaWYoICQodGhpcykuY2xvc2VzdCgnYm9keScpLndpZHRoKCkgIT09ICQodGhpcykud2lkdGgoKSApIHtcblxuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmNzcyh7J291dGxpbmUnOiAnM3B4IGRhc2hlZCByZWQnLCAnY3Vyc29yJzogJ3BvaW50ZXInfSk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgICAgICQodGhpcykuY3NzKHsnb3V0bGluZSc6ICczcHggZGFzaGVkIHJlZCcsICdvdXRsaW5lLW9mZnNldCc6ICctM3B4JywgJ2N1cnNvcic6ICdwb2ludGVyJ30pO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9KS5vbignbW91c2VsZWF2ZScsIGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5jc3MoeydvdXRsaW5lJzogJycsICdjdXJzb3InOiAnJywgJ291dGxpbmUtb2Zmc2V0JzogJyd9KTtcblxuICAgICAgICAgICAgfSkub24oJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xuXG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgICAgICBlbGVtZW50LmNsaWNrSGFuZGxlcih0aGlzKTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLmRlYWN0aXZhdGUgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCh0aGlzLmVsZW1lbnQpLm9mZignbW91c2VlbnRlciBtb3VzZWxlYXZlIGNsaWNrJyk7XG4gICAgICAgICAgICAkKHRoaXMuZWxlbWVudCkuY3NzKHsnb3V0bGluZSc6ICdub25lJywgJ2N1cnNvcic6ICdpbmhlcml0J30pO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgLy9yZW1vdmVzIHRoZSBlbGVtZW50cyBvdXRsaW5lXG4gICAgICAgIHRoaXMucmVtb3ZlT3V0bGluZSA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKHRoaXMuZWxlbWVudCkuY3NzKHsnb3V0bGluZSc6ICdub25lJywgJ2N1cnNvcic6ICdpbmhlcml0J30pO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgLy9zZXRzIHRoZSBwYXJlbnQgaWZyYW1lXG4gICAgICAgIHRoaXMuc2V0UGFyZW50RnJhbWUgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIGRvYyA9IHRoaXMuZWxlbWVudC5vd25lckRvY3VtZW50O1xuICAgICAgICAgICAgdmFyIHcgPSBkb2MuZGVmYXVsdFZpZXcgfHwgZG9jLnBhcmVudFdpbmRvdztcbiAgICAgICAgICAgIHZhciBmcmFtZXMgPSB3LnBhcmVudC5kb2N1bWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZSgnaWZyYW1lJyk7XG5cbiAgICAgICAgICAgIGZvciAodmFyIGk9IGZyYW1lcy5sZW5ndGg7IGktLT4wOykge1xuXG4gICAgICAgICAgICAgICAgdmFyIGZyYW1lPSBmcmFtZXNbaV07XG5cbiAgICAgICAgICAgICAgICB0cnkge1xuICAgICAgICAgICAgICAgICAgICB2YXIgZD0gZnJhbWUuY29udGVudERvY3VtZW50IHx8IGZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQ7XG4gICAgICAgICAgICAgICAgICAgIGlmIChkPT09ZG9jKVxuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5wYXJlbnRGcmFtZSA9IGZyYW1lO1xuICAgICAgICAgICAgICAgIH0gY2F0Y2goZSkge31cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8vc2V0cyB0aGlzIGVsZW1lbnQncyBwYXJlbnQgYmxvY2sgcmVmZXJlbmNlXG4gICAgICAgIHRoaXMuc2V0UGFyZW50QmxvY2sgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9sb29wIHRocm91Z2ggYWxsIHRoZSBibG9ja3Mgb24gdGhlIGNhbnZhc1xuICAgICAgICAgICAgZm9yKCB2YXIgaSA9IDA7IGkgPCBzaXRlQnVpbGRlci5zaXRlLnNpdGVQYWdlcy5sZW5ndGg7IGkrKyApIHtcblxuICAgICAgICAgICAgICAgIGZvciggdmFyIHggPSAwOyB4IDwgc2l0ZUJ1aWxkZXIuc2l0ZS5zaXRlUGFnZXNbaV0uYmxvY2tzLmxlbmd0aDsgeCsrICkge1xuXG4gICAgICAgICAgICAgICAgICAgIC8vaWYgdGhlIGJsb2NrJ3MgZnJhbWUgbWF0Y2hlcyB0aGlzIGVsZW1lbnQncyBwYXJlbnQgZnJhbWVcbiAgICAgICAgICAgICAgICAgICAgaWYoIHNpdGVCdWlsZGVyLnNpdGUuc2l0ZVBhZ2VzW2ldLmJsb2Nrc1t4XS5mcmFtZSA9PT0gdGhpcy5wYXJlbnRGcmFtZSApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vY3JlYXRlIGEgcmVmZXJlbmNlIHRvIHRoYXQgYmxvY2sgYW5kIHN0b3JlIGl0IGluIHRoaXMucGFyZW50QmxvY2tcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMucGFyZW50QmxvY2sgPSBzaXRlQnVpbGRlci5zaXRlLnNpdGVQYWdlc1tpXS5ibG9ja3NbeF07XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cblxuICAgICAgICB0aGlzLnNldFBhcmVudEZyYW1lKCk7XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGlzIHRoaXMgYmxvY2sgc2FuZGJveGVkP1xuICAgICAgICAqL1xuXG4gICAgICAgIGlmKCB0aGlzLnBhcmVudEZyYW1lLmdldEF0dHJpYnV0ZSgnZGF0YS1zYW5kYm94JykgKSB7XG4gICAgICAgICAgICB0aGlzLnNhbmRib3ggPSB0aGlzLnBhcmVudEZyYW1lLmdldEF0dHJpYnV0ZSgnZGF0YS1zYW5kYm94Jyk7XG4gICAgICAgIH1cblxuICAgIH07XG5cbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cbiAgICBtb2R1bGUuZXhwb3J0cy5wYWdlQ29udGFpbmVyID0gXCIjcGFnZVwiO1xuXG4gICAgbW9kdWxlLmV4cG9ydHMuZWRpdGFibGVJdGVtcyA9IHtcbiAgICAgICAgJ3NwYW4uZmEnOiBbJ2NvbG9yJywgJ2ZvbnQtc2l6ZSddLFxuICAgICAgICAnLmJnLmJnMSc6IFsnYmFja2dyb3VuZC1jb2xvciddLFxuICAgICAgICAnbmF2IGEsIGEuZWRpdCc6IFsnY29sb3InLCAnZm9udC13ZWlnaHQnLCAndGV4dC10cmFuc2Zvcm0nXSxcbiAgICAgICAgJ2gxLCBoMiwgaDMsIGg0LCBoNSwgcCc6IFsnY29sb3InLCAnZm9udC1zaXplJywgJ2JhY2tncm91bmQtY29sb3InLCAnZm9udC1mYW1pbHknXSxcbiAgICAgICAgJ2EuYnRuLCBidXR0b24uYnRuJzogWydib3JkZXItcmFkaXVzJywgJ2ZvbnQtc2l6ZScsICdiYWNrZ3JvdW5kLWNvbG9yJ10sXG5cbiAgICAgICAgJ2ltZyc6IFsnYm9yZGVyLXRvcC1sZWZ0LXJhZGl1cycsICdib3JkZXItdG9wLXJpZ2h0LXJhZGl1cycsICdib3JkZXItYm90dG9tLWxlZnQtcmFkaXVzJywgJ2JvcmRlci1ib3R0b20tcmlnaHQtcmFkaXVzJywgJ2JvcmRlci1jb2xvcicsICdib3JkZXItc3R5bGUnLCAnYm9yZGVyLXdpZHRoJ10sXG4gICAgICAgICdoci5kYXNoZWQnOiBbJ2JvcmRlci1jb2xvcicsICdib3JkZXItd2lkdGgnXSxcbiAgICAgICAgJy5kaXZpZGVyID4gc3Bhbic6IFsnY29sb3InLCAnZm9udC1zaXplJ10sXG4gICAgICAgICdoci5zaGFkb3dEb3duJzogWydtYXJnaW4tdG9wJywgJ21hcmdpbi1ib3R0b20nXSxcbiAgICAgICAgJy5mb290ZXIgYSc6IFsnY29sb3InXSxcbiAgICAgICAgJy5iZy5iZzEsIC5iZy5iZzIsIC5oZWFkZXIxMCwgLmhlYWRlcjExJzogWydiYWNrZ3JvdW5kLWltYWdlJywgJ2JhY2tncm91bmQtY29sb3InXSxcbiAgICAgICAgJy5mcmFtZUNvdmVyJzogW11cbiAgICB9O1xuXG4gICAgbW9kdWxlLmV4cG9ydHMuZWRpdGFibGVJdGVtT3B0aW9ucyA9IHtcbiAgICAgICAgJ25hdiBhIDogZm9udC13ZWlnaHQnOiBbJzQwMCcsICc3MDAnXSxcbiAgICAgICAgJ2EuYnRuIDogYm9yZGVyLXJhZGl1cyc6IFsnMHB4JywgJzRweCcsICcxMHB4J10sXG4gICAgICAgICdpbWcgOiBib3JkZXItc3R5bGUnOiBbJ25vbmUnLCAnZG90dGVkJywgJ2Rhc2hlZCcsICdzb2xpZCddLFxuICAgICAgICAnaW1nIDogYm9yZGVyLXdpZHRoJzogWycxcHgnLCAnMnB4JywgJzNweCcsICc0cHgnXSxcbiAgICAgICAgJ2gxLCBoMiwgaDMsIGg0LCBoNSwgcCA6IGZvbnQtZmFtaWx5JzogWydkZWZhdWx0JywgJ0xhdG8nLCAnSGVsdmV0aWNhJywgJ0FyaWFsJywgJ1RpbWVzIE5ldyBSb21hbiddLFxuICAgICAgICAnaDIgOiBmb250LWZhbWlseSc6IFsnZGVmYXVsdCcsICdMYXRvJywgJ0hlbHZldGljYScsICdBcmlhbCcsICdUaW1lcyBOZXcgUm9tYW4nXSxcbiAgICAgICAgJ2gzIDogZm9udC1mYW1pbHknOiBbJ2RlZmF1bHQnLCAnTGF0bycsICdIZWx2ZXRpY2EnLCAnQXJpYWwnLCAnVGltZXMgTmV3IFJvbWFuJ10sXG4gICAgICAgICdwIDogZm9udC1mYW1pbHknOiBbJ2RlZmF1bHQnLCAnTGF0bycsICdIZWx2ZXRpY2EnLCAnQXJpYWwnLCAnVGltZXMgTmV3IFJvbWFuJ11cbiAgICB9O1xuXG4gICAgbW9kdWxlLmV4cG9ydHMuZWRpdGFibGVDb250ZW50ID0gWycuZWRpdENvbnRlbnQnLCAnLm5hdmJhciBhJywgJ2J1dHRvbicsICdhLmJ0bicsICcuZm9vdGVyIGE6bm90KC5mYSknLCAnLnRhYmxlV3JhcHBlcicsICdoMScsICdoMiddO1xuXG4gICAgbW9kdWxlLmV4cG9ydHMuYXV0b1NhdmVUaW1lb3V0ID0gNjAwMDA7XG5cbiAgICBtb2R1bGUuZXhwb3J0cy5zb3VyY2VDb2RlRWRpdFN5bnRheERlbGF5ID0gMTAwMDA7XG5cbn0oKSk7IiwiKGZ1bmN0aW9uICgpe1xuXHRcInVzZSBzdHJpY3RcIjtcblxuICAgIHZhciBiQ29uZmlnID0gcmVxdWlyZSgnLi9jb25maWcuanMnKTtcbiAgICB2YXIgc2l0ZUJ1aWxkZXIgPSByZXF1aXJlKCcuL2J1aWxkZXIuanMnKTtcbiAgICB2YXIgZWRpdG9yID0gcmVxdWlyZSgnLi9zdHlsZWVkaXRvci5qcycpLnN0eWxlZWRpdG9yO1xuICAgIHZhciBhcHBVSSA9IHJlcXVpcmUoJy4vdWkuanMnKS5hcHBVSTtcblxuICAgIHZhciBpbWFnZUxpYnJhcnkgPSB7XG5cbiAgICAgICAgaW1hZ2VNb2RhbDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2ltYWdlTW9kYWwnKSxcbiAgICAgICAgaW5wdXRJbWFnZVVwbG9hZDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2ltYWdlRmlsZScpLFxuICAgICAgICBidXR0b25VcGxvYWRJbWFnZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3VwbG9hZEltYWdlQnV0dG9uJyksXG4gICAgICAgIGltYWdlTGlicmFyeUxpbmtzOiBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCcuaW1hZ2VzID4gLmltYWdlIC5idXR0b25zIC5idG4tcHJpbWFyeSwgLmltYWdlcyAuaW1hZ2VXcmFwID4gYScpLC8vdXNlZCBpbiB0aGUgbGlicmFyeSwgb3V0c2lkZSB0aGUgYnVpbGRlciBVSVxuICAgICAgICBteUltYWdlczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ215SW1hZ2VzJyksLy91c2VkIGluIHRoZSBpbWFnZSBsaWJyYXJ5LCBvdXRzaWRlIHRoZSBidWlsZGVyIFVJXG5cbiAgICAgICAgaW5pdDogZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgJCh0aGlzLmltYWdlTW9kYWwpLm9uKCdzaG93LmJzLm1vZGFsJywgdGhpcy5pbWFnZUxpYnJhcnkpO1xuICAgICAgICAgICAgJCh0aGlzLmlucHV0SW1hZ2VVcGxvYWQpLm9uKCdjaGFuZ2UnLCB0aGlzLmltYWdlSW5wdXRDaGFuZ2UpO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblVwbG9hZEltYWdlKS5vbignY2xpY2snLCB0aGlzLnVwbG9hZEltYWdlKTtcbiAgICAgICAgICAgICQodGhpcy5pbWFnZUxpYnJhcnlMaW5rcykub24oJ2NsaWNrJywgdGhpcy5pbWFnZUluTW9kYWwpO1xuICAgICAgICAgICAgJCh0aGlzLm15SW1hZ2VzKS5vbignY2xpY2snLCAnLmJ1dHRvbnMgLmJ0bi1kYW5nZXInLCB0aGlzLmRlbGV0ZUltYWdlKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGltYWdlIGxpYnJhcnkgbW9kYWxcbiAgICAgICAgKi9cbiAgICAgICAgaW1hZ2VMaWJyYXJ5OiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCgnI2ltYWdlTW9kYWwnKS5vZmYoJ2NsaWNrJywgJy5pbWFnZSBidXR0b24udXNlSW1hZ2UnKTtcblxuICAgICAgICAgICAgJCgnI2ltYWdlTW9kYWwnKS5vbignY2xpY2snLCAnLmltYWdlIGJ1dHRvbi51c2VJbWFnZScsIGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAvL3VwZGF0ZSBsaXZlIGltYWdlXG4gICAgICAgICAgICAgICAgJChlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdzcmMnLCAkKHRoaXMpLmF0dHIoJ2RhdGEtdXJsJykpO1xuXG4gICAgICAgICAgICAgICAgLy91cGRhdGUgaW1hZ2UgVVJMIGZpZWxkXG4gICAgICAgICAgICAgICAgJCgnaW5wdXQjaW1hZ2VVUkwnKS52YWwoICQodGhpcykuYXR0cignZGF0YS11cmwnKSApO1xuXG4gICAgICAgICAgICAgICAgLy9oaWRlIG1vZGFsXG4gICAgICAgICAgICAgICAgJCgnI2ltYWdlTW9kYWwnKS5tb2RhbCgnaGlkZScpO1xuXG4gICAgICAgICAgICAgICAgLy9oZWlnaHQgYWRqdXN0bWVudCBvZiB0aGUgaWZyYW1lIGhlaWdodEFkanVzdG1lbnRcblx0XHRcdFx0ZWRpdG9yLmFjdGl2ZUVsZW1lbnQucGFyZW50QmxvY2suaGVpZ2h0QWRqdXN0bWVudCgpO1xuXG4gICAgICAgICAgICAgICAgLy93ZSd2ZSBnb3QgcGVuZGluZyBjaGFuZ2VzXG4gICAgICAgICAgICAgICAgc2l0ZUJ1aWxkZXIuc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICAgICAgICAgICQodGhpcykudW5iaW5kKCdjbGljaycpO1xuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGltYWdlIHVwbG9hZCBpbnB1dCBjaGFuZWcgZXZlbnQgaGFuZGxlclxuICAgICAgICAqL1xuICAgICAgICBpbWFnZUlucHV0Q2hhbmdlOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgaWYoICQodGhpcykudmFsKCkgPT09ICcnICkge1xuICAgICAgICAgICAgICAgIC8vbm8gZmlsZSwgZGlzYWJsZSBzdWJtaXQgYnV0dG9uXG4gICAgICAgICAgICAgICAgJCgnYnV0dG9uI3VwbG9hZEltYWdlQnV0dG9uJykuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIC8vZ290IGEgZmlsZSwgZW5hYmxlIGJ1dHRvblxuICAgICAgICAgICAgICAgICQoJ2J1dHRvbiN1cGxvYWRJbWFnZUJ1dHRvbicpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgdXBsb2FkIGFuIGltYWdlIHRvIHRoZSBpbWFnZSBsaWJyYXJ5XG4gICAgICAgICovXG4gICAgICAgIHVwbG9hZEltYWdlOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgaWYoICQoJ2lucHV0I2ltYWdlRmlsZScpLnZhbCgpICE9PSAnJyApIHtcblxuICAgICAgICAgICAgICAgIC8vcmVtb3ZlIG9sZCBhbGVydHNcbiAgICAgICAgICAgICAgICAkKCcjaW1hZ2VNb2RhbCAubW9kYWwtYWxlcnRzID4gKicpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgLy9kaXNhYmxlIGJ1dHRvblxuICAgICAgICAgICAgICAgICQoJ2J1dHRvbiN1cGxvYWRJbWFnZUJ1dHRvbicpLmFkZENsYXNzKCdkaXNhYmxlJyk7XG5cbiAgICAgICAgICAgICAgICAvL3Nob3cgbG9hZGVyXG4gICAgICAgICAgICAgICAgJCgnI2ltYWdlTW9kYWwgLmxvYWRlcicpLmZhZGVJbig1MDApO1xuXG4gICAgICAgICAgICAgICAgdmFyIGZvcm0gPSAkKCdmb3JtI2ltYWdlVXBsb2FkRm9ybScpO1xuICAgICAgICAgICAgICAgIHZhciBmb3JtZGF0YSA9IGZhbHNlO1xuXG4gICAgICAgICAgICAgICAgaWYgKHdpbmRvdy5Gb3JtRGF0YSl7XG4gICAgICAgICAgICAgICAgICAgIGZvcm1kYXRhID0gbmV3IEZvcm1EYXRhKGZvcm1bMF0pO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHZhciBmb3JtQWN0aW9uID0gZm9ybS5hdHRyKCdhY3Rpb24nKTtcblxuICAgICAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgICAgIHVybCA6IGZvcm1BY3Rpb24sXG4gICAgICAgICAgICAgICAgICAgIGRhdGEgOiBmb3JtZGF0YSA/IGZvcm1kYXRhIDogZm9ybS5zZXJpYWxpemUoKSxcbiAgICAgICAgICAgICAgICAgICAgY2FjaGUgOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgY29udGVudFR5cGUgOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgcHJvY2Vzc0RhdGEgOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6IFwianNvblwiLFxuICAgICAgICAgICAgICAgICAgICB0eXBlIDogJ1BPU1QnXG4gICAgICAgICAgICAgICAgfSkuZG9uZShmdW5jdGlvbihyZXQpe1xuXG4gICAgICAgICAgICAgICAgICAgIC8vZW5hYmxlIGJ1dHRvblxuICAgICAgICAgICAgICAgICAgICAkKCdidXR0b24jdXBsb2FkSW1hZ2VCdXR0b24nKS5hZGRDbGFzcygnZGlzYWJsZScpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vaGlkZSBsb2FkZXJcbiAgICAgICAgICAgICAgICAgICAgJCgnI2ltYWdlTW9kYWwgLmxvYWRlcicpLmZhZGVPdXQoNTAwKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMCApIHsvL2Vycm9yXG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyNpbWFnZU1vZGFsIC5tb2RhbC1hbGVydHMnKS5hcHBlbmQoICQocmV0LnJlc3BvbnNlSFRNTCkgKTtcblxuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDEgKSB7Ly9zdWNjZXNzXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vYXBwZW5kIG15IGltYWdlXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjbXlJbWFnZXNUYWIgPiAqJykucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjbXlJbWFnZXNUYWInKS5hcHBlbmQoICQocmV0Lm15SW1hZ2VzKSApO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2ltYWdlTW9kYWwgLm1vZGFsLWFsZXJ0cycpLmFwcGVuZCggJChyZXQucmVzcG9uc2VIVE1MKSApO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7JCgnI2ltYWdlTW9kYWwgLm1vZGFsLWFsZXJ0cyA+IConKS5mYWRlT3V0KDUwMCk7fSwgMzAwMCk7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICBhbGVydCgnTm8gaW1hZ2Ugc2VsZWN0ZWQnKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgZGlzcGxheXMgaW1hZ2UgaW4gbW9kYWxcbiAgICAgICAgKi9cbiAgICAgICAgaW1hZ2VJbk1vZGFsOiBmdW5jdGlvbihlKSB7XG5cbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcblxuICAgIFx0XHR2YXIgdGhlU3JjID0gJCh0aGlzKS5jbG9zZXN0KCcuaW1hZ2UnKS5maW5kKCdpbWcnKS5hdHRyKCdzcmMnKTtcblxuICAgIFx0XHQkKCdpbWcjdGhlUGljJykuYXR0cignc3JjJywgdGhlU3JjKTtcblxuICAgIFx0XHQkKCcjdmlld1BpYycpLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBkZWxldGVzIGFuIGltYWdlIGZyb20gdGhlIGxpYnJhcnlcbiAgICAgICAgKi9cbiAgICAgICAgZGVsZXRlSW1hZ2U6IGZ1bmN0aW9uKGUpIHtcblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgXHRcdHZhciB0b0RlbCA9ICQodGhpcykuY2xvc2VzdCgnLmltYWdlJyk7XG4gICAgXHRcdHZhciB0aGVVUkwgPSAkKHRoaXMpLmF0dHIoJ2RhdGEtaW1nJyk7XG5cbiAgICBcdFx0JCgnI2RlbGV0ZUltYWdlTW9kYWwnKS5tb2RhbCgnc2hvdycpO1xuXG4gICAgXHRcdCQoJ2J1dHRvbiNkZWxldGVJbWFnZUJ1dHRvbicpLmNsaWNrKGZ1bmN0aW9uKCl7XG5cbiAgICBcdFx0XHQkKHRoaXMpLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgXHRcdFx0dmFyIHRoZUJ1dHRvbiA9ICQodGhpcyk7XG5cbiAgICBcdFx0XHQkLmFqYXgoe1xuICAgICAgICAgICAgICAgICAgICB1cmw6IGFwcFVJLnNpdGVVcmwrXCJhc3NldHMvZGVsSW1hZ2VcIixcbiAgICBcdFx0XHRcdGRhdGE6IHtmaWxlOiB0aGVVUkx9LFxuICAgIFx0XHRcdFx0dHlwZTogJ3Bvc3QnXG4gICAgXHRcdFx0fSkuZG9uZShmdW5jdGlvbigpe1xuXG4gICAgXHRcdFx0XHR0aGVCdXR0b24ucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICBcdFx0XHRcdCQoJyNkZWxldGVJbWFnZU1vZGFsJykubW9kYWwoJ2hpZGUnKTtcblxuICAgIFx0XHRcdFx0dG9EZWwuZmFkZU91dCg4MDAsIGZ1bmN0aW9uKCl7XG5cbiAgICBcdFx0XHRcdFx0JCh0aGlzKS5yZW1vdmUoKTtcblxuICAgIFx0XHRcdFx0fSk7XG5cbiAgICBcdFx0XHR9KTtcblxuXG4gICAgXHRcdH0pO1xuXG4gICAgICAgIH1cblxuICAgIH07XG5cbiAgICBpbWFnZUxpYnJhcnkuaW5pdCgpO1xuXG59KCkpOyIsIihmdW5jdGlvbiAoKXtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cblx0dmFyIGNhbnZhc0VsZW1lbnQgPSByZXF1aXJlKCcuL2NhbnZhc0VsZW1lbnQuanMnKS5FbGVtZW50O1xuXHR2YXIgYkNvbmZpZyA9IHJlcXVpcmUoJy4vY29uZmlnLmpzJyk7XG5cdHZhciBzaXRlQnVpbGRlciA9IHJlcXVpcmUoJy4vYnVpbGRlci5qcycpO1xuXG4gICAgdmFyIHN0eWxlZWRpdG9yID0ge1xuXG4gICAgICAgIHJhZGlvU3R5bGU6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtb2RlU3R5bGUnKSxcbiAgICAgICAgbGFiZWxTdHlsZU1vZGU6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtb2RlU3R5bGVMYWJlbCcpLFxuICAgICAgICBidXR0b25TYXZlQ2hhbmdlczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NhdmVTdHlsaW5nJyksXG4gICAgICAgIGFjdGl2ZUVsZW1lbnQ6IHt9LCAvL2hvbGRzIHRoZSBlbGVtZW50IGN1cnJlbnR5IGJlaW5nIGVkaXRlZFxuICAgICAgICBhbGxTdHlsZUl0ZW1zT25DYW52YXM6IFtdLFxuICAgICAgICBfb2xkSWNvbjogW10sXG4gICAgICAgIHN0eWxlRWRpdG9yOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc3R5bGVFZGl0b3InKSxcbiAgICAgICAgZm9ybVN0eWxlOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc3R5bGluZ0Zvcm0nKSxcbiAgICAgICAgYnV0dG9uUmVtb3ZlRWxlbWVudDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2RlbGV0ZUVsZW1lbnRDb25maXJtJyksXG4gICAgICAgIGJ1dHRvbkNsb25lRWxlbWVudDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2Nsb25lRWxlbWVudEJ1dHRvbicpLFxuICAgICAgICBidXR0b25SZXNldEVsZW1lbnQ6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdyZXNldFN0eWxlQnV0dG9uJyksXG4gICAgICAgIHNlbGVjdExpbmtzSW5lcm5hbDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2ludGVybmFsTGlua3NEcm9wZG93bicpLFxuICAgICAgICBzZWxlY3RMaW5rc1BhZ2VzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncGFnZUxpbmtzRHJvcGRvd24nKSxcbiAgICAgICAgdmlkZW9JbnB1dFlvdXR1YmU6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCd5b3V0dWJlSUQnKSxcbiAgICAgICAgdmlkZW9JbnB1dFZpbWVvOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgndmltZW9JRCcpLFxuICAgICAgICBpbnB1dEN1c3RvbUxpbms6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdpbnRlcm5hbExpbmtzQ3VzdG9tJyksXG4gICAgICAgIHNlbGVjdEljb25zOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnaWNvbnMnKSxcbiAgICAgICAgYnV0dG9uRGV0YWlsc0FwcGxpZWRIaWRlOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZGV0YWlsc0FwcGxpZWRNZXNzYWdlSGlkZScpLFxuICAgICAgICBidXR0b25DbG9zZVN0eWxlRWRpdG9yOiBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjc3R5bGVFZGl0b3IgPiBhLmNsb3NlJyksXG4gICAgICAgIHVsUGFnZUxpc3Q6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwYWdlTGlzdCcpLFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2V2ZW50c1xuICAgICAgICAgICAgJCh0aGlzLnJhZGlvU3R5bGUpLm9uKCdjbGljaycsIHRoaXMuYWN0aXZhdGVTdHlsZU1vZGUpO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblNhdmVDaGFuZ2VzKS5vbignY2xpY2snLCB0aGlzLnVwZGF0ZVN0eWxpbmcpO1xuICAgICAgICAgICAgJCh0aGlzLmZvcm1TdHlsZSkub24oJ2ZvY3VzJywgJ2lucHV0JywgdGhpcy5hbmltYXRlU3R5bGVJbnB1dEluKS5vbignYmx1cicsICdpbnB1dCcsIHRoaXMuYW5pbWF0ZVN0eWxlSW5wdXRPdXQpO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblJlbW92ZUVsZW1lbnQpLm9uKCdjbGljaycsIHRoaXMuZGVsZXRlRWxlbWVudCk7XG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uQ2xvbmVFbGVtZW50KS5vbignY2xpY2snLCB0aGlzLmNsb25lRWxlbWVudCk7XG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uUmVzZXRFbGVtZW50KS5vbignY2xpY2snLCB0aGlzLnJlc2V0RWxlbWVudCk7XG4gICAgICAgICAgICAkKHRoaXMuc2VsZWN0TGlua3NJbmVybmFsKS5vbignY2hhbmdlJywgdGhpcy5yZXNldFNlbGVjdExpbmtzSW50ZXJuYWwpO1xuICAgICAgICAgICAgJCh0aGlzLnNlbGVjdExpbmtzUGFnZXMpLm9uKCdjaGFuZ2UnLCB0aGlzLnJlc2V0U2VsZWN0TGlua3NQYWdlcyk7XG4gICAgICAgICAgICAkKHRoaXMudmlkZW9JbnB1dFlvdXR1YmUpLm9uKCdmb2N1cycsIGZ1bmN0aW9uKCl7ICQoc3R5bGVlZGl0b3IudmlkZW9JbnB1dFZpbWVvKS52YWwoJycpOyB9KTtcbiAgICAgICAgICAgICQodGhpcy52aWRlb0lucHV0VmltZW8pLm9uKCdmb2N1cycsIGZ1bmN0aW9uKCl7ICQoc3R5bGVlZGl0b3IudmlkZW9JbnB1dFlvdXR1YmUpLnZhbCgnJyk7IH0pO1xuICAgICAgICAgICAgJCh0aGlzLmlucHV0Q3VzdG9tTGluaykub24oJ2ZvY3VzJywgdGhpcy5yZXNldFNlbGVjdEFsbExpbmtzKTtcbiAgICAgICAgICAgICQodGhpcy5idXR0b25EZXRhaWxzQXBwbGllZEhpZGUpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCl7JCh0aGlzKS5wYXJlbnQoKS5mYWRlT3V0KDUwMCk7fSk7XG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uQ2xvc2VTdHlsZUVkaXRvcikub24oJ2NsaWNrJywgdGhpcy5jbG9zZVN0eWxlRWRpdG9yKTtcbiAgICAgICAgICAgICQoZG9jdW1lbnQpLm9uKCdtb2RlQ29udGVudCBtb2RlQmxvY2tzJywgJ2JvZHknLCB0aGlzLmRlQWN0aXZhdGVNb2RlKTtcblxuICAgICAgICAgICAgLy9jaG9zZW4gZm9udC1hd2Vzb21lIGRyb3Bkb3duXG4gICAgICAgICAgICAkKHRoaXMuc2VsZWN0SWNvbnMpLmNob3Nlbih7J3NlYXJjaF9jb250YWlucyc6IHRydWV9KTtcblxuICAgICAgICAgICAgLy9jaGVjayBpZiBmb3JtRGF0YSBpcyBzdXBwb3J0ZWRcbiAgICAgICAgICAgIGlmICghd2luZG93LkZvcm1EYXRhKXtcbiAgICAgICAgICAgICAgICB0aGlzLmhpZGVGaWxlVXBsb2FkcygpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL3Nob3cgdGhlIHN0eWxlIG1vZGUgcmFkaW8gYnV0dG9uXG4gICAgICAgICAgICAkKHRoaXMubGFiZWxTdHlsZU1vZGUpLnNob3coKTtcblxuICAgICAgICAgICAgLy9saXN0ZW4gZm9yIHRoZSBiZWZvcmVTYXZlIGV2ZW50XG4gICAgICAgICAgICAkKCdib2R5Jykub24oJ2JlZm9yZVNhdmUnLCB0aGlzLmNsb3NlU3R5bGVFZGl0b3IpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgQWN0aXZhdGVzIHN0eWxlIGVkaXRvciBtb2RlXG4gICAgICAgICovXG4gICAgICAgIGFjdGl2YXRlU3R5bGVNb2RlOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIGk7XG5cbiAgICAgICAgICAgIC8vRWxlbWVudCBvYmplY3QgZXh0ZW50aW9uXG4gICAgICAgICAgICBjYW52YXNFbGVtZW50LnByb3RvdHlwZS5jbGlja0hhbmRsZXIgPSBmdW5jdGlvbihlbCkge1xuICAgICAgICAgICAgICAgIHN0eWxlZWRpdG9yLnN0eWxlQ2xpY2soZWwpO1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy8gUmVtb3ZlIG92ZXJsYXkgc3BhbiBmcm9tIHBvcnRmb2xpb1xuICAgICAgICAgICAgZm9yKGkgPSAxOyBpIDw9ICQoXCJ1bCNwYWdlMSBsaVwiKS5sZW5ndGg7IGkrKyl7XG4gICAgICAgICAgICAgICAgdmFyIGlkID0gXCIjdWktaWQtXCIgKyBpO1xuICAgICAgICAgICAgICAgICQoaWQpLmNvbnRlbnRzKCkuZmluZChcIi5vdmVybGF5XCIpLnJlbW92ZSgpO1xuICAgICAgICAgICAgfVxuXG5cbiAgICAgICAgICAgIC8vdHJpZ2dlciBjdXN0b20gZXZlbnRcbiAgICAgICAgICAgICQoJ2JvZHknKS50cmlnZ2VyKCdtb2RlRGV0YWlscycpO1xuXG4gICAgICAgICAgICAvL2Rpc2FibGUgZnJhbWVDb3ZlcnNcbiAgICAgICAgICAgIGZvciggaSA9IDA7IGkgPCBzaXRlQnVpbGRlci5zaXRlLnNpdGVQYWdlcy5sZW5ndGg7IGkrKyApIHtcbiAgICAgICAgICAgICAgICBzaXRlQnVpbGRlci5zaXRlLnNpdGVQYWdlc1tpXS50b2dnbGVGcmFtZUNvdmVycygnT2ZmJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vY3JlYXRlIGFuIG9iamVjdCBmb3IgZXZlcnkgZWRpdGFibGUgZWxlbWVudCBvbiB0aGUgY2FudmFzIGFuZCBzZXR1cCBpdCdzIGV2ZW50c1xuXG4gICAgICAgICAgICBmb3IoIGkgPSAwOyBpIDwgc2l0ZUJ1aWxkZXIuc2l0ZS5zaXRlUGFnZXMubGVuZ3RoOyBpKysgKSB7XG5cbiAgICAgICAgICAgICAgICBmb3IoIHZhciB4ID0gMDsgeCA8IHNpdGVCdWlsZGVyLnNpdGUuc2l0ZVBhZ2VzW2ldLmJsb2Nrcy5sZW5ndGg7IHgrKyApIHtcblxuICAgICAgICAgICAgICAgICAgICBmb3IoIHZhciBrZXkgaW4gYkNvbmZpZy5lZGl0YWJsZUl0ZW1zICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKHNpdGVCdWlsZGVyLnNpdGUuc2l0ZVBhZ2VzW2ldLmJsb2Nrc1t4XS5mcmFtZSkuY29udGVudHMoKS5maW5kKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKyAnICcrIGtleSApLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBuZXdFbGVtZW50ID0gbmV3IGNhbnZhc0VsZW1lbnQodGhpcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBuZXdFbGVtZW50LmFjdGl2YXRlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzdHlsZWVkaXRvci5hbGxTdHlsZUl0ZW1zT25DYW52YXMucHVzaCggbmV3RWxlbWVudCApO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hdHRyKCdkYXRhLXNlbGVjdG9yJywga2V5KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8qJCgnI3BhZ2VMaXN0IHVsIGxpIGlmcmFtZScpLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgIGZvciggdmFyIGtleSBpbiBiQ29uZmlnLmVkaXRhYmxlSXRlbXMgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciArICcgJysga2V5ICkuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgbmV3RWxlbWVudCA9IG5ldyBjYW52YXNFbGVtZW50KHRoaXMpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBuZXdFbGVtZW50LmFjdGl2YXRlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHN0eWxlZWRpdG9yLmFsbFN0eWxlSXRlbXNPbkNhbnZhcy5wdXNoKCBuZXdFbGVtZW50ICk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuYXR0cignZGF0YS1zZWxlY3RvcicsIGtleSk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0pOyovXG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBFdmVudCBoYW5kbGVyIGZvciB3aGVuIHRoZSBzdHlsZSBlZGl0b3IgaXMgZW52b2tlZCBvbiBhbiBpdGVtXG4gICAgICAgICovXG4gICAgICAgIHN0eWxlQ2xpY2s6IGZ1bmN0aW9uKGVsKSB7XG5cbiAgICAgICAgICAgIC8vaWYgd2UgaGF2ZSBhbiBhY3RpdmUgZWxlbWVudCwgbWFrZSBpdCB1bmFjdGl2ZVxuICAgICAgICAgICAgaWYoIE9iamVjdC5rZXlzKHRoaXMuYWN0aXZlRWxlbWVudCkubGVuZ3RoICE9PSAwKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5hY3RpdmVFbGVtZW50LmFjdGl2YXRlKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vc2V0IHRoZSBhY3RpdmUgZWxlbWVudFxuICAgICAgICAgICAgdmFyIGFjdGl2ZUVsZW1lbnQgPSBuZXcgY2FudmFzRWxlbWVudChlbCk7XG4gICAgICAgICAgICBhY3RpdmVFbGVtZW50LnNldFBhcmVudEJsb2NrKCk7XG4gICAgICAgICAgICB0aGlzLmFjdGl2ZUVsZW1lbnQgPSBhY3RpdmVFbGVtZW50O1xuXG4gICAgICAgICAgICAvL3VuYmluZCBob3ZlciBhbmQgY2xpY2sgZXZlbnRzIGFuZCBtYWtlIHRoaXMgaXRlbSBhY3RpdmVcbiAgICAgICAgICAgIHRoaXMuYWN0aXZlRWxlbWVudC5zZXRPcGVuKCk7XG5cbiAgICAgICAgICAgIHZhciB0aGVTZWxlY3RvciA9ICQodGhpcy5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmF0dHIoJ2RhdGEtc2VsZWN0b3InKTtcblxuICAgICAgICAgICAgJCgnI2VkaXRpbmdFbGVtZW50JykudGV4dCggdGhlU2VsZWN0b3IgKTtcblxuICAgICAgICAgICAgLy9hY3RpdmF0ZSBmaXJzdCB0YWJcbiAgICAgICAgICAgICQoJyNkZXRhaWxUYWJzIGE6Zmlyc3QnKS5jbGljaygpO1xuXG4gICAgICAgICAgICAvL2hpZGUgYWxsIGJ5IGRlZmF1bHRcbiAgICAgICAgICAgICQoJ3VsI2RldGFpbFRhYnMgbGk6Z3QoMCknKS5oaWRlKCk7XG5cbiAgICAgICAgICAgIC8vd2hhdCBhcmUgd2UgZGVhbGluZyB3aXRoP1xuICAgICAgICAgICAgaWYoICQodGhpcy5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnByb3AoJ3RhZ05hbWUnKSA9PT0gJ0EnIHx8ICQodGhpcy5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnBhcmVudCgpLnByb3AoJ3RhZ05hbWUnKSA9PT0gJ0EnICkge1xuXG4gICAgICAgICAgICAgICAgdGhpcy5lZGl0TGluayh0aGlzLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCk7XG5cbiAgICAgICAgICAgIH1cblxuXHRcdFx0aWYoICQodGhpcy5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnByb3AoJ3RhZ05hbWUnKSA9PT0gJ0lNRycgKXtcblxuICAgICAgICAgICAgICAgIHRoaXMuZWRpdEltYWdlKHRoaXMuYWN0aXZlRWxlbWVudC5lbGVtZW50KTtcblxuICAgICAgICAgICAgfVxuXG5cdFx0XHRpZiggJCh0aGlzLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignZGF0YS10eXBlJykgPT09ICd2aWRlbycgKSB7XG5cbiAgICAgICAgICAgICAgICB0aGlzLmVkaXRWaWRlbyh0aGlzLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCk7XG5cbiAgICAgICAgICAgIH1cblxuXHRcdFx0aWYoICQodGhpcy5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmhhc0NsYXNzKCdmYScpICkge1xuXG4gICAgICAgICAgICAgICAgdGhpcy5lZGl0SWNvbih0aGlzLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9sb2FkIHRoZSBhdHRyaWJ1dGVzXG4gICAgICAgICAgICB0aGlzLmJ1aWxkZVN0eWxlRWxlbWVudHModGhlU2VsZWN0b3IpO1xuXG4gICAgICAgICAgICAvL29wZW4gc2lkZSBwYW5lbFxuICAgICAgICAgICAgdGhpcy50b2dnbGVTaWRlUGFuZWwoJ29wZW4nKTtcbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBkeW5hbWljYWxseSBnZW5lcmF0ZXMgdGhlIGZvcm0gZmllbGRzIGZvciBlZGl0aW5nIGFuIGVsZW1lbnRzIHN0eWxlIGF0dHJpYnV0ZXNcbiAgICAgICAgKi9cbiAgICAgICAgYnVpbGRlU3R5bGVFbGVtZW50czogZnVuY3Rpb24odGhlU2VsZWN0b3IpIHtcblxuICAgICAgICAgICAgLy9kZWxldGUgdGhlIG9sZCBvbmVzIGZpcnN0XG4gICAgICAgICAgICAkKCcjc3R5bGVFbGVtZW50cyA+ICo6bm90KCNzdHlsZUVsVGVtcGxhdGUpJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIGZvciggdmFyIHg9MDsgeDxiQ29uZmlnLmVkaXRhYmxlSXRlbXNbdGhlU2VsZWN0b3JdLmxlbmd0aDsgeCsrICkge1xuXG4gICAgICAgICAgICAgICAgLy9jcmVhdGUgc3R5bGUgZWxlbWVudHNcbiAgICAgICAgICAgICAgICB2YXIgbmV3U3R5bGVFbCA9ICQoJyNzdHlsZUVsVGVtcGxhdGUnKS5jbG9uZSgpO1xuICAgICAgICAgICAgICAgIG5ld1N0eWxlRWwuYXR0cignaWQnLCAnJyk7XG4gICAgICAgICAgICAgICAgbmV3U3R5bGVFbC5maW5kKCcuY29udHJvbC1sYWJlbCcpLnRleHQoIGJDb25maWcuZWRpdGFibGVJdGVtc1t0aGVTZWxlY3Rvcl1beF0rXCI6XCIgKTtcblxuICAgICAgICAgICAgICAgIGlmKCB0aGVTZWxlY3RvciArIFwiIDogXCIgKyBiQ29uZmlnLmVkaXRhYmxlSXRlbXNbdGhlU2VsZWN0b3JdW3hdIGluIGJDb25maWcuZWRpdGFibGVJdGVtT3B0aW9ucykgey8vd2UndmUgZ290IGEgZHJvcGRvd24gaW5zdGVhZCBvZiBvcGVuIHRleHQgaW5wdXRcblxuICAgICAgICAgICAgICAgICAgICBuZXdTdHlsZUVsLmZpbmQoJ2lucHV0JykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIG5ld0Ryb3BEb3duID0gJCgnPHNlbGVjdCBjbGFzcz1cImZvcm0tY29udHJvbCBzZWxlY3Qgc2VsZWN0LXByaW1hcnkgYnRuLWJsb2NrIHNlbGVjdC1zbVwiPjwvc2VsZWN0PicpO1xuICAgICAgICAgICAgICAgICAgICBuZXdEcm9wRG93bi5hdHRyKCduYW1lJywgYkNvbmZpZy5lZGl0YWJsZUl0ZW1zW3RoZVNlbGVjdG9yXVt4XSk7XG5cblxuICAgICAgICAgICAgICAgICAgICBmb3IoIHZhciB6PTA7IHo8YkNvbmZpZy5lZGl0YWJsZUl0ZW1PcHRpb25zWyB0aGVTZWxlY3RvcitcIiA6IFwiK2JDb25maWcuZWRpdGFibGVJdGVtc1t0aGVTZWxlY3Rvcl1beF0gXS5sZW5ndGg7IHorKyApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIG5ld09wdGlvbiA9ICQoJzxvcHRpb24gdmFsdWU9XCInK2JDb25maWcuZWRpdGFibGVJdGVtT3B0aW9uc1t0aGVTZWxlY3RvcitcIiA6IFwiK2JDb25maWcuZWRpdGFibGVJdGVtc1t0aGVTZWxlY3Rvcl1beF1dW3pdKydcIj4nK2JDb25maWcuZWRpdGFibGVJdGVtT3B0aW9uc1t0aGVTZWxlY3RvcitcIiA6IFwiK2JDb25maWcuZWRpdGFibGVJdGVtc1t0aGVTZWxlY3Rvcl1beF1dW3pdKyc8L29wdGlvbj4nKTtcblxuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiggYkNvbmZpZy5lZGl0YWJsZUl0ZW1PcHRpb25zW3RoZVNlbGVjdG9yK1wiIDogXCIrYkNvbmZpZy5lZGl0YWJsZUl0ZW1zW3RoZVNlbGVjdG9yXVt4XV1bel0gPT09ICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5jc3MoIGJDb25maWcuZWRpdGFibGVJdGVtc1t0aGVTZWxlY3Rvcl1beF0gKSApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvL2N1cnJlbnQgdmFsdWUsIG1hcmtlZCBhcyBzZWxlY3RlZFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG5ld09wdGlvbi5hdHRyKCdzZWxlY3RlZCcsICd0cnVlJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgbmV3RHJvcERvd24uYXBwZW5kKCBuZXdPcHRpb24gKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgbmV3U3R5bGVFbC5hcHBlbmQoIG5ld0Ryb3BEb3duICk7XG4gICAgICAgICAgICAgICAgICAgIG5ld0Ryb3BEb3duLnNlbGVjdDIoKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAgICAgbmV3U3R5bGVFbC5maW5kKCdpbnB1dCcpLnZhbCggJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmNzcyggYkNvbmZpZy5lZGl0YWJsZUl0ZW1zW3RoZVNlbGVjdG9yXVt4XSApICkuYXR0cignbmFtZScsIGJDb25maWcuZWRpdGFibGVJdGVtc1t0aGVTZWxlY3Rvcl1beF0pO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCBiQ29uZmlnLmVkaXRhYmxlSXRlbXNbdGhlU2VsZWN0b3JdW3hdID09PSAnYmFja2dyb3VuZC1pbWFnZScgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld1N0eWxlRWwuZmluZCgnaW5wdXQnKS5iaW5kKCdmb2N1cycsIGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgdGhlSW5wdXQgPSAkKHRoaXMpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2ltYWdlTW9kYWwnKS5tb2RhbCgnc2hvdycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoJyNpbWFnZU1vZGFsIC5pbWFnZSBidXR0b24udXNlSW1hZ2UnKS51bmJpbmQoJ2NsaWNrJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2ltYWdlTW9kYWwnKS5vbignY2xpY2snLCAnLmltYWdlIGJ1dHRvbi51c2VJbWFnZScsIGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmNzcygnYmFja2dyb3VuZC1pbWFnZScsICAndXJsKFwiJyskKHRoaXMpLmF0dHIoJ2RhdGEtdXJsJykrJ1wiKScpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vdXBkYXRlIGxpdmUgaW1hZ2VcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhlSW5wdXQudmFsKCAndXJsKFwiJyskKHRoaXMpLmF0dHIoJ2RhdGEtdXJsJykrJ1wiKScgKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvL2hpZGUgbW9kYWxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2ltYWdlTW9kYWwnKS5tb2RhbCgnaGlkZScpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vd2UndmUgZ290IHBlbmRpbmcgY2hhbmdlc1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzaXRlQnVpbGRlci5zaXRlLnNldFBlbmRpbmdDaGFuZ2VzKHRydWUpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggYkNvbmZpZy5lZGl0YWJsZUl0ZW1zW3RoZVNlbGVjdG9yXVt4XS5pbmRleE9mKFwiY29sb3JcIikgPiAtMSApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5jc3MoIGJDb25maWcuZWRpdGFibGVJdGVtc1t0aGVTZWxlY3Rvcl1beF0gKSAhPT0gJ3RyYW5zcGFyZW50JyAmJiAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuY3NzKCBiQ29uZmlnLmVkaXRhYmxlSXRlbXNbdGhlU2VsZWN0b3JdW3hdICkgIT09ICdub25lJyAmJiAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuY3NzKCBiQ29uZmlnLmVkaXRhYmxlSXRlbXNbdGhlU2VsZWN0b3JdW3hdICkgIT09ICcnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbmV3U3R5bGVFbC52YWwoICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5jc3MoIGJDb25maWcuZWRpdGFibGVJdGVtc1t0aGVTZWxlY3Rvcl1beF0gKSApO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld1N0eWxlRWwuZmluZCgnaW5wdXQnKS5zcGVjdHJ1bSh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcHJlZmVycmVkRm9ybWF0OiBcImhleFwiLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNob3dQYWxldHRlOiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGFsbG93RW1wdHk6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2hvd0lucHV0OiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHBhbGV0dGU6IFtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgW1wiIzAwMFwiLFwiIzQ0NFwiLFwiIzY2NlwiLFwiIzk5OVwiLFwiI2NjY1wiLFwiI2VlZVwiLFwiI2YzZjNmM1wiLFwiI2ZmZlwiXSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgW1wiI2YwMFwiLFwiI2Y5MFwiLFwiI2ZmMFwiLFwiIzBmMFwiLFwiIzBmZlwiLFwiIzAwZlwiLFwiIzkwZlwiLFwiI2YwZlwiXSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgW1wiI2Y0Y2NjY1wiLFwiI2ZjZTVjZFwiLFwiI2ZmZjJjY1wiLFwiI2Q5ZWFkM1wiLFwiI2QwZTBlM1wiLFwiI2NmZTJmM1wiLFwiI2Q5ZDJlOVwiLFwiI2VhZDFkY1wiXSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgW1wiI2VhOTk5OVwiLFwiI2Y5Y2I5Y1wiLFwiI2ZmZTU5OVwiLFwiI2I2ZDdhOFwiLFwiI2EyYzRjOVwiLFwiIzlmYzVlOFwiLFwiI2I0YTdkNlwiLFwiI2Q1YTZiZFwiXSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgW1wiI2UwNjY2NlwiLFwiI2Y2YjI2YlwiLFwiI2ZmZDk2NlwiLFwiIzkzYzQ3ZFwiLFwiIzc2YTVhZlwiLFwiIzZmYThkY1wiLFwiIzhlN2NjM1wiLFwiI2MyN2JhMFwiXSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgW1wiI2MwMFwiLFwiI2U2OTEzOFwiLFwiI2YxYzIzMlwiLFwiIzZhYTg0ZlwiLFwiIzQ1ODE4ZVwiLFwiIzNkODVjNlwiLFwiIzY3NGVhN1wiLFwiI2E2NGQ3OVwiXSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgW1wiIzkwMFwiLFwiI2I0NWYwNlwiLFwiI2JmOTAwMFwiLFwiIzM4NzYxZFwiLFwiIzEzNGY1Y1wiLFwiIzBiNTM5NFwiLFwiIzM1MWM3NVwiLFwiIzc0MWI0N1wiXSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgW1wiIzYwMFwiLFwiIzc4M2YwNFwiLFwiIzdmNjAwMFwiLFwiIzI3NGUxM1wiLFwiIzBjMzQzZFwiLFwiIzA3Mzc2M1wiLFwiIzIwMTI0ZFwiLFwiIzRjMTEzMFwiXVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIF1cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIG5ld1N0eWxlRWwuY3NzKCdkaXNwbGF5JywgJ2Jsb2NrJyk7XG5cbiAgICAgICAgICAgICAgICAkKCcjc3R5bGVFbGVtZW50cycpLmFwcGVuZCggbmV3U3R5bGVFbCApO1xuXG4gICAgICAgICAgICAgICAgJCgnI3N0eWxlRWRpdG9yIGZvcm0jc3R5bGluZ0Zvcm0nKS5oZWlnaHQoJ2F1dG8nKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgQXBwbGllcyB1cGRhdGVkIHN0eWxpbmcgdG8gdGhlIGNhbnZhc1xuICAgICAgICAqL1xuICAgICAgICB1cGRhdGVTdHlsaW5nOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIGVsZW1lbnRJRDtcblxuICAgICAgICAgICAgJCgnI3N0eWxlRWRpdG9yICN0YWIxIC5mb3JtLWdyb3VwOm5vdCgjc3R5bGVFbFRlbXBsYXRlKSBpbnB1dCwgI3N0eWxlRWRpdG9yICN0YWIxIC5mb3JtLWdyb3VwOm5vdCgjc3R5bGVFbFRlbXBsYXRlKSBzZWxlY3QnKS5lYWNoKGZ1bmN0aW9uKCl7XG5cblx0XHRcdFx0aWYoICQodGhpcykuYXR0cignbmFtZScpICE9PSB1bmRlZmluZWQgKSB7XG5cbiAgICAgICAgICAgICAgICBcdCQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5jc3MoICQodGhpcykuYXR0cignbmFtZScpLCAgJCh0aGlzKS52YWwoKSk7XG5cblx0XHRcdFx0fVxuXG4gICAgICAgICAgICAgICAgLyogU0FOREJPWCAqL1xuXG4gICAgICAgICAgICAgICAgaWYoIHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCApIHtcblxuICAgICAgICAgICAgICAgICAgICBlbGVtZW50SUQgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignaWQnKTtcblxuICAgICAgICAgICAgICAgICAgICAkKCcjJytzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LnNhbmRib3gpLmNvbnRlbnRzKCkuZmluZCgnIycrZWxlbWVudElEKS5jc3MoICQodGhpcykuYXR0cignbmFtZScpLCAgJCh0aGlzKS52YWwoKSApO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLyogRU5EIFNBTkRCT1ggKi9cblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8vbGlua3NcbiAgICAgICAgICAgIGlmKCAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucHJvcCgndGFnTmFtZScpID09PSAnQScgKSB7XG5cbiAgICAgICAgICAgICAgICAvL2NoYW5nZSB0aGUgaHJlZiBwcm9wP1xuICAgICAgICAgICAgICAgIGlmKCAkKCdzZWxlY3QjaW50ZXJuYWxMaW5rc0Ryb3Bkb3duJykudmFsKCkgIT09ICcjJyApIHtcblxuICAgICAgICAgICAgICAgICAgICAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignaHJlZicsICQoJ3NlbGVjdCNpbnRlcm5hbExpbmtzRHJvcGRvd24nKS52YWwoKSk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoICQoJ3NlbGVjdCNwYWdlTGlua3NEcm9wZG93bicpLnZhbCgpICE9PSAnIycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmF0dHIoJ2hyZWYnLCAkKCdzZWxlY3QjcGFnZUxpbmtzRHJvcGRvd24nKS52YWwoKSApO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmKCAkKCdpbnB1dCNpbnRlcm5hbExpbmtzQ3VzdG9tJykudmFsKCkgIT09ICcnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdocmVmJywgJCgnaW5wdXQjaW50ZXJuYWxMaW5rc0N1c3RvbScpLnZhbCgpKTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8qIFNBTkRCT1ggKi9cblxuICAgICAgICAgICAgICAgIGlmKCBzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LnNhbmRib3ggKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgZWxlbWVudElEID0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmF0dHIoJ2lkJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoICQoJ3NlbGVjdCNpbnRlcm5hbExpbmtzRHJvcGRvd24nKS52YWwoKSAhPT0gJyMnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjJytzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LnNhbmRib3gpLmNvbnRlbnRzKCkuZmluZCgnIycrZWxlbWVudElEKS5hdHRyKCdocmVmJywgJCgnc2VsZWN0I2ludGVybmFsTGlua3NEcm9wZG93bicpLnZhbCgpKTtcblxuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYoICQoJ3NlbGVjdCNwYWdlTGlua3NEcm9wZG93bicpLnZhbCgpICE9PSAnIycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyMnK3N0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCkuY29udGVudHMoKS5maW5kKCcjJytlbGVtZW50SUQpLmF0dHIoJ2hyZWYnLCAkKCdzZWxlY3QjcGFnZUxpbmtzRHJvcGRvd24nKS52YWwoKSApO1xuXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggJCgnaW5wdXQjaW50ZXJuYWxMaW5rc0N1c3RvbScpLnZhbCgpICE9PSAnJyApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnIycrc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94KS5jb250ZW50cygpLmZpbmQoJyMnK2VsZW1lbnRJRCkuYXR0cignaHJlZicsICQoJ2lucHV0I2ludGVybmFsTGlua3NDdXN0b20nKS52YWwoKSk7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLyogRU5EIFNBTkRCT1ggKi9cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiggJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnBhcmVudCgpLnByb3AoJ3RhZ05hbWUnKSA9PT0gJ0EnICkge1xuXG4gICAgICAgICAgICAgICAgLy9jaGFuZ2UgdGhlIGhyZWYgcHJvcD9cblx0XHRcdFx0aWYoICQoJ3NlbGVjdCNpbnRlcm5hbExpbmtzRHJvcGRvd24nKS52YWwoKSAhPT0gJyMnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5wYXJlbnQoKS5hdHRyKCdocmVmJywgJCgnc2VsZWN0I2ludGVybmFsTGlua3NEcm9wZG93bicpLnZhbCgpKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggJCgnc2VsZWN0I3BhZ2VMaW5rc0Ryb3Bkb3duJykudmFsKCkgIT09ICcjJyApIHtcblxuICAgICAgICAgICAgICAgICAgICAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucGFyZW50KCkuYXR0cignaHJlZicsICQoJ3NlbGVjdCNwYWdlTGlua3NEcm9wZG93bicpLnZhbCgpICk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoICQoJ2lucHV0I2ludGVybmFsTGlua3NDdXN0b20nKS52YWwoKSAhPT0gJycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnBhcmVudCgpLmF0dHIoJ2hyZWYnLCAkKCdpbnB1dCNpbnRlcm5hbExpbmtzQ3VzdG9tJykudmFsKCkpO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLyogU0FOREJPWCAqL1xuXG4gICAgICAgICAgICAgICAgaWYoIHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCApIHtcblxuICAgICAgICAgICAgICAgICAgICBlbGVtZW50SUQgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignaWQnKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiggJCgnc2VsZWN0I2ludGVybmFsTGlua3NEcm9wZG93bicpLnZhbCgpICE9PSAnIycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyMnK3N0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCkuY29udGVudHMoKS5maW5kKCcjJytlbGVtZW50SUQpLnBhcmVudCgpLmF0dHIoJ2hyZWYnLCAkKCdzZWxlY3QjaW50ZXJuYWxMaW5rc0Ryb3Bkb3duJykudmFsKCkpO1xuXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggJCgnc2VsZWN0I3BhZ2VMaW5rc0Ryb3Bkb3duJykudmFsKCkgIT09ICcjJyApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnIycrc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94KS5jb250ZW50cygpLmZpbmQoJyMnK2VsZW1lbnRJRCkucGFyZW50KCkuYXR0cignaHJlZicsICQoJ3NlbGVjdCNwYWdlTGlua3NEcm9wZG93bicpLnZhbCgpICk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmKCAkKCdpbnB1dCNpbnRlcm5hbExpbmtzQ3VzdG9tJykudmFsKCkgIT09ICcnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjJytzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LnNhbmRib3gpLmNvbnRlbnRzKCkuZmluZCgnIycrZWxlbWVudElEKS5wYXJlbnQoKS5hdHRyKCdocmVmJywgJCgnaW5wdXQjaW50ZXJuYWxMaW5rc0N1c3RvbScpLnZhbCgpKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvKiBFTkQgU0FOREJPWCAqL1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vaWNvbnNcbiAgICAgICAgICAgIGlmKCAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuaGFzQ2xhc3MoJ2ZhJykgKSB7XG5cbiAgICAgICAgICAgICAgICAvL291dCB3aXRoIHRoZSBvbGQsIGluIHdpdGggdGhlIG5ldyA6KVxuICAgICAgICAgICAgICAgIC8vZ2V0IGljb24gY2xhc3MgbmFtZSwgc3RhcnRpbmcgd2l0aCBmYS1cbiAgICAgICAgICAgICAgICB2YXIgZ2V0ID0gJC5ncmVwKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudC5jbGFzc05hbWUuc3BsaXQoXCIgXCIpLCBmdW5jdGlvbih2LCBpKXtcblxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gdi5pbmRleE9mKCdmYS0nKSA9PT0gMDtcblxuICAgICAgICAgICAgICAgIH0pLmpvaW4oKTtcblxuICAgICAgICAgICAgICAgIC8vaWYgdGhlIGljb25zIGlzIGJlaW5nIGNoYW5nZWQsIHNhdmUgdGhlIG9sZCBvbmUgc28gd2UgY2FuIHJlc2V0IGl0IGlmIG5lZWRlZFxuXG4gICAgICAgICAgICAgICAgaWYoIGdldCAhPT0gJCgnc2VsZWN0I2ljb25zJykudmFsKCkgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnVuaXF1ZUlkKCk7XG4gICAgICAgICAgICAgICAgICAgIHN0eWxlZWRpdG9yLl9vbGRJY29uWyQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdpZCcpXSA9IGdldDtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5yZW1vdmVDbGFzcyggZ2V0ICkuYWRkQ2xhc3MoICQoJ3NlbGVjdCNpY29ucycpLnZhbCgpICk7XG5cblxuICAgICAgICAgICAgICAgIC8qIFNBTkRCT1ggKi9cblxuICAgICAgICAgICAgICAgIGlmKCBzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LnNhbmRib3ggKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgZWxlbWVudElEID0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmF0dHIoJ2lkJyk7XG4gICAgICAgICAgICAgICAgICAgICQoJyMnK3N0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCkuY29udGVudHMoKS5maW5kKCcjJytlbGVtZW50SUQpLnJlbW92ZUNsYXNzKCBnZXQgKS5hZGRDbGFzcyggJCgnc2VsZWN0I2ljb25zJykudmFsKCkgKTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8qIEVORCBTQU5EQk9YICovXG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy92aWRlbyBVUkxcbiAgICAgICAgICAgIGlmKCAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignZGF0YS10eXBlJykgPT09ICd2aWRlbycgKSB7XG5cbiAgICAgICAgICAgICAgICBpZiggJCgnaW5wdXQjeW91dHViZUlEJykudmFsKCkgIT09ICcnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5wcmV2KCkuYXR0cignc3JjJywgXCIvL3d3dy55b3V0dWJlLmNvbS9lbWJlZC9cIiskKCcjdmlkZW9fVGFiIGlucHV0I3lvdXR1YmVJRCcpLnZhbCgpKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggJCgnaW5wdXQjdmltZW9JRCcpLnZhbCgpICE9PSAnJyApIHtcblxuICAgICAgICAgICAgICAgICAgICAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucHJldigpLmF0dHIoJ3NyYycsIFwiLy9wbGF5ZXIudmltZW8uY29tL3ZpZGVvL1wiKyQoJyN2aWRlb19UYWIgaW5wdXQjdmltZW9JRCcpLnZhbCgpK1wiP3RpdGxlPTAmYW1wO2J5bGluZT0wJmFtcDtwb3J0cmFpdD0wXCIpO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLyogU0FOREJPWCAqL1xuXG4gICAgICAgICAgICAgICAgaWYoIHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCApIHtcblxuICAgICAgICAgICAgICAgICAgICBlbGVtZW50SUQgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignaWQnKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiggJCgnaW5wdXQjeW91dHViZUlEJykudmFsKCkgIT09ICcnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjJytzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LnNhbmRib3gpLmNvbnRlbnRzKCkuZmluZCgnIycrZWxlbWVudElEKS5wcmV2KCkuYXR0cignc3JjJywgXCIvL3d3dy55b3V0dWJlLmNvbS9lbWJlZC9cIiskKCcjdmlkZW9fVGFiIGlucHV0I3lvdXR1YmVJRCcpLnZhbCgpKTtcblxuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYoICQoJ2lucHV0I3ZpbWVvSUQnKS52YWwoKSAhPT0gJycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyMnK3N0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCkuY29udGVudHMoKS5maW5kKCcjJytlbGVtZW50SUQpLnByZXYoKS5hdHRyKCdzcmMnLCBcIi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby9cIiskKCcjdmlkZW9fVGFiIGlucHV0I3ZpbWVvSUQnKS52YWwoKStcIj90aXRsZT0wJmFtcDtieWxpbmU9MCZhbXA7cG9ydHJhaXQ9MFwiKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvKiBFTkQgU0FOREJPWCAqL1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICQoJyNkZXRhaWxzQXBwbGllZE1lc3NhZ2UnKS5mYWRlSW4oNjAwLCBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbigpeyAkKCcjZGV0YWlsc0FwcGxpZWRNZXNzYWdlJykuZmFkZU91dCgxMDAwKTsgfSwgMzAwMCk7XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvL2FkanVzdCBmcmFtZSBoZWlnaHRcbiAgICAgICAgICAgIHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQucGFyZW50QmxvY2suaGVpZ2h0QWRqdXN0bWVudCgpO1xuXG5cbiAgICAgICAgICAgIC8vd2UndmUgZ290IHBlbmRpbmcgY2hhbmdlc1xuICAgICAgICAgICAgc2l0ZUJ1aWxkZXIuc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIG9uIGZvY3VzLCB3ZSdsbCBtYWtlIHRoZSBpbnB1dCBmaWVsZHMgd2lkZXJcbiAgICAgICAgKi9cbiAgICAgICAgYW5pbWF0ZVN0eWxlSW5wdXRJbjogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICQodGhpcykuY3NzKCdwb3NpdGlvbicsICdhYnNvbHV0ZScpO1xuICAgICAgICAgICAgJCh0aGlzKS5jc3MoJ3JpZ2h0JywgJzBweCcpO1xuICAgICAgICAgICAgJCh0aGlzKS5hbmltYXRlKHsnd2lkdGgnOiAnMTAwJSd9LCA1MDApO1xuICAgICAgICAgICAgJCh0aGlzKS5mb2N1cyhmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgIHRoaXMuc2VsZWN0KCk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIG9uIGJsdXIsIHdlJ2xsIHJldmVydCB0aGUgaW5wdXQgZmllbGRzIHRvIHRoZWlyIG9yaWdpbmFsIHNpemVcbiAgICAgICAgKi9cbiAgICAgICAgYW5pbWF0ZVN0eWxlSW5wdXRPdXQ6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKHRoaXMpLmFuaW1hdGUoeyd3aWR0aCc6ICc0MiUnfSwgNTAwLCBmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICQodGhpcykuY3NzKCdwb3NpdGlvbicsICdyZWxhdGl2ZScpO1xuICAgICAgICAgICAgICAgICQodGhpcykuY3NzKCdyaWdodCcsICdhdXRvJyk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHdoZW4gdGhlIGNsaWNrZWQgZWxlbWVudCBpcyBhbiBhbmNob3IgdGFnIChvciBoYXMgYSBwYXJlbnQgYW5jaG9yIHRhZylcbiAgICAgICAgKi9cbiAgICAgICAgZWRpdExpbms6IGZ1bmN0aW9uKGVsKSB7XG5cbiAgICAgICAgICAgICQoJ2EjbGlua19MaW5rJykucGFyZW50KCkuc2hvdygpO1xuXG4gICAgICAgICAgICB2YXIgdGhlSHJlZjtcblxuICAgICAgICAgICAgaWYoICQoZWwpLnByb3AoJ3RhZ05hbWUnKSA9PT0gJ0EnICkge1xuXG4gICAgICAgICAgICAgICAgdGhlSHJlZiA9ICQoZWwpLmF0dHIoJ2hyZWYnKTtcblxuICAgICAgICAgICAgfSBlbHNlIGlmKCAkKGVsKS5wYXJlbnQoKS5wcm9wKCd0YWdOYW1lJykgPT09ICdBJyApIHtcblxuICAgICAgICAgICAgICAgIHRoZUhyZWYgPSAkKGVsKS5wYXJlbnQoKS5hdHRyKCdocmVmJyk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdmFyIHpJbmRleCA9IDA7XG5cbiAgICAgICAgICAgIHZhciBwYWdlTGluayA9IGZhbHNlO1xuXG4gICAgICAgICAgICAvL3RoZSBhY3R1YWwgc2VsZWN0XG5cbiAgICAgICAgICAgICQoJ3NlbGVjdCNpbnRlcm5hbExpbmtzRHJvcGRvd24nKS5wcm9wKCdzZWxlY3RlZEluZGV4JywgMCk7XG5cbiAgICAgICAgICAgIC8vc2V0IHRoZSBjb3JyZWN0IGl0ZW0gdG8gXCJzZWxlY3RlZFwiXG4gICAgICAgICAgICAkKCdzZWxlY3QjaW50ZXJuYWxMaW5rc0Ryb3Bkb3duIG9wdGlvbicpLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgIGlmKCAkKHRoaXMpLmF0dHIoJ3ZhbHVlJykgPT09IHRoZUhyZWYgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hdHRyKCdzZWxlY3RlZCcsIHRydWUpO1xuXG4gICAgICAgICAgICAgICAgICAgIHpJbmRleCA9ICQodGhpcykuaW5kZXgoKTtcblxuICAgICAgICAgICAgICAgICAgICBwYWdlTGluayA9IHRydWU7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0pO1xuXG5cbiAgICAgICAgICAgIC8vdGhlIHByZXR0eSBkcm9wZG93blxuICAgICAgICAgICAgJCgnLmxpbmtfVGFiIC5idG4tZ3JvdXAuc2VsZWN0IC5kcm9wZG93bi1tZW51IGxpJykucmVtb3ZlQ2xhc3MoJ3NlbGVjdGVkJyk7XG4gICAgICAgICAgICAkKCcubGlua19UYWIgLmJ0bi1ncm91cC5zZWxlY3QgLmRyb3Bkb3duLW1lbnUgbGk6ZXEoJyt6SW5kZXgrJyknKS5hZGRDbGFzcygnc2VsZWN0ZWQnKTtcbiAgICAgICAgICAgICQoJy5saW5rX1RhYiAuYnRuLWdyb3VwLnNlbGVjdDplcSgwKSAuZmlsdGVyLW9wdGlvbicpLnRleHQoICQoJ3NlbGVjdCNpbnRlcm5hbExpbmtzRHJvcGRvd24gb3B0aW9uOnNlbGVjdGVkJykudGV4dCgpICk7XG4gICAgICAgICAgICAkKCcubGlua19UYWIgLmJ0bi1ncm91cC5zZWxlY3Q6ZXEoMSkgLmZpbHRlci1vcHRpb24nKS50ZXh0KCAkKCdzZWxlY3QjcGFnZUxpbmtzRHJvcGRvd24gb3B0aW9uOnNlbGVjdGVkJykudGV4dCgpICk7XG5cbiAgICAgICAgICAgIGlmKCBwYWdlTGluayA9PT0gdHJ1ZSApIHtcblxuICAgICAgICAgICAgICAgICQoJ2lucHV0I2ludGVybmFsTGlua3NDdXN0b20nKS52YWwoJycpO1xuXG4gICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgaWYoICQoZWwpLnByb3AoJ3RhZ05hbWUnKSA9PT0gJ0EnICkge1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCAkKGVsKS5hdHRyKCdocmVmJylbMF0gIT09ICcjJyApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJ2lucHV0I2ludGVybmFsTGlua3NDdXN0b20nKS52YWwoICQoZWwpLmF0dHIoJ2hyZWYnKSApO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnaW5wdXQjaW50ZXJuYWxMaW5rc0N1c3RvbScpLnZhbCggJycgKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmKCAkKGVsKS5wYXJlbnQoKS5wcm9wKCd0YWdOYW1lJykgPT09ICdBJyApIHtcblxuICAgICAgICAgICAgICAgICAgICBpZiggJChlbCkucGFyZW50KCkuYXR0cignaHJlZicpWzBdICE9PSAnIycgKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCdpbnB1dCNpbnRlcm5hbExpbmtzQ3VzdG9tJykudmFsKCAkKGVsKS5wYXJlbnQoKS5hdHRyKCdocmVmJykgKTtcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJ2lucHV0I2ludGVybmFsTGlua3NDdXN0b20nKS52YWwoICcnICk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL2xpc3QgYXZhaWxhYmxlIGJsb2NrcyBvbiB0aGlzIHBhZ2UsIHJlbW92ZSBvbGQgb25lcyBmaXJzdFxuXG4gICAgICAgICAgICAkKCdzZWxlY3QjcGFnZUxpbmtzRHJvcGRvd24gb3B0aW9uOm5vdCg6Zmlyc3QpJykucmVtb3ZlKCk7XG4gICAgICAgICAgICAkKCcjcGFnZUxpc3QgdWw6dmlzaWJsZSBpZnJhbWUnKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICBpZiggJCh0aGlzKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciArIFwiID4gKjpmaXJzdFwiICkuYXR0cignaWQnKSAhPT0gdW5kZWZpbmVkICkge1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciBuZXdPcHRpb247XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoICQoZWwpLmF0dHIoJ2hyZWYnKSA9PT0gJyMnKyQodGhpcykuY29udGVudHMoKS5maW5kKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKyBcIiA+ICo6Zmlyc3RcIiApLmF0dHIoJ2lkJykgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld09wdGlvbiA9ICc8b3B0aW9uIHNlbGVjdGVkIHZhbHVlPSMnKyQodGhpcykuY29udGVudHMoKS5maW5kKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKyBcIiA+ICo6Zmlyc3RcIiApLmF0dHIoJ2lkJykrJz4jJyskKHRoaXMpLmNvbnRlbnRzKCkuZmluZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyICsgXCIgPiAqOmZpcnN0XCIgKS5hdHRyKCdpZCcpKyc8L29wdGlvbj4nO1xuXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld09wdGlvbiA9ICc8b3B0aW9uIHZhbHVlPSMnKyQodGhpcykuY29udGVudHMoKS5maW5kKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKyBcIiA+ICo6Zmlyc3RcIiApLmF0dHIoJ2lkJykrJz4jJyskKHRoaXMpLmNvbnRlbnRzKCkuZmluZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyICsgXCIgPiAqOmZpcnN0XCIgKS5hdHRyKCdpZCcpKyc8L29wdGlvbj4nO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAkKCdzZWxlY3QjcGFnZUxpbmtzRHJvcGRvd24nKS5hcHBlbmQoIG5ld09wdGlvbiApO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy9pZiB0aGVyZSBhcmVuJ3QgYW55IGJsb2NrcyB0byBsaXN0LCBoaWRlIHRoZSBkcm9wZG93blxuXG4gICAgICAgICAgICBpZiggJCgnc2VsZWN0I3BhZ2VMaW5rc0Ryb3Bkb3duIG9wdGlvbicpLnNpemUoKSA9PT0gMSApIHtcblxuICAgICAgICAgICAgICAgICQoJ3NlbGVjdCNwYWdlTGlua3NEcm9wZG93bicpLm5leHQoKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgJCgnc2VsZWN0I3BhZ2VMaW5rc0Ryb3Bkb3duJykubmV4dCgpLm5leHQoKS5oaWRlKCk7XG5cbiAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAkKCdzZWxlY3QjcGFnZUxpbmtzRHJvcGRvd24nKS5uZXh0KCkuc2hvdygpO1xuICAgICAgICAgICAgICAgICQoJ3NlbGVjdCNwYWdlTGlua3NEcm9wZG93bicpLm5leHQoKS5uZXh0KCkuc2hvdygpO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICB3aGVuIHRoZSBjbGlja2VkIGVsZW1lbnQgaXMgYW4gaW1hZ2VcbiAgICAgICAgKi9cbiAgICAgICAgZWRpdEltYWdlOiBmdW5jdGlvbihlbCkge1xuXG4gICAgICAgICAgICAkKCdhI2ltZ19MaW5rJykucGFyZW50KCkuc2hvdygpO1xuXG4gICAgICAgICAgICAvL3NldCB0aGUgY3VycmVudCBTUkNcbiAgICAgICAgICAgICQoJy5pbWFnZUZpbGVUYWInKS5maW5kKCdpbnB1dCNpbWFnZVVSTCcpLnZhbCggJChlbCkuYXR0cignc3JjJykgKTtcblxuICAgICAgICAgICAgLy9yZXNldCB0aGUgZmlsZSB1cGxvYWRcbiAgICAgICAgICAgICQoJy5pbWFnZUZpbGVUYWInKS5maW5kKCdhLmZpbGVpbnB1dC1leGlzdHMnKS5jbGljaygpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgd2hlbiB0aGUgY2xpY2tlZCBlbGVtZW50IGlzIGEgdmlkZW8gZWxlbWVudFxuICAgICAgICAqL1xuICAgICAgICBlZGl0VmlkZW86IGZ1bmN0aW9uKGVsKSB7XG5cbiAgICAgICAgICAgIHZhciBtYXRjaFJlc3VsdHM7XG5cbiAgICAgICAgICAgICQoJ2EjdmlkZW9fTGluaycpLnBhcmVudCgpLnNob3coKTtcbiAgICAgICAgICAgICQoJ2EjdmlkZW9fTGluaycpLmNsaWNrKCk7XG5cbiAgICAgICAgICAgIC8vaW5qZWN0IGN1cnJlbnQgdmlkZW8gSUQsY2hlY2sgaWYgd2UncmUgZGVhbGluZyB3aXRoIFlvdXR1YmUgb3IgVmltZW9cblxuICAgICAgICAgICAgaWYoICQoZWwpLnByZXYoKS5hdHRyKCdzcmMnKS5pbmRleE9mKFwidmltZW8uY29tXCIpID4gLTEgKSB7Ly92aW1lb1xuXG4gICAgICAgICAgICAgICAgbWF0Y2hSZXN1bHRzID0gJChlbCkucHJldigpLmF0dHIoJ3NyYycpLm1hdGNoKC9wbGF5ZXJcXC52aW1lb1xcLmNvbVxcL3ZpZGVvXFwvKFswLTldKikvKTtcblxuICAgICAgICAgICAgICAgICQoJyN2aWRlb19UYWIgaW5wdXQjdmltZW9JRCcpLnZhbCggbWF0Y2hSZXN1bHRzW21hdGNoUmVzdWx0cy5sZW5ndGgtMV0gKTtcbiAgICAgICAgICAgICAgICAkKCcjdmlkZW9fVGFiIGlucHV0I3lvdXR1YmVJRCcpLnZhbCgnJyk7XG5cbiAgICAgICAgICAgIH0gZWxzZSB7Ly95b3V0dWJlXG5cbiAgICAgICAgICAgICAgICAvL3RlbXAgPSAkKGVsKS5wcmV2KCkuYXR0cignc3JjJykuc3BsaXQoJy8nKTtcbiAgICAgICAgICAgICAgICB2YXIgcmVnRXhwID0gLy4qKD86eW91dHUuYmVcXC98dlxcL3x1XFwvXFx3XFwvfGVtYmVkXFwvfHdhdGNoXFw/dj0pKFteI1xcJlxcP10qKS4qLztcbiAgICAgICAgICAgICAgICBtYXRjaFJlc3VsdHMgPSAkKGVsKS5wcmV2KCkuYXR0cignc3JjJykubWF0Y2gocmVnRXhwKTtcblxuICAgICAgICAgICAgICAgICQoJyN2aWRlb19UYWIgaW5wdXQjeW91dHViZUlEJykudmFsKCBtYXRjaFJlc3VsdHNbMV0gKTtcbiAgICAgICAgICAgICAgICAkKCcjdmlkZW9fVGFiIGlucHV0I3ZpbWVvSUQnKS52YWwoJycpO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICB3aGVuIHRoZSBjbGlja2VkIGVsZW1lbnQgaXMgYW4gZmEgaWNvblxuICAgICAgICAqL1xuICAgICAgICBlZGl0SWNvbjogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICQoJ2EjaWNvbl9MaW5rJykucGFyZW50KCkuc2hvdygpO1xuXG4gICAgICAgICAgICAvL2dldCBpY29uIGNsYXNzIG5hbWUsIHN0YXJ0aW5nIHdpdGggZmEtXG4gICAgICAgICAgICB2YXIgZ2V0ID0gJC5ncmVwKHRoaXMuYWN0aXZlRWxlbWVudC5lbGVtZW50LmNsYXNzTmFtZS5zcGxpdChcIiBcIiksIGZ1bmN0aW9uKHYsIGkpe1xuXG4gICAgICAgICAgICAgICAgcmV0dXJuIHYuaW5kZXhPZignZmEtJykgPT09IDA7XG5cbiAgICAgICAgICAgIH0pLmpvaW4oKTtcblxuICAgICAgICAgICAgJCgnc2VsZWN0I2ljb25zIG9wdGlvbicpLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgIGlmKCAkKHRoaXMpLnZhbCgpID09PSBnZXQgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hdHRyKCdzZWxlY3RlZCcsIHRydWUpO1xuXG4gICAgICAgICAgICAgICAgICAgICQoJyNpY29ucycpLnRyaWdnZXIoJ2Nob3Nlbjp1cGRhdGVkJyk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgZGVsZXRlIHNlbGVjdGVkIGVsZW1lbnRcbiAgICAgICAgKi9cbiAgICAgICAgZGVsZXRlRWxlbWVudDogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciB0b0RlbDtcblxuICAgICAgICAgICAgLy9kZXRlcm1pbmUgd2hhdCB0byBkZWxldGVcbiAgICAgICAgICAgIGlmKCAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucHJvcCgndGFnTmFtZScpID09PSAnQScgKSB7Ly9hbmNvclxuXG4gICAgICAgICAgICAgICAgaWYoICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5wYXJlbnQoKS5wcm9wKCd0YWdOYW1lJykgPT09J0xJJyApIHsvL2Nsb25lIHRoZSBMSVxuXG4gICAgICAgICAgICAgICAgICAgIHRvRGVsID0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnBhcmVudCgpO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICAgICB0b0RlbCA9ICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSBlbHNlIGlmKCAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucHJvcCgndGFnTmFtZScpID09PSAnSU1HJyApIHsvL2ltYWdlXG5cbiAgICAgICAgICAgICAgICBpZiggJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnBhcmVudCgpLnByb3AoJ3RhZ05hbWUnKSA9PT0gJ0EnICkgey8vY2xvbmUgdGhlIEFcblxuICAgICAgICAgICAgICAgICAgICB0b0RlbCA9ICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5wYXJlbnQoKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdG9EZWwgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0gZWxzZSB7Ly9ldmVyeXRoaW5nIGVsc2VcblxuICAgICAgICAgICAgICAgIHRvRGVsID0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpO1xuXG4gICAgICAgICAgICB9XG5cblxuICAgICAgICAgICAgdG9EZWwuZmFkZU91dCg1MDAsIGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICB2YXIgcmFuZG9tRWwgPSAkKHRoaXMpLmNsb3Nlc3QoJ2JvZHknKS5maW5kKCcqOmZpcnN0Jyk7XG5cbiAgICAgICAgICAgICAgICB0b0RlbC5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgIC8qIFNBTkRCT1ggKi9cblxuICAgICAgICAgICAgICAgIHZhciBlbGVtZW50SUQgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignaWQnKTtcblxuICAgICAgICAgICAgICAgICQoJyMnK3N0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCkuY29udGVudHMoKS5maW5kKCcjJytlbGVtZW50SUQpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgLyogRU5EIFNBTkRCT1ggKi9cblxuICAgICAgICAgICAgICAgIHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQucGFyZW50QmxvY2suaGVpZ2h0QWRqdXN0bWVudCgpO1xuXG4gICAgICAgICAgICAgICAgLy93ZSd2ZSBnb3QgcGVuZGluZyBjaGFuZ2VzXG4gICAgICAgICAgICAgICAgc2l0ZUJ1aWxkZXIuc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICQoJyNkZWxldGVFbGVtZW50JykubW9kYWwoJ2hpZGUnKTtcblxuICAgICAgICAgICAgc3R5bGVlZGl0b3IuY2xvc2VTdHlsZUVkaXRvcigpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgY2xvbmVzIHRoZSBzZWxlY3RlZCBlbGVtZW50XG4gICAgICAgICovXG4gICAgICAgIGNsb25lRWxlbWVudDogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciB0aGVDbG9uZSwgdGhlQ2xvbmUyLCB0aGVPbmUsIGNsb25lZCwgY2xvbmVQYXJlbnQsIGVsZW1lbnRJRDtcblxuICAgICAgICAgICAgaWYoICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5wYXJlbnQoKS5oYXNDbGFzcygncHJvcENsb25lJykgKSB7Ly9jbG9uZSB0aGUgcGFyZW50IGVsZW1lbnRcblxuICAgICAgICAgICAgICAgIHRoZUNsb25lID0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnBhcmVudCgpLmNsb25lKCk7XG4gICAgICAgICAgICAgICAgdGhlQ2xvbmUuZmluZCggJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnByb3AoJ3RhZ05hbWUnKSApLmF0dHIoJ3N0eWxlJywgJycpO1xuXG4gICAgICAgICAgICAgICAgdGhlQ2xvbmUyID0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnBhcmVudCgpLmNsb25lKCk7XG4gICAgICAgICAgICAgICAgdGhlQ2xvbmUyLmZpbmQoICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5wcm9wKCd0YWdOYW1lJykgKS5hdHRyKCdzdHlsZScsICcnKTtcblxuICAgICAgICAgICAgICAgIHRoZU9uZSA9IHRoZUNsb25lLmZpbmQoICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5wcm9wKCd0YWdOYW1lJykgKTtcbiAgICAgICAgICAgICAgICBjbG9uZWQgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucGFyZW50KCk7XG5cbiAgICAgICAgICAgICAgICBjbG9uZVBhcmVudCA9ICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5wYXJlbnQoKS5wYXJlbnQoKTtcblxuICAgICAgICAgICAgfSBlbHNlIHsvL2Nsb25lIHRoZSBlbGVtZW50IGl0c2VsZlxuXG4gICAgICAgICAgICAgICAgdGhlQ2xvbmUgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuY2xvbmUoKTtcblxuICAgICAgICAgICAgICAgIHRoZUNsb25lLmF0dHIoJ3N0eWxlJywgJycpO1xuXG4gICAgICAgICAgICAgICAgLyppZiggc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94ICkge1xuICAgICAgICAgICAgICAgICAgICB0aGVDbG9uZS5hdHRyKCdpZCcsICcnKS51bmlxdWVJZCgpO1xuICAgICAgICAgICAgICAgIH0qL1xuXG4gICAgICAgICAgICAgICAgdGhlQ2xvbmUyID0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmNsb25lKCk7XG4gICAgICAgICAgICAgICAgdGhlQ2xvbmUyLmF0dHIoJ3N0eWxlJywgJycpO1xuXG4gICAgICAgICAgICAgICAgLypcbiAgICAgICAgICAgICAgICBpZiggc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94ICkge1xuICAgICAgICAgICAgICAgICAgICB0aGVDbG9uZTIuYXR0cignaWQnLCB0aGVDbG9uZS5hdHRyKCdpZCcpKTtcbiAgICAgICAgICAgICAgICB9Ki9cblxuICAgICAgICAgICAgICAgIHRoZU9uZSA9IHRoZUNsb25lO1xuICAgICAgICAgICAgICAgIGNsb25lZCA9ICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KTtcblxuICAgICAgICAgICAgICAgIGNsb25lUGFyZW50ID0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnBhcmVudCgpO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNsb25lZC5hZnRlciggdGhlQ2xvbmUgKTtcblxuICAgICAgICAgICAgLyogU0FOREJPWCAqL1xuXG4gICAgICAgICAgICBpZiggc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94ICkge1xuXG4gICAgICAgICAgICAgICAgZWxlbWVudElEID0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmF0dHIoJ2lkJyk7XG4gICAgICAgICAgICAgICAgJCgnIycrc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94KS5jb250ZW50cygpLmZpbmQoJyMnK2VsZW1lbnRJRCkuYWZ0ZXIoIHRoZUNsb25lMiApO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8qIEVORCBTQU5EQk9YICovXG5cbiAgICAgICAgICAgIC8vbWFrZSBzdXJlIHRoZSBuZXcgZWxlbWVudCBnZXRzIHRoZSBwcm9wZXIgZXZlbnRzIHNldCBvbiBpdFxuICAgICAgICAgICAgdmFyIG5ld0VsZW1lbnQgPSBuZXcgY2FudmFzRWxlbWVudCh0aGVPbmUuZ2V0KDApKTtcbiAgICAgICAgICAgIG5ld0VsZW1lbnQuYWN0aXZhdGUoKTtcblxuICAgICAgICAgICAgLy9wb3NzaWJsZSBoZWlnaHQgYWRqdXN0bWVudHNcbiAgICAgICAgICAgIHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQucGFyZW50QmxvY2suaGVpZ2h0QWRqdXN0bWVudCgpO1xuXG4gICAgICAgICAgICAvL3dlJ3ZlIGdvdCBwZW5kaW5nIGNoYW5nZXNcbiAgICAgICAgICAgIHNpdGVCdWlsZGVyLnNpdGUuc2V0UGVuZGluZ0NoYW5nZXModHJ1ZSk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICByZXNldHMgdGhlIGFjdGl2ZSBlbGVtZW50XG4gICAgICAgICovXG4gICAgICAgIHJlc2V0RWxlbWVudDogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIGlmKCAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuY2xvc2VzdCgnYm9keScpLndpZHRoKCkgIT09ICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS53aWR0aCgpICkge1xuXG4gICAgICAgICAgICAgICAgJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmF0dHIoJ3N0eWxlJywgJycpLmNzcyh7J291dGxpbmUnOiAnM3B4IGRhc2hlZCByZWQnLCAnY3Vyc29yJzogJ3BvaW50ZXInfSk7XG5cbiAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignc3R5bGUnLCAnJykuY3NzKHsnb3V0bGluZSc6ICczcHggZGFzaGVkIHJlZCcsICdvdXRsaW5lLW9mZnNldCc6Jy0zcHgnLCAnY3Vyc29yJzogJ3BvaW50ZXInfSk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLyogU0FOREJPWCAqL1xuXG4gICAgICAgICAgICBpZiggc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94ICkge1xuXG4gICAgICAgICAgICAgICAgdmFyIGVsZW1lbnRJRCA9ICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdpZCcpO1xuICAgICAgICAgICAgICAgICQoJyMnK3N0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCkuY29udGVudHMoKS5maW5kKCcjJytlbGVtZW50SUQpLmF0dHIoJ3N0eWxlJywgJycpO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8qIEVORCBTQU5EQk9YICovXG5cbiAgICAgICAgICAgICQoJyNzdHlsZUVkaXRvciBmb3JtI3N0eWxpbmdGb3JtJykuaGVpZ2h0KCAkKCcjc3R5bGVFZGl0b3IgZm9ybSNzdHlsaW5nRm9ybScpLmhlaWdodCgpK1wicHhcIiApO1xuXG4gICAgICAgICAgICAkKCcjc3R5bGVFZGl0b3IgZm9ybSNzdHlsaW5nRm9ybSAuZm9ybS1ncm91cDpub3QoI3N0eWxlRWxUZW1wbGF0ZSknKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgIH0pO1xuXG5cbiAgICAgICAgICAgIC8vcmVzZXQgaWNvblxuXG4gICAgICAgICAgICBpZiggc3R5bGVlZGl0b3IuX29sZEljb25bJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmF0dHIoJ2lkJyldICE9PSBudWxsICkge1xuXG4gICAgICAgICAgICAgICAgdmFyIGdldCA9ICQuZ3JlcChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQuY2xhc3NOYW1lLnNwbGl0KFwiIFwiKSwgZnVuY3Rpb24odiwgaSl7XG5cbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHYuaW5kZXhPZignZmEtJykgPT09IDA7XG5cbiAgICAgICAgICAgICAgICB9KS5qb2luKCk7XG5cbiAgICAgICAgICAgICAgICAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucmVtb3ZlQ2xhc3MoIGdldCApLmFkZENsYXNzKCBzdHlsZWVkaXRvci5fb2xkSWNvblskKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignaWQnKV0gKTtcblxuICAgICAgICAgICAgICAgICQoJ3NlbGVjdCNpY29ucyBvcHRpb24nKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoICQodGhpcykudmFsKCkgPT09IHN0eWxlZWRpdG9yLl9vbGRJY29uWyQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdpZCcpXSApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hdHRyKCdzZWxlY3RlZCcsIHRydWUpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2ljb25zJykudHJpZ2dlcignY2hvc2VuOnVwZGF0ZWQnKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBzZXRUaW1lb3V0KCBmdW5jdGlvbigpe3N0eWxlZWRpdG9yLmJ1aWxkZVN0eWxlRWxlbWVudHMoICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdkYXRhLXNlbGVjdG9yJykgKTt9LCA1NTApO1xuXG4gICAgICAgICAgICBzaXRlQnVpbGRlci5zaXRlLnNldFBlbmRpbmdDaGFuZ2VzKHRydWUpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICByZXNldFNlbGVjdExpbmtzUGFnZXM6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKCcjaW50ZXJuYWxMaW5rc0Ryb3Bkb3duJykuc2VsZWN0MigndmFsJywgJyMnKTtcblxuICAgICAgICB9LFxuXG4gICAgICAgIHJlc2V0U2VsZWN0TGlua3NJbnRlcm5hbDogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICQoJyNwYWdlTGlua3NEcm9wZG93bicpLnNlbGVjdDIoJ3ZhbCcsICcjJyk7XG5cbiAgICAgICAgfSxcblxuICAgICAgICByZXNldFNlbGVjdEFsbExpbmtzOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCgnI2ludGVybmFsTGlua3NEcm9wZG93bicpLnNlbGVjdDIoJ3ZhbCcsICcjJyk7XG4gICAgICAgICAgICAkKCcjcGFnZUxpbmtzRHJvcGRvd24nKS5zZWxlY3QyKCd2YWwnLCAnIycpO1xuICAgICAgICAgICAgdGhpcy5zZWxlY3QoKTtcblxuICAgICAgICB9LFxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBoaWRlcyBmaWxlIHVwbG9hZCBmb3Jtc1xuICAgICAgICAqL1xuICAgICAgICBoaWRlRmlsZVVwbG9hZHM6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKCdmb3JtI2ltYWdlVXBsb2FkRm9ybScpLmhpZGUoKTtcbiAgICAgICAgICAgICQoJyNpbWFnZU1vZGFsICN1cGxvYWRUYWJMSScpLmhpZGUoKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGNsb3NlcyB0aGUgc3R5bGUgZWRpdG9yXG4gICAgICAgICovXG4gICAgICAgIGNsb3NlU3R5bGVFZGl0b3I6IGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgIGlmKCBPYmplY3Qua2V5cyhzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50KS5sZW5ndGggPiAwICkge1xuICAgICAgICAgICAgICAgIHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQucmVtb3ZlT3V0bGluZSgpO1xuICAgICAgICAgICAgICAgIHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuYWN0aXZhdGUoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYoICQoJyNzdHlsZUVkaXRvcicpLmNzcygnbGVmdCcpID09PSAnMHB4JyApIHtcblxuICAgICAgICAgICAgICAgIHN0eWxlZWRpdG9yLnRvZ2dsZVNpZGVQYW5lbCgnY2xvc2UnKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgdG9nZ2xlcyB0aGUgc2lkZSBwYW5lbFxuICAgICAgICAqL1xuICAgICAgICB0b2dnbGVTaWRlUGFuZWw6IGZ1bmN0aW9uKHZhbCkge1xuXG4gICAgICAgICAgICBpZiggdmFsID09PSAnb3BlbicgJiYgJCgnI3N0eWxlRWRpdG9yJykuY3NzKCdsZWZ0JykgPT09ICctMzAwcHgnICkge1xuICAgICAgICAgICAgICAgICQoJyNzdHlsZUVkaXRvcicpLmFuaW1hdGUoeydsZWZ0JzogJzBweCd9LCAyNTApO1xuICAgICAgICAgICAgfSBlbHNlIGlmKCB2YWwgPT09ICdjbG9zZScgJiYgJCgnI3N0eWxlRWRpdG9yJykuY3NzKCdsZWZ0JykgPT09ICcwcHgnICkge1xuICAgICAgICAgICAgICAgICQoJyNzdHlsZUVkaXRvcicpLmFuaW1hdGUoeydsZWZ0JzogJy0zMDBweCd9LCAyNTApO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgRXZlbnQgaGFuZGxlciBmb3Igd2hlbiB0aGlzIG1vZGUgZ2V0cyBkZWFjdGl2YXRlZFxuICAgICAgICAqL1xuICAgICAgICBkZUFjdGl2YXRlTW9kZTogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIGlmKCBPYmplY3Qua2V5cyggc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudCApLmxlbmd0aCA+IDAgKSB7XG4gICAgICAgICAgICAgICAgc3R5bGVlZGl0b3IuY2xvc2VTdHlsZUVkaXRvcigpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL2RlYWN0aXZhdGUgYWxsIHN0eWxlIGl0ZW1zIG9uIHRoZSBjYW52YXNcbiAgICAgICAgICAgIGZvciggdmFyIGkgPTA7IGkgPCBzdHlsZWVkaXRvci5hbGxTdHlsZUl0ZW1zT25DYW52YXMubGVuZ3RoOyBpKysgKSB7XG4gICAgICAgICAgICAgICAgc3R5bGVlZGl0b3IuYWxsU3R5bGVJdGVtc09uQ2FudmFzW2ldLmRlYWN0aXZhdGUoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9BZGQgb3ZlcmxheSBhZ2FpblxuICAgICAgICAgICAgLy8gZm9yKHZhciBpID0gMTsgaSA8PSAkKFwidWwjcGFnZTEgbGlcIikubGVuZ3RoOyBpKyspe1xuICAgICAgICAgICAgLy8gICAgIHZhciBpZCA9IFwiI3VpLWlkLVwiICsgaTtcbiAgICAgICAgICAgIC8vICAgICBhbGVydChpZCk7XG4gICAgICAgICAgICAvLyAgICAgLy8gb3ZlcmxheSA9ICQoJzxzcGFuIGNsYXNzPVwib3ZlcmxheVwiPjxzcGFuIGNsYXNzPVwiZnVpLWV5ZVwiPjwvc3Bhbj48L3NwYW4+Jyk7XG4gICAgICAgICAgICAvLyAgICAgLy8gJChpZCkuY29udGVudHMoKS5maW5kKCdhLm92ZXInKS5hcHBlbmQoIG92ZXJsYXkgKTtcbiAgICAgICAgICAgIC8vIH1cblxuICAgICAgICB9XG5cbiAgICB9O1xuXG4gICAgc3R5bGVlZGl0b3IuaW5pdCgpO1xuXG4gICAgZXhwb3J0cy5zdHlsZWVkaXRvciA9IHN0eWxlZWRpdG9yO1xuXG59KCkpOyIsIihmdW5jdGlvbiAoKSB7XG5cbi8qIGdsb2JhbHMgc2l0ZVVybDpmYWxzZSwgYmFzZVVybDpmYWxzZSAqL1xuICAgIFwidXNlIHN0cmljdFwiO1xuICAgICAgICBcbiAgICB2YXIgYXBwVUkgPSB7XG4gICAgICAgIFxuICAgICAgICBmaXJzdE1lbnVXaWR0aDogMTkwLFxuICAgICAgICBzZWNvbmRNZW51V2lkdGg6IDMwMCxcbiAgICAgICAgbG9hZGVyQW5pbWF0aW9uOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbG9hZGVyJyksXG4gICAgICAgIHNlY29uZE1lbnVUcmlnZ2VyQ29udGFpbmVyczogJCgnI21lbnUgI21haW4gI2VsZW1lbnRDYXRzLCAjbWVudSAjbWFpbiAjdGVtcGxhdGVzVWwnKSxcbiAgICAgICAgc2l0ZVVybDogc2l0ZVVybCxcbiAgICAgICAgYmFzZVVybDogYmFzZVVybCxcbiAgICAgICAgXG4gICAgICAgIHNldHVwOiBmdW5jdGlvbigpe1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBGYWRlIHRoZSBsb2FkZXIgYW5pbWF0aW9uXG4gICAgICAgICAgICAkKGFwcFVJLmxvYWRlckFuaW1hdGlvbikuZmFkZU91dChmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICQoJyNtZW51JykuYW5pbWF0ZSh7J2xlZnQnOiAtYXBwVUkuZmlyc3RNZW51V2lkdGh9LCAxMDAwKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBUYWJzXG4gICAgICAgICAgICAkKFwiLm5hdi10YWJzIGFcIikub24oJ2NsaWNrJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS50YWIoXCJzaG93XCIpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgICQoXCJzZWxlY3Quc2VsZWN0XCIpLnNlbGVjdDIoKTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgJCgnOnJhZGlvLCA6Y2hlY2tib3gnKS5yYWRpb2NoZWNrKCk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgIC8vIFRvb2x0aXBzXG4gICAgICAgICAgICAkKFwiW2RhdGEtdG9nZ2xlPXRvb2x0aXBdXCIpLnRvb2x0aXAoXCJoaWRlXCIpO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBUYWJsZTogVG9nZ2xlIGFsbCBjaGVja2JveGVzXG4gICAgICAgICAgICAkKCcudGFibGUgLnRvZ2dsZS1hbGwgOmNoZWNrYm94Jykub24oJ2NsaWNrJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyk7XG4gICAgICAgICAgICAgICAgdmFyIGNoID0gJHRoaXMucHJvcCgnY2hlY2tlZCcpO1xuICAgICAgICAgICAgICAgICR0aGlzLmNsb3Nlc3QoJy50YWJsZScpLmZpbmQoJ3Rib2R5IDpjaGVja2JveCcpLnJhZGlvY2hlY2soIWNoID8gJ3VuY2hlY2snIDogJ2NoZWNrJyk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgLy8gQWRkIHN0eWxlIGNsYXNzIG5hbWUgdG8gYSB0b29sdGlwc1xuICAgICAgICAgICAgJChcIi50b29sdGlwXCIpLmFkZENsYXNzKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgIGlmICgkKHRoaXMpLnByZXYoKS5hdHRyKFwiZGF0YS10b29sdGlwLXN0eWxlXCIpKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBcInRvb2x0aXAtXCIgKyAkKHRoaXMpLnByZXYoKS5hdHRyKFwiZGF0YS10b29sdGlwLXN0eWxlXCIpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAkKFwiLmJ0bi1ncm91cFwiKS5vbignY2xpY2snLCBcImFcIiwgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5zaWJsaW5ncygpLnJlbW92ZUNsYXNzKFwiYWN0aXZlXCIpLmVuZCgpLmFkZENsYXNzKFwiYWN0aXZlXCIpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgIC8vIEZvY3VzIHN0YXRlIGZvciBhcHBlbmQvcHJlcGVuZCBpbnB1dHNcbiAgICAgICAgICAgICQoJy5pbnB1dC1ncm91cCcpLm9uKCdmb2N1cycsICcuZm9ybS1jb250cm9sJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICQodGhpcykuY2xvc2VzdCgnLmlucHV0LWdyb3VwLCAuZm9ybS1ncm91cCcpLmFkZENsYXNzKCdmb2N1cycpO1xuICAgICAgICAgICAgfSkub24oJ2JsdXInLCAnLmZvcm0tY29udHJvbCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJy5pbnB1dC1ncm91cCwgLmZvcm0tZ3JvdXAnKS5yZW1vdmVDbGFzcygnZm9jdXMnKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBUYWJsZTogVG9nZ2xlIGFsbCBjaGVja2JveGVzXG4gICAgICAgICAgICAkKCcudGFibGUgLnRvZ2dsZS1hbGwnKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICB2YXIgY2ggPSAkKHRoaXMpLmZpbmQoJzpjaGVja2JveCcpLnByb3AoJ2NoZWNrZWQnKTtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJy50YWJsZScpLmZpbmQoJ3Rib2R5IDpjaGVja2JveCcpLmNoZWNrYm94KCFjaCA/ICdjaGVjaycgOiAndW5jaGVjaycpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgIC8vIFRhYmxlOiBBZGQgY2xhc3Mgcm93IHNlbGVjdGVkXG4gICAgICAgICAgICAkKCcudGFibGUgdGJvZHkgOmNoZWNrYm94Jykub24oJ2NoZWNrIHVuY2hlY2sgdG9nZ2xlJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpXG4gICAgICAgICAgICAgICAgLCBjaGVjayA9ICR0aGlzLnByb3AoJ2NoZWNrZWQnKVxuICAgICAgICAgICAgICAgICwgdG9nZ2xlID0gZS50eXBlID09PSAndG9nZ2xlJ1xuICAgICAgICAgICAgICAgICwgY2hlY2tib3hlcyA9ICQoJy50YWJsZSB0Ym9keSA6Y2hlY2tib3gnKVxuICAgICAgICAgICAgICAgICwgY2hlY2tBbGwgPSBjaGVja2JveGVzLmxlbmd0aCA9PT0gY2hlY2tib3hlcy5maWx0ZXIoJzpjaGVja2VkJykubGVuZ3RoO1xuXG4gICAgICAgICAgICAgICAgJHRoaXMuY2xvc2VzdCgndHInKVtjaGVjayA/ICdhZGRDbGFzcycgOiAncmVtb3ZlQ2xhc3MnXSgnc2VsZWN0ZWQtcm93Jyk7XG4gICAgICAgICAgICAgICAgaWYgKHRvZ2dsZSkgJHRoaXMuY2xvc2VzdCgnLnRhYmxlJykuZmluZCgnLnRvZ2dsZS1hbGwgOmNoZWNrYm94JykuY2hlY2tib3goY2hlY2tBbGwgPyAnY2hlY2snIDogJ3VuY2hlY2snKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBTd2l0Y2hcbiAgICAgICAgICAgICQoXCJbZGF0YS10b2dnbGU9J3N3aXRjaCddXCIpLndyYXAoJzxkaXYgY2xhc3M9XCJzd2l0Y2hcIiAvPicpLnBhcmVudCgpLmJvb3RzdHJhcFN3aXRjaCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgXG4gICAgICAgICAgICBhcHBVSS5zZWNvbmRNZW51VHJpZ2dlckNvbnRhaW5lcnMub24oJ2NsaWNrJywgJ2E6bm90KC5idG4pJywgYXBwVUkuc2Vjb25kTWVudUFuaW1hdGlvbik7XG4gICAgICAgICAgICAgICAgICAgICAgICBcbiAgICAgICAgfSxcbiAgICAgICAgXG4gICAgICAgIHNlY29uZE1lbnVBbmltYXRpb246IGZ1bmN0aW9uKCl7XG4gICAgICAgIFxuICAgICAgICAgICAgJCgnI21lbnUgI21haW4gYScpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcbiAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xuXHRcbiAgICAgICAgICAgIC8vc2hvdyBvbmx5IHRoZSByaWdodCBlbGVtZW50c1xuICAgICAgICAgICAgJCgnI21lbnUgI3NlY29uZCB1bCBsaScpLmhpZGUoKTtcbiAgICAgICAgICAgICQoJyNtZW51ICNzZWNvbmQgdWwgbGkuJyskKHRoaXMpLmF0dHIoJ2lkJykpLnNob3coKTtcblxuICAgICAgICAgICAgaWYoICQodGhpcykuYXR0cignaWQnKSA9PT0gJ2FsbCcgKSB7XG4gICAgICAgICAgICAgICAgJCgnI21lbnUgI3NlY29uZCB1bCNlbGVtZW50cyBsaScpLnNob3coKTtcdFx0XG4gICAgICAgICAgICB9XG5cdFxuICAgICAgICAgICAgJCgnLm1lbnUgLnNlY29uZCcpLmNzcygnZGlzcGxheScsICdibG9jaycpLnN0b3AoKS5hbmltYXRlKHtcbiAgICAgICAgICAgICAgICB3aWR0aDogYXBwVUkuc2Vjb25kTWVudVdpZHRoXG4gICAgICAgICAgICB9LCA1MDApO1x0XG4gICAgICAgICAgICAgICAgXG4gICAgICAgIH1cbiAgICAgICAgXG4gICAgfTtcbiAgICBcbiAgICAvL2luaXRpYXRlIHRoZSBVSVxuICAgIGFwcFVJLnNldHVwKCk7XG5cblxuICAgIC8vKioqKiBFWFBPUlRTXG4gICAgbW9kdWxlLmV4cG9ydHMuYXBwVUkgPSBhcHBVSTtcbiAgICBcbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcbiAgICBcInVzZSBzdHJpY3RcIjtcbiAgICBcbiAgICBleHBvcnRzLmdldFJhbmRvbUFyYml0cmFyeSA9IGZ1bmN0aW9uKG1pbiwgbWF4KSB7XG4gICAgICAgIHJldHVybiBNYXRoLmZsb29yKE1hdGgucmFuZG9tKCkgKiAobWF4IC0gbWluKSArIG1pbik7XG4gICAgfTtcblxuICAgIGV4cG9ydHMuZ2V0UGFyYW1ldGVyQnlOYW1lID0gZnVuY3Rpb24gKG5hbWUsIHVybCkge1xuXG4gICAgICAgIGlmICghdXJsKSB1cmwgPSB3aW5kb3cubG9jYXRpb24uaHJlZjtcbiAgICAgICAgbmFtZSA9IG5hbWUucmVwbGFjZSgvW1xcW1xcXV0vZywgXCJcXFxcJCZcIik7XG4gICAgICAgIHZhciByZWdleCA9IG5ldyBSZWdFeHAoXCJbPyZdXCIgKyBuYW1lICsgXCIoPShbXiYjXSopfCZ8I3wkKVwiKSxcbiAgICAgICAgICAgIHJlc3VsdHMgPSByZWdleC5leGVjKHVybCk7XG4gICAgICAgIGlmICghcmVzdWx0cykgcmV0dXJuIG51bGw7XG4gICAgICAgIGlmICghcmVzdWx0c1syXSkgcmV0dXJuICcnO1xuICAgICAgICByZXR1cm4gZGVjb2RlVVJJQ29tcG9uZW50KHJlc3VsdHNbMl0ucmVwbGFjZSgvXFwrL2csIFwiIFwiKSk7XG4gICAgICAgIFxuICAgIH07XG4gICAgXG59KCkpOyJdfQ==
