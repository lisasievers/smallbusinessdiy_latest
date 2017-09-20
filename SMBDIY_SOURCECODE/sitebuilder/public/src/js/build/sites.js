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
},{"./ui.js":7}],2:[function(require,module,exports){
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
},{"./config.js":3,"./ui.js":7,"./utils.js":8}],3:[function(require,module,exports){
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
},{}],4:[function(require,module,exports){
(function () {
	"use strict";

	var bConfig = require('./config.js');
	var siteBuilder = require('./builder.js');
	var appUI = require('./ui.js').appUI;

	var publish = {

        buttonPublish: document.getElementById('publishPage'),
        buttonSavePendingBeforePublishing: document.getElementById('buttonSavePendingBeforePublishing'),
        publishModal: document.getElementById('publishModal'),
        buttonPublishSubmit: document.getElementById('publishSubmit'),
        publishActive: 0,
        theItem: {},
        modalSiteSettings: document.getElementById('siteSettings'),

        init: function() {

            $(this.buttonPublish).on('click', this.loadPublishModal);
            $(this.buttonSavePendingBeforePublishing).on('click', this.saveBeforePublishing);
            $(this.publishModal).on('change', 'input[type=checkbox]', this.publishCheckboxEvent);
            $(this.buttonPublishSubmit).on('click', this.publishSite);
            $(this.modalSiteSettings).on('click', '#siteSettingsBrowseFTPButton, .link', this.browseFTP);
            $(this.modalSiteSettings).on('click', '#ftpListItems .close', this.closeFtpBrowser);
            $(this.modalSiteSettings).on('click', '#siteSettingsTestFTP', this.testFTPConnection);

            //show the publish button
            $(this.buttonPublish).show();

            //listen to site settings load event
            $('body').on('siteSettingsLoad', this.showPublishSettings);

            //publish hash?
            if( window.location.hash === "#publish" ) {
                $(this.buttonPublish).click();
            }

            // header tooltips
            //if( this.buttonPublish.hasAttribute('data-toggle') && this.buttonPublish.getAttribute('data-toggle') == 'tooltip' ) {
            //   $(this.buttonPublish).tooltip('show');
            //   setTimeout(function(){$(this.buttonPublish).tooltip('hide')}, 5000);
            //}

        },


        /*
            loads the publish modal
        */
        loadPublishModal: function(e) {

            e.preventDefault();

            if( publish.publishActive === 0 ) {//check if we're currently publishing anything

                //hide alerts
                $('#publishModal .modal-alerts > *').each(function(){
                    $(this).remove();
                });

                $('#publishModal .modal-body > .alert-success').hide();

                //hide loaders
                $('#publishModal_assets .publishing').each(function(){
                    $(this).hide();
                    $(this).find('.working').show();
                    $(this).find('.done').hide();
                });

                //remove published class from asset checkboxes
                $('#publishModal_assets input').each(function(){
                    $(this).removeClass('published');
                });

                //do we have pending changes?
                if( siteBuilder.site.pendingChanges === true ) {//we've got changes, save first

                    $('#publishModal #publishPendingChangesMessage').show();
                    $('#publishModal .modal-body-content').hide();

                } else {//all set, get on it with publishing

                    //get the correct pages in the Pages section of the publish modal
                    $('#publishModal_pages tbody > *').remove();

                    $('#pages li:visible').each(function(){

                        var thePage = $(this).find('a:first').text();
                        var theRow = $('<tr><td class="text-center" style="width: 30px;"><label class="checkbox no-label"><input type="checkbox" value="'+thePage+'" id="" data-type="page" name="pages[]" data-toggle="checkbox"></label></td><td>'+thePage+'<span class="publishing"><span class="working">Publishing... <img src="'+appUI.baseUrl+'images/publishLoader.gif"></span><span class="done text-primary">Published &nbsp;<span class="fui-check"></span></span></span></td></tr>');

                        //checkboxify
                        theRow.find('input').radiocheck();
                        theRow.find('input').on('check uncheck toggle', function(){
                            $(this).closest('tr')[$(this).prop('checked') ? 'addClass' : 'removeClass']('selected-row');
                        });

                        $('#publishModal_pages tbody').append( theRow );

                    });

                    $('#publishModal #publishPendingChangesMessage').hide();
                    $('#publishModal .modal-body-content').show();

                }
            }

            //enable/disable publish button

            var activateButton = false;

            $('#publishModal input[type=checkbox]').each(function(){

                if( $(this).prop('checked') ) {
                    activateButton = true;
                    return false;
                }
            });

            if( activateButton ) {
                $('#publishSubmit').removeClass('disabled');
            } else {
                $('#publishSubmit').addClass('disabled');
            }

            $('#publishModal').modal('show');

        },


        /*
            saves pending changes before publishing
        */
        saveBeforePublishing: function() {

            $('#publishModal #publishPendingChangesMessage').hide();
            $('#publishModal .loader').show();
            $(this).addClass('disabled');

            siteBuilder.site.prepForSave(false);

            var serverData = {};
            serverData.pages = siteBuilder.site.sitePagesReadyForServer;
            if( siteBuilder.site.pagesToDelete.length > 0 ) {
                serverData.toDelete = siteBuilder.site.pagesToDelete;
            }
            serverData.siteData = siteBuilder.site.data;

            $.ajax({
                url: appUI.siteUrl+"site/save/1",
                type: "POST",
                dataType: "json",
                data: serverData,
            }).done(function(res){

                $('#publishModal .loader').fadeOut(500, function(){

                    $('#publishModal .modal-alerts').append( $(res.responseHTML) );

                    //self-destruct success messages
                    setTimeout(function(){$('#publishModal .modal-alerts .alert-success').fadeOut(500, function(){$(this).remove();});}, 2500);

                    //enable button
                    $('#publishModal #publishPendingChangesMessage .btn.save').removeClass('disabled');

                });

                if( res.responseCode === 1 ) {//changes were saved without issues

                    //no more pending changes
                    siteBuilder.site.setPendingChanges(false);

                    //get the correct pages in the Pages section of the publish modal
                    $('#publishModal_pages tbody > *').remove();

                    $('#pages li:visible').each(function(){

                        var thePage = $(this).find('a:first').text();
                        var theRow = $('<tr><td class="text-center" style="width: 0px;"><label class="checkbox"><input type="checkbox" value="'+thePage+'" id="" data-type="page" name="pages[]" data-toggle="checkbox"></label></td><td>'+thePage+'<span class="publishing"><span class="working">Publishing... <img src="'+appUI.baseUrl+'images/publishLoader.gif"></span><span class="done text-primary">Published &nbsp;<span class="fui-check"></span></span></span></td></tr>');

                        //checkboxify
                        theRow.find('input').radiocheck();
                        theRow.find('input').on('check uncheck toggle', function(){
                            $(this).closest('tr')[$(this).prop('checked') ? 'addClass' : 'removeClass']('selected-row');
                        });

                        $('#publishModal_pages tbody').append( theRow );

                    });

                    //show content
                    $('#publishModal .modal-body-content').fadeIn(500);

                }

            });

        },


        /*
            event handler for the checkboxes inside the publish modal
        */
        publishCheckboxEvent: function() {

            var activateButton = false;

            $('#publishModal input[type=checkbox]').each(function(){

                if( $(this).prop('checked') ) {
                    activateButton = true;
                    return false;
                }

            });

            if( activateButton ) {

                $('#publishSubmit').removeClass('disabled');

            } else {

                $('#publishSubmit').addClass('disabled');

            }

        },


        /*
            publishes the selected items
        */
        publishSite: function() {

            //track the publishing state
            publish.publishActive = 1;

            //disable button
            $('#publishSubmit, #publishCancel').addClass('disabled');

            //remove existing alerts
            $('#publishModal .modal-alerts > *').remove();

            //prepare stuff
            $('#publishModal form input[type="hidden"].page').remove();

            //loop through all pages
            $('#pageList > ul').each(function(){

                //export this page?
                if( $('#publishModal #publishModal_pages input:eq('+($(this).index()+1)+')').prop('checked') ) {

                    //grab the skeleton markup
                    var newDocMainParent = $('iframe#skeleton').contents().find( bConfig.pageContainer );

                    //empty out the skeleton
                    newDocMainParent.find('*').remove();

                    //loop through page iframes and grab the body stuff
                    $(this).find('iframe').each(function(){

                        var attr = $(this).attr('data-sandbox');

                        var theContents;

                        if (typeof attr !== typeof undefined && attr !== false) {
                            theContents = $('#sandboxes #'+attr).contents().find( bConfig.pageContainer );
                        } else {
                            theContents = $(this).contents().find( bConfig.pageContainer );
                        }

                        theContents.find('.frameCover').each(function(){
                            $(this).remove();
                        });

                        //remove inline styling leftovers
                        for( var key in bConfig.editableItems ) {

                            theContents.find( key ).each(function(){

                                $(this).removeAttr('data-selector');

                                if( $(this).attr('style') === '' ) {
                                    $(this).removeAttr('style');
                                }

                            });

                        }

                        for (var i = 0; i < bConfig.editableContent.length; ++i) {

                            $(this).contents().find( bConfig.editableContent[i] ).each(function(){
                                $(this).removeAttr('data-selector');
                            });

                        }

                        var toAdd = theContents.html();

                        //grab scripts

                        var scripts = $(this).contents().find( bConfig.pageContainer ).find('script');

                        if( scripts.size() > 0 ) {

                            var theIframe = document.getElementById("skeleton");

                            scripts.each(function(){

                                var script;

                                if( $(this).text() !== '' ) {//script tags with content

                                    script = theIframe.contentWindow.document.createElement("script");
                                    script.type = 'text/javascript';
                                    script.innerHTML = $(this).text();
                                    theIframe.contentWindow.document.getElementById( bConfig.pageContainer.substring(1) ).appendChild(script);

                                } else if( $(this).attr('src') !== null ) {

                                    script = theIframe.contentWindow.document.createElement("script");
                                    script.type = 'text/javascript';
                                    script.src = $(this).attr('src');
                                    theIframe.contentWindow.document.getElementById( bConfig.pageContainer.substring(1) ).appendChild(script);
                                }

                            });

                        }

                        newDocMainParent.append( $(toAdd) );

                    });

                    var newInput = $('<input type="hidden" class="page" name="xpages['+$('#pages li:eq('+($(this).index()+1)+') a:first').text()+']" value="">');

                    $('#publishModal form').prepend( newInput );

                    newInput.val( "<html>"+$('iframe#skeleton').contents().find('html').html()+"</html>" );

                }

            });

            publish.publishAsset();

        },

        publishAsset: function() {

            var toPublish = $('#publishModal_assets input[type=checkbox]:checked:not(.published, .toggleAll), #publishModal_pages input[type=checkbox]:checked:not(.published, .toggleAll)');

            if( toPublish.size() > 0 ) {

                publish.theItem = toPublish.first();

                //display the asset loader
                publish.theItem.closest('td').next().find('.publishing').fadeIn(500);

                var theData;

                if( publish.theItem.attr('data-type') === 'page' ) {

                    theData = {site_id: $('form#publishForm input[name=site_id]').val(), item: publish.theItem.val(), pageContent: $('form#publishForm input[name="xpages['+publish.theItem.val()+']"]').val()};

                } else if( publish.theItem.attr('data-type') === 'asset' ) {

                    theData = {site_id: $('form#publishForm input[name=site_id]').val(), item: publish.theItem.val()};

                }

                $.ajax({
                    url: $('form#publishForm').attr('action')+"/"+publish.theItem.attr('data-type'),
                    type: 'post',
                    data: theData,
                    dataType: 'json'
                }).done(function(ret){

                    if( ret.responseCode === 0 ) {//fatal error, publishing will stop

                        //hide indicators
                        publish.theItem.closest('td').next().find('.working').hide();

                        //enable buttons
                        $('#publishSubmit, #publishCancel').removeClass('disabled');
                        $('#publishModal .modal-alerts').append( $(ret.responseHTML) );

                    } else if( ret.responseCode === 1 ) {//no issues

                        //show done
                        publish.theItem.closest('td').next().find('.working').hide();
                        publish.theItem.closest('td').next().find('.done').fadeIn();
                        publish.theItem.addClass('published');

                        publish.publishAsset();

                    }

                });

            } else {

                //publishing is done
                publish.publishActive = 0;

                //enable buttons
                $('#publishSubmit, #publishCancel').removeClass('disabled');

                //show message
                $('#publishModal .modal-body > .alert-success').fadeIn(500, function(){
                    setTimeout(function(){$('#publishModal .modal-body > .alert-success').fadeOut(500);}, 2500);
                });

            }

        },

        showPublishSettings: function() {

            $('#siteSettingsPublishing').show();
        },


        /*
            browse the FTP connection
        */
        browseFTP: function(e) {

            e.preventDefault();

    		//got all we need?

    		if( $('#siteSettings_ftpServer').val() === '' || $('#siteSettings_ftpUser').val() === '' || $('#siteSettings_ftpPassword').val() === '' ) {
                alert('Please make sure all FTP connection details are present');
                return false;
            }

            //check if this is a deeper level link
    		if( $(this).hasClass('link') ) {

    			if( $(this).hasClass('back') ) {

    				$('#siteSettings_ftpPath').val( $(this).attr('href') );

    			} else {

    				//if so, we'll change the path before connecting

    				if( $('#siteSettings_ftpPath').val().substr( $('#siteSettings_ftpPath').val.length - 1 ) === '/' ) {//prepend "/"

    					$('#siteSettings_ftpPath').val( $('#siteSettings_ftpPath').val()+$(this).attr('href') );

    				} else {

    					$('#siteSettings_ftpPath').val( $('#siteSettings_ftpPath').val()+"/"+$(this).attr('href') );

    				}

    			}


    		}

    		//destroy all alerts

    		$('#ftpAlerts .alert').fadeOut(500, function(){
    			$(this).remove();
    		});

    		//disable button
    		$(this).addClass('disabled');

    		//remove existing links
    		$('#ftpListItems > *').remove();

    		//show ftp section
    		$('#ftpBrowse .loaderFtp').show();
    		$('#ftpBrowse').slideDown(500);

    		var theButton = $(this);

    		$.ajax({
                url: appUI.siteUrl+"site/connect",
    			type: 'post',
    			dataType: 'json',
    			data: $('form#siteSettingsForm').serializeArray()
    		}).done(function(ret){

    			//enable button
    			theButton.removeClass('disabled');

    			//hide loading
    			$('#ftpBrowse .loaderFtp').hide();

    			if( ret.responseCode === 0 ) {//error
    				$('#ftpAlerts').append( $(ret.responseHTML) );
    			} else if( ret.responseCode === 1 ) {//all good
    				$('#ftpListItems').append( $(ret.responseHTML) );
    			}

    		});

        },


        /*
            hides/closes the FTP browser
        */
        closeFtpBrowser: function(e) {

            e.preventDefault();
    		$(this).closest('#ftpBrowse').slideUp(500);

        },


         /*
            tests the FTP connection with the provided details
        */
        testFTPConnection: function() {

            //got all we need?
    		if( $('#siteSettings_ftpServer').val() === '' || $('#siteSettings_ftpUser').val() === '' || $('#siteSettings_ftpPassword').val() === '' ) {
                alert('Please make sure all FTP connection details are present');
                return false;
            }

    		//destroy all alerts
            $('#ftpTestAlerts .alert').fadeOut(500, function(){
                $(this).remove();
            });

    		//disable button
    		$(this).addClass('disabled');

    		//show loading indicator
    		$(this).next().fadeIn(500);

            var theButton = $(this);

    		$.ajax({
                url: appUI.siteUrl+"site/ftptest",
    			type: 'post',
    			dataType: 'json',
    			data: $('form#siteSettingsForm').serializeArray()
    		}).done(function(ret){

    			//enable button
    			theButton.removeClass('disabled');
                theButton.next().fadeOut(500);

    			if( ret.responseCode === 0 ) {//error
                    $('#ftpTestAlerts').append( $(ret.responseHTML) );
                } else if( ret.responseCode === 1 ) {//all good
                    $('#ftpTestAlerts').append( $(ret.responseHTML) );
                }

    		});

        }

    };

    publish.init();

}());
},{"./builder.js":2,"./config.js":3,"./ui.js":7}],5:[function(require,module,exports){
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
},{"./ui.js":7}],6:[function(require,module,exports){
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
},{"./ui.js":7}],7:[function(require,module,exports){
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
},{}],8:[function(require,module,exports){
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
},{}],9:[function(require,module,exports){
(function () {
	"use strict";

	require('./modules/ui.js');
	require('./modules/account.js');
	require('./modules/sites.js');
	require('./modules/sitesettings.js');
	require('./modules/publishing.js');

}());
},{"./modules/account.js":1,"./modules/publishing.js":4,"./modules/sites.js":5,"./modules/sitesettings.js":6,"./modules/ui.js":7}]},{},[9])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJwdWJsaWMvc3JjL2pzL21vZHVsZXMvYWNjb3VudC5qcyIsInB1YmxpYy9zcmMvanMvbW9kdWxlcy9idWlsZGVyLmpzIiwicHVibGljL3NyYy9qcy9tb2R1bGVzL2NvbmZpZy5qcyIsInB1YmxpYy9zcmMvanMvbW9kdWxlcy9wdWJsaXNoaW5nLmpzIiwicHVibGljL3NyYy9qcy9tb2R1bGVzL3NpdGVzLmpzIiwicHVibGljL3NyYy9qcy9tb2R1bGVzL3NpdGVzZXR0aW5ncy5qcyIsInB1YmxpYy9zcmMvanMvbW9kdWxlcy91aS5qcyIsInB1YmxpYy9zcmMvanMvbW9kdWxlcy91dGlscy5qcyIsInB1YmxpYy9zcmMvanMvc2l0ZXMuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUNBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUN0SkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNseERBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUN0Q0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3RqQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQzNNQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDekxBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDaEhBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDbkJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsIihmdW5jdGlvbiAoKSB7XG5cdFwidXNlIHN0cmljdFwiO1xuXG5cdHZhciBhcHBVSSA9IHJlcXVpcmUoJy4vdWkuanMnKS5hcHBVSTtcblxuXHR2YXIgYWNjb3VudCA9IHtcblxuICAgICAgICBidXR0b25VcGRhdGVBY2NvdW50RGV0YWlsczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2FjY291bnREZXRhaWxzU3VibWl0JyksXG4gICAgICAgIGJ1dHRvblVwZGF0ZUxvZ2luRGV0YWlsczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2FjY291bnRMb2dpblN1Ym1pdCcpLFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uVXBkYXRlQWNjb3VudERldGFpbHMpLm9uKCdjbGljaycsIHRoaXMudXBkYXRlQWNjb3VudERldGFpbHMpO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblVwZGF0ZUxvZ2luRGV0YWlscykub24oJ2NsaWNrJywgdGhpcy51cGRhdGVMb2dpbkRldGFpbHMpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgdXBkYXRlcyBhY2NvdW50IGRldGFpbHNcbiAgICAgICAgKi9cbiAgICAgICAgdXBkYXRlQWNjb3VudERldGFpbHM6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2FsbCBmaWVsZHMgZmlsbGVkIGluP1xuXG4gICAgICAgICAgICB2YXIgYWxsR29vZCA9IDE7XG5cbiAgICAgICAgICAgIGlmKCAkKCcjYWNjb3VudF9kZXRhaWxzIGlucHV0I2ZpcnN0bmFtZScpLnZhbCgpID09PSAnJyApIHtcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9kZXRhaWxzIGlucHV0I2ZpcnN0bmFtZScpLmNsb3Nlc3QoJy5mb3JtLWdyb3VwJykuYWRkQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgIGFsbEdvb2QgPSAwO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9kZXRhaWxzIGlucHV0I2ZpcnN0bmFtZScpLmNsb3Nlc3QoJy5mb3JtLWdyb3VwJykucmVtb3ZlQ2xhc3MoJ2hhcy1lcnJvcicpO1xuICAgICAgICAgICAgICAgIGFsbEdvb2QgPSAxO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiggJCgnI2FjY291bnRfZGV0YWlscyBpbnB1dCNsYXN0bmFtZScpLnZhbCgpID09PSAnJyApIHtcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9kZXRhaWxzIGlucHV0I2xhc3RuYW1lJykuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKS5hZGRDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICAgICAgYWxsR29vZCA9IDA7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2RldGFpbHMgaW5wdXQjbGFzdG5hbWUnKS5jbG9zZXN0KCcuZm9ybS1ncm91cCcpLnJlbW92ZUNsYXNzKCdoYXMtZXJyb3InKTtcbiAgICAgICAgICAgICAgICBhbGxHb29kID0gMTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYoIGFsbEdvb2QgPT09IDEgKSB7XG5cbiAgICAgICAgICAgICAgICB2YXIgdGhlQnV0dG9uID0gJCh0aGlzKTtcblxuICAgICAgICAgICAgICAgIC8vZGlzYWJsZSBidXR0b25cbiAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICAgICAgLy9zaG93IGxvYWRlclxuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2RldGFpbHMgLmxvYWRlcicpLmZhZGVJbig1MDApO1xuXG4gICAgICAgICAgICAgICAgLy9yZW1vdmUgYWxlcnRzXG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfZGV0YWlscyAuYWxlcnRzID4gKicpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgICAgICAgICAgdXJsOiBhcHBVSS5zaXRlVXJsK1widXNlci91YWNjb3VudFwiLFxuICAgICAgICAgICAgICAgICAgICB0eXBlOiAncG9zdCcsXG4gICAgICAgICAgICAgICAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICAgICAgICAgICAgICAgIGRhdGE6ICQoJyNhY2NvdW50X2RldGFpbHMnKS5zZXJpYWxpemUoKVxuICAgICAgICAgICAgICAgIH0pLmRvbmUoZnVuY3Rpb24ocmV0KXtcblxuICAgICAgICAgICAgICAgICAgICAvL2VuYWJsZSBidXR0b25cbiAgICAgICAgICAgICAgICAgICAgdGhlQnV0dG9uLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vaGlkZSBsb2FkZXJcbiAgICAgICAgICAgICAgICAgICAgJCgnI2FjY291bnRfZGV0YWlscyAubG9hZGVyJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9kZXRhaWxzIC5hbGVydHMnKS5hcHBlbmQoICQocmV0LnJlc3BvbnNlSFRNTCkgKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMSApIHsvL3N1Y2Nlc3NcbiAgICAgICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2RldGFpbHMgLmFsZXJ0cyA+IConKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24gKCkgeyAkKHRoaXMpLnJlbW92ZSgpOyB9KTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0sIDMwMDApO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHVwZGF0ZXMgYWNjb3VudCBsb2dpbiBkZXRhaWxzXG4gICAgICAgICovXG4gICAgICAgIHVwZGF0ZUxvZ2luRGV0YWlsczogZnVuY3Rpb24oKSB7XG5cblx0XHRcdGNvbnNvbGUubG9nKGFwcFVJKTtcblxuICAgICAgICAgICAgdmFyIGFsbEdvb2QgPSAxO1xuXG4gICAgICAgICAgICBpZiggJCgnI2FjY291bnRfbG9naW4gaW5wdXQjZW1haWwnKS52YWwoKSA9PT0gJycgKSB7XG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfbG9naW4gaW5wdXQjZW1haWwnKS5jbG9zZXN0KCcuZm9ybS1ncm91cCcpLmFkZENsYXNzKCdoYXMtZXJyb3InKTtcbiAgICAgICAgICAgICAgICBhbGxHb29kID0gMDtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJCgnI2FjY291bnRfbG9naW4gaW5wdXQjZW1haWwnKS5jbG9zZXN0KCcuZm9ybS1ncm91cCcpLnJlbW92ZUNsYXNzKCdoYXMtZXJyb3InKTtcbiAgICAgICAgICAgICAgICBhbGxHb29kID0gMTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYoICQoJyNhY2NvdW50X2xvZ2luIGlucHV0I3Bhc3N3b3JkJykudmFsKCkgPT09ICcnICkge1xuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2xvZ2luIGlucHV0I3Bhc3N3b3JkJykuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKS5hZGRDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICAgICAgYWxsR29vZCA9IDA7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQoJyNhY2NvdW50X2xvZ2luIGlucHV0I3Bhc3N3b3JkJykuY2xvc2VzdCgnLmZvcm0tZ3JvdXAnKS5yZW1vdmVDbGFzcygnaGFzLWVycm9yJyk7XG4gICAgICAgICAgICAgICAgYWxsR29vZCA9IDE7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKCBhbGxHb29kID09PSAxICkge1xuXG4gICAgICAgICAgICAgICAgdmFyIHRoZUJ1dHRvbiA9ICQodGhpcyk7XG5cbiAgICAgICAgICAgICAgICAvL2Rpc2FibGUgYnV0dG9uXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgICAgIC8vc2hvdyBsb2FkZXJcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9sb2dpbiAubG9hZGVyJykuZmFkZUluKDUwMCk7XG5cbiAgICAgICAgICAgICAgICAvL3JlbW92ZSBhbGVydHNcbiAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9sb2dpbiAuYWxlcnRzID4gKicpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgICAgICAgICAgdXJsOiBhcHBVSS5zaXRlVXJsK1widXNlci91bG9naW5cIixcbiAgICAgICAgICAgICAgICAgICAgdHlwZTogJ3Bvc3QnLFxuICAgICAgICAgICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgICAgICAgICAgICAgICBkYXRhOiAkKCcjYWNjb3VudF9sb2dpbicpLnNlcmlhbGl6ZSgpXG4gICAgICAgICAgICAgICAgfSkuZG9uZShmdW5jdGlvbihyZXQpe1xuXG4gICAgICAgICAgICAgICAgICAgIC8vZW5hYmxlIGJ1dHRvblxuICAgICAgICAgICAgICAgICAgICB0aGVCdXR0b24ucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9oaWRlIGxvYWRlclxuICAgICAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9sb2dpbiAubG9hZGVyJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9sb2dpbiAuYWxlcnRzJykuYXBwZW5kKCAkKHJldC5yZXNwb25zZUhUTUwpICk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDEgKSB7Ly9zdWNjZXNzXG4gICAgICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKCcjYWNjb3VudF9sb2dpbiAuYWxlcnRzID4gKicpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbiAoKSB7ICQodGhpcykucmVtb3ZlKCk7IH0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSwgMzAwMCk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfVxuXG4gICAgfTtcblxuICAgIGFjY291bnQuaW5pdCgpO1xuXG59KCkpOyIsIihmdW5jdGlvbiAoKSB7XG5cdFwidXNlIHN0cmljdFwiO1xuXG4gICAgdmFyIHNpdGVCdWlsZGVyVXRpbHMgPSByZXF1aXJlKCcuL3V0aWxzLmpzJyk7XG4gICAgdmFyIGJDb25maWcgPSByZXF1aXJlKCcuL2NvbmZpZy5qcycpO1xuICAgIHZhciBhcHBVSSA9IHJlcXVpcmUoJy4vdWkuanMnKS5hcHBVSTtcblxuXG5cdCAvKlxuICAgICAgICBCYXNpYyBCdWlsZGVyIFVJIGluaXRpYWxpc2F0aW9uXG4gICAgKi9cbiAgICB2YXIgYnVpbGRlclVJID0ge1xuXG4gICAgICAgIGFsbEJsb2Nrczoge30sICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vaG9sZHMgYWxsIGJsb2NrcyBsb2FkZWQgZnJvbSB0aGUgc2VydmVyXG4gICAgICAgIG1lbnVXcmFwcGVyOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbWVudScpLFxuICAgICAgICBwcmltYXJ5U2lkZU1lbnVXcmFwcGVyOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbWFpbicpLFxuICAgICAgICBidXR0b25CYWNrOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnYmFja0J1dHRvbicpLFxuICAgICAgICBidXR0b25CYWNrQ29uZmlybTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2xlYXZlUGFnZUJ1dHRvbicpLFxuXG4gICAgICAgIHNpdGVCdWlsZGVyTW9kZXM6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdzaXRlQnVpbGRlck1vZGVzJyksXG4gICAgICAgIGFjZUVkaXRvcnM6IHt9LFxuICAgICAgICBmcmFtZUNvbnRlbnRzOiAnJywgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vaG9sZHMgZnJhbWUgY29udGVudHNcbiAgICAgICAgdGVtcGxhdGVJRDogMCwgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvL2hvbGRzIHRoZSB0ZW1wbGF0ZSBJRCBmb3IgYSBwYWdlICg/Pz8pXG4gICAgICAgIHJhZGlvQmxvY2tNb2RlOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbW9kZUJsb2NrJyksXG5cbiAgICAgICAgbW9kYWxEZWxldGVCbG9jazogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2RlbGV0ZUJsb2NrJyksXG4gICAgICAgIG1vZGFsUmVzZXRCbG9jazogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3Jlc2V0QmxvY2snKSxcbiAgICAgICAgbW9kYWxEZWxldGVQYWdlOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZGVsZXRlUGFnZScpLFxuICAgICAgICBidXR0b25EZWxldGVQYWdlQ29uZmlybTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2RlbGV0ZVBhZ2VDb25maXJtJyksXG5cbiAgICAgICAgZHJvcGRvd25QYWdlTGlua3M6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdpbnRlcm5hbExpbmtzRHJvcGRvd24nKSxcblxuICAgICAgICBwYWdlSW5Vcmw6IG51bGwsXG5cbiAgICAgICAgdGVtcEZyYW1lOiB7fSxcblxuICAgICAgICBpbml0OiBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAvL2xvYWQgYmxvY2tzXG4gICAgICAgICAgICAkLmdldEpTT04oYXBwVUkuYmFzZVVybCsnL2VsZW1lbnRzLmpzb24/dj0xMjM0NTY3OCcsIGZ1bmN0aW9uKGRhdGEpeyBidWlsZGVyVUkuYWxsQmxvY2tzID0gZGF0YTsgYnVpbGRlclVJLmltcGxlbWVudEJsb2NrcygpOyB9KTtcblxuICAgICAgICAgICAgLy9zaXRlYmFyIGhvdmVyIGFuaW1hdGlvbiBhY3Rpb25cbiAgICAgICAgICAgICQodGhpcy5tZW51V3JhcHBlcikub24oJ21vdXNlZW50ZXInLCBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5zdG9wKCkuYW5pbWF0ZSh7J2xlZnQnOiAnMHB4J30sIDUwMCk7XG5cbiAgICAgICAgICAgIH0pLm9uKCdtb3VzZWxlYXZlJywgZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICQodGhpcykuc3RvcCgpLmFuaW1hdGUoeydsZWZ0JzogJy0xOTBweCd9LCA1MDApO1xuXG4gICAgICAgICAgICAgICAgJCgnI21lbnUgI21haW4gYScpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcbiAgICAgICAgICAgICAgICAkKCcubWVudSAuc2Vjb25kJykuc3RvcCgpLmFuaW1hdGUoe1xuICAgICAgICAgICAgICAgICAgICB3aWR0aDogMFxuICAgICAgICAgICAgICAgIH0sIDUwMCwgZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAgICAgJCgnI21lbnUgI3NlY29uZCcpLmhpZGUoKTtcbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8vcHJldmVudCBjbGljayBldmVudCBvbiBhbmNvcnMgaW4gdGhlIGJsb2NrIHNlY3Rpb24gb2YgdGhlIHNpZGViYXJcbiAgICAgICAgICAgICQodGhpcy5wcmltYXJ5U2lkZU1lbnVXcmFwcGVyKS5vbignY2xpY2snLCAnYTpub3QoLmFjdGlvbkJ1dHRvbnMpJywgZnVuY3Rpb24oZSl7ZS5wcmV2ZW50RGVmYXVsdCgpO30pO1xuXG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uQmFjaykub24oJ2NsaWNrJywgdGhpcy5iYWNrQnV0dG9uKTtcbiAgICAgICAgICAgICQodGhpcy5idXR0b25CYWNrQ29uZmlybSkub24oJ2NsaWNrJywgdGhpcy5iYWNrQnV0dG9uQ29uZmlybSk7XG5cbiAgICAgICAgICAgIC8vbm90aWZ5IHRoZSB1c2VyIG9mIHBlbmRpbmcgY2huYWdlcyB3aGVuIGNsaWNraW5nIHRoZSBiYWNrIGJ1dHRvblxuICAgICAgICAgICAgJCh3aW5kb3cpLmJpbmQoJ2JlZm9yZXVubG9hZCcsIGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgaWYoIHNpdGUucGVuZGluZ0NoYW5nZXMgPT09IHRydWUgKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiAnWW91ciBzaXRlIGNvbnRhaW5zIGNoYW5nZWQgd2hpY2ggaGF2ZW5cXCd0IGJlZW4gc2F2ZWQgeWV0LiBBcmUgeW91IHN1cmUgeW91IHdhbnQgdG8gbGVhdmU/JztcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy9tYWtlIHN1cmUgd2Ugc3RhcnQgaW4gYmxvY2sgbW9kZVxuICAgICAgICAgICAgJCh0aGlzLnJhZGlvQmxvY2tNb2RlKS5yYWRpb2NoZWNrKCdjaGVjaycpLm9uKCdjbGljaycsIHRoaXMuYWN0aXZhdGVCbG9ja01vZGUpO1xuXG4gICAgICAgICAgICAvL1VSTCBwYXJhbWV0ZXJzXG4gICAgICAgICAgICBidWlsZGVyVUkucGFnZUluVXJsID0gc2l0ZUJ1aWxkZXJVdGlscy5nZXRQYXJhbWV0ZXJCeU5hbWUoJ3AnKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGJ1aWxkcyB0aGUgYmxvY2tzIGludG8gdGhlIHNpdGUgYmFyXG4gICAgICAgICovXG4gICAgICAgIGltcGxlbWVudEJsb2NrczogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciBuZXdJdGVtLCBsb2FkZXJGdW5jdGlvbjtcblxuICAgICAgICAgICAgZm9yKCB2YXIga2V5IGluIHRoaXMuYWxsQmxvY2tzLmVsZW1lbnRzICkge1xuXG4gICAgICAgICAgICAgICAgdmFyIG5pY2VLZXkgPSBrZXkudG9Mb3dlckNhc2UoKS5yZXBsYWNlKFwiIFwiLCBcIl9cIik7XG5cbiAgICAgICAgICAgICAgICAkKCc8bGk+PGEgaHJlZj1cIlwiIGlkPVwiJytuaWNlS2V5KydcIj4nK2tleSsnPC9hPjwvbGk+JykuYXBwZW5kVG8oJyNtZW51ICNtYWluIHVsI2VsZW1lbnRDYXRzJyk7XG5cbiAgICAgICAgICAgICAgICBmb3IoIHZhciB4ID0gMDsgeCA8IHRoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV0ubGVuZ3RoOyB4KysgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoIHRoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0udGh1bWJuYWlsID09PSBudWxsICkgey8vd2UnbGwgbmVlZCBhbiBpZnJhbWVcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9idWlsZCB1cyBzb21lIGlmcmFtZXMhXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKCB0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLnNhbmRib3ggKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiggdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS5sb2FkZXJGdW5jdGlvbiApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbG9hZGVyRnVuY3Rpb24gPSAnZGF0YS1sb2FkZXJmdW5jdGlvbj1cIicrdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS5sb2FkZXJGdW5jdGlvbisnXCInO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG5ld0l0ZW0gPSAkKCc8bGkgY2xhc3M9XCJlbGVtZW50ICcrbmljZUtleSsnXCI+PGlmcmFtZSBzcmM9XCInK2FwcFVJLmJhc2VVcmwrdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS51cmwrJ1wiIHNjcm9sbGluZz1cIm5vXCIgc2FuZGJveD1cImFsbG93LXNhbWUtb3JpZ2luXCI+PC9pZnJhbWU+PC9saT4nKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG5ld0l0ZW0gPSAkKCc8bGkgY2xhc3M9XCJlbGVtZW50ICcrbmljZUtleSsnXCI+PGlmcmFtZSBzcmM9XCJhYm91dDpibGFua1wiIHNjcm9sbGluZz1cIm5vXCI+PC9pZnJhbWU+PC9saT4nKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBuZXdJdGVtLmZpbmQoJ2lmcmFtZScpLnVuaXF1ZUlkKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBuZXdJdGVtLmZpbmQoJ2lmcmFtZScpLmF0dHIoJ3NyYycsIGFwcFVJLmJhc2VVcmwrdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS51cmwpO1xuXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7Ly93ZSd2ZSBnb3QgYSB0aHVtYm5haWxcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoIHRoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0uc2FuZGJveCApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKCB0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLmxvYWRlckZ1bmN0aW9uICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsb2FkZXJGdW5jdGlvbiA9ICdkYXRhLWxvYWRlcmZ1bmN0aW9uPVwiJyt0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLmxvYWRlckZ1bmN0aW9uKydcIic7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbmV3SXRlbSA9ICQoJzxsaSBjbGFzcz1cImVsZW1lbnQgJytuaWNlS2V5KydcIj48aW1nIHNyYz1cIicrYXBwVUkuYmFzZVVybCt0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLnRodW1ibmFpbCsnXCIgZGF0YS1zcmNjPVwiJythcHBVSS5iYXNlVXJsK3RoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0udXJsKydcIiBkYXRhLWhlaWdodD1cIicrdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS5oZWlnaHQrJ1wiIGRhdGEtc2FuZGJveD1cIlwiICcrbG9hZGVyRnVuY3Rpb24rJz48L2xpPicpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbmV3SXRlbSA9ICQoJzxsaSBjbGFzcz1cImVsZW1lbnQgJytuaWNlS2V5KydcIj48aW1nIHNyYz1cIicrYXBwVUkuYmFzZVVybCt0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLnRodW1ibmFpbCsnXCIgZGF0YS1zcmNjPVwiJythcHBVSS5iYXNlVXJsK3RoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0udXJsKydcIiBkYXRhLWhlaWdodD1cIicrdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS5oZWlnaHQrJ1wiPjwvbGk+Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIG5ld0l0ZW0uYXBwZW5kVG8oJyNtZW51ICNzZWNvbmQgdWwjZWxlbWVudHMnKTtcblxuICAgICAgICAgICAgICAgICAgICAvL3pvb21lciB3b3Jrc1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciB0aGVIZWlnaHQ7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoIHRoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0uaGVpZ2h0ICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB0aGVIZWlnaHQgPSB0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLmhlaWdodCowLjI1O1xuXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRoZUhlaWdodCA9ICdhdXRvJztcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgbmV3SXRlbS5maW5kKCdpZnJhbWUnKS56b29tZXIoe1xuICAgICAgICAgICAgICAgICAgICAgICAgem9vbTogMC4yNSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHdpZHRoOiAyNzAsXG4gICAgICAgICAgICAgICAgICAgICAgICBoZWlnaHQ6IHRoZUhlaWdodCxcbiAgICAgICAgICAgICAgICAgICAgICAgIG1lc3NhZ2U6IFwiRHJhZyZEcm9wIE1lIVwiXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vZHJhZ2dhYmxlc1xuICAgICAgICAgICAgYnVpbGRlclVJLm1ha2VEcmFnZ2FibGUoKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGV2ZW50IGhhbmRsZXIgZm9yIHdoZW4gdGhlIGJhY2sgbGluayBpcyBjbGlja2VkXG4gICAgICAgICovXG4gICAgICAgIGJhY2tCdXR0b246IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICBpZiggc2l0ZS5wZW5kaW5nQ2hhbmdlcyA9PT0gdHJ1ZSApIHtcbiAgICAgICAgICAgICAgICAkKCcjYmFja01vZGFsJykubW9kYWwoJ3Nob3cnKTtcbiAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBidXR0b24gZm9yIGNvbmZpcm1pbmcgbGVhdmluZyB0aGUgcGFnZVxuICAgICAgICAqL1xuICAgICAgICBiYWNrQnV0dG9uQ29uZmlybTogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHNpdGUucGVuZGluZ0NoYW5nZXMgPSBmYWxzZTsvL3ByZXZlbnQgdGhlIEpTIGFsZXJ0IGFmdGVyIGNvbmZpcm1pbmcgdXNlciB3YW50cyB0byBsZWF2ZVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgYWN0aXZhdGVzIGJsb2NrIG1vZGVcbiAgICAgICAgKi9cbiAgICAgICAgYWN0aXZhdGVCbG9ja01vZGU6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICBzaXRlLmFjdGl2ZVBhZ2UudG9nZ2xlRnJhbWVDb3ZlcnMoJ09uJyk7XG5cbiAgICAgICAgICAgIC8vdHJpZ2dlciBjdXN0b20gZXZlbnRcbiAgICAgICAgICAgICQoJ2JvZHknKS50cmlnZ2VyKCdtb2RlQmxvY2tzJyk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBtYWtlcyB0aGUgYmxvY2tzIGFuZCB0ZW1wbGF0ZXMgaW4gdGhlIHNpZGViYXIgZHJhZ2dhYmxlIG9udG8gdGhlIGNhbnZhc1xuICAgICAgICAqL1xuICAgICAgICBtYWtlRHJhZ2dhYmxlOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCgnI2VsZW1lbnRzIGxpLCAjdGVtcGxhdGVzIGxpJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5kcmFnZ2FibGUoe1xuICAgICAgICAgICAgICAgICAgICBoZWxwZXI6IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuICQoJzxkaXYgc3R5bGU9XCJoZWlnaHQ6IDEwMHB4OyB3aWR0aDogMzAwcHg7IGJhY2tncm91bmQ6ICNGOUZBRkE7IGJveC1zaGFkb3c6IDVweCA1cHggMXB4IHJnYmEoMCwwLDAsMC4xKTsgdGV4dC1hbGlnbjogY2VudGVyOyBsaW5lLWhlaWdodDogMTAwcHg7IGZvbnQtc2l6ZTogMjhweDsgY29sb3I6ICMxNkEwODVcIj48c3BhbiBjbGFzcz1cImZ1aS1saXN0XCI+PC9zcGFuPjwvZGl2PicpO1xuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICByZXZlcnQ6ICdpbnZhbGlkJyxcbiAgICAgICAgICAgICAgICAgICAgYXBwZW5kVG86ICdib2R5JyxcbiAgICAgICAgICAgICAgICAgICAgY29ubmVjdFRvU29ydGFibGU6ICcjcGFnZUxpc3QgPiB1bCcsXG4gICAgICAgICAgICAgICAgICAgIHN0YXJ0OiBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL3N3aXRjaCB0byBibG9jayBtb2RlXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCdpbnB1dDpyYWRpb1tuYW1lPW1vZGVdJykucGFyZW50KCkuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCdpbnB1dDpyYWRpb1tuYW1lPW1vZGVdI21vZGVCbG9jaycpLnJhZGlvY2hlY2soJ2NoZWNrJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkKCcjZWxlbWVudHMgbGkgYScpLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICQodGhpcykudW5iaW5kKCdjbGljaycpLmJpbmQoJ2NsaWNrJywgZnVuY3Rpb24oZSl7XG4gICAgICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBJbXBsZW1lbnRzIHRoZSBzaXRlIG9uIHRoZSBjYW52YXMsIGNhbGxlZCBmcm9tIHRoZSBTaXRlIG9iamVjdCB3aGVuIHRoZSBzaXRlRGF0YSBoYXMgY29tcGxldGVkIGxvYWRpbmdcbiAgICAgICAgKi9cbiAgICAgICAgcG9wdWxhdGVDYW52YXM6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB2YXIgaTtcblxuICAgICAgICAgICAgLy9pZiB3ZSBoYXZlIGFueSBibG9ja3MgYXQgYWxsLCBhY3RpdmF0ZSB0aGUgbW9kZXNcbiAgICAgICAgICAgIGlmKCBPYmplY3Qua2V5cyhzaXRlLnBhZ2VzKS5sZW5ndGggPiAwICkge1xuICAgICAgICAgICAgICAgIHZhciBtb2RlcyA9IGJ1aWxkZXJVSS5zaXRlQnVpbGRlck1vZGVzLnF1ZXJ5U2VsZWN0b3JBbGwoJ2lucHV0W3R5cGU9XCJyYWRpb1wiXScpO1xuICAgICAgICAgICAgICAgIGZvciggaSA9IDA7IGkgPCBtb2Rlcy5sZW5ndGg7IGkrKyApIHtcbiAgICAgICAgICAgICAgICAgICAgbW9kZXNbaV0ucmVtb3ZlQXR0cmlidXRlKCdkaXNhYmxlZCcpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgdmFyIGNvdW50ZXIgPSAxO1xuXG4gICAgICAgICAgICAvL2xvb3AgdGhyb3VnaCB0aGUgcGFnZXNcblxuICAgICAgICAgICAgZm9yKCBpIGluIHNpdGUucGFnZXMgKSB7XG5cbiAgICAgICAgICAgICAgICB2YXIgbmV3UGFnZSA9IG5ldyBQYWdlKGksIHNpdGUucGFnZXNbaV0sIGNvdW50ZXIpO1xuXG4gICAgICAgICAgICAgICAgY291bnRlcisrO1xuXG4gICAgICAgICAgICAgICAgLy9zZXQgdGhpcyBwYWdlIGFzIGFjdGl2ZT9cbiAgICAgICAgICAgICAgICBpZiggYnVpbGRlclVJLnBhZ2VJblVybCA9PT0gaSApIHtcbiAgICAgICAgICAgICAgICAgICAgbmV3UGFnZS5zZWxlY3RQYWdlKCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vYWN0aXZhdGUgdGhlIGZpcnN0IHBhZ2VcbiAgICAgICAgICAgIGlmKHNpdGUuc2l0ZVBhZ2VzLmxlbmd0aCA+IDAgJiYgYnVpbGRlclVJLnBhZ2VJblVybCA9PT0gbnVsbCkge1xuICAgICAgICAgICAgICAgIHNpdGUuc2l0ZVBhZ2VzWzBdLnNlbGVjdFBhZ2UoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9XG5cbiAgICB9O1xuXG5cbiAgICAvKlxuICAgICAgICBQYWdlIGNvbnN0cnVjdG9yXG4gICAgKi9cbiAgICBmdW5jdGlvbiBQYWdlIChwYWdlTmFtZSwgcGFnZSwgY291bnRlcikge1xuXG4gICAgICAgIHRoaXMubmFtZSA9IHBhZ2VOYW1lIHx8IFwiXCI7XG4gICAgICAgIHRoaXMucGFnZUlEID0gcGFnZS5wYWdlc19pZCB8fCAwO1xuICAgICAgICB0aGlzLmJsb2NrcyA9IFtdO1xuICAgICAgICB0aGlzLnBhcmVudFVMID0ge307IC8vcGFyZW50IFVMIG9uIHRoZSBjYW52YXNcbiAgICAgICAgdGhpcy5zdGF0dXMgPSAnJzsvLycnLCAnbmV3JyBvciAnY2hhbmdlZCdcbiAgICAgICAgdGhpcy5zY3JpcHRzID0gW107Ly90cmFja3Mgc2NyaXB0IFVSTHMgdXNlZCBvbiB0aGlzIHBhZ2VcblxuICAgICAgICB0aGlzLnBhZ2VTZXR0aW5ncyA9IHtcbiAgICAgICAgICAgIHRpdGxlOiBwYWdlLnBhZ2VzX3RpdGxlIHx8ICcnLFxuICAgICAgICAgICAgbWV0YV9kZXNjcmlwdGlvbjogcGFnZS5tZXRhX2Rlc2NyaXB0aW9uIHx8ICcnLFxuICAgICAgICAgICAgbWV0YV9rZXl3b3JkczogcGFnZS5tZXRhX2tleXdvcmRzIHx8ICcnLFxuICAgICAgICAgICAgaGVhZGVyX2luY2x1ZGVzOiBwYWdlLmhlYWRlcl9pbmNsdWRlcyB8fCAnJyxcbiAgICAgICAgICAgIHBhZ2VfY3NzOiBwYWdlLnBhZ2VfY3NzIHx8ICcnXG4gICAgICAgIH07XG5cbiAgICAgICAgdGhpcy5wYWdlTWVudVRlbXBsYXRlID0gJzxhIGhyZWY9XCJcIiBjbGFzcz1cIm1lbnVJdGVtTGlua1wiPnBhZ2U8L2E+PHNwYW4gY2xhc3M9XCJwYWdlQnV0dG9uc1wiPjxhIGhyZWY9XCJcIiBjbGFzcz1cImZpbGVFZGl0IGZ1aS1uZXdcIj48L2E+PGEgaHJlZj1cIlwiIGNsYXNzPVwiZmlsZURlbCBmdWktY3Jvc3NcIj48YSBjbGFzcz1cImJ0biBidG4teHMgYnRuLXByaW1hcnkgYnRuLWVtYm9zc2VkIGZpbGVTYXZlIGZ1aS1jaGVja1wiIGhyZWY9XCIjXCI+PC9hPjwvc3Bhbj48L2E+PC9zcGFuPic7XG5cbiAgICAgICAgdGhpcy5tZW51SXRlbSA9IHt9Oy8vcmVmZXJlbmNlIHRvIHRoZSBwYWdlcyBtZW51IGl0ZW0gZm9yIHRoaXMgcGFnZSBpbnN0YW5jZVxuICAgICAgICB0aGlzLmxpbmtzRHJvcGRvd25JdGVtID0ge307Ly9yZWZlcmVuY2UgdG8gdGhlIGxpbmtzIGRyb3Bkb3duIGl0ZW0gZm9yIHRoaXMgcGFnZSBpbnN0YW5jZVxuXG4gICAgICAgIHRoaXMucGFyZW50VUwgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdVTCcpO1xuICAgICAgICB0aGlzLnBhcmVudFVMLnNldEF0dHJpYnV0ZSgnaWQnLCBcInBhZ2VcIitjb3VudGVyKTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgbWFrZXMgdGhlIGNsaWNrZWQgcGFnZSBhY3RpdmVcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5zZWxlY3RQYWdlID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIC8vY29uc29sZS5sb2coJ3NlbGVjdDonKTtcbiAgICAgICAgICAgIC8vY29uc29sZS5sb2codGhpcy5wYWdlU2V0dGluZ3MpO1xuXG4gICAgICAgICAgICAvL21hcmsgdGhlIG1lbnUgaXRlbSBhcyBhY3RpdmVcbiAgICAgICAgICAgIHNpdGUuZGVBY3RpdmF0ZUFsbCgpO1xuICAgICAgICAgICAgJCh0aGlzLm1lbnVJdGVtKS5hZGRDbGFzcygnYWN0aXZlJyk7XG5cbiAgICAgICAgICAgIC8vbGV0IFNpdGUga25vdyB3aGljaCBwYWdlIGlzIGN1cnJlbnRseSBhY3RpdmVcbiAgICAgICAgICAgIHNpdGUuc2V0QWN0aXZlKHRoaXMpO1xuXG4gICAgICAgICAgICAvL2Rpc3BsYXkgdGhlIG5hbWUgb2YgdGhlIGFjdGl2ZSBwYWdlIG9uIHRoZSBjYW52YXNcbiAgICAgICAgICAgIHNpdGUucGFnZVRpdGxlLmlubmVySFRNTCA9IHRoaXMubmFtZTtcblxuICAgICAgICAgICAgLy9sb2FkIHRoZSBwYWdlIHNldHRpbmdzIGludG8gdGhlIHBhZ2Ugc2V0dGluZ3MgbW9kYWxcbiAgICAgICAgICAgIHNpdGUuaW5wdXRQYWdlU2V0dGluZ3NUaXRsZS52YWx1ZSA9IHRoaXMucGFnZVNldHRpbmdzLnRpdGxlO1xuICAgICAgICAgICAgc2l0ZS5pbnB1dFBhZ2VTZXR0aW5nc01ldGFEZXNjcmlwdGlvbi52YWx1ZSA9IHRoaXMucGFnZVNldHRpbmdzLm1ldGFfZGVzY3JpcHRpb247XG4gICAgICAgICAgICBzaXRlLmlucHV0UGFnZVNldHRpbmdzTWV0YUtleXdvcmRzLnZhbHVlID0gdGhpcy5wYWdlU2V0dGluZ3MubWV0YV9rZXl3b3JkcztcbiAgICAgICAgICAgIHNpdGUuaW5wdXRQYWdlU2V0dGluZ3NJbmNsdWRlcy52YWx1ZSA9IHRoaXMucGFnZVNldHRpbmdzLmhlYWRlcl9pbmNsdWRlcztcbiAgICAgICAgICAgIHNpdGUuaW5wdXRQYWdlU2V0dGluZ3NQYWdlQ3NzLnZhbHVlID0gdGhpcy5wYWdlU2V0dGluZ3MucGFnZV9jc3M7XG5cbiAgICAgICAgICAgIC8vdHJpZ2dlciBjdXN0b20gZXZlbnRcbiAgICAgICAgICAgICQoJ2JvZHknKS50cmlnZ2VyKCdjaGFuZ2VQYWdlJyk7XG5cbiAgICAgICAgICAgIC8vcmVzZXQgdGhlIGhlaWdodHMgZm9yIHRoZSBibG9ja3Mgb24gdGhlIGN1cnJlbnQgcGFnZVxuICAgICAgICAgICAgZm9yKCB2YXIgaSBpbiB0aGlzLmJsb2NrcyApIHtcblxuICAgICAgICAgICAgICAgIGlmKCBPYmplY3Qua2V5cyh0aGlzLmJsb2Nrc1tpXS5mcmFtZURvY3VtZW50KS5sZW5ndGggPiAwICl7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuYmxvY2tzW2ldLmhlaWdodEFkanVzdG1lbnQoKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9zaG93IHRoZSBlbXB0eSBtZXNzYWdlP1xuICAgICAgICAgICAgdGhpcy5pc0VtcHR5KCk7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgY2hhbmdlZCB0aGUgbG9jYXRpb24vb3JkZXIgb2YgYSBibG9jayB3aXRoaW4gYSBwYWdlXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuc2V0UG9zaXRpb24gPSBmdW5jdGlvbihmcmFtZUlELCBuZXdQb3MpIHtcblxuICAgICAgICAgICAgLy93ZSdsbCBuZWVkIHRoZSBibG9jayBvYmplY3QgY29ubmVjdGVkIHRvIGlmcmFtZSB3aXRoIGZyYW1lSURcblxuICAgICAgICAgICAgZm9yKHZhciBpIGluIHRoaXMuYmxvY2tzKSB7XG5cbiAgICAgICAgICAgICAgICBpZiggdGhpcy5ibG9ja3NbaV0uZnJhbWUuZ2V0QXR0cmlidXRlKCdpZCcpID09PSBmcmFtZUlEICkge1xuXG4gICAgICAgICAgICAgICAgICAgIC8vY2hhbmdlIHRoZSBwb3NpdGlvbiBvZiB0aGlzIGJsb2NrIGluIHRoZSBibG9ja3MgYXJyYXlcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5ibG9ja3Muc3BsaWNlKG5ld1BvcywgMCwgdGhpcy5ibG9ja3Muc3BsaWNlKGksIDEpWzBdKTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGRlbGV0ZSBibG9jayBmcm9tIGJsb2NrcyBhcnJheVxuICAgICAgICAqL1xuICAgICAgICB0aGlzLmRlbGV0ZUJsb2NrID0gZnVuY3Rpb24oYmxvY2spIHtcblxuICAgICAgICAgICAgLy9yZW1vdmUgZnJvbSBibG9ja3MgYXJyYXlcbiAgICAgICAgICAgIGZvciggdmFyIGkgaW4gdGhpcy5ibG9ja3MgKSB7XG4gICAgICAgICAgICAgICAgaWYoIHRoaXMuYmxvY2tzW2ldID09PSBibG9jayApIHtcbiAgICAgICAgICAgICAgICAgICAgLy9mb3VuZCBpdCwgcmVtb3ZlIGZyb20gYmxvY2tzIGFycmF5XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuYmxvY2tzLnNwbGljZShpLCAxKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHNpdGUuc2V0UGVuZGluZ0NoYW5nZXModHJ1ZSk7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgdG9nZ2xlcyBhbGwgYmxvY2sgZnJhbWVDb3ZlcnMgb24gdGhpcyBwYWdlXG4gICAgICAgICovXG4gICAgICAgIHRoaXMudG9nZ2xlRnJhbWVDb3ZlcnMgPSBmdW5jdGlvbihvbk9yT2ZmKSB7XG5cbiAgICAgICAgICAgIGZvciggdmFyIGkgaW4gdGhpcy5ibG9ja3MgKSB7XG5cbiAgICAgICAgICAgICAgICB0aGlzLmJsb2Nrc1tpXS50b2dnbGVDb3Zlcihvbk9yT2ZmKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHNldHVwIGZvciBlZGl0aW5nIGEgcGFnZSBuYW1lXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuZWRpdFBhZ2VOYW1lID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIGlmKCAhdGhpcy5tZW51SXRlbS5jbGFzc0xpc3QuY29udGFpbnMoJ2VkaXQnKSApIHtcblxuICAgICAgICAgICAgICAgIC8vaGlkZSB0aGUgbGlua1xuICAgICAgICAgICAgICAgIHRoaXMubWVudUl0ZW0ucXVlcnlTZWxlY3RvcignYS5tZW51SXRlbUxpbmsnKS5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuXG4gICAgICAgICAgICAgICAgLy9pbnNlcnQgdGhlIGlucHV0IGZpZWxkXG4gICAgICAgICAgICAgICAgdmFyIG5ld0lucHV0ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnaW5wdXQnKTtcbiAgICAgICAgICAgICAgICBuZXdJbnB1dC50eXBlID0gJ3RleHQnO1xuICAgICAgICAgICAgICAgIG5ld0lucHV0LnNldEF0dHJpYnV0ZSgnbmFtZScsICdwYWdlJyk7XG4gICAgICAgICAgICAgICAgbmV3SW5wdXQuc2V0QXR0cmlidXRlKCd2YWx1ZScsIHRoaXMubmFtZSk7XG4gICAgICAgICAgICAgICAgdGhpcy5tZW51SXRlbS5pbnNlcnRCZWZvcmUobmV3SW5wdXQsIHRoaXMubWVudUl0ZW0uZmlyc3RDaGlsZCk7XG5cbiAgICAgICAgICAgICAgICBuZXdJbnB1dC5mb2N1cygpO1xuXG4gICAgICAgICAgICAgICAgdmFyIHRtcFN0ciA9IG5ld0lucHV0LmdldEF0dHJpYnV0ZSgndmFsdWUnKTtcbiAgICAgICAgICAgICAgICBuZXdJbnB1dC5zZXRBdHRyaWJ1dGUoJ3ZhbHVlJywgJycpO1xuICAgICAgICAgICAgICAgIG5ld0lucHV0LnNldEF0dHJpYnV0ZSgndmFsdWUnLCB0bXBTdHIpO1xuXG4gICAgICAgICAgICAgICAgdGhpcy5tZW51SXRlbS5jbGFzc0xpc3QuYWRkKCdlZGl0Jyk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBVcGRhdGVzIHRoaXMgcGFnZSdzIG5hbWUgKGV2ZW50IGhhbmRsZXIgZm9yIHRoZSBzYXZlIGJ1dHRvbilcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy51cGRhdGVQYWdlTmFtZUV2ZW50ID0gZnVuY3Rpb24oZWwpIHtcblxuICAgICAgICAgICAgaWYoIHRoaXMubWVudUl0ZW0uY2xhc3NMaXN0LmNvbnRhaW5zKCdlZGl0JykgKSB7XG5cbiAgICAgICAgICAgICAgICAvL2VsIGlzIHRoZSBjbGlja2VkIGJ1dHRvbiwgd2UnbGwgbmVlZCBhY2Nlc3MgdG8gdGhlIGlucHV0XG4gICAgICAgICAgICAgICAgdmFyIHRoZUlucHV0ID0gdGhpcy5tZW51SXRlbS5xdWVyeVNlbGVjdG9yKCdpbnB1dFtuYW1lPVwicGFnZVwiXScpO1xuXG4gICAgICAgICAgICAgICAgLy9tYWtlIHN1cmUgdGhlIHBhZ2UncyBuYW1lIGlzIE9LXG4gICAgICAgICAgICAgICAgaWYoIHNpdGUuY2hlY2tQYWdlTmFtZSh0aGVJbnB1dC52YWx1ZSkgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5uYW1lID0gc2l0ZS5wcmVwUGFnZU5hbWUoIHRoZUlucHV0LnZhbHVlICk7XG5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5tZW51SXRlbS5xdWVyeVNlbGVjdG9yKCdpbnB1dFtuYW1lPVwicGFnZVwiXScpLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLm1lbnVJdGVtLnF1ZXJ5U2VsZWN0b3IoJ2EubWVudUl0ZW1MaW5rJykuaW5uZXJIVE1MID0gdGhpcy5uYW1lO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLm1lbnVJdGVtLnF1ZXJ5U2VsZWN0b3IoJ2EubWVudUl0ZW1MaW5rJykuc3R5bGUuZGlzcGxheSA9ICdibG9jayc7XG5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5tZW51SXRlbS5jbGFzc0xpc3QucmVtb3ZlKCdlZGl0Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy91cGRhdGUgdGhlIGxpbmtzIGRyb3Bkb3duIGl0ZW1cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5saW5rc0Ryb3Bkb3duSXRlbS50ZXh0ID0gdGhpcy5uYW1lO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLmxpbmtzRHJvcGRvd25JdGVtLnNldEF0dHJpYnV0ZSgndmFsdWUnLCB0aGlzLm5hbWUrXCIuaHRtbFwiKTtcblxuICAgICAgICAgICAgICAgICAgICAvL3VwZGF0ZSB0aGUgcGFnZSBuYW1lIG9uIHRoZSBjYW52YXNcbiAgICAgICAgICAgICAgICAgICAgc2l0ZS5wYWdlVGl0bGUuaW5uZXJIVE1MID0gdGhpcy5uYW1lO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vY2hhbmdlZCBwYWdlIHRpdGxlLCB3ZSd2ZSBnb3QgcGVuZGluZyBjaGFuZ2VzXG4gICAgICAgICAgICAgICAgICAgIHNpdGUuc2V0UGVuZGluZ0NoYW5nZXModHJ1ZSk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgICAgIGFsZXJ0KHNpdGUucGFnZU5hbWVFcnJvcik7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBkZWxldGVzIHRoaXMgZW50aXJlIHBhZ2VcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5kZWxldGUgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9kZWxldGUgZnJvbSB0aGUgU2l0ZVxuICAgICAgICAgICAgZm9yKCB2YXIgaSBpbiBzaXRlLnNpdGVQYWdlcyApIHtcblxuICAgICAgICAgICAgICAgIGlmKCBzaXRlLnNpdGVQYWdlc1tpXSA9PT0gdGhpcyApIHsvL2dvdCBhIG1hdGNoIVxuXG4gICAgICAgICAgICAgICAgICAgIC8vZGVsZXRlIGZyb20gc2l0ZS5zaXRlUGFnZXNcbiAgICAgICAgICAgICAgICAgICAgc2l0ZS5zaXRlUGFnZXMuc3BsaWNlKGksIDEpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vZGVsZXRlIGZyb20gY2FudmFzXG4gICAgICAgICAgICAgICAgICAgIHRoaXMucGFyZW50VUwucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9hZGQgdG8gZGVsZXRlZCBwYWdlc1xuICAgICAgICAgICAgICAgICAgICBzaXRlLnBhZ2VzVG9EZWxldGUucHVzaCh0aGlzLm5hbWUpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vZGVsZXRlIHRoZSBwYWdlJ3MgbWVudSBpdGVtXG4gICAgICAgICAgICAgICAgICAgIHRoaXMubWVudUl0ZW0ucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9kZWxldCB0aGUgcGFnZXMgbGluayBkcm9wZG93biBpdGVtXG4gICAgICAgICAgICAgICAgICAgIHRoaXMubGlua3NEcm9wZG93bkl0ZW0ucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9hY3RpdmF0ZSB0aGUgZmlyc3QgcGFnZVxuICAgICAgICAgICAgICAgICAgICBzaXRlLnNpdGVQYWdlc1swXS5zZWxlY3RQYWdlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9wYWdlIHdhcyBkZWxldGVkLCBzbyB3ZSd2ZSBnb3QgcGVuZGluZyBjaGFuZ2VzXG4gICAgICAgICAgICAgICAgICAgIHNpdGUuc2V0UGVuZGluZ0NoYW5nZXModHJ1ZSk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBjaGVja3MgaWYgdGhlIHBhZ2UgaXMgZW1wdHksIGlmIHNvIHNob3cgdGhlICdlbXB0eScgbWVzc2FnZVxuICAgICAgICAqL1xuICAgICAgICB0aGlzLmlzRW1wdHkgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgaWYoIHRoaXMuYmxvY2tzLmxlbmd0aCA9PT0gMCApIHtcblxuICAgICAgICAgICAgICAgIHNpdGUubWVzc2FnZVN0YXJ0LnN0eWxlLmRpc3BsYXkgPSAnYmxvY2snO1xuICAgICAgICAgICAgICAgIHNpdGUuZGl2RnJhbWVXcmFwcGVyLmNsYXNzTGlzdC5hZGQoJ2VtcHR5Jyk7XG5cbiAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICBzaXRlLm1lc3NhZ2VTdGFydC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICAgICAgICAgIHNpdGUuZGl2RnJhbWVXcmFwcGVyLmNsYXNzTGlzdC5yZW1vdmUoJ2VtcHR5Jyk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBwcmVwcy9zdHJpcHMgdGhpcyBwYWdlIGRhdGEgZm9yIGEgcGVuZGluZyBhamF4IHJlcXVlc3RcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5wcmVwRm9yU2F2ZSA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB2YXIgcGFnZSA9IHt9O1xuXG4gICAgICAgICAgICBwYWdlLm5hbWUgPSB0aGlzLm5hbWU7XG4gICAgICAgICAgICBwYWdlLnBhZ2VTZXR0aW5ncyA9IHRoaXMucGFnZVNldHRpbmdzO1xuICAgICAgICAgICAgcGFnZS5zdGF0dXMgPSB0aGlzLnN0YXR1cztcbiAgICAgICAgICAgIHBhZ2UuYmxvY2tzID0gW107XG5cbiAgICAgICAgICAgIC8vcHJvY2VzcyB0aGUgYmxvY2tzXG5cbiAgICAgICAgICAgIGZvciggdmFyIHggPSAwOyB4IDwgdGhpcy5ibG9ja3MubGVuZ3RoOyB4KysgKSB7XG5cbiAgICAgICAgICAgICAgICB2YXIgYmxvY2sgPSB7fTtcblxuICAgICAgICAgICAgICAgIGlmKCB0aGlzLmJsb2Nrc1t4XS5zYW5kYm94ICkge1xuXG4gICAgICAgICAgICAgICAgICAgIGJsb2NrLmZyYW1lQ29udGVudCA9IFwiPGh0bWw+XCIrJCgnI3NhbmRib3hlcyAjJyt0aGlzLmJsb2Nrc1t4XS5zYW5kYm94KS5jb250ZW50cygpLmZpbmQoJ2h0bWwnKS5odG1sKCkrXCI8L2h0bWw+XCI7XG4gICAgICAgICAgICAgICAgICAgIGJsb2NrLnNhbmRib3ggPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICBibG9jay5sb2FkZXJGdW5jdGlvbiA9IHRoaXMuYmxvY2tzW3hdLnNhbmRib3hfbG9hZGVyO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICAgICBibG9jay5mcmFtZUNvbnRlbnQgPSB0aGlzLmJsb2Nrc1t4XS5nZXRTb3VyY2UoKTtcbiAgICAgICAgICAgICAgICAgICAgYmxvY2suc2FuZGJveCA9IGZhbHNlO1xuICAgICAgICAgICAgICAgICAgICBibG9jay5sb2FkZXJGdW5jdGlvbiA9ICcnO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgYmxvY2suZnJhbWVIZWlnaHQgPSB0aGlzLmJsb2Nrc1t4XS5mcmFtZUhlaWdodDtcbiAgICAgICAgICAgICAgICBibG9jay5vcmlnaW5hbFVybCA9IHRoaXMuYmxvY2tzW3hdLm9yaWdpbmFsVXJsO1xuXG4gICAgICAgICAgICAgICAgcGFnZS5ibG9ja3MucHVzaChibG9jayk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIHBhZ2U7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgZ2VuZXJhdGVzIHRoZSBmdWxsIHBhZ2UsIHVzaW5nIHNrZWxldG9uLmh0bWxcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5mdWxsUGFnZSA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB2YXIgcGFnZSA9IHRoaXM7Ly9yZWZlcmVuY2UgdG8gc2VsZiBmb3IgbGF0ZXJcbiAgICAgICAgICAgIHBhZ2Uuc2NyaXB0cyA9IFtdOy8vbWFrZSBzdXJlIGl0J3MgZW1wdHksIHdlJ2xsIHN0b3JlIHNjcmlwdCBVUkxzIGluIHRoZXJlIGxhdGVyXG5cbiAgICAgICAgICAgIHZhciBuZXdEb2NNYWluUGFyZW50ID0gJCgnaWZyYW1lI3NrZWxldG9uJykuY29udGVudHMoKS5maW5kKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKTtcblxuICAgICAgICAgICAgLy9lbXB0eSBvdXQgdGhlIHNrZWxldG9uIGZpcnN0XG4gICAgICAgICAgICAkKCdpZnJhbWUjc2tlbGV0b24nKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciApLmh0bWwoJycpO1xuXG4gICAgICAgICAgICAvL3JlbW92ZSBvbGQgc2NyaXB0IHRhZ3NcbiAgICAgICAgICAgICQoJ2lmcmFtZSNza2VsZXRvbicpLmNvbnRlbnRzKCkuZmluZCggJ3NjcmlwdCcgKS5lYWNoKGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmUoKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB2YXIgdGhlQ29udGVudHM7XG5cbiAgICAgICAgICAgIGZvciggdmFyIGkgaW4gdGhpcy5ibG9ja3MgKSB7XG5cbiAgICAgICAgICAgICAgICAvL2dyYWIgdGhlIGJsb2NrIGNvbnRlbnRcbiAgICAgICAgICAgICAgICBpZiAodGhpcy5ibG9ja3NbaV0uc2FuZGJveCAhPT0gZmFsc2UpIHtcblxuICAgICAgICAgICAgICAgICAgICB0aGVDb250ZW50cyA9ICQoJyNzYW5kYm94ZXMgIycrdGhpcy5ibG9ja3NbaV0uc2FuZGJveCkuY29udGVudHMoKS5maW5kKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKS5jbG9uZSgpO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICAgICB0aGVDb250ZW50cyA9ICQodGhpcy5ibG9ja3NbaV0uZnJhbWVEb2N1bWVudC5ib2R5KS5maW5kKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKS5jbG9uZSgpO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLy9yZW1vdmUgdmlkZW8gZnJhbWVDb3ZlcnNcbiAgICAgICAgICAgICAgICB0aGVDb250ZW50cy5maW5kKCcuZnJhbWVDb3ZlcicpLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgLy9yZW1vdmUgdmlkZW8gZnJhbWVXcmFwcGVyc1xuICAgICAgICAgICAgICAgIHRoZUNvbnRlbnRzLmZpbmQoJy52aWRlb1dyYXBwZXInKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIGNudCA9ICQodGhpcykuY29udGVudHMoKTtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5yZXBsYWNlV2l0aChjbnQpO1xuXG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvL3JlbW92ZSBzdHlsZSBsZWZ0b3ZlcnMgZnJvbSB0aGUgc3R5bGUgZWRpdG9yXG4gICAgICAgICAgICAgICAgZm9yKCB2YXIga2V5IGluIGJDb25maWcuZWRpdGFibGVJdGVtcyApIHtcblxuICAgICAgICAgICAgICAgICAgICB0aGVDb250ZW50cy5maW5kKCBrZXkgKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQXR0cignZGF0YS1zZWxlY3RvcicpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmNzcygnb3V0bGluZScsICcnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuY3NzKCdvdXRsaW5lLW9mZnNldCcsICcnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuY3NzKCdjdXJzb3InLCAnJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKCAkKHRoaXMpLmF0dHIoJ3N0eWxlJykgPT09ICcnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmVBdHRyKCdzdHlsZScpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvL3JlbW92ZSBzdHlsZSBsZWZ0b3ZlcnMgZnJvbSB0aGUgY29udGVudCBlZGl0b3JcbiAgICAgICAgICAgICAgICBmb3IgKCB2YXIgeCA9IDA7IHggPCBiQ29uZmlnLmVkaXRhYmxlQ29udGVudC5sZW5ndGg7ICsreCkge1xuXG4gICAgICAgICAgICAgICAgICAgIHRoZUNvbnRlbnRzLmZpbmQoIGJDb25maWcuZWRpdGFibGVDb250ZW50W3hdICkuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZUF0dHIoJ2RhdGEtc2VsZWN0b3InKTtcblxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vYXBwZW5kIHRvIERPTSBpbiB0aGUgc2tlbGV0b25cbiAgICAgICAgICAgICAgICBuZXdEb2NNYWluUGFyZW50LmFwcGVuZCggJCh0aGVDb250ZW50cy5odG1sKCkpICk7XG5cbiAgICAgICAgICAgICAgICAvL2RvIHdlIG5lZWQgdG8gaW5qZWN0IGFueSBzY3JpcHRzP1xuICAgICAgICAgICAgICAgIHZhciBzY3JpcHRzID0gJCh0aGlzLmJsb2Nrc1tpXS5mcmFtZURvY3VtZW50LmJvZHkpLmZpbmQoJ3NjcmlwdCcpO1xuICAgICAgICAgICAgICAgIHZhciB0aGVJZnJhbWUgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChcInNrZWxldG9uXCIpO1xuXG4gICAgICAgICAgICAgICAgaWYoIHNjcmlwdHMuc2l6ZSgpID4gMCApIHtcblxuICAgICAgICAgICAgICAgICAgICBzY3JpcHRzLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHNjcmlwdDtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoICQodGhpcykudGV4dCgpICE9PSAnJyApIHsvL3NjcmlwdCB0YWdzIHdpdGggY29udGVudFxuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2NyaXB0ID0gdGhlSWZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQuY3JlYXRlRWxlbWVudChcInNjcmlwdFwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzY3JpcHQudHlwZSA9ICd0ZXh0L2phdmFzY3JpcHQnO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdC5pbm5lckhUTUwgPSAkKHRoaXMpLnRleHQoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoZUlmcmFtZS5jb250ZW50V2luZG93LmRvY3VtZW50LmJvZHkuYXBwZW5kQ2hpbGQoc2NyaXB0KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmKCAkKHRoaXMpLmF0dHIoJ3NyYycpICE9PSBudWxsICYmIHBhZ2Uuc2NyaXB0cy5pbmRleE9mKCQodGhpcykuYXR0cignc3JjJykpID09PSAtMSApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAvL3VzZSBpbmRleE9mIHRvIG1ha2Ugc3VyZSBlYWNoIHNjcmlwdCBvbmx5IGFwcGVhcnMgb24gdGhlIHByb2R1Y2VkIHBhZ2Ugb25jZVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2NyaXB0ID0gdGhlSWZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQuY3JlYXRlRWxlbWVudChcInNjcmlwdFwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzY3JpcHQudHlwZSA9ICd0ZXh0L2phdmFzY3JpcHQnO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdC5zcmMgPSAkKHRoaXMpLmF0dHIoJ3NyYycpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhlSWZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZChzY3JpcHQpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcGFnZS5zY3JpcHRzLnB1c2goJCh0aGlzKS5hdHRyKCdzcmMnKSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBjb25zb2xlLmxvZyh0aGlzLnNjcmlwdHMpO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGNsZWFyIG91dCB0aGlzIHBhZ2VcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5jbGVhciA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB2YXIgYmxvY2sgPSB0aGlzLmJsb2Nrcy5wb3AoKTtcblxuICAgICAgICAgICAgd2hpbGUoIGJsb2NrICE9PSB1bmRlZmluZWQgKSB7XG5cbiAgICAgICAgICAgICAgICBibG9jay5kZWxldGUoKTtcblxuICAgICAgICAgICAgICAgIGJsb2NrID0gdGhpcy5ibG9ja3MucG9wKCk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG5cbiAgICAgICAgLy9sb29wIHRocm91Z2ggdGhlIGZyYW1lcy9ibG9ja3NcblxuICAgICAgICBpZiggcGFnZS5oYXNPd25Qcm9wZXJ0eSgnYmxvY2tzJykgKSB7XG5cbiAgICAgICAgICAgIGZvciggdmFyIHggPSAwOyB4IDwgcGFnZS5ibG9ja3MubGVuZ3RoOyB4KysgKSB7XG5cbiAgICAgICAgICAgICAgICAvL2NyZWF0ZSBuZXcgQmxvY2tcblxuICAgICAgICAgICAgICAgIHZhciBuZXdCbG9jayA9IG5ldyBCbG9jaygpO1xuXG4gICAgICAgICAgICAgICAgcGFnZS5ibG9ja3NbeF0uc3JjID0gYXBwVUkuc2l0ZVVybCtcInNpdGUvZ2V0ZnJhbWUvXCIrcGFnZS5ibG9ja3NbeF0uaWQ7XG5cbiAgICAgICAgICAgICAgICAvL3NhbmRib3hlZCBibG9jaz9cbiAgICAgICAgICAgICAgICBpZiggcGFnZS5ibG9ja3NbeF0uZnJhbWVzX3NhbmRib3ggPT09ICcxJykge1xuXG4gICAgICAgICAgICAgICAgICAgIG5ld0Jsb2NrLnNhbmRib3ggPSB0cnVlO1xuICAgICAgICAgICAgICAgICAgICBuZXdCbG9jay5zYW5kYm94X2xvYWRlciA9IHBhZ2UuYmxvY2tzW3hdLmZyYW1lc19sb2FkZXJmdW5jdGlvbjtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIG5ld0Jsb2NrLmZyYW1lSUQgPSBwYWdlLmJsb2Nrc1t4XS5mcmFtZXNfaWQ7XG4gICAgICAgICAgICAgICAgbmV3QmxvY2suY3JlYXRlUGFyZW50TEkocGFnZS5ibG9ja3NbeF0uZnJhbWVzX2hlaWdodCk7XG4gICAgICAgICAgICAgICAgbmV3QmxvY2suY3JlYXRlRnJhbWUocGFnZS5ibG9ja3NbeF0pO1xuICAgICAgICAgICAgICAgIG5ld0Jsb2NrLmNyZWF0ZUZyYW1lQ292ZXIoKTtcbiAgICAgICAgICAgICAgICBuZXdCbG9jay5pbnNlcnRCbG9ja0ludG9Eb20odGhpcy5wYXJlbnRVTCk7XG5cbiAgICAgICAgICAgICAgICAvL2FkZCB0aGUgYmxvY2sgdG8gdGhlIG5ldyBwYWdlXG4gICAgICAgICAgICAgICAgdGhpcy5ibG9ja3MucHVzaChuZXdCbG9jayk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9XG5cbiAgICAgICAgLy9hZGQgdGhpcyBwYWdlIHRvIHRoZSBzaXRlIG9iamVjdFxuICAgICAgICBzaXRlLnNpdGVQYWdlcy5wdXNoKCB0aGlzICk7XG5cbiAgICAgICAgLy9wbGFudCB0aGUgbmV3IFVMIGluIHRoZSBET00gKG9uIHRoZSBjYW52YXMpXG4gICAgICAgIHNpdGUuZGl2Q2FudmFzLmFwcGVuZENoaWxkKHRoaXMucGFyZW50VUwpO1xuXG4gICAgICAgIC8vbWFrZSB0aGUgYmxvY2tzL2ZyYW1lcyBpbiBlYWNoIHBhZ2Ugc29ydGFibGVcblxuICAgICAgICB2YXIgdGhlUGFnZSA9IHRoaXM7XG5cbiAgICAgICAgJCh0aGlzLnBhcmVudFVMKS5zb3J0YWJsZSh7XG4gICAgICAgICAgICByZXZlcnQ6IHRydWUsXG4gICAgICAgICAgICBwbGFjZWhvbGRlcjogXCJkcm9wLWhvdmVyXCIsXG4gICAgICAgICAgICBzdG9wOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBiZWZvcmVTdG9wOiBmdW5jdGlvbihldmVudCwgdWkpe1xuXG4gICAgICAgICAgICAgICAgLy90ZW1wbGF0ZSBvciByZWd1bGFyIGJsb2NrP1xuICAgICAgICAgICAgICAgIHZhciBhdHRyID0gdWkuaXRlbS5hdHRyKCdkYXRhLWZyYW1lcycpO1xuXG4gICAgICAgICAgICAgICAgdmFyIG5ld0Jsb2NrO1xuXG4gICAgICAgICAgICAgICAgaWYgKHR5cGVvZiBhdHRyICE9PSB0eXBlb2YgdW5kZWZpbmVkICYmIGF0dHIgIT09IGZhbHNlKSB7Ly90ZW1wbGF0ZSwgYnVpbGQgaXRcblxuICAgICAgICAgICAgICAgICAgICAkKCcjc3RhcnQnKS5oaWRlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9jbGVhciBvdXQgYWxsIGJsb2NrcyBvbiB0aGlzIHBhZ2VcbiAgICAgICAgICAgICAgICAgICAgdGhlUGFnZS5jbGVhcigpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vY3JlYXRlIHRoZSBuZXcgZnJhbWVzXG4gICAgICAgICAgICAgICAgICAgIHZhciBmcmFtZUlEcyA9IHVpLml0ZW0uYXR0cignZGF0YS1mcmFtZXMnKS5zcGxpdCgnLScpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgaGVpZ2h0cyA9IHVpLml0ZW0uYXR0cignZGF0YS1oZWlnaHRzJykuc3BsaXQoJy0nKTtcbiAgICAgICAgICAgICAgICAgICAgdmFyIHVybHMgPSB1aS5pdGVtLmF0dHIoJ2RhdGEtb3JpZ2luYWx1cmxzJykuc3BsaXQoJy0nKTtcblxuICAgICAgICAgICAgICAgICAgICBmb3IoIHZhciB4ID0gMDsgeCA8IGZyYW1lSURzLmxlbmd0aDsgeCsrKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0Jsb2NrID0gbmV3IEJsb2NrKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBuZXdCbG9jay5jcmVhdGVQYXJlbnRMSShoZWlnaHRzW3hdKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIGZyYW1lRGF0YSA9IHt9O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBmcmFtZURhdGEuc3JjID0gYXBwVUkuc2l0ZVVybCsnc2l0ZS9nZXRmcmFtZS8nK2ZyYW1lSURzW3hdO1xuICAgICAgICAgICAgICAgICAgICAgICAgZnJhbWVEYXRhLmZyYW1lc19vcmlnaW5hbF91cmwgPSBhcHBVSS5zaXRlVXJsKydzaXRlL2dldGZyYW1lLycrZnJhbWVJRHNbeF07XG4gICAgICAgICAgICAgICAgICAgICAgICBmcmFtZURhdGEuZnJhbWVzX2hlaWdodCA9IGhlaWdodHNbeF07XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0Jsb2NrLmNyZWF0ZUZyYW1lKCBmcmFtZURhdGEgKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0Jsb2NrLmNyZWF0ZUZyYW1lQ292ZXIoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0Jsb2NrLmluc2VydEJsb2NrSW50b0RvbSh0aGVQYWdlLnBhcmVudFVMKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9hZGQgdGhlIGJsb2NrIHRvIHRoZSBuZXcgcGFnZVxuICAgICAgICAgICAgICAgICAgICAgICAgdGhlUGFnZS5ibG9ja3MucHVzaChuZXdCbG9jayk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vZHJvcHBlZCBlbGVtZW50LCBzbyB3ZSd2ZSBnb3QgcGVuZGluZyBjaGFuZ2VzXG4gICAgICAgICAgICAgICAgICAgICAgICBzaXRlLnNldFBlbmRpbmdDaGFuZ2VzKHRydWUpO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAvL3NldCB0aGUgdGVtcGF0ZUlEXG4gICAgICAgICAgICAgICAgICAgIGJ1aWxkZXJVSS50ZW1wbGF0ZUlEID0gdWkuaXRlbS5hdHRyKCdkYXRhLXBhZ2VpZCcpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vbWFrZSBzdXJlIG5vdGhpbmcgZ2V0cyBkcm9wcGVkIGluIHRoZSBsc2l0XG4gICAgICAgICAgICAgICAgICAgIHVpLml0ZW0uaHRtbChudWxsKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2RlbGV0ZSBkcmFnIHBsYWNlIGhvbGRlclxuICAgICAgICAgICAgICAgICAgICAkKCdib2R5IC51aS1zb3J0YWJsZS1oZWxwZXInKS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSB7Ly9yZWd1bGFyIGJsb2NrXG5cbiAgICAgICAgICAgICAgICAgICAgLy9hcmUgd2UgZGVhbGluZyB3aXRoIGEgbmV3IGJsb2NrIGJlaW5nIGRyb3BwZWQgb250byB0aGUgY2FudmFzLCBvciBhIHJlb3JkZXJpbmcgb2cgYmxvY2tzIGFscmVhZHkgb24gdGhlIGNhbnZhcz9cblxuICAgICAgICAgICAgICAgICAgICBpZiggdWkuaXRlbS5maW5kKCcuZnJhbWVDb3ZlciA+IGJ1dHRvbicpLnNpemUoKSA+IDAgKSB7Ly9yZS1vcmRlcmluZyBvZiBibG9ja3Mgb24gY2FudmFzXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vbm8gbmVlZCB0byBjcmVhdGUgYSBuZXcgYmxvY2sgb2JqZWN0LCB3ZSBzaW1wbHkgbmVlZCB0byBtYWtlIHN1cmUgdGhlIHBvc2l0aW9uIG9mIHRoZSBleGlzdGluZyBibG9jayBpbiB0aGUgU2l0ZSBvYmplY3RcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vaXMgY2hhbmdlZCB0byByZWZsZWN0IHRoZSBuZXcgcG9zaXRpb24gb2YgdGhlIGJsb2NrIG9uIHRoIGNhbnZhc1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgZnJhbWVJRCA9IHVpLml0ZW0uZmluZCgnaWZyYW1lJykuYXR0cignaWQnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBuZXdQb3MgPSB1aS5pdGVtLmluZGV4KCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHNpdGUuYWN0aXZlUGFnZS5zZXRQb3NpdGlvbihmcmFtZUlELCBuZXdQb3MpO1xuXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7Ly9uZXcgYmxvY2sgb24gY2FudmFzXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vbmV3IGJsb2NrXG4gICAgICAgICAgICAgICAgICAgICAgICBuZXdCbG9jayA9IG5ldyBCbG9jaygpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBuZXdCbG9jay5wbGFjZU9uQ2FudmFzKHVpKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBzdGFydDogZnVuY3Rpb24oZXZlbnQsIHVpKXtcblxuICAgICAgICAgICAgICAgIGlmKCB1aS5pdGVtLmZpbmQoJy5mcmFtZUNvdmVyJykuc2l6ZSgpICE9PSAwICkge1xuICAgICAgICAgICAgICAgICAgICBidWlsZGVyVUkuZnJhbWVDb250ZW50cyA9IHVpLml0ZW0uZmluZCgnaWZyYW1lJykuY29udGVudHMoKS5maW5kKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKS5odG1sKCk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgb3ZlcjogZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICQoJyNzdGFydCcpLmhpZGUoKTtcblxuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICAvL2FkZCB0byB0aGUgcGFnZXMgbWVudVxuICAgICAgICB0aGlzLm1lbnVJdGVtID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnTEknKTtcbiAgICAgICAgdGhpcy5tZW51SXRlbS5pbm5lckhUTUwgPSB0aGlzLnBhZ2VNZW51VGVtcGxhdGU7XG5cbiAgICAgICAgJCh0aGlzLm1lbnVJdGVtKS5maW5kKCdhOmZpcnN0JykudGV4dChwYWdlTmFtZSkuYXR0cignaHJlZicsICcjcGFnZScrY291bnRlcik7XG5cbiAgICAgICAgdmFyIHRoZUxpbmsgPSAkKHRoaXMubWVudUl0ZW0pLmZpbmQoJ2E6Zmlyc3QnKS5nZXQoMCk7XG5cbiAgICAgICAgLy9iaW5kIHNvbWUgZXZlbnRzXG4gICAgICAgIHRoaXMubWVudUl0ZW0uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCB0aGlzLCBmYWxzZSk7XG5cbiAgICAgICAgdGhpcy5tZW51SXRlbS5xdWVyeVNlbGVjdG9yKCdhLmZpbGVFZGl0JykuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCB0aGlzLCBmYWxzZSk7XG4gICAgICAgIHRoaXMubWVudUl0ZW0ucXVlcnlTZWxlY3RvcignYS5maWxlU2F2ZScpLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgdGhpcywgZmFsc2UpO1xuICAgICAgICB0aGlzLm1lbnVJdGVtLnF1ZXJ5U2VsZWN0b3IoJ2EuZmlsZURlbCcpLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgdGhpcywgZmFsc2UpO1xuXG4gICAgICAgIC8vYWRkIHRvIHRoZSBwYWdlIGxpbmsgZHJvcGRvd25cbiAgICAgICAgdGhpcy5saW5rc0Ryb3Bkb3duSXRlbSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ09QVElPTicpO1xuICAgICAgICB0aGlzLmxpbmtzRHJvcGRvd25JdGVtLnNldEF0dHJpYnV0ZSgndmFsdWUnLCBwYWdlTmFtZStcIi5odG1sXCIpO1xuICAgICAgICB0aGlzLmxpbmtzRHJvcGRvd25JdGVtLnRleHQgPSBwYWdlTmFtZTtcblxuICAgICAgICBidWlsZGVyVUkuZHJvcGRvd25QYWdlTGlua3MuYXBwZW5kQ2hpbGQoIHRoaXMubGlua3NEcm9wZG93bkl0ZW0gKTtcblxuICAgICAgICBzaXRlLnBhZ2VzTWVudS5hcHBlbmRDaGlsZCh0aGlzLm1lbnVJdGVtKTtcblxuICAgIH1cblxuICAgIFBhZ2UucHJvdG90eXBlLmhhbmRsZUV2ZW50ID0gZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgc3dpdGNoIChldmVudC50eXBlKSB7XG4gICAgICAgICAgICBjYXNlIFwiY2xpY2tcIjpcblxuICAgICAgICAgICAgICAgIGlmKCBldmVudC50YXJnZXQuY2xhc3NMaXN0LmNvbnRhaW5zKCdmaWxlRWRpdCcpICkge1xuXG4gICAgICAgICAgICAgICAgICAgIHRoaXMuZWRpdFBhZ2VOYW1lKCk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIGV2ZW50LnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ2ZpbGVTYXZlJykgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy51cGRhdGVQYWdlTmFtZUV2ZW50KGV2ZW50LnRhcmdldCk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIGV2ZW50LnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ2ZpbGVEZWwnKSApIHtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgdGhlUGFnZSA9IHRoaXM7XG5cbiAgICAgICAgICAgICAgICAgICAgJChidWlsZGVyVUkubW9kYWxEZWxldGVQYWdlKS5tb2RhbCgnc2hvdycpO1xuXG4gICAgICAgICAgICAgICAgICAgICQoYnVpbGRlclVJLm1vZGFsRGVsZXRlUGFnZSkub2ZmKCdjbGljaycsICcjZGVsZXRlUGFnZUNvbmZpcm0nKS5vbignY2xpY2snLCAnI2RlbGV0ZVBhZ2VDb25maXJtJywgZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRoZVBhZ2UuZGVsZXRlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQoYnVpbGRlclVJLm1vZGFsRGVsZXRlUGFnZSkubW9kYWwoJ2hpZGUnKTtcblxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zZWxlY3RQYWdlKCk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgfVxuICAgIH07XG5cblxuICAgIC8qXG4gICAgICAgIEJsb2NrIGNvbnN0cnVjdG9yXG4gICAgKi9cbiAgICBmdW5jdGlvbiBCbG9jayAoKSB7XG5cbiAgICAgICAgdGhpcy5mcmFtZUlEID0gMDtcbiAgICAgICAgdGhpcy5zYW5kYm94ID0gZmFsc2U7XG4gICAgICAgIHRoaXMuc2FuZGJveF9sb2FkZXIgPSAnJztcbiAgICAgICAgdGhpcy5zdGF0dXMgPSAnJzsvLycnLCAnY2hhbmdlZCcgb3IgJ25ldydcbiAgICAgICAgdGhpcy5vcmlnaW5hbFVybCA9ICcnO1xuXG4gICAgICAgIHRoaXMucGFyZW50TEkgPSB7fTtcbiAgICAgICAgdGhpcy5mcmFtZUNvdmVyID0ge307XG4gICAgICAgIHRoaXMuZnJhbWUgPSB7fTtcbiAgICAgICAgdGhpcy5mcmFtZURvY3VtZW50ID0ge307XG4gICAgICAgIHRoaXMuZnJhbWVIZWlnaHQgPSAwO1xuXG4gICAgICAgIHRoaXMuYW5ub3QgPSB7fTtcbiAgICAgICAgdGhpcy5hbm5vdFRpbWVvdXQgPSB7fTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgY3JlYXRlcyB0aGUgcGFyZW50IGNvbnRhaW5lciAoTEkpXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuY3JlYXRlUGFyZW50TEkgPSBmdW5jdGlvbihoZWlnaHQpIHtcblxuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ0xJJyk7XG4gICAgICAgICAgICB0aGlzLnBhcmVudExJLnNldEF0dHJpYnV0ZSgnY2xhc3MnLCAnZWxlbWVudCcpO1xuICAgICAgICAgICAgLy90aGlzLnBhcmVudExJLnNldEF0dHJpYnV0ZSgnc3R5bGUnLCAnaGVpZ2h0OiAnK2hlaWdodCsncHgnKTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBjcmVhdGVzIHRoZSBpZnJhbWUgb24gdGhlIGNhbnZhc1xuICAgICAgICAqL1xuICAgICAgICB0aGlzLmNyZWF0ZUZyYW1lID0gZnVuY3Rpb24oZnJhbWUpIHtcblxuICAgICAgICAgICAgdGhpcy5mcmFtZSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ0lGUkFNRScpO1xuICAgICAgICAgICAgdGhpcy5mcmFtZS5zZXRBdHRyaWJ1dGUoJ2ZyYW1lYm9yZGVyJywgMCk7XG4gICAgICAgICAgICB0aGlzLmZyYW1lLnNldEF0dHJpYnV0ZSgnc2Nyb2xsaW5nJywgMCk7XG4gICAgICAgICAgICB0aGlzLmZyYW1lLnNldEF0dHJpYnV0ZSgnc3JjJywgZnJhbWUuc3JjKTtcbiAgICAgICAgICAgIHRoaXMuZnJhbWUuc2V0QXR0cmlidXRlKCdkYXRhLW9yaWdpbmFsdXJsJywgZnJhbWUuZnJhbWVzX29yaWdpbmFsX3VybCk7XG4gICAgICAgICAgICB0aGlzLm9yaWdpbmFsVXJsID0gZnJhbWUuZnJhbWVzX29yaWdpbmFsX3VybDtcbiAgICAgICAgICAgIC8vdGhpcy5mcmFtZS5zZXRBdHRyaWJ1dGUoJ2RhdGEtaGVpZ2h0JywgZnJhbWUuZnJhbWVzX2hlaWdodCk7XG4gICAgICAgICAgICAvL3RoaXMuZnJhbWVIZWlnaHQgPSBmcmFtZS5mcmFtZXNfaGVpZ2h0O1xuICAgICAgICAgICAgdGhpcy5mcmFtZS5zZXRBdHRyaWJ1dGUoJ3N0eWxlJywgJ2JhY2tncm91bmQ6ICcrXCIjZmZmZmZmIHVybCgnXCIrYXBwVUkuYmFzZVVybCtcInNyYy9pbWFnZXMvbG9hZGluZy5naWYnKSA1MCUgNTAlIG5vLXJlcGVhdFwiKTtcblxuICAgICAgICAgICAgJCh0aGlzLmZyYW1lKS51bmlxdWVJZCgpO1xuXG4gICAgICAgICAgICAvL3NhbmRib3g/XG4gICAgICAgICAgICBpZiggdGhpcy5zYW5kYm94ICE9PSBmYWxzZSApIHtcblxuICAgICAgICAgICAgICAgIHRoaXMuZnJhbWUuc2V0QXR0cmlidXRlKCdkYXRhLWxvYWRlcmZ1bmN0aW9uJywgdGhpcy5zYW5kYm94X2xvYWRlcik7XG4gICAgICAgICAgICAgICAgdGhpcy5mcmFtZS5zZXRBdHRyaWJ1dGUoJ2RhdGEtc2FuZGJveCcsIHRoaXMuc2FuZGJveCk7XG5cbiAgICAgICAgICAgICAgICAvL3JlY3JlYXRlIHRoZSBzYW5kYm94ZWQgaWZyYW1lIGVsc2V3aGVyZVxuICAgICAgICAgICAgICAgIHZhciBzYW5kYm94ZWRGcmFtZSA9ICQoJzxpZnJhbWUgc3JjPVwiJytmcmFtZS5zcmMrJ1wiIGlkPVwiJyt0aGlzLnNhbmRib3grJ1wiIHNhbmRib3g9XCJhbGxvdy1zYW1lLW9yaWdpblwiPjwvaWZyYW1lPicpO1xuICAgICAgICAgICAgICAgICQoJyNzYW5kYm94ZXMnKS5hcHBlbmQoIHNhbmRib3hlZEZyYW1lICk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBpbnNlcnQgdGhlIGlmcmFtZSBpbnRvIHRoZSBET00gb24gdGhlIGNhbnZhc1xuICAgICAgICAqL1xuICAgICAgICB0aGlzLmluc2VydEJsb2NrSW50b0RvbSA9IGZ1bmN0aW9uKHRoZVVMKSB7XG5cbiAgICAgICAgICAgIHRoaXMucGFyZW50TEkuYXBwZW5kQ2hpbGQodGhpcy5mcmFtZSk7XG4gICAgICAgICAgICB0aGVVTC5hcHBlbmRDaGlsZCggdGhpcy5wYXJlbnRMSSApO1xuXG4gICAgICAgICAgICB0aGlzLmZyYW1lLmFkZEV2ZW50TGlzdGVuZXIoJ2xvYWQnLCB0aGlzLCBmYWxzZSk7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgc2V0cyB0aGUgZnJhbWUgZG9jdW1lbnQgZm9yIHRoZSBibG9jaydzIGlmcmFtZVxuICAgICAgICAqL1xuICAgICAgICB0aGlzLnNldEZyYW1lRG9jdW1lbnQgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9zZXQgdGhlIGZyYW1lIGRvY3VtZW50IGFzIHdlbGxcbiAgICAgICAgICAgIGlmKCB0aGlzLmZyYW1lLmNvbnRlbnREb2N1bWVudCApIHtcbiAgICAgICAgICAgICAgICB0aGlzLmZyYW1lRG9jdW1lbnQgPSB0aGlzLmZyYW1lLmNvbnRlbnREb2N1bWVudDtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgdGhpcy5mcmFtZURvY3VtZW50ID0gdGhpcy5mcmFtZS5jb250ZW50V2luZG93LmRvY3VtZW50O1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL3RoaXMuaGVpZ2h0QWRqdXN0bWVudCgpO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGNyZWF0ZXMgdGhlIGZyYW1lIGNvdmVyIGFuZCBibG9jayBhY3Rpb24gYnV0dG9uXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuY3JlYXRlRnJhbWVDb3ZlciA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2J1aWxkIHRoZSBmcmFtZSBjb3ZlciBhbmQgYmxvY2sgYWN0aW9uIGJ1dHRvbnNcbiAgICAgICAgICAgIHRoaXMuZnJhbWVDb3ZlciA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ0RJVicpO1xuICAgICAgICAgICAgdGhpcy5mcmFtZUNvdmVyLmNsYXNzTGlzdC5hZGQoJ2ZyYW1lQ292ZXInKTtcbiAgICAgICAgICAgIHRoaXMuZnJhbWVDb3Zlci5jbGFzc0xpc3QuYWRkKCdmcmVzaCcpO1xuICAgICAgICAgICAgdGhpcy5mcmFtZUNvdmVyLnN0eWxlLmhlaWdodCA9IHRoaXMuZnJhbWVIZWlnaHQrXCJweFwiO1xuXG4gICAgICAgICAgICB2YXIgZGVsQnV0dG9uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnQlVUVE9OJyk7XG4gICAgICAgICAgICBkZWxCdXR0b24uc2V0QXR0cmlidXRlKCdjbGFzcycsICdidG4gYnRuLWRhbmdlciBkZWxldGVCbG9jaycpO1xuICAgICAgICAgICAgZGVsQnV0dG9uLnNldEF0dHJpYnV0ZSgndHlwZScsICdidXR0b24nKTtcbiAgICAgICAgICAgIGRlbEJ1dHRvbi5pbm5lckhUTUwgPSAnPHNwYW4gY2xhc3M9XCJmdWktdHJhc2hcIj48L3NwYW4+IHJlbW92ZSc7XG4gICAgICAgICAgICBkZWxCdXR0b24uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCB0aGlzLCBmYWxzZSk7XG5cbiAgICAgICAgICAgIHZhciByZXNldEJ1dHRvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ0JVVFRPTicpO1xuICAgICAgICAgICAgcmVzZXRCdXR0b24uc2V0QXR0cmlidXRlKCdjbGFzcycsICdidG4gYnRuLXdhcm5pbmcgcmVzZXRCbG9jaycpO1xuICAgICAgICAgICAgcmVzZXRCdXR0b24uc2V0QXR0cmlidXRlKCd0eXBlJywgJ2J1dHRvbicpO1xuICAgICAgICAgICAgcmVzZXRCdXR0b24uaW5uZXJIVE1MID0gJzxpIGNsYXNzPVwiZmEgZmEtcmVmcmVzaFwiPjwvaT4gcmVzZXQnO1xuICAgICAgICAgICAgcmVzZXRCdXR0b24uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCB0aGlzLCBmYWxzZSk7XG5cbiAgICAgICAgICAgIHZhciBodG1sQnV0dG9uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnQlVUVE9OJyk7XG4gICAgICAgICAgICBodG1sQnV0dG9uLnNldEF0dHJpYnV0ZSgnY2xhc3MnLCAnYnRuIGJ0bi1pbnZlcnNlIGh0bWxCbG9jaycpO1xuICAgICAgICAgICAgaHRtbEJ1dHRvbi5zZXRBdHRyaWJ1dGUoJ3R5cGUnLCAnYnV0dG9uJyk7XG4gICAgICAgICAgICBodG1sQnV0dG9uLmlubmVySFRNTCA9ICc8aSBjbGFzcz1cImZhIGZhLWNvZGVcIj48L2k+IHNvdXJjZSc7XG4gICAgICAgICAgICBodG1sQnV0dG9uLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgdGhpcywgZmFsc2UpO1xuXG4gICAgICAgICAgICB0aGlzLmZyYW1lQ292ZXIuYXBwZW5kQ2hpbGQoZGVsQnV0dG9uKTtcbiAgICAgICAgICAgIHRoaXMuZnJhbWVDb3Zlci5hcHBlbmRDaGlsZChyZXNldEJ1dHRvbik7XG4gICAgICAgICAgICB0aGlzLmZyYW1lQ292ZXIuYXBwZW5kQ2hpbGQoaHRtbEJ1dHRvbik7XG5cbiAgICAgICAgICAgIHRoaXMucGFyZW50TEkuYXBwZW5kQ2hpbGQodGhpcy5mcmFtZUNvdmVyKTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBhdXRvbWF0aWNhbGx5IGNvcnJlY3RzIHRoZSBoZWlnaHQgb2YgdGhlIGJsb2NrJ3MgaWZyYW1lIGRlcGVuZGluZyBvbiBpdHMgY29udGVudFxuICAgICAgICAqL1xuICAgICAgICB0aGlzLmhlaWdodEFkanVzdG1lbnQgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIHBhZ2VDb250YWluZXIgPSB0aGlzLmZyYW1lRG9jdW1lbnQuYm9keTtcbiAgICAgICAgICAgIHZhciBoZWlnaHQgPSBwYWdlQ29udGFpbmVyLnNjcm9sbEhlaWdodDtcblxuICAgICAgICAgICAgdGhpcy5mcmFtZS5zdHlsZS5oZWlnaHQgPSBoZWlnaHQrXCJweFwiO1xuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5zdHlsZS5oZWlnaHQgPSBoZWlnaHQrXCJweFwiO1xuICAgICAgICAgICAgdGhpcy5mcmFtZUNvdmVyLnN0eWxlLmhlaWdodCA9IGhlaWdodCtcInB4XCI7XG5cbiAgICAgICAgICAgIHRoaXMuZnJhbWVIZWlnaHQgPSBoZWlnaHQ7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgZGVsZXRlcyBhIGJsb2NrXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuZGVsZXRlID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIC8vcmVtb3ZlIGZyb20gRE9NL2NhbnZhcyB3aXRoIGEgbmljZSBhbmltYXRpb25cbiAgICAgICAgICAgICQodGhpcy5mcmFtZS5wYXJlbnROb2RlKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgIHRoaXMucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgICAgICBzaXRlLmFjdGl2ZVBhZ2UuaXNFbXB0eSgpO1xuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy9yZW1vdmUgZnJvbSBibG9ja3MgYXJyYXkgaW4gdGhlIGFjdGl2ZSBwYWdlXG4gICAgICAgICAgICBzaXRlLmFjdGl2ZVBhZ2UuZGVsZXRlQmxvY2sodGhpcyk7XG5cbiAgICAgICAgICAgIC8vc2FuYm94XG4gICAgICAgICAgICBpZiggdGhpcy5zYW5iZG94ICkge1xuICAgICAgICAgICAgICAgIGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCB0aGlzLnNhbmRib3ggKS5yZW1vdmUoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9lbGVtZW50IHdhcyBkZWxldGVkLCBzbyB3ZSd2ZSBnb3QgcGVuZGluZyBjaGFuZ2VcbiAgICAgICAgICAgIHNpdGUuc2V0UGVuZGluZ0NoYW5nZXModHJ1ZSk7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgcmVzZXRzIGEgYmxvY2sgdG8gaXQncyBvcmlnbmFsIHN0YXRlXG4gICAgICAgICovXG4gICAgICAgIHRoaXMucmVzZXQgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9yZXNldCBmcmFtZSBieSByZWxvYWRpbmcgaXRcbiAgICAgICAgICAgIHRoaXMuZnJhbWUuY29udGVudFdpbmRvdy5sb2NhdGlvbi5yZWxvYWQoKTtcblxuICAgICAgICAgICAgLy9zYW5kYm94P1xuICAgICAgICAgICAgaWYoIHRoaXMuc2FuZGJveCApIHtcbiAgICAgICAgICAgICAgICB2YXIgc2FuZGJveEZyYW1lID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQodGhpcy5zYW5kYm94KS5jb250ZW50V2luZG93LmxvY2F0aW9uLnJlbG9hZCgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL2VsZW1lbnQgd2FzIGRlbGV0ZWQsIHNvIHdlJ3ZlIGdvdCBwZW5kaW5nIGNoYW5nZXNcbiAgICAgICAgICAgIHNpdGUuc2V0UGVuZGluZ0NoYW5nZXModHJ1ZSk7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgbGF1bmNoZXMgdGhlIHNvdXJjZSBjb2RlIGVkaXRvclxuICAgICAgICAqL1xuICAgICAgICB0aGlzLnNvdXJjZSA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2hpZGUgdGhlIGlmcmFtZVxuICAgICAgICAgICAgdGhpcy5mcmFtZS5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuXG4gICAgICAgICAgICAvL2Rpc2FibGUgc29ydGFibGUgb24gdGhlIHBhcmVudExJXG4gICAgICAgICAgICAkKHRoaXMucGFyZW50TEkucGFyZW50Tm9kZSkuc29ydGFibGUoJ2Rpc2FibGUnKTtcblxuICAgICAgICAgICAgLy9idWlsdCBlZGl0b3IgZWxlbWVudFxuICAgICAgICAgICAgdmFyIHRoZUVkaXRvciA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ0RJVicpO1xuICAgICAgICAgICAgdGhlRWRpdG9yLmNsYXNzTGlzdC5hZGQoJ2FjZUVkaXRvcicpO1xuICAgICAgICAgICAgJCh0aGVFZGl0b3IpLnVuaXF1ZUlkKCk7XG5cbiAgICAgICAgICAgIHRoaXMucGFyZW50TEkuYXBwZW5kQ2hpbGQodGhlRWRpdG9yKTtcblxuICAgICAgICAgICAgLy9idWlsZCBhbmQgYXBwZW5kIGVycm9yIGRyYXdlclxuICAgICAgICAgICAgdmFyIG5ld0xJID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnTEknKTtcbiAgICAgICAgICAgIHZhciBlcnJvckRyYXdlciA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ0RJVicpO1xuICAgICAgICAgICAgZXJyb3JEcmF3ZXIuY2xhc3NMaXN0LmFkZCgnZXJyb3JEcmF3ZXInKTtcbiAgICAgICAgICAgIGVycm9yRHJhd2VyLnNldEF0dHJpYnV0ZSgnaWQnLCAnZGl2X2Vycm9yRHJhd2VyJyk7XG4gICAgICAgICAgICBlcnJvckRyYXdlci5pbm5lckhUTUwgPSAnPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJidG4gYnRuLXhzIGJ0bi1lbWJvc3NlZCBidG4tZGVmYXVsdCBidXR0b25fY2xlYXJFcnJvckRyYXdlclwiIGlkPVwiYnV0dG9uX2NsZWFyRXJyb3JEcmF3ZXJcIj5DTEVBUjwvYnV0dG9uPic7XG4gICAgICAgICAgICBuZXdMSS5hcHBlbmRDaGlsZChlcnJvckRyYXdlcik7XG4gICAgICAgICAgICBlcnJvckRyYXdlci5xdWVyeVNlbGVjdG9yKCdidXR0b24nKS5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIHRoaXMsIGZhbHNlKTtcbiAgICAgICAgICAgIHRoaXMucGFyZW50TEkucGFyZW50Tm9kZS5pbnNlcnRCZWZvcmUobmV3TEksIHRoaXMucGFyZW50TEkubmV4dFNpYmxpbmcpO1xuXG5cbiAgICAgICAgICAgIHZhciB0aGVJZCA9IHRoZUVkaXRvci5nZXRBdHRyaWJ1dGUoJ2lkJyk7XG4gICAgICAgICAgICB2YXIgZWRpdG9yID0gYWNlLmVkaXQoIHRoZUlkICk7XG5cbiAgICAgICAgICAgIHZhciBwYWdlQ29udGFpbmVyID0gdGhpcy5mcmFtZURvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoIGJDb25maWcucGFnZUNvbnRhaW5lciApO1xuICAgICAgICAgICAgdmFyIHRoZUhUTUwgPSBwYWdlQ29udGFpbmVyLmlubmVySFRNTDtcblxuICAgICAgICAgICAgZWRpdG9yLnNldFZhbHVlKCB0aGVIVE1MICk7XG4gICAgICAgICAgICBlZGl0b3Iuc2V0VGhlbWUoXCJhY2UvdGhlbWUvdHdpbGlnaHRcIik7XG4gICAgICAgICAgICBlZGl0b3IuZ2V0U2Vzc2lvbigpLnNldE1vZGUoXCJhY2UvbW9kZS9odG1sXCIpO1xuXG4gICAgICAgICAgICB2YXIgYmxvY2sgPSB0aGlzO1xuXG5cbiAgICAgICAgICAgIGVkaXRvci5nZXRTZXNzaW9uKCkub24oXCJjaGFuZ2VBbm5vdGF0aW9uXCIsIGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICBibG9jay5hbm5vdCA9IGVkaXRvci5nZXRTZXNzaW9uKCkuZ2V0QW5ub3RhdGlvbnMoKTtcblxuICAgICAgICAgICAgICAgIGNsZWFyVGltZW91dChibG9jay5hbm5vdFRpbWVvdXQpO1xuXG4gICAgICAgICAgICAgICAgdmFyIHRpbWVvdXRDb3VudDtcblxuICAgICAgICAgICAgICAgIGlmKCAkKCcjZGl2X2Vycm9yRHJhd2VyIHAnKS5zaXplKCkgPT09IDAgKSB7XG4gICAgICAgICAgICAgICAgICAgIHRpbWVvdXRDb3VudCA9IGJDb25maWcuc291cmNlQ29kZUVkaXRTeW50YXhEZWxheTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICB0aW1lb3V0Q291bnQgPSAxMDA7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgYmxvY2suYW5ub3RUaW1lb3V0ID0gc2V0VGltZW91dChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgIGZvciAodmFyIGtleSBpbiBibG9jay5hbm5vdCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChibG9jay5hbm5vdC5oYXNPd25Qcm9wZXJ0eShrZXkpKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiggYmxvY2suYW5ub3Rba2V5XS50ZXh0ICE9PSBcIlN0YXJ0IHRhZyBzZWVuIHdpdGhvdXQgc2VlaW5nIGEgZG9jdHlwZSBmaXJzdC4gRXhwZWN0ZWQgZS5nLiA8IURPQ1RZUEUgaHRtbD4uXCIgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIG5ld0xpbmUgPSAkKCc8cD48L3A+Jyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBuZXdLZXkgPSAkKCc8Yj4nK2Jsb2NrLmFubm90W2tleV0udHlwZSsnOiA8L2I+Jyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBuZXdJbmZvID0gJCgnPHNwYW4+ICcrYmxvY2suYW5ub3Rba2V5XS50ZXh0ICsgXCJvbiBsaW5lIFwiICsgXCIgPGI+XCIgKyBibG9jay5hbm5vdFtrZXldLnJvdysnPC9iPjwvc3Bhbj4nKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbmV3TGluZS5hcHBlbmQoIG5ld0tleSApO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBuZXdMaW5lLmFwcGVuZCggbmV3SW5mbyApO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoJyNkaXZfZXJyb3JEcmF3ZXInKS5hcHBlbmQoIG5ld0xpbmUgKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICBpZiggJCgnI2Rpdl9lcnJvckRyYXdlcicpLmNzcygnZGlzcGxheScpID09PSAnbm9uZScgJiYgJCgnI2Rpdl9lcnJvckRyYXdlcicpLmZpbmQoJ3AnKS5zaXplKCkgPiAwICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2Rpdl9lcnJvckRyYXdlcicpLnNsaWRlRG93bigpO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9LCB0aW1lb3V0Q291bnQpO1xuXG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvL2J1dHRvbnNcbiAgICAgICAgICAgIHZhciBjYW5jZWxCdXR0b24gPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdCVVRUT04nKTtcbiAgICAgICAgICAgIGNhbmNlbEJ1dHRvbi5zZXRBdHRyaWJ1dGUoJ3R5cGUnLCAnYnV0dG9uJyk7XG4gICAgICAgICAgICBjYW5jZWxCdXR0b24uY2xhc3NMaXN0LmFkZCgnYnRuJyk7XG4gICAgICAgICAgICBjYW5jZWxCdXR0b24uY2xhc3NMaXN0LmFkZCgnYnRuLWRhbmdlcicpO1xuICAgICAgICAgICAgY2FuY2VsQnV0dG9uLmNsYXNzTGlzdC5hZGQoJ2VkaXRDYW5jZWxCdXR0b24nKTtcbiAgICAgICAgICAgIGNhbmNlbEJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdidG4td2lkZScpO1xuICAgICAgICAgICAgY2FuY2VsQnV0dG9uLmlubmVySFRNTCA9ICc8c3BhbiBjbGFzcz1cImZ1aS1jcm9zc1wiPjwvc3Bhbj4gQ2FuY2VsJztcbiAgICAgICAgICAgIGNhbmNlbEJ1dHRvbi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIHRoaXMsIGZhbHNlKTtcblxuICAgICAgICAgICAgdmFyIHNhdmVCdXR0b24gPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdCVVRUT04nKTtcbiAgICAgICAgICAgIHNhdmVCdXR0b24uc2V0QXR0cmlidXRlKCd0eXBlJywgJ2J1dHRvbicpO1xuICAgICAgICAgICAgc2F2ZUJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdidG4nKTtcbiAgICAgICAgICAgIHNhdmVCdXR0b24uY2xhc3NMaXN0LmFkZCgnYnRuLXByaW1hcnknKTtcbiAgICAgICAgICAgIHNhdmVCdXR0b24uY2xhc3NMaXN0LmFkZCgnZWRpdFNhdmVCdXR0b24nKTtcbiAgICAgICAgICAgIHNhdmVCdXR0b24uY2xhc3NMaXN0LmFkZCgnYnRuLXdpZGUnKTtcbiAgICAgICAgICAgIHNhdmVCdXR0b24uaW5uZXJIVE1MID0gJzxzcGFuIGNsYXNzPVwiZnVpLWNoZWNrXCI+PC9zcGFuPiBTYXZlJztcbiAgICAgICAgICAgIHNhdmVCdXR0b24uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCB0aGlzLCBmYWxzZSk7XG5cbiAgICAgICAgICAgIHZhciBidXR0b25XcmFwcGVyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnRElWJyk7XG4gICAgICAgICAgICBidXR0b25XcmFwcGVyLmNsYXNzTGlzdC5hZGQoJ2VkaXRvckJ1dHRvbnMnKTtcblxuICAgICAgICAgICAgYnV0dG9uV3JhcHBlci5hcHBlbmRDaGlsZCggY2FuY2VsQnV0dG9uICk7XG4gICAgICAgICAgICBidXR0b25XcmFwcGVyLmFwcGVuZENoaWxkKCBzYXZlQnV0dG9uICk7XG5cbiAgICAgICAgICAgIHRoaXMucGFyZW50TEkuYXBwZW5kQ2hpbGQoIGJ1dHRvbldyYXBwZXIgKTtcblxuICAgICAgICAgICAgYnVpbGRlclVJLmFjZUVkaXRvcnNbIHRoZUlkIF0gPSBlZGl0b3I7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgY2FuY2VscyB0aGUgYmxvY2sgc291cmNlIGNvZGUgZWRpdG9yXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuY2FuY2VsU291cmNlQmxvY2sgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9lbmFibGUgZHJhZ2dhYmxlIG9uIHRoZSBMSVxuICAgICAgICAgICAgJCh0aGlzLnBhcmVudExJLnBhcmVudE5vZGUpLnNvcnRhYmxlKCdlbmFibGUnKTtcblxuICAgICAgICAgICAgLy9kZWxldGUgdGhlIGVycm9yRHJhd2VyXG4gICAgICAgICAgICAkKHRoaXMucGFyZW50TEkubmV4dFNpYmxpbmcpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAvL2RlbGV0ZSB0aGUgZWRpdG9yXG4gICAgICAgICAgICB0aGlzLnBhcmVudExJLnF1ZXJ5U2VsZWN0b3IoJy5hY2VFZGl0b3InKS5yZW1vdmUoKTtcbiAgICAgICAgICAgICQodGhpcy5mcmFtZSkuZmFkZUluKDUwMCk7XG5cbiAgICAgICAgICAgICQodGhpcy5wYXJlbnRMSS5xdWVyeVNlbGVjdG9yKCcuZWRpdG9yQnV0dG9ucycpKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgdXBkYXRlcyB0aGUgYmxvY2tzIHNvdXJjZSBjb2RlXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuc2F2ZVNvdXJjZUJsb2NrID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIC8vZW5hYmxlIGRyYWdnYWJsZSBvbiB0aGUgTElcbiAgICAgICAgICAgICQodGhpcy5wYXJlbnRMSS5wYXJlbnROb2RlKS5zb3J0YWJsZSgnZW5hYmxlJyk7XG5cbiAgICAgICAgICAgIHZhciB0aGVJZCA9IHRoaXMucGFyZW50TEkucXVlcnlTZWxlY3RvcignLmFjZUVkaXRvcicpLmdldEF0dHJpYnV0ZSgnaWQnKTtcbiAgICAgICAgICAgIHZhciB0aGVDb250ZW50ID0gYnVpbGRlclVJLmFjZUVkaXRvcnNbdGhlSWRdLmdldFZhbHVlKCk7XG5cbiAgICAgICAgICAgIC8vZGVsZXRlIHRoZSBlcnJvckRyYXdlclxuICAgICAgICAgICAgZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2Rpdl9lcnJvckRyYXdlcicpLnBhcmVudE5vZGUucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgIC8vZGVsZXRlIHRoZSBlZGl0b3JcbiAgICAgICAgICAgIHRoaXMucGFyZW50TEkucXVlcnlTZWxlY3RvcignLmFjZUVkaXRvcicpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAvL3VwZGF0ZSB0aGUgZnJhbWUncyBjb250ZW50XG4gICAgICAgICAgICB0aGlzLmZyYW1lRG9jdW1lbnQucXVlcnlTZWxlY3RvciggYkNvbmZpZy5wYWdlQ29udGFpbmVyICkuaW5uZXJIVE1MID0gdGhlQ29udGVudDtcbiAgICAgICAgICAgIHRoaXMuZnJhbWUuc3R5bGUuZGlzcGxheSA9ICdibG9jayc7XG5cbiAgICAgICAgICAgIC8vc2FuZGJveGVkP1xuICAgICAgICAgICAgaWYoIHRoaXMuc2FuZGJveCApIHtcblxuICAgICAgICAgICAgICAgIHZhciBzYW5kYm94RnJhbWUgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggdGhpcy5zYW5kYm94ICk7XG4gICAgICAgICAgICAgICAgdmFyIHNhbmRib3hGcmFtZURvY3VtZW50ID0gc2FuZGJveEZyYW1lLmNvbnRlbnREb2N1bWVudCB8fCBzYW5kYm94RnJhbWUuY29udGVudFdpbmRvdy5kb2N1bWVudDtcblxuICAgICAgICAgICAgICAgIGJ1aWxkZXJVSS50ZW1wRnJhbWUgPSBzYW5kYm94RnJhbWU7XG5cbiAgICAgICAgICAgICAgICBzYW5kYm94RnJhbWVEb2N1bWVudC5xdWVyeVNlbGVjdG9yKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKS5pbm5lckhUTUwgPSB0aGVDb250ZW50O1xuXG4gICAgICAgICAgICAgICAgLy9kbyB3ZSBuZWVkIHRvIGV4ZWN1dGUgYSBsb2FkZXIgZnVuY3Rpb24/XG4gICAgICAgICAgICAgICAgaWYoIHRoaXMuc2FuZGJveF9sb2FkZXIgIT09ICcnICkge1xuXG4gICAgICAgICAgICAgICAgICAgIC8qXG4gICAgICAgICAgICAgICAgICAgIHZhciBjb2RlVG9FeGVjdXRlID0gXCJzYW5kYm94RnJhbWUuY29udGVudFdpbmRvdy5cIit0aGlzLnNhbmRib3hfbG9hZGVyK1wiKClcIjtcbiAgICAgICAgICAgICAgICAgICAgdmFyIHRtcEZ1bmMgPSBuZXcgRnVuY3Rpb24oY29kZVRvRXhlY3V0ZSk7XG4gICAgICAgICAgICAgICAgICAgIHRtcEZ1bmMoKTtcbiAgICAgICAgICAgICAgICAgICAgKi9cblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAkKHRoaXMucGFyZW50TEkucXVlcnlTZWxlY3RvcignLmVkaXRvckJ1dHRvbnMnKSkuZmFkZU91dCg1MDAsIGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmUoKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvL2FkanVzdCBoZWlnaHQgb2YgdGhlIGZyYW1lXG4gICAgICAgICAgICB0aGlzLmhlaWdodEFkanVzdG1lbnQoKTtcblxuICAgICAgICAgICAgLy9uZXcgcGFnZSBhZGRlZCwgd2UndmUgZ290IHBlbmRpbmcgY2hhbmdlc1xuICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICAgICAgLy9ibG9jayBoYXMgY2hhbmdlZFxuICAgICAgICAgICAgdGhpcy5zdGF0dXMgPSAnY2hhbmdlZCc7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgY2xlYXJzIG91dCB0aGUgZXJyb3IgZHJhd2VyXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuY2xlYXJFcnJvckRyYXdlciA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB2YXIgcHMgPSB0aGlzLnBhcmVudExJLm5leHRTaWJsaW5nLnF1ZXJ5U2VsZWN0b3JBbGwoJ3AnKTtcblxuICAgICAgICAgICAgZm9yKCB2YXIgaSA9IDA7IGkgPCBwcy5sZW5ndGg7IGkrKyApIHtcbiAgICAgICAgICAgICAgICBwc1tpXS5yZW1vdmUoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICB0b2dnbGVzIHRoZSB2aXNpYmlsaXR5IG9mIHRoaXMgYmxvY2sncyBmcmFtZUNvdmVyXG4gICAgICAgICovXG4gICAgICAgIHRoaXMudG9nZ2xlQ292ZXIgPSBmdW5jdGlvbihvbk9yT2ZmKSB7XG5cbiAgICAgICAgICAgIGlmKCBvbk9yT2ZmID09PSAnT24nICkge1xuXG4gICAgICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5xdWVyeVNlbGVjdG9yKCcuZnJhbWVDb3ZlcicpLnN0eWxlLmRpc3BsYXkgPSAnYmxvY2snO1xuXG4gICAgICAgICAgICB9IGVsc2UgaWYoIG9uT3JPZmYgPT09ICdPZmYnICkge1xuXG4gICAgICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5xdWVyeVNlbGVjdG9yKCcuZnJhbWVDb3ZlcicpLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICByZXR1cm5zIHRoZSBmdWxsIHNvdXJjZSBjb2RlIG9mIHRoZSBibG9jaydzIGZyYW1lXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuZ2V0U291cmNlID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciBzb3VyY2UgPSBcIjxodG1sPlwiO1xuICAgICAgICAgICAgc291cmNlICs9IHRoaXMuZnJhbWVEb2N1bWVudC5oZWFkLm91dGVySFRNTDtcbiAgICAgICAgICAgIHNvdXJjZSArPSB0aGlzLmZyYW1lRG9jdW1lbnQuYm9keS5vdXRlckhUTUw7XG5cbiAgICAgICAgICAgIHJldHVybiBzb3VyY2U7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgcGxhY2VzIGEgZHJhZ2dlZC9kcm9wcGVkIGJsb2NrIGZyb20gdGhlIGxlZnQgc2lkZWJhciBvbnRvIHRoZSBjYW52YXNcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5wbGFjZU9uQ2FudmFzID0gZnVuY3Rpb24odWkpIHtcblxuICAgICAgICAgICAgLy9mcmFtZSBkYXRhLCB3ZSdsbCBuZWVkIHRoaXMgYmVmb3JlIG1lc3Npbmcgd2l0aCB0aGUgaXRlbSdzIGNvbnRlbnQgSFRNTFxuICAgICAgICAgICAgdmFyIGZyYW1lRGF0YSA9IHt9LCBhdHRyO1xuXG4gICAgICAgICAgICBpZiggdWkuaXRlbS5maW5kKCdpZnJhbWUnKS5zaXplKCkgPiAwICkgey8vaWZyYW1lIHRodW1ibmFpbFxuXG4gICAgICAgICAgICAgICAgZnJhbWVEYXRhLnNyYyA9IHVpLml0ZW0uZmluZCgnaWZyYW1lJykuYXR0cignc3JjJyk7XG4gICAgICAgICAgICAgICAgZnJhbWVEYXRhLmZyYW1lc19vcmlnaW5hbF91cmwgPSB1aS5pdGVtLmZpbmQoJ2lmcmFtZScpLmF0dHIoJ3NyYycpO1xuICAgICAgICAgICAgICAgIGZyYW1lRGF0YS5mcmFtZXNfaGVpZ2h0ID0gdWkuaXRlbS5oZWlnaHQoKTtcblxuICAgICAgICAgICAgICAgIC8vc2FuZGJveGVkIGJsb2NrP1xuICAgICAgICAgICAgICAgIGF0dHIgPSB1aS5pdGVtLmZpbmQoJ2lmcmFtZScpLmF0dHIoJ3NhbmRib3gnKTtcblxuICAgICAgICAgICAgICAgIGlmICh0eXBlb2YgYXR0ciAhPT0gdHlwZW9mIHVuZGVmaW5lZCAmJiBhdHRyICE9PSBmYWxzZSkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNhbmRib3ggPSBzaXRlQnVpbGRlclV0aWxzLmdldFJhbmRvbUFyYml0cmFyeSgxMDAwMCwgMTAwMDAwMDAwMCk7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2FuZGJveF9sb2FkZXIgPSB1aS5pdGVtLmZpbmQoJ2lmcmFtZScpLmF0dHIoJ2RhdGEtbG9hZGVyZnVuY3Rpb24nKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0gZWxzZSB7Ly9pbWFnZSB0aHVtYm5haWxcblxuICAgICAgICAgICAgICAgIGZyYW1lRGF0YS5zcmMgPSB1aS5pdGVtLmZpbmQoJ2ltZycpLmF0dHIoJ2RhdGEtc3JjYycpO1xuICAgICAgICAgICAgICAgIGZyYW1lRGF0YS5mcmFtZXNfb3JpZ2luYWxfdXJsID0gdWkuaXRlbS5maW5kKCdpbWcnKS5hdHRyKCdkYXRhLXNyY2MnKTtcbiAgICAgICAgICAgICAgICBmcmFtZURhdGEuZnJhbWVzX2hlaWdodCA9IHVpLml0ZW0uZmluZCgnaW1nJykuYXR0cignZGF0YS1oZWlnaHQnKTtcblxuICAgICAgICAgICAgICAgIC8vc2FuZGJveGVkIGJsb2NrP1xuICAgICAgICAgICAgICAgIGF0dHIgPSB1aS5pdGVtLmZpbmQoJ2ltZycpLmF0dHIoJ2RhdGEtc2FuZGJveCcpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHR5cGVvZiBhdHRyICE9PSB0eXBlb2YgdW5kZWZpbmVkICYmIGF0dHIgIT09IGZhbHNlKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2FuZGJveCA9IHNpdGVCdWlsZGVyVXRpbHMuZ2V0UmFuZG9tQXJiaXRyYXJ5KDEwMDAwLCAxMDAwMDAwMDAwKTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zYW5kYm94X2xvYWRlciA9IHVpLml0ZW0uZmluZCgnaW1nJykuYXR0cignZGF0YS1sb2FkZXJmdW5jdGlvbicpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL2NyZWF0ZSB0aGUgbmV3IGJsb2NrIG9iamVjdFxuICAgICAgICAgICAgdGhpcy5mcmFtZUlEID0gMDtcbiAgICAgICAgICAgIHRoaXMucGFyZW50TEkgPSB1aS5pdGVtLmdldCgwKTtcbiAgICAgICAgICAgIHRoaXMucGFyZW50TEkuaW5uZXJIVE1MID0gJyc7XG4gICAgICAgICAgICB0aGlzLnN0YXR1cyA9ICduZXcnO1xuICAgICAgICAgICAgdGhpcy5jcmVhdGVGcmFtZShmcmFtZURhdGEpO1xuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5zdHlsZS5oZWlnaHQgPSB0aGlzLmZyYW1lSGVpZ2h0K1wicHhcIjtcbiAgICAgICAgICAgIHRoaXMuY3JlYXRlRnJhbWVDb3ZlcigpO1xuXG4gICAgICAgICAgICB0aGlzLmZyYW1lLmFkZEV2ZW50TGlzdGVuZXIoJ2xvYWQnLCB0aGlzKTtcblxuICAgICAgICAgICAgLy9pbnNlcnQgdGhlIGNyZWF0ZWQgaWZyYW1lXG4gICAgICAgICAgICB1aS5pdGVtLmFwcGVuZCgkKHRoaXMuZnJhbWUpKTtcblxuICAgICAgICAgICAgLy9hZGQgdGhlIGJsb2NrIHRvIHRoZSBjdXJyZW50IHBhZ2VcbiAgICAgICAgICAgIHNpdGUuYWN0aXZlUGFnZS5ibG9ja3Muc3BsaWNlKHVpLml0ZW0uaW5kZXgoKSwgMCwgdGhpcyk7XG5cbiAgICAgICAgICAgIC8vY3VzdG9tIGV2ZW50XG4gICAgICAgICAgICB1aS5pdGVtLmZpbmQoJ2lmcmFtZScpLnRyaWdnZXIoJ2NhbnZhc3VwZGF0ZWQnKTtcblxuICAgICAgICAgICAgLy9kcm9wcGVkIGVsZW1lbnQsIHNvIHdlJ3ZlIGdvdCBwZW5kaW5nIGNoYW5nZXNcbiAgICAgICAgICAgIHNpdGUuc2V0UGVuZGluZ0NoYW5nZXModHJ1ZSk7XG5cbiAgICAgICAgfTtcblxuXG4gICAgfVxuXG4gICAgQmxvY2sucHJvdG90eXBlLmhhbmRsZUV2ZW50ID0gZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgc3dpdGNoIChldmVudC50eXBlKSB7XG4gICAgICAgICAgICBjYXNlIFwibG9hZFwiOlxuICAgICAgICAgICAgICAgIHRoaXMuc2V0RnJhbWVEb2N1bWVudCgpO1xuICAgICAgICAgICAgICAgIHRoaXMuaGVpZ2h0QWRqdXN0bWVudCgpO1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzLmZyYW1lQ292ZXIpLnJlbW92ZUNsYXNzKCdmcmVzaCcsIDUwMCk7XG5cbiAgICAgICAgICAgICAgICBicmVhaztcblxuICAgICAgICAgICAgY2FzZSBcImNsaWNrXCI6XG5cbiAgICAgICAgICAgICAgICB2YXIgdGhlQmxvY2sgPSB0aGlzO1xuXG4gICAgICAgICAgICAgICAgLy9maWd1cmUgb3V0IHdoYXQgdG8gZG8gbmV4dFxuXG4gICAgICAgICAgICAgICAgaWYoIGV2ZW50LnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ2RlbGV0ZUJsb2NrJykgKSB7Ly9kZWxldGUgdGhpcyBibG9ja1xuXG4gICAgICAgICAgICAgICAgICAgICQoYnVpbGRlclVJLm1vZGFsRGVsZXRlQmxvY2spLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgJChidWlsZGVyVUkubW9kYWxEZWxldGVCbG9jaykub2ZmKCdjbGljaycsICcjZGVsZXRlQmxvY2tDb25maXJtJykub24oJ2NsaWNrJywgJyNkZWxldGVCbG9ja0NvbmZpcm0nLCBmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhlQmxvY2suZGVsZXRlKGV2ZW50KTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoYnVpbGRlclVJLm1vZGFsRGVsZXRlQmxvY2spLm1vZGFsKCdoaWRlJyk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmKCBldmVudC50YXJnZXQuY2xhc3NMaXN0LmNvbnRhaW5zKCdyZXNldEJsb2NrJykgKSB7Ly9yZXNldCB0aGUgYmxvY2tcblxuICAgICAgICAgICAgICAgICAgICAkKGJ1aWxkZXJVSS5tb2RhbFJlc2V0QmxvY2spLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgJChidWlsZGVyVUkubW9kYWxSZXNldEJsb2NrKS5vZmYoJ2NsaWNrJywgJyNyZXNldEJsb2NrQ29uZmlybScpLm9uKCdjbGljaycsICcjcmVzZXRCbG9ja0NvbmZpcm0nLCBmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhlQmxvY2sucmVzZXQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoYnVpbGRlclVJLm1vZGFsUmVzZXRCbG9jaykubW9kYWwoJ2hpZGUnKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIGV2ZW50LnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ2h0bWxCbG9jaycpICkgey8vc291cmNlIGNvZGUgZWRpdG9yXG5cbiAgICAgICAgICAgICAgICAgICAgdGhlQmxvY2suc291cmNlKCk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIGV2ZW50LnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ2VkaXRDYW5jZWxCdXR0b24nKSApIHsvL2NhbmNlbCBzb3VyY2UgY29kZSBlZGl0b3JcblxuICAgICAgICAgICAgICAgICAgICB0aGVCbG9jay5jYW5jZWxTb3VyY2VCbG9jaygpO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmKCBldmVudC50YXJnZXQuY2xhc3NMaXN0LmNvbnRhaW5zKCdlZGl0U2F2ZUJ1dHRvbicpICkgey8vc2F2ZSBzb3VyY2UgY29kZVxuXG4gICAgICAgICAgICAgICAgICAgIHRoZUJsb2NrLnNhdmVTb3VyY2VCbG9jaygpO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmKCBldmVudC50YXJnZXQuY2xhc3NMaXN0LmNvbnRhaW5zKCdidXR0b25fY2xlYXJFcnJvckRyYXdlcicpICkgey8vY2xlYXIgZXJyb3IgZHJhd2VyXG5cbiAgICAgICAgICAgICAgICAgICAgdGhlQmxvY2suY2xlYXJFcnJvckRyYXdlcigpO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cbiAgICB9O1xuXG5cbiAgICAvKlxuICAgICAgICBTaXRlIG9iamVjdCBsaXRlcmFsXG4gICAgKi9cbiAgICAvKmpzaGludCAtVzAwMyAqL1xuICAgIHZhciBzaXRlID0ge1xuXG4gICAgICAgIHBlbmRpbmdDaGFuZ2VzOiBmYWxzZSwgICAgICAvL3BlbmRpbmcgY2hhbmdlcyBvciBubz9cbiAgICAgICAgcGFnZXM6IHt9LCAgICAgICAgICAgICAgICAgIC8vYXJyYXkgY29udGFpbmluZyBhbGwgcGFnZXMsIGluY2x1ZGluZyB0aGUgY2hpbGQgZnJhbWVzLCBsb2FkZWQgZnJvbSB0aGUgc2VydmVyIG9uIHBhZ2UgbG9hZFxuICAgICAgICBpc19hZG1pbjogMCwgICAgICAgICAgICAgICAgLy8wIGZvciBub24tYWRtaW4sIDEgZm9yIGFkbWluXG4gICAgICAgIGRhdGE6IHt9LCAgICAgICAgICAgICAgICAgICAvL2NvbnRhaW5lciBmb3IgYWpheCBsb2FkZWQgc2l0ZSBkYXRhXG4gICAgICAgIHBhZ2VzVG9EZWxldGU6IFtdLCAgICAgICAgICAvL2NvbnRhaW5zIHBhZ2VzIHRvIGJlIGRlbGV0ZWRcblxuICAgICAgICBzaXRlUGFnZXM6IFtdLCAgICAgICAgICAgICAgLy90aGlzIGlzIHRoZSBvbmx5IHZhciBjb250YWluaW5nIHRoZSByZWNlbnQgY2FudmFzIGNvbnRlbnRzXG5cbiAgICAgICAgc2l0ZVBhZ2VzUmVhZHlGb3JTZXJ2ZXI6IHt9LCAgICAgLy9jb250YWlucyB0aGUgc2l0ZSBkYXRhIHJlYWR5IHRvIGJlIHNlbnQgdG8gdGhlIHNlcnZlclxuXG4gICAgICAgIGFjdGl2ZVBhZ2U6IHt9LCAgICAgICAgICAgICAvL2hvbGRzIGEgcmVmZXJlbmNlIHRvIHRoZSBwYWdlIGN1cnJlbnRseSBvcGVuIG9uIHRoZSBjYW52YXNcblxuICAgICAgICBwYWdlVGl0bGU6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwYWdlVGl0bGUnKSwvL2hvbGRzIHRoZSBwYWdlIHRpdGxlIG9mIHRoZSBjdXJyZW50IHBhZ2Ugb24gdGhlIGNhbnZhc1xuXG4gICAgICAgIGRpdkNhbnZhczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3BhZ2VMaXN0JyksLy9ESVYgY29udGFpbmluZyBhbGwgcGFnZXMgb24gdGhlIGNhbnZhc1xuXG4gICAgICAgIHBhZ2VzTWVudTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3BhZ2VzJyksIC8vVUwgY29udGFpbmluZyB0aGUgcGFnZXMgbWVudSBpbiB0aGUgc2lkZWJhclxuXG4gICAgICAgIGJ1dHRvbk5ld1BhZ2U6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdhZGRQYWdlJyksXG4gICAgICAgIGxpTmV3UGFnZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ25ld1BhZ2VMSScpLFxuXG4gICAgICAgIGlucHV0UGFnZVNldHRpbmdzVGl0bGU6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwYWdlRGF0YV90aXRsZScpLFxuICAgICAgICBpbnB1dFBhZ2VTZXR0aW5nc01ldGFEZXNjcmlwdGlvbjogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3BhZ2VEYXRhX21ldGFEZXNjcmlwdGlvbicpLFxuICAgICAgICBpbnB1dFBhZ2VTZXR0aW5nc01ldGFLZXl3b3JkczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3BhZ2VEYXRhX21ldGFLZXl3b3JkcycpLFxuICAgICAgICBpbnB1dFBhZ2VTZXR0aW5nc0luY2x1ZGVzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncGFnZURhdGFfaGVhZGVySW5jbHVkZXMnKSxcbiAgICAgICAgaW5wdXRQYWdlU2V0dGluZ3NQYWdlQ3NzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncGFnZURhdGFfaGVhZGVyQ3NzJyksXG5cbiAgICAgICAgYnV0dG9uU3VibWl0UGFnZVNldHRpbmdzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncGFnZVNldHRpbmdzU3VibWl0dEJ1dHRvbicpLFxuXG4gICAgICAgIG1vZGFsUGFnZVNldHRpbmdzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncGFnZVNldHRpbmdzTW9kYWwnKSxcblxuICAgICAgICBidXR0b25TYXZlOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2F2ZVBhZ2UnKSxcblxuICAgICAgICBtZXNzYWdlU3RhcnQ6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdzdGFydCcpLFxuICAgICAgICBkaXZGcmFtZVdyYXBwZXI6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdmcmFtZVdyYXBwZXInKSxcblxuICAgICAgICBza2VsZXRvbjogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NrZWxldG9uJyksXG5cblx0XHRhdXRvU2F2ZVRpbWVyOiB7fSxcblxuICAgICAgICBpbml0OiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJC5nZXRKU09OKGFwcFVJLnNpdGVVcmwrXCJzaXRlRGF0YVwiLCBmdW5jdGlvbihkYXRhKXtcblxuICAgICAgICAgICAgICAgIGlmKCBkYXRhLnNpdGUgIT09IHVuZGVmaW5lZCApIHtcbiAgICAgICAgICAgICAgICAgICAgc2l0ZS5kYXRhID0gZGF0YS5zaXRlO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBpZiggZGF0YS5wYWdlcyAhPT0gdW5kZWZpbmVkICkge1xuICAgICAgICAgICAgICAgICAgICBzaXRlLnBhZ2VzID0gZGF0YS5wYWdlcztcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBzaXRlLmlzX2FkbWluID0gZGF0YS5pc19hZG1pbjtcblxuXHRcdFx0XHRpZiggJCgnI3BhZ2VMaXN0Jykuc2l6ZSgpID4gMCApIHtcbiAgICAgICAgICAgICAgICBcdGJ1aWxkZXJVSS5wb3B1bGF0ZUNhbnZhcygpO1xuXHRcdFx0XHR9XG5cbiAgICAgICAgICAgICAgICAvL2ZpcmUgY3VzdG9tIGV2ZW50XG4gICAgICAgICAgICAgICAgJCgnYm9keScpLnRyaWdnZXIoJ3NpdGVEYXRhTG9hZGVkJyk7XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uTmV3UGFnZSkub24oJ2NsaWNrJywgc2l0ZS5uZXdQYWdlKTtcbiAgICAgICAgICAgICQodGhpcy5tb2RhbFBhZ2VTZXR0aW5ncykub24oJ3Nob3cuYnMubW9kYWwnLCBzaXRlLmxvYWRQYWdlU2V0dGluZ3MpO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblN1Ym1pdFBhZ2VTZXR0aW5ncykub24oJ2NsaWNrJywgc2l0ZS51cGRhdGVQYWdlU2V0dGluZ3MpO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblNhdmUpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCl7c2l0ZS5zYXZlKHRydWUpO30pO1xuXG4gICAgICAgICAgICAvL2F1dG8gc2F2ZSB0aW1lXG4gICAgICAgICAgICB0aGlzLmF1dG9TYXZlVGltZXIgPSBzZXRUaW1lb3V0KHNpdGUuYXV0b1NhdmUsIGJDb25maWcuYXV0b1NhdmVUaW1lb3V0KTtcblxuICAgICAgICB9LFxuXG4gICAgICAgIGF1dG9TYXZlOiBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICBpZihzaXRlLnBlbmRpbmdDaGFuZ2VzKSB7XG4gICAgICAgICAgICAgICAgc2l0ZS5zYXZlKGZhbHNlKTtcbiAgICAgICAgICAgIH1cblxuXHRcdFx0d2luZG93LmNsZWFySW50ZXJ2YWwodGhpcy5hdXRvU2F2ZVRpbWVyKTtcbiAgICAgICAgICAgIHRoaXMuYXV0b1NhdmVUaW1lciA9IHNldFRpbWVvdXQoc2l0ZS5hdXRvU2F2ZSwgYkNvbmZpZy5hdXRvU2F2ZVRpbWVvdXQpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgc2V0UGVuZGluZ0NoYW5nZXM6IGZ1bmN0aW9uKHZhbHVlKSB7XG5cbiAgICAgICAgICAgIHRoaXMucGVuZGluZ0NoYW5nZXMgPSB2YWx1ZTtcblxuICAgICAgICAgICAgaWYoIHZhbHVlID09PSB0cnVlICkge1xuXG5cdFx0XHRcdC8vcmVzZXQgdGltZXJcblx0XHRcdFx0d2luZG93LmNsZWFySW50ZXJ2YWwodGhpcy5hdXRvU2F2ZVRpbWVyKTtcbiAgICAgICAgICAgIFx0dGhpcy5hdXRvU2F2ZVRpbWVyID0gc2V0VGltZW91dChzaXRlLmF1dG9TYXZlLCBiQ29uZmlnLmF1dG9TYXZlVGltZW91dCk7XG5cbiAgICAgICAgICAgICAgICAkKCcjc2F2ZVBhZ2UgLmJMYWJlbCcpLnRleHQoXCJTYXZlIG5vdyAoISlcIik7XG5cbiAgICAgICAgICAgICAgICBpZiggc2l0ZS5hY3RpdmVQYWdlLnN0YXR1cyAhPT0gJ25ldycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgc2l0ZS5hY3RpdmVQYWdlLnN0YXR1cyA9ICdjaGFuZ2VkJztcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICQoJyNzYXZlUGFnZSAuYkxhYmVsJykudGV4dChcIk5vdGhpbmcgdG8gc2F2ZVwiKTtcblxuICAgICAgICAgICAgICAgIHNpdGUudXBkYXRlUGFnZVN0YXR1cygnJyk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG4gICAgICAgIHNhdmU6IGZ1bmN0aW9uKHNob3dDb25maXJtTW9kYWwpIHtcblxuICAgICAgICAgICAgLy9maXJlIGN1c3RvbSBldmVudFxuICAgICAgICAgICAgJCgnYm9keScpLnRyaWdnZXIoJ2JlZm9yZVNhdmUnKTtcblxuICAgICAgICAgICAgLy9kaXNhYmxlIGJ1dHRvblxuICAgICAgICAgICAgJChcImEjc2F2ZVBhZ2VcIikuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgICAgIC8vcmVtb3ZlIG9sZCBhbGVydHNcbiAgICAgICAgICAgICQoJyNlcnJvck1vZGFsIC5tb2RhbC1ib2R5ID4gKiwgI3N1Y2Nlc3NNb2RhbCAubW9kYWwtYm9keSA+IConKS5lYWNoKGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmUoKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBzaXRlLnByZXBGb3JTYXZlKGZhbHNlKTtcblxuICAgICAgICAgICAgdmFyIHNlcnZlckRhdGEgPSB7fTtcbiAgICAgICAgICAgIHNlcnZlckRhdGEucGFnZXMgPSB0aGlzLnNpdGVQYWdlc1JlYWR5Rm9yU2VydmVyO1xuICAgICAgICAgICAgaWYoIHRoaXMucGFnZXNUb0RlbGV0ZS5sZW5ndGggPiAwICkge1xuICAgICAgICAgICAgICAgIHNlcnZlckRhdGEudG9EZWxldGUgPSB0aGlzLnBhZ2VzVG9EZWxldGU7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBzZXJ2ZXJEYXRhLnNpdGVEYXRhID0gdGhpcy5kYXRhO1xuXG4gICAgICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgICAgIHVybDogYXBwVUkuc2l0ZVVybCtcInNpdGUvc2F2ZVwiLFxuICAgICAgICAgICAgICAgIHR5cGU6IFwiUE9TVFwiLFxuICAgICAgICAgICAgICAgIGRhdGFUeXBlOiBcImpzb25cIixcbiAgICAgICAgICAgICAgICBkYXRhOiBzZXJ2ZXJEYXRhLFxuICAgICAgICAgICAgfSkuZG9uZShmdW5jdGlvbihyZXMpe1xuXG4gICAgICAgICAgICAgICAgLy9lbmFibGUgYnV0dG9uXG4gICAgICAgICAgICAgICAgJChcImEjc2F2ZVBhZ2VcIikucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgICAgICAgICBpZiggcmVzLnJlc3BvbnNlQ29kZSA9PT0gMCApIHtcblxuICAgICAgICAgICAgICAgICAgICBpZiggc2hvd0NvbmZpcm1Nb2RhbCApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2Vycm9yTW9kYWwgLm1vZGFsLWJvZHknKS5hcHBlbmQoICQocmVzLnJlc3BvbnNlSFRNTCkgKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyNlcnJvck1vZGFsJykubW9kYWwoJ3Nob3cnKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIHJlcy5yZXNwb25zZUNvZGUgPT09IDEgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoIHNob3dDb25maXJtTW9kYWwgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyNzdWNjZXNzTW9kYWwgLm1vZGFsLWJvZHknKS5hcHBlbmQoICQocmVzLnJlc3BvbnNlSFRNTCkgKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyNzdWNjZXNzTW9kYWwnKS5tb2RhbCgnc2hvdycpO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuXG4gICAgICAgICAgICAgICAgICAgIC8vbm8gbW9yZSBwZW5kaW5nIGNoYW5nZXNcbiAgICAgICAgICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyhmYWxzZSk7XG5cblxuICAgICAgICAgICAgICAgICAgICAvL3VwZGF0ZSByZXZpc2lvbnM/XG4gICAgICAgICAgICAgICAgICAgICQoJ2JvZHknKS50cmlnZ2VyKCdjaGFuZ2VQYWdlJyk7XG5cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9LFxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBwcmVwcyB0aGUgc2l0ZSBkYXRhIGJlZm9yZSBzZW5kaW5nIGl0IHRvIHRoZSBzZXJ2ZXJcbiAgICAgICAgKi9cbiAgICAgICAgcHJlcEZvclNhdmU6IGZ1bmN0aW9uKHRlbXBsYXRlKSB7XG5cbiAgICAgICAgICAgIHRoaXMuc2l0ZVBhZ2VzUmVhZHlGb3JTZXJ2ZXIgPSB7fTtcblxuICAgICAgICAgICAgaWYoIHRlbXBsYXRlICkgey8vc2F2aW5nIHRlbXBsYXRlLCBvbmx5IHRoZSBhY3RpdmVQYWdlIGlzIG5lZWRlZFxuXG4gICAgICAgICAgICAgICAgdGhpcy5zaXRlUGFnZXNSZWFkeUZvclNlcnZlclt0aGlzLmFjdGl2ZVBhZ2UubmFtZV0gPSB0aGlzLmFjdGl2ZVBhZ2UucHJlcEZvclNhdmUoKTtcblxuICAgICAgICAgICAgICAgIHRoaXMuYWN0aXZlUGFnZS5mdWxsUGFnZSgpO1xuXG4gICAgICAgICAgICB9IGVsc2Ugey8vcmVndWxhciBzYXZlXG5cbiAgICAgICAgICAgICAgICAvL2ZpbmQgdGhlIHBhZ2VzIHdoaWNoIG5lZWQgdG8gYmUgc2VuZCB0byB0aGUgc2VydmVyXG4gICAgICAgICAgICAgICAgZm9yKCB2YXIgaSA9IDA7IGkgPCB0aGlzLnNpdGVQYWdlcy5sZW5ndGg7IGkrKyApIHtcblxuICAgICAgICAgICAgICAgICAgICBpZiggdGhpcy5zaXRlUGFnZXNbaV0uc3RhdHVzICE9PSAnJyApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5zaXRlUGFnZXNSZWFkeUZvclNlcnZlclt0aGlzLnNpdGVQYWdlc1tpXS5uYW1lXSA9IHRoaXMuc2l0ZVBhZ2VzW2ldLnByZXBGb3JTYXZlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBzZXRzIGEgcGFnZSBhcyB0aGUgYWN0aXZlIG9uZVxuICAgICAgICAqL1xuICAgICAgICBzZXRBY3RpdmU6IGZ1bmN0aW9uKHBhZ2UpIHtcblxuICAgICAgICAgICAgLy9yZWZlcmVuY2UgdG8gdGhlIGFjdGl2ZSBwYWdlXG4gICAgICAgICAgICB0aGlzLmFjdGl2ZVBhZ2UgPSBwYWdlO1xuXG4gICAgICAgICAgICAvL2hpZGUgb3RoZXIgcGFnZXNcbiAgICAgICAgICAgIGZvcih2YXIgaSBpbiB0aGlzLnNpdGVQYWdlcykge1xuICAgICAgICAgICAgICAgIHRoaXMuc2l0ZVBhZ2VzW2ldLnBhcmVudFVMLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vZGlzcGxheSBhY3RpdmUgb25lXG4gICAgICAgICAgICB0aGlzLmFjdGl2ZVBhZ2UucGFyZW50VUwuc3R5bGUuZGlzcGxheSA9ICdibG9jayc7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBkZS1hY3RpdmUgYWxsIHBhZ2UgbWVudSBpdGVtc1xuICAgICAgICAqL1xuICAgICAgICBkZUFjdGl2YXRlQWxsOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIHBhZ2VzID0gdGhpcy5wYWdlc01lbnUucXVlcnlTZWxlY3RvckFsbCgnbGknKTtcblxuICAgICAgICAgICAgZm9yKCB2YXIgaSA9IDA7IGkgPCBwYWdlcy5sZW5ndGg7IGkrKyApIHtcbiAgICAgICAgICAgICAgICBwYWdlc1tpXS5jbGFzc0xpc3QucmVtb3ZlKCdhY3RpdmUnKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGFkZHMgYSBuZXcgcGFnZSB0byB0aGUgc2l0ZVxuICAgICAgICAqL1xuICAgICAgICBuZXdQYWdlOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgc2l0ZS5kZUFjdGl2YXRlQWxsKCk7XG5cbiAgICAgICAgICAgIC8vY3JlYXRlIHRoZSBuZXcgcGFnZSBpbnN0YW5jZVxuXG4gICAgICAgICAgICB2YXIgcGFnZURhdGEgPSBbXTtcbiAgICAgICAgICAgIHZhciB0ZW1wID0ge1xuICAgICAgICAgICAgICAgIHBhZ2VzX2lkOiAwXG4gICAgICAgICAgICB9O1xuICAgICAgICAgICAgcGFnZURhdGFbMF0gPSB0ZW1wO1xuXG4gICAgICAgICAgICB2YXIgbmV3UGFnZU5hbWUgPSAncGFnZScrKHNpdGUuc2l0ZVBhZ2VzLmxlbmd0aCsxKTtcblxuICAgICAgICAgICAgdmFyIG5ld1BhZ2UgPSBuZXcgUGFnZShuZXdQYWdlTmFtZSwgcGFnZURhdGEsIHNpdGUuc2l0ZVBhZ2VzLmxlbmd0aCsxKTtcblxuICAgICAgICAgICAgbmV3UGFnZS5zdGF0dXMgPSAnbmV3JztcblxuICAgICAgICAgICAgbmV3UGFnZS5zZWxlY3RQYWdlKCk7XG4gICAgICAgICAgICBuZXdQYWdlLmVkaXRQYWdlTmFtZSgpO1xuXG4gICAgICAgICAgICBuZXdQYWdlLmlzRW1wdHkoKTtcblxuICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGNoZWNrcyBpZiB0aGUgbmFtZSBvZiBhIHBhZ2UgaXMgYWxsb3dlZFxuICAgICAgICAqL1xuICAgICAgICBjaGVja1BhZ2VOYW1lOiBmdW5jdGlvbihwYWdlTmFtZSkge1xuXG4gICAgICAgICAgICAvL21ha2Ugc3VyZSB0aGUgbmFtZSBpcyB1bmlxdWVcbiAgICAgICAgICAgIGZvciggdmFyIGkgaW4gdGhpcy5zaXRlUGFnZXMgKSB7XG5cbiAgICAgICAgICAgICAgICBpZiggdGhpcy5zaXRlUGFnZXNbaV0ubmFtZSA9PT0gcGFnZU5hbWUgJiYgdGhpcy5hY3RpdmVQYWdlICE9PSB0aGlzLnNpdGVQYWdlc1tpXSApIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5wYWdlTmFtZUVycm9yID0gXCJUaGUgcGFnZSBuYW1lIG11c3QgYmUgdW5pcXVlLlwiO1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHJldHVybiB0cnVlO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgcmVtb3ZlcyB1bmFsbG93ZWQgY2hhcmFjdGVycyBmcm9tIHRoZSBwYWdlIG5hbWVcbiAgICAgICAgKi9cbiAgICAgICAgcHJlcFBhZ2VOYW1lOiBmdW5jdGlvbihwYWdlTmFtZSkge1xuXG4gICAgICAgICAgICBwYWdlTmFtZSA9IHBhZ2VOYW1lLnJlcGxhY2UoJyAnLCAnJyk7XG4gICAgICAgICAgICBwYWdlTmFtZSA9IHBhZ2VOYW1lLnJlcGxhY2UoL1s/KiEufCYjOyQlQFwiPD4oKSssXS9nLCBcIlwiKTtcblxuICAgICAgICAgICAgcmV0dXJuIHBhZ2VOYW1lO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgc2F2ZSBwYWdlIHNldHRpbmdzIGZvciB0aGUgY3VycmVudCBwYWdlXG4gICAgICAgICovXG4gICAgICAgIHVwZGF0ZVBhZ2VTZXR0aW5nczogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHNpdGUuYWN0aXZlUGFnZS5wYWdlU2V0dGluZ3MudGl0bGUgPSBzaXRlLmlucHV0UGFnZVNldHRpbmdzVGl0bGUudmFsdWU7XG4gICAgICAgICAgICBzaXRlLmFjdGl2ZVBhZ2UucGFnZVNldHRpbmdzLm1ldGFfZGVzY3JpcHRpb24gPSBzaXRlLmlucHV0UGFnZVNldHRpbmdzTWV0YURlc2NyaXB0aW9uLnZhbHVlO1xuICAgICAgICAgICAgc2l0ZS5hY3RpdmVQYWdlLnBhZ2VTZXR0aW5ncy5tZXRhX2tleXdvcmRzID0gc2l0ZS5pbnB1dFBhZ2VTZXR0aW5nc01ldGFLZXl3b3Jkcy52YWx1ZTtcbiAgICAgICAgICAgIHNpdGUuYWN0aXZlUGFnZS5wYWdlU2V0dGluZ3MuaGVhZGVyX2luY2x1ZGVzID0gc2l0ZS5pbnB1dFBhZ2VTZXR0aW5nc0luY2x1ZGVzLnZhbHVlO1xuICAgICAgICAgICAgc2l0ZS5hY3RpdmVQYWdlLnBhZ2VTZXR0aW5ncy5wYWdlX2NzcyA9IHNpdGUuaW5wdXRQYWdlU2V0dGluZ3NQYWdlQ3NzLnZhbHVlO1xuXG4gICAgICAgICAgICBzaXRlLnNldFBlbmRpbmdDaGFuZ2VzKHRydWUpO1xuXG4gICAgICAgICAgICAkKHNpdGUubW9kYWxQYWdlU2V0dGluZ3MpLm1vZGFsKCdoaWRlJyk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICB1cGRhdGUgcGFnZSBzdGF0dXNlc1xuICAgICAgICAqL1xuICAgICAgICB1cGRhdGVQYWdlU3RhdHVzOiBmdW5jdGlvbihzdGF0dXMpIHtcblxuICAgICAgICAgICAgZm9yKCB2YXIgaSBpbiB0aGlzLnNpdGVQYWdlcyApIHtcbiAgICAgICAgICAgICAgICB0aGlzLnNpdGVQYWdlc1tpXS5zdGF0dXMgPSBzdGF0dXM7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfVxuXG4gICAgfTtcblxuICAgIGJ1aWxkZXJVSS5pbml0KCk7IHNpdGUuaW5pdCgpO1xuXG5cbiAgICAvLyoqKiogRVhQT1JUU1xuICAgIG1vZHVsZS5leHBvcnRzLnNpdGUgPSBzaXRlO1xuICAgIG1vZHVsZS5leHBvcnRzLmJ1aWxkZXJVSSA9IGJ1aWxkZXJVSTtcblxufSgpKTsiLCIoZnVuY3Rpb24gKCkge1xuXHRcInVzZSBzdHJpY3RcIjtcblxuICAgIG1vZHVsZS5leHBvcnRzLnBhZ2VDb250YWluZXIgPSBcIiNwYWdlXCI7XG5cbiAgICBtb2R1bGUuZXhwb3J0cy5lZGl0YWJsZUl0ZW1zID0ge1xuICAgICAgICAnc3Bhbi5mYSc6IFsnY29sb3InLCAnZm9udC1zaXplJ10sXG4gICAgICAgICcuYmcuYmcxJzogWydiYWNrZ3JvdW5kLWNvbG9yJ10sXG4gICAgICAgICduYXYgYSwgYS5lZGl0JzogWydjb2xvcicsICdmb250LXdlaWdodCcsICd0ZXh0LXRyYW5zZm9ybSddLFxuICAgICAgICAnaDEsIGgyLCBoMywgaDQsIGg1LCBwJzogWydjb2xvcicsICdmb250LXNpemUnLCAnYmFja2dyb3VuZC1jb2xvcicsICdmb250LWZhbWlseSddLFxuICAgICAgICAnYS5idG4sIGJ1dHRvbi5idG4nOiBbJ2JvcmRlci1yYWRpdXMnLCAnZm9udC1zaXplJywgJ2JhY2tncm91bmQtY29sb3InXSxcblxuICAgICAgICAnaW1nJzogWydib3JkZXItdG9wLWxlZnQtcmFkaXVzJywgJ2JvcmRlci10b3AtcmlnaHQtcmFkaXVzJywgJ2JvcmRlci1ib3R0b20tbGVmdC1yYWRpdXMnLCAnYm9yZGVyLWJvdHRvbS1yaWdodC1yYWRpdXMnLCAnYm9yZGVyLWNvbG9yJywgJ2JvcmRlci1zdHlsZScsICdib3JkZXItd2lkdGgnXSxcbiAgICAgICAgJ2hyLmRhc2hlZCc6IFsnYm9yZGVyLWNvbG9yJywgJ2JvcmRlci13aWR0aCddLFxuICAgICAgICAnLmRpdmlkZXIgPiBzcGFuJzogWydjb2xvcicsICdmb250LXNpemUnXSxcbiAgICAgICAgJ2hyLnNoYWRvd0Rvd24nOiBbJ21hcmdpbi10b3AnLCAnbWFyZ2luLWJvdHRvbSddLFxuICAgICAgICAnLmZvb3RlciBhJzogWydjb2xvciddLFxuICAgICAgICAnLmJnLmJnMSwgLmJnLmJnMiwgLmhlYWRlcjEwLCAuaGVhZGVyMTEnOiBbJ2JhY2tncm91bmQtaW1hZ2UnLCAnYmFja2dyb3VuZC1jb2xvciddLFxuICAgICAgICAnLmZyYW1lQ292ZXInOiBbXVxuICAgIH07XG5cbiAgICBtb2R1bGUuZXhwb3J0cy5lZGl0YWJsZUl0ZW1PcHRpb25zID0ge1xuICAgICAgICAnbmF2IGEgOiBmb250LXdlaWdodCc6IFsnNDAwJywgJzcwMCddLFxuICAgICAgICAnYS5idG4gOiBib3JkZXItcmFkaXVzJzogWycwcHgnLCAnNHB4JywgJzEwcHgnXSxcbiAgICAgICAgJ2ltZyA6IGJvcmRlci1zdHlsZSc6IFsnbm9uZScsICdkb3R0ZWQnLCAnZGFzaGVkJywgJ3NvbGlkJ10sXG4gICAgICAgICdpbWcgOiBib3JkZXItd2lkdGgnOiBbJzFweCcsICcycHgnLCAnM3B4JywgJzRweCddLFxuICAgICAgICAnaDEsIGgyLCBoMywgaDQsIGg1LCBwIDogZm9udC1mYW1pbHknOiBbJ2RlZmF1bHQnLCAnTGF0bycsICdIZWx2ZXRpY2EnLCAnQXJpYWwnLCAnVGltZXMgTmV3IFJvbWFuJ10sXG4gICAgICAgICdoMiA6IGZvbnQtZmFtaWx5JzogWydkZWZhdWx0JywgJ0xhdG8nLCAnSGVsdmV0aWNhJywgJ0FyaWFsJywgJ1RpbWVzIE5ldyBSb21hbiddLFxuICAgICAgICAnaDMgOiBmb250LWZhbWlseSc6IFsnZGVmYXVsdCcsICdMYXRvJywgJ0hlbHZldGljYScsICdBcmlhbCcsICdUaW1lcyBOZXcgUm9tYW4nXSxcbiAgICAgICAgJ3AgOiBmb250LWZhbWlseSc6IFsnZGVmYXVsdCcsICdMYXRvJywgJ0hlbHZldGljYScsICdBcmlhbCcsICdUaW1lcyBOZXcgUm9tYW4nXVxuICAgIH07XG5cbiAgICBtb2R1bGUuZXhwb3J0cy5lZGl0YWJsZUNvbnRlbnQgPSBbJy5lZGl0Q29udGVudCcsICcubmF2YmFyIGEnLCAnYnV0dG9uJywgJ2EuYnRuJywgJy5mb290ZXIgYTpub3QoLmZhKScsICcudGFibGVXcmFwcGVyJywgJ2gxJywgJ2gyJ107XG5cbiAgICBtb2R1bGUuZXhwb3J0cy5hdXRvU2F2ZVRpbWVvdXQgPSA2MDAwMDtcblxuICAgIG1vZHVsZS5leHBvcnRzLnNvdXJjZUNvZGVFZGl0U3ludGF4RGVsYXkgPSAxMDAwMDtcblxufSgpKTsiLCIoZnVuY3Rpb24gKCkge1xuXHRcInVzZSBzdHJpY3RcIjtcblxuXHR2YXIgYkNvbmZpZyA9IHJlcXVpcmUoJy4vY29uZmlnLmpzJyk7XG5cdHZhciBzaXRlQnVpbGRlciA9IHJlcXVpcmUoJy4vYnVpbGRlci5qcycpO1xuXHR2YXIgYXBwVUkgPSByZXF1aXJlKCcuL3VpLmpzJykuYXBwVUk7XG5cblx0dmFyIHB1Ymxpc2ggPSB7XG5cbiAgICAgICAgYnV0dG9uUHVibGlzaDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3B1Ymxpc2hQYWdlJyksXG4gICAgICAgIGJ1dHRvblNhdmVQZW5kaW5nQmVmb3JlUHVibGlzaGluZzogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2J1dHRvblNhdmVQZW5kaW5nQmVmb3JlUHVibGlzaGluZycpLFxuICAgICAgICBwdWJsaXNoTW9kYWw6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwdWJsaXNoTW9kYWwnKSxcbiAgICAgICAgYnV0dG9uUHVibGlzaFN1Ym1pdDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3B1Ymxpc2hTdWJtaXQnKSxcbiAgICAgICAgcHVibGlzaEFjdGl2ZTogMCxcbiAgICAgICAgdGhlSXRlbToge30sXG4gICAgICAgIG1vZGFsU2l0ZVNldHRpbmdzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2l0ZVNldHRpbmdzJyksXG5cbiAgICAgICAgaW5pdDogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICQodGhpcy5idXR0b25QdWJsaXNoKS5vbignY2xpY2snLCB0aGlzLmxvYWRQdWJsaXNoTW9kYWwpO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblNhdmVQZW5kaW5nQmVmb3JlUHVibGlzaGluZykub24oJ2NsaWNrJywgdGhpcy5zYXZlQmVmb3JlUHVibGlzaGluZyk7XG4gICAgICAgICAgICAkKHRoaXMucHVibGlzaE1vZGFsKS5vbignY2hhbmdlJywgJ2lucHV0W3R5cGU9Y2hlY2tib3hdJywgdGhpcy5wdWJsaXNoQ2hlY2tib3hFdmVudCk7XG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uUHVibGlzaFN1Ym1pdCkub24oJ2NsaWNrJywgdGhpcy5wdWJsaXNoU2l0ZSk7XG4gICAgICAgICAgICAkKHRoaXMubW9kYWxTaXRlU2V0dGluZ3MpLm9uKCdjbGljaycsICcjc2l0ZVNldHRpbmdzQnJvd3NlRlRQQnV0dG9uLCAubGluaycsIHRoaXMuYnJvd3NlRlRQKTtcbiAgICAgICAgICAgICQodGhpcy5tb2RhbFNpdGVTZXR0aW5ncykub24oJ2NsaWNrJywgJyNmdHBMaXN0SXRlbXMgLmNsb3NlJywgdGhpcy5jbG9zZUZ0cEJyb3dzZXIpO1xuICAgICAgICAgICAgJCh0aGlzLm1vZGFsU2l0ZVNldHRpbmdzKS5vbignY2xpY2snLCAnI3NpdGVTZXR0aW5nc1Rlc3RGVFAnLCB0aGlzLnRlc3RGVFBDb25uZWN0aW9uKTtcblxuICAgICAgICAgICAgLy9zaG93IHRoZSBwdWJsaXNoIGJ1dHRvblxuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblB1Ymxpc2gpLnNob3coKTtcblxuICAgICAgICAgICAgLy9saXN0ZW4gdG8gc2l0ZSBzZXR0aW5ncyBsb2FkIGV2ZW50XG4gICAgICAgICAgICAkKCdib2R5Jykub24oJ3NpdGVTZXR0aW5nc0xvYWQnLCB0aGlzLnNob3dQdWJsaXNoU2V0dGluZ3MpO1xuXG4gICAgICAgICAgICAvL3B1Ymxpc2ggaGFzaD9cbiAgICAgICAgICAgIGlmKCB3aW5kb3cubG9jYXRpb24uaGFzaCA9PT0gXCIjcHVibGlzaFwiICkge1xuICAgICAgICAgICAgICAgICQodGhpcy5idXR0b25QdWJsaXNoKS5jbGljaygpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBoZWFkZXIgdG9vbHRpcHNcbiAgICAgICAgICAgIC8vaWYoIHRoaXMuYnV0dG9uUHVibGlzaC5oYXNBdHRyaWJ1dGUoJ2RhdGEtdG9nZ2xlJykgJiYgdGhpcy5idXR0b25QdWJsaXNoLmdldEF0dHJpYnV0ZSgnZGF0YS10b2dnbGUnKSA9PSAndG9vbHRpcCcgKSB7XG4gICAgICAgICAgICAvLyAgICQodGhpcy5idXR0b25QdWJsaXNoKS50b29sdGlwKCdzaG93Jyk7XG4gICAgICAgICAgICAvLyAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKXskKHRoaXMuYnV0dG9uUHVibGlzaCkudG9vbHRpcCgnaGlkZScpfSwgNTAwMCk7XG4gICAgICAgICAgICAvL31cblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGxvYWRzIHRoZSBwdWJsaXNoIG1vZGFsXG4gICAgICAgICovXG4gICAgICAgIGxvYWRQdWJsaXNoTW9kYWw6IGZ1bmN0aW9uKGUpIHtcblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICBpZiggcHVibGlzaC5wdWJsaXNoQWN0aXZlID09PSAwICkgey8vY2hlY2sgaWYgd2UncmUgY3VycmVudGx5IHB1Ymxpc2hpbmcgYW55dGhpbmdcblxuICAgICAgICAgICAgICAgIC8vaGlkZSBhbGVydHNcbiAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsIC5tb2RhbC1hbGVydHMgPiAqJykuZWFjaChmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCAubW9kYWwtYm9keSA+IC5hbGVydC1zdWNjZXNzJykuaGlkZSgpO1xuXG4gICAgICAgICAgICAgICAgLy9oaWRlIGxvYWRlcnNcbiAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsX2Fzc2V0cyAucHVibGlzaGluZycpLmVhY2goZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLndvcmtpbmcnKS5zaG93KCk7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLmRvbmUnKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvL3JlbW92ZSBwdWJsaXNoZWQgY2xhc3MgZnJvbSBhc3NldCBjaGVja2JveGVzXG4gICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbF9hc3NldHMgaW5wdXQnKS5lYWNoKGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQ2xhc3MoJ3B1Ymxpc2hlZCcpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgLy9kbyB3ZSBoYXZlIHBlbmRpbmcgY2hhbmdlcz9cbiAgICAgICAgICAgICAgICBpZiggc2l0ZUJ1aWxkZXIuc2l0ZS5wZW5kaW5nQ2hhbmdlcyA9PT0gdHJ1ZSApIHsvL3dlJ3ZlIGdvdCBjaGFuZ2VzLCBzYXZlIGZpcnN0XG5cbiAgICAgICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCAjcHVibGlzaFBlbmRpbmdDaGFuZ2VzTWVzc2FnZScpLnNob3coKTtcbiAgICAgICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCAubW9kYWwtYm9keS1jb250ZW50JykuaGlkZSgpO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHsvL2FsbCBzZXQsIGdldCBvbiBpdCB3aXRoIHB1Ymxpc2hpbmdcblxuICAgICAgICAgICAgICAgICAgICAvL2dldCB0aGUgY29ycmVjdCBwYWdlcyBpbiB0aGUgUGFnZXMgc2VjdGlvbiBvZiB0aGUgcHVibGlzaCBtb2RhbFxuICAgICAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsX3BhZ2VzIHRib2R5ID4gKicpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgICAgICQoJyNwYWdlcyBsaTp2aXNpYmxlJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgdGhlUGFnZSA9ICQodGhpcykuZmluZCgnYTpmaXJzdCcpLnRleHQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciB0aGVSb3cgPSAkKCc8dHI+PHRkIGNsYXNzPVwidGV4dC1jZW50ZXJcIiBzdHlsZT1cIndpZHRoOiAzMHB4O1wiPjxsYWJlbCBjbGFzcz1cImNoZWNrYm94IG5vLWxhYmVsXCI+PGlucHV0IHR5cGU9XCJjaGVja2JveFwiIHZhbHVlPVwiJyt0aGVQYWdlKydcIiBpZD1cIlwiIGRhdGEtdHlwZT1cInBhZ2VcIiBuYW1lPVwicGFnZXNbXVwiIGRhdGEtdG9nZ2xlPVwiY2hlY2tib3hcIj48L2xhYmVsPjwvdGQ+PHRkPicrdGhlUGFnZSsnPHNwYW4gY2xhc3M9XCJwdWJsaXNoaW5nXCI+PHNwYW4gY2xhc3M9XCJ3b3JraW5nXCI+UHVibGlzaGluZy4uLiA8aW1nIHNyYz1cIicrYXBwVUkuYmFzZVVybCsnaW1hZ2VzL3B1Ymxpc2hMb2FkZXIuZ2lmXCI+PC9zcGFuPjxzcGFuIGNsYXNzPVwiZG9uZSB0ZXh0LXByaW1hcnlcIj5QdWJsaXNoZWQgJm5ic3A7PHNwYW4gY2xhc3M9XCJmdWktY2hlY2tcIj48L3NwYW4+PC9zcGFuPjwvc3Bhbj48L3RkPjwvdHI+Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vY2hlY2tib3hpZnlcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoZVJvdy5maW5kKCdpbnB1dCcpLnJhZGlvY2hlY2soKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoZVJvdy5maW5kKCdpbnB1dCcpLm9uKCdjaGVjayB1bmNoZWNrIHRvZ2dsZScsIGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5jbG9zZXN0KCd0cicpWyQodGhpcykucHJvcCgnY2hlY2tlZCcpID8gJ2FkZENsYXNzJyA6ICdyZW1vdmVDbGFzcyddKCdzZWxlY3RlZC1yb3cnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsX3BhZ2VzIHRib2R5JykuYXBwZW5kKCB0aGVSb3cgKTtcblxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsICNwdWJsaXNoUGVuZGluZ0NoYW5nZXNNZXNzYWdlJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsIC5tb2RhbC1ib2R5LWNvbnRlbnQnKS5zaG93KCk7XG5cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vZW5hYmxlL2Rpc2FibGUgcHVibGlzaCBidXR0b25cblxuICAgICAgICAgICAgdmFyIGFjdGl2YXRlQnV0dG9uID0gZmFsc2U7XG5cbiAgICAgICAgICAgICQoJyNwdWJsaXNoTW9kYWwgaW5wdXRbdHlwZT1jaGVja2JveF0nKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICBpZiggJCh0aGlzKS5wcm9wKCdjaGVja2VkJykgKSB7XG4gICAgICAgICAgICAgICAgICAgIGFjdGl2YXRlQnV0dG9uID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBpZiggYWN0aXZhdGVCdXR0b24gKSB7XG4gICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hTdWJtaXQnKS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hTdWJtaXQnKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCcpLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBzYXZlcyBwZW5kaW5nIGNoYW5nZXMgYmVmb3JlIHB1Ymxpc2hpbmdcbiAgICAgICAgKi9cbiAgICAgICAgc2F2ZUJlZm9yZVB1Ymxpc2hpbmc6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsICNwdWJsaXNoUGVuZGluZ0NoYW5nZXNNZXNzYWdlJykuaGlkZSgpO1xuICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCAubG9hZGVyJykuc2hvdygpO1xuICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgc2l0ZUJ1aWxkZXIuc2l0ZS5wcmVwRm9yU2F2ZShmYWxzZSk7XG5cbiAgICAgICAgICAgIHZhciBzZXJ2ZXJEYXRhID0ge307XG4gICAgICAgICAgICBzZXJ2ZXJEYXRhLnBhZ2VzID0gc2l0ZUJ1aWxkZXIuc2l0ZS5zaXRlUGFnZXNSZWFkeUZvclNlcnZlcjtcbiAgICAgICAgICAgIGlmKCBzaXRlQnVpbGRlci5zaXRlLnBhZ2VzVG9EZWxldGUubGVuZ3RoID4gMCApIHtcbiAgICAgICAgICAgICAgICBzZXJ2ZXJEYXRhLnRvRGVsZXRlID0gc2l0ZUJ1aWxkZXIuc2l0ZS5wYWdlc1RvRGVsZXRlO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgc2VydmVyRGF0YS5zaXRlRGF0YSA9IHNpdGVCdWlsZGVyLnNpdGUuZGF0YTtcblxuICAgICAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgICAgICB1cmw6IGFwcFVJLnNpdGVVcmwrXCJzaXRlL3NhdmUvMVwiLFxuICAgICAgICAgICAgICAgIHR5cGU6IFwiUE9TVFwiLFxuICAgICAgICAgICAgICAgIGRhdGFUeXBlOiBcImpzb25cIixcbiAgICAgICAgICAgICAgICBkYXRhOiBzZXJ2ZXJEYXRhLFxuICAgICAgICAgICAgfSkuZG9uZShmdW5jdGlvbihyZXMpe1xuXG4gICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCAubG9hZGVyJykuZmFkZU91dCg1MDAsIGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCAubW9kYWwtYWxlcnRzJykuYXBwZW5kKCAkKHJlcy5yZXNwb25zZUhUTUwpICk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9zZWxmLWRlc3RydWN0IHN1Y2Nlc3MgbWVzc2FnZXNcbiAgICAgICAgICAgICAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbigpeyQoJyNwdWJsaXNoTW9kYWwgLm1vZGFsLWFsZXJ0cyAuYWxlcnQtc3VjY2VzcycpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbigpeyQodGhpcykucmVtb3ZlKCk7fSk7fSwgMjUwMCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9lbmFibGUgYnV0dG9uXG4gICAgICAgICAgICAgICAgICAgICQoJyNwdWJsaXNoTW9kYWwgI3B1Ymxpc2hQZW5kaW5nQ2hhbmdlc01lc3NhZ2UgLmJ0bi5zYXZlJykucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIGlmKCByZXMucmVzcG9uc2VDb2RlID09PSAxICkgey8vY2hhbmdlcyB3ZXJlIHNhdmVkIHdpdGhvdXQgaXNzdWVzXG5cbiAgICAgICAgICAgICAgICAgICAgLy9ubyBtb3JlIHBlbmRpbmcgY2hhbmdlc1xuICAgICAgICAgICAgICAgICAgICBzaXRlQnVpbGRlci5zaXRlLnNldFBlbmRpbmdDaGFuZ2VzKGZhbHNlKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2dldCB0aGUgY29ycmVjdCBwYWdlcyBpbiB0aGUgUGFnZXMgc2VjdGlvbiBvZiB0aGUgcHVibGlzaCBtb2RhbFxuICAgICAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsX3BhZ2VzIHRib2R5ID4gKicpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgICAgICQoJyNwYWdlcyBsaTp2aXNpYmxlJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgdGhlUGFnZSA9ICQodGhpcykuZmluZCgnYTpmaXJzdCcpLnRleHQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciB0aGVSb3cgPSAkKCc8dHI+PHRkIGNsYXNzPVwidGV4dC1jZW50ZXJcIiBzdHlsZT1cIndpZHRoOiAwcHg7XCI+PGxhYmVsIGNsYXNzPVwiY2hlY2tib3hcIj48aW5wdXQgdHlwZT1cImNoZWNrYm94XCIgdmFsdWU9XCInK3RoZVBhZ2UrJ1wiIGlkPVwiXCIgZGF0YS10eXBlPVwicGFnZVwiIG5hbWU9XCJwYWdlc1tdXCIgZGF0YS10b2dnbGU9XCJjaGVja2JveFwiPjwvbGFiZWw+PC90ZD48dGQ+Jyt0aGVQYWdlKyc8c3BhbiBjbGFzcz1cInB1Ymxpc2hpbmdcIj48c3BhbiBjbGFzcz1cIndvcmtpbmdcIj5QdWJsaXNoaW5nLi4uIDxpbWcgc3JjPVwiJythcHBVSS5iYXNlVXJsKydpbWFnZXMvcHVibGlzaExvYWRlci5naWZcIj48L3NwYW4+PHNwYW4gY2xhc3M9XCJkb25lIHRleHQtcHJpbWFyeVwiPlB1Ymxpc2hlZCAmbmJzcDs8c3BhbiBjbGFzcz1cImZ1aS1jaGVja1wiPjwvc3Bhbj48L3NwYW4+PC9zcGFuPjwvdGQ+PC90cj4nKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9jaGVja2JveGlmeVxuICAgICAgICAgICAgICAgICAgICAgICAgdGhlUm93LmZpbmQoJ2lucHV0JykucmFkaW9jaGVjaygpO1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhlUm93LmZpbmQoJ2lucHV0Jykub24oJ2NoZWNrIHVuY2hlY2sgdG9nZ2xlJywgZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJ3RyJylbJCh0aGlzKS5wcm9wKCdjaGVja2VkJykgPyAnYWRkQ2xhc3MnIDogJ3JlbW92ZUNsYXNzJ10oJ3NlbGVjdGVkLXJvdycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyNwdWJsaXNoTW9kYWxfcGFnZXMgdGJvZHknKS5hcHBlbmQoIHRoZVJvdyApO1xuXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vc2hvdyBjb250ZW50XG4gICAgICAgICAgICAgICAgICAgICQoJyNwdWJsaXNoTW9kYWwgLm1vZGFsLWJvZHktY29udGVudCcpLmZhZGVJbig1MDApO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGV2ZW50IGhhbmRsZXIgZm9yIHRoZSBjaGVja2JveGVzIGluc2lkZSB0aGUgcHVibGlzaCBtb2RhbFxuICAgICAgICAqL1xuICAgICAgICBwdWJsaXNoQ2hlY2tib3hFdmVudDogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciBhY3RpdmF0ZUJ1dHRvbiA9IGZhbHNlO1xuXG4gICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsIGlucHV0W3R5cGU9Y2hlY2tib3hdJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgaWYoICQodGhpcykucHJvcCgnY2hlY2tlZCcpICkge1xuICAgICAgICAgICAgICAgICAgICBhY3RpdmF0ZUJ1dHRvbiA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBpZiggYWN0aXZhdGVCdXR0b24gKSB7XG5cbiAgICAgICAgICAgICAgICAkKCcjcHVibGlzaFN1Ym1pdCcpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hTdWJtaXQnKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgcHVibGlzaGVzIHRoZSBzZWxlY3RlZCBpdGVtc1xuICAgICAgICAqL1xuICAgICAgICBwdWJsaXNoU2l0ZTogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIC8vdHJhY2sgdGhlIHB1Ymxpc2hpbmcgc3RhdGVcbiAgICAgICAgICAgIHB1Ymxpc2gucHVibGlzaEFjdGl2ZSA9IDE7XG5cbiAgICAgICAgICAgIC8vZGlzYWJsZSBidXR0b25cbiAgICAgICAgICAgICQoJyNwdWJsaXNoU3VibWl0LCAjcHVibGlzaENhbmNlbCcpLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICAvL3JlbW92ZSBleGlzdGluZyBhbGVydHNcbiAgICAgICAgICAgICQoJyNwdWJsaXNoTW9kYWwgLm1vZGFsLWFsZXJ0cyA+IConKS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgLy9wcmVwYXJlIHN0dWZmXG4gICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsIGZvcm0gaW5wdXRbdHlwZT1cImhpZGRlblwiXS5wYWdlJykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgIC8vbG9vcCB0aHJvdWdoIGFsbCBwYWdlc1xuICAgICAgICAgICAgJCgnI3BhZ2VMaXN0ID4gdWwnKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAvL2V4cG9ydCB0aGlzIHBhZ2U/XG4gICAgICAgICAgICAgICAgaWYoICQoJyNwdWJsaXNoTW9kYWwgI3B1Ymxpc2hNb2RhbF9wYWdlcyBpbnB1dDplcSgnKygkKHRoaXMpLmluZGV4KCkrMSkrJyknKS5wcm9wKCdjaGVja2VkJykgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9ncmFiIHRoZSBza2VsZXRvbiBtYXJrdXBcbiAgICAgICAgICAgICAgICAgICAgdmFyIG5ld0RvY01haW5QYXJlbnQgPSAkKCdpZnJhbWUjc2tlbGV0b24nKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciApO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vZW1wdHkgb3V0IHRoZSBza2VsZXRvblxuICAgICAgICAgICAgICAgICAgICBuZXdEb2NNYWluUGFyZW50LmZpbmQoJyonKS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2xvb3AgdGhyb3VnaCBwYWdlIGlmcmFtZXMgYW5kIGdyYWIgdGhlIGJvZHkgc3R1ZmZcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCdpZnJhbWUnKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBhdHRyID0gJCh0aGlzKS5hdHRyKCdkYXRhLXNhbmRib3gnKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHRoZUNvbnRlbnRzO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodHlwZW9mIGF0dHIgIT09IHR5cGVvZiB1bmRlZmluZWQgJiYgYXR0ciAhPT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGVDb250ZW50cyA9ICQoJyNzYW5kYm94ZXMgIycrYXR0cikuY29udGVudHMoKS5maW5kKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhlQ29udGVudHMgPSAkKHRoaXMpLmNvbnRlbnRzKCkuZmluZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyICk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRoZUNvbnRlbnRzLmZpbmQoJy5mcmFtZUNvdmVyJykuZWFjaChmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9yZW1vdmUgaW5saW5lIHN0eWxpbmcgbGVmdG92ZXJzXG4gICAgICAgICAgICAgICAgICAgICAgICBmb3IoIHZhciBrZXkgaW4gYkNvbmZpZy5lZGl0YWJsZUl0ZW1zICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhlQ29udGVudHMuZmluZCgga2V5ICkuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQXR0cignZGF0YS1zZWxlY3RvcicpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKCAkKHRoaXMpLmF0dHIoJ3N0eWxlJykgPT09ICcnICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmVBdHRyKCdzdHlsZScpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGJDb25maWcuZWRpdGFibGVDb250ZW50Lmxlbmd0aDsgKytpKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmNvbnRlbnRzKCkuZmluZCggYkNvbmZpZy5lZGl0YWJsZUNvbnRlbnRbaV0gKS5lYWNoKGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQXR0cignZGF0YS1zZWxlY3RvcicpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciB0b0FkZCA9IHRoZUNvbnRlbnRzLmh0bWwoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9ncmFiIHNjcmlwdHNcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHNjcmlwdHMgPSAkKHRoaXMpLmNvbnRlbnRzKCkuZmluZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyICkuZmluZCgnc2NyaXB0Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKCBzY3JpcHRzLnNpemUoKSA+IDAgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgdGhlSWZyYW1lID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoXCJza2VsZXRvblwiKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdHMuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBzY3JpcHQ7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoICQodGhpcykudGV4dCgpICE9PSAnJyApIHsvL3NjcmlwdCB0YWdzIHdpdGggY29udGVudFxuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzY3JpcHQgPSB0aGVJZnJhbWUuY29udGVudFdpbmRvdy5kb2N1bWVudC5jcmVhdGVFbGVtZW50KFwic2NyaXB0XCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2NyaXB0LnR5cGUgPSAndGV4dC9qYXZhc2NyaXB0JztcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdC5pbm5lckhUTUwgPSAkKHRoaXMpLnRleHQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoZUlmcmFtZS5jb250ZW50V2luZG93LmRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCBiQ29uZmlnLnBhZ2VDb250YWluZXIuc3Vic3RyaW5nKDEpICkuYXBwZW5kQ2hpbGQoc2NyaXB0KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYoICQodGhpcykuYXR0cignc3JjJykgIT09IG51bGwgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdCA9IHRoZUlmcmFtZS5jb250ZW50V2luZG93LmRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoXCJzY3JpcHRcIik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzY3JpcHQudHlwZSA9ICd0ZXh0L2phdmFzY3JpcHQnO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2NyaXB0LnNyYyA9ICQodGhpcykuYXR0cignc3JjJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGVJZnJhbWUuY29udGVudFdpbmRvdy5kb2N1bWVudC5nZXRFbGVtZW50QnlJZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyLnN1YnN0cmluZygxKSApLmFwcGVuZENoaWxkKHNjcmlwdCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0RvY01haW5QYXJlbnQuYXBwZW5kKCAkKHRvQWRkKSApO1xuXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciBuZXdJbnB1dCA9ICQoJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgY2xhc3M9XCJwYWdlXCIgbmFtZT1cInhwYWdlc1snKyQoJyNwYWdlcyBsaTplcSgnKygkKHRoaXMpLmluZGV4KCkrMSkrJykgYTpmaXJzdCcpLnRleHQoKSsnXVwiIHZhbHVlPVwiXCI+Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCBmb3JtJykucHJlcGVuZCggbmV3SW5wdXQgKTtcblxuICAgICAgICAgICAgICAgICAgICBuZXdJbnB1dC52YWwoIFwiPGh0bWw+XCIrJCgnaWZyYW1lI3NrZWxldG9uJykuY29udGVudHMoKS5maW5kKCdodG1sJykuaHRtbCgpK1wiPC9odG1sPlwiICk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBwdWJsaXNoLnB1Ymxpc2hBc3NldCgpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgcHVibGlzaEFzc2V0OiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIHRvUHVibGlzaCA9ICQoJyNwdWJsaXNoTW9kYWxfYXNzZXRzIGlucHV0W3R5cGU9Y2hlY2tib3hdOmNoZWNrZWQ6bm90KC5wdWJsaXNoZWQsIC50b2dnbGVBbGwpLCAjcHVibGlzaE1vZGFsX3BhZ2VzIGlucHV0W3R5cGU9Y2hlY2tib3hdOmNoZWNrZWQ6bm90KC5wdWJsaXNoZWQsIC50b2dnbGVBbGwpJyk7XG5cbiAgICAgICAgICAgIGlmKCB0b1B1Ymxpc2guc2l6ZSgpID4gMCApIHtcblxuICAgICAgICAgICAgICAgIHB1Ymxpc2gudGhlSXRlbSA9IHRvUHVibGlzaC5maXJzdCgpO1xuXG4gICAgICAgICAgICAgICAgLy9kaXNwbGF5IHRoZSBhc3NldCBsb2FkZXJcbiAgICAgICAgICAgICAgICBwdWJsaXNoLnRoZUl0ZW0uY2xvc2VzdCgndGQnKS5uZXh0KCkuZmluZCgnLnB1Ymxpc2hpbmcnKS5mYWRlSW4oNTAwKTtcblxuICAgICAgICAgICAgICAgIHZhciB0aGVEYXRhO1xuXG4gICAgICAgICAgICAgICAgaWYoIHB1Ymxpc2gudGhlSXRlbS5hdHRyKCdkYXRhLXR5cGUnKSA9PT0gJ3BhZ2UnICkge1xuXG4gICAgICAgICAgICAgICAgICAgIHRoZURhdGEgPSB7c2l0ZV9pZDogJCgnZm9ybSNwdWJsaXNoRm9ybSBpbnB1dFtuYW1lPXNpdGVfaWRdJykudmFsKCksIGl0ZW06IHB1Ymxpc2gudGhlSXRlbS52YWwoKSwgcGFnZUNvbnRlbnQ6ICQoJ2Zvcm0jcHVibGlzaEZvcm0gaW5wdXRbbmFtZT1cInhwYWdlc1snK3B1Ymxpc2gudGhlSXRlbS52YWwoKSsnXVwiXScpLnZhbCgpfTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggcHVibGlzaC50aGVJdGVtLmF0dHIoJ2RhdGEtdHlwZScpID09PSAnYXNzZXQnICkge1xuXG4gICAgICAgICAgICAgICAgICAgIHRoZURhdGEgPSB7c2l0ZV9pZDogJCgnZm9ybSNwdWJsaXNoRm9ybSBpbnB1dFtuYW1lPXNpdGVfaWRdJykudmFsKCksIGl0ZW06IHB1Ymxpc2gudGhlSXRlbS52YWwoKX07XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgICAgICAgICB1cmw6ICQoJ2Zvcm0jcHVibGlzaEZvcm0nKS5hdHRyKCdhY3Rpb24nKStcIi9cIitwdWJsaXNoLnRoZUl0ZW0uYXR0cignZGF0YS10eXBlJyksXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6ICdwb3N0JyxcbiAgICAgICAgICAgICAgICAgICAgZGF0YTogdGhlRGF0YSxcbiAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6ICdqc29uJ1xuICAgICAgICAgICAgICAgIH0pLmRvbmUoZnVuY3Rpb24ocmV0KXtcblxuICAgICAgICAgICAgICAgICAgICBpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMCApIHsvL2ZhdGFsIGVycm9yLCBwdWJsaXNoaW5nIHdpbGwgc3RvcFxuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL2hpZGUgaW5kaWNhdG9yc1xuICAgICAgICAgICAgICAgICAgICAgICAgcHVibGlzaC50aGVJdGVtLmNsb3Nlc3QoJ3RkJykubmV4dCgpLmZpbmQoJy53b3JraW5nJykuaGlkZSgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL2VuYWJsZSBidXR0b25zXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjcHVibGlzaFN1Ym1pdCwgI3B1Ymxpc2hDYW5jZWwnKS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyNwdWJsaXNoTW9kYWwgLm1vZGFsLWFsZXJ0cycpLmFwcGVuZCggJChyZXQucmVzcG9uc2VIVE1MKSApO1xuXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMSApIHsvL25vIGlzc3Vlc1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL3Nob3cgZG9uZVxuICAgICAgICAgICAgICAgICAgICAgICAgcHVibGlzaC50aGVJdGVtLmNsb3Nlc3QoJ3RkJykubmV4dCgpLmZpbmQoJy53b3JraW5nJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgcHVibGlzaC50aGVJdGVtLmNsb3Nlc3QoJ3RkJykubmV4dCgpLmZpbmQoJy5kb25lJykuZmFkZUluKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBwdWJsaXNoLnRoZUl0ZW0uYWRkQ2xhc3MoJ3B1Ymxpc2hlZCcpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBwdWJsaXNoLnB1Ymxpc2hBc3NldCgpO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgLy9wdWJsaXNoaW5nIGlzIGRvbmVcbiAgICAgICAgICAgICAgICBwdWJsaXNoLnB1Ymxpc2hBY3RpdmUgPSAwO1xuXG4gICAgICAgICAgICAgICAgLy9lbmFibGUgYnV0dG9uc1xuICAgICAgICAgICAgICAgICQoJyNwdWJsaXNoU3VibWl0LCAjcHVibGlzaENhbmNlbCcpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICAgICAgLy9zaG93IG1lc3NhZ2VcbiAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsIC5tb2RhbC1ib2R5ID4gLmFsZXJ0LXN1Y2Nlc3MnKS5mYWRlSW4oNTAwLCBmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7JCgnI3B1Ymxpc2hNb2RhbCAubW9kYWwtYm9keSA+IC5hbGVydC1zdWNjZXNzJykuZmFkZU91dCg1MDApO30sIDI1MDApO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICBzaG93UHVibGlzaFNldHRpbmdzOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCgnI3NpdGVTZXR0aW5nc1B1Ymxpc2hpbmcnKS5zaG93KCk7XG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgYnJvd3NlIHRoZSBGVFAgY29ubmVjdGlvblxuICAgICAgICAqL1xuICAgICAgICBicm93c2VGVFA6IGZ1bmN0aW9uKGUpIHtcblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgXHRcdC8vZ290IGFsbCB3ZSBuZWVkP1xuXG4gICAgXHRcdGlmKCAkKCcjc2l0ZVNldHRpbmdzX2Z0cFNlcnZlcicpLnZhbCgpID09PSAnJyB8fCAkKCcjc2l0ZVNldHRpbmdzX2Z0cFVzZXInKS52YWwoKSA9PT0gJycgfHwgJCgnI3NpdGVTZXR0aW5nc19mdHBQYXNzd29yZCcpLnZhbCgpID09PSAnJyApIHtcbiAgICAgICAgICAgICAgICBhbGVydCgnUGxlYXNlIG1ha2Ugc3VyZSBhbGwgRlRQIGNvbm5lY3Rpb24gZGV0YWlscyBhcmUgcHJlc2VudCcpO1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9jaGVjayBpZiB0aGlzIGlzIGEgZGVlcGVyIGxldmVsIGxpbmtcbiAgICBcdFx0aWYoICQodGhpcykuaGFzQ2xhc3MoJ2xpbmsnKSApIHtcblxuICAgIFx0XHRcdGlmKCAkKHRoaXMpLmhhc0NsYXNzKCdiYWNrJykgKSB7XG5cbiAgICBcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3NfZnRwUGF0aCcpLnZhbCggJCh0aGlzKS5hdHRyKCdocmVmJykgKTtcblxuICAgIFx0XHRcdH0gZWxzZSB7XG5cbiAgICBcdFx0XHRcdC8vaWYgc28sIHdlJ2xsIGNoYW5nZSB0aGUgcGF0aCBiZWZvcmUgY29ubmVjdGluZ1xuXG4gICAgXHRcdFx0XHRpZiggJCgnI3NpdGVTZXR0aW5nc19mdHBQYXRoJykudmFsKCkuc3Vic3RyKCAkKCcjc2l0ZVNldHRpbmdzX2Z0cFBhdGgnKS52YWwubGVuZ3RoIC0gMSApID09PSAnLycgKSB7Ly9wcmVwZW5kIFwiL1wiXG5cbiAgICBcdFx0XHRcdFx0JCgnI3NpdGVTZXR0aW5nc19mdHBQYXRoJykudmFsKCAkKCcjc2l0ZVNldHRpbmdzX2Z0cFBhdGgnKS52YWwoKSskKHRoaXMpLmF0dHIoJ2hyZWYnKSApO1xuXG4gICAgXHRcdFx0XHR9IGVsc2Uge1xuXG4gICAgXHRcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3NfZnRwUGF0aCcpLnZhbCggJCgnI3NpdGVTZXR0aW5nc19mdHBQYXRoJykudmFsKCkrXCIvXCIrJCh0aGlzKS5hdHRyKCdocmVmJykgKTtcblxuICAgIFx0XHRcdFx0fVxuXG4gICAgXHRcdFx0fVxuXG5cbiAgICBcdFx0fVxuXG4gICAgXHRcdC8vZGVzdHJveSBhbGwgYWxlcnRzXG5cbiAgICBcdFx0JCgnI2Z0cEFsZXJ0cyAuYWxlcnQnKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcbiAgICBcdFx0XHQkKHRoaXMpLnJlbW92ZSgpO1xuICAgIFx0XHR9KTtcblxuICAgIFx0XHQvL2Rpc2FibGUgYnV0dG9uXG4gICAgXHRcdCQodGhpcykuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICBcdFx0Ly9yZW1vdmUgZXhpc3RpbmcgbGlua3NcbiAgICBcdFx0JCgnI2Z0cExpc3RJdGVtcyA+IConKS5yZW1vdmUoKTtcblxuICAgIFx0XHQvL3Nob3cgZnRwIHNlY3Rpb25cbiAgICBcdFx0JCgnI2Z0cEJyb3dzZSAubG9hZGVyRnRwJykuc2hvdygpO1xuICAgIFx0XHQkKCcjZnRwQnJvd3NlJykuc2xpZGVEb3duKDUwMCk7XG5cbiAgICBcdFx0dmFyIHRoZUJ1dHRvbiA9ICQodGhpcyk7XG5cbiAgICBcdFx0JC5hamF4KHtcbiAgICAgICAgICAgICAgICB1cmw6IGFwcFVJLnNpdGVVcmwrXCJzaXRlL2Nvbm5lY3RcIixcbiAgICBcdFx0XHR0eXBlOiAncG9zdCcsXG4gICAgXHRcdFx0ZGF0YVR5cGU6ICdqc29uJyxcbiAgICBcdFx0XHRkYXRhOiAkKCdmb3JtI3NpdGVTZXR0aW5nc0Zvcm0nKS5zZXJpYWxpemVBcnJheSgpXG4gICAgXHRcdH0pLmRvbmUoZnVuY3Rpb24ocmV0KXtcblxuICAgIFx0XHRcdC8vZW5hYmxlIGJ1dHRvblxuICAgIFx0XHRcdHRoZUJ1dHRvbi5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgIFx0XHRcdC8vaGlkZSBsb2FkaW5nXG4gICAgXHRcdFx0JCgnI2Z0cEJyb3dzZSAubG9hZGVyRnRwJykuaGlkZSgpO1xuXG4gICAgXHRcdFx0aWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDAgKSB7Ly9lcnJvclxuICAgIFx0XHRcdFx0JCgnI2Z0cEFsZXJ0cycpLmFwcGVuZCggJChyZXQucmVzcG9uc2VIVE1MKSApO1xuICAgIFx0XHRcdH0gZWxzZSBpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMSApIHsvL2FsbCBnb29kXG4gICAgXHRcdFx0XHQkKCcjZnRwTGlzdEl0ZW1zJykuYXBwZW5kKCAkKHJldC5yZXNwb25zZUhUTUwpICk7XG4gICAgXHRcdFx0fVxuXG4gICAgXHRcdH0pO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgaGlkZXMvY2xvc2VzIHRoZSBGVFAgYnJvd3NlclxuICAgICAgICAqL1xuICAgICAgICBjbG9zZUZ0cEJyb3dzZXI6IGZ1bmN0aW9uKGUpIHtcblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgIFx0XHQkKHRoaXMpLmNsb3Nlc3QoJyNmdHBCcm93c2UnKS5zbGlkZVVwKDUwMCk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgICAvKlxuICAgICAgICAgICAgdGVzdHMgdGhlIEZUUCBjb25uZWN0aW9uIHdpdGggdGhlIHByb3ZpZGVkIGRldGFpbHNcbiAgICAgICAgKi9cbiAgICAgICAgdGVzdEZUUENvbm5lY3Rpb246IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2dvdCBhbGwgd2UgbmVlZD9cbiAgICBcdFx0aWYoICQoJyNzaXRlU2V0dGluZ3NfZnRwU2VydmVyJykudmFsKCkgPT09ICcnIHx8ICQoJyNzaXRlU2V0dGluZ3NfZnRwVXNlcicpLnZhbCgpID09PSAnJyB8fCAkKCcjc2l0ZVNldHRpbmdzX2Z0cFBhc3N3b3JkJykudmFsKCkgPT09ICcnICkge1xuICAgICAgICAgICAgICAgIGFsZXJ0KCdQbGVhc2UgbWFrZSBzdXJlIGFsbCBGVFAgY29ubmVjdGlvbiBkZXRhaWxzIGFyZSBwcmVzZW50Jyk7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuXG4gICAgXHRcdC8vZGVzdHJveSBhbGwgYWxlcnRzXG4gICAgICAgICAgICAkKCcjZnRwVGVzdEFsZXJ0cyAuYWxlcnQnKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICBcdFx0Ly9kaXNhYmxlIGJ1dHRvblxuICAgIFx0XHQkKHRoaXMpLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgXHRcdC8vc2hvdyBsb2FkaW5nIGluZGljYXRvclxuICAgIFx0XHQkKHRoaXMpLm5leHQoKS5mYWRlSW4oNTAwKTtcblxuICAgICAgICAgICAgdmFyIHRoZUJ1dHRvbiA9ICQodGhpcyk7XG5cbiAgICBcdFx0JC5hamF4KHtcbiAgICAgICAgICAgICAgICB1cmw6IGFwcFVJLnNpdGVVcmwrXCJzaXRlL2Z0cHRlc3RcIixcbiAgICBcdFx0XHR0eXBlOiAncG9zdCcsXG4gICAgXHRcdFx0ZGF0YVR5cGU6ICdqc29uJyxcbiAgICBcdFx0XHRkYXRhOiAkKCdmb3JtI3NpdGVTZXR0aW5nc0Zvcm0nKS5zZXJpYWxpemVBcnJheSgpXG4gICAgXHRcdH0pLmRvbmUoZnVuY3Rpb24ocmV0KXtcblxuICAgIFx0XHRcdC8vZW5hYmxlIGJ1dHRvblxuICAgIFx0XHRcdHRoZUJ1dHRvbi5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgICAgICB0aGVCdXR0b24ubmV4dCgpLmZhZGVPdXQoNTAwKTtcblxuICAgIFx0XHRcdGlmKCByZXQucmVzcG9uc2VDb2RlID09PSAwICkgey8vZXJyb3JcbiAgICAgICAgICAgICAgICAgICAgJCgnI2Z0cFRlc3RBbGVydHMnKS5hcHBlbmQoICQocmV0LnJlc3BvbnNlSFRNTCkgKTtcbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDEgKSB7Ly9hbGwgZ29vZFxuICAgICAgICAgICAgICAgICAgICAkKCcjZnRwVGVzdEFsZXJ0cycpLmFwcGVuZCggJChyZXQucmVzcG9uc2VIVE1MKSApO1xuICAgICAgICAgICAgICAgIH1cblxuICAgIFx0XHR9KTtcblxuICAgICAgICB9XG5cbiAgICB9O1xuXG4gICAgcHVibGlzaC5pbml0KCk7XG5cbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cblx0dmFyIGFwcFVJID0gcmVxdWlyZSgnLi91aS5qcycpLmFwcFVJO1xuXG5cdHZhciBzaXRlcyA9IHtcblxuICAgICAgICB3cmFwcGVyU2l0ZXM6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdzaXRlcycpLFxuICAgICAgICBzZWxlY3RVc2VyOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgndXNlckRyb3BEb3duJyksXG4gICAgICAgIHNlbGVjdFNvcnQ6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdzb3J0RHJvcERvd24nKSxcbiAgICAgICAgYnV0dG9uRGVsZXRlU2l0ZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2RlbGV0ZVNpdGVCdXR0b24nKSxcblx0XHRidXR0b25zRGVsZXRlU2l0ZTogZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLmRlbGV0ZVNpdGVCdXR0b24nKSxcblxuICAgICAgICBpbml0OiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdGhpcy5jcmVhdGVUaHVtYm5haWxzKCk7XG5cbiAgICAgICAgICAgICQodGhpcy5zZWxlY3RVc2VyKS5vbignY2hhbmdlJywgdGhpcy5maWx0ZXJVc2VyKTtcbiAgICAgICAgICAgICQodGhpcy5zZWxlY3RTb3J0KS5vbignY2hhbmdlJywgdGhpcy5jaGFuZ2VTb3J0aW5nKTtcbiAgICAgICAgICAgICQodGhpcy5idXR0b25zRGVsZXRlU2l0ZSkub24oJ2NsaWNrJywgdGhpcy5kZWxldGVTaXRlKTtcblx0XHRcdCQodGhpcy5idXR0b25EZWxldGVTaXRlKS5vbignY2xpY2snLCB0aGlzLmRlbGV0ZVNpdGUpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgYXBwbGllcyB6b29tZXIgdG8gY3JlYXRlIHRoZSBpZnJhbWUgdGh1Ym1uYWlsc1xuICAgICAgICAqL1xuICAgICAgICBjcmVhdGVUaHVtYm5haWxzOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCh0aGlzLndyYXBwZXJTaXRlcykuZmluZCgnaWZyYW1lJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgdmFyIHRoZUhlaWdodCA9ICQodGhpcykuYXR0cignZGF0YS1oZWlnaHQnKSowLjI1O1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS56b29tZXIoe1xuICAgICAgICAgICAgICAgICAgICB6b29tOiAwLjI1LFxuICAgICAgICAgICAgICAgICAgICBoZWlnaHQ6IHRoZUhlaWdodCxcbiAgICAgICAgICAgICAgICAgICAgd2lkdGg6ICQodGhpcykucGFyZW50KCkud2lkdGgoKSxcbiAgICAgICAgICAgICAgICAgICAgbWVzc2FnZTogXCJcIixcbiAgICAgICAgICAgICAgICAgICAgbWVzc2FnZVVSTDogYXBwVUkuc2l0ZVVybCtcInNpdGUvXCIrJCh0aGlzKS5hdHRyKCdkYXRhLXNpdGVpZCcpXG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJy5zaXRlJykuZmluZCgnLnpvb21lci1jb3ZlciA+IGEnKS5hdHRyKCd0YXJnZXQnLCAnJyk7XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgZmlsdGVycyB0aGUgc2l0ZSBsaXN0IGJ5IHNlbGVjdGVkIHVzZXJcbiAgICAgICAgKi9cbiAgICAgICAgZmlsdGVyVXNlcjogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIGlmKCAkKHRoaXMpLnZhbCgpID09PSAnQWxsJyB8fCAkKHRoaXMpLnZhbCgpID09PSAnJyApIHtcbiAgICAgICAgICAgICAgICAkKCcjc2l0ZXMgLnNpdGUnKS5oaWRlKCkuZmFkZUluKDUwMCk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQoJyNzaXRlcyAuc2l0ZScpLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAkKCcjc2l0ZXMnKS5maW5kKCdbZGF0YS1uYW1lPVwiJyskKHRoaXMpLnZhbCgpKydcIl0nKS5mYWRlSW4oNTAwKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGNobmFnZXMgdGhlIHNvcnRpbmcgb24gdGhlIHNpdGUgbGlzdFxuICAgICAgICAqL1xuICAgICAgICBjaGFuZ2VTb3J0aW5nOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIHNpdGVzO1xuXG4gICAgICAgICAgICBpZiggJCh0aGlzKS52YWwoKSA9PT0gJ05vT2ZQYWdlcycgKSB7XG5cblx0XHRcdFx0c2l0ZXMgPSAkKCcjc2l0ZXMgLnNpdGUnKTtcblxuXHRcdFx0XHRzaXRlcy5zb3J0KCBmdW5jdGlvbihhLGIpe1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciBhbiA9IGEuZ2V0QXR0cmlidXRlKCdkYXRhLXBhZ2VzJyk7XG5cdFx0XHRcdFx0dmFyIGJuID0gYi5nZXRBdHRyaWJ1dGUoJ2RhdGEtcGFnZXMnKTtcblxuXHRcdFx0XHRcdGlmKGFuID4gYm4pIHtcblx0XHRcdFx0XHRcdHJldHVybiAxO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdGlmKGFuIDwgYm4pIHtcblx0XHRcdFx0XHRcdHJldHVybiAtMTtcblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHRyZXR1cm4gMDtcblxuXHRcdFx0XHR9ICk7XG5cblx0XHRcdFx0c2l0ZXMuZGV0YWNoKCkuYXBwZW5kVG8oICQoJyNzaXRlcycpICk7XG5cblx0XHRcdH0gZWxzZSBpZiggJCh0aGlzKS52YWwoKSA9PT0gJ0NyZWF0aW9uRGF0ZScgKSB7XG5cblx0XHRcdFx0c2l0ZXMgPSAkKCcjc2l0ZXMgLnNpdGUnKTtcblxuXHRcdFx0XHRzaXRlcy5zb3J0KCBmdW5jdGlvbihhLGIpe1xuXG5cdFx0XHRcdFx0dmFyIGFuID0gYS5nZXRBdHRyaWJ1dGUoJ2RhdGEtY3JlYXRlZCcpLnJlcGxhY2UoXCItXCIsIFwiXCIpO1xuXHRcdFx0XHRcdHZhciBibiA9IGIuZ2V0QXR0cmlidXRlKCdkYXRhLWNyZWF0ZWQnKS5yZXBsYWNlKFwiLVwiLCBcIlwiKTtcblxuXHRcdFx0XHRcdGlmKGFuID4gYm4pIHtcblx0XHRcdFx0XHRcdHJldHVybiAxO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdGlmKGFuIDwgYm4pIHtcblx0XHRcdFx0XHRcdHJldHVybiAtMTtcblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHRyZXR1cm4gMDtcblxuXHRcdFx0XHR9ICk7XG5cblx0XHRcdFx0c2l0ZXMuZGV0YWNoKCkuYXBwZW5kVG8oICQoJyNzaXRlcycpICk7XG5cblx0XHRcdH0gZWxzZSBpZiggJCh0aGlzKS52YWwoKSA9PT0gJ0xhc3RVcGRhdGUnICkge1xuXG5cdFx0XHRcdHNpdGVzID0gJCgnI3NpdGVzIC5zaXRlJyk7XG5cblx0XHRcdFx0c2l0ZXMuc29ydCggZnVuY3Rpb24oYSxiKXtcblxuXHRcdFx0XHRcdHZhciBhbiA9IGEuZ2V0QXR0cmlidXRlKCdkYXRhLXVwZGF0ZScpLnJlcGxhY2UoXCItXCIsIFwiXCIpO1xuXHRcdFx0XHRcdHZhciBibiA9IGIuZ2V0QXR0cmlidXRlKCdkYXRhLXVwZGF0ZScpLnJlcGxhY2UoXCItXCIsIFwiXCIpO1xuXG5cdFx0XHRcdFx0aWYoYW4gPiBibikge1xuXHRcdFx0XHRcdFx0cmV0dXJuIDE7XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0aWYoYW4gPCBibikge1xuXHRcdFx0XHRcdFx0cmV0dXJuIC0xO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRyZXR1cm4gMDtcblxuXHRcdFx0XHR9ICk7XG5cblx0XHRcdFx0c2l0ZXMuZGV0YWNoKCkuYXBwZW5kVG8oICQoJyNzaXRlcycpICk7XG5cblx0XHRcdH1cblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGRlbGV0ZXMgYSBzaXRlXG4gICAgICAgICovXG4gICAgICAgIGRlbGV0ZVNpdGU6IGZ1bmN0aW9uKGUpIHtcblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICAkKCcjZGVsZXRlU2l0ZU1vZGFsIC5tb2RhbC1jb250ZW50IHAnKS5zaG93KCk7XG5cbiAgICAgICAgICAgIC8vcmVtb3ZlIG9sZCBhbGVydHNcbiAgICAgICAgICAgICQoJyNkZWxldGVTaXRlTW9kYWwgLm1vZGFsLWFsZXJ0cyA+IConKS5yZW1vdmUoKTtcbiAgICAgICAgICAgICQoJyNkZWxldGVTaXRlTW9kYWwgLmxvYWRlcicpLmhpZGUoKTtcblxuICAgICAgICAgICAgdmFyIHRvRGVsID0gJCh0aGlzKS5jbG9zZXN0KCcuc2l0ZScpO1xuICAgICAgICAgICAgdmFyIGRlbEJ1dHRvbiA9ICQodGhpcyk7XG5cbiAgICAgICAgICAgICQoJyNkZWxldGVTaXRlTW9kYWwgYnV0dG9uI2RlbGV0ZVNpdGVCdXR0b24nKS5zaG93KCk7XG4gICAgICAgICAgICAkKCcjZGVsZXRlU2l0ZU1vZGFsJykubW9kYWwoJ3Nob3cnKTtcblxuICAgICAgICAgICAgJCgnI2RlbGV0ZVNpdGVNb2RhbCBidXR0b24jZGVsZXRlU2l0ZUJ1dHRvbicpLnVuYmluZCgnY2xpY2snKS5jbGljayhmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgICAgICAkKCcjZGVsZXRlU2l0ZU1vZGFsIC5sb2FkZXInKS5mYWRlSW4oNTAwKTtcblxuICAgICAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgICAgIHVybDogYXBwVUkuc2l0ZVVybCtcInNpdGUvdHJhc2gvXCIrZGVsQnV0dG9uLmF0dHIoJ2RhdGEtc2l0ZWlkJyksXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6ICdnZXQnLFxuICAgICAgICAgICAgICAgICAgICBkYXRhVHlwZTogJ2pzb24nXG4gICAgICAgICAgICAgICAgfSkuZG9uZShmdW5jdGlvbihyZXQpe1xuXG4gICAgICAgICAgICAgICAgICAgICQoJyNkZWxldGVTaXRlTW9kYWwgLmxvYWRlcicpLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgJCgnI2RlbGV0ZVNpdGVNb2RhbCBidXR0b24jZGVsZXRlU2l0ZUJ1dHRvbicpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCByZXQucmVzcG9uc2VDb2RlID09PSAwICkgey8vZXJyb3JcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2RlbGV0ZVNpdGVNb2RhbCAubW9kYWwtY29udGVudCBwJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2RlbGV0ZVNpdGVNb2RhbCAubW9kYWwtYWxlcnRzJykuYXBwZW5kKCAkKHJldC5yZXNwb25zZUhUTUwpICk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmKCByZXQucmVzcG9uc2VDb2RlID09PSAxICkgey8vYWxsIGdvb2RcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2RlbGV0ZVNpdGVNb2RhbCAubW9kYWwtY29udGVudCBwJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2RlbGV0ZVNpdGVNb2RhbCAubW9kYWwtYWxlcnRzJykuYXBwZW5kKCAkKHJldC5yZXNwb25zZUhUTUwpICk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjZGVsZXRlU2l0ZU1vZGFsIGJ1dHRvbiNkZWxldGVTaXRlQnV0dG9uJykuaGlkZSgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB0b0RlbC5mYWRlT3V0KDgwMCwgZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfVxuXG4gICAgfTtcblxuICAgIHNpdGVzLmluaXQoKTtcblxufSgpKTsiLCIoZnVuY3Rpb24gKCkge1xuXHRcInVzZSBzdHJpY3RcIjtcblxuXHR2YXIgYXBwVUkgPSByZXF1aXJlKCcuL3VpLmpzJykuYXBwVUk7XG5cblx0dmFyIHNpdGVTZXR0aW5ncyA9IHtcblxuICAgICAgICAvL2J1dHRvblNpdGVTZXR0aW5nczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NpdGVTZXR0aW5nc0J1dHRvbicpLFxuXHRcdGJ1dHRvblNpdGVTZXR0aW5nczI6ICQoJy5zaXRlU2V0dGluZ3NNb2RhbEJ1dHRvbicpLFxuICAgICAgICBidXR0b25TYXZlU2l0ZVNldHRpbmdzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2F2ZVNpdGVTZXR0aW5nc0J1dHRvbicpLFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvLyQodGhpcy5idXR0b25TaXRlU2V0dGluZ3MpLm9uKCdjbGljaycsIHRoaXMuc2l0ZVNldHRpbmdzTW9kYWwpO1xuXHRcdFx0dGhpcy5idXR0b25TaXRlU2V0dGluZ3MyLm9uKCdjbGljaycsIHRoaXMuc2l0ZVNldHRpbmdzTW9kYWwpO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblNhdmVTaXRlU2V0dGluZ3MpLm9uKCdjbGljaycsIHRoaXMuc2F2ZVNpdGVTZXR0aW5ncyk7XG5cbiAgICAgICAgfSxcblxuICAgICAgICAvKlxuICAgICAgICAgICAgbG9hZHMgdGhlIHNpdGUgc2V0dGluZ3MgZGF0YVxuICAgICAgICAqL1xuICAgICAgICBzaXRlU2V0dGluZ3NNb2RhbDogZnVuY3Rpb24oZSkge1xuXG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICBcdFx0JCgnI3NpdGVTZXR0aW5ncycpLm1vZGFsKCdzaG93Jyk7XG5cbiAgICBcdFx0Ly9kZXN0cm95IGFsbCBhbGVydHNcbiAgICBcdFx0JCgnI3NpdGVTZXR0aW5ncyAuYWxlcnQnKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcblxuICAgIFx0XHRcdCQodGhpcykucmVtb3ZlKCk7XG5cbiAgICBcdFx0fSk7XG5cbiAgICBcdFx0Ly9zZXQgdGhlIHNpdGVJRFxuICAgIFx0XHQkKCdpbnB1dCNzaXRlSUQnKS52YWwoICQodGhpcykuYXR0cignZGF0YS1zaXRlaWQnKSApO1xuXG4gICAgXHRcdC8vZGVzdHJveSBjdXJyZW50IGZvcm1zXG4gICAgXHRcdCQoJyNzaXRlU2V0dGluZ3MgLm1vZGFsLWJvZHktY29udGVudCA+IConKS5lYWNoKGZ1bmN0aW9uKCl7XG4gICAgXHRcdFx0JCh0aGlzKS5yZW1vdmUoKTtcbiAgICBcdFx0fSk7XG5cbiAgICAgICAgICAgIC8vc2hvdyBsb2FkZXIsIGhpZGUgcmVzdFxuICAgIFx0XHQkKCcjc2l0ZVNldHRpbmdzV3JhcHBlciAubG9hZGVyJykuc2hvdygpO1xuICAgIFx0XHQkKCcjc2l0ZVNldHRpbmdzV3JhcHBlciA+ICo6bm90KC5sb2FkZXIpJykuaGlkZSgpO1xuXG4gICAgXHRcdC8vbG9hZCBzaXRlIGRhdGEgdXNpbmcgYWpheFxuICAgIFx0XHQkLmFqYXgoe1xuICAgICAgICAgICAgICAgIHVybDogYXBwVUkuc2l0ZVVybCtcInNpdGVBamF4L1wiKyQodGhpcykuYXR0cignZGF0YS1zaXRlaWQnKSxcbiAgICBcdFx0XHR0eXBlOiAnZ2V0JyxcbiAgICBcdFx0XHRkYXRhVHlwZTogJ2pzb24nXG4gICAgXHRcdH0pLmRvbmUoZnVuY3Rpb24ocmV0KXtcblxuICAgIFx0XHRcdGlmKCByZXQucmVzcG9uc2VDb2RlID09PSAwICkgey8vZXJyb3JcblxuICAgIFx0XHRcdFx0Ly9oaWRlIGxvYWRlciwgc2hvdyBlcnJvciBtZXNzYWdlXG4gICAgXHRcdFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5sb2FkZXInKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcblxuICAgIFx0XHRcdFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5tb2RhbC1hbGVydHMnKS5hcHBlbmQoICQocmV0LnJlc3BvbnNlSFRNTCkgKTtcblxuICAgIFx0XHRcdFx0fSk7XG5cbiAgICBcdFx0XHRcdC8vZGlzYWJsZSBzdWJtaXQgYnV0dG9uXG4gICAgXHRcdFx0XHQkKCcjc2F2ZVNpdGVTZXR0aW5nc0J1dHRvbicpLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuXG5cbiAgICBcdFx0XHR9IGVsc2UgaWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDEgKSB7Ly9hbGwgd2VsbCA6KVxuXG4gICAgXHRcdFx0XHQvL2hpZGUgbG9hZGVyLCBzaG93IGRhdGFcblxuICAgIFx0XHRcdFx0JCgnI3NpdGVTZXR0aW5ncyAubG9hZGVyJykuZmFkZU91dCg1MDAsIGZ1bmN0aW9uKCl7XG5cbiAgICBcdFx0XHRcdFx0JCgnI3NpdGVTZXR0aW5ncyAubW9kYWwtYm9keS1jb250ZW50JykuYXBwZW5kKCAkKHJldC5yZXNwb25zZUhUTUwpICk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQoJ2JvZHknKS50cmlnZ2VyKCdzaXRlU2V0dGluZ3NMb2FkJyk7XG5cbiAgICBcdFx0XHRcdH0pO1xuXG4gICAgXHRcdFx0XHQvL2VuYWJsZSBzdWJtaXQgYnV0dG9uXG4gICAgXHRcdFx0XHQkKCcjc2F2ZVNpdGVTZXR0aW5nc0J1dHRvbicpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgXHRcdFx0fVxuXG4gICAgXHRcdH0pO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgc2F2ZXMgdGhlIHNpdGUgc2V0dGluZ3NcbiAgICAgICAgKi9cbiAgICAgICAgc2F2ZVNpdGVTZXR0aW5nczogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIC8vZGVzdHJveSBhbGwgYWxlcnRzXG4gICAgXHRcdCQoJyNzaXRlU2V0dGluZ3MgLmFsZXJ0JykuZmFkZU91dCg1MDAsIGZ1bmN0aW9uKCl7XG5cbiAgICBcdFx0XHQkKHRoaXMpLnJlbW92ZSgpO1xuXG4gICAgXHRcdH0pO1xuXG4gICAgXHRcdC8vZGlzYWJsZSBidXR0b25cbiAgICBcdFx0JCgnI3NhdmVTaXRlU2V0dGluZ3NCdXR0b24nKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgIFx0XHQvL2hpZGUgZm9ybSBkYXRhXG4gICAgXHRcdCQoJyNzaXRlU2V0dGluZ3MgLm1vZGFsLWJvZHktY29udGVudCA+IConKS5oaWRlKCk7XG5cbiAgICBcdFx0Ly9zaG93IGxvYWRlclxuICAgIFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5sb2FkZXInKS5zaG93KCk7XG5cbiAgICBcdFx0JC5hamF4KHtcbiAgICAgICAgICAgICAgICB1cmw6IGFwcFVJLnNpdGVVcmwrXCJzaXRlQWpheFVwZGF0ZVwiLFxuICAgIFx0XHRcdHR5cGU6ICdwb3N0JyxcbiAgICBcdFx0XHRkYXRhVHlwZTogJ2pzb24nLFxuICAgIFx0XHRcdGRhdGE6ICQoJ2Zvcm0jc2l0ZVNldHRpbmdzRm9ybScpLnNlcmlhbGl6ZUFycmF5KClcbiAgICBcdFx0fSkuZG9uZShmdW5jdGlvbihyZXQpe1xuXG4gICAgXHRcdFx0aWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDAgKSB7Ly9lcnJvclxuXG4gICAgXHRcdFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5sb2FkZXInKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcblxuICAgIFx0XHRcdFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5tb2RhbC1hbGVydHMnKS5hcHBlbmQoIHJldC5yZXNwb25zZUhUTUwgKTtcblxuICAgIFx0XHRcdFx0XHQvL3Nob3cgZm9ybSBkYXRhXG4gICAgXHRcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3MgLm1vZGFsLWJvZHktY29udGVudCA+IConKS5zaG93KCk7XG5cbiAgICBcdFx0XHRcdFx0Ly9lbmFibGUgYnV0dG9uXG4gICAgXHRcdFx0XHRcdCQoJyNzYXZlU2l0ZVNldHRpbmdzQnV0dG9uJykucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICBcdFx0XHRcdH0pO1xuXG5cbiAgICBcdFx0XHR9IGVsc2UgaWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDEgKSB7Ly9hbGwgaXMgd2VsbFxuXG4gICAgXHRcdFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5sb2FkZXInKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcblxuXG4gICAgXHRcdFx0XHRcdC8vdXBkYXRlIHNpdGUgbmFtZSBpbiB0b3AgbWVudVxuICAgIFx0XHRcdFx0XHQkKCcjc2l0ZVRpdGxlJykudGV4dCggcmV0LnNpdGVOYW1lICk7XG5cbiAgICBcdFx0XHRcdFx0JCgnI3NpdGVTZXR0aW5ncyAubW9kYWwtYWxlcnRzJykuYXBwZW5kKCByZXQucmVzcG9uc2VIVE1MICk7XG5cbiAgICBcdFx0XHRcdFx0Ly9oaWRlIGZvcm0gZGF0YVxuICAgIFx0XHRcdFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5tb2RhbC1ib2R5LWNvbnRlbnQgPiAqJykucmVtb3ZlKCk7XG4gICAgXHRcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3MgLm1vZGFsLWJvZHktY29udGVudCcpLmFwcGVuZCggcmV0LnJlc3BvbnNlSFRNTDIgKTtcblxuICAgIFx0XHRcdFx0XHQvL2VuYWJsZSBidXR0b25cbiAgICBcdFx0XHRcdFx0JCgnI3NhdmVTaXRlU2V0dGluZ3NCdXR0b24nKS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgIFx0XHRcdFx0XHQvL2lzIHRoZSBGVFAgc3R1ZmYgYWxsIGdvb2Q/XG5cbiAgICBcdFx0XHRcdFx0aWYoIHJldC5mdHBPayA9PT0gMSApIHsvL3llcywgYWxsIGdvb2RcblxuICAgIFx0XHRcdFx0XHRcdCQoJyNwdWJsaXNoUGFnZScpLnJlbW92ZUF0dHIoJ2RhdGEtdG9nZ2xlJyk7XG4gICAgXHRcdFx0XHRcdFx0JCgnI3B1Ymxpc2hQYWdlIHNwYW4udGV4dC1kYW5nZXInKS5oaWRlKCk7XG5cbiAgICBcdFx0XHRcdFx0XHQkKCcjcHVibGlzaFBhZ2UnKS50b29sdGlwKCdkZXN0cm95Jyk7XG5cbiAgICBcdFx0XHRcdFx0fSBlbHNlIHsvL25vcGUsIGNhbid0IHVzZSBGVFBcblxuICAgIFx0XHRcdFx0XHRcdCQoJyNwdWJsaXNoUGFnZScpLmF0dHIoJ2RhdGEtdG9nZ2xlJywgJ3Rvb2x0aXAnKTtcbiAgICBcdFx0XHRcdFx0XHQkKCcjcHVibGlzaFBhZ2Ugc3Bhbi50ZXh0LWRhbmdlcicpLnNob3coKTtcblxuICAgIFx0XHRcdFx0XHRcdCQoJyNwdWJsaXNoUGFnZScpLnRvb2x0aXAoJ3Nob3cnKTtcblxuICAgIFx0XHRcdFx0XHR9XG5cblxuICAgIFx0XHRcdFx0XHQvL3VwZGF0ZSB0aGUgc2l0ZSBuYW1lIGluIHRoZSBzbWFsbCB3aW5kb3dcbiAgICBcdFx0XHRcdFx0JCgnI3NpdGVfJytyZXQuc2l0ZUlEKycgLndpbmRvdyAudG9wIGInKS50ZXh0KCByZXQuc2l0ZU5hbWUgKTtcblxuICAgIFx0XHRcdFx0fSk7XG5cblxuICAgIFx0XHRcdH1cblxuICAgIFx0XHR9KTtcblxuICAgICAgICB9LFxuXG5cbiAgICB9O1xuXG4gICAgc2l0ZVNldHRpbmdzLmluaXQoKTtcblxufSgpKTsiLCIoZnVuY3Rpb24gKCkge1xuXG4vKiBnbG9iYWxzIHNpdGVVcmw6ZmFsc2UsIGJhc2VVcmw6ZmFsc2UgKi9cbiAgICBcInVzZSBzdHJpY3RcIjtcbiAgICAgICAgXG4gICAgdmFyIGFwcFVJID0ge1xuICAgICAgICBcbiAgICAgICAgZmlyc3RNZW51V2lkdGg6IDE5MCxcbiAgICAgICAgc2Vjb25kTWVudVdpZHRoOiAzMDAsXG4gICAgICAgIGxvYWRlckFuaW1hdGlvbjogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2xvYWRlcicpLFxuICAgICAgICBzZWNvbmRNZW51VHJpZ2dlckNvbnRhaW5lcnM6ICQoJyNtZW51ICNtYWluICNlbGVtZW50Q2F0cywgI21lbnUgI21haW4gI3RlbXBsYXRlc1VsJyksXG4gICAgICAgIHNpdGVVcmw6IHNpdGVVcmwsXG4gICAgICAgIGJhc2VVcmw6IGJhc2VVcmwsXG4gICAgICAgIFxuICAgICAgICBzZXR1cDogZnVuY3Rpb24oKXtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgLy8gRmFkZSB0aGUgbG9hZGVyIGFuaW1hdGlvblxuICAgICAgICAgICAgJChhcHBVSS5sb2FkZXJBbmltYXRpb24pLmZhZGVPdXQoZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAkKCcjbWVudScpLmFuaW1hdGUoeydsZWZ0JzogLWFwcFVJLmZpcnN0TWVudVdpZHRofSwgMTAwMCk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgLy8gVGFic1xuICAgICAgICAgICAgJChcIi5uYXYtdGFicyBhXCIpLm9uKCdjbGljaycsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgICQodGhpcykudGFiKFwic2hvd1wiKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAkKFwic2VsZWN0LnNlbGVjdFwiKS5zZWxlY3QyKCk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgICQoJzpyYWRpbywgOmNoZWNrYm94JykucmFkaW9jaGVjaygpO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBUb29sdGlwc1xuICAgICAgICAgICAgJChcIltkYXRhLXRvZ2dsZT10b29sdGlwXVwiKS50b29sdGlwKFwiaGlkZVwiKTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgLy8gVGFibGU6IFRvZ2dsZSBhbGwgY2hlY2tib3hlc1xuICAgICAgICAgICAgJCgnLnRhYmxlIC50b2dnbGUtYWxsIDpjaGVja2JveCcpLm9uKCdjbGljaycsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgICAgICAgICAgIHZhciBjaCA9ICR0aGlzLnByb3AoJ2NoZWNrZWQnKTtcbiAgICAgICAgICAgICAgICAkdGhpcy5jbG9zZXN0KCcudGFibGUnKS5maW5kKCd0Ym9keSA6Y2hlY2tib3gnKS5yYWRpb2NoZWNrKCFjaCA/ICd1bmNoZWNrJyA6ICdjaGVjaycpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgIC8vIEFkZCBzdHlsZSBjbGFzcyBuYW1lIHRvIGEgdG9vbHRpcHNcbiAgICAgICAgICAgICQoXCIudG9vbHRpcFwiKS5hZGRDbGFzcyhmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBpZiAoJCh0aGlzKS5wcmV2KCkuYXR0cihcImRhdGEtdG9vbHRpcC1zdHlsZVwiKSkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gXCJ0b29sdGlwLVwiICsgJCh0aGlzKS5wcmV2KCkuYXR0cihcImRhdGEtdG9vbHRpcC1zdHlsZVwiKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgJChcIi5idG4tZ3JvdXBcIikub24oJ2NsaWNrJywgXCJhXCIsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICQodGhpcykuc2libGluZ3MoKS5yZW1vdmVDbGFzcyhcImFjdGl2ZVwiKS5lbmQoKS5hZGRDbGFzcyhcImFjdGl2ZVwiKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBGb2N1cyBzdGF0ZSBmb3IgYXBwZW5kL3ByZXBlbmQgaW5wdXRzXG4gICAgICAgICAgICAkKCcuaW5wdXQtZ3JvdXAnKS5vbignZm9jdXMnLCAnLmZvcm0tY29udHJvbCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJy5pbnB1dC1ncm91cCwgLmZvcm0tZ3JvdXAnKS5hZGRDbGFzcygnZm9jdXMnKTtcbiAgICAgICAgICAgIH0pLm9uKCdibHVyJywgJy5mb3JtLWNvbnRyb2wnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5jbG9zZXN0KCcuaW5wdXQtZ3JvdXAsIC5mb3JtLWdyb3VwJykucmVtb3ZlQ2xhc3MoJ2ZvY3VzJyk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgLy8gVGFibGU6IFRvZ2dsZSBhbGwgY2hlY2tib3hlc1xuICAgICAgICAgICAgJCgnLnRhYmxlIC50b2dnbGUtYWxsJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgdmFyIGNoID0gJCh0aGlzKS5maW5kKCc6Y2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJyk7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5jbG9zZXN0KCcudGFibGUnKS5maW5kKCd0Ym9keSA6Y2hlY2tib3gnKS5jaGVja2JveCghY2ggPyAnY2hlY2snIDogJ3VuY2hlY2snKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBUYWJsZTogQWRkIGNsYXNzIHJvdyBzZWxlY3RlZFxuICAgICAgICAgICAgJCgnLnRhYmxlIHRib2R5IDpjaGVja2JveCcpLm9uKCdjaGVjayB1bmNoZWNrIHRvZ2dsZScsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKVxuICAgICAgICAgICAgICAgICwgY2hlY2sgPSAkdGhpcy5wcm9wKCdjaGVja2VkJylcbiAgICAgICAgICAgICAgICAsIHRvZ2dsZSA9IGUudHlwZSA9PT0gJ3RvZ2dsZSdcbiAgICAgICAgICAgICAgICAsIGNoZWNrYm94ZXMgPSAkKCcudGFibGUgdGJvZHkgOmNoZWNrYm94JylcbiAgICAgICAgICAgICAgICAsIGNoZWNrQWxsID0gY2hlY2tib3hlcy5sZW5ndGggPT09IGNoZWNrYm94ZXMuZmlsdGVyKCc6Y2hlY2tlZCcpLmxlbmd0aDtcblxuICAgICAgICAgICAgICAgICR0aGlzLmNsb3Nlc3QoJ3RyJylbY2hlY2sgPyAnYWRkQ2xhc3MnIDogJ3JlbW92ZUNsYXNzJ10oJ3NlbGVjdGVkLXJvdycpO1xuICAgICAgICAgICAgICAgIGlmICh0b2dnbGUpICR0aGlzLmNsb3Nlc3QoJy50YWJsZScpLmZpbmQoJy50b2dnbGUtYWxsIDpjaGVja2JveCcpLmNoZWNrYm94KGNoZWNrQWxsID8gJ2NoZWNrJyA6ICd1bmNoZWNrJyk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgLy8gU3dpdGNoXG4gICAgICAgICAgICAkKFwiW2RhdGEtdG9nZ2xlPSdzd2l0Y2gnXVwiKS53cmFwKCc8ZGl2IGNsYXNzPVwic3dpdGNoXCIgLz4nKS5wYXJlbnQoKS5ib290c3RyYXBTd2l0Y2goKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIFxuICAgICAgICAgICAgYXBwVUkuc2Vjb25kTWVudVRyaWdnZXJDb250YWluZXJzLm9uKCdjbGljaycsICdhOm5vdCguYnRuKScsIGFwcFVJLnNlY29uZE1lbnVBbmltYXRpb24pO1xuICAgICAgICAgICAgICAgICAgICAgICAgXG4gICAgICAgIH0sXG4gICAgICAgIFxuICAgICAgICBzZWNvbmRNZW51QW5pbWF0aW9uOiBmdW5jdGlvbigpe1xuICAgICAgICBcbiAgICAgICAgICAgICQoJyNtZW51ICNtYWluIGEnKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XG4gICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdhY3RpdmUnKTtcblx0XG4gICAgICAgICAgICAvL3Nob3cgb25seSB0aGUgcmlnaHQgZWxlbWVudHNcbiAgICAgICAgICAgICQoJyNtZW51ICNzZWNvbmQgdWwgbGknKS5oaWRlKCk7XG4gICAgICAgICAgICAkKCcjbWVudSAjc2Vjb25kIHVsIGxpLicrJCh0aGlzKS5hdHRyKCdpZCcpKS5zaG93KCk7XG5cbiAgICAgICAgICAgIGlmKCAkKHRoaXMpLmF0dHIoJ2lkJykgPT09ICdhbGwnICkge1xuICAgICAgICAgICAgICAgICQoJyNtZW51ICNzZWNvbmQgdWwjZWxlbWVudHMgbGknKS5zaG93KCk7XHRcdFxuICAgICAgICAgICAgfVxuXHRcbiAgICAgICAgICAgICQoJy5tZW51IC5zZWNvbmQnKS5jc3MoJ2Rpc3BsYXknLCAnYmxvY2snKS5zdG9wKCkuYW5pbWF0ZSh7XG4gICAgICAgICAgICAgICAgd2lkdGg6IGFwcFVJLnNlY29uZE1lbnVXaWR0aFxuICAgICAgICAgICAgfSwgNTAwKTtcdFxuICAgICAgICAgICAgICAgIFxuICAgICAgICB9XG4gICAgICAgIFxuICAgIH07XG4gICAgXG4gICAgLy9pbml0aWF0ZSB0aGUgVUlcbiAgICBhcHBVSS5zZXR1cCgpO1xuXG5cbiAgICAvLyoqKiogRVhQT1JUU1xuICAgIG1vZHVsZS5leHBvcnRzLmFwcFVJID0gYXBwVUk7XG4gICAgXG59KCkpOyIsIihmdW5jdGlvbiAoKSB7XG4gICAgXCJ1c2Ugc3RyaWN0XCI7XG4gICAgXG4gICAgZXhwb3J0cy5nZXRSYW5kb21BcmJpdHJhcnkgPSBmdW5jdGlvbihtaW4sIG1heCkge1xuICAgICAgICByZXR1cm4gTWF0aC5mbG9vcihNYXRoLnJhbmRvbSgpICogKG1heCAtIG1pbikgKyBtaW4pO1xuICAgIH07XG5cbiAgICBleHBvcnRzLmdldFBhcmFtZXRlckJ5TmFtZSA9IGZ1bmN0aW9uIChuYW1lLCB1cmwpIHtcblxuICAgICAgICBpZiAoIXVybCkgdXJsID0gd2luZG93LmxvY2F0aW9uLmhyZWY7XG4gICAgICAgIG5hbWUgPSBuYW1lLnJlcGxhY2UoL1tcXFtcXF1dL2csIFwiXFxcXCQmXCIpO1xuICAgICAgICB2YXIgcmVnZXggPSBuZXcgUmVnRXhwKFwiWz8mXVwiICsgbmFtZSArIFwiKD0oW14mI10qKXwmfCN8JClcIiksXG4gICAgICAgICAgICByZXN1bHRzID0gcmVnZXguZXhlYyh1cmwpO1xuICAgICAgICBpZiAoIXJlc3VsdHMpIHJldHVybiBudWxsO1xuICAgICAgICBpZiAoIXJlc3VsdHNbMl0pIHJldHVybiAnJztcbiAgICAgICAgcmV0dXJuIGRlY29kZVVSSUNvbXBvbmVudChyZXN1bHRzWzJdLnJlcGxhY2UoL1xcKy9nLCBcIiBcIikpO1xuICAgICAgICBcbiAgICB9O1xuICAgIFxufSgpKTsiLCIoZnVuY3Rpb24gKCkge1xuXHRcInVzZSBzdHJpY3RcIjtcblxuXHRyZXF1aXJlKCcuL21vZHVsZXMvdWkuanMnKTtcblx0cmVxdWlyZSgnLi9tb2R1bGVzL2FjY291bnQuanMnKTtcblx0cmVxdWlyZSgnLi9tb2R1bGVzL3NpdGVzLmpzJyk7XG5cdHJlcXVpcmUoJy4vbW9kdWxlcy9zaXRlc2V0dGluZ3MuanMnKTtcblx0cmVxdWlyZSgnLi9tb2R1bGVzL3B1Ymxpc2hpbmcuanMnKTtcblxufSgpKTsiXX0=
