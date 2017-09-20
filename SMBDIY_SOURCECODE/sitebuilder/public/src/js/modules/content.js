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