// Add templates into Elementor system
jQuery( document ).ready(function($) {

    /* Add actions to the EUL view */
    var EUL_updateActions = function(insertIndex){

        /* INSERT template buttons */
        $('.ELU__btn-template-insert').unbind('click');
        $('.ELU__btn-template-insert').click(function(){
            var ELU_selectedElement = this;
            elementor.templates.layout.showLoadingView();
            $.post(ajaxurl, { action : 'check_if_required_plugin_is_installed', required: $(ELU_selectedElement).attr("data-requirements")}, function(condition) {
                if(condition == 'true'){
                    if($("#"+$(ELU_selectedElement).attr("data-version")).length && $("#"+$(ELU_selectedElement).attr("data-version")).val() != 'none'){ // If versions exist
                        var filename = $(ELU_selectedElement).attr("data-template-name")+"-"+$("#"+$(ELU_selectedElement).attr("data-version")).val()+".json";
                    } else {
                        var filename = $(ELU_selectedElement).attr("data-template-name")+".json";  
                    }
                    //console.log(filename);
                    $.post(ajaxurl, { action : 'get_content_from_elementor_export_file', filename: filename}, function(data) {
                        data = JSON.parse(data);
                        //console.log(data);
                        if(insertIndex == -1){
                            elementor.getPreviewView().addChildModel(data, {silent: 0});
                        } else {
                            elementor.getPreviewView().addChildModel(data, {at: insertIndex, silent: 0});                 
                        }
                        elementor.templates.layout.hideLoadingView();
                        elementor.templates.layout.modal.hide();
                    }); 
                } else {
                    elementor.templates.layout.hideLoadingView();
                    EUL_how_to_accuire_modal(condition);
                } 
            });

        });

        /* Search button */
        $('.ELU__search').unbind('click');
        $('.ELU__search').click(function(){
            var searchValue = $('.ELU__search-value').val();
            if(searchValue == '') searchValue = " ";
            var data = {
                "search": searchValue,
                "page" : 0
            }
            elementor.templates.layout.showLoadingView();
            $.post(ajaxurl, { action : 'get_ELU_thumbnails', data: data}, function(data) {
                elementor.templates.layout.hideLoadingView();
                $('.ELU__main-view').html(data);
                EUL_updateActions(insertIndex);
            }); 
        });

        
        /* Swap thumbnail based on version selected */
        $('.ELU__version').unbind('change');
        $('.ELU__version').change(function(){
            baseClass = "."+$(this).attr('data-thumb-class');
            $(baseClass).hide();
            $(baseClass+"-"+$(this).val()).show();
        });

        /* Open up preview window */
        $('.ELU__thumb').unbind('click');
        $('.ELU__thumb').click(function(){
            var data = ELU_Index[$(this).attr('data-index')]
            elementor.templates.layout.showLoadingView();
            $.post(ajaxurl, { action : 'get_ELU_preview', data: data}, function(data) {
                elementor.templates.layout.hideLoadingView();
                $('body').append(data);
                $('.ELU__preview-window').addClass('ELU__expand');
                EUL_updateActions(insertIndex);
            }); 

        });

        /* Close preview window */
        $('.ELU__preview-close').unbind('click');
        $('.ELU__preview-close').click(function(){
            $('.ELU__preview-window').replaceWith('');
        });

        /* Paging Goto Page buttons */
        $('.ELU__page-goto').unbind('click');
        $('.ELU__page-goto').click(function(){
            var data = {
                "search" : $('.ELU__search-saved').val(),
                "page" : (Number($(this).html().trim())-1)
            }
            elementor.templates.layout.showLoadingView();
            $.post(ajaxurl, { action : 'get_ELU_thumbnails', data: data}, function(data) {
                elementor.templates.layout.hideLoadingView();
                $('.ELU__main-view').html(data);
                EUL_updateActions(insertIndex);
            }); 
        });

        // wp_ajax_get_ELU_thumbnails
        /* Top Power Button Actions */
        $('.ELU__more-power, .ELU__more-power-close').unbind('click');
        $('.ELU__more-power, .ELU__more-power-close').click(function(){
            $('.ELU__more-power').toggle();
            $('.ELU__more-power-details').toggle();
        });
    }

    /* HOW TO ACUIRE REQUIRED PLUGINS CTA MODAL */
    var EUL_how_to_accuire_modal = function(plugin_){
        if(plugin_ == "pro") {
            var name = "Elementor Pro";
            var link = "https://elementor.com/pro/?ref=1544&campaign=webdisrupt";
        } else if(plugin_ == "funnelmentals") {
            var name = "Funnelmentals";
            var link = "https://wordpress.org/plugins/web-disrupt-funnelmentals/";
        } else if(plugin_ == "funnelmentals-pro") {
            var name = "Funnelmentals Pro";  
            var link = "https://webdisrupt.com/funnelmentals/";
        }
        $('.dialog-message').prepend("<div class='ELU__background'><div class='ELU__where_to_get_modal'> "+name+" is required to use this template! You can travel here to accuire this plugin. <div class='ELU__actions_modal'> <a class='ELU__cancel' target='_aquire_'> Cancel </a> <a href='"+link+"' class='ELU__go' target='_aquire_'> Get it Now! </a></div> </div> </div>");
        $('.ELU__cancel').click(function(){
            $('.ELU__background').replaceWith('');
        });
    }

    var EUL_updateAddTemplateButtons = function(){
        $(".elementor-editor-element-setting.elementor-editor-element-add").click(function(){
            EUL_updatePreviewAddButton();
        });
    }

    /* Add New Section to the template menu */
    $('#elementor-preview-iframe').load(function(){
        
        if(!ELUCached){ var ELUCached = null; }
        if(!insertIndex){ var insertIndex = null; }
        var refreshId = setInterval( function() 
        {
            // If menu exists then continue
            if( $('#elementor-template-library-header-menu').length){
                // If button doesn't exist then continue
                if(!$('#elementor-template-library-menu-unlimited-library').length){
                    /* Add Ultimate Library Tab and Clicked Action */
                    $('#elementor-template-library-header-menu').append("<div id='elementor-template-library-menu-unlimited-library' class='elementor-template-library-menu-custom-item'> Unlimited Library </div>");
                    $('#elementor-template-library-menu-unlimited-library').click(function(){
                        // Active Tab and Loading
                        $(".elementor-template-library-menu-item").removeClass('elementor-active');
                        $(this).addClass('active');
                        elementor.templates.layout.showLoadingView();
                        if(ELUCached == null){ // If cache not created then load it
                            /* Load template view via Ajax */
                            $.post(ajaxurl, { action : 'get_main_view_from_elementor_ultimate_template_library'}, function(data) {
                                $(".elementor-templates-modal .dialog-content").html(data);
                                ELUCached = data;
                                elementor.templates.layout.hideLoadingView();
                                EUL_updateActions(insertIndex);
                            });
                        } else {
                            $(".elementor-templates-modal .dialog-content").html(ELUCached);
                            EUL_updateActions(insertIndex);
                            elementor.templates.layout.hideLoadingView();
                        }
                    });
                } /* Library tab already exists */
            } /* Modal not open */


            /* Add bottom hover to capture the correct index for insertion */
            var getTemplateBottomButton = $('#elementor-preview-iframe').contents().find('#elementor-add-new-section .elementor-add-template-button');
            if( getTemplateBottomButton.length && !getTemplateBottomButton.hasClass('ELU_template_btn') ){
                getTemplateBottomButton.hover(function(){
                    $(this).addClass('ELU_template_btn');
                    insertIndex = -1;
                });
            }


           /* Add inline hover to capture the correct index for insertion */
           var getTemplateInlineButtons = $('#elementor-preview-iframe').contents().find('.elementor-add-section-inline .elementor-add-template-button');
           if( getTemplateInlineButtons.length ){
                getTemplateInlineButtons.each(function(){
                    if(!$(this).hasClass('ELU_template_btn')){
                        $(this).addClass('ELU_template_btn');
                    } else {
                        $(this).unbind('hover');
                        $(this).hover(function(){
                            var templateContainer = $(this).parent().parent().parent(),
                            allSections = $(this).parent().parent().parent().parent().children(),
                            tempInsertIndex = [];
                            for (let index = 0; index < allSections.length; index++) {
                                if(allSections[index].localName != 'div' || allSections[index] == templateContainer[0]){
                                    tempInsertIndex.push(allSections[index]);   
                                }
                            } // Make new array with only the selected add template
                            for (let index = 0; index < tempInsertIndex.length; index++) {
                                if(tempInsertIndex[index] == templateContainer[0]){ insertIndex = index;  }
                            } // get index of that selected add template area
                        });
                    }
                }); /* loop through inline template buttons */

            }  /* Inline template exists */

        }, 250);      
    });



});