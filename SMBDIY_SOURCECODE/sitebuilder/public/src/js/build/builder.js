(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function () {
	"use strict";

	require('./modules/ui.js');
	require('./modules/builder.js');
	require('./modules/config.js');
	require('./modules/utils.js');
	require('./modules/canvasElement.js');
	require('./modules/styleeditor.js');
	require('./modules/imageLibrary.js');
	require('./modules/content.js');
	require('./modules/sitesettings.js');
	require('./modules/publishing.js');
	require('./modules/export.js');
	require('./modules/preview.js');
	require('./modules/revisions.js');
	require('./modules/templates.js');

}());
},{"./modules/builder.js":2,"./modules/canvasElement.js":3,"./modules/config.js":4,"./modules/content.js":5,"./modules/export.js":6,"./modules/imageLibrary.js":7,"./modules/preview.js":8,"./modules/publishing.js":9,"./modules/revisions.js":10,"./modules/sitesettings.js":11,"./modules/styleeditor.js":12,"./modules/templates.js":13,"./modules/ui.js":14,"./modules/utils.js":15}],2:[function(require,module,exports){
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
},{"./config.js":4,"./ui.js":14,"./utils.js":15}],3:[function(require,module,exports){
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
},{"./builder.js":2}],4:[function(require,module,exports){
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
},{}],5:[function(require,module,exports){
(function () {
	"use strict";

	var canvasElement = require('./canvasElement.js').Element;
	var bConfig = require('./config');
	var siteBuilder = require('./builder.js');

	var contenteditor = {

        labelContentMode: document.getElementById('modeContentLabel'),
        radioContent: document.getElementById('modeContent'),
        buttonUpdateContent: document.getElementById('updateContentInFrameSubmit'),
        activeElement: {},
        allContentItemsOnCanvas: [],
        modalEditContent: document.getElementById('editContentModal'),

        init: function() {

            //display content mode label
            $(this.labelContentMode).show();

            $(this.radioContent).on('click', this.activateContentMode);
            $(this.buttonUpdateContent).on('click', this.updateElementContent);
            $(this.modalEditContent).on('hidden.bs.modal', this.editContentModalCloseEvent);
            $(document).on('modeDetails modeBlocks', 'body', this.deActivateMode);

			//listen for the beforeSave event, removes outlines before saving
            $('body').on('beforeSave', function () {

				if( Object.keys( contenteditor.activeElement ).length > 0 ) {
                	contenteditor.activeElement.removeOutline();
            	}

			});

        },


        /*
            Activates content mode
        */
        activateContentMode: function() {

            //Element object extention
            canvasElement.prototype.clickHandler = function(el) {
                contenteditor.contentClick(el);
            };

            //trigger custom event
            $('body').trigger('modeContent');

            //disable frameCovers
            for( var i = 0; i < siteBuilder.site.sitePages.length; i++ ) {
                siteBuilder.site.sitePages[i].toggleFrameCovers('Off');
            }

            //create an object for every editable element on the canvas and setup it's events
            $('#pageList ul li iframe').each(function(){

                for( var key in bConfig.editableContent ) {

                    $(this).contents().find( bConfig.pageContainer + ' '+ bConfig.editableContent[key] ).each(function(){

                        var newElement = new canvasElement(this);

                        newElement.activate();

                        //store in array
                        contenteditor.allContentItemsOnCanvas.push( newElement );

                    });

                }

            });

        },


        /*
            Opens up the content editor
        */
        contentClick: function(el) {

            //if we have an active element, make it unactive
            if( Object.keys(this.activeElement).length !== 0) {
                this.activeElement.activate();
            }

            //set the active element
            var activeElement = new canvasElement(el);
            activeElement.setParentBlock();
            contenteditor.activeElement = activeElement;

            //unbind hover and click events and make this item active
            contenteditor.activeElement.setOpen();

            $('#editContentModal').modal('show');

            //for the elements below, we'll use a simplyfied editor, only direct text can be done through this one
            if( el.tagName === 'SMALL' || el.tagName === 'A' || el.tagName === 'LI' || el.tagName === 'SPAN' || el.tagName === 'B' || el.tagName === 'I' || el.tagName === 'TT' || el.tageName === 'CODE' || el.tagName === 'EM' || el.tagName === 'STRONG' || el.tagName === 'SUB' || el.tagName === 'BUTTON' || el.tagName === 'LABEL' || el.tagName === 'P' || el.tagName === 'H1' || el.tagName === 'H2' || el.tagName === 'H2' || el.tagName === 'H3' || el.tagName === 'H4' || el.tagName === 'H5' || el.tagName === 'H6' ) {

				$('#contentToEdit').summernote({
					toolbar: [
					// [groupName, [list of button]]
					['codeview', ['codeview']],
					['fontstyle', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
					['help', ['undo', 'redo']]
				  ]
				});

            } else if( el.tagName === 'DIV' && $(el).hasClass('tableWrapper') ) {

				$('#contentToEdit').summernote({
					toolbar: [
					['codeview', ['codeview']],
					['styleselect', ['style']],
					['fontstyle', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
					['table', ['table']],
					['link', ['link', 'unlink']],
					['help', ['undo', 'redo']]
				  ]
				});

            } else {

				$('#contentToEdit').summernote({
					toolbar: [
					['codeview', ['codeview']],
					['styleselect', ['style']],
					['fontstyle', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
					['lists', ['ol', 'ul']],
					['link', ['link', 'unlink']],
					['help', ['undo', 'redo']]
				  ]
				});

            }

			$('#contentToEdit').summernote('code', $(el).html());

        },


        /*
            updates the content of an element
        */
        updateElementContent: function() {

            $(contenteditor.activeElement.element).html( $('#editContentModal #contentToEdit').summernote('code') ).css({'outline': '', 'cursor':''});

            /* SANDBOX */

            if( contenteditor.activeElement.sandbox ) {

                var elementID = $(contenteditor.activeElement.element).attr('id');
                $('#'+contenteditor.activeElement.sandbox).contents().find('#'+elementID).html( $('#editContentModal #contentToEdit').summernote('code') );

            }

            /* END SANDBOX */

			$('#editContentModal #contentToEdit').summernote('code', '');
			$('#editContentModal #contentToEdit').summernote('destroy');

            $('#editContentModal').modal('hide');

            $(this).closest('body').removeClass('modal-open').attr('style', '');

            //reset iframe height
            contenteditor.activeElement.parentBlock.heightAdjustment();

            //content was updated, so we've got pending change
            siteBuilder.site.setPendingChanges(true);

            //reactivate element
            contenteditor.activeElement.activate();

        },


        /*
            event handler for when the edit content modal is closed
        */
        editContentModalCloseEvent: function() {

            $('#editContentModal #contentToEdit').summernote('destroy');

            //re-activate element
            contenteditor.activeElement.activate();

        },


        /*
            Event handler for when mode gets deactivated
        */
        deActivateMode: function() {
            if( Object.keys( contenteditor.activeElement ).length > 0 ) {
                contenteditor.activeElement.removeOutline();
            }

            //deactivate all content blocks
            for( var i = 0; i < contenteditor.allContentItemsOnCanvas.length; i++ ) {
                contenteditor.allContentItemsOnCanvas[i].deactivate();
            }

        }

    };

    contenteditor.init();

}());
},{"./builder.js":2,"./canvasElement.js":3,"./config":4}],6:[function(require,module,exports){
(function () {
	"use strict";

	var bConfig = require('./config.js');

	var bexport = {

        modalExport: document.getElementById('exportModal'),
        buttonExport: document.getElementById('exportPage'),

        init: function() {

            $(this.modalExport).on('show.bs.modal', this.doExportModal);
            $(this.modalExport).on('shown.bs.modal', this.prepExport);
            $(this.modalExport).find('form').on('submit', this.exportFormSubmit);

            //reveal export button
            $(this.buttonExport).show();

        },

        doExportModal: function() {

            $('#exportModal > form #exportSubmit').show('');
            $('#exportModal > form #exportCancel').text('Cancel & Close');

        },


        /*
            prepares the export data
        */
        prepExport: function(e) {

            //delete older hidden fields
            $('#exportModal form input[type="hidden"].pages').remove();

            //loop through all pages
            $('#pageList > ul').each(function(){

                var theContents;

                //grab the skeleton markup
                var newDocMainParent = $('iframe#skeleton').contents().find( bConfig.pageContainer );

                //empty out the skeleto
                newDocMainParent.find('*').remove();

                //loop through page iframes and grab the body stuff
                $(this).find('iframe').each(function(){

                    var attr = $(this).attr('data-sandbox');

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

                    for ( var i = 0; i < bConfig.editableContent.length; ++i) {

                        $(this).contents().find( bConfig.editableContent[i] ).each(function(){
                            $(this).removeAttr('data-selector');
                        });
                    }

                    var toAdd = theContents.html();

                    //grab scripts
                    var scripts = $(this).contents().find( bConfig.pageContainer ).find('script');

                    if( scripts.size() > 0 ) {

                        var theIframe = document.getElementById("skeleton"), script;

                        scripts.each(function(){

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

                var newInput = $('<input type="hidden" name="pages['+$('#pages li:eq('+($(this).index()+1)+') a:first').text()+']" class="pages" value="">');
                $('#exportModal form').prepend( newInput );
                newInput.val( "<html>"+$('iframe#skeleton').contents().find('html').html()+"</html>" );

            });
        },


        /*
            event handler for the export from submit
        */
        exportFormSubmit: function() {

            $('#exportModal > form #exportSubmit').hide('');
            $('#exportModal > form #exportCancel').text('Close Window');

        }

    };

    bexport.init();

}());
},{"./config.js":4}],7:[function(require,module,exports){
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
},{"./builder.js":2,"./config.js":4,"./styleeditor.js":12,"./ui.js":14}],8:[function(require,module,exports){
(function () {
	"use strict";

	var bConfig = require('./config.js');
	var siteBuilder = require('./builder.js');

	var preview = {

        modalPreview: document.getElementById('previewModal'),
        buttonPreview: document.getElementById('buttonPreview'),

        init: function() {

            //events
            $(this.modalPreview).on('shown.bs.modal', this.prepPreview);
            $(this.modalPreview).on('show.bs.modal', this.prepPreviewLink);

            //reveal preview button
            $(this.buttonPreview).show();

        },


        /*
            prepares the preview data
        */
        prepPreview: function() {

            $('#previewModal form input[type="hidden"]').remove();

            //build the page
            siteBuilder.site.activePage.fullPage();

            var newInput;

            //markup
            newInput = $('<input type="hidden" name="page" value="">');
            $('#previewModal form').prepend( newInput );
            newInput.val( "<html>"+$('iframe#skeleton').contents().find('html').html()+"</html>" );

            //page title
            newInput = $('<input type="hidden" name="meta_title" value="">');
            $('#previewModal form').prepend( newInput );
            newInput.val( siteBuilder.site.activePage.pageSettings.title );
            //alert(JSON.stringify(siteBuilder.site.activePage.pageSettings));

            //page meta description
            newInput = $('<input type="hidden" name="meta_description" value="">');
            $('#previewModal form').prepend( newInput );
            newInput.val( siteBuilder.site.activePage.pageSettings.meta_description );

            //page meta keywords
            newInput = $('<input type="hidden" name="meta_keywords" value="">');
            $('#previewModal form').prepend( newInput );
            newInput.val( siteBuilder.site.activePage.pageSettings.meta_keywords );

            //page header includes
            newInput = $('<input type="hidden" name="header_includes" value="">');
            $('#previewModal form').prepend( newInput );
            newInput.val( siteBuilder.site.activePage.pageSettings.header_includes );

            //page css
            newInput = $('<input type="hidden" name="page_css" value="">');
            $('#previewModal form').prepend( newInput );
            newInput.val( siteBuilder.site.activePage.pageSettings.page_css );

            //site ID
            newInput = $('<input type="hidden" name="siteID" value="">');
            $('#previewModal form').prepend( newInput );
            newInput.val( siteBuilder.site.data.id );

            //CSRF token
            newInput = $('<input type="hidden" name="_token" value="">');
            $('#previewModal form').prepend( newInput );
            newInput.val( $('meta[name="csrf-token"]').attr('content') );

        },


        /*
            prepares the actual preview link
        */
        prepPreviewLink: function() {

            $('#pagePreviewLink').attr( 'href', $('#pagePreviewLink').attr('data-defurl')+$('#pages li.active a').text() );

        }

    };

    preview.init();

}());
},{"./builder.js":2,"./config.js":4}],9:[function(require,module,exports){
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
},{"./builder.js":2,"./config.js":4,"./ui.js":14}],10:[function(require,module,exports){
(function () {
	"use strict";

	var siteBuilder = require('./builder.js');
	var appUI = require('./ui.js').appUI;

	var revisions = {

        selectRevisions: document.getElementById('dropdown_revisions'),
        buttonRevisions: document.getElementById('button_revisionsDropdown'),

        init: function() {

            $(this.selectRevisions).on('click', 'a.link_deleteRevision', this.deleteRevision);
            $(this.selectRevisions).on('click', 'a.link_restoreRevision', this.restoreRevision);
            $(document).on('changePage', 'body', this.loadRevisions);

            //reveal the revisions dropdown
            $(this.buttonRevisions).show();

        },


        /*
            deletes a single revision
        */
        deleteRevision: function(e) {

            e.preventDefault();

            var theLink = $(this);

            if( confirm('Are you sure you want to delete this revision?') ) {

                $.ajax({
                    url: $(this).attr('href'),
                    method: 'get',
                    dataType: 'json'
                }).done(function(ret){

                    if( ret.code === 1 ) {//if successfull, remove LI from list

                        theLink.parent().fadeOut(function(){

                            $(this).remove();

                            if( $('ul#dropdown_revisions li').size() === 0 ) {//list is empty, hide revisions dropdown
                                $('#button_revisionsDropdown button').addClass('disabled');
                                $('#button_revisionsDropdown').dropdown('toggle');
                            }

                        });

                    }

                });

            }

            return false;

        },


        /*
            restores a revision
        */
        restoreRevision: function() {

            if( confirm('Are you sure you want to restore this revision? This would overwrite the current page. Continue?') ) {
                return true;
            } else {
                return false;
            }

        },


        /*
            loads revisions for the active page
        */
        loadRevisions: function() {

            $.ajax({
                url: appUI.siteUrl+"site/getRevisions/"+siteBuilder.site.data.id+"/"+siteBuilder.site.activePage.name
            }).done(function(ret){

                if( ret === '' ) {

                    $('#button_revisionsDropdown button').each(function(){
                        $(this).addClass('disabled');
                    });

                    $('ul#dropdown_revisions').html( '' );

                } else {

                    $('ul#dropdown_revisions').html( ret );
                    $('#button_revisionsDropdown button').each(function(){
                        $(this).removeClass('disabled');
                    });

                }
            });

        }

    };

    revisions.init();

}());
},{"./builder.js":2,"./ui.js":14}],11:[function(require,module,exports){
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
},{"./ui.js":14}],12:[function(require,module,exports){
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
},{"./builder.js":2,"./canvasElement.js":3,"./config.js":4}],13:[function(require,module,exports){
(function () {
	"use strict";

	var siteBuilder = require('./builder.js');
	var appUI = require('./ui.js').appUI;

	var templates = {

        ulTemplates: document.getElementById('templates'),
        buttonSaveTemplate: document.getElementById('saveTemplate'),
        modalDeleteTemplate: document.getElementById('delTemplateModal'),

        init: function() {

            //format the template thumbnails
            this.zoomTemplateIframes();

            //make template thumbs draggable
            this.makeDraggable();

            $(this.buttonSaveTemplate).on('click', this.saveTemplate);

            //listen for the beforeSave event
            $('body').on('siteDataLoaded', function(){

                if( siteBuilder.site.is_admin === 1 ) {

                    templates.addDelLinks();
                    $(templates.modalDeleteTemplate).on('show.bs.modal', templates.prepTemplateDeleteModal);

                }

            });

        },


        /*
            applies zoomer to all template iframes in the sidebar
        */
        zoomTemplateIframes: function() {

            $(this.ulTemplates).find('iframe').each(function(){

                $(this).zoomer({
                    zoom: 0.25,
                    width: 270,
                    height: $(this).attr('data-height')*0.25,
                    message: "Drag & Drop me"
                });

            });

        },


        /*
            makes the template thumbnails draggable
        */
        makeDraggable: function() {

            $(this.ulTemplates).find('li').each(function(){

                $(this).draggable({
                    helper: function() {
                    return $('<div style="height: 100px; width: 300px; background: #F9FAFA; box-shadow: 5px 5px 1px rgba(0,0,0,0.1); text-align: center; line-height: 100px; font-size: 28px; color: #16A085"><span class="fui-list"></span></div>');
                },
                    revert: 'invalid',
                    appendTo: 'body',
                    connectToSortable: '#page1',
                    start: function(){

                        //switch to block mode
                        $('input:radio[name=mode]').parent().addClass('disabled');
                        $('input:radio[name=mode]#modeBlock').radio('check');

                        //show all iframe covers and activate designMode

                        $('#pageList ul .zoomer-wrapper .zoomer-cover').each(function(){

                            $(this).show();

                        });
                    }

                });

                //disable click events on child ancors
                $(this).find('a').each(function(){
                    $(this).unbind('click').bind('click', function(e){
                        e.preventDefault();
                    });
                });

            });

        },


        /*
            Saves a page as a template
        */
        saveTemplate: function(e) {

            e.preventDefault();

            //disable button
            $("a#savePage").addClass('disabled');

            //remove old alerts
            $('#errorModal .modal-body > *, #successModal .modal-body > *').each(function(){
                $(this).remove();
            });

            siteBuilder.site.prepForSave(true);

            var serverData = {};
            serverData.pages = siteBuilder.site.sitePagesReadyForServer;
            serverData.siteData = siteBuilder.site.data;
            serverData.fullPage = "<html>"+$(siteBuilder.site.skeleton).contents().find('html').html()+"</html>";

            //are we updating an existing template or creating a new one?
            serverData.templateID = siteBuilder.builder.templateID;

            console.log(siteBuilder.builder.templateID);

            $.ajax({
                url: appUI.siteUrl+"site/tsave",
                type: "POST",
                dataType: "json",
                data: serverData
            }).done(function(res){


                //enable button
                $("a#savePage").removeClass('disabled');

                if( res.responseCode === 0 ) {

                    $('#errorModal .modal-body').append( $(res.responseHTML) );
                    $('#errorModal').modal('show');
                    siteBuilder.builder.templateID = 0;

                } else if( res.responseCode === 1 ) {

                    $('#successModal .modal-body').append( $(res.responseHTML) );
                    $('#successModal').modal('show');
                    siteBuilder.builder.templateID = res.templateID;

                    //no more pending changes
                    siteBuilder.site.setPendingChanges(false);
                }
            });

        },


        /*
            adds DEL links for admin users
        */
        addDelLinks: function() {

            $(this.ulTemplates).find('li').each(function(){

                var newLink = $('<a href="#delTemplateModal" data-toggle="modal" data-pageid="'+$(this).attr('data-pageid')+'" class="btn btn-danger btn-sm">DEL</a>');
                $(this).find('.zoomer-cover').append( newLink );

            });

        },


        /*
            preps the delete template modal
        */
        prepTemplateDeleteModal: function(e) {

            var button = $(e.relatedTarget); // Button that triggered the modal
		  	var pageID = button.attr('data-pageid'); // Extract info from data-* attributes

		  	$('#delTemplateModal').find('#templateDelButton').attr('href', $('#delTemplateModal').find('#templateDelButton').attr('href')+"/"+pageID);
        }

    };

    templates.init();

    exports.templates = templates;

}());
},{"./builder.js":2,"./ui.js":14}],14:[function(require,module,exports){
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
},{}],15:[function(require,module,exports){
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJwdWJsaWMvc3JjL2pzL2J1aWxkZXIuanMiLCJwdWJsaWMvc3JjL2pzL21vZHVsZXMvYnVpbGRlci5qcyIsInB1YmxpYy9zcmMvanMvbW9kdWxlcy9jYW52YXNFbGVtZW50LmpzIiwicHVibGljL3NyYy9qcy9tb2R1bGVzL2NvbmZpZy5qcyIsInB1YmxpYy9zcmMvanMvbW9kdWxlcy9jb250ZW50LmpzIiwicHVibGljL3NyYy9qcy9tb2R1bGVzL2V4cG9ydC5qcyIsInB1YmxpYy9zcmMvanMvbW9kdWxlcy9pbWFnZUxpYnJhcnkuanMiLCJwdWJsaWMvc3JjL2pzL21vZHVsZXMvcHJldmlldy5qcyIsInB1YmxpYy9zcmMvanMvbW9kdWxlcy9wdWJsaXNoaW5nLmpzIiwicHVibGljL3NyYy9qcy9tb2R1bGVzL3JldmlzaW9ucy5qcyIsInB1YmxpYy9zcmMvanMvbW9kdWxlcy9zaXRlc2V0dGluZ3MuanMiLCJwdWJsaWMvc3JjL2pzL21vZHVsZXMvc3R5bGVlZGl0b3IuanMiLCJwdWJsaWMvc3JjL2pzL21vZHVsZXMvdGVtcGxhdGVzLmpzIiwicHVibGljL3NyYy9qcy9tb2R1bGVzL3VpLmpzIiwicHVibGljL3NyYy9qcy9tb2R1bGVzL3V0aWxzLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FDQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDbEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDbHhEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3JJQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDdENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3JOQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUM3SUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUMxTUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQzVGQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDdGpCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUMvR0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ3pMQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUNqaENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQzdMQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOztBQ2hIQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsIihmdW5jdGlvbiAoKSB7XG5cdFwidXNlIHN0cmljdFwiO1xuXG5cdHJlcXVpcmUoJy4vbW9kdWxlcy91aS5qcycpO1xuXHRyZXF1aXJlKCcuL21vZHVsZXMvYnVpbGRlci5qcycpO1xuXHRyZXF1aXJlKCcuL21vZHVsZXMvY29uZmlnLmpzJyk7XG5cdHJlcXVpcmUoJy4vbW9kdWxlcy91dGlscy5qcycpO1xuXHRyZXF1aXJlKCcuL21vZHVsZXMvY2FudmFzRWxlbWVudC5qcycpO1xuXHRyZXF1aXJlKCcuL21vZHVsZXMvc3R5bGVlZGl0b3IuanMnKTtcblx0cmVxdWlyZSgnLi9tb2R1bGVzL2ltYWdlTGlicmFyeS5qcycpO1xuXHRyZXF1aXJlKCcuL21vZHVsZXMvY29udGVudC5qcycpO1xuXHRyZXF1aXJlKCcuL21vZHVsZXMvc2l0ZXNldHRpbmdzLmpzJyk7XG5cdHJlcXVpcmUoJy4vbW9kdWxlcy9wdWJsaXNoaW5nLmpzJyk7XG5cdHJlcXVpcmUoJy4vbW9kdWxlcy9leHBvcnQuanMnKTtcblx0cmVxdWlyZSgnLi9tb2R1bGVzL3ByZXZpZXcuanMnKTtcblx0cmVxdWlyZSgnLi9tb2R1bGVzL3JldmlzaW9ucy5qcycpO1xuXHRyZXF1aXJlKCcuL21vZHVsZXMvdGVtcGxhdGVzLmpzJyk7XG5cbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cbiAgICB2YXIgc2l0ZUJ1aWxkZXJVdGlscyA9IHJlcXVpcmUoJy4vdXRpbHMuanMnKTtcbiAgICB2YXIgYkNvbmZpZyA9IHJlcXVpcmUoJy4vY29uZmlnLmpzJyk7XG4gICAgdmFyIGFwcFVJID0gcmVxdWlyZSgnLi91aS5qcycpLmFwcFVJO1xuXG5cblx0IC8qXG4gICAgICAgIEJhc2ljIEJ1aWxkZXIgVUkgaW5pdGlhbGlzYXRpb25cbiAgICAqL1xuICAgIHZhciBidWlsZGVyVUkgPSB7XG5cbiAgICAgICAgYWxsQmxvY2tzOiB7fSwgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy9ob2xkcyBhbGwgYmxvY2tzIGxvYWRlZCBmcm9tIHRoZSBzZXJ2ZXJcbiAgICAgICAgbWVudVdyYXBwZXI6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtZW51JyksXG4gICAgICAgIHByaW1hcnlTaWRlTWVudVdyYXBwZXI6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtYWluJyksXG4gICAgICAgIGJ1dHRvbkJhY2s6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdiYWNrQnV0dG9uJyksXG4gICAgICAgIGJ1dHRvbkJhY2tDb25maXJtOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbGVhdmVQYWdlQnV0dG9uJyksXG5cbiAgICAgICAgc2l0ZUJ1aWxkZXJNb2RlczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NpdGVCdWlsZGVyTW9kZXMnKSxcbiAgICAgICAgYWNlRWRpdG9yczoge30sXG4gICAgICAgIGZyYW1lQ29udGVudHM6ICcnLCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy9ob2xkcyBmcmFtZSBjb250ZW50c1xuICAgICAgICB0ZW1wbGF0ZUlEOiAwLCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vaG9sZHMgdGhlIHRlbXBsYXRlIElEIGZvciBhIHBhZ2UgKD8/PylcbiAgICAgICAgcmFkaW9CbG9ja01vZGU6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtb2RlQmxvY2snKSxcblxuICAgICAgICBtb2RhbERlbGV0ZUJsb2NrOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZGVsZXRlQmxvY2snKSxcbiAgICAgICAgbW9kYWxSZXNldEJsb2NrOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncmVzZXRCbG9jaycpLFxuICAgICAgICBtb2RhbERlbGV0ZVBhZ2U6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdkZWxldGVQYWdlJyksXG4gICAgICAgIGJ1dHRvbkRlbGV0ZVBhZ2VDb25maXJtOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZGVsZXRlUGFnZUNvbmZpcm0nKSxcblxuICAgICAgICBkcm9wZG93blBhZ2VMaW5rczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2ludGVybmFsTGlua3NEcm9wZG93bicpLFxuXG4gICAgICAgIHBhZ2VJblVybDogbnVsbCxcblxuICAgICAgICB0ZW1wRnJhbWU6IHt9LFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgIC8vbG9hZCBibG9ja3NcbiAgICAgICAgICAgICQuZ2V0SlNPTihhcHBVSS5iYXNlVXJsKycvZWxlbWVudHMuanNvbj92PTEyMzQ1Njc4JywgZnVuY3Rpb24oZGF0YSl7IGJ1aWxkZXJVSS5hbGxCbG9ja3MgPSBkYXRhOyBidWlsZGVyVUkuaW1wbGVtZW50QmxvY2tzKCk7IH0pO1xuXG4gICAgICAgICAgICAvL3NpdGViYXIgaG92ZXIgYW5pbWF0aW9uIGFjdGlvblxuICAgICAgICAgICAgJCh0aGlzLm1lbnVXcmFwcGVyKS5vbignbW91c2VlbnRlcicsIGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMpLnN0b3AoKS5hbmltYXRlKHsnbGVmdCc6ICcwcHgnfSwgNTAwKTtcblxuICAgICAgICAgICAgfSkub24oJ21vdXNlbGVhdmUnLCBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5zdG9wKCkuYW5pbWF0ZSh7J2xlZnQnOiAnLTE5MHB4J30sIDUwMCk7XG5cbiAgICAgICAgICAgICAgICAkKCcjbWVudSAjbWFpbiBhJykucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xuICAgICAgICAgICAgICAgICQoJy5tZW51IC5zZWNvbmQnKS5zdG9wKCkuYW5pbWF0ZSh7XG4gICAgICAgICAgICAgICAgICAgIHdpZHRoOiAwXG4gICAgICAgICAgICAgICAgfSwgNTAwLCBmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICAgICAkKCcjbWVudSAjc2Vjb25kJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy9wcmV2ZW50IGNsaWNrIGV2ZW50IG9uIGFuY29ycyBpbiB0aGUgYmxvY2sgc2VjdGlvbiBvZiB0aGUgc2lkZWJhclxuICAgICAgICAgICAgJCh0aGlzLnByaW1hcnlTaWRlTWVudVdyYXBwZXIpLm9uKCdjbGljaycsICdhOm5vdCguYWN0aW9uQnV0dG9ucyknLCBmdW5jdGlvbihlKXtlLnByZXZlbnREZWZhdWx0KCk7fSk7XG5cbiAgICAgICAgICAgICQodGhpcy5idXR0b25CYWNrKS5vbignY2xpY2snLCB0aGlzLmJhY2tCdXR0b24pO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvbkJhY2tDb25maXJtKS5vbignY2xpY2snLCB0aGlzLmJhY2tCdXR0b25Db25maXJtKTtcblxuICAgICAgICAgICAgLy9ub3RpZnkgdGhlIHVzZXIgb2YgcGVuZGluZyBjaG5hZ2VzIHdoZW4gY2xpY2tpbmcgdGhlIGJhY2sgYnV0dG9uXG4gICAgICAgICAgICAkKHdpbmRvdykuYmluZCgnYmVmb3JldW5sb2FkJywgZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICBpZiggc2l0ZS5wZW5kaW5nQ2hhbmdlcyA9PT0gdHJ1ZSApIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuICdZb3VyIHNpdGUgY29udGFpbnMgY2hhbmdlZCB3aGljaCBoYXZlblxcJ3QgYmVlbiBzYXZlZCB5ZXQuIEFyZSB5b3Ugc3VyZSB5b3Ugd2FudCB0byBsZWF2ZT8nO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvL21ha2Ugc3VyZSB3ZSBzdGFydCBpbiBibG9jayBtb2RlXG4gICAgICAgICAgICAkKHRoaXMucmFkaW9CbG9ja01vZGUpLnJhZGlvY2hlY2soJ2NoZWNrJykub24oJ2NsaWNrJywgdGhpcy5hY3RpdmF0ZUJsb2NrTW9kZSk7XG5cbiAgICAgICAgICAgIC8vVVJMIHBhcmFtZXRlcnNcbiAgICAgICAgICAgIGJ1aWxkZXJVSS5wYWdlSW5VcmwgPSBzaXRlQnVpbGRlclV0aWxzLmdldFBhcmFtZXRlckJ5TmFtZSgncCcpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgYnVpbGRzIHRoZSBibG9ja3MgaW50byB0aGUgc2l0ZSBiYXJcbiAgICAgICAgKi9cbiAgICAgICAgaW1wbGVtZW50QmxvY2tzOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIG5ld0l0ZW0sIGxvYWRlckZ1bmN0aW9uO1xuXG4gICAgICAgICAgICBmb3IoIHZhciBrZXkgaW4gdGhpcy5hbGxCbG9ja3MuZWxlbWVudHMgKSB7XG5cbiAgICAgICAgICAgICAgICB2YXIgbmljZUtleSA9IGtleS50b0xvd2VyQ2FzZSgpLnJlcGxhY2UoXCIgXCIsIFwiX1wiKTtcblxuICAgICAgICAgICAgICAgICQoJzxsaT48YSBocmVmPVwiXCIgaWQ9XCInK25pY2VLZXkrJ1wiPicra2V5Kyc8L2E+PC9saT4nKS5hcHBlbmRUbygnI21lbnUgI21haW4gdWwjZWxlbWVudENhdHMnKTtcblxuICAgICAgICAgICAgICAgIGZvciggdmFyIHggPSAwOyB4IDwgdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XS5sZW5ndGg7IHgrKyApIHtcblxuICAgICAgICAgICAgICAgICAgICBpZiggdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS50aHVtYm5haWwgPT09IG51bGwgKSB7Ly93ZSdsbCBuZWVkIGFuIGlmcmFtZVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL2J1aWxkIHVzIHNvbWUgaWZyYW1lcyFcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoIHRoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0uc2FuZGJveCApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKCB0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLmxvYWRlckZ1bmN0aW9uICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBsb2FkZXJGdW5jdGlvbiA9ICdkYXRhLWxvYWRlcmZ1bmN0aW9uPVwiJyt0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLmxvYWRlckZ1bmN0aW9uKydcIic7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbmV3SXRlbSA9ICQoJzxsaSBjbGFzcz1cImVsZW1lbnQgJytuaWNlS2V5KydcIj48aWZyYW1lIHNyYz1cIicrYXBwVUkuYmFzZVVybCt0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLnVybCsnXCIgc2Nyb2xsaW5nPVwibm9cIiBzYW5kYm94PVwiYWxsb3ctc2FtZS1vcmlnaW5cIj48L2lmcmFtZT48L2xpPicpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbmV3SXRlbSA9ICQoJzxsaSBjbGFzcz1cImVsZW1lbnQgJytuaWNlS2V5KydcIj48aWZyYW1lIHNyYz1cImFib3V0OmJsYW5rXCIgc2Nyb2xsaW5nPVwibm9cIj48L2lmcmFtZT48L2xpPicpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0l0ZW0uZmluZCgnaWZyYW1lJykudW5pcXVlSWQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0l0ZW0uZmluZCgnaWZyYW1lJykuYXR0cignc3JjJywgYXBwVUkuYmFzZVVybCt0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLnVybCk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHsvL3dlJ3ZlIGdvdCBhIHRodW1ibmFpbFxuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiggdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS5zYW5kYm94ICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoIHRoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0ubG9hZGVyRnVuY3Rpb24gKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxvYWRlckZ1bmN0aW9uID0gJ2RhdGEtbG9hZGVyZnVuY3Rpb249XCInK3RoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0ubG9hZGVyRnVuY3Rpb24rJ1wiJztcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBuZXdJdGVtID0gJCgnPGxpIGNsYXNzPVwiZWxlbWVudCAnK25pY2VLZXkrJ1wiPjxpbWcgc3JjPVwiJythcHBVSS5iYXNlVXJsK3RoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0udGh1bWJuYWlsKydcIiBkYXRhLXNyY2M9XCInK2FwcFVJLmJhc2VVcmwrdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS51cmwrJ1wiIGRhdGEtaGVpZ2h0PVwiJyt0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLmhlaWdodCsnXCIgZGF0YS1zYW5kYm94PVwiXCIgJytsb2FkZXJGdW5jdGlvbisnPjwvbGk+Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBuZXdJdGVtID0gJCgnPGxpIGNsYXNzPVwiZWxlbWVudCAnK25pY2VLZXkrJ1wiPjxpbWcgc3JjPVwiJythcHBVSS5iYXNlVXJsK3RoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0udGh1bWJuYWlsKydcIiBkYXRhLXNyY2M9XCInK2FwcFVJLmJhc2VVcmwrdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS51cmwrJ1wiIGRhdGEtaGVpZ2h0PVwiJyt0aGlzLmFsbEJsb2Nrcy5lbGVtZW50c1trZXldW3hdLmhlaWdodCsnXCI+PC9saT4nKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgbmV3SXRlbS5hcHBlbmRUbygnI21lbnUgI3NlY29uZCB1bCNlbGVtZW50cycpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vem9vbWVyIHdvcmtzXG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIHRoZUhlaWdodDtcblxuICAgICAgICAgICAgICAgICAgICBpZiggdGhpcy5hbGxCbG9ja3MuZWxlbWVudHNba2V5XVt4XS5oZWlnaHQgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRoZUhlaWdodCA9IHRoaXMuYWxsQmxvY2tzLmVsZW1lbnRzW2tleV1beF0uaGVpZ2h0KjAuMjU7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdGhlSGVpZ2h0ID0gJ2F1dG8nO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICBuZXdJdGVtLmZpbmQoJ2lmcmFtZScpLnpvb21lcih7XG4gICAgICAgICAgICAgICAgICAgICAgICB6b29tOiAwLjI1LFxuICAgICAgICAgICAgICAgICAgICAgICAgd2lkdGg6IDI3MCxcbiAgICAgICAgICAgICAgICAgICAgICAgIGhlaWdodDogdGhlSGVpZ2h0LFxuICAgICAgICAgICAgICAgICAgICAgICAgbWVzc2FnZTogXCJEcmFnJkRyb3AgTWUhXCJcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9kcmFnZ2FibGVzXG4gICAgICAgICAgICBidWlsZGVyVUkubWFrZURyYWdnYWJsZSgpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgZXZlbnQgaGFuZGxlciBmb3Igd2hlbiB0aGUgYmFjayBsaW5rIGlzIGNsaWNrZWRcbiAgICAgICAgKi9cbiAgICAgICAgYmFja0J1dHRvbjogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIGlmKCBzaXRlLnBlbmRpbmdDaGFuZ2VzID09PSB0cnVlICkge1xuICAgICAgICAgICAgICAgICQoJyNiYWNrTW9kYWwnKS5tb2RhbCgnc2hvdycpO1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGJ1dHRvbiBmb3IgY29uZmlybWluZyBsZWF2aW5nIHRoZSBwYWdlXG4gICAgICAgICovXG4gICAgICAgIGJhY2tCdXR0b25Db25maXJtOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgc2l0ZS5wZW5kaW5nQ2hhbmdlcyA9IGZhbHNlOy8vcHJldmVudCB0aGUgSlMgYWxlcnQgYWZ0ZXIgY29uZmlybWluZyB1c2VyIHdhbnRzIHRvIGxlYXZlXG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBhY3RpdmF0ZXMgYmxvY2sgbW9kZVxuICAgICAgICAqL1xuICAgICAgICBhY3RpdmF0ZUJsb2NrTW9kZTogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHNpdGUuYWN0aXZlUGFnZS50b2dnbGVGcmFtZUNvdmVycygnT24nKTtcblxuICAgICAgICAgICAgLy90cmlnZ2VyIGN1c3RvbSBldmVudFxuICAgICAgICAgICAgJCgnYm9keScpLnRyaWdnZXIoJ21vZGVCbG9ja3MnKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIG1ha2VzIHRoZSBibG9ja3MgYW5kIHRlbXBsYXRlcyBpbiB0aGUgc2lkZWJhciBkcmFnZ2FibGUgb250byB0aGUgY2FudmFzXG4gICAgICAgICovXG4gICAgICAgIG1ha2VEcmFnZ2FibGU6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKCcjZWxlbWVudHMgbGksICN0ZW1wbGF0ZXMgbGknKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMpLmRyYWdnYWJsZSh7XG4gICAgICAgICAgICAgICAgICAgIGhlbHBlcjogZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gJCgnPGRpdiBzdHlsZT1cImhlaWdodDogMTAwcHg7IHdpZHRoOiAzMDBweDsgYmFja2dyb3VuZDogI0Y5RkFGQTsgYm94LXNoYWRvdzogNXB4IDVweCAxcHggcmdiYSgwLDAsMCwwLjEpOyB0ZXh0LWFsaWduOiBjZW50ZXI7IGxpbmUtaGVpZ2h0OiAxMDBweDsgZm9udC1zaXplOiAyOHB4OyBjb2xvcjogIzE2QTA4NVwiPjxzcGFuIGNsYXNzPVwiZnVpLWxpc3RcIj48L3NwYW4+PC9kaXY+Jyk7XG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHJldmVydDogJ2ludmFsaWQnLFxuICAgICAgICAgICAgICAgICAgICBhcHBlbmRUbzogJ2JvZHknLFxuICAgICAgICAgICAgICAgICAgICBjb25uZWN0VG9Tb3J0YWJsZTogJyNwYWdlTGlzdCA+IHVsJyxcbiAgICAgICAgICAgICAgICAgICAgc3RhcnQ6IGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vc3dpdGNoIHRvIGJsb2NrIG1vZGVcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJ2lucHV0OnJhZGlvW25hbWU9bW9kZV0nKS5wYXJlbnQoKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJ2lucHV0OnJhZGlvW25hbWU9bW9kZV0jbW9kZUJsb2NrJykucmFkaW9jaGVjaygnY2hlY2snKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICQoJyNlbGVtZW50cyBsaSBhJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS51bmJpbmQoJ2NsaWNrJykuYmluZCgnY2xpY2snLCBmdW5jdGlvbihlKXtcbiAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIEltcGxlbWVudHMgdGhlIHNpdGUgb24gdGhlIGNhbnZhcywgY2FsbGVkIGZyb20gdGhlIFNpdGUgb2JqZWN0IHdoZW4gdGhlIHNpdGVEYXRhIGhhcyBjb21wbGV0ZWQgbG9hZGluZ1xuICAgICAgICAqL1xuICAgICAgICBwb3B1bGF0ZUNhbnZhczogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciBpO1xuXG4gICAgICAgICAgICAvL2lmIHdlIGhhdmUgYW55IGJsb2NrcyBhdCBhbGwsIGFjdGl2YXRlIHRoZSBtb2Rlc1xuICAgICAgICAgICAgaWYoIE9iamVjdC5rZXlzKHNpdGUucGFnZXMpLmxlbmd0aCA+IDAgKSB7XG4gICAgICAgICAgICAgICAgdmFyIG1vZGVzID0gYnVpbGRlclVJLnNpdGVCdWlsZGVyTW9kZXMucXVlcnlTZWxlY3RvckFsbCgnaW5wdXRbdHlwZT1cInJhZGlvXCJdJyk7XG4gICAgICAgICAgICAgICAgZm9yKCBpID0gMDsgaSA8IG1vZGVzLmxlbmd0aDsgaSsrICkge1xuICAgICAgICAgICAgICAgICAgICBtb2Rlc1tpXS5yZW1vdmVBdHRyaWJ1dGUoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgY291bnRlciA9IDE7XG5cbiAgICAgICAgICAgIC8vbG9vcCB0aHJvdWdoIHRoZSBwYWdlc1xuXG4gICAgICAgICAgICBmb3IoIGkgaW4gc2l0ZS5wYWdlcyApIHtcblxuICAgICAgICAgICAgICAgIHZhciBuZXdQYWdlID0gbmV3IFBhZ2UoaSwgc2l0ZS5wYWdlc1tpXSwgY291bnRlcik7XG5cbiAgICAgICAgICAgICAgICBjb3VudGVyKys7XG5cbiAgICAgICAgICAgICAgICAvL3NldCB0aGlzIHBhZ2UgYXMgYWN0aXZlP1xuICAgICAgICAgICAgICAgIGlmKCBidWlsZGVyVUkucGFnZUluVXJsID09PSBpICkge1xuICAgICAgICAgICAgICAgICAgICBuZXdQYWdlLnNlbGVjdFBhZ2UoKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9hY3RpdmF0ZSB0aGUgZmlyc3QgcGFnZVxuICAgICAgICAgICAgaWYoc2l0ZS5zaXRlUGFnZXMubGVuZ3RoID4gMCAmJiBidWlsZGVyVUkucGFnZUluVXJsID09PSBudWxsKSB7XG4gICAgICAgICAgICAgICAgc2l0ZS5zaXRlUGFnZXNbMF0uc2VsZWN0UGFnZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cblxuICAgIH07XG5cblxuICAgIC8qXG4gICAgICAgIFBhZ2UgY29uc3RydWN0b3JcbiAgICAqL1xuICAgIGZ1bmN0aW9uIFBhZ2UgKHBhZ2VOYW1lLCBwYWdlLCBjb3VudGVyKSB7XG5cbiAgICAgICAgdGhpcy5uYW1lID0gcGFnZU5hbWUgfHwgXCJcIjtcbiAgICAgICAgdGhpcy5wYWdlSUQgPSBwYWdlLnBhZ2VzX2lkIHx8IDA7XG4gICAgICAgIHRoaXMuYmxvY2tzID0gW107XG4gICAgICAgIHRoaXMucGFyZW50VUwgPSB7fTsgLy9wYXJlbnQgVUwgb24gdGhlIGNhbnZhc1xuICAgICAgICB0aGlzLnN0YXR1cyA9ICcnOy8vJycsICduZXcnIG9yICdjaGFuZ2VkJ1xuICAgICAgICB0aGlzLnNjcmlwdHMgPSBbXTsvL3RyYWNrcyBzY3JpcHQgVVJMcyB1c2VkIG9uIHRoaXMgcGFnZVxuXG4gICAgICAgIHRoaXMucGFnZVNldHRpbmdzID0ge1xuICAgICAgICAgICAgdGl0bGU6IHBhZ2UucGFnZXNfdGl0bGUgfHwgJycsXG4gICAgICAgICAgICBtZXRhX2Rlc2NyaXB0aW9uOiBwYWdlLm1ldGFfZGVzY3JpcHRpb24gfHwgJycsXG4gICAgICAgICAgICBtZXRhX2tleXdvcmRzOiBwYWdlLm1ldGFfa2V5d29yZHMgfHwgJycsXG4gICAgICAgICAgICBoZWFkZXJfaW5jbHVkZXM6IHBhZ2UuaGVhZGVyX2luY2x1ZGVzIHx8ICcnLFxuICAgICAgICAgICAgcGFnZV9jc3M6IHBhZ2UucGFnZV9jc3MgfHwgJydcbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLnBhZ2VNZW51VGVtcGxhdGUgPSAnPGEgaHJlZj1cIlwiIGNsYXNzPVwibWVudUl0ZW1MaW5rXCI+cGFnZTwvYT48c3BhbiBjbGFzcz1cInBhZ2VCdXR0b25zXCI+PGEgaHJlZj1cIlwiIGNsYXNzPVwiZmlsZUVkaXQgZnVpLW5ld1wiPjwvYT48YSBocmVmPVwiXCIgY2xhc3M9XCJmaWxlRGVsIGZ1aS1jcm9zc1wiPjxhIGNsYXNzPVwiYnRuIGJ0bi14cyBidG4tcHJpbWFyeSBidG4tZW1ib3NzZWQgZmlsZVNhdmUgZnVpLWNoZWNrXCIgaHJlZj1cIiNcIj48L2E+PC9zcGFuPjwvYT48L3NwYW4+JztcblxuICAgICAgICB0aGlzLm1lbnVJdGVtID0ge307Ly9yZWZlcmVuY2UgdG8gdGhlIHBhZ2VzIG1lbnUgaXRlbSBmb3IgdGhpcyBwYWdlIGluc3RhbmNlXG4gICAgICAgIHRoaXMubGlua3NEcm9wZG93bkl0ZW0gPSB7fTsvL3JlZmVyZW5jZSB0byB0aGUgbGlua3MgZHJvcGRvd24gaXRlbSBmb3IgdGhpcyBwYWdlIGluc3RhbmNlXG5cbiAgICAgICAgdGhpcy5wYXJlbnRVTCA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ1VMJyk7XG4gICAgICAgIHRoaXMucGFyZW50VUwuc2V0QXR0cmlidXRlKCdpZCcsIFwicGFnZVwiK2NvdW50ZXIpO1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBtYWtlcyB0aGUgY2xpY2tlZCBwYWdlIGFjdGl2ZVxuICAgICAgICAqL1xuICAgICAgICB0aGlzLnNlbGVjdFBhZ2UgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9jb25zb2xlLmxvZygnc2VsZWN0OicpO1xuICAgICAgICAgICAgLy9jb25zb2xlLmxvZyh0aGlzLnBhZ2VTZXR0aW5ncyk7XG5cbiAgICAgICAgICAgIC8vbWFyayB0aGUgbWVudSBpdGVtIGFzIGFjdGl2ZVxuICAgICAgICAgICAgc2l0ZS5kZUFjdGl2YXRlQWxsKCk7XG4gICAgICAgICAgICAkKHRoaXMubWVudUl0ZW0pLmFkZENsYXNzKCdhY3RpdmUnKTtcblxuICAgICAgICAgICAgLy9sZXQgU2l0ZSBrbm93IHdoaWNoIHBhZ2UgaXMgY3VycmVudGx5IGFjdGl2ZVxuICAgICAgICAgICAgc2l0ZS5zZXRBY3RpdmUodGhpcyk7XG5cbiAgICAgICAgICAgIC8vZGlzcGxheSB0aGUgbmFtZSBvZiB0aGUgYWN0aXZlIHBhZ2Ugb24gdGhlIGNhbnZhc1xuICAgICAgICAgICAgc2l0ZS5wYWdlVGl0bGUuaW5uZXJIVE1MID0gdGhpcy5uYW1lO1xuXG4gICAgICAgICAgICAvL2xvYWQgdGhlIHBhZ2Ugc2V0dGluZ3MgaW50byB0aGUgcGFnZSBzZXR0aW5ncyBtb2RhbFxuICAgICAgICAgICAgc2l0ZS5pbnB1dFBhZ2VTZXR0aW5nc1RpdGxlLnZhbHVlID0gdGhpcy5wYWdlU2V0dGluZ3MudGl0bGU7XG4gICAgICAgICAgICBzaXRlLmlucHV0UGFnZVNldHRpbmdzTWV0YURlc2NyaXB0aW9uLnZhbHVlID0gdGhpcy5wYWdlU2V0dGluZ3MubWV0YV9kZXNjcmlwdGlvbjtcbiAgICAgICAgICAgIHNpdGUuaW5wdXRQYWdlU2V0dGluZ3NNZXRhS2V5d29yZHMudmFsdWUgPSB0aGlzLnBhZ2VTZXR0aW5ncy5tZXRhX2tleXdvcmRzO1xuICAgICAgICAgICAgc2l0ZS5pbnB1dFBhZ2VTZXR0aW5nc0luY2x1ZGVzLnZhbHVlID0gdGhpcy5wYWdlU2V0dGluZ3MuaGVhZGVyX2luY2x1ZGVzO1xuICAgICAgICAgICAgc2l0ZS5pbnB1dFBhZ2VTZXR0aW5nc1BhZ2VDc3MudmFsdWUgPSB0aGlzLnBhZ2VTZXR0aW5ncy5wYWdlX2NzcztcblxuICAgICAgICAgICAgLy90cmlnZ2VyIGN1c3RvbSBldmVudFxuICAgICAgICAgICAgJCgnYm9keScpLnRyaWdnZXIoJ2NoYW5nZVBhZ2UnKTtcblxuICAgICAgICAgICAgLy9yZXNldCB0aGUgaGVpZ2h0cyBmb3IgdGhlIGJsb2NrcyBvbiB0aGUgY3VycmVudCBwYWdlXG4gICAgICAgICAgICBmb3IoIHZhciBpIGluIHRoaXMuYmxvY2tzICkge1xuXG4gICAgICAgICAgICAgICAgaWYoIE9iamVjdC5rZXlzKHRoaXMuYmxvY2tzW2ldLmZyYW1lRG9jdW1lbnQpLmxlbmd0aCA+IDAgKXtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5ibG9ja3NbaV0uaGVpZ2h0QWRqdXN0bWVudCgpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL3Nob3cgdGhlIGVtcHR5IG1lc3NhZ2U/XG4gICAgICAgICAgICB0aGlzLmlzRW1wdHkoKTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBjaGFuZ2VkIHRoZSBsb2NhdGlvbi9vcmRlciBvZiBhIGJsb2NrIHdpdGhpbiBhIHBhZ2VcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5zZXRQb3NpdGlvbiA9IGZ1bmN0aW9uKGZyYW1lSUQsIG5ld1Bvcykge1xuXG4gICAgICAgICAgICAvL3dlJ2xsIG5lZWQgdGhlIGJsb2NrIG9iamVjdCBjb25uZWN0ZWQgdG8gaWZyYW1lIHdpdGggZnJhbWVJRFxuXG4gICAgICAgICAgICBmb3IodmFyIGkgaW4gdGhpcy5ibG9ja3MpIHtcblxuICAgICAgICAgICAgICAgIGlmKCB0aGlzLmJsb2Nrc1tpXS5mcmFtZS5nZXRBdHRyaWJ1dGUoJ2lkJykgPT09IGZyYW1lSUQgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9jaGFuZ2UgdGhlIHBvc2l0aW9uIG9mIHRoaXMgYmxvY2sgaW4gdGhlIGJsb2NrcyBhcnJheVxuICAgICAgICAgICAgICAgICAgICB0aGlzLmJsb2Nrcy5zcGxpY2UobmV3UG9zLCAwLCB0aGlzLmJsb2Nrcy5zcGxpY2UoaSwgMSlbMF0pO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgZGVsZXRlIGJsb2NrIGZyb20gYmxvY2tzIGFycmF5XG4gICAgICAgICovXG4gICAgICAgIHRoaXMuZGVsZXRlQmxvY2sgPSBmdW5jdGlvbihibG9jaykge1xuXG4gICAgICAgICAgICAvL3JlbW92ZSBmcm9tIGJsb2NrcyBhcnJheVxuICAgICAgICAgICAgZm9yKCB2YXIgaSBpbiB0aGlzLmJsb2NrcyApIHtcbiAgICAgICAgICAgICAgICBpZiggdGhpcy5ibG9ja3NbaV0gPT09IGJsb2NrICkge1xuICAgICAgICAgICAgICAgICAgICAvL2ZvdW5kIGl0LCByZW1vdmUgZnJvbSBibG9ja3MgYXJyYXlcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5ibG9ja3Muc3BsaWNlKGksIDEpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICB0b2dnbGVzIGFsbCBibG9jayBmcmFtZUNvdmVycyBvbiB0aGlzIHBhZ2VcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy50b2dnbGVGcmFtZUNvdmVycyA9IGZ1bmN0aW9uKG9uT3JPZmYpIHtcblxuICAgICAgICAgICAgZm9yKCB2YXIgaSBpbiB0aGlzLmJsb2NrcyApIHtcblxuICAgICAgICAgICAgICAgIHRoaXMuYmxvY2tzW2ldLnRvZ2dsZUNvdmVyKG9uT3JPZmYpO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgc2V0dXAgZm9yIGVkaXRpbmcgYSBwYWdlIG5hbWVcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5lZGl0UGFnZU5hbWUgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgaWYoICF0aGlzLm1lbnVJdGVtLmNsYXNzTGlzdC5jb250YWlucygnZWRpdCcpICkge1xuXG4gICAgICAgICAgICAgICAgLy9oaWRlIHRoZSBsaW5rXG4gICAgICAgICAgICAgICAgdGhpcy5tZW51SXRlbS5xdWVyeVNlbGVjdG9yKCdhLm1lbnVJdGVtTGluaycpLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG5cbiAgICAgICAgICAgICAgICAvL2luc2VydCB0aGUgaW5wdXQgZmllbGRcbiAgICAgICAgICAgICAgICB2YXIgbmV3SW5wdXQgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdpbnB1dCcpO1xuICAgICAgICAgICAgICAgIG5ld0lucHV0LnR5cGUgPSAndGV4dCc7XG4gICAgICAgICAgICAgICAgbmV3SW5wdXQuc2V0QXR0cmlidXRlKCduYW1lJywgJ3BhZ2UnKTtcbiAgICAgICAgICAgICAgICBuZXdJbnB1dC5zZXRBdHRyaWJ1dGUoJ3ZhbHVlJywgdGhpcy5uYW1lKTtcbiAgICAgICAgICAgICAgICB0aGlzLm1lbnVJdGVtLmluc2VydEJlZm9yZShuZXdJbnB1dCwgdGhpcy5tZW51SXRlbS5maXJzdENoaWxkKTtcblxuICAgICAgICAgICAgICAgIG5ld0lucHV0LmZvY3VzKCk7XG5cbiAgICAgICAgICAgICAgICB2YXIgdG1wU3RyID0gbmV3SW5wdXQuZ2V0QXR0cmlidXRlKCd2YWx1ZScpO1xuICAgICAgICAgICAgICAgIG5ld0lucHV0LnNldEF0dHJpYnV0ZSgndmFsdWUnLCAnJyk7XG4gICAgICAgICAgICAgICAgbmV3SW5wdXQuc2V0QXR0cmlidXRlKCd2YWx1ZScsIHRtcFN0cik7XG5cbiAgICAgICAgICAgICAgICB0aGlzLm1lbnVJdGVtLmNsYXNzTGlzdC5hZGQoJ2VkaXQnKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIFVwZGF0ZXMgdGhpcyBwYWdlJ3MgbmFtZSAoZXZlbnQgaGFuZGxlciBmb3IgdGhlIHNhdmUgYnV0dG9uKVxuICAgICAgICAqL1xuICAgICAgICB0aGlzLnVwZGF0ZVBhZ2VOYW1lRXZlbnQgPSBmdW5jdGlvbihlbCkge1xuXG4gICAgICAgICAgICBpZiggdGhpcy5tZW51SXRlbS5jbGFzc0xpc3QuY29udGFpbnMoJ2VkaXQnKSApIHtcblxuICAgICAgICAgICAgICAgIC8vZWwgaXMgdGhlIGNsaWNrZWQgYnV0dG9uLCB3ZSdsbCBuZWVkIGFjY2VzcyB0byB0aGUgaW5wdXRcbiAgICAgICAgICAgICAgICB2YXIgdGhlSW5wdXQgPSB0aGlzLm1lbnVJdGVtLnF1ZXJ5U2VsZWN0b3IoJ2lucHV0W25hbWU9XCJwYWdlXCJdJyk7XG5cbiAgICAgICAgICAgICAgICAvL21ha2Ugc3VyZSB0aGUgcGFnZSdzIG5hbWUgaXMgT0tcbiAgICAgICAgICAgICAgICBpZiggc2l0ZS5jaGVja1BhZ2VOYW1lKHRoZUlucHV0LnZhbHVlKSApIHtcblxuICAgICAgICAgICAgICAgICAgICB0aGlzLm5hbWUgPSBzaXRlLnByZXBQYWdlTmFtZSggdGhlSW5wdXQudmFsdWUgKTtcblxuICAgICAgICAgICAgICAgICAgICB0aGlzLm1lbnVJdGVtLnF1ZXJ5U2VsZWN0b3IoJ2lucHV0W25hbWU9XCJwYWdlXCJdJykucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMubWVudUl0ZW0ucXVlcnlTZWxlY3RvcignYS5tZW51SXRlbUxpbmsnKS5pbm5lckhUTUwgPSB0aGlzLm5hbWU7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMubWVudUl0ZW0ucXVlcnlTZWxlY3RvcignYS5tZW51SXRlbUxpbmsnKS5zdHlsZS5kaXNwbGF5ID0gJ2Jsb2NrJztcblxuICAgICAgICAgICAgICAgICAgICB0aGlzLm1lbnVJdGVtLmNsYXNzTGlzdC5yZW1vdmUoJ2VkaXQnKTtcblxuICAgICAgICAgICAgICAgICAgICAvL3VwZGF0ZSB0aGUgbGlua3MgZHJvcGRvd24gaXRlbVxuICAgICAgICAgICAgICAgICAgICB0aGlzLmxpbmtzRHJvcGRvd25JdGVtLnRleHQgPSB0aGlzLm5hbWU7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMubGlua3NEcm9wZG93bkl0ZW0uc2V0QXR0cmlidXRlKCd2YWx1ZScsIHRoaXMubmFtZStcIi5odG1sXCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vdXBkYXRlIHRoZSBwYWdlIG5hbWUgb24gdGhlIGNhbnZhc1xuICAgICAgICAgICAgICAgICAgICBzaXRlLnBhZ2VUaXRsZS5pbm5lckhUTUwgPSB0aGlzLm5hbWU7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9jaGFuZ2VkIHBhZ2UgdGl0bGUsIHdlJ3ZlIGdvdCBwZW5kaW5nIGNoYW5nZXNcbiAgICAgICAgICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICAgICAgYWxlcnQoc2l0ZS5wYWdlTmFtZUVycm9yKTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGRlbGV0ZXMgdGhpcyBlbnRpcmUgcGFnZVxuICAgICAgICAqL1xuICAgICAgICB0aGlzLmRlbGV0ZSA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2RlbGV0ZSBmcm9tIHRoZSBTaXRlXG4gICAgICAgICAgICBmb3IoIHZhciBpIGluIHNpdGUuc2l0ZVBhZ2VzICkge1xuXG4gICAgICAgICAgICAgICAgaWYoIHNpdGUuc2l0ZVBhZ2VzW2ldID09PSB0aGlzICkgey8vZ290IGEgbWF0Y2ghXG5cbiAgICAgICAgICAgICAgICAgICAgLy9kZWxldGUgZnJvbSBzaXRlLnNpdGVQYWdlc1xuICAgICAgICAgICAgICAgICAgICBzaXRlLnNpdGVQYWdlcy5zcGxpY2UoaSwgMSk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9kZWxldGUgZnJvbSBjYW52YXNcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5wYXJlbnRVTC5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2FkZCB0byBkZWxldGVkIHBhZ2VzXG4gICAgICAgICAgICAgICAgICAgIHNpdGUucGFnZXNUb0RlbGV0ZS5wdXNoKHRoaXMubmFtZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9kZWxldGUgdGhlIHBhZ2UncyBtZW51IGl0ZW1cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5tZW51SXRlbS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2RlbGV0IHRoZSBwYWdlcyBsaW5rIGRyb3Bkb3duIGl0ZW1cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5saW5rc0Ryb3Bkb3duSXRlbS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2FjdGl2YXRlIHRoZSBmaXJzdCBwYWdlXG4gICAgICAgICAgICAgICAgICAgIHNpdGUuc2l0ZVBhZ2VzWzBdLnNlbGVjdFBhZ2UoKTtcblxuICAgICAgICAgICAgICAgICAgICAvL3BhZ2Ugd2FzIGRlbGV0ZWQsIHNvIHdlJ3ZlIGdvdCBwZW5kaW5nIGNoYW5nZXNcbiAgICAgICAgICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGNoZWNrcyBpZiB0aGUgcGFnZSBpcyBlbXB0eSwgaWYgc28gc2hvdyB0aGUgJ2VtcHR5JyBtZXNzYWdlXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuaXNFbXB0eSA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICBpZiggdGhpcy5ibG9ja3MubGVuZ3RoID09PSAwICkge1xuXG4gICAgICAgICAgICAgICAgc2l0ZS5tZXNzYWdlU3RhcnQuc3R5bGUuZGlzcGxheSA9ICdibG9jayc7XG4gICAgICAgICAgICAgICAgc2l0ZS5kaXZGcmFtZVdyYXBwZXIuY2xhc3NMaXN0LmFkZCgnZW1wdHknKTtcblxuICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgIHNpdGUubWVzc2FnZVN0YXJ0LnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgICAgICAgICAgc2l0ZS5kaXZGcmFtZVdyYXBwZXIuY2xhc3NMaXN0LnJlbW92ZSgnZW1wdHknKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHByZXBzL3N0cmlwcyB0aGlzIHBhZ2UgZGF0YSBmb3IgYSBwZW5kaW5nIGFqYXggcmVxdWVzdFxuICAgICAgICAqL1xuICAgICAgICB0aGlzLnByZXBGb3JTYXZlID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciBwYWdlID0ge307XG5cbiAgICAgICAgICAgIHBhZ2UubmFtZSA9IHRoaXMubmFtZTtcbiAgICAgICAgICAgIHBhZ2UucGFnZVNldHRpbmdzID0gdGhpcy5wYWdlU2V0dGluZ3M7XG4gICAgICAgICAgICBwYWdlLnN0YXR1cyA9IHRoaXMuc3RhdHVzO1xuICAgICAgICAgICAgcGFnZS5ibG9ja3MgPSBbXTtcblxuICAgICAgICAgICAgLy9wcm9jZXNzIHRoZSBibG9ja3NcblxuICAgICAgICAgICAgZm9yKCB2YXIgeCA9IDA7IHggPCB0aGlzLmJsb2Nrcy5sZW5ndGg7IHgrKyApIHtcblxuICAgICAgICAgICAgICAgIHZhciBibG9jayA9IHt9O1xuXG4gICAgICAgICAgICAgICAgaWYoIHRoaXMuYmxvY2tzW3hdLnNhbmRib3ggKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgYmxvY2suZnJhbWVDb250ZW50ID0gXCI8aHRtbD5cIiskKCcjc2FuZGJveGVzICMnK3RoaXMuYmxvY2tzW3hdLnNhbmRib3gpLmNvbnRlbnRzKCkuZmluZCgnaHRtbCcpLmh0bWwoKStcIjwvaHRtbD5cIjtcbiAgICAgICAgICAgICAgICAgICAgYmxvY2suc2FuZGJveCA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIGJsb2NrLmxvYWRlckZ1bmN0aW9uID0gdGhpcy5ibG9ja3NbeF0uc2FuZGJveF9sb2FkZXI7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgICAgIGJsb2NrLmZyYW1lQ29udGVudCA9IHRoaXMuYmxvY2tzW3hdLmdldFNvdXJjZSgpO1xuICAgICAgICAgICAgICAgICAgICBibG9jay5zYW5kYm94ID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgIGJsb2NrLmxvYWRlckZ1bmN0aW9uID0gJyc7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBibG9jay5mcmFtZUhlaWdodCA9IHRoaXMuYmxvY2tzW3hdLmZyYW1lSGVpZ2h0O1xuICAgICAgICAgICAgICAgIGJsb2NrLm9yaWdpbmFsVXJsID0gdGhpcy5ibG9ja3NbeF0ub3JpZ2luYWxVcmw7XG5cbiAgICAgICAgICAgICAgICBwYWdlLmJsb2Nrcy5wdXNoKGJsb2NrKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gcGFnZTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBnZW5lcmF0ZXMgdGhlIGZ1bGwgcGFnZSwgdXNpbmcgc2tlbGV0b24uaHRtbFxuICAgICAgICAqL1xuICAgICAgICB0aGlzLmZ1bGxQYWdlID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciBwYWdlID0gdGhpczsvL3JlZmVyZW5jZSB0byBzZWxmIGZvciBsYXRlclxuICAgICAgICAgICAgcGFnZS5zY3JpcHRzID0gW107Ly9tYWtlIHN1cmUgaXQncyBlbXB0eSwgd2UnbGwgc3RvcmUgc2NyaXB0IFVSTHMgaW4gdGhlcmUgbGF0ZXJcblxuICAgICAgICAgICAgdmFyIG5ld0RvY01haW5QYXJlbnQgPSAkKCdpZnJhbWUjc2tlbGV0b24nKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciApO1xuXG4gICAgICAgICAgICAvL2VtcHR5IG91dCB0aGUgc2tlbGV0b24gZmlyc3RcbiAgICAgICAgICAgICQoJ2lmcmFtZSNza2VsZXRvbicpLmNvbnRlbnRzKCkuZmluZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyICkuaHRtbCgnJyk7XG5cbiAgICAgICAgICAgIC8vcmVtb3ZlIG9sZCBzY3JpcHQgdGFnc1xuICAgICAgICAgICAgJCgnaWZyYW1lI3NrZWxldG9uJykuY29udGVudHMoKS5maW5kKCAnc2NyaXB0JyApLmVhY2goZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHZhciB0aGVDb250ZW50cztcblxuICAgICAgICAgICAgZm9yKCB2YXIgaSBpbiB0aGlzLmJsb2NrcyApIHtcblxuICAgICAgICAgICAgICAgIC8vZ3JhYiB0aGUgYmxvY2sgY29udGVudFxuICAgICAgICAgICAgICAgIGlmICh0aGlzLmJsb2Nrc1tpXS5zYW5kYm94ICE9PSBmYWxzZSkge1xuXG4gICAgICAgICAgICAgICAgICAgIHRoZUNvbnRlbnRzID0gJCgnI3NhbmRib3hlcyAjJyt0aGlzLmJsb2Nrc1tpXS5zYW5kYm94KS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciApLmNsb25lKCk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgICAgIHRoZUNvbnRlbnRzID0gJCh0aGlzLmJsb2Nrc1tpXS5mcmFtZURvY3VtZW50LmJvZHkpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciApLmNsb25lKCk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvL3JlbW92ZSB2aWRlbyBmcmFtZUNvdmVyc1xuICAgICAgICAgICAgICAgIHRoZUNvbnRlbnRzLmZpbmQoJy5mcmFtZUNvdmVyJykuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvL3JlbW92ZSB2aWRlbyBmcmFtZVdyYXBwZXJzXG4gICAgICAgICAgICAgICAgdGhlQ29udGVudHMuZmluZCgnLnZpZGVvV3JhcHBlcicpLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgY250ID0gJCh0aGlzKS5jb250ZW50cygpO1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnJlcGxhY2VXaXRoKGNudCk7XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vcmVtb3ZlIHN0eWxlIGxlZnRvdmVycyBmcm9tIHRoZSBzdHlsZSBlZGl0b3JcbiAgICAgICAgICAgICAgICBmb3IoIHZhciBrZXkgaW4gYkNvbmZpZy5lZGl0YWJsZUl0ZW1zICkge1xuXG4gICAgICAgICAgICAgICAgICAgIHRoZUNvbnRlbnRzLmZpbmQoIGtleSApLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmVBdHRyKCdkYXRhLXNlbGVjdG9yJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuY3NzKCdvdXRsaW5lJywgJycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5jc3MoJ291dGxpbmUtb2Zmc2V0JywgJycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5jc3MoJ2N1cnNvcicsICcnKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYoICQodGhpcykuYXR0cignc3R5bGUnKSA9PT0gJycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZUF0dHIoJ3N0eWxlJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8vcmVtb3ZlIHN0eWxlIGxlZnRvdmVycyBmcm9tIHRoZSBjb250ZW50IGVkaXRvclxuICAgICAgICAgICAgICAgIGZvciAoIHZhciB4ID0gMDsgeCA8IGJDb25maWcuZWRpdGFibGVDb250ZW50Lmxlbmd0aDsgKyt4KSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdGhlQ29udGVudHMuZmluZCggYkNvbmZpZy5lZGl0YWJsZUNvbnRlbnRbeF0gKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQXR0cignZGF0YS1zZWxlY3RvcicpO1xuXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLy9hcHBlbmQgdG8gRE9NIGluIHRoZSBza2VsZXRvblxuICAgICAgICAgICAgICAgIG5ld0RvY01haW5QYXJlbnQuYXBwZW5kKCAkKHRoZUNvbnRlbnRzLmh0bWwoKSkgKTtcblxuICAgICAgICAgICAgICAgIC8vZG8gd2UgbmVlZCB0byBpbmplY3QgYW55IHNjcmlwdHM/XG4gICAgICAgICAgICAgICAgdmFyIHNjcmlwdHMgPSAkKHRoaXMuYmxvY2tzW2ldLmZyYW1lRG9jdW1lbnQuYm9keSkuZmluZCgnc2NyaXB0Jyk7XG4gICAgICAgICAgICAgICAgdmFyIHRoZUlmcmFtZSA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKFwic2tlbGV0b25cIik7XG5cbiAgICAgICAgICAgICAgICBpZiggc2NyaXB0cy5zaXplKCkgPiAwICkge1xuXG4gICAgICAgICAgICAgICAgICAgIHNjcmlwdHMuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgc2NyaXB0O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiggJCh0aGlzKS50ZXh0KCkgIT09ICcnICkgey8vc2NyaXB0IHRhZ3Mgd2l0aCBjb250ZW50XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzY3JpcHQgPSB0aGVJZnJhbWUuY29udGVudFdpbmRvdy5kb2N1bWVudC5jcmVhdGVFbGVtZW50KFwic2NyaXB0XCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdC50eXBlID0gJ3RleHQvamF2YXNjcmlwdCc7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2NyaXB0LmlubmVySFRNTCA9ICQodGhpcykudGV4dCgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhlSWZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZChzY3JpcHQpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYoICQodGhpcykuYXR0cignc3JjJykgIT09IG51bGwgJiYgcGFnZS5zY3JpcHRzLmluZGV4T2YoJCh0aGlzKS5hdHRyKCdzcmMnKSkgPT09IC0xICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vdXNlIGluZGV4T2YgdG8gbWFrZSBzdXJlIGVhY2ggc2NyaXB0IG9ubHkgYXBwZWFycyBvbiB0aGUgcHJvZHVjZWQgcGFnZSBvbmNlXG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzY3JpcHQgPSB0aGVJZnJhbWUuY29udGVudFdpbmRvdy5kb2N1bWVudC5jcmVhdGVFbGVtZW50KFwic2NyaXB0XCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdC50eXBlID0gJ3RleHQvamF2YXNjcmlwdCc7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2NyaXB0LnNyYyA9ICQodGhpcykuYXR0cignc3JjJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGVJZnJhbWUuY29udGVudFdpbmRvdy5kb2N1bWVudC5ib2R5LmFwcGVuZENoaWxkKHNjcmlwdCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBwYWdlLnNjcmlwdHMucHVzaCgkKHRoaXMpLmF0dHIoJ3NyYycpKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGNvbnNvbGUubG9nKHRoaXMuc2NyaXB0cyk7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgY2xlYXIgb3V0IHRoaXMgcGFnZVxuICAgICAgICAqL1xuICAgICAgICB0aGlzLmNsZWFyID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciBibG9jayA9IHRoaXMuYmxvY2tzLnBvcCgpO1xuXG4gICAgICAgICAgICB3aGlsZSggYmxvY2sgIT09IHVuZGVmaW5lZCApIHtcblxuICAgICAgICAgICAgICAgIGJsb2NrLmRlbGV0ZSgpO1xuXG4gICAgICAgICAgICAgICAgYmxvY2sgPSB0aGlzLmJsb2Nrcy5wb3AoKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cblxuICAgICAgICAvL2xvb3AgdGhyb3VnaCB0aGUgZnJhbWVzL2Jsb2Nrc1xuXG4gICAgICAgIGlmKCBwYWdlLmhhc093blByb3BlcnR5KCdibG9ja3MnKSApIHtcblxuICAgICAgICAgICAgZm9yKCB2YXIgeCA9IDA7IHggPCBwYWdlLmJsb2Nrcy5sZW5ndGg7IHgrKyApIHtcblxuICAgICAgICAgICAgICAgIC8vY3JlYXRlIG5ldyBCbG9ja1xuXG4gICAgICAgICAgICAgICAgdmFyIG5ld0Jsb2NrID0gbmV3IEJsb2NrKCk7XG5cbiAgICAgICAgICAgICAgICBwYWdlLmJsb2Nrc1t4XS5zcmMgPSBhcHBVSS5zaXRlVXJsK1wic2l0ZS9nZXRmcmFtZS9cIitwYWdlLmJsb2Nrc1t4XS5pZDtcblxuICAgICAgICAgICAgICAgIC8vc2FuZGJveGVkIGJsb2NrP1xuICAgICAgICAgICAgICAgIGlmKCBwYWdlLmJsb2Nrc1t4XS5mcmFtZXNfc2FuZGJveCA9PT0gJzEnKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgbmV3QmxvY2suc2FuZGJveCA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIG5ld0Jsb2NrLnNhbmRib3hfbG9hZGVyID0gcGFnZS5ibG9ja3NbeF0uZnJhbWVzX2xvYWRlcmZ1bmN0aW9uO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgbmV3QmxvY2suZnJhbWVJRCA9IHBhZ2UuYmxvY2tzW3hdLmZyYW1lc19pZDtcbiAgICAgICAgICAgICAgICBuZXdCbG9jay5jcmVhdGVQYXJlbnRMSShwYWdlLmJsb2Nrc1t4XS5mcmFtZXNfaGVpZ2h0KTtcbiAgICAgICAgICAgICAgICBuZXdCbG9jay5jcmVhdGVGcmFtZShwYWdlLmJsb2Nrc1t4XSk7XG4gICAgICAgICAgICAgICAgbmV3QmxvY2suY3JlYXRlRnJhbWVDb3ZlcigpO1xuICAgICAgICAgICAgICAgIG5ld0Jsb2NrLmluc2VydEJsb2NrSW50b0RvbSh0aGlzLnBhcmVudFVMKTtcblxuICAgICAgICAgICAgICAgIC8vYWRkIHRoZSBibG9jayB0byB0aGUgbmV3IHBhZ2VcbiAgICAgICAgICAgICAgICB0aGlzLmJsb2Nrcy5wdXNoKG5ld0Jsb2NrKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH1cblxuICAgICAgICAvL2FkZCB0aGlzIHBhZ2UgdG8gdGhlIHNpdGUgb2JqZWN0XG4gICAgICAgIHNpdGUuc2l0ZVBhZ2VzLnB1c2goIHRoaXMgKTtcblxuICAgICAgICAvL3BsYW50IHRoZSBuZXcgVUwgaW4gdGhlIERPTSAob24gdGhlIGNhbnZhcylcbiAgICAgICAgc2l0ZS5kaXZDYW52YXMuYXBwZW5kQ2hpbGQodGhpcy5wYXJlbnRVTCk7XG5cbiAgICAgICAgLy9tYWtlIHRoZSBibG9ja3MvZnJhbWVzIGluIGVhY2ggcGFnZSBzb3J0YWJsZVxuXG4gICAgICAgIHZhciB0aGVQYWdlID0gdGhpcztcblxuICAgICAgICAkKHRoaXMucGFyZW50VUwpLnNvcnRhYmxlKHtcbiAgICAgICAgICAgIHJldmVydDogdHJ1ZSxcbiAgICAgICAgICAgIHBsYWNlaG9sZGVyOiBcImRyb3AtaG92ZXJcIixcbiAgICAgICAgICAgIHN0b3A6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBzaXRlLnNldFBlbmRpbmdDaGFuZ2VzKHRydWUpO1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGJlZm9yZVN0b3A6IGZ1bmN0aW9uKGV2ZW50LCB1aSl7XG5cbiAgICAgICAgICAgICAgICAvL3RlbXBsYXRlIG9yIHJlZ3VsYXIgYmxvY2s/XG4gICAgICAgICAgICAgICAgdmFyIGF0dHIgPSB1aS5pdGVtLmF0dHIoJ2RhdGEtZnJhbWVzJyk7XG5cbiAgICAgICAgICAgICAgICB2YXIgbmV3QmxvY2s7XG5cbiAgICAgICAgICAgICAgICBpZiAodHlwZW9mIGF0dHIgIT09IHR5cGVvZiB1bmRlZmluZWQgJiYgYXR0ciAhPT0gZmFsc2UpIHsvL3RlbXBsYXRlLCBidWlsZCBpdFxuXG4gICAgICAgICAgICAgICAgICAgICQoJyNzdGFydCcpLmhpZGUoKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2NsZWFyIG91dCBhbGwgYmxvY2tzIG9uIHRoaXMgcGFnZVxuICAgICAgICAgICAgICAgICAgICB0aGVQYWdlLmNsZWFyKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9jcmVhdGUgdGhlIG5ldyBmcmFtZXNcbiAgICAgICAgICAgICAgICAgICAgdmFyIGZyYW1lSURzID0gdWkuaXRlbS5hdHRyKCdkYXRhLWZyYW1lcycpLnNwbGl0KCctJyk7XG4gICAgICAgICAgICAgICAgICAgIHZhciBoZWlnaHRzID0gdWkuaXRlbS5hdHRyKCdkYXRhLWhlaWdodHMnKS5zcGxpdCgnLScpO1xuICAgICAgICAgICAgICAgICAgICB2YXIgdXJscyA9IHVpLml0ZW0uYXR0cignZGF0YS1vcmlnaW5hbHVybHMnKS5zcGxpdCgnLScpO1xuXG4gICAgICAgICAgICAgICAgICAgIGZvciggdmFyIHggPSAwOyB4IDwgZnJhbWVJRHMubGVuZ3RoOyB4KyspIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgbmV3QmxvY2sgPSBuZXcgQmxvY2soKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0Jsb2NrLmNyZWF0ZVBhcmVudExJKGhlaWdodHNbeF0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgZnJhbWVEYXRhID0ge307XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGZyYW1lRGF0YS5zcmMgPSBhcHBVSS5zaXRlVXJsKydzaXRlL2dldGZyYW1lLycrZnJhbWVJRHNbeF07XG4gICAgICAgICAgICAgICAgICAgICAgICBmcmFtZURhdGEuZnJhbWVzX29yaWdpbmFsX3VybCA9IGFwcFVJLnNpdGVVcmwrJ3NpdGUvZ2V0ZnJhbWUvJytmcmFtZUlEc1t4XTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGZyYW1lRGF0YS5mcmFtZXNfaGVpZ2h0ID0gaGVpZ2h0c1t4XTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgbmV3QmxvY2suY3JlYXRlRnJhbWUoIGZyYW1lRGF0YSApO1xuICAgICAgICAgICAgICAgICAgICAgICAgbmV3QmxvY2suY3JlYXRlRnJhbWVDb3ZlcigpO1xuICAgICAgICAgICAgICAgICAgICAgICAgbmV3QmxvY2suaW5zZXJ0QmxvY2tJbnRvRG9tKHRoZVBhZ2UucGFyZW50VUwpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL2FkZCB0aGUgYmxvY2sgdG8gdGhlIG5ldyBwYWdlXG4gICAgICAgICAgICAgICAgICAgICAgICB0aGVQYWdlLmJsb2Nrcy5wdXNoKG5ld0Jsb2NrKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9kcm9wcGVkIGVsZW1lbnQsIHNvIHdlJ3ZlIGdvdCBwZW5kaW5nIGNoYW5nZXNcbiAgICAgICAgICAgICAgICAgICAgICAgIHNpdGUuc2V0UGVuZGluZ0NoYW5nZXModHJ1ZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIC8vc2V0IHRoZSB0ZW1wYXRlSURcbiAgICAgICAgICAgICAgICAgICAgYnVpbGRlclVJLnRlbXBsYXRlSUQgPSB1aS5pdGVtLmF0dHIoJ2RhdGEtcGFnZWlkJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9tYWtlIHN1cmUgbm90aGluZyBnZXRzIGRyb3BwZWQgaW4gdGhlIGxzaXRcbiAgICAgICAgICAgICAgICAgICAgdWkuaXRlbS5odG1sKG51bGwpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vZGVsZXRlIGRyYWcgcGxhY2UgaG9sZGVyXG4gICAgICAgICAgICAgICAgICAgICQoJ2JvZHkgLnVpLXNvcnRhYmxlLWhlbHBlcicpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHsvL3JlZ3VsYXIgYmxvY2tcblxuICAgICAgICAgICAgICAgICAgICAvL2FyZSB3ZSBkZWFsaW5nIHdpdGggYSBuZXcgYmxvY2sgYmVpbmcgZHJvcHBlZCBvbnRvIHRoZSBjYW52YXMsIG9yIGEgcmVvcmRlcmluZyBvZyBibG9ja3MgYWxyZWFkeSBvbiB0aGUgY2FudmFzP1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCB1aS5pdGVtLmZpbmQoJy5mcmFtZUNvdmVyID4gYnV0dG9uJykuc2l6ZSgpID4gMCApIHsvL3JlLW9yZGVyaW5nIG9mIGJsb2NrcyBvbiBjYW52YXNcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9ubyBuZWVkIHRvIGNyZWF0ZSBhIG5ldyBibG9jayBvYmplY3QsIHdlIHNpbXBseSBuZWVkIHRvIG1ha2Ugc3VyZSB0aGUgcG9zaXRpb24gb2YgdGhlIGV4aXN0aW5nIGJsb2NrIGluIHRoZSBTaXRlIG9iamVjdFxuICAgICAgICAgICAgICAgICAgICAgICAgLy9pcyBjaGFuZ2VkIHRvIHJlZmxlY3QgdGhlIG5ldyBwb3NpdGlvbiBvZiB0aGUgYmxvY2sgb24gdGggY2FudmFzXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBmcmFtZUlEID0gdWkuaXRlbS5maW5kKCdpZnJhbWUnKS5hdHRyKCdpZCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIG5ld1BvcyA9IHVpLml0ZW0uaW5kZXgoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgc2l0ZS5hY3RpdmVQYWdlLnNldFBvc2l0aW9uKGZyYW1lSUQsIG5ld1Bvcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHsvL25ldyBibG9jayBvbiBjYW52YXNcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9uZXcgYmxvY2tcbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0Jsb2NrID0gbmV3IEJsb2NrKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0Jsb2NrLnBsYWNlT25DYW52YXModWkpO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHN0YXJ0OiBmdW5jdGlvbihldmVudCwgdWkpe1xuXG4gICAgICAgICAgICAgICAgaWYoIHVpLml0ZW0uZmluZCgnLmZyYW1lQ292ZXInKS5zaXplKCkgIT09IDAgKSB7XG4gICAgICAgICAgICAgICAgICAgIGJ1aWxkZXJVSS5mcmFtZUNvbnRlbnRzID0gdWkuaXRlbS5maW5kKCdpZnJhbWUnKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciApLmh0bWwoKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBvdmVyOiBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgJCgnI3N0YXJ0JykuaGlkZSgpO1xuXG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vYWRkIHRvIHRoZSBwYWdlcyBtZW51XG4gICAgICAgIHRoaXMubWVudUl0ZW0gPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdMSScpO1xuICAgICAgICB0aGlzLm1lbnVJdGVtLmlubmVySFRNTCA9IHRoaXMucGFnZU1lbnVUZW1wbGF0ZTtcblxuICAgICAgICAkKHRoaXMubWVudUl0ZW0pLmZpbmQoJ2E6Zmlyc3QnKS50ZXh0KHBhZ2VOYW1lKS5hdHRyKCdocmVmJywgJyNwYWdlJytjb3VudGVyKTtcblxuICAgICAgICB2YXIgdGhlTGluayA9ICQodGhpcy5tZW51SXRlbSkuZmluZCgnYTpmaXJzdCcpLmdldCgwKTtcblxuICAgICAgICAvL2JpbmQgc29tZSBldmVudHNcbiAgICAgICAgdGhpcy5tZW51SXRlbS5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIHRoaXMsIGZhbHNlKTtcblxuICAgICAgICB0aGlzLm1lbnVJdGVtLnF1ZXJ5U2VsZWN0b3IoJ2EuZmlsZUVkaXQnKS5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIHRoaXMsIGZhbHNlKTtcbiAgICAgICAgdGhpcy5tZW51SXRlbS5xdWVyeVNlbGVjdG9yKCdhLmZpbGVTYXZlJykuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCB0aGlzLCBmYWxzZSk7XG4gICAgICAgIHRoaXMubWVudUl0ZW0ucXVlcnlTZWxlY3RvcignYS5maWxlRGVsJykuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCB0aGlzLCBmYWxzZSk7XG5cbiAgICAgICAgLy9hZGQgdG8gdGhlIHBhZ2UgbGluayBkcm9wZG93blxuICAgICAgICB0aGlzLmxpbmtzRHJvcGRvd25JdGVtID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnT1BUSU9OJyk7XG4gICAgICAgIHRoaXMubGlua3NEcm9wZG93bkl0ZW0uc2V0QXR0cmlidXRlKCd2YWx1ZScsIHBhZ2VOYW1lK1wiLmh0bWxcIik7XG4gICAgICAgIHRoaXMubGlua3NEcm9wZG93bkl0ZW0udGV4dCA9IHBhZ2VOYW1lO1xuXG4gICAgICAgIGJ1aWxkZXJVSS5kcm9wZG93blBhZ2VMaW5rcy5hcHBlbmRDaGlsZCggdGhpcy5saW5rc0Ryb3Bkb3duSXRlbSApO1xuXG4gICAgICAgIHNpdGUucGFnZXNNZW51LmFwcGVuZENoaWxkKHRoaXMubWVudUl0ZW0pO1xuXG4gICAgfVxuXG4gICAgUGFnZS5wcm90b3R5cGUuaGFuZGxlRXZlbnQgPSBmdW5jdGlvbihldmVudCkge1xuICAgICAgICBzd2l0Y2ggKGV2ZW50LnR5cGUpIHtcbiAgICAgICAgICAgIGNhc2UgXCJjbGlja1wiOlxuXG4gICAgICAgICAgICAgICAgaWYoIGV2ZW50LnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ2ZpbGVFZGl0JykgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdGhpcy5lZGl0UGFnZU5hbWUoKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggZXZlbnQudGFyZ2V0LmNsYXNzTGlzdC5jb250YWlucygnZmlsZVNhdmUnKSApIHtcblxuICAgICAgICAgICAgICAgICAgICB0aGlzLnVwZGF0ZVBhZ2VOYW1lRXZlbnQoZXZlbnQudGFyZ2V0KTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggZXZlbnQudGFyZ2V0LmNsYXNzTGlzdC5jb250YWlucygnZmlsZURlbCcpICkge1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciB0aGVQYWdlID0gdGhpcztcblxuICAgICAgICAgICAgICAgICAgICAkKGJ1aWxkZXJVSS5tb2RhbERlbGV0ZVBhZ2UpLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgJChidWlsZGVyVUkubW9kYWxEZWxldGVQYWdlKS5vZmYoJ2NsaWNrJywgJyNkZWxldGVQYWdlQ29uZmlybScpLm9uKCdjbGljaycsICcjZGVsZXRlUGFnZUNvbmZpcm0nLCBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdGhlUGFnZS5kZWxldGUoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJChidWlsZGVyVUkubW9kYWxEZWxldGVQYWdlKS5tb2RhbCgnaGlkZScpO1xuXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICAgICB0aGlzLnNlbGVjdFBhZ2UoKTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICB9XG4gICAgfTtcblxuXG4gICAgLypcbiAgICAgICAgQmxvY2sgY29uc3RydWN0b3JcbiAgICAqL1xuICAgIGZ1bmN0aW9uIEJsb2NrICgpIHtcblxuICAgICAgICB0aGlzLmZyYW1lSUQgPSAwO1xuICAgICAgICB0aGlzLnNhbmRib3ggPSBmYWxzZTtcbiAgICAgICAgdGhpcy5zYW5kYm94X2xvYWRlciA9ICcnO1xuICAgICAgICB0aGlzLnN0YXR1cyA9ICcnOy8vJycsICdjaGFuZ2VkJyBvciAnbmV3J1xuICAgICAgICB0aGlzLm9yaWdpbmFsVXJsID0gJyc7XG5cbiAgICAgICAgdGhpcy5wYXJlbnRMSSA9IHt9O1xuICAgICAgICB0aGlzLmZyYW1lQ292ZXIgPSB7fTtcbiAgICAgICAgdGhpcy5mcmFtZSA9IHt9O1xuICAgICAgICB0aGlzLmZyYW1lRG9jdW1lbnQgPSB7fTtcbiAgICAgICAgdGhpcy5mcmFtZUhlaWdodCA9IDA7XG5cbiAgICAgICAgdGhpcy5hbm5vdCA9IHt9O1xuICAgICAgICB0aGlzLmFubm90VGltZW91dCA9IHt9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBjcmVhdGVzIHRoZSBwYXJlbnQgY29udGFpbmVyIChMSSlcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5jcmVhdGVQYXJlbnRMSSA9IGZ1bmN0aW9uKGhlaWdodCkge1xuXG4gICAgICAgICAgICB0aGlzLnBhcmVudExJID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnTEknKTtcbiAgICAgICAgICAgIHRoaXMucGFyZW50TEkuc2V0QXR0cmlidXRlKCdjbGFzcycsICdlbGVtZW50Jyk7XG4gICAgICAgICAgICAvL3RoaXMucGFyZW50TEkuc2V0QXR0cmlidXRlKCdzdHlsZScsICdoZWlnaHQ6ICcraGVpZ2h0KydweCcpO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGNyZWF0ZXMgdGhlIGlmcmFtZSBvbiB0aGUgY2FudmFzXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuY3JlYXRlRnJhbWUgPSBmdW5jdGlvbihmcmFtZSkge1xuXG4gICAgICAgICAgICB0aGlzLmZyYW1lID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnSUZSQU1FJyk7XG4gICAgICAgICAgICB0aGlzLmZyYW1lLnNldEF0dHJpYnV0ZSgnZnJhbWVib3JkZXInLCAwKTtcbiAgICAgICAgICAgIHRoaXMuZnJhbWUuc2V0QXR0cmlidXRlKCdzY3JvbGxpbmcnLCAwKTtcbiAgICAgICAgICAgIHRoaXMuZnJhbWUuc2V0QXR0cmlidXRlKCdzcmMnLCBmcmFtZS5zcmMpO1xuICAgICAgICAgICAgdGhpcy5mcmFtZS5zZXRBdHRyaWJ1dGUoJ2RhdGEtb3JpZ2luYWx1cmwnLCBmcmFtZS5mcmFtZXNfb3JpZ2luYWxfdXJsKTtcbiAgICAgICAgICAgIHRoaXMub3JpZ2luYWxVcmwgPSBmcmFtZS5mcmFtZXNfb3JpZ2luYWxfdXJsO1xuICAgICAgICAgICAgLy90aGlzLmZyYW1lLnNldEF0dHJpYnV0ZSgnZGF0YS1oZWlnaHQnLCBmcmFtZS5mcmFtZXNfaGVpZ2h0KTtcbiAgICAgICAgICAgIC8vdGhpcy5mcmFtZUhlaWdodCA9IGZyYW1lLmZyYW1lc19oZWlnaHQ7XG4gICAgICAgICAgICB0aGlzLmZyYW1lLnNldEF0dHJpYnV0ZSgnc3R5bGUnLCAnYmFja2dyb3VuZDogJytcIiNmZmZmZmYgdXJsKCdcIithcHBVSS5iYXNlVXJsK1wic3JjL2ltYWdlcy9sb2FkaW5nLmdpZicpIDUwJSA1MCUgbm8tcmVwZWF0XCIpO1xuXG4gICAgICAgICAgICAkKHRoaXMuZnJhbWUpLnVuaXF1ZUlkKCk7XG5cbiAgICAgICAgICAgIC8vc2FuZGJveD9cbiAgICAgICAgICAgIGlmKCB0aGlzLnNhbmRib3ggIT09IGZhbHNlICkge1xuXG4gICAgICAgICAgICAgICAgdGhpcy5mcmFtZS5zZXRBdHRyaWJ1dGUoJ2RhdGEtbG9hZGVyZnVuY3Rpb24nLCB0aGlzLnNhbmRib3hfbG9hZGVyKTtcbiAgICAgICAgICAgICAgICB0aGlzLmZyYW1lLnNldEF0dHJpYnV0ZSgnZGF0YS1zYW5kYm94JywgdGhpcy5zYW5kYm94KTtcblxuICAgICAgICAgICAgICAgIC8vcmVjcmVhdGUgdGhlIHNhbmRib3hlZCBpZnJhbWUgZWxzZXdoZXJlXG4gICAgICAgICAgICAgICAgdmFyIHNhbmRib3hlZEZyYW1lID0gJCgnPGlmcmFtZSBzcmM9XCInK2ZyYW1lLnNyYysnXCIgaWQ9XCInK3RoaXMuc2FuZGJveCsnXCIgc2FuZGJveD1cImFsbG93LXNhbWUtb3JpZ2luXCI+PC9pZnJhbWU+Jyk7XG4gICAgICAgICAgICAgICAgJCgnI3NhbmRib3hlcycpLmFwcGVuZCggc2FuZGJveGVkRnJhbWUgKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGluc2VydCB0aGUgaWZyYW1lIGludG8gdGhlIERPTSBvbiB0aGUgY2FudmFzXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuaW5zZXJ0QmxvY2tJbnRvRG9tID0gZnVuY3Rpb24odGhlVUwpIHtcblxuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5hcHBlbmRDaGlsZCh0aGlzLmZyYW1lKTtcbiAgICAgICAgICAgIHRoZVVMLmFwcGVuZENoaWxkKCB0aGlzLnBhcmVudExJICk7XG5cbiAgICAgICAgICAgIHRoaXMuZnJhbWUuYWRkRXZlbnRMaXN0ZW5lcignbG9hZCcsIHRoaXMsIGZhbHNlKTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBzZXRzIHRoZSBmcmFtZSBkb2N1bWVudCBmb3IgdGhlIGJsb2NrJ3MgaWZyYW1lXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuc2V0RnJhbWVEb2N1bWVudCA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL3NldCB0aGUgZnJhbWUgZG9jdW1lbnQgYXMgd2VsbFxuICAgICAgICAgICAgaWYoIHRoaXMuZnJhbWUuY29udGVudERvY3VtZW50ICkge1xuICAgICAgICAgICAgICAgIHRoaXMuZnJhbWVEb2N1bWVudCA9IHRoaXMuZnJhbWUuY29udGVudERvY3VtZW50O1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICB0aGlzLmZyYW1lRG9jdW1lbnQgPSB0aGlzLmZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQ7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vdGhpcy5oZWlnaHRBZGp1c3RtZW50KCk7XG5cbiAgICAgICAgfTtcblxuICAgICAgICAvKlxuICAgICAgICAgICAgY3JlYXRlcyB0aGUgZnJhbWUgY292ZXIgYW5kIGJsb2NrIGFjdGlvbiBidXR0b25cbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5jcmVhdGVGcmFtZUNvdmVyID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIC8vYnVpbGQgdGhlIGZyYW1lIGNvdmVyIGFuZCBibG9jayBhY3Rpb24gYnV0dG9uc1xuICAgICAgICAgICAgdGhpcy5mcmFtZUNvdmVyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnRElWJyk7XG4gICAgICAgICAgICB0aGlzLmZyYW1lQ292ZXIuY2xhc3NMaXN0LmFkZCgnZnJhbWVDb3ZlcicpO1xuICAgICAgICAgICAgdGhpcy5mcmFtZUNvdmVyLmNsYXNzTGlzdC5hZGQoJ2ZyZXNoJyk7XG4gICAgICAgICAgICB0aGlzLmZyYW1lQ292ZXIuc3R5bGUuaGVpZ2h0ID0gdGhpcy5mcmFtZUhlaWdodCtcInB4XCI7XG5cbiAgICAgICAgICAgIHZhciBkZWxCdXR0b24gPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdCVVRUT04nKTtcbiAgICAgICAgICAgIGRlbEJ1dHRvbi5zZXRBdHRyaWJ1dGUoJ2NsYXNzJywgJ2J0biBidG4tZGFuZ2VyIGRlbGV0ZUJsb2NrJyk7XG4gICAgICAgICAgICBkZWxCdXR0b24uc2V0QXR0cmlidXRlKCd0eXBlJywgJ2J1dHRvbicpO1xuICAgICAgICAgICAgZGVsQnV0dG9uLmlubmVySFRNTCA9ICc8c3BhbiBjbGFzcz1cImZ1aS10cmFzaFwiPjwvc3Bhbj4gcmVtb3ZlJztcbiAgICAgICAgICAgIGRlbEJ1dHRvbi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIHRoaXMsIGZhbHNlKTtcblxuICAgICAgICAgICAgdmFyIHJlc2V0QnV0dG9uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnQlVUVE9OJyk7XG4gICAgICAgICAgICByZXNldEJ1dHRvbi5zZXRBdHRyaWJ1dGUoJ2NsYXNzJywgJ2J0biBidG4td2FybmluZyByZXNldEJsb2NrJyk7XG4gICAgICAgICAgICByZXNldEJ1dHRvbi5zZXRBdHRyaWJ1dGUoJ3R5cGUnLCAnYnV0dG9uJyk7XG4gICAgICAgICAgICByZXNldEJ1dHRvbi5pbm5lckhUTUwgPSAnPGkgY2xhc3M9XCJmYSBmYS1yZWZyZXNoXCI+PC9pPiByZXNldCc7XG4gICAgICAgICAgICByZXNldEJ1dHRvbi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIHRoaXMsIGZhbHNlKTtcblxuICAgICAgICAgICAgdmFyIGh0bWxCdXR0b24gPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdCVVRUT04nKTtcbiAgICAgICAgICAgIGh0bWxCdXR0b24uc2V0QXR0cmlidXRlKCdjbGFzcycsICdidG4gYnRuLWludmVyc2UgaHRtbEJsb2NrJyk7XG4gICAgICAgICAgICBodG1sQnV0dG9uLnNldEF0dHJpYnV0ZSgndHlwZScsICdidXR0b24nKTtcbiAgICAgICAgICAgIGh0bWxCdXR0b24uaW5uZXJIVE1MID0gJzxpIGNsYXNzPVwiZmEgZmEtY29kZVwiPjwvaT4gc291cmNlJztcbiAgICAgICAgICAgIGh0bWxCdXR0b24uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCB0aGlzLCBmYWxzZSk7XG5cbiAgICAgICAgICAgIHRoaXMuZnJhbWVDb3Zlci5hcHBlbmRDaGlsZChkZWxCdXR0b24pO1xuICAgICAgICAgICAgdGhpcy5mcmFtZUNvdmVyLmFwcGVuZENoaWxkKHJlc2V0QnV0dG9uKTtcbiAgICAgICAgICAgIHRoaXMuZnJhbWVDb3Zlci5hcHBlbmRDaGlsZChodG1sQnV0dG9uKTtcblxuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5hcHBlbmRDaGlsZCh0aGlzLmZyYW1lQ292ZXIpO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGF1dG9tYXRpY2FsbHkgY29ycmVjdHMgdGhlIGhlaWdodCBvZiB0aGUgYmxvY2sncyBpZnJhbWUgZGVwZW5kaW5nIG9uIGl0cyBjb250ZW50XG4gICAgICAgICovXG4gICAgICAgIHRoaXMuaGVpZ2h0QWRqdXN0bWVudCA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB2YXIgcGFnZUNvbnRhaW5lciA9IHRoaXMuZnJhbWVEb2N1bWVudC5ib2R5O1xuICAgICAgICAgICAgdmFyIGhlaWdodCA9IHBhZ2VDb250YWluZXIuc2Nyb2xsSGVpZ2h0O1xuXG4gICAgICAgICAgICB0aGlzLmZyYW1lLnN0eWxlLmhlaWdodCA9IGhlaWdodCtcInB4XCI7XG4gICAgICAgICAgICB0aGlzLnBhcmVudExJLnN0eWxlLmhlaWdodCA9IGhlaWdodCtcInB4XCI7XG4gICAgICAgICAgICB0aGlzLmZyYW1lQ292ZXIuc3R5bGUuaGVpZ2h0ID0gaGVpZ2h0K1wicHhcIjtcblxuICAgICAgICAgICAgdGhpcy5mcmFtZUhlaWdodCA9IGhlaWdodDtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBkZWxldGVzIGEgYmxvY2tcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5kZWxldGUgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9yZW1vdmUgZnJvbSBET00vY2FudmFzIHdpdGggYSBuaWNlIGFuaW1hdGlvblxuICAgICAgICAgICAgJCh0aGlzLmZyYW1lLnBhcmVudE5vZGUpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgdGhpcy5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgIHNpdGUuYWN0aXZlUGFnZS5pc0VtcHR5KCk7XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvL3JlbW92ZSBmcm9tIGJsb2NrcyBhcnJheSBpbiB0aGUgYWN0aXZlIHBhZ2VcbiAgICAgICAgICAgIHNpdGUuYWN0aXZlUGFnZS5kZWxldGVCbG9jayh0aGlzKTtcblxuICAgICAgICAgICAgLy9zYW5ib3hcbiAgICAgICAgICAgIGlmKCB0aGlzLnNhbmJkb3ggKSB7XG4gICAgICAgICAgICAgICAgZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoIHRoaXMuc2FuZGJveCApLnJlbW92ZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL2VsZW1lbnQgd2FzIGRlbGV0ZWQsIHNvIHdlJ3ZlIGdvdCBwZW5kaW5nIGNoYW5nZVxuICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICByZXNldHMgYSBibG9jayB0byBpdCdzIG9yaWduYWwgc3RhdGVcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5yZXNldCA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL3Jlc2V0IGZyYW1lIGJ5IHJlbG9hZGluZyBpdFxuICAgICAgICAgICAgdGhpcy5mcmFtZS5jb250ZW50V2luZG93LmxvY2F0aW9uLnJlbG9hZCgpO1xuXG4gICAgICAgICAgICAvL3NhbmRib3g/XG4gICAgICAgICAgICBpZiggdGhpcy5zYW5kYm94ICkge1xuICAgICAgICAgICAgICAgIHZhciBzYW5kYm94RnJhbWUgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCh0aGlzLnNhbmRib3gpLmNvbnRlbnRXaW5kb3cubG9jYXRpb24ucmVsb2FkKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vZWxlbWVudCB3YXMgZGVsZXRlZCwgc28gd2UndmUgZ290IHBlbmRpbmcgY2hhbmdlc1xuICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBsYXVuY2hlcyB0aGUgc291cmNlIGNvZGUgZWRpdG9yXG4gICAgICAgICovXG4gICAgICAgIHRoaXMuc291cmNlID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIC8vaGlkZSB0aGUgaWZyYW1lXG4gICAgICAgICAgICB0aGlzLmZyYW1lLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG5cbiAgICAgICAgICAgIC8vZGlzYWJsZSBzb3J0YWJsZSBvbiB0aGUgcGFyZW50TElcbiAgICAgICAgICAgICQodGhpcy5wYXJlbnRMSS5wYXJlbnROb2RlKS5zb3J0YWJsZSgnZGlzYWJsZScpO1xuXG4gICAgICAgICAgICAvL2J1aWx0IGVkaXRvciBlbGVtZW50XG4gICAgICAgICAgICB2YXIgdGhlRWRpdG9yID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnRElWJyk7XG4gICAgICAgICAgICB0aGVFZGl0b3IuY2xhc3NMaXN0LmFkZCgnYWNlRWRpdG9yJyk7XG4gICAgICAgICAgICAkKHRoZUVkaXRvcikudW5pcXVlSWQoKTtcblxuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5hcHBlbmRDaGlsZCh0aGVFZGl0b3IpO1xuXG4gICAgICAgICAgICAvL2J1aWxkIGFuZCBhcHBlbmQgZXJyb3IgZHJhd2VyXG4gICAgICAgICAgICB2YXIgbmV3TEkgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdMSScpO1xuICAgICAgICAgICAgdmFyIGVycm9yRHJhd2VyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnRElWJyk7XG4gICAgICAgICAgICBlcnJvckRyYXdlci5jbGFzc0xpc3QuYWRkKCdlcnJvckRyYXdlcicpO1xuICAgICAgICAgICAgZXJyb3JEcmF3ZXIuc2V0QXR0cmlidXRlKCdpZCcsICdkaXZfZXJyb3JEcmF3ZXInKTtcbiAgICAgICAgICAgIGVycm9yRHJhd2VyLmlubmVySFRNTCA9ICc8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImJ0biBidG4teHMgYnRuLWVtYm9zc2VkIGJ0bi1kZWZhdWx0IGJ1dHRvbl9jbGVhckVycm9yRHJhd2VyXCIgaWQ9XCJidXR0b25fY2xlYXJFcnJvckRyYXdlclwiPkNMRUFSPC9idXR0b24+JztcbiAgICAgICAgICAgIG5ld0xJLmFwcGVuZENoaWxkKGVycm9yRHJhd2VyKTtcbiAgICAgICAgICAgIGVycm9yRHJhd2VyLnF1ZXJ5U2VsZWN0b3IoJ2J1dHRvbicpLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgdGhpcywgZmFsc2UpO1xuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5wYXJlbnROb2RlLmluc2VydEJlZm9yZShuZXdMSSwgdGhpcy5wYXJlbnRMSS5uZXh0U2libGluZyk7XG5cblxuICAgICAgICAgICAgdmFyIHRoZUlkID0gdGhlRWRpdG9yLmdldEF0dHJpYnV0ZSgnaWQnKTtcbiAgICAgICAgICAgIHZhciBlZGl0b3IgPSBhY2UuZWRpdCggdGhlSWQgKTtcblxuICAgICAgICAgICAgdmFyIHBhZ2VDb250YWluZXIgPSB0aGlzLmZyYW1lRG9jdW1lbnQucXVlcnlTZWxlY3RvciggYkNvbmZpZy5wYWdlQ29udGFpbmVyICk7XG4gICAgICAgICAgICB2YXIgdGhlSFRNTCA9IHBhZ2VDb250YWluZXIuaW5uZXJIVE1MO1xuXG4gICAgICAgICAgICBlZGl0b3Iuc2V0VmFsdWUoIHRoZUhUTUwgKTtcbiAgICAgICAgICAgIGVkaXRvci5zZXRUaGVtZShcImFjZS90aGVtZS90d2lsaWdodFwiKTtcbiAgICAgICAgICAgIGVkaXRvci5nZXRTZXNzaW9uKCkuc2V0TW9kZShcImFjZS9tb2RlL2h0bWxcIik7XG5cbiAgICAgICAgICAgIHZhciBibG9jayA9IHRoaXM7XG5cblxuICAgICAgICAgICAgZWRpdG9yLmdldFNlc3Npb24oKS5vbihcImNoYW5nZUFubm90YXRpb25cIiwgZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgIGJsb2NrLmFubm90ID0gZWRpdG9yLmdldFNlc3Npb24oKS5nZXRBbm5vdGF0aW9ucygpO1xuXG4gICAgICAgICAgICAgICAgY2xlYXJUaW1lb3V0KGJsb2NrLmFubm90VGltZW91dCk7XG5cbiAgICAgICAgICAgICAgICB2YXIgdGltZW91dENvdW50O1xuXG4gICAgICAgICAgICAgICAgaWYoICQoJyNkaXZfZXJyb3JEcmF3ZXIgcCcpLnNpemUoKSA9PT0gMCApIHtcbiAgICAgICAgICAgICAgICAgICAgdGltZW91dENvdW50ID0gYkNvbmZpZy5zb3VyY2VDb2RlRWRpdFN5bnRheERlbGF5O1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIHRpbWVvdXRDb3VudCA9IDEwMDtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBibG9jay5hbm5vdFRpbWVvdXQgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgZm9yICh2YXIga2V5IGluIGJsb2NrLmFubm90KXtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGJsb2NrLmFubm90Lmhhc093blByb3BlcnR5KGtleSkpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKCBibG9jay5hbm5vdFtrZXldLnRleHQgIT09IFwiU3RhcnQgdGFnIHNlZW4gd2l0aG91dCBzZWVpbmcgYSBkb2N0eXBlIGZpcnN0LiBFeHBlY3RlZCBlLmcuIDwhRE9DVFlQRSBodG1sPi5cIiApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgbmV3TGluZSA9ICQoJzxwPjwvcD4nKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIG5ld0tleSA9ICQoJzxiPicrYmxvY2suYW5ub3Rba2V5XS50eXBlKyc6IDwvYj4nKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIG5ld0luZm8gPSAkKCc8c3Bhbj4gJytibG9jay5hbm5vdFtrZXldLnRleHQgKyBcIm9uIGxpbmUgXCIgKyBcIiA8Yj5cIiArIGJsb2NrLmFubm90W2tleV0ucm93Kyc8L2I+PC9zcGFuPicpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBuZXdMaW5lLmFwcGVuZCggbmV3S2V5ICk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIG5ld0xpbmUuYXBwZW5kKCBuZXdJbmZvICk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2Rpdl9lcnJvckRyYXdlcicpLmFwcGVuZCggbmV3TGluZSApO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIGlmKCAkKCcjZGl2X2Vycm9yRHJhd2VyJykuY3NzKCdkaXNwbGF5JykgPT09ICdub25lJyAmJiAkKCcjZGl2X2Vycm9yRHJhd2VyJykuZmluZCgncCcpLnNpemUoKSA+IDAgKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjZGl2X2Vycm9yRHJhd2VyJykuc2xpZGVEb3duKCk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH0sIHRpbWVvdXRDb3VudCk7XG5cblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8vYnV0dG9uc1xuICAgICAgICAgICAgdmFyIGNhbmNlbEJ1dHRvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ0JVVFRPTicpO1xuICAgICAgICAgICAgY2FuY2VsQnV0dG9uLnNldEF0dHJpYnV0ZSgndHlwZScsICdidXR0b24nKTtcbiAgICAgICAgICAgIGNhbmNlbEJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdidG4nKTtcbiAgICAgICAgICAgIGNhbmNlbEJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdidG4tZGFuZ2VyJyk7XG4gICAgICAgICAgICBjYW5jZWxCdXR0b24uY2xhc3NMaXN0LmFkZCgnZWRpdENhbmNlbEJ1dHRvbicpO1xuICAgICAgICAgICAgY2FuY2VsQnV0dG9uLmNsYXNzTGlzdC5hZGQoJ2J0bi13aWRlJyk7XG4gICAgICAgICAgICBjYW5jZWxCdXR0b24uaW5uZXJIVE1MID0gJzxzcGFuIGNsYXNzPVwiZnVpLWNyb3NzXCI+PC9zcGFuPiBDYW5jZWwnO1xuICAgICAgICAgICAgY2FuY2VsQnV0dG9uLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgdGhpcywgZmFsc2UpO1xuXG4gICAgICAgICAgICB2YXIgc2F2ZUJ1dHRvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ0JVVFRPTicpO1xuICAgICAgICAgICAgc2F2ZUJ1dHRvbi5zZXRBdHRyaWJ1dGUoJ3R5cGUnLCAnYnV0dG9uJyk7XG4gICAgICAgICAgICBzYXZlQnV0dG9uLmNsYXNzTGlzdC5hZGQoJ2J0bicpO1xuICAgICAgICAgICAgc2F2ZUJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdidG4tcHJpbWFyeScpO1xuICAgICAgICAgICAgc2F2ZUJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdlZGl0U2F2ZUJ1dHRvbicpO1xuICAgICAgICAgICAgc2F2ZUJ1dHRvbi5jbGFzc0xpc3QuYWRkKCdidG4td2lkZScpO1xuICAgICAgICAgICAgc2F2ZUJ1dHRvbi5pbm5lckhUTUwgPSAnPHNwYW4gY2xhc3M9XCJmdWktY2hlY2tcIj48L3NwYW4+IFNhdmUnO1xuICAgICAgICAgICAgc2F2ZUJ1dHRvbi5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIHRoaXMsIGZhbHNlKTtcblxuICAgICAgICAgICAgdmFyIGJ1dHRvbldyYXBwZXIgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdESVYnKTtcbiAgICAgICAgICAgIGJ1dHRvbldyYXBwZXIuY2xhc3NMaXN0LmFkZCgnZWRpdG9yQnV0dG9ucycpO1xuXG4gICAgICAgICAgICBidXR0b25XcmFwcGVyLmFwcGVuZENoaWxkKCBjYW5jZWxCdXR0b24gKTtcbiAgICAgICAgICAgIGJ1dHRvbldyYXBwZXIuYXBwZW5kQ2hpbGQoIHNhdmVCdXR0b24gKTtcblxuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5hcHBlbmRDaGlsZCggYnV0dG9uV3JhcHBlciApO1xuXG4gICAgICAgICAgICBidWlsZGVyVUkuYWNlRWRpdG9yc1sgdGhlSWQgXSA9IGVkaXRvcjtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBjYW5jZWxzIHRoZSBibG9jayBzb3VyY2UgY29kZSBlZGl0b3JcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5jYW5jZWxTb3VyY2VCbG9jayA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2VuYWJsZSBkcmFnZ2FibGUgb24gdGhlIExJXG4gICAgICAgICAgICAkKHRoaXMucGFyZW50TEkucGFyZW50Tm9kZSkuc29ydGFibGUoJ2VuYWJsZScpO1xuXG4gICAgICAgICAgICAvL2RlbGV0ZSB0aGUgZXJyb3JEcmF3ZXJcbiAgICAgICAgICAgICQodGhpcy5wYXJlbnRMSS5uZXh0U2libGluZykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgIC8vZGVsZXRlIHRoZSBlZGl0b3JcbiAgICAgICAgICAgIHRoaXMucGFyZW50TEkucXVlcnlTZWxlY3RvcignLmFjZUVkaXRvcicpLnJlbW92ZSgpO1xuICAgICAgICAgICAgJCh0aGlzLmZyYW1lKS5mYWRlSW4oNTAwKTtcblxuICAgICAgICAgICAgJCh0aGlzLnBhcmVudExJLnF1ZXJ5U2VsZWN0b3IoJy5lZGl0b3JCdXR0b25zJykpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlKCk7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICB1cGRhdGVzIHRoZSBibG9ja3Mgc291cmNlIGNvZGVcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5zYXZlU291cmNlQmxvY2sgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9lbmFibGUgZHJhZ2dhYmxlIG9uIHRoZSBMSVxuICAgICAgICAgICAgJCh0aGlzLnBhcmVudExJLnBhcmVudE5vZGUpLnNvcnRhYmxlKCdlbmFibGUnKTtcblxuICAgICAgICAgICAgdmFyIHRoZUlkID0gdGhpcy5wYXJlbnRMSS5xdWVyeVNlbGVjdG9yKCcuYWNlRWRpdG9yJykuZ2V0QXR0cmlidXRlKCdpZCcpO1xuICAgICAgICAgICAgdmFyIHRoZUNvbnRlbnQgPSBidWlsZGVyVUkuYWNlRWRpdG9yc1t0aGVJZF0uZ2V0VmFsdWUoKTtcblxuICAgICAgICAgICAgLy9kZWxldGUgdGhlIGVycm9yRHJhd2VyXG4gICAgICAgICAgICBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZGl2X2Vycm9yRHJhd2VyJykucGFyZW50Tm9kZS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgLy9kZWxldGUgdGhlIGVkaXRvclxuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5xdWVyeVNlbGVjdG9yKCcuYWNlRWRpdG9yJykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgIC8vdXBkYXRlIHRoZSBmcmFtZSdzIGNvbnRlbnRcbiAgICAgICAgICAgIHRoaXMuZnJhbWVEb2N1bWVudC5xdWVyeVNlbGVjdG9yKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKS5pbm5lckhUTUwgPSB0aGVDb250ZW50O1xuICAgICAgICAgICAgdGhpcy5mcmFtZS5zdHlsZS5kaXNwbGF5ID0gJ2Jsb2NrJztcblxuICAgICAgICAgICAgLy9zYW5kYm94ZWQ/XG4gICAgICAgICAgICBpZiggdGhpcy5zYW5kYm94ICkge1xuXG4gICAgICAgICAgICAgICAgdmFyIHNhbmRib3hGcmFtZSA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCB0aGlzLnNhbmRib3ggKTtcbiAgICAgICAgICAgICAgICB2YXIgc2FuZGJveEZyYW1lRG9jdW1lbnQgPSBzYW5kYm94RnJhbWUuY29udGVudERvY3VtZW50IHx8IHNhbmRib3hGcmFtZS5jb250ZW50V2luZG93LmRvY3VtZW50O1xuXG4gICAgICAgICAgICAgICAgYnVpbGRlclVJLnRlbXBGcmFtZSA9IHNhbmRib3hGcmFtZTtcblxuICAgICAgICAgICAgICAgIHNhbmRib3hGcmFtZURvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoIGJDb25maWcucGFnZUNvbnRhaW5lciApLmlubmVySFRNTCA9IHRoZUNvbnRlbnQ7XG5cbiAgICAgICAgICAgICAgICAvL2RvIHdlIG5lZWQgdG8gZXhlY3V0ZSBhIGxvYWRlciBmdW5jdGlvbj9cbiAgICAgICAgICAgICAgICBpZiggdGhpcy5zYW5kYm94X2xvYWRlciAhPT0gJycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgLypcbiAgICAgICAgICAgICAgICAgICAgdmFyIGNvZGVUb0V4ZWN1dGUgPSBcInNhbmRib3hGcmFtZS5jb250ZW50V2luZG93LlwiK3RoaXMuc2FuZGJveF9sb2FkZXIrXCIoKVwiO1xuICAgICAgICAgICAgICAgICAgICB2YXIgdG1wRnVuYyA9IG5ldyBGdW5jdGlvbihjb2RlVG9FeGVjdXRlKTtcbiAgICAgICAgICAgICAgICAgICAgdG1wRnVuYygpO1xuICAgICAgICAgICAgICAgICAgICAqL1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICQodGhpcy5wYXJlbnRMSS5xdWVyeVNlbGVjdG9yKCcuZWRpdG9yQnV0dG9ucycpKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8vYWRqdXN0IGhlaWdodCBvZiB0aGUgZnJhbWVcbiAgICAgICAgICAgIHRoaXMuaGVpZ2h0QWRqdXN0bWVudCgpO1xuXG4gICAgICAgICAgICAvL25ldyBwYWdlIGFkZGVkLCB3ZSd2ZSBnb3QgcGVuZGluZyBjaGFuZ2VzXG4gICAgICAgICAgICBzaXRlLnNldFBlbmRpbmdDaGFuZ2VzKHRydWUpO1xuXG4gICAgICAgICAgICAvL2Jsb2NrIGhhcyBjaGFuZ2VkXG4gICAgICAgICAgICB0aGlzLnN0YXR1cyA9ICdjaGFuZ2VkJztcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBjbGVhcnMgb3V0IHRoZSBlcnJvciBkcmF3ZXJcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5jbGVhckVycm9yRHJhd2VyID0gZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciBwcyA9IHRoaXMucGFyZW50TEkubmV4dFNpYmxpbmcucXVlcnlTZWxlY3RvckFsbCgncCcpO1xuXG4gICAgICAgICAgICBmb3IoIHZhciBpID0gMDsgaSA8IHBzLmxlbmd0aDsgaSsrICkge1xuICAgICAgICAgICAgICAgIHBzW2ldLnJlbW92ZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHRvZ2dsZXMgdGhlIHZpc2liaWxpdHkgb2YgdGhpcyBibG9jaydzIGZyYW1lQ292ZXJcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy50b2dnbGVDb3ZlciA9IGZ1bmN0aW9uKG9uT3JPZmYpIHtcblxuICAgICAgICAgICAgaWYoIG9uT3JPZmYgPT09ICdPbicgKSB7XG5cbiAgICAgICAgICAgICAgICB0aGlzLnBhcmVudExJLnF1ZXJ5U2VsZWN0b3IoJy5mcmFtZUNvdmVyJykuc3R5bGUuZGlzcGxheSA9ICdibG9jayc7XG5cbiAgICAgICAgICAgIH0gZWxzZSBpZiggb25Pck9mZiA9PT0gJ09mZicgKSB7XG5cbiAgICAgICAgICAgICAgICB0aGlzLnBhcmVudExJLnF1ZXJ5U2VsZWN0b3IoJy5mcmFtZUNvdmVyJykuc3R5bGUuZGlzcGxheSA9ICdub25lJztcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHJldHVybnMgdGhlIGZ1bGwgc291cmNlIGNvZGUgb2YgdGhlIGJsb2NrJ3MgZnJhbWVcbiAgICAgICAgKi9cbiAgICAgICAgdGhpcy5nZXRTb3VyY2UgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIHNvdXJjZSA9IFwiPGh0bWw+XCI7XG4gICAgICAgICAgICBzb3VyY2UgKz0gdGhpcy5mcmFtZURvY3VtZW50LmhlYWQub3V0ZXJIVE1MO1xuICAgICAgICAgICAgc291cmNlICs9IHRoaXMuZnJhbWVEb2N1bWVudC5ib2R5Lm91dGVySFRNTDtcblxuICAgICAgICAgICAgcmV0dXJuIHNvdXJjZTtcblxuICAgICAgICB9O1xuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBwbGFjZXMgYSBkcmFnZ2VkL2Ryb3BwZWQgYmxvY2sgZnJvbSB0aGUgbGVmdCBzaWRlYmFyIG9udG8gdGhlIGNhbnZhc1xuICAgICAgICAqL1xuICAgICAgICB0aGlzLnBsYWNlT25DYW52YXMgPSBmdW5jdGlvbih1aSkge1xuXG4gICAgICAgICAgICAvL2ZyYW1lIGRhdGEsIHdlJ2xsIG5lZWQgdGhpcyBiZWZvcmUgbWVzc2luZyB3aXRoIHRoZSBpdGVtJ3MgY29udGVudCBIVE1MXG4gICAgICAgICAgICB2YXIgZnJhbWVEYXRhID0ge30sIGF0dHI7XG5cbiAgICAgICAgICAgIGlmKCB1aS5pdGVtLmZpbmQoJ2lmcmFtZScpLnNpemUoKSA+IDAgKSB7Ly9pZnJhbWUgdGh1bWJuYWlsXG5cbiAgICAgICAgICAgICAgICBmcmFtZURhdGEuc3JjID0gdWkuaXRlbS5maW5kKCdpZnJhbWUnKS5hdHRyKCdzcmMnKTtcbiAgICAgICAgICAgICAgICBmcmFtZURhdGEuZnJhbWVzX29yaWdpbmFsX3VybCA9IHVpLml0ZW0uZmluZCgnaWZyYW1lJykuYXR0cignc3JjJyk7XG4gICAgICAgICAgICAgICAgZnJhbWVEYXRhLmZyYW1lc19oZWlnaHQgPSB1aS5pdGVtLmhlaWdodCgpO1xuXG4gICAgICAgICAgICAgICAgLy9zYW5kYm94ZWQgYmxvY2s/XG4gICAgICAgICAgICAgICAgYXR0ciA9IHVpLml0ZW0uZmluZCgnaWZyYW1lJykuYXR0cignc2FuZGJveCcpO1xuXG4gICAgICAgICAgICAgICAgaWYgKHR5cGVvZiBhdHRyICE9PSB0eXBlb2YgdW5kZWZpbmVkICYmIGF0dHIgIT09IGZhbHNlKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoaXMuc2FuZGJveCA9IHNpdGVCdWlsZGVyVXRpbHMuZ2V0UmFuZG9tQXJiaXRyYXJ5KDEwMDAwLCAxMDAwMDAwMDAwKTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zYW5kYm94X2xvYWRlciA9IHVpLml0ZW0uZmluZCgnaWZyYW1lJykuYXR0cignZGF0YS1sb2FkZXJmdW5jdGlvbicpO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSBlbHNlIHsvL2ltYWdlIHRodW1ibmFpbFxuXG4gICAgICAgICAgICAgICAgZnJhbWVEYXRhLnNyYyA9IHVpLml0ZW0uZmluZCgnaW1nJykuYXR0cignZGF0YS1zcmNjJyk7XG4gICAgICAgICAgICAgICAgZnJhbWVEYXRhLmZyYW1lc19vcmlnaW5hbF91cmwgPSB1aS5pdGVtLmZpbmQoJ2ltZycpLmF0dHIoJ2RhdGEtc3JjYycpO1xuICAgICAgICAgICAgICAgIGZyYW1lRGF0YS5mcmFtZXNfaGVpZ2h0ID0gdWkuaXRlbS5maW5kKCdpbWcnKS5hdHRyKCdkYXRhLWhlaWdodCcpO1xuXG4gICAgICAgICAgICAgICAgLy9zYW5kYm94ZWQgYmxvY2s/XG4gICAgICAgICAgICAgICAgYXR0ciA9IHVpLml0ZW0uZmluZCgnaW1nJykuYXR0cignZGF0YS1zYW5kYm94Jyk7XG5cbiAgICAgICAgICAgICAgICBpZiAodHlwZW9mIGF0dHIgIT09IHR5cGVvZiB1bmRlZmluZWQgJiYgYXR0ciAhPT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zYW5kYm94ID0gc2l0ZUJ1aWxkZXJVdGlscy5nZXRSYW5kb21BcmJpdHJhcnkoMTAwMDAsIDEwMDAwMDAwMDApO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNhbmRib3hfbG9hZGVyID0gdWkuaXRlbS5maW5kKCdpbWcnKS5hdHRyKCdkYXRhLWxvYWRlcmZ1bmN0aW9uJyk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vY3JlYXRlIHRoZSBuZXcgYmxvY2sgb2JqZWN0XG4gICAgICAgICAgICB0aGlzLmZyYW1lSUQgPSAwO1xuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSSA9IHVpLml0ZW0uZ2V0KDApO1xuICAgICAgICAgICAgdGhpcy5wYXJlbnRMSS5pbm5lckhUTUwgPSAnJztcbiAgICAgICAgICAgIHRoaXMuc3RhdHVzID0gJ25ldyc7XG4gICAgICAgICAgICB0aGlzLmNyZWF0ZUZyYW1lKGZyYW1lRGF0YSk7XG4gICAgICAgICAgICB0aGlzLnBhcmVudExJLnN0eWxlLmhlaWdodCA9IHRoaXMuZnJhbWVIZWlnaHQrXCJweFwiO1xuICAgICAgICAgICAgdGhpcy5jcmVhdGVGcmFtZUNvdmVyKCk7XG5cbiAgICAgICAgICAgIHRoaXMuZnJhbWUuYWRkRXZlbnRMaXN0ZW5lcignbG9hZCcsIHRoaXMpO1xuXG4gICAgICAgICAgICAvL2luc2VydCB0aGUgY3JlYXRlZCBpZnJhbWVcbiAgICAgICAgICAgIHVpLml0ZW0uYXBwZW5kKCQodGhpcy5mcmFtZSkpO1xuXG4gICAgICAgICAgICAvL2FkZCB0aGUgYmxvY2sgdG8gdGhlIGN1cnJlbnQgcGFnZVxuICAgICAgICAgICAgc2l0ZS5hY3RpdmVQYWdlLmJsb2Nrcy5zcGxpY2UodWkuaXRlbS5pbmRleCgpLCAwLCB0aGlzKTtcblxuICAgICAgICAgICAgLy9jdXN0b20gZXZlbnRcbiAgICAgICAgICAgIHVpLml0ZW0uZmluZCgnaWZyYW1lJykudHJpZ2dlcignY2FudmFzdXBkYXRlZCcpO1xuXG4gICAgICAgICAgICAvL2Ryb3BwZWQgZWxlbWVudCwgc28gd2UndmUgZ290IHBlbmRpbmcgY2hhbmdlc1xuICAgICAgICAgICAgc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICB9O1xuXG5cbiAgICB9XG5cbiAgICBCbG9jay5wcm90b3R5cGUuaGFuZGxlRXZlbnQgPSBmdW5jdGlvbihldmVudCkge1xuICAgICAgICBzd2l0Y2ggKGV2ZW50LnR5cGUpIHtcbiAgICAgICAgICAgIGNhc2UgXCJsb2FkXCI6XG4gICAgICAgICAgICAgICAgdGhpcy5zZXRGcmFtZURvY3VtZW50KCk7XG4gICAgICAgICAgICAgICAgdGhpcy5oZWlnaHRBZGp1c3RtZW50KCk7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMuZnJhbWVDb3ZlcikucmVtb3ZlQ2xhc3MoJ2ZyZXNoJywgNTAwKTtcblxuICAgICAgICAgICAgICAgIGJyZWFrO1xuXG4gICAgICAgICAgICBjYXNlIFwiY2xpY2tcIjpcblxuICAgICAgICAgICAgICAgIHZhciB0aGVCbG9jayA9IHRoaXM7XG5cbiAgICAgICAgICAgICAgICAvL2ZpZ3VyZSBvdXQgd2hhdCB0byBkbyBuZXh0XG5cbiAgICAgICAgICAgICAgICBpZiggZXZlbnQudGFyZ2V0LmNsYXNzTGlzdC5jb250YWlucygnZGVsZXRlQmxvY2snKSApIHsvL2RlbGV0ZSB0aGlzIGJsb2NrXG5cbiAgICAgICAgICAgICAgICAgICAgJChidWlsZGVyVUkubW9kYWxEZWxldGVCbG9jaykubW9kYWwoJ3Nob3cnKTtcblxuICAgICAgICAgICAgICAgICAgICAkKGJ1aWxkZXJVSS5tb2RhbERlbGV0ZUJsb2NrKS5vZmYoJ2NsaWNrJywgJyNkZWxldGVCbG9ja0NvbmZpcm0nKS5vbignY2xpY2snLCAnI2RlbGV0ZUJsb2NrQ29uZmlybScsIGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgICAgICAgICB0aGVCbG9jay5kZWxldGUoZXZlbnQpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJChidWlsZGVyVUkubW9kYWxEZWxldGVCbG9jaykubW9kYWwoJ2hpZGUnKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIGV2ZW50LnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ3Jlc2V0QmxvY2snKSApIHsvL3Jlc2V0IHRoZSBibG9ja1xuXG4gICAgICAgICAgICAgICAgICAgICQoYnVpbGRlclVJLm1vZGFsUmVzZXRCbG9jaykubW9kYWwoJ3Nob3cnKTtcblxuICAgICAgICAgICAgICAgICAgICAkKGJ1aWxkZXJVSS5tb2RhbFJlc2V0QmxvY2spLm9mZignY2xpY2snLCAnI3Jlc2V0QmxvY2tDb25maXJtJykub24oJ2NsaWNrJywgJyNyZXNldEJsb2NrQ29uZmlybScsIGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgICAgICAgICB0aGVCbG9jay5yZXNldCgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJChidWlsZGVyVUkubW9kYWxSZXNldEJsb2NrKS5tb2RhbCgnaGlkZScpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggZXZlbnQudGFyZ2V0LmNsYXNzTGlzdC5jb250YWlucygnaHRtbEJsb2NrJykgKSB7Ly9zb3VyY2UgY29kZSBlZGl0b3JcblxuICAgICAgICAgICAgICAgICAgICB0aGVCbG9jay5zb3VyY2UoKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggZXZlbnQudGFyZ2V0LmNsYXNzTGlzdC5jb250YWlucygnZWRpdENhbmNlbEJ1dHRvbicpICkgey8vY2FuY2VsIHNvdXJjZSBjb2RlIGVkaXRvclxuXG4gICAgICAgICAgICAgICAgICAgIHRoZUJsb2NrLmNhbmNlbFNvdXJjZUJsb2NrKCk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIGV2ZW50LnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ2VkaXRTYXZlQnV0dG9uJykgKSB7Ly9zYXZlIHNvdXJjZSBjb2RlXG5cbiAgICAgICAgICAgICAgICAgICAgdGhlQmxvY2suc2F2ZVNvdXJjZUJsb2NrKCk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIGV2ZW50LnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ2J1dHRvbl9jbGVhckVycm9yRHJhd2VyJykgKSB7Ly9jbGVhciBlcnJvciBkcmF3ZXJcblxuICAgICAgICAgICAgICAgICAgICB0aGVCbG9jay5jbGVhckVycm9yRHJhd2VyKCk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgfVxuICAgIH07XG5cblxuICAgIC8qXG4gICAgICAgIFNpdGUgb2JqZWN0IGxpdGVyYWxcbiAgICAqL1xuICAgIC8qanNoaW50IC1XMDAzICovXG4gICAgdmFyIHNpdGUgPSB7XG5cbiAgICAgICAgcGVuZGluZ0NoYW5nZXM6IGZhbHNlLCAgICAgIC8vcGVuZGluZyBjaGFuZ2VzIG9yIG5vP1xuICAgICAgICBwYWdlczoge30sICAgICAgICAgICAgICAgICAgLy9hcnJheSBjb250YWluaW5nIGFsbCBwYWdlcywgaW5jbHVkaW5nIHRoZSBjaGlsZCBmcmFtZXMsIGxvYWRlZCBmcm9tIHRoZSBzZXJ2ZXIgb24gcGFnZSBsb2FkXG4gICAgICAgIGlzX2FkbWluOiAwLCAgICAgICAgICAgICAgICAvLzAgZm9yIG5vbi1hZG1pbiwgMSBmb3IgYWRtaW5cbiAgICAgICAgZGF0YToge30sICAgICAgICAgICAgICAgICAgIC8vY29udGFpbmVyIGZvciBhamF4IGxvYWRlZCBzaXRlIGRhdGFcbiAgICAgICAgcGFnZXNUb0RlbGV0ZTogW10sICAgICAgICAgIC8vY29udGFpbnMgcGFnZXMgdG8gYmUgZGVsZXRlZFxuXG4gICAgICAgIHNpdGVQYWdlczogW10sICAgICAgICAgICAgICAvL3RoaXMgaXMgdGhlIG9ubHkgdmFyIGNvbnRhaW5pbmcgdGhlIHJlY2VudCBjYW52YXMgY29udGVudHNcblxuICAgICAgICBzaXRlUGFnZXNSZWFkeUZvclNlcnZlcjoge30sICAgICAvL2NvbnRhaW5zIHRoZSBzaXRlIGRhdGEgcmVhZHkgdG8gYmUgc2VudCB0byB0aGUgc2VydmVyXG5cbiAgICAgICAgYWN0aXZlUGFnZToge30sICAgICAgICAgICAgIC8vaG9sZHMgYSByZWZlcmVuY2UgdG8gdGhlIHBhZ2UgY3VycmVudGx5IG9wZW4gb24gdGhlIGNhbnZhc1xuXG4gICAgICAgIHBhZ2VUaXRsZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3BhZ2VUaXRsZScpLC8vaG9sZHMgdGhlIHBhZ2UgdGl0bGUgb2YgdGhlIGN1cnJlbnQgcGFnZSBvbiB0aGUgY2FudmFzXG5cbiAgICAgICAgZGl2Q2FudmFzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncGFnZUxpc3QnKSwvL0RJViBjb250YWluaW5nIGFsbCBwYWdlcyBvbiB0aGUgY2FudmFzXG5cbiAgICAgICAgcGFnZXNNZW51OiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncGFnZXMnKSwgLy9VTCBjb250YWluaW5nIHRoZSBwYWdlcyBtZW51IGluIHRoZSBzaWRlYmFyXG5cbiAgICAgICAgYnV0dG9uTmV3UGFnZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2FkZFBhZ2UnKSxcbiAgICAgICAgbGlOZXdQYWdlOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbmV3UGFnZUxJJyksXG5cbiAgICAgICAgaW5wdXRQYWdlU2V0dGluZ3NUaXRsZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3BhZ2VEYXRhX3RpdGxlJyksXG4gICAgICAgIGlucHV0UGFnZVNldHRpbmdzTWV0YURlc2NyaXB0aW9uOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncGFnZURhdGFfbWV0YURlc2NyaXB0aW9uJyksXG4gICAgICAgIGlucHV0UGFnZVNldHRpbmdzTWV0YUtleXdvcmRzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncGFnZURhdGFfbWV0YUtleXdvcmRzJyksXG4gICAgICAgIGlucHV0UGFnZVNldHRpbmdzSW5jbHVkZXM6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwYWdlRGF0YV9oZWFkZXJJbmNsdWRlcycpLFxuICAgICAgICBpbnB1dFBhZ2VTZXR0aW5nc1BhZ2VDc3M6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwYWdlRGF0YV9oZWFkZXJDc3MnKSxcblxuICAgICAgICBidXR0b25TdWJtaXRQYWdlU2V0dGluZ3M6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwYWdlU2V0dGluZ3NTdWJtaXR0QnV0dG9uJyksXG5cbiAgICAgICAgbW9kYWxQYWdlU2V0dGluZ3M6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwYWdlU2V0dGluZ3NNb2RhbCcpLFxuXG4gICAgICAgIGJ1dHRvblNhdmU6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdzYXZlUGFnZScpLFxuXG4gICAgICAgIG1lc3NhZ2VTdGFydDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3N0YXJ0JyksXG4gICAgICAgIGRpdkZyYW1lV3JhcHBlcjogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2ZyYW1lV3JhcHBlcicpLFxuXG4gICAgICAgIHNrZWxldG9uOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2tlbGV0b24nKSxcblxuXHRcdGF1dG9TYXZlVGltZXI6IHt9LFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkLmdldEpTT04oYXBwVUkuc2l0ZVVybCtcInNpdGVEYXRhXCIsIGZ1bmN0aW9uKGRhdGEpe1xuXG4gICAgICAgICAgICAgICAgaWYoIGRhdGEuc2l0ZSAhPT0gdW5kZWZpbmVkICkge1xuICAgICAgICAgICAgICAgICAgICBzaXRlLmRhdGEgPSBkYXRhLnNpdGU7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGlmKCBkYXRhLnBhZ2VzICE9PSB1bmRlZmluZWQgKSB7XG4gICAgICAgICAgICAgICAgICAgIHNpdGUucGFnZXMgPSBkYXRhLnBhZ2VzO1xuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIHNpdGUuaXNfYWRtaW4gPSBkYXRhLmlzX2FkbWluO1xuXG5cdFx0XHRcdGlmKCAkKCcjcGFnZUxpc3QnKS5zaXplKCkgPiAwICkge1xuICAgICAgICAgICAgICAgIFx0YnVpbGRlclVJLnBvcHVsYXRlQ2FudmFzKCk7XG5cdFx0XHRcdH1cblxuICAgICAgICAgICAgICAgIC8vZmlyZSBjdXN0b20gZXZlbnRcbiAgICAgICAgICAgICAgICAkKCdib2R5JykudHJpZ2dlcignc2l0ZURhdGFMb2FkZWQnKTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICQodGhpcy5idXR0b25OZXdQYWdlKS5vbignY2xpY2snLCBzaXRlLm5ld1BhZ2UpO1xuICAgICAgICAgICAgJCh0aGlzLm1vZGFsUGFnZVNldHRpbmdzKS5vbignc2hvdy5icy5tb2RhbCcsIHNpdGUubG9hZFBhZ2VTZXR0aW5ncyk7XG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uU3VibWl0UGFnZVNldHRpbmdzKS5vbignY2xpY2snLCBzaXRlLnVwZGF0ZVBhZ2VTZXR0aW5ncyk7XG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uU2F2ZSkub24oJ2NsaWNrJywgZnVuY3Rpb24oKXtzaXRlLnNhdmUodHJ1ZSk7fSk7XG5cbiAgICAgICAgICAgIC8vYXV0byBzYXZlIHRpbWVcbiAgICAgICAgICAgIHRoaXMuYXV0b1NhdmVUaW1lciA9IHNldFRpbWVvdXQoc2l0ZS5hdXRvU2F2ZSwgYkNvbmZpZy5hdXRvU2F2ZVRpbWVvdXQpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgYXV0b1NhdmU6IGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgIGlmKHNpdGUucGVuZGluZ0NoYW5nZXMpIHtcbiAgICAgICAgICAgICAgICBzaXRlLnNhdmUoZmFsc2UpO1xuICAgICAgICAgICAgfVxuXG5cdFx0XHR3aW5kb3cuY2xlYXJJbnRlcnZhbCh0aGlzLmF1dG9TYXZlVGltZXIpO1xuICAgICAgICAgICAgdGhpcy5hdXRvU2F2ZVRpbWVyID0gc2V0VGltZW91dChzaXRlLmF1dG9TYXZlLCBiQ29uZmlnLmF1dG9TYXZlVGltZW91dCk7XG5cbiAgICAgICAgfSxcblxuICAgICAgICBzZXRQZW5kaW5nQ2hhbmdlczogZnVuY3Rpb24odmFsdWUpIHtcblxuICAgICAgICAgICAgdGhpcy5wZW5kaW5nQ2hhbmdlcyA9IHZhbHVlO1xuXG4gICAgICAgICAgICBpZiggdmFsdWUgPT09IHRydWUgKSB7XG5cblx0XHRcdFx0Ly9yZXNldCB0aW1lclxuXHRcdFx0XHR3aW5kb3cuY2xlYXJJbnRlcnZhbCh0aGlzLmF1dG9TYXZlVGltZXIpO1xuICAgICAgICAgICAgXHR0aGlzLmF1dG9TYXZlVGltZXIgPSBzZXRUaW1lb3V0KHNpdGUuYXV0b1NhdmUsIGJDb25maWcuYXV0b1NhdmVUaW1lb3V0KTtcblxuICAgICAgICAgICAgICAgICQoJyNzYXZlUGFnZSAuYkxhYmVsJykudGV4dChcIlNhdmUgbm93ICghKVwiKTtcblxuICAgICAgICAgICAgICAgIGlmKCBzaXRlLmFjdGl2ZVBhZ2Uuc3RhdHVzICE9PSAnbmV3JyApIHtcblxuICAgICAgICAgICAgICAgICAgICBzaXRlLmFjdGl2ZVBhZ2Uuc3RhdHVzID0gJ2NoYW5nZWQnO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgJCgnI3NhdmVQYWdlIC5iTGFiZWwnKS50ZXh0KFwiTm90aGluZyB0byBzYXZlXCIpO1xuXG4gICAgICAgICAgICAgICAgc2l0ZS51cGRhdGVQYWdlU3RhdHVzKCcnKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cbiAgICAgICAgc2F2ZTogZnVuY3Rpb24oc2hvd0NvbmZpcm1Nb2RhbCkge1xuXG4gICAgICAgICAgICAvL2ZpcmUgY3VzdG9tIGV2ZW50XG4gICAgICAgICAgICAkKCdib2R5JykudHJpZ2dlcignYmVmb3JlU2F2ZScpO1xuXG4gICAgICAgICAgICAvL2Rpc2FibGUgYnV0dG9uXG4gICAgICAgICAgICAkKFwiYSNzYXZlUGFnZVwiKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgLy9yZW1vdmUgb2xkIGFsZXJ0c1xuICAgICAgICAgICAgJCgnI2Vycm9yTW9kYWwgLm1vZGFsLWJvZHkgPiAqLCAjc3VjY2Vzc01vZGFsIC5tb2RhbC1ib2R5ID4gKicpLmVhY2goZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHNpdGUucHJlcEZvclNhdmUoZmFsc2UpO1xuXG4gICAgICAgICAgICB2YXIgc2VydmVyRGF0YSA9IHt9O1xuICAgICAgICAgICAgc2VydmVyRGF0YS5wYWdlcyA9IHRoaXMuc2l0ZVBhZ2VzUmVhZHlGb3JTZXJ2ZXI7XG4gICAgICAgICAgICBpZiggdGhpcy5wYWdlc1RvRGVsZXRlLmxlbmd0aCA+IDAgKSB7XG4gICAgICAgICAgICAgICAgc2VydmVyRGF0YS50b0RlbGV0ZSA9IHRoaXMucGFnZXNUb0RlbGV0ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHNlcnZlckRhdGEuc2l0ZURhdGEgPSB0aGlzLmRhdGE7XG5cbiAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgdXJsOiBhcHBVSS5zaXRlVXJsK1wic2l0ZS9zYXZlXCIsXG4gICAgICAgICAgICAgICAgdHlwZTogXCJQT1NUXCIsXG4gICAgICAgICAgICAgICAgZGF0YVR5cGU6IFwianNvblwiLFxuICAgICAgICAgICAgICAgIGRhdGE6IHNlcnZlckRhdGEsXG4gICAgICAgICAgICB9KS5kb25lKGZ1bmN0aW9uKHJlcyl7XG5cbiAgICAgICAgICAgICAgICAvL2VuYWJsZSBidXR0b25cbiAgICAgICAgICAgICAgICAkKFwiYSNzYXZlUGFnZVwiKS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgICAgIGlmKCByZXMucmVzcG9uc2VDb2RlID09PSAwICkge1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCBzaG93Q29uZmlybU1vZGFsICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjZXJyb3JNb2RhbCAubW9kYWwtYm9keScpLmFwcGVuZCggJChyZXMucmVzcG9uc2VIVE1MKSApO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2Vycm9yTW9kYWwnKS5tb2RhbCgnc2hvdycpO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggcmVzLnJlc3BvbnNlQ29kZSA9PT0gMSApIHtcblxuICAgICAgICAgICAgICAgICAgICBpZiggc2hvd0NvbmZpcm1Nb2RhbCApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI3N1Y2Nlc3NNb2RhbCAubW9kYWwtYm9keScpLmFwcGVuZCggJChyZXMucmVzcG9uc2VIVE1MKSApO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI3N1Y2Nlc3NNb2RhbCcpLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG5cbiAgICAgICAgICAgICAgICAgICAgLy9ubyBtb3JlIHBlbmRpbmcgY2hhbmdlc1xuICAgICAgICAgICAgICAgICAgICBzaXRlLnNldFBlbmRpbmdDaGFuZ2VzKGZhbHNlKTtcblxuXG4gICAgICAgICAgICAgICAgICAgIC8vdXBkYXRlIHJldmlzaW9ucz9cbiAgICAgICAgICAgICAgICAgICAgJCgnYm9keScpLnRyaWdnZXIoJ2NoYW5nZVBhZ2UnKTtcblxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHByZXBzIHRoZSBzaXRlIGRhdGEgYmVmb3JlIHNlbmRpbmcgaXQgdG8gdGhlIHNlcnZlclxuICAgICAgICAqL1xuICAgICAgICBwcmVwRm9yU2F2ZTogZnVuY3Rpb24odGVtcGxhdGUpIHtcblxuICAgICAgICAgICAgdGhpcy5zaXRlUGFnZXNSZWFkeUZvclNlcnZlciA9IHt9O1xuXG4gICAgICAgICAgICBpZiggdGVtcGxhdGUgKSB7Ly9zYXZpbmcgdGVtcGxhdGUsIG9ubHkgdGhlIGFjdGl2ZVBhZ2UgaXMgbmVlZGVkXG5cbiAgICAgICAgICAgICAgICB0aGlzLnNpdGVQYWdlc1JlYWR5Rm9yU2VydmVyW3RoaXMuYWN0aXZlUGFnZS5uYW1lXSA9IHRoaXMuYWN0aXZlUGFnZS5wcmVwRm9yU2F2ZSgpO1xuXG4gICAgICAgICAgICAgICAgdGhpcy5hY3RpdmVQYWdlLmZ1bGxQYWdlKCk7XG5cbiAgICAgICAgICAgIH0gZWxzZSB7Ly9yZWd1bGFyIHNhdmVcblxuICAgICAgICAgICAgICAgIC8vZmluZCB0aGUgcGFnZXMgd2hpY2ggbmVlZCB0byBiZSBzZW5kIHRvIHRoZSBzZXJ2ZXJcbiAgICAgICAgICAgICAgICBmb3IoIHZhciBpID0gMDsgaSA8IHRoaXMuc2l0ZVBhZ2VzLmxlbmd0aDsgaSsrICkge1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCB0aGlzLnNpdGVQYWdlc1tpXS5zdGF0dXMgIT09ICcnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB0aGlzLnNpdGVQYWdlc1JlYWR5Rm9yU2VydmVyW3RoaXMuc2l0ZVBhZ2VzW2ldLm5hbWVdID0gdGhpcy5zaXRlUGFnZXNbaV0ucHJlcEZvclNhdmUoKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHNldHMgYSBwYWdlIGFzIHRoZSBhY3RpdmUgb25lXG4gICAgICAgICovXG4gICAgICAgIHNldEFjdGl2ZTogZnVuY3Rpb24ocGFnZSkge1xuXG4gICAgICAgICAgICAvL3JlZmVyZW5jZSB0byB0aGUgYWN0aXZlIHBhZ2VcbiAgICAgICAgICAgIHRoaXMuYWN0aXZlUGFnZSA9IHBhZ2U7XG5cbiAgICAgICAgICAgIC8vaGlkZSBvdGhlciBwYWdlc1xuICAgICAgICAgICAgZm9yKHZhciBpIGluIHRoaXMuc2l0ZVBhZ2VzKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5zaXRlUGFnZXNbaV0ucGFyZW50VUwuc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9kaXNwbGF5IGFjdGl2ZSBvbmVcbiAgICAgICAgICAgIHRoaXMuYWN0aXZlUGFnZS5wYXJlbnRVTC5zdHlsZS5kaXNwbGF5ID0gJ2Jsb2NrJztcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGRlLWFjdGl2ZSBhbGwgcGFnZSBtZW51IGl0ZW1zXG4gICAgICAgICovXG4gICAgICAgIGRlQWN0aXZhdGVBbGw6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB2YXIgcGFnZXMgPSB0aGlzLnBhZ2VzTWVudS5xdWVyeVNlbGVjdG9yQWxsKCdsaScpO1xuXG4gICAgICAgICAgICBmb3IoIHZhciBpID0gMDsgaSA8IHBhZ2VzLmxlbmd0aDsgaSsrICkge1xuICAgICAgICAgICAgICAgIHBhZ2VzW2ldLmNsYXNzTGlzdC5yZW1vdmUoJ2FjdGl2ZScpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgYWRkcyBhIG5ldyBwYWdlIHRvIHRoZSBzaXRlXG4gICAgICAgICovXG4gICAgICAgIG5ld1BhZ2U6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICBzaXRlLmRlQWN0aXZhdGVBbGwoKTtcblxuICAgICAgICAgICAgLy9jcmVhdGUgdGhlIG5ldyBwYWdlIGluc3RhbmNlXG5cbiAgICAgICAgICAgIHZhciBwYWdlRGF0YSA9IFtdO1xuICAgICAgICAgICAgdmFyIHRlbXAgPSB7XG4gICAgICAgICAgICAgICAgcGFnZXNfaWQ6IDBcbiAgICAgICAgICAgIH07XG4gICAgICAgICAgICBwYWdlRGF0YVswXSA9IHRlbXA7XG5cbiAgICAgICAgICAgIHZhciBuZXdQYWdlTmFtZSA9ICdwYWdlJysoc2l0ZS5zaXRlUGFnZXMubGVuZ3RoKzEpO1xuXG4gICAgICAgICAgICB2YXIgbmV3UGFnZSA9IG5ldyBQYWdlKG5ld1BhZ2VOYW1lLCBwYWdlRGF0YSwgc2l0ZS5zaXRlUGFnZXMubGVuZ3RoKzEpO1xuXG4gICAgICAgICAgICBuZXdQYWdlLnN0YXR1cyA9ICduZXcnO1xuXG4gICAgICAgICAgICBuZXdQYWdlLnNlbGVjdFBhZ2UoKTtcbiAgICAgICAgICAgIG5ld1BhZ2UuZWRpdFBhZ2VOYW1lKCk7XG5cbiAgICAgICAgICAgIG5ld1BhZ2UuaXNFbXB0eSgpO1xuXG4gICAgICAgICAgICBzaXRlLnNldFBlbmRpbmdDaGFuZ2VzKHRydWUpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgY2hlY2tzIGlmIHRoZSBuYW1lIG9mIGEgcGFnZSBpcyBhbGxvd2VkXG4gICAgICAgICovXG4gICAgICAgIGNoZWNrUGFnZU5hbWU6IGZ1bmN0aW9uKHBhZ2VOYW1lKSB7XG5cbiAgICAgICAgICAgIC8vbWFrZSBzdXJlIHRoZSBuYW1lIGlzIHVuaXF1ZVxuICAgICAgICAgICAgZm9yKCB2YXIgaSBpbiB0aGlzLnNpdGVQYWdlcyApIHtcblxuICAgICAgICAgICAgICAgIGlmKCB0aGlzLnNpdGVQYWdlc1tpXS5uYW1lID09PSBwYWdlTmFtZSAmJiB0aGlzLmFjdGl2ZVBhZ2UgIT09IHRoaXMuc2l0ZVBhZ2VzW2ldICkge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnBhZ2VOYW1lRXJyb3IgPSBcIlRoZSBwYWdlIG5hbWUgbXVzdCBiZSB1bmlxdWUuXCI7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgcmV0dXJuIHRydWU7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICByZW1vdmVzIHVuYWxsb3dlZCBjaGFyYWN0ZXJzIGZyb20gdGhlIHBhZ2UgbmFtZVxuICAgICAgICAqL1xuICAgICAgICBwcmVwUGFnZU5hbWU6IGZ1bmN0aW9uKHBhZ2VOYW1lKSB7XG5cbiAgICAgICAgICAgIHBhZ2VOYW1lID0gcGFnZU5hbWUucmVwbGFjZSgnICcsICcnKTtcbiAgICAgICAgICAgIHBhZ2VOYW1lID0gcGFnZU5hbWUucmVwbGFjZSgvWz8qIS58JiM7JCVAXCI8PigpKyxdL2csIFwiXCIpO1xuXG4gICAgICAgICAgICByZXR1cm4gcGFnZU5hbWU7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBzYXZlIHBhZ2Ugc2V0dGluZ3MgZm9yIHRoZSBjdXJyZW50IHBhZ2VcbiAgICAgICAgKi9cbiAgICAgICAgdXBkYXRlUGFnZVNldHRpbmdzOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgc2l0ZS5hY3RpdmVQYWdlLnBhZ2VTZXR0aW5ncy50aXRsZSA9IHNpdGUuaW5wdXRQYWdlU2V0dGluZ3NUaXRsZS52YWx1ZTtcbiAgICAgICAgICAgIHNpdGUuYWN0aXZlUGFnZS5wYWdlU2V0dGluZ3MubWV0YV9kZXNjcmlwdGlvbiA9IHNpdGUuaW5wdXRQYWdlU2V0dGluZ3NNZXRhRGVzY3JpcHRpb24udmFsdWU7XG4gICAgICAgICAgICBzaXRlLmFjdGl2ZVBhZ2UucGFnZVNldHRpbmdzLm1ldGFfa2V5d29yZHMgPSBzaXRlLmlucHV0UGFnZVNldHRpbmdzTWV0YUtleXdvcmRzLnZhbHVlO1xuICAgICAgICAgICAgc2l0ZS5hY3RpdmVQYWdlLnBhZ2VTZXR0aW5ncy5oZWFkZXJfaW5jbHVkZXMgPSBzaXRlLmlucHV0UGFnZVNldHRpbmdzSW5jbHVkZXMudmFsdWU7XG4gICAgICAgICAgICBzaXRlLmFjdGl2ZVBhZ2UucGFnZVNldHRpbmdzLnBhZ2VfY3NzID0gc2l0ZS5pbnB1dFBhZ2VTZXR0aW5nc1BhZ2VDc3MudmFsdWU7XG5cbiAgICAgICAgICAgIHNpdGUuc2V0UGVuZGluZ0NoYW5nZXModHJ1ZSk7XG5cbiAgICAgICAgICAgICQoc2l0ZS5tb2RhbFBhZ2VTZXR0aW5ncykubW9kYWwoJ2hpZGUnKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHVwZGF0ZSBwYWdlIHN0YXR1c2VzXG4gICAgICAgICovXG4gICAgICAgIHVwZGF0ZVBhZ2VTdGF0dXM6IGZ1bmN0aW9uKHN0YXR1cykge1xuXG4gICAgICAgICAgICBmb3IoIHZhciBpIGluIHRoaXMuc2l0ZVBhZ2VzICkge1xuICAgICAgICAgICAgICAgIHRoaXMuc2l0ZVBhZ2VzW2ldLnN0YXR1cyA9IHN0YXR1cztcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9XG5cbiAgICB9O1xuXG4gICAgYnVpbGRlclVJLmluaXQoKTsgc2l0ZS5pbml0KCk7XG5cblxuICAgIC8vKioqKiBFWFBPUlRTXG4gICAgbW9kdWxlLmV4cG9ydHMuc2l0ZSA9IHNpdGU7XG4gICAgbW9kdWxlLmV4cG9ydHMuYnVpbGRlclVJID0gYnVpbGRlclVJO1xuXG59KCkpOyIsIihmdW5jdGlvbiAoKSB7XG4gICAgXCJ1c2Ugc3RyaWN0XCI7XG5cbiAgICB2YXIgc2l0ZUJ1aWxkZXIgPSByZXF1aXJlKCcuL2J1aWxkZXIuanMnKTtcblxuICAgIC8qXG4gICAgICAgIGNvbnN0cnVjdG9yIGZ1bmN0aW9uIGZvciBFbGVtZW50XG4gICAgKi9cbiAgICBtb2R1bGUuZXhwb3J0cy5FbGVtZW50ID0gZnVuY3Rpb24gKGVsKSB7XG5cbiAgICAgICAgdGhpcy5lbGVtZW50ID0gZWw7XG4gICAgICAgIHRoaXMuc2FuZGJveCA9IGZhbHNlO1xuICAgICAgICB0aGlzLnBhcmVudEZyYW1lID0ge307XG4gICAgICAgIHRoaXMucGFyZW50QmxvY2sgPSB7fTsvL3JlZmVyZW5jZSB0byB0aGUgcGFyZW50IGJsb2NrIGVsZW1lbnRcblxuICAgICAgICAvL21ha2UgY3VycmVudCBlbGVtZW50IGFjdGl2ZS9vcGVuIChiZWluZyB3b3JrZWQgb24pXG4gICAgICAgIHRoaXMuc2V0T3BlbiA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKHRoaXMuZWxlbWVudCkub2ZmKCdtb3VzZWVudGVyIG1vdXNlbGVhdmUgY2xpY2snKTtcblxuICAgICAgICAgICAgaWYoICQodGhpcy5lbGVtZW50KS5jbG9zZXN0KCdib2R5Jykud2lkdGgoKSAhPT0gJCh0aGlzLmVsZW1lbnQpLndpZHRoKCkgKSB7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMuZWxlbWVudCkuY3NzKHsnb3V0bGluZSc6ICczcHggZGFzaGVkIHJlZCcsICdjdXJzb3InOiAncG9pbnRlcid9KTtcblxuICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICQodGhpcy5lbGVtZW50KS5jc3MoeydvdXRsaW5lJzogJzNweCBkYXNoZWQgcmVkJywgJ291dGxpbmUtb2Zmc2V0JzonLTNweCcsICAnY3Vyc29yJzogJ3BvaW50ZXInfSk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8vc2V0cyB1cCBob3ZlciBhbmQgY2xpY2sgZXZlbnRzLCBtYWtpbmcgdGhlIGVsZW1lbnQgYWN0aXZlIG9uIHRoZSBjYW52YXNcbiAgICAgICAgdGhpcy5hY3RpdmF0ZSA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB2YXIgZWxlbWVudCA9IHRoaXM7XG5cbiAgICAgICAgICAgICQodGhpcy5lbGVtZW50KS5jc3MoeydvdXRsaW5lJzogJ25vbmUnLCAnY3Vyc29yJzogJ2luaGVyaXQnfSk7XG5cbiAgICAgICAgICAgICQodGhpcy5lbGVtZW50KS5vbignbW91c2VlbnRlcicsIGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAgICAgaWYoICQodGhpcykuY2xvc2VzdCgnYm9keScpLndpZHRoKCkgIT09ICQodGhpcykud2lkdGgoKSApIHtcblxuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmNzcyh7J291dGxpbmUnOiAnM3B4IGRhc2hlZCByZWQnLCAnY3Vyc29yJzogJ3BvaW50ZXInfSk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgICAgICQodGhpcykuY3NzKHsnb3V0bGluZSc6ICczcHggZGFzaGVkIHJlZCcsICdvdXRsaW5lLW9mZnNldCc6ICctM3B4JywgJ2N1cnNvcic6ICdwb2ludGVyJ30pO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9KS5vbignbW91c2VsZWF2ZScsIGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5jc3MoeydvdXRsaW5lJzogJycsICdjdXJzb3InOiAnJywgJ291dGxpbmUtb2Zmc2V0JzogJyd9KTtcblxuICAgICAgICAgICAgfSkub24oJ2NsaWNrJywgZnVuY3Rpb24oZSkge1xuXG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgICAgICBlbGVtZW50LmNsaWNrSGFuZGxlcih0aGlzKTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfTtcblxuICAgICAgICB0aGlzLmRlYWN0aXZhdGUgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCh0aGlzLmVsZW1lbnQpLm9mZignbW91c2VlbnRlciBtb3VzZWxlYXZlIGNsaWNrJyk7XG4gICAgICAgICAgICAkKHRoaXMuZWxlbWVudCkuY3NzKHsnb3V0bGluZSc6ICdub25lJywgJ2N1cnNvcic6ICdpbmhlcml0J30pO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgLy9yZW1vdmVzIHRoZSBlbGVtZW50cyBvdXRsaW5lXG4gICAgICAgIHRoaXMucmVtb3ZlT3V0bGluZSA9IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKHRoaXMuZWxlbWVudCkuY3NzKHsnb3V0bGluZSc6ICdub25lJywgJ2N1cnNvcic6ICdpbmhlcml0J30pO1xuXG4gICAgICAgIH07XG5cbiAgICAgICAgLy9zZXRzIHRoZSBwYXJlbnQgaWZyYW1lXG4gICAgICAgIHRoaXMuc2V0UGFyZW50RnJhbWUgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIGRvYyA9IHRoaXMuZWxlbWVudC5vd25lckRvY3VtZW50O1xuICAgICAgICAgICAgdmFyIHcgPSBkb2MuZGVmYXVsdFZpZXcgfHwgZG9jLnBhcmVudFdpbmRvdztcbiAgICAgICAgICAgIHZhciBmcmFtZXMgPSB3LnBhcmVudC5kb2N1bWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZSgnaWZyYW1lJyk7XG5cbiAgICAgICAgICAgIGZvciAodmFyIGk9IGZyYW1lcy5sZW5ndGg7IGktLT4wOykge1xuXG4gICAgICAgICAgICAgICAgdmFyIGZyYW1lPSBmcmFtZXNbaV07XG5cbiAgICAgICAgICAgICAgICB0cnkge1xuICAgICAgICAgICAgICAgICAgICB2YXIgZD0gZnJhbWUuY29udGVudERvY3VtZW50IHx8IGZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQ7XG4gICAgICAgICAgICAgICAgICAgIGlmIChkPT09ZG9jKVxuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5wYXJlbnRGcmFtZSA9IGZyYW1lO1xuICAgICAgICAgICAgICAgIH0gY2F0Y2goZSkge31cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9O1xuXG4gICAgICAgIC8vc2V0cyB0aGlzIGVsZW1lbnQncyBwYXJlbnQgYmxvY2sgcmVmZXJlbmNlXG4gICAgICAgIHRoaXMuc2V0UGFyZW50QmxvY2sgPSBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9sb29wIHRocm91Z2ggYWxsIHRoZSBibG9ja3Mgb24gdGhlIGNhbnZhc1xuICAgICAgICAgICAgZm9yKCB2YXIgaSA9IDA7IGkgPCBzaXRlQnVpbGRlci5zaXRlLnNpdGVQYWdlcy5sZW5ndGg7IGkrKyApIHtcblxuICAgICAgICAgICAgICAgIGZvciggdmFyIHggPSAwOyB4IDwgc2l0ZUJ1aWxkZXIuc2l0ZS5zaXRlUGFnZXNbaV0uYmxvY2tzLmxlbmd0aDsgeCsrICkge1xuXG4gICAgICAgICAgICAgICAgICAgIC8vaWYgdGhlIGJsb2NrJ3MgZnJhbWUgbWF0Y2hlcyB0aGlzIGVsZW1lbnQncyBwYXJlbnQgZnJhbWVcbiAgICAgICAgICAgICAgICAgICAgaWYoIHNpdGVCdWlsZGVyLnNpdGUuc2l0ZVBhZ2VzW2ldLmJsb2Nrc1t4XS5mcmFtZSA9PT0gdGhpcy5wYXJlbnRGcmFtZSApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vY3JlYXRlIGEgcmVmZXJlbmNlIHRvIHRoYXQgYmxvY2sgYW5kIHN0b3JlIGl0IGluIHRoaXMucGFyZW50QmxvY2tcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMucGFyZW50QmxvY2sgPSBzaXRlQnVpbGRlci5zaXRlLnNpdGVQYWdlc1tpXS5ibG9ja3NbeF07XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH07XG5cblxuICAgICAgICB0aGlzLnNldFBhcmVudEZyYW1lKCk7XG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGlzIHRoaXMgYmxvY2sgc2FuZGJveGVkP1xuICAgICAgICAqL1xuXG4gICAgICAgIGlmKCB0aGlzLnBhcmVudEZyYW1lLmdldEF0dHJpYnV0ZSgnZGF0YS1zYW5kYm94JykgKSB7XG4gICAgICAgICAgICB0aGlzLnNhbmRib3ggPSB0aGlzLnBhcmVudEZyYW1lLmdldEF0dHJpYnV0ZSgnZGF0YS1zYW5kYm94Jyk7XG4gICAgICAgIH1cblxuICAgIH07XG5cbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cbiAgICBtb2R1bGUuZXhwb3J0cy5wYWdlQ29udGFpbmVyID0gXCIjcGFnZVwiO1xuXG4gICAgbW9kdWxlLmV4cG9ydHMuZWRpdGFibGVJdGVtcyA9IHtcbiAgICAgICAgJ3NwYW4uZmEnOiBbJ2NvbG9yJywgJ2ZvbnQtc2l6ZSddLFxuICAgICAgICAnLmJnLmJnMSc6IFsnYmFja2dyb3VuZC1jb2xvciddLFxuICAgICAgICAnbmF2IGEsIGEuZWRpdCc6IFsnY29sb3InLCAnZm9udC13ZWlnaHQnLCAndGV4dC10cmFuc2Zvcm0nXSxcbiAgICAgICAgJ2gxLCBoMiwgaDMsIGg0LCBoNSwgcCc6IFsnY29sb3InLCAnZm9udC1zaXplJywgJ2JhY2tncm91bmQtY29sb3InLCAnZm9udC1mYW1pbHknXSxcbiAgICAgICAgJ2EuYnRuLCBidXR0b24uYnRuJzogWydib3JkZXItcmFkaXVzJywgJ2ZvbnQtc2l6ZScsICdiYWNrZ3JvdW5kLWNvbG9yJ10sXG5cbiAgICAgICAgJ2ltZyc6IFsnYm9yZGVyLXRvcC1sZWZ0LXJhZGl1cycsICdib3JkZXItdG9wLXJpZ2h0LXJhZGl1cycsICdib3JkZXItYm90dG9tLWxlZnQtcmFkaXVzJywgJ2JvcmRlci1ib3R0b20tcmlnaHQtcmFkaXVzJywgJ2JvcmRlci1jb2xvcicsICdib3JkZXItc3R5bGUnLCAnYm9yZGVyLXdpZHRoJ10sXG4gICAgICAgICdoci5kYXNoZWQnOiBbJ2JvcmRlci1jb2xvcicsICdib3JkZXItd2lkdGgnXSxcbiAgICAgICAgJy5kaXZpZGVyID4gc3Bhbic6IFsnY29sb3InLCAnZm9udC1zaXplJ10sXG4gICAgICAgICdoci5zaGFkb3dEb3duJzogWydtYXJnaW4tdG9wJywgJ21hcmdpbi1ib3R0b20nXSxcbiAgICAgICAgJy5mb290ZXIgYSc6IFsnY29sb3InXSxcbiAgICAgICAgJy5iZy5iZzEsIC5iZy5iZzIsIC5oZWFkZXIxMCwgLmhlYWRlcjExJzogWydiYWNrZ3JvdW5kLWltYWdlJywgJ2JhY2tncm91bmQtY29sb3InXSxcbiAgICAgICAgJy5mcmFtZUNvdmVyJzogW11cbiAgICB9O1xuXG4gICAgbW9kdWxlLmV4cG9ydHMuZWRpdGFibGVJdGVtT3B0aW9ucyA9IHtcbiAgICAgICAgJ25hdiBhIDogZm9udC13ZWlnaHQnOiBbJzQwMCcsICc3MDAnXSxcbiAgICAgICAgJ2EuYnRuIDogYm9yZGVyLXJhZGl1cyc6IFsnMHB4JywgJzRweCcsICcxMHB4J10sXG4gICAgICAgICdpbWcgOiBib3JkZXItc3R5bGUnOiBbJ25vbmUnLCAnZG90dGVkJywgJ2Rhc2hlZCcsICdzb2xpZCddLFxuICAgICAgICAnaW1nIDogYm9yZGVyLXdpZHRoJzogWycxcHgnLCAnMnB4JywgJzNweCcsICc0cHgnXSxcbiAgICAgICAgJ2gxLCBoMiwgaDMsIGg0LCBoNSwgcCA6IGZvbnQtZmFtaWx5JzogWydkZWZhdWx0JywgJ0xhdG8nLCAnSGVsdmV0aWNhJywgJ0FyaWFsJywgJ1RpbWVzIE5ldyBSb21hbiddLFxuICAgICAgICAnaDIgOiBmb250LWZhbWlseSc6IFsnZGVmYXVsdCcsICdMYXRvJywgJ0hlbHZldGljYScsICdBcmlhbCcsICdUaW1lcyBOZXcgUm9tYW4nXSxcbiAgICAgICAgJ2gzIDogZm9udC1mYW1pbHknOiBbJ2RlZmF1bHQnLCAnTGF0bycsICdIZWx2ZXRpY2EnLCAnQXJpYWwnLCAnVGltZXMgTmV3IFJvbWFuJ10sXG4gICAgICAgICdwIDogZm9udC1mYW1pbHknOiBbJ2RlZmF1bHQnLCAnTGF0bycsICdIZWx2ZXRpY2EnLCAnQXJpYWwnLCAnVGltZXMgTmV3IFJvbWFuJ11cbiAgICB9O1xuXG4gICAgbW9kdWxlLmV4cG9ydHMuZWRpdGFibGVDb250ZW50ID0gWycuZWRpdENvbnRlbnQnLCAnLm5hdmJhciBhJywgJ2J1dHRvbicsICdhLmJ0bicsICcuZm9vdGVyIGE6bm90KC5mYSknLCAnLnRhYmxlV3JhcHBlcicsICdoMScsICdoMiddO1xuXG4gICAgbW9kdWxlLmV4cG9ydHMuYXV0b1NhdmVUaW1lb3V0ID0gNjAwMDA7XG5cbiAgICBtb2R1bGUuZXhwb3J0cy5zb3VyY2VDb2RlRWRpdFN5bnRheERlbGF5ID0gMTAwMDA7XG5cbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cblx0dmFyIGNhbnZhc0VsZW1lbnQgPSByZXF1aXJlKCcuL2NhbnZhc0VsZW1lbnQuanMnKS5FbGVtZW50O1xuXHR2YXIgYkNvbmZpZyA9IHJlcXVpcmUoJy4vY29uZmlnJyk7XG5cdHZhciBzaXRlQnVpbGRlciA9IHJlcXVpcmUoJy4vYnVpbGRlci5qcycpO1xuXG5cdHZhciBjb250ZW50ZWRpdG9yID0ge1xuXG4gICAgICAgIGxhYmVsQ29udGVudE1vZGU6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtb2RlQ29udGVudExhYmVsJyksXG4gICAgICAgIHJhZGlvQ29udGVudDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ21vZGVDb250ZW50JyksXG4gICAgICAgIGJ1dHRvblVwZGF0ZUNvbnRlbnQ6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCd1cGRhdGVDb250ZW50SW5GcmFtZVN1Ym1pdCcpLFxuICAgICAgICBhY3RpdmVFbGVtZW50OiB7fSxcbiAgICAgICAgYWxsQ29udGVudEl0ZW1zT25DYW52YXM6IFtdLFxuICAgICAgICBtb2RhbEVkaXRDb250ZW50OiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZWRpdENvbnRlbnRNb2RhbCcpLFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2Rpc3BsYXkgY29udGVudCBtb2RlIGxhYmVsXG4gICAgICAgICAgICAkKHRoaXMubGFiZWxDb250ZW50TW9kZSkuc2hvdygpO1xuXG4gICAgICAgICAgICAkKHRoaXMucmFkaW9Db250ZW50KS5vbignY2xpY2snLCB0aGlzLmFjdGl2YXRlQ29udGVudE1vZGUpO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblVwZGF0ZUNvbnRlbnQpLm9uKCdjbGljaycsIHRoaXMudXBkYXRlRWxlbWVudENvbnRlbnQpO1xuICAgICAgICAgICAgJCh0aGlzLm1vZGFsRWRpdENvbnRlbnQpLm9uKCdoaWRkZW4uYnMubW9kYWwnLCB0aGlzLmVkaXRDb250ZW50TW9kYWxDbG9zZUV2ZW50KTtcbiAgICAgICAgICAgICQoZG9jdW1lbnQpLm9uKCdtb2RlRGV0YWlscyBtb2RlQmxvY2tzJywgJ2JvZHknLCB0aGlzLmRlQWN0aXZhdGVNb2RlKTtcblxuXHRcdFx0Ly9saXN0ZW4gZm9yIHRoZSBiZWZvcmVTYXZlIGV2ZW50LCByZW1vdmVzIG91dGxpbmVzIGJlZm9yZSBzYXZpbmdcbiAgICAgICAgICAgICQoJ2JvZHknKS5vbignYmVmb3JlU2F2ZScsIGZ1bmN0aW9uICgpIHtcblxuXHRcdFx0XHRpZiggT2JqZWN0LmtleXMoIGNvbnRlbnRlZGl0b3IuYWN0aXZlRWxlbWVudCApLmxlbmd0aCA+IDAgKSB7XG4gICAgICAgICAgICAgICAgXHRjb250ZW50ZWRpdG9yLmFjdGl2ZUVsZW1lbnQucmVtb3ZlT3V0bGluZSgpO1xuICAgICAgICAgICAgXHR9XG5cblx0XHRcdH0pO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgQWN0aXZhdGVzIGNvbnRlbnQgbW9kZVxuICAgICAgICAqL1xuICAgICAgICBhY3RpdmF0ZUNvbnRlbnRNb2RlOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9FbGVtZW50IG9iamVjdCBleHRlbnRpb25cbiAgICAgICAgICAgIGNhbnZhc0VsZW1lbnQucHJvdG90eXBlLmNsaWNrSGFuZGxlciA9IGZ1bmN0aW9uKGVsKSB7XG4gICAgICAgICAgICAgICAgY29udGVudGVkaXRvci5jb250ZW50Q2xpY2soZWwpO1xuICAgICAgICAgICAgfTtcblxuICAgICAgICAgICAgLy90cmlnZ2VyIGN1c3RvbSBldmVudFxuICAgICAgICAgICAgJCgnYm9keScpLnRyaWdnZXIoJ21vZGVDb250ZW50Jyk7XG5cbiAgICAgICAgICAgIC8vZGlzYWJsZSBmcmFtZUNvdmVyc1xuICAgICAgICAgICAgZm9yKCB2YXIgaSA9IDA7IGkgPCBzaXRlQnVpbGRlci5zaXRlLnNpdGVQYWdlcy5sZW5ndGg7IGkrKyApIHtcbiAgICAgICAgICAgICAgICBzaXRlQnVpbGRlci5zaXRlLnNpdGVQYWdlc1tpXS50b2dnbGVGcmFtZUNvdmVycygnT2ZmJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vY3JlYXRlIGFuIG9iamVjdCBmb3IgZXZlcnkgZWRpdGFibGUgZWxlbWVudCBvbiB0aGUgY2FudmFzIGFuZCBzZXR1cCBpdCdzIGV2ZW50c1xuICAgICAgICAgICAgJCgnI3BhZ2VMaXN0IHVsIGxpIGlmcmFtZScpLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgIGZvciggdmFyIGtleSBpbiBiQ29uZmlnLmVkaXRhYmxlQ29udGVudCApIHtcblxuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmNvbnRlbnRzKCkuZmluZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyICsgJyAnKyBiQ29uZmlnLmVkaXRhYmxlQ29udGVudFtrZXldICkuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgbmV3RWxlbWVudCA9IG5ldyBjYW52YXNFbGVtZW50KHRoaXMpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBuZXdFbGVtZW50LmFjdGl2YXRlKCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vc3RvcmUgaW4gYXJyYXlcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRlbnRlZGl0b3IuYWxsQ29udGVudEl0ZW1zT25DYW52YXMucHVzaCggbmV3RWxlbWVudCApO1xuXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIE9wZW5zIHVwIHRoZSBjb250ZW50IGVkaXRvclxuICAgICAgICAqL1xuICAgICAgICBjb250ZW50Q2xpY2s6IGZ1bmN0aW9uKGVsKSB7XG5cbiAgICAgICAgICAgIC8vaWYgd2UgaGF2ZSBhbiBhY3RpdmUgZWxlbWVudCwgbWFrZSBpdCB1bmFjdGl2ZVxuICAgICAgICAgICAgaWYoIE9iamVjdC5rZXlzKHRoaXMuYWN0aXZlRWxlbWVudCkubGVuZ3RoICE9PSAwKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5hY3RpdmVFbGVtZW50LmFjdGl2YXRlKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vc2V0IHRoZSBhY3RpdmUgZWxlbWVudFxuICAgICAgICAgICAgdmFyIGFjdGl2ZUVsZW1lbnQgPSBuZXcgY2FudmFzRWxlbWVudChlbCk7XG4gICAgICAgICAgICBhY3RpdmVFbGVtZW50LnNldFBhcmVudEJsb2NrKCk7XG4gICAgICAgICAgICBjb250ZW50ZWRpdG9yLmFjdGl2ZUVsZW1lbnQgPSBhY3RpdmVFbGVtZW50O1xuXG4gICAgICAgICAgICAvL3VuYmluZCBob3ZlciBhbmQgY2xpY2sgZXZlbnRzIGFuZCBtYWtlIHRoaXMgaXRlbSBhY3RpdmVcbiAgICAgICAgICAgIGNvbnRlbnRlZGl0b3IuYWN0aXZlRWxlbWVudC5zZXRPcGVuKCk7XG5cbiAgICAgICAgICAgICQoJyNlZGl0Q29udGVudE1vZGFsJykubW9kYWwoJ3Nob3cnKTtcblxuICAgICAgICAgICAgLy9mb3IgdGhlIGVsZW1lbnRzIGJlbG93LCB3ZSdsbCB1c2UgYSBzaW1wbHlmaWVkIGVkaXRvciwgb25seSBkaXJlY3QgdGV4dCBjYW4gYmUgZG9uZSB0aHJvdWdoIHRoaXMgb25lXG4gICAgICAgICAgICBpZiggZWwudGFnTmFtZSA9PT0gJ1NNQUxMJyB8fCBlbC50YWdOYW1lID09PSAnQScgfHwgZWwudGFnTmFtZSA9PT0gJ0xJJyB8fCBlbC50YWdOYW1lID09PSAnU1BBTicgfHwgZWwudGFnTmFtZSA9PT0gJ0InIHx8IGVsLnRhZ05hbWUgPT09ICdJJyB8fCBlbC50YWdOYW1lID09PSAnVFQnIHx8IGVsLnRhZ2VOYW1lID09PSAnQ09ERScgfHwgZWwudGFnTmFtZSA9PT0gJ0VNJyB8fCBlbC50YWdOYW1lID09PSAnU1RST05HJyB8fCBlbC50YWdOYW1lID09PSAnU1VCJyB8fCBlbC50YWdOYW1lID09PSAnQlVUVE9OJyB8fCBlbC50YWdOYW1lID09PSAnTEFCRUwnIHx8IGVsLnRhZ05hbWUgPT09ICdQJyB8fCBlbC50YWdOYW1lID09PSAnSDEnIHx8IGVsLnRhZ05hbWUgPT09ICdIMicgfHwgZWwudGFnTmFtZSA9PT0gJ0gyJyB8fCBlbC50YWdOYW1lID09PSAnSDMnIHx8IGVsLnRhZ05hbWUgPT09ICdINCcgfHwgZWwudGFnTmFtZSA9PT0gJ0g1JyB8fCBlbC50YWdOYW1lID09PSAnSDYnICkge1xuXG5cdFx0XHRcdCQoJyNjb250ZW50VG9FZGl0Jykuc3VtbWVybm90ZSh7XG5cdFx0XHRcdFx0dG9vbGJhcjogW1xuXHRcdFx0XHRcdC8vIFtncm91cE5hbWUsIFtsaXN0IG9mIGJ1dHRvbl1dXG5cdFx0XHRcdFx0Wydjb2RldmlldycsIFsnY29kZXZpZXcnXV0sXG5cdFx0XHRcdFx0Wydmb250c3R5bGUnLCBbJ2JvbGQnLCAnaXRhbGljJywgJ3VuZGVybGluZScsICdzdHJpa2V0aHJvdWdoJywgJ2NsZWFyJ11dLFxuXHRcdFx0XHRcdFsnaGVscCcsIFsndW5kbycsICdyZWRvJ11dXG5cdFx0XHRcdCAgXVxuXHRcdFx0XHR9KTtcblxuICAgICAgICAgICAgfSBlbHNlIGlmKCBlbC50YWdOYW1lID09PSAnRElWJyAmJiAkKGVsKS5oYXNDbGFzcygndGFibGVXcmFwcGVyJykgKSB7XG5cblx0XHRcdFx0JCgnI2NvbnRlbnRUb0VkaXQnKS5zdW1tZXJub3RlKHtcblx0XHRcdFx0XHR0b29sYmFyOiBbXG5cdFx0XHRcdFx0Wydjb2RldmlldycsIFsnY29kZXZpZXcnXV0sXG5cdFx0XHRcdFx0WydzdHlsZXNlbGVjdCcsIFsnc3R5bGUnXV0sXG5cdFx0XHRcdFx0Wydmb250c3R5bGUnLCBbJ2JvbGQnLCAnaXRhbGljJywgJ3VuZGVybGluZScsICdzdHJpa2V0aHJvdWdoJywgJ2NsZWFyJ11dLFxuXHRcdFx0XHRcdFsndGFibGUnLCBbJ3RhYmxlJ11dLFxuXHRcdFx0XHRcdFsnbGluaycsIFsnbGluaycsICd1bmxpbmsnXV0sXG5cdFx0XHRcdFx0WydoZWxwJywgWyd1bmRvJywgJ3JlZG8nXV1cblx0XHRcdFx0ICBdXG5cdFx0XHRcdH0pO1xuXG4gICAgICAgICAgICB9IGVsc2Uge1xuXG5cdFx0XHRcdCQoJyNjb250ZW50VG9FZGl0Jykuc3VtbWVybm90ZSh7XG5cdFx0XHRcdFx0dG9vbGJhcjogW1xuXHRcdFx0XHRcdFsnY29kZXZpZXcnLCBbJ2NvZGV2aWV3J11dLFxuXHRcdFx0XHRcdFsnc3R5bGVzZWxlY3QnLCBbJ3N0eWxlJ11dLFxuXHRcdFx0XHRcdFsnZm9udHN0eWxlJywgWydib2xkJywgJ2l0YWxpYycsICd1bmRlcmxpbmUnLCAnc3RyaWtldGhyb3VnaCcsICdjbGVhciddXSxcblx0XHRcdFx0XHRbJ2xpc3RzJywgWydvbCcsICd1bCddXSxcblx0XHRcdFx0XHRbJ2xpbmsnLCBbJ2xpbmsnLCAndW5saW5rJ11dLFxuXHRcdFx0XHRcdFsnaGVscCcsIFsndW5kbycsICdyZWRvJ11dXG5cdFx0XHRcdCAgXVxuXHRcdFx0XHR9KTtcblxuICAgICAgICAgICAgfVxuXG5cdFx0XHQkKCcjY29udGVudFRvRWRpdCcpLnN1bW1lcm5vdGUoJ2NvZGUnLCAkKGVsKS5odG1sKCkpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgdXBkYXRlcyB0aGUgY29udGVudCBvZiBhbiBlbGVtZW50XG4gICAgICAgICovXG4gICAgICAgIHVwZGF0ZUVsZW1lbnRDb250ZW50OiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJChjb250ZW50ZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuaHRtbCggJCgnI2VkaXRDb250ZW50TW9kYWwgI2NvbnRlbnRUb0VkaXQnKS5zdW1tZXJub3RlKCdjb2RlJykgKS5jc3MoeydvdXRsaW5lJzogJycsICdjdXJzb3InOicnfSk7XG5cbiAgICAgICAgICAgIC8qIFNBTkRCT1ggKi9cblxuICAgICAgICAgICAgaWYoIGNvbnRlbnRlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94ICkge1xuXG4gICAgICAgICAgICAgICAgdmFyIGVsZW1lbnRJRCA9ICQoY29udGVudGVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmF0dHIoJ2lkJyk7XG4gICAgICAgICAgICAgICAgJCgnIycrY29udGVudGVkaXRvci5hY3RpdmVFbGVtZW50LnNhbmRib3gpLmNvbnRlbnRzKCkuZmluZCgnIycrZWxlbWVudElEKS5odG1sKCAkKCcjZWRpdENvbnRlbnRNb2RhbCAjY29udGVudFRvRWRpdCcpLnN1bW1lcm5vdGUoJ2NvZGUnKSApO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8qIEVORCBTQU5EQk9YICovXG5cblx0XHRcdCQoJyNlZGl0Q29udGVudE1vZGFsICNjb250ZW50VG9FZGl0Jykuc3VtbWVybm90ZSgnY29kZScsICcnKTtcblx0XHRcdCQoJyNlZGl0Q29udGVudE1vZGFsICNjb250ZW50VG9FZGl0Jykuc3VtbWVybm90ZSgnZGVzdHJveScpO1xuXG4gICAgICAgICAgICAkKCcjZWRpdENvbnRlbnRNb2RhbCcpLm1vZGFsKCdoaWRlJyk7XG5cbiAgICAgICAgICAgICQodGhpcykuY2xvc2VzdCgnYm9keScpLnJlbW92ZUNsYXNzKCdtb2RhbC1vcGVuJykuYXR0cignc3R5bGUnLCAnJyk7XG5cbiAgICAgICAgICAgIC8vcmVzZXQgaWZyYW1lIGhlaWdodFxuICAgICAgICAgICAgY29udGVudGVkaXRvci5hY3RpdmVFbGVtZW50LnBhcmVudEJsb2NrLmhlaWdodEFkanVzdG1lbnQoKTtcblxuICAgICAgICAgICAgLy9jb250ZW50IHdhcyB1cGRhdGVkLCBzbyB3ZSd2ZSBnb3QgcGVuZGluZyBjaGFuZ2VcbiAgICAgICAgICAgIHNpdGVCdWlsZGVyLnNpdGUuc2V0UGVuZGluZ0NoYW5nZXModHJ1ZSk7XG5cbiAgICAgICAgICAgIC8vcmVhY3RpdmF0ZSBlbGVtZW50XG4gICAgICAgICAgICBjb250ZW50ZWRpdG9yLmFjdGl2ZUVsZW1lbnQuYWN0aXZhdGUoKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGV2ZW50IGhhbmRsZXIgZm9yIHdoZW4gdGhlIGVkaXQgY29udGVudCBtb2RhbCBpcyBjbG9zZWRcbiAgICAgICAgKi9cbiAgICAgICAgZWRpdENvbnRlbnRNb2RhbENsb3NlRXZlbnQ6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKCcjZWRpdENvbnRlbnRNb2RhbCAjY29udGVudFRvRWRpdCcpLnN1bW1lcm5vdGUoJ2Rlc3Ryb3knKTtcblxuICAgICAgICAgICAgLy9yZS1hY3RpdmF0ZSBlbGVtZW50XG4gICAgICAgICAgICBjb250ZW50ZWRpdG9yLmFjdGl2ZUVsZW1lbnQuYWN0aXZhdGUoKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIEV2ZW50IGhhbmRsZXIgZm9yIHdoZW4gbW9kZSBnZXRzIGRlYWN0aXZhdGVkXG4gICAgICAgICovXG4gICAgICAgIGRlQWN0aXZhdGVNb2RlOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGlmKCBPYmplY3Qua2V5cyggY29udGVudGVkaXRvci5hY3RpdmVFbGVtZW50ICkubGVuZ3RoID4gMCApIHtcbiAgICAgICAgICAgICAgICBjb250ZW50ZWRpdG9yLmFjdGl2ZUVsZW1lbnQucmVtb3ZlT3V0bGluZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL2RlYWN0aXZhdGUgYWxsIGNvbnRlbnQgYmxvY2tzXG4gICAgICAgICAgICBmb3IoIHZhciBpID0gMDsgaSA8IGNvbnRlbnRlZGl0b3IuYWxsQ29udGVudEl0ZW1zT25DYW52YXMubGVuZ3RoOyBpKysgKSB7XG4gICAgICAgICAgICAgICAgY29udGVudGVkaXRvci5hbGxDb250ZW50SXRlbXNPbkNhbnZhc1tpXS5kZWFjdGl2YXRlKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfVxuXG4gICAgfTtcblxuICAgIGNvbnRlbnRlZGl0b3IuaW5pdCgpO1xuXG59KCkpOyIsIihmdW5jdGlvbiAoKSB7XG5cdFwidXNlIHN0cmljdFwiO1xuXG5cdHZhciBiQ29uZmlnID0gcmVxdWlyZSgnLi9jb25maWcuanMnKTtcblxuXHR2YXIgYmV4cG9ydCA9IHtcblxuICAgICAgICBtb2RhbEV4cG9ydDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2V4cG9ydE1vZGFsJyksXG4gICAgICAgIGJ1dHRvbkV4cG9ydDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2V4cG9ydFBhZ2UnKSxcblxuICAgICAgICBpbml0OiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCh0aGlzLm1vZGFsRXhwb3J0KS5vbignc2hvdy5icy5tb2RhbCcsIHRoaXMuZG9FeHBvcnRNb2RhbCk7XG4gICAgICAgICAgICAkKHRoaXMubW9kYWxFeHBvcnQpLm9uKCdzaG93bi5icy5tb2RhbCcsIHRoaXMucHJlcEV4cG9ydCk7XG4gICAgICAgICAgICAkKHRoaXMubW9kYWxFeHBvcnQpLmZpbmQoJ2Zvcm0nKS5vbignc3VibWl0JywgdGhpcy5leHBvcnRGb3JtU3VibWl0KTtcblxuICAgICAgICAgICAgLy9yZXZlYWwgZXhwb3J0IGJ1dHRvblxuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvbkV4cG9ydCkuc2hvdygpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgZG9FeHBvcnRNb2RhbDogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICQoJyNleHBvcnRNb2RhbCA+IGZvcm0gI2V4cG9ydFN1Ym1pdCcpLnNob3coJycpO1xuICAgICAgICAgICAgJCgnI2V4cG9ydE1vZGFsID4gZm9ybSAjZXhwb3J0Q2FuY2VsJykudGV4dCgnQ2FuY2VsICYgQ2xvc2UnKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHByZXBhcmVzIHRoZSBleHBvcnQgZGF0YVxuICAgICAgICAqL1xuICAgICAgICBwcmVwRXhwb3J0OiBmdW5jdGlvbihlKSB7XG5cbiAgICAgICAgICAgIC8vZGVsZXRlIG9sZGVyIGhpZGRlbiBmaWVsZHNcbiAgICAgICAgICAgICQoJyNleHBvcnRNb2RhbCBmb3JtIGlucHV0W3R5cGU9XCJoaWRkZW5cIl0ucGFnZXMnKS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgLy9sb29wIHRocm91Z2ggYWxsIHBhZ2VzXG4gICAgICAgICAgICAkKCcjcGFnZUxpc3QgPiB1bCcpLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgIHZhciB0aGVDb250ZW50cztcblxuICAgICAgICAgICAgICAgIC8vZ3JhYiB0aGUgc2tlbGV0b24gbWFya3VwXG4gICAgICAgICAgICAgICAgdmFyIG5ld0RvY01haW5QYXJlbnQgPSAkKCdpZnJhbWUjc2tlbGV0b24nKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciApO1xuXG4gICAgICAgICAgICAgICAgLy9lbXB0eSBvdXQgdGhlIHNrZWxldG9cbiAgICAgICAgICAgICAgICBuZXdEb2NNYWluUGFyZW50LmZpbmQoJyonKS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgIC8vbG9vcCB0aHJvdWdoIHBhZ2UgaWZyYW1lcyBhbmQgZ3JhYiB0aGUgYm9keSBzdHVmZlxuICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnaWZyYW1lJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciBhdHRyID0gJCh0aGlzKS5hdHRyKCdkYXRhLXNhbmRib3gnKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAodHlwZW9mIGF0dHIgIT09IHR5cGVvZiB1bmRlZmluZWQgJiYgYXR0ciAhPT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoZUNvbnRlbnRzID0gJCgnI3NhbmRib3hlcyAjJythdHRyKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciApO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhlQ29udGVudHMgPSAkKHRoaXMpLmNvbnRlbnRzKCkuZmluZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyICk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICB0aGVDb250ZW50cy5maW5kKCcuZnJhbWVDb3ZlcicpLmVhY2goZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG5cbiAgICAgICAgICAgICAgICAgICAgLy9yZW1vdmUgaW5saW5lIHN0eWxpbmcgbGVmdG92ZXJzXG4gICAgICAgICAgICAgICAgICAgIGZvciggdmFyIGtleSBpbiBiQ29uZmlnLmVkaXRhYmxlSXRlbXMgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRoZUNvbnRlbnRzLmZpbmQoIGtleSApLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQXR0cignZGF0YS1zZWxlY3RvcicpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoICQodGhpcykuYXR0cignc3R5bGUnKSA9PT0gJycgKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQXR0cignc3R5bGUnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgIGZvciAoIHZhciBpID0gMDsgaSA8IGJDb25maWcuZWRpdGFibGVDb250ZW50Lmxlbmd0aDsgKytpKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuY29udGVudHMoKS5maW5kKCBiQ29uZmlnLmVkaXRhYmxlQ29udGVudFtpXSApLmVhY2goZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZUF0dHIoJ2RhdGEtc2VsZWN0b3InKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIHRvQWRkID0gdGhlQ29udGVudHMuaHRtbCgpO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vZ3JhYiBzY3JpcHRzXG4gICAgICAgICAgICAgICAgICAgIHZhciBzY3JpcHRzID0gJCh0aGlzKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciApLmZpbmQoJ3NjcmlwdCcpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCBzY3JpcHRzLnNpemUoKSA+IDAgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciB0aGVJZnJhbWUgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChcInNrZWxldG9uXCIpLCBzY3JpcHQ7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdHMuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoICQodGhpcykudGV4dCgpICE9PSAnJyApIHsvL3NjcmlwdCB0YWdzIHdpdGggY29udGVudFxuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdCA9IHRoZUlmcmFtZS5jb250ZW50V2luZG93LmRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoXCJzY3JpcHRcIik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdC50eXBlID0gJ3RleHQvamF2YXNjcmlwdCc7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdC5pbm5lckhUTUwgPSAkKHRoaXMpLnRleHQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhlSWZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoIGJDb25maWcucGFnZUNvbnRhaW5lci5zdWJzdHJpbmcoMSkgKS5hcHBlbmRDaGlsZChzY3JpcHQpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmKCAkKHRoaXMpLmF0dHIoJ3NyYycpICE9PSBudWxsICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdCA9IHRoZUlmcmFtZS5jb250ZW50V2luZG93LmRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoXCJzY3JpcHRcIik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdC50eXBlID0gJ3RleHQvamF2YXNjcmlwdCc7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdC5zcmMgPSAkKHRoaXMpLmF0dHIoJ3NyYycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGVJZnJhbWUuY29udGVudFdpbmRvdy5kb2N1bWVudC5nZXRFbGVtZW50QnlJZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyLnN1YnN0cmluZygxKSApLmFwcGVuZENoaWxkKHNjcmlwdCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICBuZXdEb2NNYWluUGFyZW50LmFwcGVuZCggJCh0b0FkZCkgKTtcblxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgdmFyIG5ld0lucHV0ID0gJCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwicGFnZXNbJyskKCcjcGFnZXMgbGk6ZXEoJysoJCh0aGlzKS5pbmRleCgpKzEpKycpIGE6Zmlyc3QnKS50ZXh0KCkrJ11cIiBjbGFzcz1cInBhZ2VzXCIgdmFsdWU9XCJcIj4nKTtcbiAgICAgICAgICAgICAgICAkKCcjZXhwb3J0TW9kYWwgZm9ybScpLnByZXBlbmQoIG5ld0lucHV0ICk7XG4gICAgICAgICAgICAgICAgbmV3SW5wdXQudmFsKCBcIjxodG1sPlwiKyQoJ2lmcmFtZSNza2VsZXRvbicpLmNvbnRlbnRzKCkuZmluZCgnaHRtbCcpLmh0bWwoKStcIjwvaHRtbD5cIiApO1xuXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBldmVudCBoYW5kbGVyIGZvciB0aGUgZXhwb3J0IGZyb20gc3VibWl0XG4gICAgICAgICovXG4gICAgICAgIGV4cG9ydEZvcm1TdWJtaXQ6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKCcjZXhwb3J0TW9kYWwgPiBmb3JtICNleHBvcnRTdWJtaXQnKS5oaWRlKCcnKTtcbiAgICAgICAgICAgICQoJyNleHBvcnRNb2RhbCA+IGZvcm0gI2V4cG9ydENhbmNlbCcpLnRleHQoJ0Nsb3NlIFdpbmRvdycpO1xuXG4gICAgICAgIH1cblxuICAgIH07XG5cbiAgICBiZXhwb3J0LmluaXQoKTtcblxufSgpKTsiLCIoZnVuY3Rpb24gKCl7XG5cdFwidXNlIHN0cmljdFwiO1xuXG4gICAgdmFyIGJDb25maWcgPSByZXF1aXJlKCcuL2NvbmZpZy5qcycpO1xuICAgIHZhciBzaXRlQnVpbGRlciA9IHJlcXVpcmUoJy4vYnVpbGRlci5qcycpO1xuICAgIHZhciBlZGl0b3IgPSByZXF1aXJlKCcuL3N0eWxlZWRpdG9yLmpzJykuc3R5bGVlZGl0b3I7XG4gICAgdmFyIGFwcFVJID0gcmVxdWlyZSgnLi91aS5qcycpLmFwcFVJO1xuXG4gICAgdmFyIGltYWdlTGlicmFyeSA9IHtcblxuICAgICAgICBpbWFnZU1vZGFsOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnaW1hZ2VNb2RhbCcpLFxuICAgICAgICBpbnB1dEltYWdlVXBsb2FkOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnaW1hZ2VGaWxlJyksXG4gICAgICAgIGJ1dHRvblVwbG9hZEltYWdlOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgndXBsb2FkSW1hZ2VCdXR0b24nKSxcbiAgICAgICAgaW1hZ2VMaWJyYXJ5TGlua3M6IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJy5pbWFnZXMgPiAuaW1hZ2UgLmJ1dHRvbnMgLmJ0bi1wcmltYXJ5LCAuaW1hZ2VzIC5pbWFnZVdyYXAgPiBhJyksLy91c2VkIGluIHRoZSBsaWJyYXJ5LCBvdXRzaWRlIHRoZSBidWlsZGVyIFVJXG4gICAgICAgIG15SW1hZ2VzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbXlJbWFnZXMnKSwvL3VzZWQgaW4gdGhlIGltYWdlIGxpYnJhcnksIG91dHNpZGUgdGhlIGJ1aWxkZXIgVUlcblxuICAgICAgICBpbml0OiBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAkKHRoaXMuaW1hZ2VNb2RhbCkub24oJ3Nob3cuYnMubW9kYWwnLCB0aGlzLmltYWdlTGlicmFyeSk7XG4gICAgICAgICAgICAkKHRoaXMuaW5wdXRJbWFnZVVwbG9hZCkub24oJ2NoYW5nZScsIHRoaXMuaW1hZ2VJbnB1dENoYW5nZSk7XG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uVXBsb2FkSW1hZ2UpLm9uKCdjbGljaycsIHRoaXMudXBsb2FkSW1hZ2UpO1xuICAgICAgICAgICAgJCh0aGlzLmltYWdlTGlicmFyeUxpbmtzKS5vbignY2xpY2snLCB0aGlzLmltYWdlSW5Nb2RhbCk7XG4gICAgICAgICAgICAkKHRoaXMubXlJbWFnZXMpLm9uKCdjbGljaycsICcuYnV0dG9ucyAuYnRuLWRhbmdlcicsIHRoaXMuZGVsZXRlSW1hZ2UpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgaW1hZ2UgbGlicmFyeSBtb2RhbFxuICAgICAgICAqL1xuICAgICAgICBpbWFnZUxpYnJhcnk6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKCcjaW1hZ2VNb2RhbCcpLm9mZignY2xpY2snLCAnLmltYWdlIGJ1dHRvbi51c2VJbWFnZScpO1xuXG4gICAgICAgICAgICAkKCcjaW1hZ2VNb2RhbCcpLm9uKCdjbGljaycsICcuaW1hZ2UgYnV0dG9uLnVzZUltYWdlJywgZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgIC8vdXBkYXRlIGxpdmUgaW1hZ2VcbiAgICAgICAgICAgICAgICAkKGVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmF0dHIoJ3NyYycsICQodGhpcykuYXR0cignZGF0YS11cmwnKSk7XG5cbiAgICAgICAgICAgICAgICAvL3VwZGF0ZSBpbWFnZSBVUkwgZmllbGRcbiAgICAgICAgICAgICAgICAkKCdpbnB1dCNpbWFnZVVSTCcpLnZhbCggJCh0aGlzKS5hdHRyKCdkYXRhLXVybCcpICk7XG5cbiAgICAgICAgICAgICAgICAvL2hpZGUgbW9kYWxcbiAgICAgICAgICAgICAgICAkKCcjaW1hZ2VNb2RhbCcpLm1vZGFsKCdoaWRlJyk7XG5cbiAgICAgICAgICAgICAgICAvL2hlaWdodCBhZGp1c3RtZW50IG9mIHRoZSBpZnJhbWUgaGVpZ2h0QWRqdXN0bWVudFxuXHRcdFx0XHRlZGl0b3IuYWN0aXZlRWxlbWVudC5wYXJlbnRCbG9jay5oZWlnaHRBZGp1c3RtZW50KCk7XG5cbiAgICAgICAgICAgICAgICAvL3dlJ3ZlIGdvdCBwZW5kaW5nIGNoYW5nZXNcbiAgICAgICAgICAgICAgICBzaXRlQnVpbGRlci5zaXRlLnNldFBlbmRpbmdDaGFuZ2VzKHRydWUpO1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS51bmJpbmQoJ2NsaWNrJyk7XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgaW1hZ2UgdXBsb2FkIGlucHV0IGNoYW5lZyBldmVudCBoYW5kbGVyXG4gICAgICAgICovXG4gICAgICAgIGltYWdlSW5wdXRDaGFuZ2U6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICBpZiggJCh0aGlzKS52YWwoKSA9PT0gJycgKSB7XG4gICAgICAgICAgICAgICAgLy9ubyBmaWxlLCBkaXNhYmxlIHN1Ym1pdCBidXR0b25cbiAgICAgICAgICAgICAgICAkKCdidXR0b24jdXBsb2FkSW1hZ2VCdXR0b24nKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgLy9nb3QgYSBmaWxlLCBlbmFibGUgYnV0dG9uXG4gICAgICAgICAgICAgICAgJCgnYnV0dG9uI3VwbG9hZEltYWdlQnV0dG9uJykucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICB1cGxvYWQgYW4gaW1hZ2UgdG8gdGhlIGltYWdlIGxpYnJhcnlcbiAgICAgICAgKi9cbiAgICAgICAgdXBsb2FkSW1hZ2U6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICBpZiggJCgnaW5wdXQjaW1hZ2VGaWxlJykudmFsKCkgIT09ICcnICkge1xuXG4gICAgICAgICAgICAgICAgLy9yZW1vdmUgb2xkIGFsZXJ0c1xuICAgICAgICAgICAgICAgICQoJyNpbWFnZU1vZGFsIC5tb2RhbC1hbGVydHMgPiAqJykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgICAgICAvL2Rpc2FibGUgYnV0dG9uXG4gICAgICAgICAgICAgICAgJCgnYnV0dG9uI3VwbG9hZEltYWdlQnV0dG9uJykuYWRkQ2xhc3MoJ2Rpc2FibGUnKTtcblxuICAgICAgICAgICAgICAgIC8vc2hvdyBsb2FkZXJcbiAgICAgICAgICAgICAgICAkKCcjaW1hZ2VNb2RhbCAubG9hZGVyJykuZmFkZUluKDUwMCk7XG5cbiAgICAgICAgICAgICAgICB2YXIgZm9ybSA9ICQoJ2Zvcm0jaW1hZ2VVcGxvYWRGb3JtJyk7XG4gICAgICAgICAgICAgICAgdmFyIGZvcm1kYXRhID0gZmFsc2U7XG5cbiAgICAgICAgICAgICAgICBpZiAod2luZG93LkZvcm1EYXRhKXtcbiAgICAgICAgICAgICAgICAgICAgZm9ybWRhdGEgPSBuZXcgRm9ybURhdGEoZm9ybVswXSk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgdmFyIGZvcm1BY3Rpb24gPSBmb3JtLmF0dHIoJ2FjdGlvbicpO1xuXG4gICAgICAgICAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgICAgICAgICAgdXJsIDogZm9ybUFjdGlvbixcbiAgICAgICAgICAgICAgICAgICAgZGF0YSA6IGZvcm1kYXRhID8gZm9ybWRhdGEgOiBmb3JtLnNlcmlhbGl6ZSgpLFxuICAgICAgICAgICAgICAgICAgICBjYWNoZSA6IGZhbHNlLFxuICAgICAgICAgICAgICAgICAgICBjb250ZW50VHlwZSA6IGZhbHNlLFxuICAgICAgICAgICAgICAgICAgICBwcm9jZXNzRGF0YSA6IGZhbHNlLFxuICAgICAgICAgICAgICAgICAgICBkYXRhVHlwZTogXCJqc29uXCIsXG4gICAgICAgICAgICAgICAgICAgIHR5cGUgOiAnUE9TVCdcbiAgICAgICAgICAgICAgICB9KS5kb25lKGZ1bmN0aW9uKHJldCl7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9lbmFibGUgYnV0dG9uXG4gICAgICAgICAgICAgICAgICAgICQoJ2J1dHRvbiN1cGxvYWRJbWFnZUJ1dHRvbicpLmFkZENsYXNzKCdkaXNhYmxlJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9oaWRlIGxvYWRlclxuICAgICAgICAgICAgICAgICAgICAkKCcjaW1hZ2VNb2RhbCAubG9hZGVyJykuZmFkZU91dCg1MDApO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCByZXQucmVzcG9uc2VDb2RlID09PSAwICkgey8vZXJyb3JcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2ltYWdlTW9kYWwgLm1vZGFsLWFsZXJ0cycpLmFwcGVuZCggJChyZXQucmVzcG9uc2VIVE1MKSApO1xuXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMSApIHsvL3N1Y2Nlc3NcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9hcHBlbmQgbXkgaW1hZ2VcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyNteUltYWdlc1RhYiA+IConKS5yZW1vdmUoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyNteUltYWdlc1RhYicpLmFwcGVuZCggJChyZXQubXlJbWFnZXMpICk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjaW1hZ2VNb2RhbCAubW9kYWwtYWxlcnRzJykuYXBwZW5kKCAkKHJldC5yZXNwb25zZUhUTUwpICk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKXskKCcjaW1hZ2VNb2RhbCAubW9kYWwtYWxlcnRzID4gKicpLmZhZGVPdXQoNTAwKTt9LCAzMDAwKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgIGFsZXJ0KCdObyBpbWFnZSBzZWxlY3RlZCcpO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBkaXNwbGF5cyBpbWFnZSBpbiBtb2RhbFxuICAgICAgICAqL1xuICAgICAgICBpbWFnZUluTW9kYWw6IGZ1bmN0aW9uKGUpIHtcblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgXHRcdHZhciB0aGVTcmMgPSAkKHRoaXMpLmNsb3Nlc3QoJy5pbWFnZScpLmZpbmQoJ2ltZycpLmF0dHIoJ3NyYycpO1xuXG4gICAgXHRcdCQoJ2ltZyN0aGVQaWMnKS5hdHRyKCdzcmMnLCB0aGVTcmMpO1xuXG4gICAgXHRcdCQoJyN2aWV3UGljJykubW9kYWwoJ3Nob3cnKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGRlbGV0ZXMgYW4gaW1hZ2UgZnJvbSB0aGUgbGlicmFyeVxuICAgICAgICAqL1xuICAgICAgICBkZWxldGVJbWFnZTogZnVuY3Rpb24oZSkge1xuXG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICBcdFx0dmFyIHRvRGVsID0gJCh0aGlzKS5jbG9zZXN0KCcuaW1hZ2UnKTtcbiAgICBcdFx0dmFyIHRoZVVSTCA9ICQodGhpcykuYXR0cignZGF0YS1pbWcnKTtcblxuICAgIFx0XHQkKCcjZGVsZXRlSW1hZ2VNb2RhbCcpLm1vZGFsKCdzaG93Jyk7XG5cbiAgICBcdFx0JCgnYnV0dG9uI2RlbGV0ZUltYWdlQnV0dG9uJykuY2xpY2soZnVuY3Rpb24oKXtcblxuICAgIFx0XHRcdCQodGhpcykuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICBcdFx0XHR2YXIgdGhlQnV0dG9uID0gJCh0aGlzKTtcblxuICAgIFx0XHRcdCQuYWpheCh7XG4gICAgICAgICAgICAgICAgICAgIHVybDogYXBwVUkuc2l0ZVVybCtcImFzc2V0cy9kZWxJbWFnZVwiLFxuICAgIFx0XHRcdFx0ZGF0YToge2ZpbGU6IHRoZVVSTH0sXG4gICAgXHRcdFx0XHR0eXBlOiAncG9zdCdcbiAgICBcdFx0XHR9KS5kb25lKGZ1bmN0aW9uKCl7XG5cbiAgICBcdFx0XHRcdHRoZUJ1dHRvbi5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgIFx0XHRcdFx0JCgnI2RlbGV0ZUltYWdlTW9kYWwnKS5tb2RhbCgnaGlkZScpO1xuXG4gICAgXHRcdFx0XHR0b0RlbC5mYWRlT3V0KDgwMCwgZnVuY3Rpb24oKXtcblxuICAgIFx0XHRcdFx0XHQkKHRoaXMpLnJlbW92ZSgpO1xuXG4gICAgXHRcdFx0XHR9KTtcblxuICAgIFx0XHRcdH0pO1xuXG5cbiAgICBcdFx0fSk7XG5cbiAgICAgICAgfVxuXG4gICAgfTtcblxuICAgIGltYWdlTGlicmFyeS5pbml0KCk7XG5cbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cblx0dmFyIGJDb25maWcgPSByZXF1aXJlKCcuL2NvbmZpZy5qcycpO1xuXHR2YXIgc2l0ZUJ1aWxkZXIgPSByZXF1aXJlKCcuL2J1aWxkZXIuanMnKTtcblxuXHR2YXIgcHJldmlldyA9IHtcblxuICAgICAgICBtb2RhbFByZXZpZXc6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwcmV2aWV3TW9kYWwnKSxcbiAgICAgICAgYnV0dG9uUHJldmlldzogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2J1dHRvblByZXZpZXcnKSxcblxuICAgICAgICBpbml0OiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy9ldmVudHNcbiAgICAgICAgICAgICQodGhpcy5tb2RhbFByZXZpZXcpLm9uKCdzaG93bi5icy5tb2RhbCcsIHRoaXMucHJlcFByZXZpZXcpO1xuICAgICAgICAgICAgJCh0aGlzLm1vZGFsUHJldmlldykub24oJ3Nob3cuYnMubW9kYWwnLCB0aGlzLnByZXBQcmV2aWV3TGluayk7XG5cbiAgICAgICAgICAgIC8vcmV2ZWFsIHByZXZpZXcgYnV0dG9uXG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uUHJldmlldykuc2hvdygpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgcHJlcGFyZXMgdGhlIHByZXZpZXcgZGF0YVxuICAgICAgICAqL1xuICAgICAgICBwcmVwUHJldmlldzogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICQoJyNwcmV2aWV3TW9kYWwgZm9ybSBpbnB1dFt0eXBlPVwiaGlkZGVuXCJdJykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgIC8vYnVpbGQgdGhlIHBhZ2VcbiAgICAgICAgICAgIHNpdGVCdWlsZGVyLnNpdGUuYWN0aXZlUGFnZS5mdWxsUGFnZSgpO1xuXG4gICAgICAgICAgICB2YXIgbmV3SW5wdXQ7XG5cbiAgICAgICAgICAgIC8vbWFya3VwXG4gICAgICAgICAgICBuZXdJbnB1dCA9ICQoJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cInBhZ2VcIiB2YWx1ZT1cIlwiPicpO1xuICAgICAgICAgICAgJCgnI3ByZXZpZXdNb2RhbCBmb3JtJykucHJlcGVuZCggbmV3SW5wdXQgKTtcbiAgICAgICAgICAgIG5ld0lucHV0LnZhbCggXCI8aHRtbD5cIiskKCdpZnJhbWUjc2tlbGV0b24nKS5jb250ZW50cygpLmZpbmQoJ2h0bWwnKS5odG1sKCkrXCI8L2h0bWw+XCIgKTtcblxuICAgICAgICAgICAgLy9wYWdlIHRpdGxlXG4gICAgICAgICAgICBuZXdJbnB1dCA9ICQoJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cIm1ldGFfdGl0bGVcIiB2YWx1ZT1cIlwiPicpO1xuICAgICAgICAgICAgJCgnI3ByZXZpZXdNb2RhbCBmb3JtJykucHJlcGVuZCggbmV3SW5wdXQgKTtcbiAgICAgICAgICAgIG5ld0lucHV0LnZhbCggc2l0ZUJ1aWxkZXIuc2l0ZS5hY3RpdmVQYWdlLnBhZ2VTZXR0aW5ncy50aXRsZSApO1xuICAgICAgICAgICAgLy9hbGVydChKU09OLnN0cmluZ2lmeShzaXRlQnVpbGRlci5zaXRlLmFjdGl2ZVBhZ2UucGFnZVNldHRpbmdzKSk7XG5cbiAgICAgICAgICAgIC8vcGFnZSBtZXRhIGRlc2NyaXB0aW9uXG4gICAgICAgICAgICBuZXdJbnB1dCA9ICQoJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cIm1ldGFfZGVzY3JpcHRpb25cIiB2YWx1ZT1cIlwiPicpO1xuICAgICAgICAgICAgJCgnI3ByZXZpZXdNb2RhbCBmb3JtJykucHJlcGVuZCggbmV3SW5wdXQgKTtcbiAgICAgICAgICAgIG5ld0lucHV0LnZhbCggc2l0ZUJ1aWxkZXIuc2l0ZS5hY3RpdmVQYWdlLnBhZ2VTZXR0aW5ncy5tZXRhX2Rlc2NyaXB0aW9uICk7XG5cbiAgICAgICAgICAgIC8vcGFnZSBtZXRhIGtleXdvcmRzXG4gICAgICAgICAgICBuZXdJbnB1dCA9ICQoJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cIm1ldGFfa2V5d29yZHNcIiB2YWx1ZT1cIlwiPicpO1xuICAgICAgICAgICAgJCgnI3ByZXZpZXdNb2RhbCBmb3JtJykucHJlcGVuZCggbmV3SW5wdXQgKTtcbiAgICAgICAgICAgIG5ld0lucHV0LnZhbCggc2l0ZUJ1aWxkZXIuc2l0ZS5hY3RpdmVQYWdlLnBhZ2VTZXR0aW5ncy5tZXRhX2tleXdvcmRzICk7XG5cbiAgICAgICAgICAgIC8vcGFnZSBoZWFkZXIgaW5jbHVkZXNcbiAgICAgICAgICAgIG5ld0lucHV0ID0gJCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiaGVhZGVyX2luY2x1ZGVzXCIgdmFsdWU9XCJcIj4nKTtcbiAgICAgICAgICAgICQoJyNwcmV2aWV3TW9kYWwgZm9ybScpLnByZXBlbmQoIG5ld0lucHV0ICk7XG4gICAgICAgICAgICBuZXdJbnB1dC52YWwoIHNpdGVCdWlsZGVyLnNpdGUuYWN0aXZlUGFnZS5wYWdlU2V0dGluZ3MuaGVhZGVyX2luY2x1ZGVzICk7XG5cbiAgICAgICAgICAgIC8vcGFnZSBjc3NcbiAgICAgICAgICAgIG5ld0lucHV0ID0gJCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwicGFnZV9jc3NcIiB2YWx1ZT1cIlwiPicpO1xuICAgICAgICAgICAgJCgnI3ByZXZpZXdNb2RhbCBmb3JtJykucHJlcGVuZCggbmV3SW5wdXQgKTtcbiAgICAgICAgICAgIG5ld0lucHV0LnZhbCggc2l0ZUJ1aWxkZXIuc2l0ZS5hY3RpdmVQYWdlLnBhZ2VTZXR0aW5ncy5wYWdlX2NzcyApO1xuXG4gICAgICAgICAgICAvL3NpdGUgSURcbiAgICAgICAgICAgIG5ld0lucHV0ID0gJCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwic2l0ZUlEXCIgdmFsdWU9XCJcIj4nKTtcbiAgICAgICAgICAgICQoJyNwcmV2aWV3TW9kYWwgZm9ybScpLnByZXBlbmQoIG5ld0lucHV0ICk7XG4gICAgICAgICAgICBuZXdJbnB1dC52YWwoIHNpdGVCdWlsZGVyLnNpdGUuZGF0YS5pZCApO1xuXG4gICAgICAgICAgICAvL0NTUkYgdG9rZW5cbiAgICAgICAgICAgIG5ld0lucHV0ID0gJCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiX3Rva2VuXCIgdmFsdWU9XCJcIj4nKTtcbiAgICAgICAgICAgICQoJyNwcmV2aWV3TW9kYWwgZm9ybScpLnByZXBlbmQoIG5ld0lucHV0ICk7XG4gICAgICAgICAgICBuZXdJbnB1dC52YWwoICQoJ21ldGFbbmFtZT1cImNzcmYtdG9rZW5cIl0nKS5hdHRyKCdjb250ZW50JykgKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHByZXBhcmVzIHRoZSBhY3R1YWwgcHJldmlldyBsaW5rXG4gICAgICAgICovXG4gICAgICAgIHByZXBQcmV2aWV3TGluazogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICQoJyNwYWdlUHJldmlld0xpbmsnKS5hdHRyKCAnaHJlZicsICQoJyNwYWdlUHJldmlld0xpbmsnKS5hdHRyKCdkYXRhLWRlZnVybCcpKyQoJyNwYWdlcyBsaS5hY3RpdmUgYScpLnRleHQoKSApO1xuXG4gICAgICAgIH1cblxuICAgIH07XG5cbiAgICBwcmV2aWV3LmluaXQoKTtcblxufSgpKTsiLCIoZnVuY3Rpb24gKCkge1xuXHRcInVzZSBzdHJpY3RcIjtcblxuXHR2YXIgYkNvbmZpZyA9IHJlcXVpcmUoJy4vY29uZmlnLmpzJyk7XG5cdHZhciBzaXRlQnVpbGRlciA9IHJlcXVpcmUoJy4vYnVpbGRlci5qcycpO1xuXHR2YXIgYXBwVUkgPSByZXF1aXJlKCcuL3VpLmpzJykuYXBwVUk7XG5cblx0dmFyIHB1Ymxpc2ggPSB7XG5cbiAgICAgICAgYnV0dG9uUHVibGlzaDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3B1Ymxpc2hQYWdlJyksXG4gICAgICAgIGJ1dHRvblNhdmVQZW5kaW5nQmVmb3JlUHVibGlzaGluZzogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2J1dHRvblNhdmVQZW5kaW5nQmVmb3JlUHVibGlzaGluZycpLFxuICAgICAgICBwdWJsaXNoTW9kYWw6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwdWJsaXNoTW9kYWwnKSxcbiAgICAgICAgYnV0dG9uUHVibGlzaFN1Ym1pdDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3B1Ymxpc2hTdWJtaXQnKSxcbiAgICAgICAgcHVibGlzaEFjdGl2ZTogMCxcbiAgICAgICAgdGhlSXRlbToge30sXG4gICAgICAgIG1vZGFsU2l0ZVNldHRpbmdzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2l0ZVNldHRpbmdzJyksXG5cbiAgICAgICAgaW5pdDogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICQodGhpcy5idXR0b25QdWJsaXNoKS5vbignY2xpY2snLCB0aGlzLmxvYWRQdWJsaXNoTW9kYWwpO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblNhdmVQZW5kaW5nQmVmb3JlUHVibGlzaGluZykub24oJ2NsaWNrJywgdGhpcy5zYXZlQmVmb3JlUHVibGlzaGluZyk7XG4gICAgICAgICAgICAkKHRoaXMucHVibGlzaE1vZGFsKS5vbignY2hhbmdlJywgJ2lucHV0W3R5cGU9Y2hlY2tib3hdJywgdGhpcy5wdWJsaXNoQ2hlY2tib3hFdmVudCk7XG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uUHVibGlzaFN1Ym1pdCkub24oJ2NsaWNrJywgdGhpcy5wdWJsaXNoU2l0ZSk7XG4gICAgICAgICAgICAkKHRoaXMubW9kYWxTaXRlU2V0dGluZ3MpLm9uKCdjbGljaycsICcjc2l0ZVNldHRpbmdzQnJvd3NlRlRQQnV0dG9uLCAubGluaycsIHRoaXMuYnJvd3NlRlRQKTtcbiAgICAgICAgICAgICQodGhpcy5tb2RhbFNpdGVTZXR0aW5ncykub24oJ2NsaWNrJywgJyNmdHBMaXN0SXRlbXMgLmNsb3NlJywgdGhpcy5jbG9zZUZ0cEJyb3dzZXIpO1xuICAgICAgICAgICAgJCh0aGlzLm1vZGFsU2l0ZVNldHRpbmdzKS5vbignY2xpY2snLCAnI3NpdGVTZXR0aW5nc1Rlc3RGVFAnLCB0aGlzLnRlc3RGVFBDb25uZWN0aW9uKTtcblxuICAgICAgICAgICAgLy9zaG93IHRoZSBwdWJsaXNoIGJ1dHRvblxuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblB1Ymxpc2gpLnNob3coKTtcblxuICAgICAgICAgICAgLy9saXN0ZW4gdG8gc2l0ZSBzZXR0aW5ncyBsb2FkIGV2ZW50XG4gICAgICAgICAgICAkKCdib2R5Jykub24oJ3NpdGVTZXR0aW5nc0xvYWQnLCB0aGlzLnNob3dQdWJsaXNoU2V0dGluZ3MpO1xuXG4gICAgICAgICAgICAvL3B1Ymxpc2ggaGFzaD9cbiAgICAgICAgICAgIGlmKCB3aW5kb3cubG9jYXRpb24uaGFzaCA9PT0gXCIjcHVibGlzaFwiICkge1xuICAgICAgICAgICAgICAgICQodGhpcy5idXR0b25QdWJsaXNoKS5jbGljaygpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvLyBoZWFkZXIgdG9vbHRpcHNcbiAgICAgICAgICAgIC8vaWYoIHRoaXMuYnV0dG9uUHVibGlzaC5oYXNBdHRyaWJ1dGUoJ2RhdGEtdG9nZ2xlJykgJiYgdGhpcy5idXR0b25QdWJsaXNoLmdldEF0dHJpYnV0ZSgnZGF0YS10b2dnbGUnKSA9PSAndG9vbHRpcCcgKSB7XG4gICAgICAgICAgICAvLyAgICQodGhpcy5idXR0b25QdWJsaXNoKS50b29sdGlwKCdzaG93Jyk7XG4gICAgICAgICAgICAvLyAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKXskKHRoaXMuYnV0dG9uUHVibGlzaCkudG9vbHRpcCgnaGlkZScpfSwgNTAwMCk7XG4gICAgICAgICAgICAvL31cblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGxvYWRzIHRoZSBwdWJsaXNoIG1vZGFsXG4gICAgICAgICovXG4gICAgICAgIGxvYWRQdWJsaXNoTW9kYWw6IGZ1bmN0aW9uKGUpIHtcblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICBpZiggcHVibGlzaC5wdWJsaXNoQWN0aXZlID09PSAwICkgey8vY2hlY2sgaWYgd2UncmUgY3VycmVudGx5IHB1Ymxpc2hpbmcgYW55dGhpbmdcblxuICAgICAgICAgICAgICAgIC8vaGlkZSBhbGVydHNcbiAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsIC5tb2RhbC1hbGVydHMgPiAqJykuZWFjaChmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCAubW9kYWwtYm9keSA+IC5hbGVydC1zdWNjZXNzJykuaGlkZSgpO1xuXG4gICAgICAgICAgICAgICAgLy9oaWRlIGxvYWRlcnNcbiAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsX2Fzc2V0cyAucHVibGlzaGluZycpLmVhY2goZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLndvcmtpbmcnKS5zaG93KCk7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLmRvbmUnKS5oaWRlKCk7XG4gICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAvL3JlbW92ZSBwdWJsaXNoZWQgY2xhc3MgZnJvbSBhc3NldCBjaGVja2JveGVzXG4gICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbF9hc3NldHMgaW5wdXQnKS5lYWNoKGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQ2xhc3MoJ3B1Ymxpc2hlZCcpO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgLy9kbyB3ZSBoYXZlIHBlbmRpbmcgY2hhbmdlcz9cbiAgICAgICAgICAgICAgICBpZiggc2l0ZUJ1aWxkZXIuc2l0ZS5wZW5kaW5nQ2hhbmdlcyA9PT0gdHJ1ZSApIHsvL3dlJ3ZlIGdvdCBjaGFuZ2VzLCBzYXZlIGZpcnN0XG5cbiAgICAgICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCAjcHVibGlzaFBlbmRpbmdDaGFuZ2VzTWVzc2FnZScpLnNob3coKTtcbiAgICAgICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCAubW9kYWwtYm9keS1jb250ZW50JykuaGlkZSgpO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHsvL2FsbCBzZXQsIGdldCBvbiBpdCB3aXRoIHB1Ymxpc2hpbmdcblxuICAgICAgICAgICAgICAgICAgICAvL2dldCB0aGUgY29ycmVjdCBwYWdlcyBpbiB0aGUgUGFnZXMgc2VjdGlvbiBvZiB0aGUgcHVibGlzaCBtb2RhbFxuICAgICAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsX3BhZ2VzIHRib2R5ID4gKicpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgICAgICQoJyNwYWdlcyBsaTp2aXNpYmxlJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgdGhlUGFnZSA9ICQodGhpcykuZmluZCgnYTpmaXJzdCcpLnRleHQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciB0aGVSb3cgPSAkKCc8dHI+PHRkIGNsYXNzPVwidGV4dC1jZW50ZXJcIiBzdHlsZT1cIndpZHRoOiAzMHB4O1wiPjxsYWJlbCBjbGFzcz1cImNoZWNrYm94IG5vLWxhYmVsXCI+PGlucHV0IHR5cGU9XCJjaGVja2JveFwiIHZhbHVlPVwiJyt0aGVQYWdlKydcIiBpZD1cIlwiIGRhdGEtdHlwZT1cInBhZ2VcIiBuYW1lPVwicGFnZXNbXVwiIGRhdGEtdG9nZ2xlPVwiY2hlY2tib3hcIj48L2xhYmVsPjwvdGQ+PHRkPicrdGhlUGFnZSsnPHNwYW4gY2xhc3M9XCJwdWJsaXNoaW5nXCI+PHNwYW4gY2xhc3M9XCJ3b3JraW5nXCI+UHVibGlzaGluZy4uLiA8aW1nIHNyYz1cIicrYXBwVUkuYmFzZVVybCsnaW1hZ2VzL3B1Ymxpc2hMb2FkZXIuZ2lmXCI+PC9zcGFuPjxzcGFuIGNsYXNzPVwiZG9uZSB0ZXh0LXByaW1hcnlcIj5QdWJsaXNoZWQgJm5ic3A7PHNwYW4gY2xhc3M9XCJmdWktY2hlY2tcIj48L3NwYW4+PC9zcGFuPjwvc3Bhbj48L3RkPjwvdHI+Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vY2hlY2tib3hpZnlcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoZVJvdy5maW5kKCdpbnB1dCcpLnJhZGlvY2hlY2soKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHRoZVJvdy5maW5kKCdpbnB1dCcpLm9uKCdjaGVjayB1bmNoZWNrIHRvZ2dsZScsIGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5jbG9zZXN0KCd0cicpWyQodGhpcykucHJvcCgnY2hlY2tlZCcpID8gJ2FkZENsYXNzJyA6ICdyZW1vdmVDbGFzcyddKCdzZWxlY3RlZC1yb3cnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsX3BhZ2VzIHRib2R5JykuYXBwZW5kKCB0aGVSb3cgKTtcblxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsICNwdWJsaXNoUGVuZGluZ0NoYW5nZXNNZXNzYWdlJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsIC5tb2RhbC1ib2R5LWNvbnRlbnQnKS5zaG93KCk7XG5cbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vZW5hYmxlL2Rpc2FibGUgcHVibGlzaCBidXR0b25cblxuICAgICAgICAgICAgdmFyIGFjdGl2YXRlQnV0dG9uID0gZmFsc2U7XG5cbiAgICAgICAgICAgICQoJyNwdWJsaXNoTW9kYWwgaW5wdXRbdHlwZT1jaGVja2JveF0nKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICBpZiggJCh0aGlzKS5wcm9wKCdjaGVja2VkJykgKSB7XG4gICAgICAgICAgICAgICAgICAgIGFjdGl2YXRlQnV0dG9uID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBpZiggYWN0aXZhdGVCdXR0b24gKSB7XG4gICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hTdWJtaXQnKS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hTdWJtaXQnKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCcpLm1vZGFsKCdzaG93Jyk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBzYXZlcyBwZW5kaW5nIGNoYW5nZXMgYmVmb3JlIHB1Ymxpc2hpbmdcbiAgICAgICAgKi9cbiAgICAgICAgc2F2ZUJlZm9yZVB1Ymxpc2hpbmc6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsICNwdWJsaXNoUGVuZGluZ0NoYW5nZXNNZXNzYWdlJykuaGlkZSgpO1xuICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCAubG9hZGVyJykuc2hvdygpO1xuICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgc2l0ZUJ1aWxkZXIuc2l0ZS5wcmVwRm9yU2F2ZShmYWxzZSk7XG5cbiAgICAgICAgICAgIHZhciBzZXJ2ZXJEYXRhID0ge307XG4gICAgICAgICAgICBzZXJ2ZXJEYXRhLnBhZ2VzID0gc2l0ZUJ1aWxkZXIuc2l0ZS5zaXRlUGFnZXNSZWFkeUZvclNlcnZlcjtcbiAgICAgICAgICAgIGlmKCBzaXRlQnVpbGRlci5zaXRlLnBhZ2VzVG9EZWxldGUubGVuZ3RoID4gMCApIHtcbiAgICAgICAgICAgICAgICBzZXJ2ZXJEYXRhLnRvRGVsZXRlID0gc2l0ZUJ1aWxkZXIuc2l0ZS5wYWdlc1RvRGVsZXRlO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgc2VydmVyRGF0YS5zaXRlRGF0YSA9IHNpdGVCdWlsZGVyLnNpdGUuZGF0YTtcblxuICAgICAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgICAgICB1cmw6IGFwcFVJLnNpdGVVcmwrXCJzaXRlL3NhdmUvMVwiLFxuICAgICAgICAgICAgICAgIHR5cGU6IFwiUE9TVFwiLFxuICAgICAgICAgICAgICAgIGRhdGFUeXBlOiBcImpzb25cIixcbiAgICAgICAgICAgICAgICBkYXRhOiBzZXJ2ZXJEYXRhLFxuICAgICAgICAgICAgfSkuZG9uZShmdW5jdGlvbihyZXMpe1xuXG4gICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCAubG9hZGVyJykuZmFkZU91dCg1MDAsIGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCAubW9kYWwtYWxlcnRzJykuYXBwZW5kKCAkKHJlcy5yZXNwb25zZUhUTUwpICk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9zZWxmLWRlc3RydWN0IHN1Y2Nlc3MgbWVzc2FnZXNcbiAgICAgICAgICAgICAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbigpeyQoJyNwdWJsaXNoTW9kYWwgLm1vZGFsLWFsZXJ0cyAuYWxlcnQtc3VjY2VzcycpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbigpeyQodGhpcykucmVtb3ZlKCk7fSk7fSwgMjUwMCk7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9lbmFibGUgYnV0dG9uXG4gICAgICAgICAgICAgICAgICAgICQoJyNwdWJsaXNoTW9kYWwgI3B1Ymxpc2hQZW5kaW5nQ2hhbmdlc01lc3NhZ2UgLmJ0bi5zYXZlJykucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIGlmKCByZXMucmVzcG9uc2VDb2RlID09PSAxICkgey8vY2hhbmdlcyB3ZXJlIHNhdmVkIHdpdGhvdXQgaXNzdWVzXG5cbiAgICAgICAgICAgICAgICAgICAgLy9ubyBtb3JlIHBlbmRpbmcgY2hhbmdlc1xuICAgICAgICAgICAgICAgICAgICBzaXRlQnVpbGRlci5zaXRlLnNldFBlbmRpbmdDaGFuZ2VzKGZhbHNlKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2dldCB0aGUgY29ycmVjdCBwYWdlcyBpbiB0aGUgUGFnZXMgc2VjdGlvbiBvZiB0aGUgcHVibGlzaCBtb2RhbFxuICAgICAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsX3BhZ2VzIHRib2R5ID4gKicpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgICAgICQoJyNwYWdlcyBsaTp2aXNpYmxlJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgdGhlUGFnZSA9ICQodGhpcykuZmluZCgnYTpmaXJzdCcpLnRleHQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciB0aGVSb3cgPSAkKCc8dHI+PHRkIGNsYXNzPVwidGV4dC1jZW50ZXJcIiBzdHlsZT1cIndpZHRoOiAwcHg7XCI+PGxhYmVsIGNsYXNzPVwiY2hlY2tib3hcIj48aW5wdXQgdHlwZT1cImNoZWNrYm94XCIgdmFsdWU9XCInK3RoZVBhZ2UrJ1wiIGlkPVwiXCIgZGF0YS10eXBlPVwicGFnZVwiIG5hbWU9XCJwYWdlc1tdXCIgZGF0YS10b2dnbGU9XCJjaGVja2JveFwiPjwvbGFiZWw+PC90ZD48dGQ+Jyt0aGVQYWdlKyc8c3BhbiBjbGFzcz1cInB1Ymxpc2hpbmdcIj48c3BhbiBjbGFzcz1cIndvcmtpbmdcIj5QdWJsaXNoaW5nLi4uIDxpbWcgc3JjPVwiJythcHBVSS5iYXNlVXJsKydpbWFnZXMvcHVibGlzaExvYWRlci5naWZcIj48L3NwYW4+PHNwYW4gY2xhc3M9XCJkb25lIHRleHQtcHJpbWFyeVwiPlB1Ymxpc2hlZCAmbmJzcDs8c3BhbiBjbGFzcz1cImZ1aS1jaGVja1wiPjwvc3Bhbj48L3NwYW4+PC9zcGFuPjwvdGQ+PC90cj4nKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9jaGVja2JveGlmeVxuICAgICAgICAgICAgICAgICAgICAgICAgdGhlUm93LmZpbmQoJ2lucHV0JykucmFkaW9jaGVjaygpO1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhlUm93LmZpbmQoJ2lucHV0Jykub24oJ2NoZWNrIHVuY2hlY2sgdG9nZ2xlJywgZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJ3RyJylbJCh0aGlzKS5wcm9wKCdjaGVja2VkJykgPyAnYWRkQ2xhc3MnIDogJ3JlbW92ZUNsYXNzJ10oJ3NlbGVjdGVkLXJvdycpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyNwdWJsaXNoTW9kYWxfcGFnZXMgdGJvZHknKS5hcHBlbmQoIHRoZVJvdyApO1xuXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vc2hvdyBjb250ZW50XG4gICAgICAgICAgICAgICAgICAgICQoJyNwdWJsaXNoTW9kYWwgLm1vZGFsLWJvZHktY29udGVudCcpLmZhZGVJbig1MDApO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGV2ZW50IGhhbmRsZXIgZm9yIHRoZSBjaGVja2JveGVzIGluc2lkZSB0aGUgcHVibGlzaCBtb2RhbFxuICAgICAgICAqL1xuICAgICAgICBwdWJsaXNoQ2hlY2tib3hFdmVudDogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIHZhciBhY3RpdmF0ZUJ1dHRvbiA9IGZhbHNlO1xuXG4gICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsIGlucHV0W3R5cGU9Y2hlY2tib3hdJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgaWYoICQodGhpcykucHJvcCgnY2hlY2tlZCcpICkge1xuICAgICAgICAgICAgICAgICAgICBhY3RpdmF0ZUJ1dHRvbiA9IHRydWU7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBpZiggYWN0aXZhdGVCdXR0b24gKSB7XG5cbiAgICAgICAgICAgICAgICAkKCcjcHVibGlzaFN1Ym1pdCcpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hTdWJtaXQnKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgcHVibGlzaGVzIHRoZSBzZWxlY3RlZCBpdGVtc1xuICAgICAgICAqL1xuICAgICAgICBwdWJsaXNoU2l0ZTogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIC8vdHJhY2sgdGhlIHB1Ymxpc2hpbmcgc3RhdGVcbiAgICAgICAgICAgIHB1Ymxpc2gucHVibGlzaEFjdGl2ZSA9IDE7XG5cbiAgICAgICAgICAgIC8vZGlzYWJsZSBidXR0b25cbiAgICAgICAgICAgICQoJyNwdWJsaXNoU3VibWl0LCAjcHVibGlzaENhbmNlbCcpLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICAvL3JlbW92ZSBleGlzdGluZyBhbGVydHNcbiAgICAgICAgICAgICQoJyNwdWJsaXNoTW9kYWwgLm1vZGFsLWFsZXJ0cyA+IConKS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgLy9wcmVwYXJlIHN0dWZmXG4gICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsIGZvcm0gaW5wdXRbdHlwZT1cImhpZGRlblwiXS5wYWdlJykucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgIC8vbG9vcCB0aHJvdWdoIGFsbCBwYWdlc1xuICAgICAgICAgICAgJCgnI3BhZ2VMaXN0ID4gdWwnKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAvL2V4cG9ydCB0aGlzIHBhZ2U/XG4gICAgICAgICAgICAgICAgaWYoICQoJyNwdWJsaXNoTW9kYWwgI3B1Ymxpc2hNb2RhbF9wYWdlcyBpbnB1dDplcSgnKygkKHRoaXMpLmluZGV4KCkrMSkrJyknKS5wcm9wKCdjaGVja2VkJykgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgLy9ncmFiIHRoZSBza2VsZXRvbiBtYXJrdXBcbiAgICAgICAgICAgICAgICAgICAgdmFyIG5ld0RvY01haW5QYXJlbnQgPSAkKCdpZnJhbWUjc2tlbGV0b24nKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciApO1xuXG4gICAgICAgICAgICAgICAgICAgIC8vZW1wdHkgb3V0IHRoZSBza2VsZXRvblxuICAgICAgICAgICAgICAgICAgICBuZXdEb2NNYWluUGFyZW50LmZpbmQoJyonKS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgICAgICAvL2xvb3AgdGhyb3VnaCBwYWdlIGlmcmFtZXMgYW5kIGdyYWIgdGhlIGJvZHkgc3R1ZmZcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCdpZnJhbWUnKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBhdHRyID0gJCh0aGlzKS5hdHRyKCdkYXRhLXNhbmRib3gnKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHRoZUNvbnRlbnRzO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAodHlwZW9mIGF0dHIgIT09IHR5cGVvZiB1bmRlZmluZWQgJiYgYXR0ciAhPT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGVDb250ZW50cyA9ICQoJyNzYW5kYm94ZXMgIycrYXR0cikuY29udGVudHMoKS5maW5kKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhlQ29udGVudHMgPSAkKHRoaXMpLmNvbnRlbnRzKCkuZmluZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyICk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHRoZUNvbnRlbnRzLmZpbmQoJy5mcmFtZUNvdmVyJykuZWFjaChmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9yZW1vdmUgaW5saW5lIHN0eWxpbmcgbGVmdG92ZXJzXG4gICAgICAgICAgICAgICAgICAgICAgICBmb3IoIHZhciBrZXkgaW4gYkNvbmZpZy5lZGl0YWJsZUl0ZW1zICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhlQ29udGVudHMuZmluZCgga2V5ICkuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQXR0cignZGF0YS1zZWxlY3RvcicpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKCAkKHRoaXMpLmF0dHIoJ3N0eWxlJykgPT09ICcnICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmVBdHRyKCdzdHlsZScpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IGJDb25maWcuZWRpdGFibGVDb250ZW50Lmxlbmd0aDsgKytpKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmNvbnRlbnRzKCkuZmluZCggYkNvbmZpZy5lZGl0YWJsZUNvbnRlbnRbaV0gKS5lYWNoKGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQXR0cignZGF0YS1zZWxlY3RvcicpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciB0b0FkZCA9IHRoZUNvbnRlbnRzLmh0bWwoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy9ncmFiIHNjcmlwdHNcblxuICAgICAgICAgICAgICAgICAgICAgICAgdmFyIHNjcmlwdHMgPSAkKHRoaXMpLmNvbnRlbnRzKCkuZmluZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyICkuZmluZCgnc2NyaXB0Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKCBzY3JpcHRzLnNpemUoKSA+IDAgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB2YXIgdGhlSWZyYW1lID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoXCJza2VsZXRvblwiKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdHMuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciBzY3JpcHQ7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoICQodGhpcykudGV4dCgpICE9PSAnJyApIHsvL3NjcmlwdCB0YWdzIHdpdGggY29udGVudFxuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzY3JpcHQgPSB0aGVJZnJhbWUuY29udGVudFdpbmRvdy5kb2N1bWVudC5jcmVhdGVFbGVtZW50KFwic2NyaXB0XCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2NyaXB0LnR5cGUgPSAndGV4dC9qYXZhc2NyaXB0JztcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdC5pbm5lckhUTUwgPSAkKHRoaXMpLnRleHQoKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRoZUlmcmFtZS5jb250ZW50V2luZG93LmRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCBiQ29uZmlnLnBhZ2VDb250YWluZXIuc3Vic3RyaW5nKDEpICkuYXBwZW5kQ2hpbGQoc2NyaXB0KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYoICQodGhpcykuYXR0cignc3JjJykgIT09IG51bGwgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcmlwdCA9IHRoZUlmcmFtZS5jb250ZW50V2luZG93LmRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoXCJzY3JpcHRcIik7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzY3JpcHQudHlwZSA9ICd0ZXh0L2phdmFzY3JpcHQnO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2NyaXB0LnNyYyA9ICQodGhpcykuYXR0cignc3JjJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGVJZnJhbWUuY29udGVudFdpbmRvdy5kb2N1bWVudC5nZXRFbGVtZW50QnlJZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyLnN1YnN0cmluZygxKSApLmFwcGVuZENoaWxkKHNjcmlwdCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0RvY01haW5QYXJlbnQuYXBwZW5kKCAkKHRvQWRkKSApO1xuXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIHZhciBuZXdJbnB1dCA9ICQoJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgY2xhc3M9XCJwYWdlXCIgbmFtZT1cInhwYWdlc1snKyQoJyNwYWdlcyBsaTplcSgnKygkKHRoaXMpLmluZGV4KCkrMSkrJykgYTpmaXJzdCcpLnRleHQoKSsnXVwiIHZhbHVlPVwiXCI+Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgJCgnI3B1Ymxpc2hNb2RhbCBmb3JtJykucHJlcGVuZCggbmV3SW5wdXQgKTtcblxuICAgICAgICAgICAgICAgICAgICBuZXdJbnB1dC52YWwoIFwiPGh0bWw+XCIrJCgnaWZyYW1lI3NrZWxldG9uJykuY29udGVudHMoKS5maW5kKCdodG1sJykuaHRtbCgpK1wiPC9odG1sPlwiICk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBwdWJsaXNoLnB1Ymxpc2hBc3NldCgpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgcHVibGlzaEFzc2V0OiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIHRvUHVibGlzaCA9ICQoJyNwdWJsaXNoTW9kYWxfYXNzZXRzIGlucHV0W3R5cGU9Y2hlY2tib3hdOmNoZWNrZWQ6bm90KC5wdWJsaXNoZWQsIC50b2dnbGVBbGwpLCAjcHVibGlzaE1vZGFsX3BhZ2VzIGlucHV0W3R5cGU9Y2hlY2tib3hdOmNoZWNrZWQ6bm90KC5wdWJsaXNoZWQsIC50b2dnbGVBbGwpJyk7XG5cbiAgICAgICAgICAgIGlmKCB0b1B1Ymxpc2guc2l6ZSgpID4gMCApIHtcblxuICAgICAgICAgICAgICAgIHB1Ymxpc2gudGhlSXRlbSA9IHRvUHVibGlzaC5maXJzdCgpO1xuXG4gICAgICAgICAgICAgICAgLy9kaXNwbGF5IHRoZSBhc3NldCBsb2FkZXJcbiAgICAgICAgICAgICAgICBwdWJsaXNoLnRoZUl0ZW0uY2xvc2VzdCgndGQnKS5uZXh0KCkuZmluZCgnLnB1Ymxpc2hpbmcnKS5mYWRlSW4oNTAwKTtcblxuICAgICAgICAgICAgICAgIHZhciB0aGVEYXRhO1xuXG4gICAgICAgICAgICAgICAgaWYoIHB1Ymxpc2gudGhlSXRlbS5hdHRyKCdkYXRhLXR5cGUnKSA9PT0gJ3BhZ2UnICkge1xuXG4gICAgICAgICAgICAgICAgICAgIHRoZURhdGEgPSB7c2l0ZV9pZDogJCgnZm9ybSNwdWJsaXNoRm9ybSBpbnB1dFtuYW1lPXNpdGVfaWRdJykudmFsKCksIGl0ZW06IHB1Ymxpc2gudGhlSXRlbS52YWwoKSwgcGFnZUNvbnRlbnQ6ICQoJ2Zvcm0jcHVibGlzaEZvcm0gaW5wdXRbbmFtZT1cInhwYWdlc1snK3B1Ymxpc2gudGhlSXRlbS52YWwoKSsnXVwiXScpLnZhbCgpfTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggcHVibGlzaC50aGVJdGVtLmF0dHIoJ2RhdGEtdHlwZScpID09PSAnYXNzZXQnICkge1xuXG4gICAgICAgICAgICAgICAgICAgIHRoZURhdGEgPSB7c2l0ZV9pZDogJCgnZm9ybSNwdWJsaXNoRm9ybSBpbnB1dFtuYW1lPXNpdGVfaWRdJykudmFsKCksIGl0ZW06IHB1Ymxpc2gudGhlSXRlbS52YWwoKX07XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgICAgICAgICB1cmw6ICQoJ2Zvcm0jcHVibGlzaEZvcm0nKS5hdHRyKCdhY3Rpb24nKStcIi9cIitwdWJsaXNoLnRoZUl0ZW0uYXR0cignZGF0YS10eXBlJyksXG4gICAgICAgICAgICAgICAgICAgIHR5cGU6ICdwb3N0JyxcbiAgICAgICAgICAgICAgICAgICAgZGF0YTogdGhlRGF0YSxcbiAgICAgICAgICAgICAgICAgICAgZGF0YVR5cGU6ICdqc29uJ1xuICAgICAgICAgICAgICAgIH0pLmRvbmUoZnVuY3Rpb24ocmV0KXtcblxuICAgICAgICAgICAgICAgICAgICBpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMCApIHsvL2ZhdGFsIGVycm9yLCBwdWJsaXNoaW5nIHdpbGwgc3RvcFxuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL2hpZGUgaW5kaWNhdG9yc1xuICAgICAgICAgICAgICAgICAgICAgICAgcHVibGlzaC50aGVJdGVtLmNsb3Nlc3QoJ3RkJykubmV4dCgpLmZpbmQoJy53b3JraW5nJykuaGlkZSgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL2VuYWJsZSBidXR0b25zXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjcHVibGlzaFN1Ym1pdCwgI3B1Ymxpc2hDYW5jZWwnKS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyNwdWJsaXNoTW9kYWwgLm1vZGFsLWFsZXJ0cycpLmFwcGVuZCggJChyZXQucmVzcG9uc2VIVE1MKSApO1xuXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMSApIHsvL25vIGlzc3Vlc1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL3Nob3cgZG9uZVxuICAgICAgICAgICAgICAgICAgICAgICAgcHVibGlzaC50aGVJdGVtLmNsb3Nlc3QoJ3RkJykubmV4dCgpLmZpbmQoJy53b3JraW5nJykuaGlkZSgpO1xuICAgICAgICAgICAgICAgICAgICAgICAgcHVibGlzaC50aGVJdGVtLmNsb3Nlc3QoJ3RkJykubmV4dCgpLmZpbmQoJy5kb25lJykuZmFkZUluKCk7XG4gICAgICAgICAgICAgICAgICAgICAgICBwdWJsaXNoLnRoZUl0ZW0uYWRkQ2xhc3MoJ3B1Ymxpc2hlZCcpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBwdWJsaXNoLnB1Ymxpc2hBc3NldCgpO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgLy9wdWJsaXNoaW5nIGlzIGRvbmVcbiAgICAgICAgICAgICAgICBwdWJsaXNoLnB1Ymxpc2hBY3RpdmUgPSAwO1xuXG4gICAgICAgICAgICAgICAgLy9lbmFibGUgYnV0dG9uc1xuICAgICAgICAgICAgICAgICQoJyNwdWJsaXNoU3VibWl0LCAjcHVibGlzaENhbmNlbCcpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgICAgICAgICAgICAgLy9zaG93IG1lc3NhZ2VcbiAgICAgICAgICAgICAgICAkKCcjcHVibGlzaE1vZGFsIC5tb2RhbC1ib2R5ID4gLmFsZXJ0LXN1Y2Nlc3MnKS5mYWRlSW4oNTAwLCBmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7JCgnI3B1Ymxpc2hNb2RhbCAubW9kYWwtYm9keSA+IC5hbGVydC1zdWNjZXNzJykuZmFkZU91dCg1MDApO30sIDI1MDApO1xuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuICAgICAgICBzaG93UHVibGlzaFNldHRpbmdzOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCgnI3NpdGVTZXR0aW5nc1B1Ymxpc2hpbmcnKS5zaG93KCk7XG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgYnJvd3NlIHRoZSBGVFAgY29ubmVjdGlvblxuICAgICAgICAqL1xuICAgICAgICBicm93c2VGVFA6IGZ1bmN0aW9uKGUpIHtcblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgXHRcdC8vZ290IGFsbCB3ZSBuZWVkP1xuXG4gICAgXHRcdGlmKCAkKCcjc2l0ZVNldHRpbmdzX2Z0cFNlcnZlcicpLnZhbCgpID09PSAnJyB8fCAkKCcjc2l0ZVNldHRpbmdzX2Z0cFVzZXInKS52YWwoKSA9PT0gJycgfHwgJCgnI3NpdGVTZXR0aW5nc19mdHBQYXNzd29yZCcpLnZhbCgpID09PSAnJyApIHtcbiAgICAgICAgICAgICAgICBhbGVydCgnUGxlYXNlIG1ha2Ugc3VyZSBhbGwgRlRQIGNvbm5lY3Rpb24gZGV0YWlscyBhcmUgcHJlc2VudCcpO1xuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9jaGVjayBpZiB0aGlzIGlzIGEgZGVlcGVyIGxldmVsIGxpbmtcbiAgICBcdFx0aWYoICQodGhpcykuaGFzQ2xhc3MoJ2xpbmsnKSApIHtcblxuICAgIFx0XHRcdGlmKCAkKHRoaXMpLmhhc0NsYXNzKCdiYWNrJykgKSB7XG5cbiAgICBcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3NfZnRwUGF0aCcpLnZhbCggJCh0aGlzKS5hdHRyKCdocmVmJykgKTtcblxuICAgIFx0XHRcdH0gZWxzZSB7XG5cbiAgICBcdFx0XHRcdC8vaWYgc28sIHdlJ2xsIGNoYW5nZSB0aGUgcGF0aCBiZWZvcmUgY29ubmVjdGluZ1xuXG4gICAgXHRcdFx0XHRpZiggJCgnI3NpdGVTZXR0aW5nc19mdHBQYXRoJykudmFsKCkuc3Vic3RyKCAkKCcjc2l0ZVNldHRpbmdzX2Z0cFBhdGgnKS52YWwubGVuZ3RoIC0gMSApID09PSAnLycgKSB7Ly9wcmVwZW5kIFwiL1wiXG5cbiAgICBcdFx0XHRcdFx0JCgnI3NpdGVTZXR0aW5nc19mdHBQYXRoJykudmFsKCAkKCcjc2l0ZVNldHRpbmdzX2Z0cFBhdGgnKS52YWwoKSskKHRoaXMpLmF0dHIoJ2hyZWYnKSApO1xuXG4gICAgXHRcdFx0XHR9IGVsc2Uge1xuXG4gICAgXHRcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3NfZnRwUGF0aCcpLnZhbCggJCgnI3NpdGVTZXR0aW5nc19mdHBQYXRoJykudmFsKCkrXCIvXCIrJCh0aGlzKS5hdHRyKCdocmVmJykgKTtcblxuICAgIFx0XHRcdFx0fVxuXG4gICAgXHRcdFx0fVxuXG5cbiAgICBcdFx0fVxuXG4gICAgXHRcdC8vZGVzdHJveSBhbGwgYWxlcnRzXG5cbiAgICBcdFx0JCgnI2Z0cEFsZXJ0cyAuYWxlcnQnKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcbiAgICBcdFx0XHQkKHRoaXMpLnJlbW92ZSgpO1xuICAgIFx0XHR9KTtcblxuICAgIFx0XHQvL2Rpc2FibGUgYnV0dG9uXG4gICAgXHRcdCQodGhpcykuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICBcdFx0Ly9yZW1vdmUgZXhpc3RpbmcgbGlua3NcbiAgICBcdFx0JCgnI2Z0cExpc3RJdGVtcyA+IConKS5yZW1vdmUoKTtcblxuICAgIFx0XHQvL3Nob3cgZnRwIHNlY3Rpb25cbiAgICBcdFx0JCgnI2Z0cEJyb3dzZSAubG9hZGVyRnRwJykuc2hvdygpO1xuICAgIFx0XHQkKCcjZnRwQnJvd3NlJykuc2xpZGVEb3duKDUwMCk7XG5cbiAgICBcdFx0dmFyIHRoZUJ1dHRvbiA9ICQodGhpcyk7XG5cbiAgICBcdFx0JC5hamF4KHtcbiAgICAgICAgICAgICAgICB1cmw6IGFwcFVJLnNpdGVVcmwrXCJzaXRlL2Nvbm5lY3RcIixcbiAgICBcdFx0XHR0eXBlOiAncG9zdCcsXG4gICAgXHRcdFx0ZGF0YVR5cGU6ICdqc29uJyxcbiAgICBcdFx0XHRkYXRhOiAkKCdmb3JtI3NpdGVTZXR0aW5nc0Zvcm0nKS5zZXJpYWxpemVBcnJheSgpXG4gICAgXHRcdH0pLmRvbmUoZnVuY3Rpb24ocmV0KXtcblxuICAgIFx0XHRcdC8vZW5hYmxlIGJ1dHRvblxuICAgIFx0XHRcdHRoZUJ1dHRvbi5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgIFx0XHRcdC8vaGlkZSBsb2FkaW5nXG4gICAgXHRcdFx0JCgnI2Z0cEJyb3dzZSAubG9hZGVyRnRwJykuaGlkZSgpO1xuXG4gICAgXHRcdFx0aWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDAgKSB7Ly9lcnJvclxuICAgIFx0XHRcdFx0JCgnI2Z0cEFsZXJ0cycpLmFwcGVuZCggJChyZXQucmVzcG9uc2VIVE1MKSApO1xuICAgIFx0XHRcdH0gZWxzZSBpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMSApIHsvL2FsbCBnb29kXG4gICAgXHRcdFx0XHQkKCcjZnRwTGlzdEl0ZW1zJykuYXBwZW5kKCAkKHJldC5yZXNwb25zZUhUTUwpICk7XG4gICAgXHRcdFx0fVxuXG4gICAgXHRcdH0pO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgaGlkZXMvY2xvc2VzIHRoZSBGVFAgYnJvd3NlclxuICAgICAgICAqL1xuICAgICAgICBjbG9zZUZ0cEJyb3dzZXI6IGZ1bmN0aW9uKGUpIHtcblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgIFx0XHQkKHRoaXMpLmNsb3Nlc3QoJyNmdHBCcm93c2UnKS5zbGlkZVVwKDUwMCk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgICAvKlxuICAgICAgICAgICAgdGVzdHMgdGhlIEZUUCBjb25uZWN0aW9uIHdpdGggdGhlIHByb3ZpZGVkIGRldGFpbHNcbiAgICAgICAgKi9cbiAgICAgICAgdGVzdEZUUENvbm5lY3Rpb246IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2dvdCBhbGwgd2UgbmVlZD9cbiAgICBcdFx0aWYoICQoJyNzaXRlU2V0dGluZ3NfZnRwU2VydmVyJykudmFsKCkgPT09ICcnIHx8ICQoJyNzaXRlU2V0dGluZ3NfZnRwVXNlcicpLnZhbCgpID09PSAnJyB8fCAkKCcjc2l0ZVNldHRpbmdzX2Z0cFBhc3N3b3JkJykudmFsKCkgPT09ICcnICkge1xuICAgICAgICAgICAgICAgIGFsZXJ0KCdQbGVhc2UgbWFrZSBzdXJlIGFsbCBGVFAgY29ubmVjdGlvbiBkZXRhaWxzIGFyZSBwcmVzZW50Jyk7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuXG4gICAgXHRcdC8vZGVzdHJveSBhbGwgYWxlcnRzXG4gICAgICAgICAgICAkKCcjZnRwVGVzdEFsZXJ0cyAuYWxlcnQnKS5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICBcdFx0Ly9kaXNhYmxlIGJ1dHRvblxuICAgIFx0XHQkKHRoaXMpLmFkZENsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgXHRcdC8vc2hvdyBsb2FkaW5nIGluZGljYXRvclxuICAgIFx0XHQkKHRoaXMpLm5leHQoKS5mYWRlSW4oNTAwKTtcblxuICAgICAgICAgICAgdmFyIHRoZUJ1dHRvbiA9ICQodGhpcyk7XG5cbiAgICBcdFx0JC5hamF4KHtcbiAgICAgICAgICAgICAgICB1cmw6IGFwcFVJLnNpdGVVcmwrXCJzaXRlL2Z0cHRlc3RcIixcbiAgICBcdFx0XHR0eXBlOiAncG9zdCcsXG4gICAgXHRcdFx0ZGF0YVR5cGU6ICdqc29uJyxcbiAgICBcdFx0XHRkYXRhOiAkKCdmb3JtI3NpdGVTZXR0aW5nc0Zvcm0nKS5zZXJpYWxpemVBcnJheSgpXG4gICAgXHRcdH0pLmRvbmUoZnVuY3Rpb24ocmV0KXtcblxuICAgIFx0XHRcdC8vZW5hYmxlIGJ1dHRvblxuICAgIFx0XHRcdHRoZUJ1dHRvbi5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcbiAgICAgICAgICAgICAgICB0aGVCdXR0b24ubmV4dCgpLmZhZGVPdXQoNTAwKTtcblxuICAgIFx0XHRcdGlmKCByZXQucmVzcG9uc2VDb2RlID09PSAwICkgey8vZXJyb3JcbiAgICAgICAgICAgICAgICAgICAgJCgnI2Z0cFRlc3RBbGVydHMnKS5hcHBlbmQoICQocmV0LnJlc3BvbnNlSFRNTCkgKTtcbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoIHJldC5yZXNwb25zZUNvZGUgPT09IDEgKSB7Ly9hbGwgZ29vZFxuICAgICAgICAgICAgICAgICAgICAkKCcjZnRwVGVzdEFsZXJ0cycpLmFwcGVuZCggJChyZXQucmVzcG9uc2VIVE1MKSApO1xuICAgICAgICAgICAgICAgIH1cblxuICAgIFx0XHR9KTtcblxuICAgICAgICB9XG5cbiAgICB9O1xuXG4gICAgcHVibGlzaC5pbml0KCk7XG5cbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cblx0dmFyIHNpdGVCdWlsZGVyID0gcmVxdWlyZSgnLi9idWlsZGVyLmpzJyk7XG5cdHZhciBhcHBVSSA9IHJlcXVpcmUoJy4vdWkuanMnKS5hcHBVSTtcblxuXHR2YXIgcmV2aXNpb25zID0ge1xuXG4gICAgICAgIHNlbGVjdFJldmlzaW9uczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2Ryb3Bkb3duX3JldmlzaW9ucycpLFxuICAgICAgICBidXR0b25SZXZpc2lvbnM6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdidXR0b25fcmV2aXNpb25zRHJvcGRvd24nKSxcblxuICAgICAgICBpbml0OiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCh0aGlzLnNlbGVjdFJldmlzaW9ucykub24oJ2NsaWNrJywgJ2EubGlua19kZWxldGVSZXZpc2lvbicsIHRoaXMuZGVsZXRlUmV2aXNpb24pO1xuICAgICAgICAgICAgJCh0aGlzLnNlbGVjdFJldmlzaW9ucykub24oJ2NsaWNrJywgJ2EubGlua19yZXN0b3JlUmV2aXNpb24nLCB0aGlzLnJlc3RvcmVSZXZpc2lvbik7XG4gICAgICAgICAgICAkKGRvY3VtZW50KS5vbignY2hhbmdlUGFnZScsICdib2R5JywgdGhpcy5sb2FkUmV2aXNpb25zKTtcblxuICAgICAgICAgICAgLy9yZXZlYWwgdGhlIHJldmlzaW9ucyBkcm9wZG93blxuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblJldmlzaW9ucykuc2hvdygpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgZGVsZXRlcyBhIHNpbmdsZSByZXZpc2lvblxuICAgICAgICAqL1xuICAgICAgICBkZWxldGVSZXZpc2lvbjogZnVuY3Rpb24oZSkge1xuXG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgICAgIHZhciB0aGVMaW5rID0gJCh0aGlzKTtcblxuICAgICAgICAgICAgaWYoIGNvbmZpcm0oJ0FyZSB5b3Ugc3VyZSB5b3Ugd2FudCB0byBkZWxldGUgdGhpcyByZXZpc2lvbj8nKSApIHtcblxuICAgICAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgICAgIHVybDogJCh0aGlzKS5hdHRyKCdocmVmJyksXG4gICAgICAgICAgICAgICAgICAgIG1ldGhvZDogJ2dldCcsXG4gICAgICAgICAgICAgICAgICAgIGRhdGFUeXBlOiAnanNvbidcbiAgICAgICAgICAgICAgICB9KS5kb25lKGZ1bmN0aW9uKHJldCl7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoIHJldC5jb2RlID09PSAxICkgey8vaWYgc3VjY2Vzc2Z1bGwsIHJlbW92ZSBMSSBmcm9tIGxpc3RcblxuICAgICAgICAgICAgICAgICAgICAgICAgdGhlTGluay5wYXJlbnQoKS5mYWRlT3V0KGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoICQoJ3VsI2Ryb3Bkb3duX3JldmlzaW9ucyBsaScpLnNpemUoKSA9PT0gMCApIHsvL2xpc3QgaXMgZW1wdHksIGhpZGUgcmV2aXNpb25zIGRyb3Bkb3duXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoJyNidXR0b25fcmV2aXNpb25zRHJvcGRvd24gYnV0dG9uJykuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoJyNidXR0b25fcmV2aXNpb25zRHJvcGRvd24nKS5kcm9wZG93bigndG9nZ2xlJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICByZXR1cm4gZmFsc2U7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICByZXN0b3JlcyBhIHJldmlzaW9uXG4gICAgICAgICovXG4gICAgICAgIHJlc3RvcmVSZXZpc2lvbjogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIGlmKCBjb25maXJtKCdBcmUgeW91IHN1cmUgeW91IHdhbnQgdG8gcmVzdG9yZSB0aGlzIHJldmlzaW9uPyBUaGlzIHdvdWxkIG92ZXJ3cml0ZSB0aGUgY3VycmVudCBwYWdlLiBDb250aW51ZT8nKSApIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gdHJ1ZTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgbG9hZHMgcmV2aXNpb25zIGZvciB0aGUgYWN0aXZlIHBhZ2VcbiAgICAgICAgKi9cbiAgICAgICAgbG9hZFJldmlzaW9uczogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICAgICAgdXJsOiBhcHBVSS5zaXRlVXJsK1wic2l0ZS9nZXRSZXZpc2lvbnMvXCIrc2l0ZUJ1aWxkZXIuc2l0ZS5kYXRhLmlkK1wiL1wiK3NpdGVCdWlsZGVyLnNpdGUuYWN0aXZlUGFnZS5uYW1lXG4gICAgICAgICAgICB9KS5kb25lKGZ1bmN0aW9uKHJldCl7XG5cbiAgICAgICAgICAgICAgICBpZiggcmV0ID09PSAnJyApIHtcblxuICAgICAgICAgICAgICAgICAgICAkKCcjYnV0dG9uX3JldmlzaW9uc0Ryb3Bkb3duIGJ1dHRvbicpLmVhY2goZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgICQoJ3VsI2Ryb3Bkb3duX3JldmlzaW9ucycpLmh0bWwoICcnICk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgICAgICQoJ3VsI2Ryb3Bkb3duX3JldmlzaW9ucycpLmh0bWwoIHJldCApO1xuICAgICAgICAgICAgICAgICAgICAkKCcjYnV0dG9uX3JldmlzaW9uc0Ryb3Bkb3duIGJ1dHRvbicpLmVhY2goZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfVxuXG4gICAgfTtcblxuICAgIHJldmlzaW9ucy5pbml0KCk7XG5cbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cblx0dmFyIGFwcFVJID0gcmVxdWlyZSgnLi91aS5qcycpLmFwcFVJO1xuXG5cdHZhciBzaXRlU2V0dGluZ3MgPSB7XG5cbiAgICAgICAgLy9idXR0b25TaXRlU2V0dGluZ3M6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdzaXRlU2V0dGluZ3NCdXR0b24nKSxcblx0XHRidXR0b25TaXRlU2V0dGluZ3MyOiAkKCcuc2l0ZVNldHRpbmdzTW9kYWxCdXR0b24nKSxcbiAgICAgICAgYnV0dG9uU2F2ZVNpdGVTZXR0aW5nczogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NhdmVTaXRlU2V0dGluZ3NCdXR0b24nKSxcblxuICAgICAgICBpbml0OiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgLy8kKHRoaXMuYnV0dG9uU2l0ZVNldHRpbmdzKS5vbignY2xpY2snLCB0aGlzLnNpdGVTZXR0aW5nc01vZGFsKTtcblx0XHRcdHRoaXMuYnV0dG9uU2l0ZVNldHRpbmdzMi5vbignY2xpY2snLCB0aGlzLnNpdGVTZXR0aW5nc01vZGFsKTtcbiAgICAgICAgICAgICQodGhpcy5idXR0b25TYXZlU2l0ZVNldHRpbmdzKS5vbignY2xpY2snLCB0aGlzLnNhdmVTaXRlU2V0dGluZ3MpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGxvYWRzIHRoZSBzaXRlIHNldHRpbmdzIGRhdGFcbiAgICAgICAgKi9cbiAgICAgICAgc2l0ZVNldHRpbmdzTW9kYWw6IGZ1bmN0aW9uKGUpIHtcblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgXHRcdCQoJyNzaXRlU2V0dGluZ3MnKS5tb2RhbCgnc2hvdycpO1xuXG4gICAgXHRcdC8vZGVzdHJveSBhbGwgYWxlcnRzXG4gICAgXHRcdCQoJyNzaXRlU2V0dGluZ3MgLmFsZXJ0JykuZmFkZU91dCg1MDAsIGZ1bmN0aW9uKCl7XG5cbiAgICBcdFx0XHQkKHRoaXMpLnJlbW92ZSgpO1xuXG4gICAgXHRcdH0pO1xuXG4gICAgXHRcdC8vc2V0IHRoZSBzaXRlSURcbiAgICBcdFx0JCgnaW5wdXQjc2l0ZUlEJykudmFsKCAkKHRoaXMpLmF0dHIoJ2RhdGEtc2l0ZWlkJykgKTtcblxuICAgIFx0XHQvL2Rlc3Ryb3kgY3VycmVudCBmb3Jtc1xuICAgIFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5tb2RhbC1ib2R5LWNvbnRlbnQgPiAqJykuZWFjaChmdW5jdGlvbigpe1xuICAgIFx0XHRcdCQodGhpcykucmVtb3ZlKCk7XG4gICAgXHRcdH0pO1xuXG4gICAgICAgICAgICAvL3Nob3cgbG9hZGVyLCBoaWRlIHJlc3RcbiAgICBcdFx0JCgnI3NpdGVTZXR0aW5nc1dyYXBwZXIgLmxvYWRlcicpLnNob3coKTtcbiAgICBcdFx0JCgnI3NpdGVTZXR0aW5nc1dyYXBwZXIgPiAqOm5vdCgubG9hZGVyKScpLmhpZGUoKTtcblxuICAgIFx0XHQvL2xvYWQgc2l0ZSBkYXRhIHVzaW5nIGFqYXhcbiAgICBcdFx0JC5hamF4KHtcbiAgICAgICAgICAgICAgICB1cmw6IGFwcFVJLnNpdGVVcmwrXCJzaXRlQWpheC9cIiskKHRoaXMpLmF0dHIoJ2RhdGEtc2l0ZWlkJyksXG4gICAgXHRcdFx0dHlwZTogJ2dldCcsXG4gICAgXHRcdFx0ZGF0YVR5cGU6ICdqc29uJ1xuICAgIFx0XHR9KS5kb25lKGZ1bmN0aW9uKHJldCl7XG5cbiAgICBcdFx0XHRpZiggcmV0LnJlc3BvbnNlQ29kZSA9PT0gMCApIHsvL2Vycm9yXG5cbiAgICBcdFx0XHRcdC8vaGlkZSBsb2FkZXIsIHNob3cgZXJyb3IgbWVzc2FnZVxuICAgIFx0XHRcdFx0JCgnI3NpdGVTZXR0aW5ncyAubG9hZGVyJykuZmFkZU91dCg1MDAsIGZ1bmN0aW9uKCl7XG5cbiAgICBcdFx0XHRcdFx0JCgnI3NpdGVTZXR0aW5ncyAubW9kYWwtYWxlcnRzJykuYXBwZW5kKCAkKHJldC5yZXNwb25zZUhUTUwpICk7XG5cbiAgICBcdFx0XHRcdH0pO1xuXG4gICAgXHRcdFx0XHQvL2Rpc2FibGUgc3VibWl0IGJ1dHRvblxuICAgIFx0XHRcdFx0JCgnI3NhdmVTaXRlU2V0dGluZ3NCdXR0b24nKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuXG4gICAgXHRcdFx0fSBlbHNlIGlmKCByZXQucmVzcG9uc2VDb2RlID09PSAxICkgey8vYWxsIHdlbGwgOilcblxuICAgIFx0XHRcdFx0Ly9oaWRlIGxvYWRlciwgc2hvdyBkYXRhXG5cbiAgICBcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3MgLmxvYWRlcicpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbigpe1xuXG4gICAgXHRcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3MgLm1vZGFsLWJvZHktY29udGVudCcpLmFwcGVuZCggJChyZXQucmVzcG9uc2VIVE1MKSApO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCdib2R5JykudHJpZ2dlcignc2l0ZVNldHRpbmdzTG9hZCcpO1xuXG4gICAgXHRcdFx0XHR9KTtcblxuICAgIFx0XHRcdFx0Ly9lbmFibGUgc3VibWl0IGJ1dHRvblxuICAgIFx0XHRcdFx0JCgnI3NhdmVTaXRlU2V0dGluZ3NCdXR0b24nKS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgIFx0XHRcdH1cblxuICAgIFx0XHR9KTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHNhdmVzIHRoZSBzaXRlIHNldHRpbmdzXG4gICAgICAgICovXG4gICAgICAgIHNhdmVTaXRlU2V0dGluZ3M6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2Rlc3Ryb3kgYWxsIGFsZXJ0c1xuICAgIFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5hbGVydCcpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbigpe1xuXG4gICAgXHRcdFx0JCh0aGlzKS5yZW1vdmUoKTtcblxuICAgIFx0XHR9KTtcblxuICAgIFx0XHQvL2Rpc2FibGUgYnV0dG9uXG4gICAgXHRcdCQoJyNzYXZlU2l0ZVNldHRpbmdzQnV0dG9uJykuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICBcdFx0Ly9oaWRlIGZvcm0gZGF0YVxuICAgIFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5tb2RhbC1ib2R5LWNvbnRlbnQgPiAqJykuaGlkZSgpO1xuXG4gICAgXHRcdC8vc2hvdyBsb2FkZXJcbiAgICBcdFx0JCgnI3NpdGVTZXR0aW5ncyAubG9hZGVyJykuc2hvdygpO1xuXG4gICAgXHRcdCQuYWpheCh7XG4gICAgICAgICAgICAgICAgdXJsOiBhcHBVSS5zaXRlVXJsK1wic2l0ZUFqYXhVcGRhdGVcIixcbiAgICBcdFx0XHR0eXBlOiAncG9zdCcsXG4gICAgXHRcdFx0ZGF0YVR5cGU6ICdqc29uJyxcbiAgICBcdFx0XHRkYXRhOiAkKCdmb3JtI3NpdGVTZXR0aW5nc0Zvcm0nKS5zZXJpYWxpemVBcnJheSgpXG4gICAgXHRcdH0pLmRvbmUoZnVuY3Rpb24ocmV0KXtcblxuICAgIFx0XHRcdGlmKCByZXQucmVzcG9uc2VDb2RlID09PSAwICkgey8vZXJyb3JcblxuICAgIFx0XHRcdFx0JCgnI3NpdGVTZXR0aW5ncyAubG9hZGVyJykuZmFkZU91dCg1MDAsIGZ1bmN0aW9uKCl7XG5cbiAgICBcdFx0XHRcdFx0JCgnI3NpdGVTZXR0aW5ncyAubW9kYWwtYWxlcnRzJykuYXBwZW5kKCByZXQucmVzcG9uc2VIVE1MICk7XG5cbiAgICBcdFx0XHRcdFx0Ly9zaG93IGZvcm0gZGF0YVxuICAgIFx0XHRcdFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5tb2RhbC1ib2R5LWNvbnRlbnQgPiAqJykuc2hvdygpO1xuXG4gICAgXHRcdFx0XHRcdC8vZW5hYmxlIGJ1dHRvblxuICAgIFx0XHRcdFx0XHQkKCcjc2F2ZVNpdGVTZXR0aW5nc0J1dHRvbicpLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpO1xuXG4gICAgXHRcdFx0XHR9KTtcblxuXG4gICAgXHRcdFx0fSBlbHNlIGlmKCByZXQucmVzcG9uc2VDb2RlID09PSAxICkgey8vYWxsIGlzIHdlbGxcblxuICAgIFx0XHRcdFx0JCgnI3NpdGVTZXR0aW5ncyAubG9hZGVyJykuZmFkZU91dCg1MDAsIGZ1bmN0aW9uKCl7XG5cblxuICAgIFx0XHRcdFx0XHQvL3VwZGF0ZSBzaXRlIG5hbWUgaW4gdG9wIG1lbnVcbiAgICBcdFx0XHRcdFx0JCgnI3NpdGVUaXRsZScpLnRleHQoIHJldC5zaXRlTmFtZSApO1xuXG4gICAgXHRcdFx0XHRcdCQoJyNzaXRlU2V0dGluZ3MgLm1vZGFsLWFsZXJ0cycpLmFwcGVuZCggcmV0LnJlc3BvbnNlSFRNTCApO1xuXG4gICAgXHRcdFx0XHRcdC8vaGlkZSBmb3JtIGRhdGFcbiAgICBcdFx0XHRcdFx0JCgnI3NpdGVTZXR0aW5ncyAubW9kYWwtYm9keS1jb250ZW50ID4gKicpLnJlbW92ZSgpO1xuICAgIFx0XHRcdFx0XHQkKCcjc2l0ZVNldHRpbmdzIC5tb2RhbC1ib2R5LWNvbnRlbnQnKS5hcHBlbmQoIHJldC5yZXNwb25zZUhUTUwyICk7XG5cbiAgICBcdFx0XHRcdFx0Ly9lbmFibGUgYnV0dG9uXG4gICAgXHRcdFx0XHRcdCQoJyNzYXZlU2l0ZVNldHRpbmdzQnV0dG9uJykucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJyk7XG5cbiAgICBcdFx0XHRcdFx0Ly9pcyB0aGUgRlRQIHN0dWZmIGFsbCBnb29kP1xuXG4gICAgXHRcdFx0XHRcdGlmKCByZXQuZnRwT2sgPT09IDEgKSB7Ly95ZXMsIGFsbCBnb29kXG5cbiAgICBcdFx0XHRcdFx0XHQkKCcjcHVibGlzaFBhZ2UnKS5yZW1vdmVBdHRyKCdkYXRhLXRvZ2dsZScpO1xuICAgIFx0XHRcdFx0XHRcdCQoJyNwdWJsaXNoUGFnZSBzcGFuLnRleHQtZGFuZ2VyJykuaGlkZSgpO1xuXG4gICAgXHRcdFx0XHRcdFx0JCgnI3B1Ymxpc2hQYWdlJykudG9vbHRpcCgnZGVzdHJveScpO1xuXG4gICAgXHRcdFx0XHRcdH0gZWxzZSB7Ly9ub3BlLCBjYW4ndCB1c2UgRlRQXG5cbiAgICBcdFx0XHRcdFx0XHQkKCcjcHVibGlzaFBhZ2UnKS5hdHRyKCdkYXRhLXRvZ2dsZScsICd0b29sdGlwJyk7XG4gICAgXHRcdFx0XHRcdFx0JCgnI3B1Ymxpc2hQYWdlIHNwYW4udGV4dC1kYW5nZXInKS5zaG93KCk7XG5cbiAgICBcdFx0XHRcdFx0XHQkKCcjcHVibGlzaFBhZ2UnKS50b29sdGlwKCdzaG93Jyk7XG5cbiAgICBcdFx0XHRcdFx0fVxuXG5cbiAgICBcdFx0XHRcdFx0Ly91cGRhdGUgdGhlIHNpdGUgbmFtZSBpbiB0aGUgc21hbGwgd2luZG93XG4gICAgXHRcdFx0XHRcdCQoJyNzaXRlXycrcmV0LnNpdGVJRCsnIC53aW5kb3cgLnRvcCBiJykudGV4dCggcmV0LnNpdGVOYW1lICk7XG5cbiAgICBcdFx0XHRcdH0pO1xuXG5cbiAgICBcdFx0XHR9XG5cbiAgICBcdFx0fSk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgfTtcblxuICAgIHNpdGVTZXR0aW5ncy5pbml0KCk7XG5cbn0oKSk7IiwiKGZ1bmN0aW9uICgpe1xuXHRcInVzZSBzdHJpY3RcIjtcblxuXHR2YXIgY2FudmFzRWxlbWVudCA9IHJlcXVpcmUoJy4vY2FudmFzRWxlbWVudC5qcycpLkVsZW1lbnQ7XG5cdHZhciBiQ29uZmlnID0gcmVxdWlyZSgnLi9jb25maWcuanMnKTtcblx0dmFyIHNpdGVCdWlsZGVyID0gcmVxdWlyZSgnLi9idWlsZGVyLmpzJyk7XG5cbiAgICB2YXIgc3R5bGVlZGl0b3IgPSB7XG5cbiAgICAgICAgcmFkaW9TdHlsZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ21vZGVTdHlsZScpLFxuICAgICAgICBsYWJlbFN0eWxlTW9kZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ21vZGVTdHlsZUxhYmVsJyksXG4gICAgICAgIGJ1dHRvblNhdmVDaGFuZ2VzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2F2ZVN0eWxpbmcnKSxcbiAgICAgICAgYWN0aXZlRWxlbWVudDoge30sIC8vaG9sZHMgdGhlIGVsZW1lbnQgY3VycmVudHkgYmVpbmcgZWRpdGVkXG4gICAgICAgIGFsbFN0eWxlSXRlbXNPbkNhbnZhczogW10sXG4gICAgICAgIF9vbGRJY29uOiBbXSxcbiAgICAgICAgc3R5bGVFZGl0b3I6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdzdHlsZUVkaXRvcicpLFxuICAgICAgICBmb3JtU3R5bGU6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdzdHlsaW5nRm9ybScpLFxuICAgICAgICBidXR0b25SZW1vdmVFbGVtZW50OiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZGVsZXRlRWxlbWVudENvbmZpcm0nKSxcbiAgICAgICAgYnV0dG9uQ2xvbmVFbGVtZW50OiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnY2xvbmVFbGVtZW50QnV0dG9uJyksXG4gICAgICAgIGJ1dHRvblJlc2V0RWxlbWVudDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3Jlc2V0U3R5bGVCdXR0b24nKSxcbiAgICAgICAgc2VsZWN0TGlua3NJbmVybmFsOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnaW50ZXJuYWxMaW5rc0Ryb3Bkb3duJyksXG4gICAgICAgIHNlbGVjdExpbmtzUGFnZXM6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdwYWdlTGlua3NEcm9wZG93bicpLFxuICAgICAgICB2aWRlb0lucHV0WW91dHViZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3lvdXR1YmVJRCcpLFxuICAgICAgICB2aWRlb0lucHV0VmltZW86IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCd2aW1lb0lEJyksXG4gICAgICAgIGlucHV0Q3VzdG9tTGluazogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2ludGVybmFsTGlua3NDdXN0b20nKSxcbiAgICAgICAgc2VsZWN0SWNvbnM6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdpY29ucycpLFxuICAgICAgICBidXR0b25EZXRhaWxzQXBwbGllZEhpZGU6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdkZXRhaWxzQXBwbGllZE1lc3NhZ2VIaWRlJyksXG4gICAgICAgIGJ1dHRvbkNsb3NlU3R5bGVFZGl0b3I6IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNzdHlsZUVkaXRvciA+IGEuY2xvc2UnKSxcbiAgICAgICAgdWxQYWdlTGlzdDogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3BhZ2VMaXN0JyksXG5cbiAgICAgICAgaW5pdDogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgIC8vZXZlbnRzXG4gICAgICAgICAgICAkKHRoaXMucmFkaW9TdHlsZSkub24oJ2NsaWNrJywgdGhpcy5hY3RpdmF0ZVN0eWxlTW9kZSk7XG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uU2F2ZUNoYW5nZXMpLm9uKCdjbGljaycsIHRoaXMudXBkYXRlU3R5bGluZyk7XG4gICAgICAgICAgICAkKHRoaXMuZm9ybVN0eWxlKS5vbignZm9jdXMnLCAnaW5wdXQnLCB0aGlzLmFuaW1hdGVTdHlsZUlucHV0SW4pLm9uKCdibHVyJywgJ2lucHV0JywgdGhpcy5hbmltYXRlU3R5bGVJbnB1dE91dCk7XG4gICAgICAgICAgICAkKHRoaXMuYnV0dG9uUmVtb3ZlRWxlbWVudCkub24oJ2NsaWNrJywgdGhpcy5kZWxldGVFbGVtZW50KTtcbiAgICAgICAgICAgICQodGhpcy5idXR0b25DbG9uZUVsZW1lbnQpLm9uKCdjbGljaycsIHRoaXMuY2xvbmVFbGVtZW50KTtcbiAgICAgICAgICAgICQodGhpcy5idXR0b25SZXNldEVsZW1lbnQpLm9uKCdjbGljaycsIHRoaXMucmVzZXRFbGVtZW50KTtcbiAgICAgICAgICAgICQodGhpcy5zZWxlY3RMaW5rc0luZXJuYWwpLm9uKCdjaGFuZ2UnLCB0aGlzLnJlc2V0U2VsZWN0TGlua3NJbnRlcm5hbCk7XG4gICAgICAgICAgICAkKHRoaXMuc2VsZWN0TGlua3NQYWdlcykub24oJ2NoYW5nZScsIHRoaXMucmVzZXRTZWxlY3RMaW5rc1BhZ2VzKTtcbiAgICAgICAgICAgICQodGhpcy52aWRlb0lucHV0WW91dHViZSkub24oJ2ZvY3VzJywgZnVuY3Rpb24oKXsgJChzdHlsZWVkaXRvci52aWRlb0lucHV0VmltZW8pLnZhbCgnJyk7IH0pO1xuICAgICAgICAgICAgJCh0aGlzLnZpZGVvSW5wdXRWaW1lbykub24oJ2ZvY3VzJywgZnVuY3Rpb24oKXsgJChzdHlsZWVkaXRvci52aWRlb0lucHV0WW91dHViZSkudmFsKCcnKTsgfSk7XG4gICAgICAgICAgICAkKHRoaXMuaW5wdXRDdXN0b21MaW5rKS5vbignZm9jdXMnLCB0aGlzLnJlc2V0U2VsZWN0QWxsTGlua3MpO1xuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvbkRldGFpbHNBcHBsaWVkSGlkZSkub24oJ2NsaWNrJywgZnVuY3Rpb24oKXskKHRoaXMpLnBhcmVudCgpLmZhZGVPdXQoNTAwKTt9KTtcbiAgICAgICAgICAgICQodGhpcy5idXR0b25DbG9zZVN0eWxlRWRpdG9yKS5vbignY2xpY2snLCB0aGlzLmNsb3NlU3R5bGVFZGl0b3IpO1xuICAgICAgICAgICAgJChkb2N1bWVudCkub24oJ21vZGVDb250ZW50IG1vZGVCbG9ja3MnLCAnYm9keScsIHRoaXMuZGVBY3RpdmF0ZU1vZGUpO1xuXG4gICAgICAgICAgICAvL2Nob3NlbiBmb250LWF3ZXNvbWUgZHJvcGRvd25cbiAgICAgICAgICAgICQodGhpcy5zZWxlY3RJY29ucykuY2hvc2VuKHsnc2VhcmNoX2NvbnRhaW5zJzogdHJ1ZX0pO1xuXG4gICAgICAgICAgICAvL2NoZWNrIGlmIGZvcm1EYXRhIGlzIHN1cHBvcnRlZFxuICAgICAgICAgICAgaWYgKCF3aW5kb3cuRm9ybURhdGEpe1xuICAgICAgICAgICAgICAgIHRoaXMuaGlkZUZpbGVVcGxvYWRzKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vc2hvdyB0aGUgc3R5bGUgbW9kZSByYWRpbyBidXR0b25cbiAgICAgICAgICAgICQodGhpcy5sYWJlbFN0eWxlTW9kZSkuc2hvdygpO1xuXG4gICAgICAgICAgICAvL2xpc3RlbiBmb3IgdGhlIGJlZm9yZVNhdmUgZXZlbnRcbiAgICAgICAgICAgICQoJ2JvZHknKS5vbignYmVmb3JlU2F2ZScsIHRoaXMuY2xvc2VTdHlsZUVkaXRvcik7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBBY3RpdmF0ZXMgc3R5bGUgZWRpdG9yIG1vZGVcbiAgICAgICAgKi9cbiAgICAgICAgYWN0aXZhdGVTdHlsZU1vZGU6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB2YXIgaTtcblxuICAgICAgICAgICAgLy9FbGVtZW50IG9iamVjdCBleHRlbnRpb25cbiAgICAgICAgICAgIGNhbnZhc0VsZW1lbnQucHJvdG90eXBlLmNsaWNrSGFuZGxlciA9IGZ1bmN0aW9uKGVsKSB7XG4gICAgICAgICAgICAgICAgc3R5bGVlZGl0b3Iuc3R5bGVDbGljayhlbCk7XG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAvLyBSZW1vdmUgb3ZlcmxheSBzcGFuIGZyb20gcG9ydGZvbGlvXG4gICAgICAgICAgICBmb3IoaSA9IDE7IGkgPD0gJChcInVsI3BhZ2UxIGxpXCIpLmxlbmd0aDsgaSsrKXtcbiAgICAgICAgICAgICAgICB2YXIgaWQgPSBcIiN1aS1pZC1cIiArIGk7XG4gICAgICAgICAgICAgICAgJChpZCkuY29udGVudHMoKS5maW5kKFwiLm92ZXJsYXlcIikucmVtb3ZlKCk7XG4gICAgICAgICAgICB9XG5cblxuICAgICAgICAgICAgLy90cmlnZ2VyIGN1c3RvbSBldmVudFxuICAgICAgICAgICAgJCgnYm9keScpLnRyaWdnZXIoJ21vZGVEZXRhaWxzJyk7XG5cbiAgICAgICAgICAgIC8vZGlzYWJsZSBmcmFtZUNvdmVyc1xuICAgICAgICAgICAgZm9yKCBpID0gMDsgaSA8IHNpdGVCdWlsZGVyLnNpdGUuc2l0ZVBhZ2VzLmxlbmd0aDsgaSsrICkge1xuICAgICAgICAgICAgICAgIHNpdGVCdWlsZGVyLnNpdGUuc2l0ZVBhZ2VzW2ldLnRvZ2dsZUZyYW1lQ292ZXJzKCdPZmYnKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9jcmVhdGUgYW4gb2JqZWN0IGZvciBldmVyeSBlZGl0YWJsZSBlbGVtZW50IG9uIHRoZSBjYW52YXMgYW5kIHNldHVwIGl0J3MgZXZlbnRzXG5cbiAgICAgICAgICAgIGZvciggaSA9IDA7IGkgPCBzaXRlQnVpbGRlci5zaXRlLnNpdGVQYWdlcy5sZW5ndGg7IGkrKyApIHtcblxuICAgICAgICAgICAgICAgIGZvciggdmFyIHggPSAwOyB4IDwgc2l0ZUJ1aWxkZXIuc2l0ZS5zaXRlUGFnZXNbaV0uYmxvY2tzLmxlbmd0aDsgeCsrICkge1xuXG4gICAgICAgICAgICAgICAgICAgIGZvciggdmFyIGtleSBpbiBiQ29uZmlnLmVkaXRhYmxlSXRlbXMgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQoc2l0ZUJ1aWxkZXIuc2l0ZS5zaXRlUGFnZXNbaV0uYmxvY2tzW3hdLmZyYW1lKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciArICcgJysga2V5ICkuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdmFyIG5ld0VsZW1lbnQgPSBuZXcgY2FudmFzRWxlbWVudCh0aGlzKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG5ld0VsZW1lbnQuYWN0aXZhdGUoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN0eWxlZWRpdG9yLmFsbFN0eWxlSXRlbXNPbkNhbnZhcy5wdXNoKCBuZXdFbGVtZW50ICk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmF0dHIoJ2RhdGEtc2VsZWN0b3InLCBrZXkpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLyokKCcjcGFnZUxpc3QgdWwgbGkgaWZyYW1lJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgZm9yKCB2YXIga2V5IGluIGJDb25maWcuZWRpdGFibGVJdGVtcyApIHtcblxuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmNvbnRlbnRzKCkuZmluZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyICsgJyAnKyBrZXkgKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBuZXdFbGVtZW50ID0gbmV3IGNhbnZhc0VsZW1lbnQodGhpcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIG5ld0VsZW1lbnQuYWN0aXZhdGUoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgc3R5bGVlZGl0b3IuYWxsU3R5bGVJdGVtc09uQ2FudmFzLnB1c2goIG5ld0VsZW1lbnQgKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hdHRyKCdkYXRhLXNlbGVjdG9yJywga2V5KTtcblxuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSk7Ki9cblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIEV2ZW50IGhhbmRsZXIgZm9yIHdoZW4gdGhlIHN0eWxlIGVkaXRvciBpcyBlbnZva2VkIG9uIGFuIGl0ZW1cbiAgICAgICAgKi9cbiAgICAgICAgc3R5bGVDbGljazogZnVuY3Rpb24oZWwpIHtcblxuICAgICAgICAgICAgLy9pZiB3ZSBoYXZlIGFuIGFjdGl2ZSBlbGVtZW50LCBtYWtlIGl0IHVuYWN0aXZlXG4gICAgICAgICAgICBpZiggT2JqZWN0LmtleXModGhpcy5hY3RpdmVFbGVtZW50KS5sZW5ndGggIT09IDApIHtcbiAgICAgICAgICAgICAgICB0aGlzLmFjdGl2ZUVsZW1lbnQuYWN0aXZhdGUoKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9zZXQgdGhlIGFjdGl2ZSBlbGVtZW50XG4gICAgICAgICAgICB2YXIgYWN0aXZlRWxlbWVudCA9IG5ldyBjYW52YXNFbGVtZW50KGVsKTtcbiAgICAgICAgICAgIGFjdGl2ZUVsZW1lbnQuc2V0UGFyZW50QmxvY2soKTtcbiAgICAgICAgICAgIHRoaXMuYWN0aXZlRWxlbWVudCA9IGFjdGl2ZUVsZW1lbnQ7XG5cbiAgICAgICAgICAgIC8vdW5iaW5kIGhvdmVyIGFuZCBjbGljayBldmVudHMgYW5kIG1ha2UgdGhpcyBpdGVtIGFjdGl2ZVxuICAgICAgICAgICAgdGhpcy5hY3RpdmVFbGVtZW50LnNldE9wZW4oKTtcblxuICAgICAgICAgICAgdmFyIHRoZVNlbGVjdG9yID0gJCh0aGlzLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignZGF0YS1zZWxlY3RvcicpO1xuXG4gICAgICAgICAgICAkKCcjZWRpdGluZ0VsZW1lbnQnKS50ZXh0KCB0aGVTZWxlY3RvciApO1xuXG4gICAgICAgICAgICAvL2FjdGl2YXRlIGZpcnN0IHRhYlxuICAgICAgICAgICAgJCgnI2RldGFpbFRhYnMgYTpmaXJzdCcpLmNsaWNrKCk7XG5cbiAgICAgICAgICAgIC8vaGlkZSBhbGwgYnkgZGVmYXVsdFxuICAgICAgICAgICAgJCgndWwjZGV0YWlsVGFicyBsaTpndCgwKScpLmhpZGUoKTtcblxuICAgICAgICAgICAgLy93aGF0IGFyZSB3ZSBkZWFsaW5nIHdpdGg/XG4gICAgICAgICAgICBpZiggJCh0aGlzLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucHJvcCgndGFnTmFtZScpID09PSAnQScgfHwgJCh0aGlzLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucGFyZW50KCkucHJvcCgndGFnTmFtZScpID09PSAnQScgKSB7XG5cbiAgICAgICAgICAgICAgICB0aGlzLmVkaXRMaW5rKHRoaXMuYWN0aXZlRWxlbWVudC5lbGVtZW50KTtcblxuICAgICAgICAgICAgfVxuXG5cdFx0XHRpZiggJCh0aGlzLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucHJvcCgndGFnTmFtZScpID09PSAnSU1HJyApe1xuXG4gICAgICAgICAgICAgICAgdGhpcy5lZGl0SW1hZ2UodGhpcy5hY3RpdmVFbGVtZW50LmVsZW1lbnQpO1xuXG4gICAgICAgICAgICB9XG5cblx0XHRcdGlmKCAkKHRoaXMuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdkYXRhLXR5cGUnKSA9PT0gJ3ZpZGVvJyApIHtcblxuICAgICAgICAgICAgICAgIHRoaXMuZWRpdFZpZGVvKHRoaXMuYWN0aXZlRWxlbWVudC5lbGVtZW50KTtcblxuICAgICAgICAgICAgfVxuXG5cdFx0XHRpZiggJCh0aGlzLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuaGFzQ2xhc3MoJ2ZhJykgKSB7XG5cbiAgICAgICAgICAgICAgICB0aGlzLmVkaXRJY29uKHRoaXMuYWN0aXZlRWxlbWVudC5lbGVtZW50KTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL2xvYWQgdGhlIGF0dHJpYnV0ZXNcbiAgICAgICAgICAgIHRoaXMuYnVpbGRlU3R5bGVFbGVtZW50cyh0aGVTZWxlY3Rvcik7XG5cbiAgICAgICAgICAgIC8vb3BlbiBzaWRlIHBhbmVsXG4gICAgICAgICAgICB0aGlzLnRvZ2dsZVNpZGVQYW5lbCgnb3BlbicpO1xuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGR5bmFtaWNhbGx5IGdlbmVyYXRlcyB0aGUgZm9ybSBmaWVsZHMgZm9yIGVkaXRpbmcgYW4gZWxlbWVudHMgc3R5bGUgYXR0cmlidXRlc1xuICAgICAgICAqL1xuICAgICAgICBidWlsZGVTdHlsZUVsZW1lbnRzOiBmdW5jdGlvbih0aGVTZWxlY3Rvcikge1xuXG4gICAgICAgICAgICAvL2RlbGV0ZSB0aGUgb2xkIG9uZXMgZmlyc3RcbiAgICAgICAgICAgICQoJyNzdHlsZUVsZW1lbnRzID4gKjpub3QoI3N0eWxlRWxUZW1wbGF0ZSknKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgZm9yKCB2YXIgeD0wOyB4PGJDb25maWcuZWRpdGFibGVJdGVtc1t0aGVTZWxlY3Rvcl0ubGVuZ3RoOyB4KysgKSB7XG5cbiAgICAgICAgICAgICAgICAvL2NyZWF0ZSBzdHlsZSBlbGVtZW50c1xuICAgICAgICAgICAgICAgIHZhciBuZXdTdHlsZUVsID0gJCgnI3N0eWxlRWxUZW1wbGF0ZScpLmNsb25lKCk7XG4gICAgICAgICAgICAgICAgbmV3U3R5bGVFbC5hdHRyKCdpZCcsICcnKTtcbiAgICAgICAgICAgICAgICBuZXdTdHlsZUVsLmZpbmQoJy5jb250cm9sLWxhYmVsJykudGV4dCggYkNvbmZpZy5lZGl0YWJsZUl0ZW1zW3RoZVNlbGVjdG9yXVt4XStcIjpcIiApO1xuXG4gICAgICAgICAgICAgICAgaWYoIHRoZVNlbGVjdG9yICsgXCIgOiBcIiArIGJDb25maWcuZWRpdGFibGVJdGVtc1t0aGVTZWxlY3Rvcl1beF0gaW4gYkNvbmZpZy5lZGl0YWJsZUl0ZW1PcHRpb25zKSB7Ly93ZSd2ZSBnb3QgYSBkcm9wZG93biBpbnN0ZWFkIG9mIG9wZW4gdGV4dCBpbnB1dFxuXG4gICAgICAgICAgICAgICAgICAgIG5ld1N0eWxlRWwuZmluZCgnaW5wdXQnKS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgbmV3RHJvcERvd24gPSAkKCc8c2VsZWN0IGNsYXNzPVwiZm9ybS1jb250cm9sIHNlbGVjdCBzZWxlY3QtcHJpbWFyeSBidG4tYmxvY2sgc2VsZWN0LXNtXCI+PC9zZWxlY3Q+Jyk7XG4gICAgICAgICAgICAgICAgICAgIG5ld0Ryb3BEb3duLmF0dHIoJ25hbWUnLCBiQ29uZmlnLmVkaXRhYmxlSXRlbXNbdGhlU2VsZWN0b3JdW3hdKTtcblxuXG4gICAgICAgICAgICAgICAgICAgIGZvciggdmFyIHo9MDsgejxiQ29uZmlnLmVkaXRhYmxlSXRlbU9wdGlvbnNbIHRoZVNlbGVjdG9yK1wiIDogXCIrYkNvbmZpZy5lZGl0YWJsZUl0ZW1zW3RoZVNlbGVjdG9yXVt4XSBdLmxlbmd0aDsgeisrICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgbmV3T3B0aW9uID0gJCgnPG9wdGlvbiB2YWx1ZT1cIicrYkNvbmZpZy5lZGl0YWJsZUl0ZW1PcHRpb25zW3RoZVNlbGVjdG9yK1wiIDogXCIrYkNvbmZpZy5lZGl0YWJsZUl0ZW1zW3RoZVNlbGVjdG9yXVt4XV1bel0rJ1wiPicrYkNvbmZpZy5lZGl0YWJsZUl0ZW1PcHRpb25zW3RoZVNlbGVjdG9yK1wiIDogXCIrYkNvbmZpZy5lZGl0YWJsZUl0ZW1zW3RoZVNlbGVjdG9yXVt4XV1bel0rJzwvb3B0aW9uPicpO1xuXG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmKCBiQ29uZmlnLmVkaXRhYmxlSXRlbU9wdGlvbnNbdGhlU2VsZWN0b3IrXCIgOiBcIitiQ29uZmlnLmVkaXRhYmxlSXRlbXNbdGhlU2VsZWN0b3JdW3hdXVt6XSA9PT0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmNzcyggYkNvbmZpZy5lZGl0YWJsZUl0ZW1zW3RoZVNlbGVjdG9yXVt4XSApICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vY3VycmVudCB2YWx1ZSwgbWFya2VkIGFzIHNlbGVjdGVkXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbmV3T3B0aW9uLmF0dHIoJ3NlbGVjdGVkJywgJ3RydWUnKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBuZXdEcm9wRG93bi5hcHBlbmQoIG5ld09wdGlvbiApO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICBuZXdTdHlsZUVsLmFwcGVuZCggbmV3RHJvcERvd24gKTtcbiAgICAgICAgICAgICAgICAgICAgbmV3RHJvcERvd24uc2VsZWN0MigpO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICAgICBuZXdTdHlsZUVsLmZpbmQoJ2lucHV0JykudmFsKCAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuY3NzKCBiQ29uZmlnLmVkaXRhYmxlSXRlbXNbdGhlU2VsZWN0b3JdW3hdICkgKS5hdHRyKCduYW1lJywgYkNvbmZpZy5lZGl0YWJsZUl0ZW1zW3RoZVNlbGVjdG9yXVt4XSk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoIGJDb25maWcuZWRpdGFibGVJdGVtc1t0aGVTZWxlY3Rvcl1beF0gPT09ICdiYWNrZ3JvdW5kLWltYWdlJyApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgbmV3U3R5bGVFbC5maW5kKCdpbnB1dCcpLmJpbmQoJ2ZvY3VzJywgZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHZhciB0aGVJbnB1dCA9ICQodGhpcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKCcjaW1hZ2VNb2RhbCcpLm1vZGFsKCdzaG93Jyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJCgnI2ltYWdlTW9kYWwgLmltYWdlIGJ1dHRvbi51c2VJbWFnZScpLnVuYmluZCgnY2xpY2snKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKCcjaW1hZ2VNb2RhbCcpLm9uKCdjbGljaycsICcuaW1hZ2UgYnV0dG9uLnVzZUltYWdlJywgZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuY3NzKCdiYWNrZ3JvdW5kLWltYWdlJywgICd1cmwoXCInKyQodGhpcykuYXR0cignZGF0YS11cmwnKSsnXCIpJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy91cGRhdGUgbGl2ZSBpbWFnZVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aGVJbnB1dC52YWwoICd1cmwoXCInKyQodGhpcykuYXR0cignZGF0YS11cmwnKSsnXCIpJyApO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vaGlkZSBtb2RhbFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKCcjaW1hZ2VNb2RhbCcpLm1vZGFsKCdoaWRlJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgLy93ZSd2ZSBnb3QgcGVuZGluZyBjaGFuZ2VzXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNpdGVCdWlsZGVyLnNpdGUuc2V0UGVuZGluZ0NoYW5nZXModHJ1ZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmKCBiQ29uZmlnLmVkaXRhYmxlSXRlbXNbdGhlU2VsZWN0b3JdW3hdLmluZGV4T2YoXCJjb2xvclwiKSA+IC0xICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiggJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmNzcyggYkNvbmZpZy5lZGl0YWJsZUl0ZW1zW3RoZVNlbGVjdG9yXVt4XSApICE9PSAndHJhbnNwYXJlbnQnICYmICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5jc3MoIGJDb25maWcuZWRpdGFibGVJdGVtc1t0aGVTZWxlY3Rvcl1beF0gKSAhPT0gJ25vbmUnICYmICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5jc3MoIGJDb25maWcuZWRpdGFibGVJdGVtc1t0aGVTZWxlY3Rvcl1beF0gKSAhPT0gJycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBuZXdTdHlsZUVsLnZhbCggJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmNzcyggYkNvbmZpZy5lZGl0YWJsZUl0ZW1zW3RoZVNlbGVjdG9yXVt4XSApICk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgbmV3U3R5bGVFbC5maW5kKCdpbnB1dCcpLnNwZWN0cnVtKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBwcmVmZXJyZWRGb3JtYXQ6IFwiaGV4XCIsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2hvd1BhbGV0dGU6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYWxsb3dFbXB0eTogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzaG93SW5wdXQ6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcGFsZXR0ZTogW1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBbXCIjMDAwXCIsXCIjNDQ0XCIsXCIjNjY2XCIsXCIjOTk5XCIsXCIjY2NjXCIsXCIjZWVlXCIsXCIjZjNmM2YzXCIsXCIjZmZmXCJdLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBbXCIjZjAwXCIsXCIjZjkwXCIsXCIjZmYwXCIsXCIjMGYwXCIsXCIjMGZmXCIsXCIjMDBmXCIsXCIjOTBmXCIsXCIjZjBmXCJdLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBbXCIjZjRjY2NjXCIsXCIjZmNlNWNkXCIsXCIjZmZmMmNjXCIsXCIjZDllYWQzXCIsXCIjZDBlMGUzXCIsXCIjY2ZlMmYzXCIsXCIjZDlkMmU5XCIsXCIjZWFkMWRjXCJdLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBbXCIjZWE5OTk5XCIsXCIjZjljYjljXCIsXCIjZmZlNTk5XCIsXCIjYjZkN2E4XCIsXCIjYTJjNGM5XCIsXCIjOWZjNWU4XCIsXCIjYjRhN2Q2XCIsXCIjZDVhNmJkXCJdLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBbXCIjZTA2NjY2XCIsXCIjZjZiMjZiXCIsXCIjZmZkOTY2XCIsXCIjOTNjNDdkXCIsXCIjNzZhNWFmXCIsXCIjNmZhOGRjXCIsXCIjOGU3Y2MzXCIsXCIjYzI3YmEwXCJdLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBbXCIjYzAwXCIsXCIjZTY5MTM4XCIsXCIjZjFjMjMyXCIsXCIjNmFhODRmXCIsXCIjNDU4MThlXCIsXCIjM2Q4NWM2XCIsXCIjNjc0ZWE3XCIsXCIjYTY0ZDc5XCJdLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBbXCIjOTAwXCIsXCIjYjQ1ZjA2XCIsXCIjYmY5MDAwXCIsXCIjMzg3NjFkXCIsXCIjMTM0ZjVjXCIsXCIjMGI1Mzk0XCIsXCIjMzUxYzc1XCIsXCIjNzQxYjQ3XCJdLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBbXCIjNjAwXCIsXCIjNzgzZjA0XCIsXCIjN2Y2MDAwXCIsXCIjMjc0ZTEzXCIsXCIjMGMzNDNkXCIsXCIjMDczNzYzXCIsXCIjMjAxMjRkXCIsXCIjNGMxMTMwXCJdXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgXVxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgbmV3U3R5bGVFbC5jc3MoJ2Rpc3BsYXknLCAnYmxvY2snKTtcblxuICAgICAgICAgICAgICAgICQoJyNzdHlsZUVsZW1lbnRzJykuYXBwZW5kKCBuZXdTdHlsZUVsICk7XG5cbiAgICAgICAgICAgICAgICAkKCcjc3R5bGVFZGl0b3IgZm9ybSNzdHlsaW5nRm9ybScpLmhlaWdodCgnYXV0bycpO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBBcHBsaWVzIHVwZGF0ZWQgc3R5bGluZyB0byB0aGUgY2FudmFzXG4gICAgICAgICovXG4gICAgICAgIHVwZGF0ZVN0eWxpbmc6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICB2YXIgZWxlbWVudElEO1xuXG4gICAgICAgICAgICAkKCcjc3R5bGVFZGl0b3IgI3RhYjEgLmZvcm0tZ3JvdXA6bm90KCNzdHlsZUVsVGVtcGxhdGUpIGlucHV0LCAjc3R5bGVFZGl0b3IgI3RhYjEgLmZvcm0tZ3JvdXA6bm90KCNzdHlsZUVsVGVtcGxhdGUpIHNlbGVjdCcpLmVhY2goZnVuY3Rpb24oKXtcblxuXHRcdFx0XHRpZiggJCh0aGlzKS5hdHRyKCduYW1lJykgIT09IHVuZGVmaW5lZCApIHtcblxuICAgICAgICAgICAgICAgIFx0JChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmNzcyggJCh0aGlzKS5hdHRyKCduYW1lJyksICAkKHRoaXMpLnZhbCgpKTtcblxuXHRcdFx0XHR9XG5cbiAgICAgICAgICAgICAgICAvKiBTQU5EQk9YICovXG5cbiAgICAgICAgICAgICAgICBpZiggc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94ICkge1xuXG4gICAgICAgICAgICAgICAgICAgIGVsZW1lbnRJRCA9ICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdpZCcpO1xuXG4gICAgICAgICAgICAgICAgICAgICQoJyMnK3N0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCkuY29udGVudHMoKS5maW5kKCcjJytlbGVtZW50SUQpLmNzcyggJCh0aGlzKS5hdHRyKCduYW1lJyksICAkKHRoaXMpLnZhbCgpICk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvKiBFTkQgU0FOREJPWCAqL1xuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy9saW5rc1xuICAgICAgICAgICAgaWYoICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5wcm9wKCd0YWdOYW1lJykgPT09ICdBJyApIHtcblxuICAgICAgICAgICAgICAgIC8vY2hhbmdlIHRoZSBocmVmIHByb3A/XG4gICAgICAgICAgICAgICAgaWYoICQoJ3NlbGVjdCNpbnRlcm5hbExpbmtzRHJvcGRvd24nKS52YWwoKSAhPT0gJyMnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdocmVmJywgJCgnc2VsZWN0I2ludGVybmFsTGlua3NEcm9wZG93bicpLnZhbCgpKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggJCgnc2VsZWN0I3BhZ2VMaW5rc0Ryb3Bkb3duJykudmFsKCkgIT09ICcjJyApIHtcblxuICAgICAgICAgICAgICAgICAgICAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignaHJlZicsICQoJ3NlbGVjdCNwYWdlTGlua3NEcm9wZG93bicpLnZhbCgpICk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoICQoJ2lucHV0I2ludGVybmFsTGlua3NDdXN0b20nKS52YWwoKSAhPT0gJycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmF0dHIoJ2hyZWYnLCAkKCdpbnB1dCNpbnRlcm5hbExpbmtzQ3VzdG9tJykudmFsKCkpO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLyogU0FOREJPWCAqL1xuXG4gICAgICAgICAgICAgICAgaWYoIHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCApIHtcblxuICAgICAgICAgICAgICAgICAgICBlbGVtZW50SUQgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignaWQnKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiggJCgnc2VsZWN0I2ludGVybmFsTGlua3NEcm9wZG93bicpLnZhbCgpICE9PSAnIycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyMnK3N0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCkuY29udGVudHMoKS5maW5kKCcjJytlbGVtZW50SUQpLmF0dHIoJ2hyZWYnLCAkKCdzZWxlY3QjaW50ZXJuYWxMaW5rc0Ryb3Bkb3duJykudmFsKCkpO1xuXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggJCgnc2VsZWN0I3BhZ2VMaW5rc0Ryb3Bkb3duJykudmFsKCkgIT09ICcjJyApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnIycrc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94KS5jb250ZW50cygpLmZpbmQoJyMnK2VsZW1lbnRJRCkuYXR0cignaHJlZicsICQoJ3NlbGVjdCNwYWdlTGlua3NEcm9wZG93bicpLnZhbCgpICk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmKCAkKCdpbnB1dCNpbnRlcm5hbExpbmtzQ3VzdG9tJykudmFsKCkgIT09ICcnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjJytzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LnNhbmRib3gpLmNvbnRlbnRzKCkuZmluZCgnIycrZWxlbWVudElEKS5hdHRyKCdocmVmJywgJCgnaW5wdXQjaW50ZXJuYWxMaW5rc0N1c3RvbScpLnZhbCgpKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvKiBFTkQgU0FOREJPWCAqL1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmKCAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucGFyZW50KCkucHJvcCgndGFnTmFtZScpID09PSAnQScgKSB7XG5cbiAgICAgICAgICAgICAgICAvL2NoYW5nZSB0aGUgaHJlZiBwcm9wP1xuXHRcdFx0XHRpZiggJCgnc2VsZWN0I2ludGVybmFsTGlua3NEcm9wZG93bicpLnZhbCgpICE9PSAnIycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnBhcmVudCgpLmF0dHIoJ2hyZWYnLCAkKCdzZWxlY3QjaW50ZXJuYWxMaW5rc0Ryb3Bkb3duJykudmFsKCkpO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmKCAkKCdzZWxlY3QjcGFnZUxpbmtzRHJvcGRvd24nKS52YWwoKSAhPT0gJyMnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5wYXJlbnQoKS5hdHRyKCdocmVmJywgJCgnc2VsZWN0I3BhZ2VMaW5rc0Ryb3Bkb3duJykudmFsKCkgKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggJCgnaW5wdXQjaW50ZXJuYWxMaW5rc0N1c3RvbScpLnZhbCgpICE9PSAnJyApIHtcblxuICAgICAgICAgICAgICAgICAgICAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucGFyZW50KCkuYXR0cignaHJlZicsICQoJ2lucHV0I2ludGVybmFsTGlua3NDdXN0b20nKS52YWwoKSk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvKiBTQU5EQk9YICovXG5cbiAgICAgICAgICAgICAgICBpZiggc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94ICkge1xuXG4gICAgICAgICAgICAgICAgICAgIGVsZW1lbnRJRCA9ICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdpZCcpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCAkKCdzZWxlY3QjaW50ZXJuYWxMaW5rc0Ryb3Bkb3duJykudmFsKCkgIT09ICcjJyApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnIycrc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94KS5jb250ZW50cygpLmZpbmQoJyMnK2VsZW1lbnRJRCkucGFyZW50KCkuYXR0cignaHJlZicsICQoJ3NlbGVjdCNpbnRlcm5hbExpbmtzRHJvcGRvd24nKS52YWwoKSk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmKCAkKCdzZWxlY3QjcGFnZUxpbmtzRHJvcGRvd24nKS52YWwoKSAhPT0gJyMnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjJytzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LnNhbmRib3gpLmNvbnRlbnRzKCkuZmluZCgnIycrZWxlbWVudElEKS5wYXJlbnQoKS5hdHRyKCdocmVmJywgJCgnc2VsZWN0I3BhZ2VMaW5rc0Ryb3Bkb3duJykudmFsKCkgKTtcblxuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYoICQoJ2lucHV0I2ludGVybmFsTGlua3NDdXN0b20nKS52YWwoKSAhPT0gJycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyMnK3N0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCkuY29udGVudHMoKS5maW5kKCcjJytlbGVtZW50SUQpLnBhcmVudCgpLmF0dHIoJ2hyZWYnLCAkKCdpbnB1dCNpbnRlcm5hbExpbmtzQ3VzdG9tJykudmFsKCkpO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8qIEVORCBTQU5EQk9YICovXG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLy9pY29uc1xuICAgICAgICAgICAgaWYoICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5oYXNDbGFzcygnZmEnKSApIHtcblxuICAgICAgICAgICAgICAgIC8vb3V0IHdpdGggdGhlIG9sZCwgaW4gd2l0aCB0aGUgbmV3IDopXG4gICAgICAgICAgICAgICAgLy9nZXQgaWNvbiBjbGFzcyBuYW1lLCBzdGFydGluZyB3aXRoIGZhLVxuICAgICAgICAgICAgICAgIHZhciBnZXQgPSAkLmdyZXAoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50LmNsYXNzTmFtZS5zcGxpdChcIiBcIiksIGZ1bmN0aW9uKHYsIGkpe1xuXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiB2LmluZGV4T2YoJ2ZhLScpID09PSAwO1xuXG4gICAgICAgICAgICAgICAgfSkuam9pbigpO1xuXG4gICAgICAgICAgICAgICAgLy9pZiB0aGUgaWNvbnMgaXMgYmVpbmcgY2hhbmdlZCwgc2F2ZSB0aGUgb2xkIG9uZSBzbyB3ZSBjYW4gcmVzZXQgaXQgaWYgbmVlZGVkXG5cbiAgICAgICAgICAgICAgICBpZiggZ2V0ICE9PSAkKCdzZWxlY3QjaWNvbnMnKS52YWwoKSApIHtcblxuICAgICAgICAgICAgICAgICAgICAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkudW5pcXVlSWQoKTtcbiAgICAgICAgICAgICAgICAgICAgc3R5bGVlZGl0b3IuX29sZEljb25bJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmF0dHIoJ2lkJyldID0gZ2V0O1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnJlbW92ZUNsYXNzKCBnZXQgKS5hZGRDbGFzcyggJCgnc2VsZWN0I2ljb25zJykudmFsKCkgKTtcblxuXG4gICAgICAgICAgICAgICAgLyogU0FOREJPWCAqL1xuXG4gICAgICAgICAgICAgICAgaWYoIHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCApIHtcblxuICAgICAgICAgICAgICAgICAgICBlbGVtZW50SUQgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignaWQnKTtcbiAgICAgICAgICAgICAgICAgICAgJCgnIycrc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94KS5jb250ZW50cygpLmZpbmQoJyMnK2VsZW1lbnRJRCkucmVtb3ZlQ2xhc3MoIGdldCApLmFkZENsYXNzKCAkKCdzZWxlY3QjaWNvbnMnKS52YWwoKSApO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgLyogRU5EIFNBTkRCT1ggKi9cblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL3ZpZGVvIFVSTFxuICAgICAgICAgICAgaWYoICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdkYXRhLXR5cGUnKSA9PT0gJ3ZpZGVvJyApIHtcblxuICAgICAgICAgICAgICAgIGlmKCAkKCdpbnB1dCN5b3V0dWJlSUQnKS52YWwoKSAhPT0gJycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnByZXYoKS5hdHRyKCdzcmMnLCBcIi8vd3d3LnlvdXR1YmUuY29tL2VtYmVkL1wiKyQoJyN2aWRlb19UYWIgaW5wdXQjeW91dHViZUlEJykudmFsKCkpO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIGlmKCAkKCdpbnB1dCN2aW1lb0lEJykudmFsKCkgIT09ICcnICkge1xuXG4gICAgICAgICAgICAgICAgICAgICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5wcmV2KCkuYXR0cignc3JjJywgXCIvL3BsYXllci52aW1lby5jb20vdmlkZW8vXCIrJCgnI3ZpZGVvX1RhYiBpbnB1dCN2aW1lb0lEJykudmFsKCkrXCI/dGl0bGU9MCZhbXA7YnlsaW5lPTAmYW1wO3BvcnRyYWl0PTBcIik7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAvKiBTQU5EQk9YICovXG5cbiAgICAgICAgICAgICAgICBpZiggc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94ICkge1xuXG4gICAgICAgICAgICAgICAgICAgIGVsZW1lbnRJRCA9ICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdpZCcpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCAkKCdpbnB1dCN5b3V0dWJlSUQnKS52YWwoKSAhPT0gJycgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICQoJyMnK3N0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuc2FuZGJveCkuY29udGVudHMoKS5maW5kKCcjJytlbGVtZW50SUQpLnByZXYoKS5hdHRyKCdzcmMnLCBcIi8vd3d3LnlvdXR1YmUuY29tL2VtYmVkL1wiKyQoJyN2aWRlb19UYWIgaW5wdXQjeW91dHViZUlEJykudmFsKCkpO1xuXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggJCgnaW5wdXQjdmltZW9JRCcpLnZhbCgpICE9PSAnJyApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnIycrc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94KS5jb250ZW50cygpLmZpbmQoJyMnK2VsZW1lbnRJRCkucHJldigpLmF0dHIoJ3NyYycsIFwiLy9wbGF5ZXIudmltZW8uY29tL3ZpZGVvL1wiKyQoJyN2aWRlb19UYWIgaW5wdXQjdmltZW9JRCcpLnZhbCgpK1wiP3RpdGxlPTAmYW1wO2J5bGluZT0wJmFtcDtwb3J0cmFpdD0wXCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIC8qIEVORCBTQU5EQk9YICovXG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgJCgnI2RldGFpbHNBcHBsaWVkTWVzc2FnZScpLmZhZGVJbig2MDAsIGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7ICQoJyNkZXRhaWxzQXBwbGllZE1lc3NhZ2UnKS5mYWRlT3V0KDEwMDApOyB9LCAzMDAwKTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIC8vYWRqdXN0IGZyYW1lIGhlaWdodFxuICAgICAgICAgICAgc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5wYXJlbnRCbG9jay5oZWlnaHRBZGp1c3RtZW50KCk7XG5cblxuICAgICAgICAgICAgLy93ZSd2ZSBnb3QgcGVuZGluZyBjaGFuZ2VzXG4gICAgICAgICAgICBzaXRlQnVpbGRlci5zaXRlLnNldFBlbmRpbmdDaGFuZ2VzKHRydWUpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgb24gZm9jdXMsIHdlJ2xsIG1ha2UgdGhlIGlucHV0IGZpZWxkcyB3aWRlclxuICAgICAgICAqL1xuICAgICAgICBhbmltYXRlU3R5bGVJbnB1dEluOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCh0aGlzKS5jc3MoJ3Bvc2l0aW9uJywgJ2Fic29sdXRlJyk7XG4gICAgICAgICAgICAkKHRoaXMpLmNzcygncmlnaHQnLCAnMHB4Jyk7XG4gICAgICAgICAgICAkKHRoaXMpLmFuaW1hdGUoeyd3aWR0aCc6ICcxMDAlJ30sIDUwMCk7XG4gICAgICAgICAgICAkKHRoaXMpLmZvY3VzKGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgdGhpcy5zZWxlY3QoKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgb24gYmx1ciwgd2UnbGwgcmV2ZXJ0IHRoZSBpbnB1dCBmaWVsZHMgdG8gdGhlaXIgb3JpZ2luYWwgc2l6ZVxuICAgICAgICAqL1xuICAgICAgICBhbmltYXRlU3R5bGVJbnB1dE91dDogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICQodGhpcykuYW5pbWF0ZSh7J3dpZHRoJzogJzQyJSd9LCA1MDAsIGZ1bmN0aW9uKCl7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5jc3MoJ3Bvc2l0aW9uJywgJ3JlbGF0aXZlJyk7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5jc3MoJ3JpZ2h0JywgJ2F1dG8nKTtcbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgd2hlbiB0aGUgY2xpY2tlZCBlbGVtZW50IGlzIGFuIGFuY2hvciB0YWcgKG9yIGhhcyBhIHBhcmVudCBhbmNob3IgdGFnKVxuICAgICAgICAqL1xuICAgICAgICBlZGl0TGluazogZnVuY3Rpb24oZWwpIHtcblxuICAgICAgICAgICAgJCgnYSNsaW5rX0xpbmsnKS5wYXJlbnQoKS5zaG93KCk7XG5cbiAgICAgICAgICAgIHZhciB0aGVIcmVmO1xuXG4gICAgICAgICAgICBpZiggJChlbCkucHJvcCgndGFnTmFtZScpID09PSAnQScgKSB7XG5cbiAgICAgICAgICAgICAgICB0aGVIcmVmID0gJChlbCkuYXR0cignaHJlZicpO1xuXG4gICAgICAgICAgICB9IGVsc2UgaWYoICQoZWwpLnBhcmVudCgpLnByb3AoJ3RhZ05hbWUnKSA9PT0gJ0EnICkge1xuXG4gICAgICAgICAgICAgICAgdGhlSHJlZiA9ICQoZWwpLnBhcmVudCgpLmF0dHIoJ2hyZWYnKTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB2YXIgekluZGV4ID0gMDtcblxuICAgICAgICAgICAgdmFyIHBhZ2VMaW5rID0gZmFsc2U7XG5cbiAgICAgICAgICAgIC8vdGhlIGFjdHVhbCBzZWxlY3RcblxuICAgICAgICAgICAgJCgnc2VsZWN0I2ludGVybmFsTGlua3NEcm9wZG93bicpLnByb3AoJ3NlbGVjdGVkSW5kZXgnLCAwKTtcblxuICAgICAgICAgICAgLy9zZXQgdGhlIGNvcnJlY3QgaXRlbSB0byBcInNlbGVjdGVkXCJcbiAgICAgICAgICAgICQoJ3NlbGVjdCNpbnRlcm5hbExpbmtzRHJvcGRvd24gb3B0aW9uJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgaWYoICQodGhpcykuYXR0cigndmFsdWUnKSA9PT0gdGhlSHJlZiApIHtcblxuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmF0dHIoJ3NlbGVjdGVkJywgdHJ1ZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgekluZGV4ID0gJCh0aGlzKS5pbmRleCgpO1xuXG4gICAgICAgICAgICAgICAgICAgIHBhZ2VMaW5rID0gdHJ1ZTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSk7XG5cblxuICAgICAgICAgICAgLy90aGUgcHJldHR5IGRyb3Bkb3duXG4gICAgICAgICAgICAkKCcubGlua19UYWIgLmJ0bi1ncm91cC5zZWxlY3QgLmRyb3Bkb3duLW1lbnUgbGknKS5yZW1vdmVDbGFzcygnc2VsZWN0ZWQnKTtcbiAgICAgICAgICAgICQoJy5saW5rX1RhYiAuYnRuLWdyb3VwLnNlbGVjdCAuZHJvcGRvd24tbWVudSBsaTplcSgnK3pJbmRleCsnKScpLmFkZENsYXNzKCdzZWxlY3RlZCcpO1xuICAgICAgICAgICAgJCgnLmxpbmtfVGFiIC5idG4tZ3JvdXAuc2VsZWN0OmVxKDApIC5maWx0ZXItb3B0aW9uJykudGV4dCggJCgnc2VsZWN0I2ludGVybmFsTGlua3NEcm9wZG93biBvcHRpb246c2VsZWN0ZWQnKS50ZXh0KCkgKTtcbiAgICAgICAgICAgICQoJy5saW5rX1RhYiAuYnRuLWdyb3VwLnNlbGVjdDplcSgxKSAuZmlsdGVyLW9wdGlvbicpLnRleHQoICQoJ3NlbGVjdCNwYWdlTGlua3NEcm9wZG93biBvcHRpb246c2VsZWN0ZWQnKS50ZXh0KCkgKTtcblxuICAgICAgICAgICAgaWYoIHBhZ2VMaW5rID09PSB0cnVlICkge1xuXG4gICAgICAgICAgICAgICAgJCgnaW5wdXQjaW50ZXJuYWxMaW5rc0N1c3RvbScpLnZhbCgnJyk7XG5cbiAgICAgICAgICAgIH0gZWxzZSB7XG5cbiAgICAgICAgICAgICAgICBpZiggJChlbCkucHJvcCgndGFnTmFtZScpID09PSAnQScgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYoICQoZWwpLmF0dHIoJ2hyZWYnKVswXSAhPT0gJyMnICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnaW5wdXQjaW50ZXJuYWxMaW5rc0N1c3RvbScpLnZhbCggJChlbCkuYXR0cignaHJlZicpICk7XG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCdpbnB1dCNpbnRlcm5hbExpbmtzQ3VzdG9tJykudmFsKCAnJyApO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYoICQoZWwpLnBhcmVudCgpLnByb3AoJ3RhZ05hbWUnKSA9PT0gJ0EnICkge1xuXG4gICAgICAgICAgICAgICAgICAgIGlmKCAkKGVsKS5wYXJlbnQoKS5hdHRyKCdocmVmJylbMF0gIT09ICcjJyApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoJ2lucHV0I2ludGVybmFsTGlua3NDdXN0b20nKS52YWwoICQoZWwpLnBhcmVudCgpLmF0dHIoJ2hyZWYnKSApO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgJCgnaW5wdXQjaW50ZXJuYWxMaW5rc0N1c3RvbScpLnZhbCggJycgKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vbGlzdCBhdmFpbGFibGUgYmxvY2tzIG9uIHRoaXMgcGFnZSwgcmVtb3ZlIG9sZCBvbmVzIGZpcnN0XG5cbiAgICAgICAgICAgICQoJ3NlbGVjdCNwYWdlTGlua3NEcm9wZG93biBvcHRpb246bm90KDpmaXJzdCknKS5yZW1vdmUoKTtcbiAgICAgICAgICAgICQoJyNwYWdlTGlzdCB1bDp2aXNpYmxlIGlmcmFtZScpLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgIGlmKCAkKHRoaXMpLmNvbnRlbnRzKCkuZmluZCggYkNvbmZpZy5wYWdlQ29udGFpbmVyICsgXCIgPiAqOmZpcnN0XCIgKS5hdHRyKCdpZCcpICE9PSB1bmRlZmluZWQgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyIG5ld09wdGlvbjtcblxuICAgICAgICAgICAgICAgICAgICBpZiggJChlbCkuYXR0cignaHJlZicpID09PSAnIycrJCh0aGlzKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciArIFwiID4gKjpmaXJzdFwiICkuYXR0cignaWQnKSApIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgbmV3T3B0aW9uID0gJzxvcHRpb24gc2VsZWN0ZWQgdmFsdWU9IycrJCh0aGlzKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciArIFwiID4gKjpmaXJzdFwiICkuYXR0cignaWQnKSsnPiMnKyQodGhpcykuY29udGVudHMoKS5maW5kKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKyBcIiA+ICo6Zmlyc3RcIiApLmF0dHIoJ2lkJykrJzwvb3B0aW9uPic7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgbmV3T3B0aW9uID0gJzxvcHRpb24gdmFsdWU9IycrJCh0aGlzKS5jb250ZW50cygpLmZpbmQoIGJDb25maWcucGFnZUNvbnRhaW5lciArIFwiID4gKjpmaXJzdFwiICkuYXR0cignaWQnKSsnPiMnKyQodGhpcykuY29udGVudHMoKS5maW5kKCBiQ29uZmlnLnBhZ2VDb250YWluZXIgKyBcIiA+ICo6Zmlyc3RcIiApLmF0dHIoJ2lkJykrJzwvb3B0aW9uPic7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICQoJ3NlbGVjdCNwYWdlTGlua3NEcm9wZG93bicpLmFwcGVuZCggbmV3T3B0aW9uICk7XG5cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAvL2lmIHRoZXJlIGFyZW4ndCBhbnkgYmxvY2tzIHRvIGxpc3QsIGhpZGUgdGhlIGRyb3Bkb3duXG5cbiAgICAgICAgICAgIGlmKCAkKCdzZWxlY3QjcGFnZUxpbmtzRHJvcGRvd24gb3B0aW9uJykuc2l6ZSgpID09PSAxICkge1xuXG4gICAgICAgICAgICAgICAgJCgnc2VsZWN0I3BhZ2VMaW5rc0Ryb3Bkb3duJykubmV4dCgpLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAkKCdzZWxlY3QjcGFnZUxpbmtzRHJvcGRvd24nKS5uZXh0KCkubmV4dCgpLmhpZGUoKTtcblxuICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICQoJ3NlbGVjdCNwYWdlTGlua3NEcm9wZG93bicpLm5leHQoKS5zaG93KCk7XG4gICAgICAgICAgICAgICAgJCgnc2VsZWN0I3BhZ2VMaW5rc0Ryb3Bkb3duJykubmV4dCgpLm5leHQoKS5zaG93KCk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHdoZW4gdGhlIGNsaWNrZWQgZWxlbWVudCBpcyBhbiBpbWFnZVxuICAgICAgICAqL1xuICAgICAgICBlZGl0SW1hZ2U6IGZ1bmN0aW9uKGVsKSB7XG5cbiAgICAgICAgICAgICQoJ2EjaW1nX0xpbmsnKS5wYXJlbnQoKS5zaG93KCk7XG5cbiAgICAgICAgICAgIC8vc2V0IHRoZSBjdXJyZW50IFNSQ1xuICAgICAgICAgICAgJCgnLmltYWdlRmlsZVRhYicpLmZpbmQoJ2lucHV0I2ltYWdlVVJMJykudmFsKCAkKGVsKS5hdHRyKCdzcmMnKSApO1xuXG4gICAgICAgICAgICAvL3Jlc2V0IHRoZSBmaWxlIHVwbG9hZFxuICAgICAgICAgICAgJCgnLmltYWdlRmlsZVRhYicpLmZpbmQoJ2EuZmlsZWlucHV0LWV4aXN0cycpLmNsaWNrKCk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICB3aGVuIHRoZSBjbGlja2VkIGVsZW1lbnQgaXMgYSB2aWRlbyBlbGVtZW50XG4gICAgICAgICovXG4gICAgICAgIGVkaXRWaWRlbzogZnVuY3Rpb24oZWwpIHtcblxuICAgICAgICAgICAgdmFyIG1hdGNoUmVzdWx0cztcblxuICAgICAgICAgICAgJCgnYSN2aWRlb19MaW5rJykucGFyZW50KCkuc2hvdygpO1xuICAgICAgICAgICAgJCgnYSN2aWRlb19MaW5rJykuY2xpY2soKTtcblxuICAgICAgICAgICAgLy9pbmplY3QgY3VycmVudCB2aWRlbyBJRCxjaGVjayBpZiB3ZSdyZSBkZWFsaW5nIHdpdGggWW91dHViZSBvciBWaW1lb1xuXG4gICAgICAgICAgICBpZiggJChlbCkucHJldigpLmF0dHIoJ3NyYycpLmluZGV4T2YoXCJ2aW1lby5jb21cIikgPiAtMSApIHsvL3ZpbWVvXG5cbiAgICAgICAgICAgICAgICBtYXRjaFJlc3VsdHMgPSAkKGVsKS5wcmV2KCkuYXR0cignc3JjJykubWF0Y2goL3BsYXllclxcLnZpbWVvXFwuY29tXFwvdmlkZW9cXC8oWzAtOV0qKS8pO1xuXG4gICAgICAgICAgICAgICAgJCgnI3ZpZGVvX1RhYiBpbnB1dCN2aW1lb0lEJykudmFsKCBtYXRjaFJlc3VsdHNbbWF0Y2hSZXN1bHRzLmxlbmd0aC0xXSApO1xuICAgICAgICAgICAgICAgICQoJyN2aWRlb19UYWIgaW5wdXQjeW91dHViZUlEJykudmFsKCcnKTtcblxuICAgICAgICAgICAgfSBlbHNlIHsvL3lvdXR1YmVcblxuICAgICAgICAgICAgICAgIC8vdGVtcCA9ICQoZWwpLnByZXYoKS5hdHRyKCdzcmMnKS5zcGxpdCgnLycpO1xuICAgICAgICAgICAgICAgIHZhciByZWdFeHAgPSAvLiooPzp5b3V0dS5iZVxcL3x2XFwvfHVcXC9cXHdcXC98ZW1iZWRcXC98d2F0Y2hcXD92PSkoW14jXFwmXFw/XSopLiovO1xuICAgICAgICAgICAgICAgIG1hdGNoUmVzdWx0cyA9ICQoZWwpLnByZXYoKS5hdHRyKCdzcmMnKS5tYXRjaChyZWdFeHApO1xuXG4gICAgICAgICAgICAgICAgJCgnI3ZpZGVvX1RhYiBpbnB1dCN5b3V0dWJlSUQnKS52YWwoIG1hdGNoUmVzdWx0c1sxXSApO1xuICAgICAgICAgICAgICAgICQoJyN2aWRlb19UYWIgaW5wdXQjdmltZW9JRCcpLnZhbCgnJyk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHdoZW4gdGhlIGNsaWNrZWQgZWxlbWVudCBpcyBhbiBmYSBpY29uXG4gICAgICAgICovXG4gICAgICAgIGVkaXRJY29uOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCgnYSNpY29uX0xpbmsnKS5wYXJlbnQoKS5zaG93KCk7XG5cbiAgICAgICAgICAgIC8vZ2V0IGljb24gY2xhc3MgbmFtZSwgc3RhcnRpbmcgd2l0aCBmYS1cbiAgICAgICAgICAgIHZhciBnZXQgPSAkLmdyZXAodGhpcy5hY3RpdmVFbGVtZW50LmVsZW1lbnQuY2xhc3NOYW1lLnNwbGl0KFwiIFwiKSwgZnVuY3Rpb24odiwgaSl7XG5cbiAgICAgICAgICAgICAgICByZXR1cm4gdi5pbmRleE9mKCdmYS0nKSA9PT0gMDtcblxuICAgICAgICAgICAgfSkuam9pbigpO1xuXG4gICAgICAgICAgICAkKCdzZWxlY3QjaWNvbnMgb3B0aW9uJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgaWYoICQodGhpcykudmFsKCkgPT09IGdldCApIHtcblxuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmF0dHIoJ3NlbGVjdGVkJywgdHJ1ZSk7XG5cbiAgICAgICAgICAgICAgICAgICAgJCgnI2ljb25zJykudHJpZ2dlcignY2hvc2VuOnVwZGF0ZWQnKTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBkZWxldGUgc2VsZWN0ZWQgZWxlbWVudFxuICAgICAgICAqL1xuICAgICAgICBkZWxldGVFbGVtZW50OiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIHRvRGVsO1xuXG4gICAgICAgICAgICAvL2RldGVybWluZSB3aGF0IHRvIGRlbGV0ZVxuICAgICAgICAgICAgaWYoICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5wcm9wKCd0YWdOYW1lJykgPT09ICdBJyApIHsvL2FuY29yXG5cbiAgICAgICAgICAgICAgICBpZiggJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnBhcmVudCgpLnByb3AoJ3RhZ05hbWUnKSA9PT0nTEknICkgey8vY2xvbmUgdGhlIExJXG5cbiAgICAgICAgICAgICAgICAgICAgdG9EZWwgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucGFyZW50KCk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuXG4gICAgICAgICAgICAgICAgICAgIHRvRGVsID0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpO1xuXG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB9IGVsc2UgaWYoICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5wcm9wKCd0YWdOYW1lJykgPT09ICdJTUcnICkgey8vaW1hZ2VcblxuICAgICAgICAgICAgICAgIGlmKCAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucGFyZW50KCkucHJvcCgndGFnTmFtZScpID09PSAnQScgKSB7Ly9jbG9uZSB0aGUgQVxuXG4gICAgICAgICAgICAgICAgICAgIHRvRGVsID0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnBhcmVudCgpO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICAgICB0b0RlbCA9ICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSBlbHNlIHsvL2V2ZXJ5dGhpbmcgZWxzZVxuXG4gICAgICAgICAgICAgICAgdG9EZWwgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCk7XG5cbiAgICAgICAgICAgIH1cblxuXG4gICAgICAgICAgICB0b0RlbC5mYWRlT3V0KDUwMCwgZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgIHZhciByYW5kb21FbCA9ICQodGhpcykuY2xvc2VzdCgnYm9keScpLmZpbmQoJyo6Zmlyc3QnKTtcblxuICAgICAgICAgICAgICAgIHRvRGVsLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICAgICAgLyogU0FOREJPWCAqL1xuXG4gICAgICAgICAgICAgICAgdmFyIGVsZW1lbnRJRCA9ICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdpZCcpO1xuXG4gICAgICAgICAgICAgICAgJCgnIycrc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94KS5jb250ZW50cygpLmZpbmQoJyMnK2VsZW1lbnRJRCkucmVtb3ZlKCk7XG5cbiAgICAgICAgICAgICAgICAvKiBFTkQgU0FOREJPWCAqL1xuXG4gICAgICAgICAgICAgICAgc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5wYXJlbnRCbG9jay5oZWlnaHRBZGp1c3RtZW50KCk7XG5cbiAgICAgICAgICAgICAgICAvL3dlJ3ZlIGdvdCBwZW5kaW5nIGNoYW5nZXNcbiAgICAgICAgICAgICAgICBzaXRlQnVpbGRlci5zaXRlLnNldFBlbmRpbmdDaGFuZ2VzKHRydWUpO1xuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgJCgnI2RlbGV0ZUVsZW1lbnQnKS5tb2RhbCgnaGlkZScpO1xuXG4gICAgICAgICAgICBzdHlsZWVkaXRvci5jbG9zZVN0eWxlRWRpdG9yKCk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBjbG9uZXMgdGhlIHNlbGVjdGVkIGVsZW1lbnRcbiAgICAgICAgKi9cbiAgICAgICAgY2xvbmVFbGVtZW50OiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgdmFyIHRoZUNsb25lLCB0aGVDbG9uZTIsIHRoZU9uZSwgY2xvbmVkLCBjbG9uZVBhcmVudCwgZWxlbWVudElEO1xuXG4gICAgICAgICAgICBpZiggJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnBhcmVudCgpLmhhc0NsYXNzKCdwcm9wQ2xvbmUnKSApIHsvL2Nsb25lIHRoZSBwYXJlbnQgZWxlbWVudFxuXG4gICAgICAgICAgICAgICAgdGhlQ2xvbmUgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucGFyZW50KCkuY2xvbmUoKTtcbiAgICAgICAgICAgICAgICB0aGVDbG9uZS5maW5kKCAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucHJvcCgndGFnTmFtZScpICkuYXR0cignc3R5bGUnLCAnJyk7XG5cbiAgICAgICAgICAgICAgICB0aGVDbG9uZTIgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucGFyZW50KCkuY2xvbmUoKTtcbiAgICAgICAgICAgICAgICB0aGVDbG9uZTIuZmluZCggJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnByb3AoJ3RhZ05hbWUnKSApLmF0dHIoJ3N0eWxlJywgJycpO1xuXG4gICAgICAgICAgICAgICAgdGhlT25lID0gdGhlQ2xvbmUuZmluZCggJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnByb3AoJ3RhZ05hbWUnKSApO1xuICAgICAgICAgICAgICAgIGNsb25lZCA9ICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5wYXJlbnQoKTtcblxuICAgICAgICAgICAgICAgIGNsb25lUGFyZW50ID0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLnBhcmVudCgpLnBhcmVudCgpO1xuXG4gICAgICAgICAgICB9IGVsc2Ugey8vY2xvbmUgdGhlIGVsZW1lbnQgaXRzZWxmXG5cbiAgICAgICAgICAgICAgICB0aGVDbG9uZSA9ICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5jbG9uZSgpO1xuXG4gICAgICAgICAgICAgICAgdGhlQ2xvbmUuYXR0cignc3R5bGUnLCAnJyk7XG5cbiAgICAgICAgICAgICAgICAvKmlmKCBzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LnNhbmRib3ggKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoZUNsb25lLmF0dHIoJ2lkJywgJycpLnVuaXF1ZUlkKCk7XG4gICAgICAgICAgICAgICAgfSovXG5cbiAgICAgICAgICAgICAgICB0aGVDbG9uZTIgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuY2xvbmUoKTtcbiAgICAgICAgICAgICAgICB0aGVDbG9uZTIuYXR0cignc3R5bGUnLCAnJyk7XG5cbiAgICAgICAgICAgICAgICAvKlxuICAgICAgICAgICAgICAgIGlmKCBzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LnNhbmRib3ggKSB7XG4gICAgICAgICAgICAgICAgICAgIHRoZUNsb25lMi5hdHRyKCdpZCcsIHRoZUNsb25lLmF0dHIoJ2lkJykpO1xuICAgICAgICAgICAgICAgIH0qL1xuXG4gICAgICAgICAgICAgICAgdGhlT25lID0gdGhlQ2xvbmU7XG4gICAgICAgICAgICAgICAgY2xvbmVkID0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpO1xuXG4gICAgICAgICAgICAgICAgY2xvbmVQYXJlbnQgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkucGFyZW50KCk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgY2xvbmVkLmFmdGVyKCB0aGVDbG9uZSApO1xuXG4gICAgICAgICAgICAvKiBTQU5EQk9YICovXG5cbiAgICAgICAgICAgIGlmKCBzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LnNhbmRib3ggKSB7XG5cbiAgICAgICAgICAgICAgICBlbGVtZW50SUQgPSAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignaWQnKTtcbiAgICAgICAgICAgICAgICAkKCcjJytzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LnNhbmRib3gpLmNvbnRlbnRzKCkuZmluZCgnIycrZWxlbWVudElEKS5hZnRlciggdGhlQ2xvbmUyICk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLyogRU5EIFNBTkRCT1ggKi9cblxuICAgICAgICAgICAgLy9tYWtlIHN1cmUgdGhlIG5ldyBlbGVtZW50IGdldHMgdGhlIHByb3BlciBldmVudHMgc2V0IG9uIGl0XG4gICAgICAgICAgICB2YXIgbmV3RWxlbWVudCA9IG5ldyBjYW52YXNFbGVtZW50KHRoZU9uZS5nZXQoMCkpO1xuICAgICAgICAgICAgbmV3RWxlbWVudC5hY3RpdmF0ZSgpO1xuXG4gICAgICAgICAgICAvL3Bvc3NpYmxlIGhlaWdodCBhZGp1c3RtZW50c1xuICAgICAgICAgICAgc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5wYXJlbnRCbG9jay5oZWlnaHRBZGp1c3RtZW50KCk7XG5cbiAgICAgICAgICAgIC8vd2UndmUgZ290IHBlbmRpbmcgY2hhbmdlc1xuICAgICAgICAgICAgc2l0ZUJ1aWxkZXIuc2l0ZS5zZXRQZW5kaW5nQ2hhbmdlcyh0cnVlKTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHJlc2V0cyB0aGUgYWN0aXZlIGVsZW1lbnRcbiAgICAgICAgKi9cbiAgICAgICAgcmVzZXRFbGVtZW50OiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgaWYoICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5jbG9zZXN0KCdib2R5Jykud2lkdGgoKSAhPT0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLndpZHRoKCkgKSB7XG5cbiAgICAgICAgICAgICAgICAkKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignc3R5bGUnLCAnJykuY3NzKHsnb3V0bGluZSc6ICczcHggZGFzaGVkIHJlZCcsICdjdXJzb3InOiAncG9pbnRlcid9KTtcblxuICAgICAgICAgICAgfSBlbHNlIHtcblxuICAgICAgICAgICAgICAgICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdzdHlsZScsICcnKS5jc3MoeydvdXRsaW5lJzogJzNweCBkYXNoZWQgcmVkJywgJ291dGxpbmUtb2Zmc2V0JzonLTNweCcsICdjdXJzb3InOiAncG9pbnRlcid9KTtcblxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvKiBTQU5EQk9YICovXG5cbiAgICAgICAgICAgIGlmKCBzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LnNhbmRib3ggKSB7XG5cbiAgICAgICAgICAgICAgICB2YXIgZWxlbWVudElEID0gJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmF0dHIoJ2lkJyk7XG4gICAgICAgICAgICAgICAgJCgnIycrc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5zYW5kYm94KS5jb250ZW50cygpLmZpbmQoJyMnK2VsZW1lbnRJRCkuYXR0cignc3R5bGUnLCAnJyk7XG5cbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgLyogRU5EIFNBTkRCT1ggKi9cblxuICAgICAgICAgICAgJCgnI3N0eWxlRWRpdG9yIGZvcm0jc3R5bGluZ0Zvcm0nKS5oZWlnaHQoICQoJyNzdHlsZUVkaXRvciBmb3JtI3N0eWxpbmdGb3JtJykuaGVpZ2h0KCkrXCJweFwiICk7XG5cbiAgICAgICAgICAgICQoJyNzdHlsZUVkaXRvciBmb3JtI3N0eWxpbmdGb3JtIC5mb3JtLWdyb3VwOm5vdCgjc3R5bGVFbFRlbXBsYXRlKScpLmZhZGVPdXQoNTAwLCBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmUoKTtcblxuICAgICAgICAgICAgfSk7XG5cblxuICAgICAgICAgICAgLy9yZXNldCBpY29uXG5cbiAgICAgICAgICAgIGlmKCBzdHlsZWVkaXRvci5fb2xkSWNvblskKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudCkuYXR0cignaWQnKV0gIT09IG51bGwgKSB7XG5cbiAgICAgICAgICAgICAgICB2YXIgZ2V0ID0gJC5ncmVwKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQuZWxlbWVudC5jbGFzc05hbWUuc3BsaXQoXCIgXCIpLCBmdW5jdGlvbih2LCBpKXtcblxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gdi5pbmRleE9mKCdmYS0nKSA9PT0gMDtcblxuICAgICAgICAgICAgICAgIH0pLmpvaW4oKTtcblxuICAgICAgICAgICAgICAgICQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5yZW1vdmVDbGFzcyggZ2V0ICkuYWRkQ2xhc3MoIHN0eWxlZWRpdG9yLl9vbGRJY29uWyQoc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5lbGVtZW50KS5hdHRyKCdpZCcpXSApO1xuXG4gICAgICAgICAgICAgICAgJCgnc2VsZWN0I2ljb25zIG9wdGlvbicpLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICAgICBpZiggJCh0aGlzKS52YWwoKSA9PT0gc3R5bGVlZGl0b3IuX29sZEljb25bJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmF0dHIoJ2lkJyldICkge1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmF0dHIoJ3NlbGVjdGVkJywgdHJ1ZSk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCcjaWNvbnMnKS50cmlnZ2VyKCdjaG9zZW46dXBkYXRlZCcpO1xuXG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIHNldFRpbWVvdXQoIGZ1bmN0aW9uKCl7c3R5bGVlZGl0b3IuYnVpbGRlU3R5bGVFbGVtZW50cyggJChzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50LmVsZW1lbnQpLmF0dHIoJ2RhdGEtc2VsZWN0b3InKSApO30sIDU1MCk7XG5cbiAgICAgICAgICAgIHNpdGVCdWlsZGVyLnNpdGUuc2V0UGVuZGluZ0NoYW5nZXModHJ1ZSk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIHJlc2V0U2VsZWN0TGlua3NQYWdlczogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICQoJyNpbnRlcm5hbExpbmtzRHJvcGRvd24nKS5zZWxlY3QyKCd2YWwnLCAnIycpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgcmVzZXRTZWxlY3RMaW5rc0ludGVybmFsOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCgnI3BhZ2VMaW5rc0Ryb3Bkb3duJykuc2VsZWN0MigndmFsJywgJyMnKTtcblxuICAgICAgICB9LFxuXG4gICAgICAgIHJlc2V0U2VsZWN0QWxsTGlua3M6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAkKCcjaW50ZXJuYWxMaW5rc0Ryb3Bkb3duJykuc2VsZWN0MigndmFsJywgJyMnKTtcbiAgICAgICAgICAgICQoJyNwYWdlTGlua3NEcm9wZG93bicpLnNlbGVjdDIoJ3ZhbCcsICcjJyk7XG4gICAgICAgICAgICB0aGlzLnNlbGVjdCgpO1xuXG4gICAgICAgIH0sXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIGhpZGVzIGZpbGUgdXBsb2FkIGZvcm1zXG4gICAgICAgICovXG4gICAgICAgIGhpZGVGaWxlVXBsb2FkczogZnVuY3Rpb24oKSB7XG5cbiAgICAgICAgICAgICQoJ2Zvcm0jaW1hZ2VVcGxvYWRGb3JtJykuaGlkZSgpO1xuICAgICAgICAgICAgJCgnI2ltYWdlTW9kYWwgI3VwbG9hZFRhYkxJJykuaGlkZSgpO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgY2xvc2VzIHRoZSBzdHlsZSBlZGl0b3JcbiAgICAgICAgKi9cbiAgICAgICAgY2xvc2VTdHlsZUVkaXRvcjogZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgaWYoIE9iamVjdC5rZXlzKHN0eWxlZWRpdG9yLmFjdGl2ZUVsZW1lbnQpLmxlbmd0aCA+IDAgKSB7XG4gICAgICAgICAgICAgICAgc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5yZW1vdmVPdXRsaW5lKCk7XG4gICAgICAgICAgICAgICAgc3R5bGVlZGl0b3IuYWN0aXZlRWxlbWVudC5hY3RpdmF0ZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiggJCgnI3N0eWxlRWRpdG9yJykuY3NzKCdsZWZ0JykgPT09ICcwcHgnICkge1xuXG4gICAgICAgICAgICAgICAgc3R5bGVlZGl0b3IudG9nZ2xlU2lkZVBhbmVsKCdjbG9zZScpO1xuXG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICB0b2dnbGVzIHRoZSBzaWRlIHBhbmVsXG4gICAgICAgICovXG4gICAgICAgIHRvZ2dsZVNpZGVQYW5lbDogZnVuY3Rpb24odmFsKSB7XG5cbiAgICAgICAgICAgIGlmKCB2YWwgPT09ICdvcGVuJyAmJiAkKCcjc3R5bGVFZGl0b3InKS5jc3MoJ2xlZnQnKSA9PT0gJy0zMDBweCcgKSB7XG4gICAgICAgICAgICAgICAgJCgnI3N0eWxlRWRpdG9yJykuYW5pbWF0ZSh7J2xlZnQnOiAnMHB4J30sIDI1MCk7XG4gICAgICAgICAgICB9IGVsc2UgaWYoIHZhbCA9PT0gJ2Nsb3NlJyAmJiAkKCcjc3R5bGVFZGl0b3InKS5jc3MoJ2xlZnQnKSA9PT0gJzBweCcgKSB7XG4gICAgICAgICAgICAgICAgJCgnI3N0eWxlRWRpdG9yJykuYW5pbWF0ZSh7J2xlZnQnOiAnLTMwMHB4J30sIDI1MCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBFdmVudCBoYW5kbGVyIGZvciB3aGVuIHRoaXMgbW9kZSBnZXRzIGRlYWN0aXZhdGVkXG4gICAgICAgICovXG4gICAgICAgIGRlQWN0aXZhdGVNb2RlOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgaWYoIE9iamVjdC5rZXlzKCBzdHlsZWVkaXRvci5hY3RpdmVFbGVtZW50ICkubGVuZ3RoID4gMCApIHtcbiAgICAgICAgICAgICAgICBzdHlsZWVkaXRvci5jbG9zZVN0eWxlRWRpdG9yKCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIC8vZGVhY3RpdmF0ZSBhbGwgc3R5bGUgaXRlbXMgb24gdGhlIGNhbnZhc1xuICAgICAgICAgICAgZm9yKCB2YXIgaSA9MDsgaSA8IHN0eWxlZWRpdG9yLmFsbFN0eWxlSXRlbXNPbkNhbnZhcy5sZW5ndGg7IGkrKyApIHtcbiAgICAgICAgICAgICAgICBzdHlsZWVkaXRvci5hbGxTdHlsZUl0ZW1zT25DYW52YXNbaV0uZGVhY3RpdmF0ZSgpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAvL0FkZCBvdmVybGF5IGFnYWluXG4gICAgICAgICAgICAvLyBmb3IodmFyIGkgPSAxOyBpIDw9ICQoXCJ1bCNwYWdlMSBsaVwiKS5sZW5ndGg7IGkrKyl7XG4gICAgICAgICAgICAvLyAgICAgdmFyIGlkID0gXCIjdWktaWQtXCIgKyBpO1xuICAgICAgICAgICAgLy8gICAgIGFsZXJ0KGlkKTtcbiAgICAgICAgICAgIC8vICAgICAvLyBvdmVybGF5ID0gJCgnPHNwYW4gY2xhc3M9XCJvdmVybGF5XCI+PHNwYW4gY2xhc3M9XCJmdWktZXllXCI+PC9zcGFuPjwvc3Bhbj4nKTtcbiAgICAgICAgICAgIC8vICAgICAvLyAkKGlkKS5jb250ZW50cygpLmZpbmQoJ2Eub3ZlcicpLmFwcGVuZCggb3ZlcmxheSApO1xuICAgICAgICAgICAgLy8gfVxuXG4gICAgICAgIH1cblxuICAgIH07XG5cbiAgICBzdHlsZWVkaXRvci5pbml0KCk7XG5cbiAgICBleHBvcnRzLnN0eWxlZWRpdG9yID0gc3R5bGVlZGl0b3I7XG5cbn0oKSk7IiwiKGZ1bmN0aW9uICgpIHtcblx0XCJ1c2Ugc3RyaWN0XCI7XG5cblx0dmFyIHNpdGVCdWlsZGVyID0gcmVxdWlyZSgnLi9idWlsZGVyLmpzJyk7XG5cdHZhciBhcHBVSSA9IHJlcXVpcmUoJy4vdWkuanMnKS5hcHBVSTtcblxuXHR2YXIgdGVtcGxhdGVzID0ge1xuXG4gICAgICAgIHVsVGVtcGxhdGVzOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgndGVtcGxhdGVzJyksXG4gICAgICAgIGJ1dHRvblNhdmVUZW1wbGF0ZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NhdmVUZW1wbGF0ZScpLFxuICAgICAgICBtb2RhbERlbGV0ZVRlbXBsYXRlOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZGVsVGVtcGxhdGVNb2RhbCcpLFxuXG4gICAgICAgIGluaXQ6IGZ1bmN0aW9uKCkge1xuXG4gICAgICAgICAgICAvL2Zvcm1hdCB0aGUgdGVtcGxhdGUgdGh1bWJuYWlsc1xuICAgICAgICAgICAgdGhpcy56b29tVGVtcGxhdGVJZnJhbWVzKCk7XG5cbiAgICAgICAgICAgIC8vbWFrZSB0ZW1wbGF0ZSB0aHVtYnMgZHJhZ2dhYmxlXG4gICAgICAgICAgICB0aGlzLm1ha2VEcmFnZ2FibGUoKTtcblxuICAgICAgICAgICAgJCh0aGlzLmJ1dHRvblNhdmVUZW1wbGF0ZSkub24oJ2NsaWNrJywgdGhpcy5zYXZlVGVtcGxhdGUpO1xuXG4gICAgICAgICAgICAvL2xpc3RlbiBmb3IgdGhlIGJlZm9yZVNhdmUgZXZlbnRcbiAgICAgICAgICAgICQoJ2JvZHknKS5vbignc2l0ZURhdGFMb2FkZWQnLCBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgaWYoIHNpdGVCdWlsZGVyLnNpdGUuaXNfYWRtaW4gPT09IDEgKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdGVtcGxhdGVzLmFkZERlbExpbmtzKCk7XG4gICAgICAgICAgICAgICAgICAgICQodGVtcGxhdGVzLm1vZGFsRGVsZXRlVGVtcGxhdGUpLm9uKCdzaG93LmJzLm1vZGFsJywgdGVtcGxhdGVzLnByZXBUZW1wbGF0ZURlbGV0ZU1vZGFsKTtcblxuICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBhcHBsaWVzIHpvb21lciB0byBhbGwgdGVtcGxhdGUgaWZyYW1lcyBpbiB0aGUgc2lkZWJhclxuICAgICAgICAqL1xuICAgICAgICB6b29tVGVtcGxhdGVJZnJhbWVzOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCh0aGlzLnVsVGVtcGxhdGVzKS5maW5kKCdpZnJhbWUnKS5lYWNoKGZ1bmN0aW9uKCl7XG5cbiAgICAgICAgICAgICAgICAkKHRoaXMpLnpvb21lcih7XG4gICAgICAgICAgICAgICAgICAgIHpvb206IDAuMjUsXG4gICAgICAgICAgICAgICAgICAgIHdpZHRoOiAyNzAsXG4gICAgICAgICAgICAgICAgICAgIGhlaWdodDogJCh0aGlzKS5hdHRyKCdkYXRhLWhlaWdodCcpKjAuMjUsXG4gICAgICAgICAgICAgICAgICAgIG1lc3NhZ2U6IFwiRHJhZyAmIERyb3AgbWVcIlxuICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIG1ha2VzIHRoZSB0ZW1wbGF0ZSB0aHVtYm5haWxzIGRyYWdnYWJsZVxuICAgICAgICAqL1xuICAgICAgICBtYWtlRHJhZ2dhYmxlOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCh0aGlzLnVsVGVtcGxhdGVzKS5maW5kKCdsaScpLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgICQodGhpcykuZHJhZ2dhYmxlKHtcbiAgICAgICAgICAgICAgICAgICAgaGVscGVyOiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuICQoJzxkaXYgc3R5bGU9XCJoZWlnaHQ6IDEwMHB4OyB3aWR0aDogMzAwcHg7IGJhY2tncm91bmQ6ICNGOUZBRkE7IGJveC1zaGFkb3c6IDVweCA1cHggMXB4IHJnYmEoMCwwLDAsMC4xKTsgdGV4dC1hbGlnbjogY2VudGVyOyBsaW5lLWhlaWdodDogMTAwcHg7IGZvbnQtc2l6ZTogMjhweDsgY29sb3I6ICMxNkEwODVcIj48c3BhbiBjbGFzcz1cImZ1aS1saXN0XCI+PC9zcGFuPjwvZGl2PicpO1xuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHJldmVydDogJ2ludmFsaWQnLFxuICAgICAgICAgICAgICAgICAgICBhcHBlbmRUbzogJ2JvZHknLFxuICAgICAgICAgICAgICAgICAgICBjb25uZWN0VG9Tb3J0YWJsZTogJyNwYWdlMScsXG4gICAgICAgICAgICAgICAgICAgIHN0YXJ0OiBmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL3N3aXRjaCB0byBibG9jayBtb2RlXG4gICAgICAgICAgICAgICAgICAgICAgICAkKCdpbnB1dDpyYWRpb1tuYW1lPW1vZGVdJykucGFyZW50KCkuYWRkQ2xhc3MoJ2Rpc2FibGVkJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCdpbnB1dDpyYWRpb1tuYW1lPW1vZGVdI21vZGVCbG9jaycpLnJhZGlvKCdjaGVjaycpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAvL3Nob3cgYWxsIGlmcmFtZSBjb3ZlcnMgYW5kIGFjdGl2YXRlIGRlc2lnbk1vZGVcblxuICAgICAgICAgICAgICAgICAgICAgICAgJCgnI3BhZ2VMaXN0IHVsIC56b29tZXItd3JhcHBlciAuem9vbWVyLWNvdmVyJykuZWFjaChmdW5jdGlvbigpe1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5zaG93KCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIC8vZGlzYWJsZSBjbGljayBldmVudHMgb24gY2hpbGQgYW5jb3JzXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCdhJykuZWFjaChmdW5jdGlvbigpe1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnVuYmluZCgnY2xpY2snKS5iaW5kKCdjbGljaycsIGZ1bmN0aW9uKGUpe1xuICAgICAgICAgICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgfSxcblxuXG4gICAgICAgIC8qXG4gICAgICAgICAgICBTYXZlcyBhIHBhZ2UgYXMgYSB0ZW1wbGF0ZVxuICAgICAgICAqL1xuICAgICAgICBzYXZlVGVtcGxhdGU6IGZ1bmN0aW9uKGUpIHtcblxuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICAvL2Rpc2FibGUgYnV0dG9uXG4gICAgICAgICAgICAkKFwiYSNzYXZlUGFnZVwiKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgLy9yZW1vdmUgb2xkIGFsZXJ0c1xuICAgICAgICAgICAgJCgnI2Vycm9yTW9kYWwgLm1vZGFsLWJvZHkgPiAqLCAjc3VjY2Vzc01vZGFsIC5tb2RhbC1ib2R5ID4gKicpLmVhY2goZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZSgpO1xuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIHNpdGVCdWlsZGVyLnNpdGUucHJlcEZvclNhdmUodHJ1ZSk7XG5cbiAgICAgICAgICAgIHZhciBzZXJ2ZXJEYXRhID0ge307XG4gICAgICAgICAgICBzZXJ2ZXJEYXRhLnBhZ2VzID0gc2l0ZUJ1aWxkZXIuc2l0ZS5zaXRlUGFnZXNSZWFkeUZvclNlcnZlcjtcbiAgICAgICAgICAgIHNlcnZlckRhdGEuc2l0ZURhdGEgPSBzaXRlQnVpbGRlci5zaXRlLmRhdGE7XG4gICAgICAgICAgICBzZXJ2ZXJEYXRhLmZ1bGxQYWdlID0gXCI8aHRtbD5cIiskKHNpdGVCdWlsZGVyLnNpdGUuc2tlbGV0b24pLmNvbnRlbnRzKCkuZmluZCgnaHRtbCcpLmh0bWwoKStcIjwvaHRtbD5cIjtcblxuICAgICAgICAgICAgLy9hcmUgd2UgdXBkYXRpbmcgYW4gZXhpc3RpbmcgdGVtcGxhdGUgb3IgY3JlYXRpbmcgYSBuZXcgb25lP1xuICAgICAgICAgICAgc2VydmVyRGF0YS50ZW1wbGF0ZUlEID0gc2l0ZUJ1aWxkZXIuYnVpbGRlci50ZW1wbGF0ZUlEO1xuXG4gICAgICAgICAgICBjb25zb2xlLmxvZyhzaXRlQnVpbGRlci5idWlsZGVyLnRlbXBsYXRlSUQpO1xuXG4gICAgICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgICAgIHVybDogYXBwVUkuc2l0ZVVybCtcInNpdGUvdHNhdmVcIixcbiAgICAgICAgICAgICAgICB0eXBlOiBcIlBPU1RcIixcbiAgICAgICAgICAgICAgICBkYXRhVHlwZTogXCJqc29uXCIsXG4gICAgICAgICAgICAgICAgZGF0YTogc2VydmVyRGF0YVxuICAgICAgICAgICAgfSkuZG9uZShmdW5jdGlvbihyZXMpe1xuXG5cbiAgICAgICAgICAgICAgICAvL2VuYWJsZSBidXR0b25cbiAgICAgICAgICAgICAgICAkKFwiYSNzYXZlUGFnZVwiKS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcblxuICAgICAgICAgICAgICAgIGlmKCByZXMucmVzcG9uc2VDb2RlID09PSAwICkge1xuXG4gICAgICAgICAgICAgICAgICAgICQoJyNlcnJvck1vZGFsIC5tb2RhbC1ib2R5JykuYXBwZW5kKCAkKHJlcy5yZXNwb25zZUhUTUwpICk7XG4gICAgICAgICAgICAgICAgICAgICQoJyNlcnJvck1vZGFsJykubW9kYWwoJ3Nob3cnKTtcbiAgICAgICAgICAgICAgICAgICAgc2l0ZUJ1aWxkZXIuYnVpbGRlci50ZW1wbGF0ZUlEID0gMDtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggcmVzLnJlc3BvbnNlQ29kZSA9PT0gMSApIHtcblxuICAgICAgICAgICAgICAgICAgICAkKCcjc3VjY2Vzc01vZGFsIC5tb2RhbC1ib2R5JykuYXBwZW5kKCAkKHJlcy5yZXNwb25zZUhUTUwpICk7XG4gICAgICAgICAgICAgICAgICAgICQoJyNzdWNjZXNzTW9kYWwnKS5tb2RhbCgnc2hvdycpO1xuICAgICAgICAgICAgICAgICAgICBzaXRlQnVpbGRlci5idWlsZGVyLnRlbXBsYXRlSUQgPSByZXMudGVtcGxhdGVJRDtcblxuICAgICAgICAgICAgICAgICAgICAvL25vIG1vcmUgcGVuZGluZyBjaGFuZ2VzXG4gICAgICAgICAgICAgICAgICAgIHNpdGVCdWlsZGVyLnNpdGUuc2V0UGVuZGluZ0NoYW5nZXMoZmFsc2UpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgIH0sXG5cblxuICAgICAgICAvKlxuICAgICAgICAgICAgYWRkcyBERUwgbGlua3MgZm9yIGFkbWluIHVzZXJzXG4gICAgICAgICovXG4gICAgICAgIGFkZERlbExpbmtzOiBmdW5jdGlvbigpIHtcblxuICAgICAgICAgICAgJCh0aGlzLnVsVGVtcGxhdGVzKS5maW5kKCdsaScpLmVhY2goZnVuY3Rpb24oKXtcblxuICAgICAgICAgICAgICAgIHZhciBuZXdMaW5rID0gJCgnPGEgaHJlZj1cIiNkZWxUZW1wbGF0ZU1vZGFsXCIgZGF0YS10b2dnbGU9XCJtb2RhbFwiIGRhdGEtcGFnZWlkPVwiJyskKHRoaXMpLmF0dHIoJ2RhdGEtcGFnZWlkJykrJ1wiIGNsYXNzPVwiYnRuIGJ0bi1kYW5nZXIgYnRuLXNtXCI+REVMPC9hPicpO1xuICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLnpvb21lci1jb3ZlcicpLmFwcGVuZCggbmV3TGluayApO1xuXG4gICAgICAgICAgICB9KTtcblxuICAgICAgICB9LFxuXG5cbiAgICAgICAgLypcbiAgICAgICAgICAgIHByZXBzIHRoZSBkZWxldGUgdGVtcGxhdGUgbW9kYWxcbiAgICAgICAgKi9cbiAgICAgICAgcHJlcFRlbXBsYXRlRGVsZXRlTW9kYWw6IGZ1bmN0aW9uKGUpIHtcblxuICAgICAgICAgICAgdmFyIGJ1dHRvbiA9ICQoZS5yZWxhdGVkVGFyZ2V0KTsgLy8gQnV0dG9uIHRoYXQgdHJpZ2dlcmVkIHRoZSBtb2RhbFxuXHRcdCAgXHR2YXIgcGFnZUlEID0gYnV0dG9uLmF0dHIoJ2RhdGEtcGFnZWlkJyk7IC8vIEV4dHJhY3QgaW5mbyBmcm9tIGRhdGEtKiBhdHRyaWJ1dGVzXG5cblx0XHQgIFx0JCgnI2RlbFRlbXBsYXRlTW9kYWwnKS5maW5kKCcjdGVtcGxhdGVEZWxCdXR0b24nKS5hdHRyKCdocmVmJywgJCgnI2RlbFRlbXBsYXRlTW9kYWwnKS5maW5kKCcjdGVtcGxhdGVEZWxCdXR0b24nKS5hdHRyKCdocmVmJykrXCIvXCIrcGFnZUlEKTtcbiAgICAgICAgfVxuXG4gICAgfTtcblxuICAgIHRlbXBsYXRlcy5pbml0KCk7XG5cbiAgICBleHBvcnRzLnRlbXBsYXRlcyA9IHRlbXBsYXRlcztcblxufSgpKTsiLCIoZnVuY3Rpb24gKCkge1xuXG4vKiBnbG9iYWxzIHNpdGVVcmw6ZmFsc2UsIGJhc2VVcmw6ZmFsc2UgKi9cbiAgICBcInVzZSBzdHJpY3RcIjtcbiAgICAgICAgXG4gICAgdmFyIGFwcFVJID0ge1xuICAgICAgICBcbiAgICAgICAgZmlyc3RNZW51V2lkdGg6IDE5MCxcbiAgICAgICAgc2Vjb25kTWVudVdpZHRoOiAzMDAsXG4gICAgICAgIGxvYWRlckFuaW1hdGlvbjogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2xvYWRlcicpLFxuICAgICAgICBzZWNvbmRNZW51VHJpZ2dlckNvbnRhaW5lcnM6ICQoJyNtZW51ICNtYWluICNlbGVtZW50Q2F0cywgI21lbnUgI21haW4gI3RlbXBsYXRlc1VsJyksXG4gICAgICAgIHNpdGVVcmw6IHNpdGVVcmwsXG4gICAgICAgIGJhc2VVcmw6IGJhc2VVcmwsXG4gICAgICAgIFxuICAgICAgICBzZXR1cDogZnVuY3Rpb24oKXtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgLy8gRmFkZSB0aGUgbG9hZGVyIGFuaW1hdGlvblxuICAgICAgICAgICAgJChhcHBVSS5sb2FkZXJBbmltYXRpb24pLmZhZGVPdXQoZnVuY3Rpb24oKXtcbiAgICAgICAgICAgICAgICAkKCcjbWVudScpLmFuaW1hdGUoeydsZWZ0JzogLWFwcFVJLmZpcnN0TWVudVdpZHRofSwgMTAwMCk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgLy8gVGFic1xuICAgICAgICAgICAgJChcIi5uYXYtdGFicyBhXCIpLm9uKCdjbGljaycsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgICAgICQodGhpcykudGFiKFwic2hvd1wiKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAkKFwic2VsZWN0LnNlbGVjdFwiKS5zZWxlY3QyKCk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgICQoJzpyYWRpbywgOmNoZWNrYm94JykucmFkaW9jaGVjaygpO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBUb29sdGlwc1xuICAgICAgICAgICAgJChcIltkYXRhLXRvZ2dsZT10b29sdGlwXVwiKS50b29sdGlwKFwiaGlkZVwiKTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgLy8gVGFibGU6IFRvZ2dsZSBhbGwgY2hlY2tib3hlc1xuICAgICAgICAgICAgJCgnLnRhYmxlIC50b2dnbGUtYWxsIDpjaGVja2JveCcpLm9uKCdjbGljaycsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgICAgICAgICAgIHZhciBjaCA9ICR0aGlzLnByb3AoJ2NoZWNrZWQnKTtcbiAgICAgICAgICAgICAgICAkdGhpcy5jbG9zZXN0KCcudGFibGUnKS5maW5kKCd0Ym9keSA6Y2hlY2tib3gnKS5yYWRpb2NoZWNrKCFjaCA/ICd1bmNoZWNrJyA6ICdjaGVjaycpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBcbiAgICAgICAgICAgIC8vIEFkZCBzdHlsZSBjbGFzcyBuYW1lIHRvIGEgdG9vbHRpcHNcbiAgICAgICAgICAgICQoXCIudG9vbHRpcFwiKS5hZGRDbGFzcyhmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBpZiAoJCh0aGlzKS5wcmV2KCkuYXR0cihcImRhdGEtdG9vbHRpcC1zdHlsZVwiKSkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gXCJ0b29sdGlwLVwiICsgJCh0aGlzKS5wcmV2KCkuYXR0cihcImRhdGEtdG9vbHRpcC1zdHlsZVwiKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgJChcIi5idG4tZ3JvdXBcIikub24oJ2NsaWNrJywgXCJhXCIsIGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICQodGhpcykuc2libGluZ3MoKS5yZW1vdmVDbGFzcyhcImFjdGl2ZVwiKS5lbmQoKS5hZGRDbGFzcyhcImFjdGl2ZVwiKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBGb2N1cyBzdGF0ZSBmb3IgYXBwZW5kL3ByZXBlbmQgaW5wdXRzXG4gICAgICAgICAgICAkKCcuaW5wdXQtZ3JvdXAnKS5vbignZm9jdXMnLCAnLmZvcm0tY29udHJvbCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmNsb3Nlc3QoJy5pbnB1dC1ncm91cCwgLmZvcm0tZ3JvdXAnKS5hZGRDbGFzcygnZm9jdXMnKTtcbiAgICAgICAgICAgIH0pLm9uKCdibHVyJywgJy5mb3JtLWNvbnRyb2wnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5jbG9zZXN0KCcuaW5wdXQtZ3JvdXAsIC5mb3JtLWdyb3VwJykucmVtb3ZlQ2xhc3MoJ2ZvY3VzJyk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgLy8gVGFibGU6IFRvZ2dsZSBhbGwgY2hlY2tib3hlc1xuICAgICAgICAgICAgJCgnLnRhYmxlIC50b2dnbGUtYWxsJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgdmFyIGNoID0gJCh0aGlzKS5maW5kKCc6Y2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJyk7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5jbG9zZXN0KCcudGFibGUnKS5maW5kKCd0Ym9keSA6Y2hlY2tib3gnKS5jaGVja2JveCghY2ggPyAnY2hlY2snIDogJ3VuY2hlY2snKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgXG4gICAgICAgICAgICAvLyBUYWJsZTogQWRkIGNsYXNzIHJvdyBzZWxlY3RlZFxuICAgICAgICAgICAgJCgnLnRhYmxlIHRib2R5IDpjaGVja2JveCcpLm9uKCdjaGVjayB1bmNoZWNrIHRvZ2dsZScsIGZ1bmN0aW9uIChlKSB7XG4gICAgICAgICAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKVxuICAgICAgICAgICAgICAgICwgY2hlY2sgPSAkdGhpcy5wcm9wKCdjaGVja2VkJylcbiAgICAgICAgICAgICAgICAsIHRvZ2dsZSA9IGUudHlwZSA9PT0gJ3RvZ2dsZSdcbiAgICAgICAgICAgICAgICAsIGNoZWNrYm94ZXMgPSAkKCcudGFibGUgdGJvZHkgOmNoZWNrYm94JylcbiAgICAgICAgICAgICAgICAsIGNoZWNrQWxsID0gY2hlY2tib3hlcy5sZW5ndGggPT09IGNoZWNrYm94ZXMuZmlsdGVyKCc6Y2hlY2tlZCcpLmxlbmd0aDtcblxuICAgICAgICAgICAgICAgICR0aGlzLmNsb3Nlc3QoJ3RyJylbY2hlY2sgPyAnYWRkQ2xhc3MnIDogJ3JlbW92ZUNsYXNzJ10oJ3NlbGVjdGVkLXJvdycpO1xuICAgICAgICAgICAgICAgIGlmICh0b2dnbGUpICR0aGlzLmNsb3Nlc3QoJy50YWJsZScpLmZpbmQoJy50b2dnbGUtYWxsIDpjaGVja2JveCcpLmNoZWNrYm94KGNoZWNrQWxsID8gJ2NoZWNrJyA6ICd1bmNoZWNrJyk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIFxuICAgICAgICAgICAgLy8gU3dpdGNoXG4gICAgICAgICAgICAkKFwiW2RhdGEtdG9nZ2xlPSdzd2l0Y2gnXVwiKS53cmFwKCc8ZGl2IGNsYXNzPVwic3dpdGNoXCIgLz4nKS5wYXJlbnQoKS5ib290c3RyYXBTd2l0Y2goKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIFxuICAgICAgICAgICAgYXBwVUkuc2Vjb25kTWVudVRyaWdnZXJDb250YWluZXJzLm9uKCdjbGljaycsICdhOm5vdCguYnRuKScsIGFwcFVJLnNlY29uZE1lbnVBbmltYXRpb24pO1xuICAgICAgICAgICAgICAgICAgICAgICAgXG4gICAgICAgIH0sXG4gICAgICAgIFxuICAgICAgICBzZWNvbmRNZW51QW5pbWF0aW9uOiBmdW5jdGlvbigpe1xuICAgICAgICBcbiAgICAgICAgICAgICQoJyNtZW51ICNtYWluIGEnKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XG4gICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdhY3RpdmUnKTtcblx0XG4gICAgICAgICAgICAvL3Nob3cgb25seSB0aGUgcmlnaHQgZWxlbWVudHNcbiAgICAgICAgICAgICQoJyNtZW51ICNzZWNvbmQgdWwgbGknKS5oaWRlKCk7XG4gICAgICAgICAgICAkKCcjbWVudSAjc2Vjb25kIHVsIGxpLicrJCh0aGlzKS5hdHRyKCdpZCcpKS5zaG93KCk7XG5cbiAgICAgICAgICAgIGlmKCAkKHRoaXMpLmF0dHIoJ2lkJykgPT09ICdhbGwnICkge1xuICAgICAgICAgICAgICAgICQoJyNtZW51ICNzZWNvbmQgdWwjZWxlbWVudHMgbGknKS5zaG93KCk7XHRcdFxuICAgICAgICAgICAgfVxuXHRcbiAgICAgICAgICAgICQoJy5tZW51IC5zZWNvbmQnKS5jc3MoJ2Rpc3BsYXknLCAnYmxvY2snKS5zdG9wKCkuYW5pbWF0ZSh7XG4gICAgICAgICAgICAgICAgd2lkdGg6IGFwcFVJLnNlY29uZE1lbnVXaWR0aFxuICAgICAgICAgICAgfSwgNTAwKTtcdFxuICAgICAgICAgICAgICAgIFxuICAgICAgICB9XG4gICAgICAgIFxuICAgIH07XG4gICAgXG4gICAgLy9pbml0aWF0ZSB0aGUgVUlcbiAgICBhcHBVSS5zZXR1cCgpO1xuXG5cbiAgICAvLyoqKiogRVhQT1JUU1xuICAgIG1vZHVsZS5leHBvcnRzLmFwcFVJID0gYXBwVUk7XG4gICAgXG59KCkpOyIsIihmdW5jdGlvbiAoKSB7XG4gICAgXCJ1c2Ugc3RyaWN0XCI7XG4gICAgXG4gICAgZXhwb3J0cy5nZXRSYW5kb21BcmJpdHJhcnkgPSBmdW5jdGlvbihtaW4sIG1heCkge1xuICAgICAgICByZXR1cm4gTWF0aC5mbG9vcihNYXRoLnJhbmRvbSgpICogKG1heCAtIG1pbikgKyBtaW4pO1xuICAgIH07XG5cbiAgICBleHBvcnRzLmdldFBhcmFtZXRlckJ5TmFtZSA9IGZ1bmN0aW9uIChuYW1lLCB1cmwpIHtcblxuICAgICAgICBpZiAoIXVybCkgdXJsID0gd2luZG93LmxvY2F0aW9uLmhyZWY7XG4gICAgICAgIG5hbWUgPSBuYW1lLnJlcGxhY2UoL1tcXFtcXF1dL2csIFwiXFxcXCQmXCIpO1xuICAgICAgICB2YXIgcmVnZXggPSBuZXcgUmVnRXhwKFwiWz8mXVwiICsgbmFtZSArIFwiKD0oW14mI10qKXwmfCN8JClcIiksXG4gICAgICAgICAgICByZXN1bHRzID0gcmVnZXguZXhlYyh1cmwpO1xuICAgICAgICBpZiAoIXJlc3VsdHMpIHJldHVybiBudWxsO1xuICAgICAgICBpZiAoIXJlc3VsdHNbMl0pIHJldHVybiAnJztcbiAgICAgICAgcmV0dXJuIGRlY29kZVVSSUNvbXBvbmVudChyZXN1bHRzWzJdLnJlcGxhY2UoL1xcKy9nLCBcIiBcIikpO1xuICAgICAgICBcbiAgICB9O1xuICAgIFxufSgpKTsiXX0=
