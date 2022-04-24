EsConfigureService = function() {
    this.massSelectFields = function(selectAll) {
        let mappingCheckboxes = jQuery('#configure-service').find('.data-mappings');

        selectAll.is(':checked') ? jQuery(mappingCheckboxes).prop('checked', true) : jQuery(mappingCheckboxes).prop('checked', false);
    }
}